<?php
/** Blocking direct access to plugin
=============================================== */
defined('ABSPATH') or die('Are you crazy!');

/** Create your menu outside the class
=============================================== */
add_action('admin_menu','ilist_sold_menu');
if (!function_exists("ilist_sold_menu")) {
    function ilist_sold_menu() {
        $hook_menu_ilist = add_submenu_page('ilist', __('All sales', ILIST_ID_LANGUAGES), __('All sales', ILIST_ID_LANGUAGES), 'manage_options', 'ilist_sold', 'ilist_sold_page_handler');
        add_action( "load-$hook_menu_ilist", 'add_options_ilist_sold' );
    }
}

/** Add a Filter to Save Our Option
=============================================== */
add_filter('set-screen-option', 'ilist_sold_set_option', 10, 3);
if (!function_exists("ilist_sold_set_option")) {
    function ilist_sold_set_option($status, $option, $value) {
        if ( 'ilist_sold_per_page' == $option ) return $value;
            return $status;
    }
}

/** Add screen options to the page ilist sold
=============================================== */
if (!function_exists("add_options_ilist_sold")) {
    function add_options_ilist_sold() {
        global $mySoldTable;
        $screen = get_current_screen();
        $option = 'per_page';
        $args = array(
               'label' => __('All Sales', ILIST_ID_LANGUAGES),
               'default' => 50,
               'option' => 'ilist_sold_per_page'
               );
        add_screen_option( $option, $args );
        $mySoldTable = new ilist_Sold_Table();
    }
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
if (!function_exists("ilist_sold_page_handler")) {
    function ilist_sold_page_handler() {
        global $wpdb, $ilist_framework;
    
        $tableSold = new ilist_Sold_Table();
        //Fetch, prepare, sort, and filter our data...
        if( isset($_POST['s']) ){
            $tableSold->prepare_items($_POST['s']);
        } else {
            $tableSold->prepare_items();
        }
    
        // Initialization
        $message = '';
        
        ?>
    <div class="wrap page_ilist">
    
        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
        <h1>
            <?php
                echo ILIST_NAME . ' :: ';
                _e('All Sales', ILIST_ID_LANGUAGES);
            ?>
        </h1>
        <?php
            // Main menu ILIST Kado
            echo $ilist_framework->nav_tabs();
            echo $message;
        ?>
        <br>
        <form method="post">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
          <?php
            $ilist_search_name_button = __('Search in sales', ILIST_ID_LANGUAGES);
            $tableSold->search_box($ilist_search_name_button, 'ilist-search-id'); ?>
        </form>
    
        <form id="sales-table" method="GET">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
            <?php $tableSold->display() ?>
        </form>
    
    </div>
    <?php
    }
}

?>