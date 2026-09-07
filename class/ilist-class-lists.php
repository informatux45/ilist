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
class ilist_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;    
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'list',     // singular name of the listed records
            'plural'    => 'lists',    // plural name of the listed records
            'ajax'      => false       // does this table support ajax?
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
        // Build row actions
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on current page
        // also notice how we use $this->_args['singular'] so in this example it will be something like &person=2
        $first_actions = array(
			 '' => '<span style="color:silver">ID: '.$item['id'].'</span>'
            ,'link' => '<a href="' . ilist_get_option( 'ilist_url_complete' ) . '?a=liste&k=' . $item['keyaccess'] . '" target="_blank">' . __( 'See list', ILIST_ID_LANGUAGES ) . '</a>'
            ,'delete'      => sprintf('<a onclick="return ilist_confirm_delete(\''.__('Permanently delete this list', ILIST_ID_LANGUAGES).' ?\');" href="?page=%s&action=%s&id=%s">%s</a>', $_REQUEST['page'], 'delete', $item['id'], __('Permanently delete', ILIST_ID_LANGUAGES))
        );
        
        // Custom row actions
        if (has_filter('ilist_list_extra_row_actions')) {
            // $item est passé en 2e argument depuis la 2.3.0 : sans lui, une
            // action ajoutée ne peut pas cibler la liste de la ligne courante.
            // Les callbacks à un seul argument restent compatibles.
            $extra_row_actions = apply_filters( 'ilist_list_extra_row_actions', $first_actions, $item );
            $actions = array_merge($first_actions, $extra_row_actions);
        } else {
            $actions = $first_actions;
        }

        // Return the actions
        return sprintf('%1$s %2$s',
            /*$1%s*/ ilistConvertDate($item['date'], 'FR'),
            /*$2%s*/ $this->row_actions($actions)
        );
    }
	
    /**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_user_id($item) {
		$user_info = get_userdata($item['user_id']);
        return ($user_info && $user_info->display_name) ? $user_info->display_name . ' (' . $user_info->user_nicename . ')' : '--';
    }

    /**
     * [OPTIONAL] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_name($item) {
        // Build row actions
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on current page
        // also notice how we use $this->_args['singular'] so in this example it will be something like &person=2
        if (isset($item['active']) && $item['active'] == '1') {
            $action_desactivate = sprintf('<a style="color: #b32d2e;" onclick="return ilist_confirm_delete(\''.__('Desactivate this list (This will remove unsold products from this list)', ILIST_ID_LANGUAGES).' ?\');" href="?page=%s&action=%s&id=%s">%s</a>', $_REQUEST['page'], 'desactivate', $item['id'], __('Desactivate', ILIST_ID_LANGUAGES));
        } else {
            $action_desactivate = sprintf('<a style="color: #b32d2e;" onclick="return ilist_confirm_delete(\''.__('Activate this list', ILIST_ID_LANGUAGES).' ?\');" href="?page=%s&action=%s&id=%s">%s</a>', $_REQUEST['page'], 'activate', $item['id'], __('Activate', ILIST_ID_LANGUAGES));
        }
        // Delete: This will delete the list, its products and information available in WooCommerce orders
        $actions = array(
             'edit'        => sprintf('<a href="?page=%s&action=%s&id=%s">%s</a>', 'ilist_form', 'edit', $item['id'], __('Update', ILIST_ID_LANGUAGES))
            ,'desactivate' => $action_desactivate
            ,'product'     => sprintf('<a href="?page=%s&id=%s">%s</a>', 'ilist_products',$item['id'], __('See products', ILIST_ID_LANGUAGES))
        );

        // Return the title contents
        return sprintf('%1$s %2$s',
            /*$1%s*/ $item['name'],
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
    function column_active($item) {
        return ($item['active']) ? '<i class="fa-solid fa-check" style="color: green;" aria-hidden="true"></i>' : '<i class="fa-solid fa-circle" style="color: red;" aria-hidden="true"></i>';
    }
    
    /**
     * [OPTIONAL] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_password($item) {
        return (!empty($item['password'])) ? '<i class="fa-solid fa-lock" style="color: red;" aria-hidden="true" title="' . __('Password protected list', ILIST_ID_LANGUAGES) . '"></i>' : '<i class="fa-solid fa-lock-open" style="color: green;" aria-hidden="true" title="' . __('List accessible to visitors', ILIST_ID_LANGUAGES) . '"></i>';
    }
	
	function column_nbproduct($item) {
		$ilist_nb_products = ilist_get_nb_products_by_list($item['id']);
		return ($ilist_nb_products > 0) ? '<span class="buy_shop">' . $ilist_nb_products . '</span>' : $ilist_nb_products;
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
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'date'      => __('Date', ILIST_ID_LANGUAGES),
            'name'      => __('List', ILIST_ID_LANGUAGES),
            'user_id'   => __('Creator', ILIST_ID_LANGUAGES),
            'firstname' => __('Firstname', ILIST_ID_LANGUAGES),
            'lastname'  => __('Lastname', ILIST_ID_LANGUAGES),
            'email'     => __('Email', ILIST_ID_LANGUAGES),
            'nbproduct' => __('Product(s)', ILIST_ID_LANGUAGES),
            'password'  => __('Password', ILIST_ID_LANGUAGES),
            'active'    => __('Active', ILIST_ID_LANGUAGES),
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
            'date'      => array('date', true),   //true means it's already sorted
            'user_id'   => array('user_id', false),  //true means it's already sorted
            'name'      => array('name', false),  //true means it's already sorted
            'firstname' => array('firstname', false),
            'lastname'  => array('lastname', false),
            'email'     => array('email', false),
            'password'  => array('password', false),
            'active'    => array('active', false),
        );
        return $sortable_columns;
    }
	
	/**
	 * Displays the search box.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param string $text     The 'submit' button label.
	 * @param string $input_id ID attribute value for the search input field.
	 */
	public function search_box( $text, $input_id ) {
		if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
			return;

		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) )
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		if ( ! empty( $_REQUEST['order'] ) )
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		if ( ! empty( $_REQUEST['post_mime_type'] ) )
			echo '<input type="hidden" name="post_mime_type" value="' . esc_attr( $_REQUEST['post_mime_type'] ) . '" />';
		if ( ! empty( $_REQUEST['detached'] ) )
			echo '<input type="hidden" name="detached" value="' . esc_attr( $_REQUEST['detached'] ) . '" />';
