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
class ilist_Sold_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;    
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'vendu',     // singular name of the listed records
            'plural'    => 'vendus',    // plural name of the listed records
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
        // Return the title contents
        return ilistConvertDate($item['date'], 'FRT');
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
        $_product_id = (isset($item['variation_id']) && $item['variation_id'] > 0) ? $item['variation_id'] : $item['product_id'];
        return '<a href="' . get_permalink($_product_id) . '" title="' . __('See product', ILIST_ID_LANGUAGES) . '">' . $item['product_name'] . '</a>';
    }
    
    
    function column_order_id($item) {
        return '<a href="' . get_edit_post_link($item['order_id']) . '" title="' . __('See order', ILIST_ID_LANGUAGES) . '" target="_blank">' . $item['order_id'] . '</a>';
    }
    
    /**
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_status($item) {
        $class_color = "";
		$item_status = ilist_get_status_info($item['status'], 'title');
        $item_color  = ilist_get_status_info($item['status'], 'color');
		switch($item['status']) {
            case "":
                $item_status = __( 'Participation', ILIST_ID_LANGUAGES );
                $class_color = 'class="ilist_participation"';
            break;
			case "1":
                $class_color = (!isset($item_color)) ? 'class="buy_shop"' : 'style="color: '.$item_color.'"';
            break;
			case "2":
                $class_color = (!isset($item_color)) ? 'class="buy_internet"' : 'style="color: '.$item_color.'"';
            break;
			default:
                $class_color = (!isset($item_color)) ? 'class="buy_other"' : 'style="color: '.$item_color.'"';
            break;
		}
		return '<span '.$class_color.'>' . $item_status . '</span>';
    }

    /**
     * [OPTIONAL] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_list_id($item) {
        // Return the title contents
        return '<a href="?page=ilist_products&id=' . $item['list_id'] .'" title="' . __('See list', ILIST_ID_LANGUAGES) . '">' . ilist_get_list_infos_by_id($item['list_id'], 'name') . '</a>';
    }
    
    /**
     * [OPTIONAL] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_customer($item) {
		if (isset($item['status']) && $item['status'] > 1) {
			if (isset($item['customer']) && trim($item['customer']) != "")
				return $item['customer'];
			else
				return __('--', ILIST_ID_LANGUAGES);
		} else {
            if (isset($item['date'])) {
                return $item['customer'];
            } else {
    			return __('--', ILIST_ID_LANGUAGES);
            }
		}
    }
	
    /**
     * [OPTIONAL] this is a default column renderer
     *
     * @param $item - row (key, value array)
     * @param $column_name - string (key)
     * @return HTML
     */
    function column_active($item) {
        return ($item['active']) ? '<i class="fa fa-check-square-o" style="color: green;" aria-hidden="true"></i>' : '<i class="fa fa-circle" style="color: red;" aria-hidden="true"></i>';
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
            'date'         => __('Date', ILIST_ID_LANGUAGES),
            'order_id'     => __('Order number', ILIST_ID_LANGUAGES),
            'product_name' => __('Product name', ILIST_ID_LANGUAGES),
            'list_id'      => __('List', ILIST_ID_LANGUAGES),
            'customer'     => __('Customer', ILIST_ID_LANGUAGES),
            'status'       => __('Status', ILIST_ID_LANGUAGES),
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
            //'date'         => array('date', false),   //true means it's already sorted
            'order_id'     => array('order_id', true),
            'product_name' => array('product', false),
            'list_id'      => array('list_id', false),  //true means it's already sorted
            'customer'     => array('customer', false),
            'status'    => array('status', false),
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
    <br>
    Recherches possibles sur : <strong>date</strong> - <strong>n° de commande</strong> - <strong>nom de produit</strong> - <strong>acheteur</strong>
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
            //'delete' => __('Delete', ILIST_ID_LANGUAGES),
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
        $total_items_1 = $wpdb->get_var( "SELECT COUNT(id) FROM " . ILIST_TBL_PRODUCT . " WHERE date != '' AND status > 1" );
        $total_items_2 = $wpdb->get_var( "SELECT COUNT(id) FROM " . ILIST_TBL_CAGNOTTE );
        $total_items   = $total_items_1 + $total_items_2;
        
        // prepare query params, as usual current page, order by and order direction
		$paged = !empty($_GET["paged"]) ? intval($_GET["paged"]) : '';		
		if (empty($paged) || !is_numeric($paged) || $paged <= 0) $paged = 1;
        $orderby = (isset($_REQUEST['orderby']) && is_array($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'date';
        $order   = (isset($_REQUEST['order']) && is_array($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'desc';
        $listid  = (isset($_REQUEST['id'])) ? intval($_REQUEST['id']) : 0;
        
		// If the value is not NULL, do a search for it
		if ( $search != NULL ) {
			// Trim Search Term
			$search = trim($search);
			/* Notice how you can search multiple columns for your search term easily, and return one data set */
            $this->items = $wpdb->get_results( $wpdb->prepare("
                                            SELECT id AS id, list_id AS list_id, order_id AS order_id, date AS date, product_id AS product_id, variation_id AS variation_id, product_name AS product_name, customer AS customer, status AS status, null AS participation
                                            FROM " . ILIST_TBL_PRODUCT . "
                                            WHERE (order_id LIKE '%%%s%%' OR date LIKE '%%%s%%' OR product_name LIKE '%%%s%%' OR customer LIKE '%%%s%%') AND (date != '' AND status > 1)
                                            UNION ALL
                                            SELECT t1.id AS id, t1.list_id AS list_id, t1.order_id AS order_id, t1.date AS date, t1.product_id AS product_id, t1.variation_id AS variation_id, t1.product_name AS product_name, t1.customer AS customer, null AS status, t1.participation AS participation
                                            FROM " . ILIST_TBL_CAGNOTTE . " AS t1
                                            WHERE (t1.order_id LIKE '%%%s%%' OR t1.date LIKE '%%%s%%' OR t1.product_name LIKE '%%%s%%' OR t1.customer LIKE '%%%s%%')
                                            ORDER BY $orderby $order
                                        ", $search, $search, $search, $search, $search, $search, $search, $search), ARRAY_A );
            
		} else {
			// [REQUIRED] define $items array
			// notice that last argument is ARRAY_A, so we will retrieve array
			if (isset($paged) && $paged > 1) {
				$paged--;
                $this->items = $wpdb->get_results( $wpdb->prepare("
                                                SELECT id AS id, list_id AS list_id, order_id AS order_id, date AS date, product_id AS product_id, variation_id AS variation_id, product_name AS product_name, customer AS customer, status AS status, null AS participation
                                                FROM " . ILIST_TBL_PRODUCT . "
                                                WHERE date != '' AND status > 1
                                                UNION ALL
                                                SELECT t1.id AS id, t1.list_id AS list_id, t1.order_id AS order_id, t1.date AS date, t1.product_id AS product_id, t1.variation_id AS variation_id, t1.product_name AS product_name, t1.customer AS customer, null AS status, t1.participation AS participation
                                                FROM " . ILIST_TBL_CAGNOTTE . " AS t1
                                                ORDER BY $orderby $order LIMIT %d,%d
                                            ", $paged * $per_page, $per_page), ARRAY_A );               
			} else {
                $this->items = $wpdb->get_results( $wpdb->prepare("
                                                SELECT id AS id, list_id AS list_id, order_id AS order_id, date AS date, product_id AS product_id, variation_id AS variation_id, product_name AS product_name, customer AS customer, status AS status, null AS participation
                                                FROM " . ILIST_TBL_PRODUCT . "
                                                WHERE date != '' AND status > 1
                                                UNION ALL
                                                SELECT t1.id AS id, t1.list_id AS list_id, t1.order_id AS order_id, t1.date AS date, t1.product_id AS product_id, t1.variation_id AS variation_id, t1.product_name AS product_name, t1.customer AS customer, null AS status, t1.participation AS participation
                                                FROM " . ILIST_TBL_CAGNOTTE . " AS t1
                                                ORDER BY $orderby $order LIMIT %d
                                            ", $per_page), ARRAY_A );                
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

?>