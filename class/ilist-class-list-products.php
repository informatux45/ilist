<?php
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// Blocking direct access to plugin      -=
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
defined('ABSPATH') or die('Are you crazy!');

/**
 * LOAD THE BASE CLASS
 * ==============================================================
 * http://codex.wordpress.org/Class_Reference/WP_List_Table
 * http://wordpress.org/extend/plugins/custom-list-table-example/
 */
if ( ! class_exists('WP_List_Table') ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 */
class ilist_List_Product_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;    
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'list_product',     // singular name of the listed records
            'plural'    => 'list_products',    // plural name of the listed records
            'ajax'      => false               // does this table support ajax?
        ) );
    }
	
    /**
     * [REQUIRED] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_default($item, $column_name) {
        return $item[$column_name];
    }
    
    /**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_date($item) {
		if ($item['status'] > 1) {
			if (!$item['date'] || $item['date'] == "0000-00-00 00:00:00")
				return __('--', ILIST_ID_LANGUAGES);
			else
				return ilistConvertDate($item['date'], 'FR');
		} else {
			return __('--', ILIST_ID_LANGUAGES);
		}
    }
	
    /**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_status($item) {
        global $wpdb, $Ilist;
        $status      = "";
        $class_color = "";
		$item_status = ilist_get_status_info($item['status'], 'title');
        $item_color  = ilist_get_status_info($item['status'], 'color');
		switch($item['status']) {
			case "1":
                $class_color = (!isset($item_color)) ? 'class="ilist_for_sale"' : 'style="color: '.$item_color.'"';
            break;
			case "2":
                $class_color = (!isset($item_color)) ? 'class="buy_internet"' : 'style="color: '.$item_color.'"';
            break;
			default:
                $class_color = (!isset($item_color)) ? 'class="buy_other"' : 'style="color: '.$item_color.'"';
            break;
		}
        // Status Text Color
		$status .= '<span '.$class_color.'>' . $item_status . '</span>';
        // Check if participations
        $product_id = (isset($item['variation_id']) && $item['variation_id'] > 0) ? intval($item['variation_id']) : intval($item['product_id']);
        $list_id    = intval($item['list_id']);
        // Get participation from list ID AND product ID
        $participations = ilist_get_all_participations_by_product($list_id, $product_id, $item['id']);
        if ($participations) {
            $status .= '<br>' . ( ($item['status'] == 1) ? __( 'Participations in progress', ILIST_ID_LANGUAGES ) : __( 'Participations', ILIST_ID_LANGUAGES ) ) . ' : ';
            foreach($participations as $participation) {
                $status .= "<br>";
                $status .= "<a href='" . admin_url( 'post.php?post='.$participation->order_id.'&action=edit') . "'>" . sprintf( __( 'Order No.%s', ILIST_ID_LANGUAGES ), $participation->order_id ) . '</a>';
                $status .= ' : ' . $participation->customer . ' (' . $Ilist->get_woocommerce_price_format( $participation->participation ) . ')';
            }
        }
		return $status;
    }

    /**
     * [OPTIONAL] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_product_name($item) {
        // Build row actions
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on current page
        // also notice how we use $this->_args['singular'] so in this example it will be something like &person=2
        $product_id = (isset($item['variation_id']) && $item['variation_id'] > 0) ? $item['variation_id'] : $item['product_id'];
        $actions = array(
			'b' => '<span style="color:silver">ID: '.$item['id'].'</span>',
            'edit'    => sprintf('<a href="?page=%s&action=%s&id=%s">%s</a>', 'ilist_product', 'edit', $item['id'], __('Update', ILIST_ID_LANGUAGES)),
            'delete'  => sprintf('<a onclick="return ilist_confirm_delete(\''.__('this product', ILIST_ID_LANGUAGES).'\');" href="?page=%s&action=%s&pid=%s&id=%s&product_id=%s&variation_id=%s">%s</a>', $_REQUEST['page'], 'delete', $item['id'], $item['list_id'], $item['product_id'], $item['variation_id'], __('Delete', ILIST_ID_LANGUAGES)),
            'show'    => sprintf('<a href="%s" target="_blank">%s</a>', get_permalink($product_id), __('See product', ILIST_ID_LANGUAGES)),
			'c'       => (isset($item['order_id']) && $item['order_id'] != "") ? sprintf('<a href="%s" target="_blank">%s</a>', get_edit_post_link($item['order_id']), __('See order', ILIST_ID_LANGUAGES)) : __('No orders', ILIST_ID_LANGUAGES),
        );

        // Return the title contents
        return sprintf('%1$s %2$s',
            /*$1%s*/ $item['product_name'],
            /*$2%s*/ $this->row_actions($actions)
        );
    }
	
    /**
     * [OPTIONAL] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_customer($item) {
		if ($item['status'] > 1) {
			if (isset($item['customer']) && trim($item['customer']) != "") {
                // Check if full purchase or partial purchase
                $product_id = (isset($item['variation_id']) && $item['variation_id'] > 0) ? $item['variation_id'] : $item['product_id'];
                $end_participation  = ilist_is_end_participation($product_id, $item['list_id'], $item['order_id']);
                $_end_participation = "";
                if ($end_participation) {
                    $_end_participation = " (" . __( 'End of participation', ILIST_ID_LANGUAGES ) . ": " . $end_participation . ")";
                }
				return $item['customer'] . $_end_participation;
			} else {
				return __('--', ILIST_ID_LANGUAGES);
			}
		} else {
			return __('--', ILIST_ID_LANGUAGES);
		}
        
    }
    
    /**
     * [OPTIONAL] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_admin_note($item) {
		$ilist_column_note = (isset($item['admin_note']) && $item['admin_note'] != '') ? stripslashes_deep($item['admin_note']) : __('--', ILIST_ID_LANGUAGES);
        return '<div class="ilist-note-editable" id="editable_' . $item['id'] . '" data-id="'.$item['id'].'">' . $ilist_column_note . '</div><div class="ilist-note-editable-buttons" id="editable_buttons_' . $item['id'] . '"><a class="button cancel" id="editable_button_' . $item['id'] . '" data-itemid="'.$item['id'].'">Cancel</a>&nbsp;<a class="button ok" id="editable_button_' . $item['id'] . '" data-itemid="'.$item['id'].'">OK</a></div>';
    }
    
    /**
     * [OPTIONAL] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_is_pot($item) {
        if (ilist_get_option('ilist_pot_is_active')) {
    		return ( $item['is_pot'] == '1' ) ? '<strong style="color: green;">' . __( 'Activated', ILIST_ID_LANGUAGES ) . '</strong>' : __( 'Desactivated', ILIST_ID_LANGUAGES );
        } else {
            return '--';
        }
    }
	
    /**
     * [OPTIONAL] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
	function column_product_price($item) {
        global $Ilist;
		$_product = wc_get_product( ( $item['variation_id'] ? $item['variation_id'] : $item['product_id'] ) );
        return ($_product) ? $Ilist->get_woocommerce_price_format( $_product->get_price() ) : '<strong>'.__('Unknown product', ILIST_ID_LANGUAGES).'</strong>';
    }
	
    /**
     * [REQUIRED] this is how checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }
	
    /**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'            => '<input type="checkbox" />', //Render a checkbox instead of text
            'product_name'  => __('Product', ILIST_ID_LANGUAGES),
			'product_price' => __('Price', ILIST_ID_LANGUAGES),
            'customer'      => __('Buyer', ILIST_ID_LANGUAGES),
            'status'        => __('Status', ILIST_ID_LANGUAGES),
            'admin_note'    => __('Note', ILIST_ID_LANGUAGES),
            'is_pot'        => __('Participation', ILIST_ID_LANGUAGES),
            'date'          => __('Order date', ILIST_ID_LANGUAGES),
        );
        return $columns;
    }
	
    /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'product_name' => array('product_name', false),   //true means it's already sorted
            'customer'     => array('customer', false),  //true means it's already sorted
            'status'       => array('status', false),
            'admin_note'   => array('admin_note', false),
            'is_pot'       => array('is_pot', false),
            'date'         => array('date', true),  //true means it's already sorted
        );
        return $sortable_columns;
    }
    
    /**
     * [OPTIONNAL] Output the controls to allow user roles to be changed in bulk
     * $which (string) (Required) Whether this is being invoked above ("top") or below the table ("bottom")
     *
     * @return string
     */
	protected function extra_tablenav( $which ) {
		global $wpdb, $Ilist;
		$move_on_url = '&cat-filter=';
		if ( 'top' === $which ) {
			$sale_filter      = (isset($_GET['sale_filter'])) ? $_GET['sale_filter'] : 0;
			$ilist_all_status = ilist_get_status_list();
		
			$total_price = 0;
			if ( !empty($this->items) ) {
                foreach( $this->items as $k => $v ) {
					$product_id = (!empty($v['variation_id'])) ? $v['variation_id'] : $v['product_id'];
					$_product   = wc_get_product( $product_id );
					if ($_product) {
                        $total_price += $_product->get_price();
                    }
				}
			}
			?>
			<div class="ilist_total_price_row">
                <?php echo __('Total product price', ILIST_ID_LANGUAGES);?> : <?php echo $Ilist->get_woocommerce_price_format( $total_price ); ?>
            </div>

			<input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : 0 ;?>" />
			<div class="alignleft actions">
				<select name="sale_filter" id="sale_filter">
					<option value=""><?php _e('All status', ILIST_ID_LANGUAGES); ?></option>
					<?php
                    if ( !empty($ilist_all_status) ) {
                        foreach($ilist_all_status as $ilist_status) {
                            $selected_sale_filter = ($sale_filter == $ilist_status->id) ? ' selected="selected"' : '';
                            echo '<option value="'. $ilist_status->id . '"' . $selected_sale_filter . '>' . $ilist_status->title . '</option>';
                        }
                    }
                    ?>
				</select>
				<?php submit_button( __( 'Filter by status', ILIST_ID_LANGUAGES ), '', 'filter_action', false, array( 'id' => 'post-query-submit' ) ); ?>
			</div>
			
			<?php
            
            do_action( 'ilist_admin_list_products_after' );
		}
	}
	
    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
    function get_bulk_actions() {
        $actions = array(
            //'delete' => __('Delete', ILIST_ID_LANGUAGES),
        );
        return $actions;
    }
	
    /**
     * [OPTIONAL] This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
    function process_bulk_action() {
        global $wpdb;
        
        // Delete product(s)
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['pid']) ? $_REQUEST['pid'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM " . ILIST_TBL_PRODUCT . " WHERE id IN($ids)");
                // --------------------------------------------------------------------------
                // Check if management stock product AND management stock ILIST are activated
                // --------------------------------------------------------------------------
                $ilist_product_id   = $_REQUEST['product_id'];
                $ilist_variation_id = $_REQUEST['variation_id'];
                if (is_array($ids)) {
                    foreach($ids as $id) {
                        // Get product_id / variation_id
                        $request = $wpdb->get_row( "SELECT product_id, variation_id FROM " . ILIST_TBL_PRODUCT . " WHERE id = '$id'" );
                        $ilist_product_id   = $request->product_id;
                        $ilist_variation_id = $request->variation_id;
                        $_product = (isset($ilist_variation_id) && $ilist_variation_id > 0) ? wc_get_product( $ilist_variation_id ) : wc_get_product( $ilist_product_id );
                        ilist_update_stock($_product, 'increase');
                    }
                } else {
                    $ilist_product_id   = $_REQUEST['product_id'];
                    $ilist_variation_id = $_REQUEST['variation_id'];
                    $_product = (isset($ilist_variation_id) && $ilist_variation_id > 0) ? wc_get_product( $ilist_variation_id ) : wc_get_product( $ilist_product_id );
                    ilist_update_stock($_product, 'increase');
                }
            }
        }
        // Add product
        if ('add' === $this->current_action() && $_POST && 'add' === $_POST['productadd'] && $_POST['list_id'] > 0 && $_POST['product_id'] > 0) {
            // Search if variable product
            if (strpos($_POST['product_id'], "|") === false) {
                // Simple product
                $productid    = intval($_POST['product_id']);
                $variation_id = NULL;
                $productname  = esc_html( get_the_title( $_POST['product_id'] ) );
                $_product     = wc_get_product( $productid );
            } else {
                // Variable product
                list($productid, $variation_id) = explode("|", $_POST['product_id']);
                $productname = esc_html( get_the_title( $variation_id ) );
                $_product    = wc_get_product( $variation_id );
            }
            $is_pot = ( isset($_POST['is_pot']) && $_POST['is_pot'] == 'yes' ) ? "1" : "0";
            // We can insert in the list (Simple product)
            $insert = $wpdb->insert( 
                ILIST_TBL_PRODUCT, 
                array( 
                     'list_id'      => $_POST['list_id']
                    ,'product_id'   => $_POST['product_id']
                    ,'variation_id' => $variation_id
                    ,'product_name' => $productname
                    ,'is_pot'       => $is_pot
                ), 
                array( '%d', '%d', '%d', '%s', '%d' )
            );
            // Result
            $_product_id = (!$variation_id) ? $productid : $variation_id;
            $_POST['is_result_insert'] = ($insert) ? $_product_id : false;
        }
    }
	
	/**
	 * Get number of items to display on a single page
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param string $option
	 * @param int    $default
	 * @return int
	 */
	protected function get_items_per_page( $option, $default = 20 ) {
		$per_page = (int) get_user_option( $option );
		if ( empty( $per_page ) || $per_page < 1 )
			$per_page = $default;

		/**
		 * Filters the number of items to be displayed on each page of the list table.
		 *
		 * The dynamic hook name, $option, refers to the `per_page` option depending
		 * on the type of list table in use. Possible values include: 'edit_comments_per_page',
		 * 'sites_network_per_page', 'site_themes_network_per_page', 'themes_network_per_page',
		 * 'users_network_per_page', 'edit_post_per_page', 'edit_page_per_page',
		 * 'edit_{$post_type}_per_page', etc.
		 *
		 * @since 2.9.0
		 *
		 * @param int $per_page Number of items to be displayed. Default 20.
		 */
		return (int) apply_filters( "{$option}", $per_page );
	}
    
	/**
	 * Displays the product add box.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param string $list_id ID attribute value for the list input field.
	 */
	public function productadd_box( $list_id = NULL ) {
        // Get all products in WooCommerce
        //$ilist_all_products = ilist_get_woocommerce_product_list();

        ?>
        <p class="productadd-box">
            <select name="product_id" id="product_id">
                <option><?php echo '&mdash; ' . __('Choose a product', ILIST_ID_LANGUAGES) . ' &mdash;'; ?></option>
            </select>
            <?php if (ilist_get_option('ilist_pot_is_active')) { ?> &nbsp;&nbsp;<input type="checkbox" name="is_pot" value="yes">&nbsp;<?php _e( 'Participation mode', ILIST_ID_LANGUAGES ); ?>&nbsp;&nbsp; <?php } ?>
            <input type="hidden" name="list_id" value="<?php echo $list_id; ?>" />
            <input type="hidden" name="productadd" value="add" />
            <?php submit_button( __('Add', ILIST_ID_LANGUAGES), '', '', false, array( 'id' => 'productadd-submit' ) ); ?>
        </p>
        <?php
	}
	
    /**
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items($search = NULL) {
        global $wpdb;

        // How much records will be shown per page (option screen)
        $per_page = 1000;
		
        // here we configure table headers, defined in our methods
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // prepare query params, as usual current page, order by and order direction
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'product_name';
        $order   = (isset($_REQUEST['order']) && $_REQUEST['order'] == 'desc') ? strtoupper($_REQUEST['order']) : 'ASC';
        $listid  = (isset($_REQUEST['id'])) ? intval($_REQUEST['id']) : 0;
        
        // Search by status filter
		$sale_filter = (isset($_REQUEST['sale_filter'])) ? intval($_REQUEST['sale_filter']) : 0;
		if ( $sale_filter > 0 ) {
            // will be used in pagination settings
            $total_items = $wpdb->get_var("SELECT COUNT(list_id) FROM " . ILIST_TBL_PRODUCT . " WHERE list_id = '$listid' AND status = '$sale_filter'");
			$this->items = $wpdb->get_results( sprintf("SELECT * FROM " . ILIST_TBL_PRODUCT . " WHERE list_id = '%d' AND status = '%d' ORDER BY %s %s LIMIT %d", ilist_stopXSS($listid), ilist_stopXSS($sale_filter), ilist_stopXSS($orderby), $order, $per_page), ARRAY_A);
		} else {
            // will be used in pagination settings
            $total_items = $wpdb->get_var("SELECT COUNT(list_id) FROM " . ILIST_TBL_PRODUCT . " WHERE list_id = '$listid'");
            // If the value is not NULL, do a search for it
			$this->items = $wpdb->get_results( sprintf("SELECT * FROM " . ILIST_TBL_PRODUCT . " WHERE list_id = '%d' ORDER BY %s %s LIMIT %d", ilist_stopXSS($listid), ilist_stopXSS($orderby), $order, $per_page), ARRAY_A );
		}
		
        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page'    => $per_page,    // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
		
    }
	
}

/**
 * Get List Information
 * @return string
 */
