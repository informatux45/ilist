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
class ilist_List_Type_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;    
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'type',     // singular name of the listed records
            'plural'    => 'types',    // plural name of the listed records
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
    function column_title($item) {
        // Build row actions
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on current page
        // also notice how we use $this->_args['singular'] so in this example it will be something like &person=2
		$item_unauthorized_to_delete = [1,2];
		$actions = array(
			''        => '<span style="color:silver">ID: '.$item['id'].'</span>',
			'edit'    => sprintf('<a href="?page=%s&action=%s&id=%s">%s</a>', 'ilist_types', 'edit', $item['id'], __('Update', ILIST_ID_LANGUAGES)),
			'delete'  => (is_array($item_unauthorized_to_delete) && in_array($item['id'], $item_unauthorized_to_delete)) ? __('Undeletable', ILIST_ID_LANGUAGES) : sprintf('<a onclick="return ilist_confirm_delete(\''.__('this type', ILIST_ID_LANGUAGES).'\');" href="?page=%s&action=%s&id=%s">%s</a>', $_REQUEST['page'], 'delete', $item['id'], __('Delete', ILIST_ID_LANGUAGES)),
		);

        // Return the title contents
        return sprintf('%1$s %2$s',
            /*$1%s*/ $item['title'],
            /*$2%s*/ $this->row_actions($actions)
        );
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
     * [OPTIONAL] this is example, how to render column with actions,
     * when you hover row "Edit | Delete" links showed
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_color($item) {
        $color = "";
        if (isset($item['color'])) {
            $color = "<span style='color: " . $item['color'] . ";'>&bullet;</span>";
        } else {
            switch($item['id']) {
                default: $color = "<span class='buy_other'>&bullet;</span>"; break;
                case 1: $color = "<span class='buy_shop'>&bullet;</span>"; break;
                case 2: $color = "<span class='buy_internet'>&bullet;</span>"; break;
            }
        }
        return '<strong style="font-size: 5em;">' . $color . '</strong>';
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
            'cb'          => '<input type="checkbox" />', //Render a checkbox instead of text
            //'id'          => __('id', ILIST_ID_LANGUAGES),
            'title'       => __('Name', ILIST_ID_LANGUAGES),
            'description' => __('Description', ILIST_ID_LANGUAGES),
            'color'       => __('Color', ILIST_ID_LANGUAGES),
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
            //'id'          => array('id', true),   //true means it's already sorted
            'title'       => array('title', false),  //true means it's already sorted
            'description' => array('description', false),  //true means it's already sorted
            'color'       => array('color', false),  //true means it's already sorted
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
            'delete' => __('Delete', ILIST_ID_LANGUAGES),
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
		
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM " . ILIST_TBL_TYPE . " WHERE id IN($ids)");
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
	 * Displays the product add box.
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param string $list_id ID attribute value for the list input field.
	 */
	public function type_box( $type_id = NULL ) {
		global $wpdb;

        ?>
        <p class="type-box">
			
			<?php
				if ($type_id) {
					$type_form = 'edit';
					$type_id   = intval($type_id);
					$type_info = $wpdb->get_row( "SELECT * FROM " . ILIST_TBL_TYPE . " WHERE id = $type_id" );
				} else {
					$type_form = 'add';
				}
				// Initialization FORM
				$type_title       = (isset($type_info) && $type_info->title) ? $type_info->title : '';
				$type_description = (isset($type_info) && $type_info->description) ? $type_info->description : '';
                if (isset($type_info) && $type_info->color) {
                    $type_color = $type_info->color;
                } else {
                    switch( $type_id ) {
                        default: $type_color = "#ce8200"; break; // Autres
                        case 1: $type_color = "#7ad03a"; break;  // A vendre
                        case 2: $type_color = "#00ced1"; break;  // Achat par Internet
                    }
                }
			?>
			
			<h2>
				<?php
					if ($type_form == 'add')
						_e("Add type", ILIST_ID_LANGUAGES);
					else
						_e("Edit type", ILIST_ID_LANGUAGES);
				?>
			</h2>
            
            <link rel="stylesheet" href="<?php echo ILIST_URL; ?>vendor/ColorPicker/coloris.min.css" />
            <script src="<?php echo ILIST_URL; ?>vendor/ColorPicker/coloris.min.js"></script>
            <style> .clr-alpha { display: none !important; } </style>

			<div class="form-field form-required term-name-wrap">
				<label for="title"><?php _e('Status Title', ILIST_ID_LANGUAGES); ?> <span class='ilist-red'>*</span></label>
				<input name="title" id="title" value="<?php echo $type_title; ?>" size="40" aria-required="true" type="text">
				<p><?php _e('Status name of your product.', ILIST_ID_LANGUAGES); ?></p>
			</div>
			
			<div class="form-field term-description-wrap">
				<label for="description"><?php _e('Description', ILIST_ID_LANGUAGES); ?></label>
				<textarea name="description" id="description" rows="5" cols="40"><?php echo $type_description; ?></textarea>
				<p><?php _e("Status description. You can leave it blank.", ILIST_ID_LANGUAGES); ?></p>
			</div>
            
            <div class="form-field term-color-wrap square">
                <label for="color"><?php _e("Text color", ILIST_ID_LANGUAGES); ?></label>
                <input type="text" name="color" id="color" class="coloris" value="<?php echo $type_color; ?>">
                <p><?php _e("Status text color in sales/products tab", ILIST_ID_LANGUAGES); ?></p>
            </div>
			
			<p><span class='ilist-red'>*</span> <?php _e("Required fields", ILIST_ID_LANGUAGES); ?></p>

            <input type="hidden" name="type_id" value="<?php echo $type_id; ?>" />
            <input type="hidden" name="type_box" value="<?php echo $type_form; ?>" />
			<br>
		</p>
		<p class="type-box">
            <?php
				if ($type_form == 'add') {
					//echo '<input type="submit" class="button button-primary" id="type-add-submit" value="' . __("Add new type", ILIST_ID_LANGUAGES) . '">';
					submit_button( __( 'Add new type', ILIST_ID_LANGUAGES ) );
				} else {
					submit_button( __( 'Edit type', ILIST_ID_LANGUAGES ), 'primary', 'submit-form', false );
					echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					echo '<a href="' . get_admin_url(get_current_blog_id(), 'admin.php?page=ilist_types') . '" class="button">' . __("Cancel", ILIST_ID_LANGUAGES) . '</a>';
				}
			?>
        </p>
        <script>
            Coloris({
              el: '.coloris',
              format: 'hex', // hex, rgb, mixed (default)
              swatches: [
                '#264653',
                '#2a9d8f',
                '#e9c46a',
                '#f4a261',
                '#e76f51',
                '#d62828',
                '#023e8a',
                '#0077b6',
                '#0096c7',
                '#00b4d8',
                '#48cae4',
              ]
            });
        </script>
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
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM " . ILIST_TBL_TYPE);

        // prepare query params, as usual current page, order by and order direction
		$paged = !empty($_GET["paged"]) ? intval($_GET["paged"]) : '';		
		if (empty($paged) || !is_numeric($paged) || $paged <= 0) $paged = 1;
        $orderby = (isset($_REQUEST['orderby']) && is_array($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';
        $order = (isset($_REQUEST['order']) && is_array($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

		// If the value is not NULL, do a search for it
		if ( $search != NULL ) {
			// Trim Search Term
			$search = trim($search);
			/* Notice how you can search multiple columns for your search term easily, and return one data set */
			$this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . ILIST_TBL_TYPE . " WHERE title LIKE '%%%s%%' OR description LIKE '%%%s%%' ORDER BY $orderby $order", $search, $search), ARRAY_A);
		} else {
			// [REQUIRED] define $items array
			// notice that last argument is ARRAY_A, so we will retrieve array
			if ($paged > 1)
				$this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . ILIST_TBL_TYPE . " ORDER BY $orderby $order LIMIT %d,%d", $per_page, $paged), ARRAY_A);
			else
				$this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . ILIST_TBL_TYPE . " ORDER BY $orderby $order LIMIT %d", $per_page), ARRAY_A);
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
 * http://codex.wordpress.org/Data_Validation
 * http://codex.wordpress.org/Function_Reference/selected
 *
 * Form page handler checks is there some data posted and tries to save it
 * Also it renders basic wrapper in which we are callin meta box render
 */
function ilist_list_type_form_page_handler() {
    global $wpdb;

    // Initialization
    $message = '';
    $notice  = '';

    // this is default $item which will be used for new records
    $default = array(
        'id'    => 0,
        'title' => '',
        'name'  => '',
    );

    // here we are verifying does this request is post back and have correct nonce
    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        // combine our default item with request params
        $item = shortcode_atts($default, $_REQUEST);
        // validate data, and if all ok save item to database
        // if id is zero insert otherwise update
        $item_valid = ilist_validate_type($item);
        if ($item_valid === true) {
            if ($item['id'] == 0) {
                $result = $wpdb->insert(ILIST_TBL_TYPE, $item);
                $item['id'] = $wpdb->insert_id;
                if ($result) {
                    $message = __('Type saved successfully', ILIST_ID_LANGUAGES);
                } else {
                    $notice = __('ERROR when saving list', ILIST_ID_LANGUAGES);
                }
            } else {
                $result = $wpdb->update(ILIST_TBL_TYPE, stripslashes_deep($item), array('id' => $item['id']));
                if ($result) {
                    $message = __('Type updated successfully', ILIST_ID_LANGUAGES);
                } else {
                    if (_ILIST_DEBUG) {
                        $wpdb->show_errors();
                        $wpdb->print_error();
                    }
                    $notice = __('ERROR when editing type (no information changed)', ILIST_ID_LANGUAGES);
                }
            }
        } else {
            // if $item_valid not true it contains error message(s)
            $notice = $item_valid;
        }
    } else {
        // if this is not post back we load item to edit or give new one to create
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . ILIST_TBL_TYPE . " WHERE id = %d", $_REQUEST['id']), ARRAY_A);
            if (!$item) {
                $item = $default;
                $notice = __('Type not found', ILIST_ID_LANGUAGES);
            }
        }
    }

    // here we adding our custom meta box
    $ilist_meta_box_edit_type = __('Edit type', ILIST_ID_LANGUAGES);
    add_meta_box('ilist_type_form_meta_box', $ilist_meta_box_edit_type, 'ilist_list_type_form_meta_box_handler', 'ilist', 'normal', 'default');

    ?>

	<div class="wrap page_ilist">
		<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
		<h2>
			<?php
				echo ILIST_NAME . ' :: ';
				_e('Type :: ', ILIST_ID_LANGUAGES);
				echo $item['title'];
			?>
			&nbsp;
			<a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=ilist_list');?>">
				<?php _e('Back to types', ILIST_ID_LANGUAGES)?>
			</a>
			&nbsp;
			<a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=ilist_products&id=' . $item['id']);?>">
				<?php _e('See products', ILIST_ID_LANGUAGES)?>
			</a>
		</h2>
	
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
function ilist_list_type_form_meta_box_handler($item) {
    ?>

<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
    <tbody>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="title"><?php _e('Type title', ILIST_ID_LANGUAGES)?> *</label>
        </th>
        <td>
            <input id="title" name="title" type="text" style="width: 95%" value="<?php echo esc_attr($item['title'])?>"
                   size="50" class="code" placeholder="<?php _e('Type title', ILIST_ID_LANGUAGES)?>" required>
        </td>
    </tr>
    <tr class="form-field">
        <th valign="top" scope="row">
            <label for="name"><?php _e('Type name', ILIST_ID_LANGUAGES)?> *</label>
        </th>
        <td>
            <input id="name" name="name" type="text" style="width: 95%" value="<?php echo esc_attr($item['name'])?>"
                   size="50" class="code" placeholder="<?php _e('Type name', ILIST_ID_LANGUAGES)?>" required>
        </td>
    </tr>
    </tbody>
</table>
<?php
}

/**
 * Simple function that validates data and retrieve bool on success
 * and error message(s) on error
 *
 * @param $item
 * @return bool|string
 */
function ilist_validate_type($item) {
    $messages = array();

    if (empty($item['title'])) $messages[] = __('Type title is required', ILIST_ID_LANGUAGES);

    if (empty($messages)) return true;
    return implode('<br>', $messages);
}

?>