?>
<p class="search-box">
	<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo $text; ?>:</label>
	<input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>" />
	<?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
</p>
<?php
	}

	
    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
    function get_bulk_actions() {
        $actions = array(
            'desactivate' => __('Desactivate', ILIST_ID_LANGUAGES),
            'delete'      => __('Delete', ILIST_ID_LANGUAGES),
            'activate'    => __('Activate', ILIST_ID_LANGUAGES),
        );
        //return $actions;
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
        // ------------------------------------------------
		// ----------------- DELETE A LIST ----------------
        // ------------------------------------------------
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids) && !array_keys( $ids, true )) {
                //// Si suppression de plusieurs listes
                //$pids = ilist_get_all_ids_for_stock($ids);
                //$ids  = implode(',', $ids);
                //if (!empty($ids)) {
                //    $wpdb->query("DELETE FROM " . ILIST_TBL_MAIN . " WHERE id IN($ids)");
                //    //$wpdb->query("DELETE FROM " . ILIST_TBL_PRODUCT . " WHERE list_id IN($ids)");
                //    foreach($ids as $id) {
                //        // --------------------------------------------------------------------------
                //        // Check if management stock product AND management stock ILIST are activated
                //        // --------------------------------------------------------------------------
                //        $_product = wc_get_product( $id );
                //        ilist_update_stock($_product, 'increase');
                //        
                //        // Remove from the list the product that has not been purchased 
                //        $wpdb->query("DELETE FROM " . ILIST_TBL_PRODUCT . " WHERE list_id = '$id' AND status = '1'");
                //    }
                //}
            } else {
                // Si suppression d'une seule liste
                if (!empty($ids)) {
                    // Delete the list
                    $wpdb->query("DELETE FROM " . ILIST_TBL_MAIN . " WHERE id IN($ids)");
                    // Get all ID products from this list for stock management
                    $pids = ilist_get_all_ids_for_stock($ids, true); // Status = 1 (A vendre)
                    // Loop on all products
                    foreach($pids as $pid) {
                        // --------------------------------------------------------------------------
                        // Check if management stock product AND management stock ILIST are activated
                        // --------------------------------------------------------------------------
                        $_product = wc_get_product( $pid );
                        ilist_update_stock($_product, 'increase');
                    }
                    // Remove all products from the list that have not been purchased
                    $wpdb->query("DELETE FROM " . ILIST_TBL_PRODUCT . " WHERE list_id = '$ids'");
                }
            }
        }
        // ------------------------------------------------
		// -------------- DESACTIVATE A LIST --------------
        // ------------------------------------------------
        if ('desactivate' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids) && !array_keys( $ids, true )) {
                // ------------------------------------
                // Si désactivation de plusieurs listes
                // ------------------------------------
            } else {
                // Si suppression d'une seule liste
                if (!empty($ids)) {
                    // Update the list (No active => Desactivation)
                    $wpdb->update( 
                        ILIST_TBL_MAIN, 
                        array( 'active' => '0' ), 
                        array( 'id' => $ids ), 
                        array( '%d' ), 
                        array( '%d' ) 
                    );
                    // Get all ID products from this list for stock management
                    $pids = ilist_get_all_ids_for_stock($ids, true); // Status = 1 (A vendre)
                    // Loop on all products
                    foreach($pids as $pid) {
                        // --------------------------------------------------------------------------
                        // Check if management stock product AND management stock ILIST are activated
                        // --------------------------------------------------------------------------
                        $_product = wc_get_product( $pid );
                        ilist_update_stock($_product, 'increase');
                    }
                    // Remove all products from the list that have not been purchased
                    $wpdb->query("DELETE FROM " . ILIST_TBL_PRODUCT . " WHERE list_id = '$ids' AND status = '1'");
                }
            }
        }
        // ------------------------------------------------
        // ---------------- ACTIVATE A LIST ---------------
        // ------------------------------------------------
        if ('activate' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids) && !array_keys( $ids, true )) {
                // ---------------------------------
                // Si activation de plusieurs listes
                // ---------------------------------
            } else {
                // Si activation d'une seule liste
                if (!empty($ids)) {
                    // Update the list (Active => Activation)
                    $wpdb->update( 
                        ILIST_TBL_MAIN, 
                        array( 'active' => '1' ), 
                        array( 'id' => $ids ), 
                        array( '%d' ), 
                        array( '%d' ) 
                    );
                }
            }
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
     * [REQUIRED] This is the most important method
     *
     * It will get rows from database and prepare them to be showed in table
     */
    function prepare_items($search = NULL) {
        global $wpdb;

        // How much records will be shown per page (option screen)
		$screen   = get_current_screen();  // Get screen option
		$user     = get_current_user_id(); // Get current user ID
		$option   = $screen->get_option('per_page', 'option');
		$per_page = get_user_meta($user, $option, true);
		if ( empty ( $per_page) || $per_page < 1 ) {
			$per_page = $screen->get_option( 'per_page', 'default' );
		}
		
        // here we configure table headers, defined in our methods
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM " . ILIST_TBL_MAIN);

        // prepare query params, as usual current page, order by and order direction
		$paged = !empty($_GET["paged"]) ? intval($_GET["paged"]) : '';		
		if (empty($paged) || !is_numeric($paged) || $paged <= 0) $paged = 1;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'date';
        $order   = (isset($_REQUEST['order']) && $_REQUEST['order'] == 'desc') ? strtoupper($_REQUEST['order']) : 'ASC';

		// If the value is not NULL, do a search for it
		if ( $search != NULL ) {
			// Trim Search Term
			$search = trim($search);
			/* Notice how you can search multiple columns for your search term easily, and return one data set */
			$this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . ILIST_TBL_MAIN . " WHERE name LIKE '%%%s%%' OR firstname LIKE '%%%s%%' OR lastname LIKE '%%%s%%' OR email LIKE '%%%s%%' ORDER BY $orderby $order", $search, $search, $search, $search), ARRAY_A);
		} else {
			// [REQUIRED] define $items array
			// notice that last argument is ARRAY_A, so we will retrieve array
			if ($paged > 1) {
				$paged--;
				$this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . ILIST_TBL_MAIN . " ORDER BY $orderby $order LIMIT %d,%d", $paged * $per_page, $per_page), ARRAY_A);
			} else {
				$this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . ILIST_TBL_MAIN . " ORDER BY $orderby $order LIMIT %d", $per_page), ARRAY_A);
			}
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
 * Display the products number by list
 * 
 * @param string $list_id ID attribute value.
 */