if (!function_exists("ilist_get_list_info")) {
    function ilist_get_list_info($ilist_id = NULL, $column = '') {
        global $wpdb;
        $table_column = ($column != '') ? $column : '*';
        
        if (!$ilist_id) return;
        
        $ilist_info = $wpdb->get_row( "SELECT $table_column FROM " . ILIST_TBL_MAIN . " WHERE id = $ilist_id" );
    
        // Check result    
        if ( null !== $ilist_info ) {
            return $ilist_info->{$table_column};
        } else {
            // no info found
            return false;
        }
    }
}

/**
 * http://codex.wordpress.org/Data_Validation
 * http://codex.wordpress.org/Function_Reference/selected
 *
 * Form page handler checks is there some data posted and tries to save it
 * Also it renders basic wrapper in which we are callin meta box render
 */
if (!function_exists("ilist_product_form_page_handler")) {
    function ilist_product_form_page_handler() {
            global $wpdb;
        
            // Initialization
            $message = '';
            $notice  = '';
        
            // this is default $item which will be used for new records
            $default = array(
                'id'           => 0,
                'list_id'      => 0,
                'date'         => '',
                'product_name' => '',
                'product_id'   => '',
                'customer'     => '',
                'admin_note'   => '',
                'status'       => 1,
                'is_pot'       => 0
            );
        
            // here we are verifying does this request is post back and have correct nonce
            if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
                // combine our default item with request params
                $item = shortcode_atts($default, $_REQUEST);
                // validate data, and if all ok save item to database
                // if id is zero insert otherwise update
                $item_valid = ilist_validate_product($item);
                if ($item_valid === true) {
                    if ($item['id'] == 0) {
                        $result     = $wpdb->insert(ILIST_TBL_PRODUCT, $item);
                        $item['id'] = $wpdb->insert_id;
                        if ($result) {
                            $message = __('Product saved successfully', ILIST_ID_LANGUAGES);
                        } else {
                            $notice = __("ERROR when saving the product", ILIST_ID_LANGUAGES);
                        }
                    } else {
                        $result = $wpdb->update(ILIST_TBL_PRODUCT, 
                                                array( 
                                                    'date'       => $_REQUEST['date'],
                                                    'customer'   => stripslashes( esc_html($_REQUEST['customer']) ),
                                                    'admin_note' => stripslashes( esc_html($_REQUEST['admin_note']) ),
                                                    'status'     => $_REQUEST['status'],
                                                    'is_pot'     => $_REQUEST['is_pot']
                                                ), 
                                                array( 'id' => $item['id'] ), 
                                                array( '%s', '%s', '%s', '%s', '%d' ), 
                                                array( '%d' ) 
                                            );
                        if ($result) {
                            if (!isset($_POST['submit_with_email'])) {
                                $message = __('Product updated successfully', ILIST_ID_LANGUAGES);
                            } else {
                                $message = '';
                                $notice  = '';
                                // -------------------------------------------
                                // Send Email to ADMINISTRATOR(S)
                                // -------------------------------------------
                                // --- Get options
                                $ilist_emails_admin         = trim(ilist_get_option( 'ilist_email_admin' ));
                                $ilist_email_admin_name     = trim(ilist_get_option( 'ilist_email_admin_name' ));
                                $ilist_email_admin_response = trim(ilist_get_option( 'ilist_email_admin_response' ));
                                $ilist_email_admin_subject  = trim(ilist_get_option( 'ilist_email_subject_alert_one_admin' ));
                                $ilist_email_admin_body     = trim(ilist_get_option( 'ilist_email_body_alert_one_admin' ));
                                $ilist_comment_checkout     = (isset($_REQUEST['comment_customer']) && $_REQUEST['comment_customer'] != '') ? trim(stripslashes(esc_html($_REQUEST['comment_customer']))) : __('None', ILIST_ID_LANGUAGES);
                                $ilist_type                 = ilist_get_status_info(intval($item['status']), 'title');
                                // --- Replace Variables in email subject
                                $ilist_email_admin_subject  = @preg_replace("/{ILIST_NAME}/", esc_html(ilist_get_list_info($item['list_id'], 'name')), $ilist_email_admin_subject);
                                // --- Replace Variables in email body
                                $ilist_email_admin_body = @preg_replace("/{ILIST_NAME}/", esc_html(ilist_get_list_info($item['list_id'], 'name')), $ilist_email_admin_body);
                                $ilist_email_admin_body = @preg_replace("/{ILIST_PRODUCT}/", '<a href="'.get_permalink($item['product_id']).'">'.get_the_title($item['product_id']).'</a>', $ilist_email_admin_body);
                                $ilist_email_admin_body = @preg_replace("/{ILIST_FRIEND}/", esc_html($item['customer']), $ilist_email_admin_body);
                                $ilist_email_admin_body = @preg_replace("/{ILIST_DATE}/", ilistConvertDate($item['date'], 'FRT'), $ilist_email_admin_body);
                                $ilist_email_admin_body = @preg_replace("/{ILIST_TYPE}/", $ilist_type, $ilist_email_admin_body);
                                $ilist_email_admin_body = @preg_replace("/{ILIST_COMMENT}/", $ilist_comment_checkout, $ilist_email_admin_body);
                                $ilist_email_admin_body = @preg_replace("/{ILIST_WP_SITE_NAME}/", esc_html(get_bloginfo('name')), $ilist_email_admin_body);
                                $ilist_email_admin_body = @preg_replace("/{ILIST_WP_SITE_DESC}/", esc_html(get_bloginfo('description')), $ilist_email_admin_body);
                                // --- Send email
                                function ilist_set_html_mail_content_type() {
                                    return 'text/html';
                                }
                                add_filter( 'wp_mail_content_type', 'ilist_set_html_mail_content_type' );
                                // Initialize
                                //$attachments = array( WP_CONTENT_DIR . '/uploads/file_to_attach.zip' );
                                $attachments = [];
                                $headers     = ["From: $ilist_email_admin_name <$ilist_email_admin_response>",
                                                ];
                                $to_admins   = $ilist_emails_admin;
                                $subject     = $ilist_email_admin_subject;
                                $body        = $ilist_email_admin_body;
                                // -----------------------------
                                if ( wp_mail( $to_admins, $subject, $body, $headers, $attachments ) ) {
                                    //echo json_encode(array("result"=>"complete"));
                                    $message .= __('Product updated successfully', ILIST_ID_LANGUAGES);
                                    $message .= __('<br>Comment: ', ILIST_ID_LANGUAGES) . trim(stripslashes(esc_html($ilist_comment_checkout)));
                                    $message .= __('<br>Email sent to the administrators ('.$ilist_emails_admin.')', ILIST_ID_LANGUAGES);
                                } else {
                                    //echo json_encode(array("result"=>"mail_error"));
                                    $notice .= __("Product updated successfully", ILIST_ID_LANGUAGES);
                                    $notice .= __("<br>Error sending email to administrators", ILIST_ID_LANGUAGES);
                                    var_dump($GLOBALS['phpmailer']->ErrorInfo);
                                }
                                // -------------------------------------------
                                // Send Email to CLIENT
                                // -------------------------------------------
                                // --- Get options
                                $ilist_email_client         = ilist_get_list_info($item['list_id'], 'email');
                                $ilist_email_client_subject = trim(ilist_get_option( 'ilist_email_subject_alert_one' ));
                                $ilist_email_client_body    = trim(ilist_get_option( 'ilist_email_body_alert_one' ));
                                // --- Replace Variables in email subject
                                $ilist_email_client_subject = @preg_replace("/{ILIST_NAME}/", esc_html(ilist_get_list_info($item['list_id'], 'name')), $ilist_email_client_subject);
                                // --- Replace Variables in email body
                                $ilist_email_client_body = @preg_replace("/{ILIST_NAME}/", esc_html(ilist_get_list_info($item['list_id'], 'name')), $ilist_email_client_body);
                                $ilist_email_client_body = @preg_replace("/{ILIST_PRODUCT}/", '<a href="'.get_permalink($item['product_id']).'">'.get_the_title($item['product_id']).'</a>', $ilist_email_client_body);
                                $ilist_email_client_body = @preg_replace("/{ILIST_FRIEND}/", esc_html($item['customer']), $ilist_email_client_body);
                                $ilist_email_client_body = @preg_replace("/{ILIST_DATE}/", ilistConvertDate($item['date'], 'FRT'), $ilist_email_client_body);
                                $ilist_email_client_body = @preg_replace("/{ILIST_TYPE}/", $ilist_type, $ilist_email_client_body);
                                $ilist_email_client_body = @preg_replace("/{ILIST_COMMENT}/", $ilist_comment_checkout, $ilist_email_client_body);
                                $ilist_email_client_body = @preg_replace("/{ILIST_WP_SITE_NAME}/", esc_html(get_bloginfo('name')), $ilist_email_client_body);
                                $ilist_email_client_body = @preg_replace("/{ILIST_WP_SITE_DESC}/", esc_html(get_bloginfo('description')), $ilist_email_client_body);
                                // --- Send email
                                // Initialize
                                //$attachments = array( WP_CONTENT_DIR . '/uploads/file_to_attach.zip' );
                                $attachments = [];
                                $to_client   = $ilist_email_client;
                                $subject_2   = $ilist_email_client_subject;
                                $body_2      = $ilist_email_client_body;
                                // -----------------------------
                                if ( wp_mail( $to_client, $subject_2, $body_2, $headers, $attachments ) ) {
                                    //echo json_encode(array("result"=>"complete"));
                                    $message .= __('<br>Email sent to client ('.$ilist_email_client.')', ILIST_ID_LANGUAGES);
                                } else {
                                    //echo json_encode(array("result"=>"mail_error"));
                                    $notice .= __("<br>Error sending email to client", ILIST_ID_LANGUAGES);
                                    var_dump($GLOBALS['phpmailer']->ErrorInfo);
                                }
                                // Reset content-type to avoid conflicts -- https://core.trac.wordpress.org/ticket/23578
                                remove_filter( 'wp_mail_content_type', 'ilist_set_html_mail_content_type' );
                            }
                        } else {
                            if (_ILIST_DEBUG) {
                                $wpdb->show_errors();
                                $wpdb->print_error();
                            }
                            $notice = __('ERROR when editing product (no information changed)', ILIST_ID_LANGUAGES);
                        }
                    }
                } else {
                    // if $item_valid not true it contains error message(s)
                    $notice = $item_valid;
                }
            }
            else {
                // if this is not post back we load item to edit or give new one to create
                $item = $default;
                if (isset($_REQUEST['id'])) {
                    $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . ILIST_TBL_PRODUCT . " WHERE id = %d", $_REQUEST['id']), ARRAY_A);
                    if (!$item) {
                        $item = $default;
                        $notice = __('Product not found', ILIST_ID_LANGUAGES);
                    }
                }
            }
        
            // here we adding our custom meta box
            $ilist_meta_box_edit_product = __('Edit product :: ', ILIST_ID_LANGUAGES);
            add_meta_box('ilist_product_form_meta_box', $ilist_meta_box_edit_product . $item['product_name'], 'ilist_product_form_meta_box_handler', 'ilist', 'normal', 'default');
        
            ?>
    
        <div class="wrap page_ilist">
            <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
            <h1>
                <?php
                    echo ILIST_NAME . ' :: ';
                    _e('Edit a List product :: ', ILIST_ID_LANGUAGES);
                    echo ilist_get_list_info($item['list_id'], 'name');
                ?>
                &nbsp;
                <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=ilist_list');?>">
                    <?php _e('Back to lists', ILIST_ID_LANGUAGES)?>
                </a>
                &nbsp;
                <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=ilist_products&id=' . $item['list_id']);?>">
                    <?php _e('Back to the products of this list', ILIST_ID_LANGUAGES)?>
                </a>
            </h1>
        
            <?php if (!empty($notice)): ?>
            <div id="notice" class="error"><p><?php echo $notice ?></p></div>
            <?php endif;?>
            <?php if (!empty($message)): ?>
            <div id="message" class="updated"><p><?php echo $message ?></p></div>
            <?php endif;?>
        
            <form id="form" method="POST">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
                <?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
                <input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>
                <input type="hidden" name="list_id" value="<?php echo $item['list_id'] ?>"/>
                <input type="hidden" name="date" value="<?php echo $item['date'] ?>"/>
                <input type="hidden" name="product_name" value="<?php echo $item['product_name'] ?>"/>
                <input type="hidden" name="product_id" value="<?php echo $item['product_id'] ?>"/>
        
                <div class="metabox-holder" id="poststuff">
                    <div id="post-body">
                        <div id="post-body-content" class="post-body-ilist">
                            <?php /* And here we call our custom meta box */ ?>
                            <?php do_meta_boxes('ilist', 'normal', $item); ?>
                            <input type="submit" value="<?php _e('Save', ILIST_ID_LANGUAGES)?>" id="submit" class="button-primary" name="submit">
                            &nbsp;&nbsp;<?php _e('OR', ILIST_ID_LANGUAGES)?>&nbsp;&nbsp;
                            <input type="submit" value="<?php _e('Save and send email', ILIST_ID_LANGUAGES)?>" id="submit_with_email" class="button-primary" name="submit_with_email">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
}

