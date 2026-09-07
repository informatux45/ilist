<?php
/** Blocking direct access to plugin
=============================================== */
defined('ABSPATH') or die('Are you crazy!');

/** Create your menu outside the class
=============================================== */
add_action('admin_menu','ilist_list_menu');
// Render your admin menu outside the class
function ilist_list_menu() {
    $hook_menu_ilist = add_submenu_page(ILIST_ID, __('All Lists', ILIST_ID_LANGUAGES), __('All Lists', ILIST_ID_LANGUAGES), 'manage_options', 'ilist_list', 'ilist_page_handler');
	add_action( "load-$hook_menu_ilist", 'add_options_ilist' );
	
	add_submenu_page(ILIST_ID, __('Add new list', ILIST_ID_LANGUAGES), __('Add new list', ILIST_ID_LANGUAGES), 'activate_plugins', 'ilist_form', 'ilist_list_form_page_handler');
}

/** Add a Filter to Save Our Option
=============================================== */
add_filter('set-screen-option', 'ilist_set_option', 10, 3);
function ilist_set_option($status, $option, $value) {
    if ( 'ilist_per_page' == $option ) return $value;
		return $status;
}

/** Add screen options to the page ilist
=============================================== */
function add_options_ilist() {
	global $myListTable;
	$screen = get_current_screen();
	$option = 'per_page';
	$args = array(
		   'label' => __('All Lists', ILIST_ID_LANGUAGES),
		   'default' => 10,
		   'option' => 'ilist_per_page'
		   );
	add_screen_option( $option, $args );
	$myListTable = new ilist_List_Table();
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
function ilist_page_handler() {
    global $wpdb, $ilist_framework;

    $tableList = new ilist_List_Table();
	//Fetch, prepare, sort, and filter our data...
	if( isset($_POST['s']) ){
		$tableList->prepare_items($_POST['s']);
	} else {
		$tableList->prepare_items();
	}

	// Initialization
    $message = $count = '';
    if ('delete' === $tableList->current_action()) {
        $count   = (isset($_REQUEST['id']) && is_array($_REQUEST['id'])) ? count($_REQUEST['id']) : 1;
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('List(s) successfully deleted : %d', ILIST_ID_LANGUAGES), $count) . '</p></div>';
    }
    if ('desactivate' === $tableList->current_action()) {
        $count   = (isset($_REQUEST['id']) && is_array($_REQUEST['id'])) ? count($_REQUEST['id']) : 1;
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('List(s) successfully desactivated : %d', ILIST_ID_LANGUAGES), $count) . '</p></div>';
    }
    if ('activate' === $tableList->current_action()) {
        $count   = (isset($_REQUEST['id']) && is_array($_REQUEST['id'])) ? count($_REQUEST['id']) : 1;
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('List(s) successfully activated : %d', ILIST_ID_LANGUAGES), $count) . '</p></div>';
    }
	
    ?>
    
<link href="<?php echo ILIST_URL; ?>vendor/fontawesome/<?php echo ILIST_AWESOME_VERSION; ?>/css/fontawesome.min.css" rel="stylesheet">
<link href="<?php echo ILIST_URL; ?>vendor/fontawesome/<?php echo ILIST_AWESOME_VERSION; ?>/css/brands.css" rel="stylesheet">
<link href="<?php echo ILIST_URL; ?>vendor/fontawesome/<?php echo ILIST_AWESOME_VERSION; ?>/css/solid.css" rel="stylesheet">
<div class="wrap page_ilist">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h1>
		<?php
			echo ILIST_NAME . ' :: ';
			_e('All Lists', ILIST_ID_LANGUAGES);
		?>
    </h1>
    &nbsp;
    <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=ilist_form');?>">
        <?php _e('Add new list', ILIST_ID_LANGUAGES)?>
    </a>
    <?php
        // Main menu ILIST Kado
        echo $ilist_framework->nav_tabs();
    ?>
    <br>
    
    <?php echo $message; ?>
	
	<form method="post">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
	  <?php
		$ilist_search_name_button = __('Search in lists', ILIST_ID_LANGUAGES);
		$tableList->search_box($ilist_search_name_button, 'ilist-search-id'); ?>
	</form>

    <form id="lists-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $tableList->display() ?>
    </form>

</div>
<?php
}

?>