function ilist_get_nb_products_by_list($list_id) {
	global $wpdb;
	// Initialization
	$rowcount = 0;
	// Check if list ID
	if ($list_id > 0) {
		$rowcount = $wpdb->get_var("SELECT COUNT(list_id) FROM " . ILIST_TBL_PRODUCT . " WHERE list_id = '$list_id'");
	}
	
	return $rowcount;
}

/**
 * http://codex.wordpress.org/Data_Validation
 * http://codex.wordpress.org/Function_Reference/selected
 *
 * Form page handler checks is there some data posted and tries to save it
 * Also it renders basic wrapper in which we are callin meta box render
 */
function ilist_list_form_page_handler() {

    global $wpdb, $Ilist;

    // Initialization
    $message = '';
    $notice  = '';
    // PASSWORD Algorithm
    $algo_password = $Ilist->algo_password;

    // this is default $item which will be used for new records
    $default = array(
         'id'               => 0
        ,'user_id'          => get_current_user_id()
        ,'date'             => date('Y-m-d H:i:s')
        ,'event_date'       => null
        ,'name'             => ''
        ,'description'      => null
        ,'image'            => null
        ,'firstname'        => ''
        ,'lastname'         => ''
        ,'email'            => ''
        ,'keyaccess'        => ilist_rand_sha1(30)
        ,'password'         => ''
        ,'password_confirm' => ''
        ,'password_hash'    => ''
        ,'password_remove'  => ''
        ,'active'           => 1
    );

    // here we are verifying does this request is post back and have correct nonce
    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        // combine our default item with request params
        $item = shortcode_atts($default, $_REQUEST);
        // validate data, and if all ok save item to database
        // if id is zero insert otherwise update
        $item_valid = ilist_validate_list($item);
        if ($item_valid === true) {
            if ($item['id'] == 0) {
                // Check if new password
                $_password = NULL;
                if (isset($item['password']) && $item['password'] != '') {
                    $_password = password_hash( $item['password'], $algo_password );
                } elseif (isset($item['password_hash']) && $item['password_hash']) {
                    $_password = trim($item['password_hash']);
                } else {
                    $item['password'] = $item['password_confirm'] = $item['password_hash'] = "";
                }
                $result = $wpdb->insert(
                    ILIST_TBL_MAIN,
                    [
                         'user_id'     => $item['user_id']
                        ,'date'        => $item['date']
                        ,'event_date'  => $item['event_date']
                        ,'name'        => $item['name']
                        ,'description' => $item['description']
                        ,'image'       => $item['image']
                        ,'firstname'   => $item['firstname']
                        ,'lastname'    => $item['lastname']
                        ,'email'       => $item['email']
                        ,'keyaccess'   => $item['keyaccess']
                        ,'password'    => $_password
                       , 'active'      => $item['active']
                    ], 
                    [ '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d' ]
                );
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('List saved successfully', ILIST_ID_LANGUAGES);
                    $item['password'] = $_password;
                } else {
                    $notice = __('ERROR when saving list', ILIST_ID_LANGUAGES);
                }
            } else {
                // Check if new password
                $_password = trim( $item['password_hash'] );
                if (isset($item['password']) && $item['password'] != '') {
                    $_password = password_hash( $item['password'], $algo_password );
                } elseif (isset($item['password_remove'])) {
                    $_password = NULL;
                }
                // SQL Request
                $result = $wpdb->update( 
                    ILIST_TBL_MAIN, 
                    [
                         'user_id'     => $item['user_id']
                        ,'date'        => $item['date']
                        ,'event_date'  => $item['event_date']
                        ,'name'        => stripslashes_deep( $item['name'] )
                        ,'description' => stripslashes_deep( $item['description'] )
                        ,'image'       => $item['image']
                        ,'firstname'   => stripslashes_deep( $item['firstname'] )
                        ,'lastname'    => stripslashes_deep( $item['lastname'] )
                        ,'email'       => $item['email']
                        ,'keyaccess'   => $item['keyaccess']
                        ,'password'    => $_password
                        ,'active'      => $item['active']
                    ], 
                    [ 'id' => $item['id'] ], 
                    [ '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d' ],
                    [ '%d' ] 
                );
                if ($result) {
                    $message = __('List updated successfully', ILIST_ID_LANGUAGES);
                    $item['password'] = $_password;
                } else {
                    if (_ILIST_DEBUG) {
                        $wpdb->show_errors();
                        $wpdb->print_error();
                    }
                    $notice = __('ERROR when editing list (no information changed)', ILIST_ID_LANGUAGES);
                }
            }
        } else {
            // if $item_valid not true it contains error message(s)
            $notice = $item_valid;
            $item['password'] = $item['password_confirm'] = "";
        }
    } else {
        // if this is not post back we load item to edit or give new one to create
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . ILIST_TBL_MAIN . " WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('List not found', ILIST_ID_LANGUAGES);
            }
        }
    }

    // here we adding our custom meta box
    $ilist_meta_box_edit_list = __('Edit list', ILIST_ID_LANGUAGES);
    add_meta_box('ilist_form_meta_box', $ilist_meta_box_edit_list, 'ilist_list_form_meta_box_handler', 'ilist', 'normal', 'default');

    ?>

	<div class="wrap page_ilist">
		<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
		<h1>
			<?php
				echo ILIST_NAME . ' :: ';
				_e('List :: ', ILIST_ID_LANGUAGES);
				echo $item['name'];
			?>
			&nbsp;
			<a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=ilist_list');?>">
				<?php _e('Back to lists', ILIST_ID_LANGUAGES)?>
			</a>
			&nbsp;
			<a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=ilist_products&id=' . $item['id']);?>">
				<?php _e('See products', ILIST_ID_LANGUAGES)?>
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
			<input type="hidden" name="id" value="<?php echo $item['id']; ?>">
			<input type="hidden" name="date" value="<?php echo $item['date']; ?>">
			<input type="hidden" name="user_id" value="<?php echo $item['user_id']; ?>">
			<input type="hidden" name="keyaccess" value="<?php echo $item['keyaccess']; ?>">
			<input type="hidden" name="password_hash" value="<?php echo esc_attr($item['password']); ?>">
	
			<div class="metabox-holder" id="poststuff">
				<div id="post-body">
					<div id="post-body-content" class="post-body-ilist">
						<?php /* And here we call our custom meta box */ ?>
						<?php do_meta_boxes('ilist', 'normal', $item); ?>
						<input type="submit" value="<?php _e('Save', ILIST_ID_LANGUAGES)?>" id="submit" class="button-primary" name="submit">
					</div>
				</div>
			</div>
		</form>
	</div>
	<?php
}