/**
 * This function renders our custom meta box
 * $item is row
 *
 * @param $item
 */
if (!function_exists("ilist_product_form_meta_box_handler")) {
    function ilist_product_form_meta_box_handler($item) {
        global $wpdb, $Ilist;
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', ILIST_URL . 'vendor/Jquery/jquery-ui.min.css');
        $ilist_all_status = ilist_get_status_list();
        ?>
    
    <table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
        <tbody>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="status"><?php _e('Status', ILIST_ID_LANGUAGES); ?></label>
            </th>
            <td>
                <?php
                    if ($ilist_all_status) {
                        foreach($ilist_all_status as $ilist_status) {
                            $ilist_status_checked = ($item['status'] == $ilist_status->id) ? ' checked="checked"' : '';
                            echo '<input type="radio" name="status" value="' . $ilist_status->id . '"' . $ilist_status_checked . ' />';
                            echo '&nbsp;' . $ilist_status->title;
                            echo '<br><br>';
                        }
                    }
                ?>
            </td>
        </tr>
        <?php if (ilist_get_option('ilist_pot_is_active')) {
            // ------------------------------------
            // Participations à l'achat du produit
            // ------------------------------------
            $are_participations = false;
            $product_id        = (isset($item['variation_id'])) ? $item['variation_id'] : $item['product_id'];
            $participations    = $wpdb->get_results( "SELECT * FROM " . ILIST_TBL_CAGNOTTE . " WHERE list_id = '" . $item['list_id'] . "' AND product_id = '$product_id'" );
            // Check if participations
            if ($participations) {
                $are_participations = true;
                $all_products_text  = "";
                foreach($participations as $participation) {
                    $all_products_text .= "<a href='" . admin_url( 'post.php?post='.$participation->order_id.'&action=edit') . "'>" . sprintf( __( 'Order No.%s', ILIST_ID_LANGUAGES ), $participation->order_id ) . '</a>';
                    $all_products_text .= ' : ' . $participation->customer . ' (' . $Ilist->get_woocommerce_price_format( $participation->participation ) . ')<br>';
                    
                }
            } ?>
            <tr>
                <th valign="top" scope="row">
                    <label for=""><?php echo __('Participation', ILIST_ID_LANGUAGES) . ($are_participations ? '(s)' : ''); ?></label>
                </th>
                <td>
                    <?php if ($are_participations) { ?>
                        <input type="hidden" name="is_pot" value="1">
                        <?php echo $all_products_text; ?>
                    <?php } else { ?>
                        <input type="checkbox" name="is_pot" value="1" <?php checked( $item['is_pot'], 1 ); ?>>&nbsp;&nbsp;<?php _e( 'Participation mode', ILIST_ID_LANGUAGES ); ?>    
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
        <tr id="customer_comment" class="form-field">
            <th valign="top" scope="row">
                <label for="comment_customer"><?php _e("Buyer comment", ILIST_ID_LANGUAGES)?></label>
            </th>
            <td>
                <textarea id="comment_customer" name="comment_customer" type="text" rows="4" placeholder="<?php _e("Buyer comment", ILIST_ID_LANGUAGES)?>" disabled="disabled"><?php echo esc_textarea( ilist_get_order_meta( $item['order_id'], 'order-meta-ilist' ) ); ?></textarea>
                <br>
                <span class="description ilist-style-description">
                    <?php _e('Do not indicate anything if no comment.<br>It will not be saved if it was not dropped during an Internet order.<br>It will just insert in the email sent if you click the "Save and send email" button', ILIST_ID_LANGUAGES); ?>
                </span>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="admin_note"><?php _e("Note (Admin only)", ILIST_ID_LANGUAGES)?></label>
            </th>
            <td>
                <textarea id="admin_note" name="admin_note" type="text" rows="4" placeholder="<?php _e("Note", ILIST_ID_LANGUAGES)?>"><?php echo stripslashes_deep( $item['admin_note'] ); ?></textarea>
                <br>
                <span class="description ilist-style-description">
                    <?php _e('Free fields for administrators (note) - Visible only in administration', ILIST_ID_LANGUAGES); ?>
                </span>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="date"><?php _e('Order date', ILIST_ID_LANGUAGES)?></label>
            </th>
            <td>
                <input type="text" style="width: 25%;" class="datepicker" name="date" id="date" value="<?php echo ($item['date'])?>" />
                <br>
                <span class="description ilist-style-description">
                    <?php _e('ISO Format ( YYYY-MM-DD HH:MM:SS )', ILIST_ID_LANGUAGES); ?>
                </span>
            </td>
        </tr>
        <tr class="form-field">
            <th valign="top" scope="row">
                <label for="customer"><?php _e("Buyer name", ILIST_ID_LANGUAGES)?></label>
            </th>
            <td>
                <input id="customer" name="customer" type="text" style="width: 95%" value="<?php echo stripslashes(esc_attr($item['customer']))?>"
                       size="50" class="code" placeholder="<?php _e("Buyer name", ILIST_ID_LANGUAGES)?>">
            </td>
        </tr>
        </tbody>
    </table>
    
    <script>
        function ilist_comment_to_display(comment) {
            if (comment > 1) {
                jQuery("tr#customer_comment textarea#comment_customer").prop("disabled", false).removeClass('cssformcolor').css('background-color','inherit');
                jQuery("label[for='comment_customer']").text("<?php _e("Buyer comment (Enabled)", ILIST_ID_LANGUAGES)?>");
            } else {	
                jQuery("tr#customer_comment textarea#comment_customer").prop("disabled", true).addClass('cssformcolor');
                jQuery("label[for='comment_customer']").text("<?php _e("Buyer comment (Disabled)", ILIST_ID_LANGUAGES)?>");
            }
        }
    
        jQuery(document).ready(function() {
            // Date
            var ilist_current_time = "yy-mm-dd " + new Date().toLocaleTimeString('fr-FR', { hour: "numeric", minute: "numeric", second: "numeric" });
            jQuery('.datepicker').datepicker({
                dateFormat: ilist_current_time
            });
            // Textarea show/hide (on INIT)
            jQuery("input[name$='status']:checked").bind('init', function() {
                // Test value comment to display
                ilist_comment_to_display(jQuery(this).val());
            }).trigger('init');
            // Textarea show/hide (on CLICK)
            jQuery("input[name$='status']").on('click', function() {
                // Test value comment to display
                ilist_comment_to_display(jQuery(this).val());
            });
            
        });
    </script>
    
    <?php
    }
}

/**
 * Simple function that validates data and retrieve bool on success
 * and error message(s) on error
 *
 * @param $item
 * @return bool|string
 */
if (!function_exists("ilist_validate_product")) {
    function ilist_validate_product($item) {
        $messages = array();
    
        if (empty($item['status'])) $messages[] = __('Product status is required', ILIST_ID_LANGUAGES);
        if (empty($item['product_name'])) $messages[] = __('Product name is required', ILIST_ID_LANGUAGES);
    
        if (empty($messages)) return true;
        return implode('<br />', $messages);
    }
}

?>