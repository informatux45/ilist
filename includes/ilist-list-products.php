<?php
/** Blocking direct access to plugin
=============================================== */
defined('ABSPATH') or die('Are you crazy!');

/** Create your menu outside the class
=============================================== */
add_action('admin_menu','ilist_list_products_menu');
// Render your admin menu outside the class
function ilist_list_products_menu() {
    $hook_menu_ilist_products = add_submenu_page(ILIST_ID, __('Products', ILIST_ID_LANGUAGES), __('The products', ILIST_ID_LANGUAGES), 'manage_options', 'ilist_products', 'ilist_products_page_handler');
	add_action( "load-$hook_menu_ilist_products", 'add_options_ilist_products' );
	
	add_submenu_page(ILIST_ID, __('Edit product', ILIST_ID_LANGUAGES), __('Edit product', ILIST_ID_LANGUAGES), 'activate_plugins', 'ilist_product', 'ilist_product_form_page_handler');
}

/** Add a Filter to Save Our Option
=============================================== */
add_filter('set-screen-option', 'ilist_set_products_option', 10, 3);
function ilist_set_products_option($status, $option, $value) {
    if ( 'ilist_products_per_page' == $option ) return $value;
		return $status;
}

/** Add screen options to the page ilist
=============================================== */
function add_options_ilist_products() {
	global $myListTable;
	$screen = get_current_screen();
	$option = 'per_page';
	$args = array(
		   'label' => __('All products', ILIST_ID_LANGUAGES),
		   'default' => 50,
		   'option' => 'ilist_products_per_page'
		   );
	add_screen_option( $option, $args );
	$myListTable = new ilist_List_Product_Table();
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
function ilist_products_page_handler() {
    global $wpdb;

    $tableIlistProducts = new ilist_List_Product_Table();
	// Fetch, prepare, sort, and filter our data...
	$tableIlistProducts->prepare_items();

    $message = $count = '';
    // Delete product
	if ('delete' === $tableIlistProducts->current_action()) {
        $count   = (isset($_REQUEST['id']) && is_array($_REQUEST['id'])) ? count($_REQUEST['id']) : 1;
        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Product(s) successfully deleted : %d', ILIST_ID_LANGUAGES), $count) . '</p></div>';
    } elseif (isset($_REQUEST['action'])) {
        $count   = (isset($_REQUEST['pid']) && is_array($_REQUEST['pid'])) ? count($_REQUEST['pid']) : 1;
		$message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Product(s) status successfully updated : %d', ILIST_ID_LANGUAGES), $count) . '</p></div>';
	}
	// Add product
    if ('add' === $tableIlistProducts->current_action()) {
		if (isset($_POST['is_result_insert'])) {
			$message = '<div class="updated below-h2" id="message"><p>' . __('Product added successfully to the list', ILIST_ID_LANGUAGES) . '</p></div>';
			// --------------------------------------------------------------------------
			// Check if management stock product AND management stock ILIST are activated
			// --------------------------------------------------------------------------
			ilist_update_stock(wc_get_product( $_POST['is_result_insert'] ), 'decrease');
		} else {
			$message = '<div class="error below-h2" id="message"><p>' . __("Error adding product", ILIST_ID_LANGUAGES) . '</p></div>';
		}
	}
	
    ?>

<link href="<?php echo ILIST_URL; ?>/vendor/Select2/select2.min.css" rel="stylesheet">
<script src="<?php echo ILIST_URL; ?>/vendor/Select2/select2.min.js"></script>

<script>
    jQuery(function($) {
        // Click on editable zone (Note)
        $('.ilist-note-editable').on('click', function(e) {
            e.preventDefault();
            // -------------------------------------
            var id = jQuery(this).attr('data-id');
            // -------------------------------------
            let textarea = document.getElementById("editable_"+id);
            textarea.contentEditable = true;
            textarea.style.border = "1px solid burlywood";
            // -------------------------------------
            let textarea_buttons = document.getElementById("editable_buttons_"+id);
            textarea_buttons.style.display = "block";
            // -------------------------------------
        });
        
        // Click on editable buttons (Cancel OR ok - Note)
        $('.ilist-note-editable-buttons a.button').on('click', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-itemid');
            var textarea = document.getElementById("editable_"+id);
            var textarea_buttons = document.getElementById("editable_buttons_"+id);
            console.log('editable_'+id);
            // -------------------------------------
            var new_content = document.getElementById('editable_'+id).textContent;
            var this_item = $(this);
            // -------------------------------------
            if (this_item.hasClass('ok')) {
                // -------------------------------------
                // Ok Button - Save content
                // -------------------------------------
                jQuery.ajax({
                    url: ilist_admin_ajax.ajaxurl,
                    type: "POST",
                    data: {
                        //_ajax_nonce: ilist_ajax_script.nonce, // nonce
                        action: 'ilist_action_ajax',
                        switch_a: 'save_note_product',
                        note: new_content,
                        pid: id
                    },
                    datatype: 'json'
                })
                .done(function (response) {
                    alert(response);
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                });
            } else {
                // -------------------------------------
                // Cancel Button - Don't save content
                // -------------------------------------
            }
            // Remove style
            textarea.style.border = "0px";
            textarea_buttons.style.display = "none";
            return false;
        });
    });

</script>

<div class="wrap page_ilist">

    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
    <h1>
		<?php
			echo ILIST_NAME . ' :: ';
			_e('Products from the list :: ', ILIST_ID_LANGUAGES);
			echo ilist_get_list_info($_REQUEST['id'], 'name');
		?>
		&nbsp;
		<a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=ilist_list'); ?>">
			<?php _e('Back to lists', ILIST_ID_LANGUAGES)?>
		</a>
    </h1>
    <?php echo $message; ?>
	
	<!--<div class='ilist_help_message'>
		<img style="vertical-align: middle; width: 50px;" align="middle" src="<?php echo ILIST_URL . 'images/icon-ilist.png'; ?>" alt="Help" /><span><?php echo sprintf( __('You can add / edit / delete the status of your products from <a href="%s">status management</a>', ILIST_ID_LANGUAGES), get_admin_url(get_current_blog_id(), 'admin.php?page=ilist_types') ); ?></span>
	</div>-->
	
	<hr style="margin-top: 2em;">
	<form method="post">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <input type="hidden" name="action" value="add" />
		<?php $tableIlistProducts->productadd_box(intval($_REQUEST['id'])); ?>
	</form>
	<hr>
	
    <form id="list-products-table" method="GET">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $tableIlistProducts->display() ?>
    </form>

</div>

<?php
}

?>