/**
 * This function renders our custom meta box
 * $item is row
 *
 * @param $item
 */
function ilist_list_form_meta_box_handler($item) {
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-style', ILIST_URL . 'vendor/Jquery/jquery-ui.min.css');
    ?>
<link href="<?php echo ILIST_URL; ?>vendor/Select2/select2.min.css" rel="stylesheet">
<style>.select2-container { width: 500px !important; }</style>
<script src="<?php echo ILIST_URL; ?>vendor/Select2/select2.min.js"></script>
<script>
    jQuery(function($) {
        $("#user_id").select2();
    });
</script>

<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="active"><?php _e('Active', ILIST_ID_LANGUAGES)?> <strong style="color: red;">*</strong></label>
        </th>
        <td>
			<input type="radio" name="active" value="1" <?php if ($item['active'] == '1') echo 'checked="checked"' ?> />&nbsp;<?php _e('YES', ILIST_ID_LANGUAGES); ?>
			&nbsp;&nbsp;&nbsp;
			<input type="radio" name="active" value="0" <?php if ($item['active'] == '0') echo 'checked="checked"' ?> />&nbsp;<?php _e('NO', ILIST_ID_LANGUAGES); ?>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="event_date"><?php _e('Event date', ILIST_ID_LANGUAGES)?></label>
            <br>
            <span class="description ilist-style-description"><?php _e("If you enter the date of the event, the list will be deactivated on the website after this date", ILIST_ID_LANGUAGES); ?></span>
        </th>
        <td>
            <?php list($event_date, $event_hour) = explode(" ", $item['event_date'], 2); ?>
            <input type="text" style="width: 25%;" class="datepicker" name="event_date" id="event_date" autocomplete="off" value="<?php echo $event_date; ?>" />
            <br>
            <span class="description ilist-style-description">
                <?php _e('ISO Format ( YYYY-MM-DD )', ILIST_ID_LANGUAGES); ?>
            </span>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="password"><?php _e('Password', ILIST_ID_LANGUAGES)?></label>
            <br>
            <span class="description ilist-style-description"><?php _e("You can put a password so that buyers can access the products on the list", ILIST_ID_LANGUAGES); ?></span>
        </th>
        <td>
            <input id="password" name="password" type="password" style="max-width: 200px; width: 100%; margin-bottom: 0.5em;" value="" placeholder="<?php _e('Password', ILIST_ID_LANGUAGES)?>">
            <br>
            <input id="password_confirm" name="password_confirm" type="password" style="max-width: 200px; width: 100%;" value="" placeholder="<?php _e('Password confirm', ILIST_ID_LANGUAGES)?>">
        </td>
    </tr>
    <?php if ( (isset($item['password']) && trim($item['password']) != '')  ) { ?>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="password_remove"><?php _e('Remove password', ILIST_ID_LANGUAGES)?></label>
        </th>
        <td>
			<input type="checkbox" name="password_remove" value="1">&nbsp;<?php _e('Yes', ILIST_ID_LANGUAGES); ?>
        </td>
    </tr>
    <?php } ?>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="name"><?php _e('Creator of this list', ILIST_ID_LANGUAGES)?> <strong style="color: red;">*</strong></label>
        </th>
        <td>
            <select name="user_id" id="user_id" class="select2-container" required>
                <option value=""<?php if (!isset($_GET['action'])) echo ' selected="selected"'; ?>>&mdash; <?php _e('Choose', ILIST_ID_LANGUAGES); ?> &mdash;</option>
                <?php
                $all_users = get_users();
                if ($all_users) {
                    foreach ($all_users as $user) {
                        echo '<option value="' . $user->ID . '" ';
                        if ( $user->ID == esc_attr($item['user_id']) && ( isset($_GET['action']) && $_GET['action'] == 'edit' ) ) echo 'selected="selected"';
                        echo '>';
                        echo esc_html( $user->display_name ) . ' (' . esc_html( $user->user_email ) . ' - ID: ' . $user->ID . ')';
                        echo '</option>';
                    }
                }
                ?>
            </select>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="name"><?php _e('List name', ILIST_ID_LANGUAGES)?> <strong style="color: red;">*</strong></label>
        </th>
        <td>
            <input id="name" name="name" type="text" style="width: 95%" value="<?php echo esc_attr($item['name'])?>"
                   size="50" class="code" placeholder="<?php _e('List name', ILIST_ID_LANGUAGES)?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="image"><?php _e('List image', ILIST_ID_LANGUAGES)?> <strong style="color: red;">*</strong></label>
        </th>
        <td>
            <?php
                if ( isset($item['image']) && $item['image'] != '' ) {
                    echo '<a href="#" id="ilist-upload-img" class="ilist-upload"><img style="max-height: 200px; max-with: 200px;" src="' . $item['image'] . '"></a><br>
                          <a href="#" class="ilist-remove">' . __('Remove image', ILIST_ID_LANGUAGES) . '</a>
                          <input type="hidden" id="image" name="image" value="' . $item['image'] . '">';
                } else {
                    echo '<a href="#" id="ilist-upload-img" class="ilist-upload">' . __('Upload image', ILIST_ID_LANGUAGES) . '</a>
                          <a href="#" class="ilist-remove" style="display: none;">' . __('Remove image', ILIST_ID_LANGUAGES) . '</a>
                          <input type="hidden" id="image" name="image" value="">';
                }
            ?>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="firstname"><?php _e('Firstname', ILIST_ID_LANGUAGES)?> <strong style="color: red;">*</strong></label>
        </th>
        <td>
            <input id="firstname" name="firstname" type="text" style="width: 95%" value="<?php echo esc_attr($item['firstname'])?>"
                   size="50" class="code" placeholder="<?php _e('Firstname', ILIST_ID_LANGUAGES)?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="lastname"><?php _e('Lastname', ILIST_ID_LANGUAGES)?> <strong style="color: red;">*</strong></label>
        </th>
        <td>
            <input id="lastname" name="lastname" type="text" style="width: 95%" value="<?php echo esc_attr($item['lastname'])?>"
                   size="50" class="code" placeholder="<?php _e('Lastname', ILIST_ID_LANGUAGES)?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="email"><?php _e('Email', ILIST_ID_LANGUAGES)?> <strong style="color: red;">*</strong></label>
        </th>
        <td>
            <input id="email" name="email" type="email" style="width: 95%" value="<?php echo esc_attr($item['email'])?>"
                   size="50" class="code" placeholder="<?php _e('Your Email', ILIST_ID_LANGUAGES)?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="description"><?php _e('Description', ILIST_ID_LANGUAGES)?></label>
        </th>
        <td>
            <textarea id="description" name="description" style="width: 95%" rows="5"
                   class="code" placeholder="<?php _e('Description', ILIST_ID_LANGUAGES)?>"><?php echo stripslashes_from_strings_only($item['description'])?></textarea>
        </td>
    </tr>
    <tr class="form-field">
        <th colspan="2" valign="top" scope="row">
            <strong style="color: red;">*</strong> <?php _e('Required fields', ILIST_ID_LANGUAGES); ?>
        </th>
    </tr>
    </tbody>
</table>
<script>
    jQuery(function($){
        // on upload button click
        $('body').on( 'click', '.ilist-upload', function(e) {
            e.preventDefault();
            if (typeof wp_enqueue_media === 'function') {
                wp_enqueue_media(); // JavaScript exception: ERROR that appears on some browsers
            }
            var button = $(this),
            custom_uploader = wp.media({
                title: '<?php _e('Insert image', ILIST_ID_LANGUAGES); ?>',
                library : {
                    // uploadedTo : wp.media.view.settings.post.id, // attach to the current post?
                    type : 'image'
                },
                button: {
                    text: '<?php _e('Use this image', ILIST_ID_LANGUAGES); ?>' // button label text
                },
                multiple: false
            }).on('select', function() { // it also has "open" and "close" events
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                button.html('<img style="max-height: 200px; max-with: 200px;" src="' + attachment.url + '">').next().val(attachment.id).next().show();
                $('.ilist-remove').css('display', 'block');
                $('#image').val( attachment.url );
            }).open();
        });
    
        // on remove button click
        $('body').on('click', '.ilist-remove', function(e){
            e.preventDefault();
            var button = $(this);
            button.next().val(''); // emptying the hidden field
            button.hide().prev().html("<?php _e('Upload image', ILIST_ID_LANGUAGES); ?>");
            $('#image').val(''); // emptying the hidden field
            $('#ilist-upload-img img').remove(); // remove image from dom
            $('#ilist-upload-img').html("<?php _e('Upload image', ILIST_ID_LANGUAGES); ?>");
        });
        
        // Date
        //var ilist_event_date = "yy-mm-dd " + new Date().toLocaleTimeString('fr-FR', { hour: "numeric", minute: "numeric", second: "numeric" });
        $('.datepicker').datepicker({
            dateFormat: "yy-mm-dd"
        });
    
    });
</script>
<?php
}

