<?php
/** Blocking direct access to plugin
=============================================== */
defined('ABSPATH') or die('Are you crazy!');

/** Create your menu outside the class
=============================================== */
add_action('admin_menu','ilist_list_type_menu');
// Render your admin menu outside the class
function ilist_list_type_menu() {
    $hook_menu_type_ilist = add_submenu_page(ILIST_ID, __('All types', ILIST_ID_LANGUAGES), __('All types', ILIST_ID_LANGUAGES), 'manage_options', 'ilist_types', 'ilist_page_type_handler');
	add_action( "load-$hook_menu_type_ilist", 'add_options_type_ilist' );
	
	add_submenu_page(ILIST_ID, __('Add new type', ILIST_ID_LANGUAGES), __('Add new type', ILIST_ID_LANGUAGES), 'activate_plugins', 'ilist_type_form', 'ilist_list_type_form_page_handler');
}

/** Add a Filter to Save Our Option
=============================================== */
add_filter('set-screen-option', 'ilist_set_type_option', 10, 3);
function ilist_set_type_option($status, $option, $value) {
    if ( 'ilist_types_per_page' == $option ) return $value;
		return $status;
}

/** Add screen options to the page ilist
=============================================== */
function add_options_type_ilist() {
	global $myListTable;
	$screen = get_current_screen();
	$option = 'per_page';
	$args = array(
		   'label'   => __('All types', ILIST_ID_LANGUAGES),
		   'default' => 50,
		   'option'  => 'ilist_types_per_page'
		   );
	add_screen_option( $option, $args );
	$myListTable = new ilist_List_Type_Table();
}

/**
 * List page handler
 *
 * This function renders our custom table
 * Notice how we display message about successfull deletion
 * Actualy this is very easy, and you can add as many features
 * as you want.
 *
 * Look into /wp-admin/includes/class-wp-*-list-table.php for examples
 */
function ilist_page_type_handler() {
    global $wpdb, $ilist_framework;

    $tableType = new ilist_List_Type_Table();
	//Fetch, prepare, sort, and filter our data...
	if( isset($_POST['s']) ){
		$tableType->prepare_items($_POST['s']);
	} else {
		$tableType->prepare_items();
	}

	// Initialization
    $message = '';
    if ('delete' === $tableType->current_action()) {
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Type(s) successfully deleted : %d', ILIST_ID_LANGUAGES), count($_REQUEST['id'])) . '</p></div>';
    }
	
    ?>
	
<div class="wrap nosubsub">

	<div class="page_ilist">
		<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
		<h1>
			<?php
				echo ILIST_NAME . ' :: ';
				_e('All types', ILIST_ID_LANGUAGES);
			?>
		</h1>
	</div>
    <?php
        // Main menu ILIST Kado
        echo $ilist_framework->nav_tabs();
        echo '<br>';

		// Initialization
		$message_form = '';
		$notice_form  = '';
	
		// this is default $item which will be used for new records
		$default = array(
			'id'          => 0,
			'title'       => '',
			'description' => '',
            'color'       => '',
		);
	
		// here we are verifying does this request is post back and have correct nonce
		if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__)) && $_POST['type_box'] != '') {
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
						$message_form = __('Type saved successfully', ILIST_ID_LANGUAGES);
					} else {
						$notice_form = __('ERROR when saving list', ILIST_ID_LANGUAGES);
					}
				} else {
					$result = $wpdb->update(ILIST_TBL_TYPE, stripslashes_deep($item), array('id' => $item['id']));
					if ($result) {
						$message_form = __('Type updated successfully', ILIST_ID_LANGUAGES);
					} else {
						if (_ILIST_DEBUG) {
							$wpdb->show_errors();
							$wpdb->print_error();
						}
						$notice_form = __('ERROR when editing type (no information changed)', ILIST_ID_LANGUAGES);
					}
				}
			} else {
				// if $item_valid not true it contains error message(s)
				$notice_form = $item_valid;
			}
		}
	
		// Show message form or notice form
		if ( !empty($notice_form) ) {
			echo '<div id="notice" class="error"><p>' . $notice_form . '</p></div>';
		}
		if ( !empty($message_form) ) {
			echo '<div id="message" class="updated"><p>' . $message_form . '&nbsp;&nbsp;&nbsp;<a class="button" href="' . get_admin_url(get_current_blog_id(), 'admin.php?page=ilist_types') . '">' . __('Reload this page', ILIST_ID_LANGUAGES) . '</a></p></div>';
		}
	
	// Show message from table list actions
	echo $message; ?>
	
		<form method="post" class="search-form wp-clearfix">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		  <?php
			$ilist_search_name_button = __('Search in types', ILIST_ID_LANGUAGES);
			$tableType->search_box($ilist_search_name_button, 'ilist-search-id'); ?>
		</form>
		
	<div id="wpbody-content">
		
		<div id="col-container" class="wp-clearfix">
			
			<div id="col-left" class="page_ilist">
				
				<div class="col-wrap">
					<div class="form-wrap">
						<form id="type_box" method="post" class="validate">
							<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
							<input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
							<?php
								$ilist_request_id = (isset($_REQUEST['id']) && $_REQUEST['id']) ? intval($_REQUEST['id']) : null;
								$tableType->type_box($ilist_request_id); ?>
						</form>
					</div>
				</div>
				
			</div> <!-- End #col-left -->
				
			<div id="col-right">
			
				<div class="wrap page_ilist">
				
					<form id="lists-table" method="GET">
						<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
						<?php $tableType->display() ?>
					</form>
					
					<div class="form-wrap edit-term-notes">
						<p>
							<?php _e("<strong> Note: </ strong> <br> Status with <strong>ID 1 and 2</strong> can not be deleted. They are part of the internal process of the ILIST plugin.<br>You can add as many as you want for the good operation of your lists.", ILIST_ID_LANGUAGES); ?>
						</p>
					</div>
				
				</div> <!-- End .wrap .page_ilist -->
			
			</div> <!-- End #col-right -->
		</div> <!-- End #col-container -->
	</div> <!-- End #wpbody-content -->

</div> <!-- End .wrap .nosubsub -->

<?php
}

?>