/**
 * Simple function that validates data and retrieve bool on success
 * and error message(s) on error
 *
 * @param $item
 * @return bool|string
 */
function ilist_validate_list($item) {
    $messages = array();

    if (empty($item['user_id'])) $messages[] = __('Creator of this list is required', ILIST_ID_LANGUAGES);
    if (empty($item['name'])) $messages[] = __('List name is required', ILIST_ID_LANGUAGES);
    if (empty($item['firstname'])) $messages[] = __('Firstname is required', ILIST_ID_LANGUAGES);
    if (empty($item['lastname'])) $messages[] = __('Lastname is required', ILIST_ID_LANGUAGES);
    if (empty($item['email'])) $messages[] = __('Email is required', ILIST_ID_LANGUAGES);
    if (!empty($item['email']) && !is_email($item['email'])) $messages[] = __('The email has an incorrect format', ILIST_ID_LANGUAGES);
    if ((isset($item['password']) && trim($item['password']) != '') && empty($item['password_confirm'])) {
        $messages[] = __('Enter password to confirm', ILIST_ID_LANGUAGES);
    }
    if ( (isset($item['password']) && $item['password'] != '') && (isset($item['password_confirm']) && $item['password_confirm'] != '') ) {
        if (trim($item['password_confirm']) != trim($item['password'])) {
            $messages[] = __("Passwords do not match", ILIST_ID_LANGUAGES);
        }
    }

    if (empty($messages)) return true;
    return implode('<br />', $messages);
}

?>