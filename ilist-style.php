<?php
/** Blocking direct access to plugin
============================================= */
defined('ABSPATH') or die('Are you crazy!');

/** Add CSS stylesheets / JS scripts to front
============================================= */
add_action( 'wp_enqueue_scripts', 'ilist_styles' );
if (!function_exists("ilist_styles")) {
  function ilist_styles() {
    wp_register_style( 'ilist-style', ILIST_URL . 'css/ilist-style.css', false, ilist_get_version( 'Version' ) );
    wp_enqueue_style( 'ilist-style' );
    $ilist_format_display_products = ilist_get_option('ilist_format_display_products');
    if (isset($ilist_format_display_products) && $ilist_format_display_products == 'grid' && !is_account_page()) {
      wp_register_style( 'ilist-style-grid', ILIST_URL . 'css/ilist-style-grid.css', false, ilist_get_version( 'Version' ) );
      wp_enqueue_style( 'ilist-style-grid' );
    }
    wp_register_style( 'ilist-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', false, '4.7.0' );
    wp_enqueue_style( 'ilist-awesome' );
  }
}
// ----------------------------------------
add_action( 'wp_enqueue_scripts', 'ilist_script_method' );
if (!function_exists("ilist_script_method")) {
  function ilist_script_method() {
    wp_enqueue_script( 'ilist-script', ILIST_URL . 'js/ilist-script.js', array( 'jquery' ), ilist_get_version( 'Version' ) );
  }
}
// ----------------------------------------
add_action( 'wp_enqueue_scripts', 'ilist_modal_method' );
if (!function_exists("ilist_modal_method")) {
  function ilist_modal_method() {
    wp_enqueue_script( 'ilist-modal', ILIST_URL . 'js/ilist.plainmodal.min.js', array( 'jquery' ), ilist_get_version( 'Version' ) );
  }
}
// ----------------------------------------
add_action('wp_enqueue_scripts', 'ilist_add_live_search');
if (!function_exists("ilist_add_live_search")) {
  function ilist_add_live_search() {
    wp_enqueue_script( 'ilist-live-search', ILIST_URL . 'js/ilist-live-search.js', array('jquery'), ilist_get_version( 'Version' ), true );
    // pass Ajax Url to script.js
    // Methode conseillee pour Wordpress 5.7: wp_add_inline_script()
    wp_localize_script('script', 'ajaxurl', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );  
  }
}
// ----------------------------------------
add_action('wp_head', 'ilist_hook_ajaxurl');
if (!function_exists("ilist_hook_ajaxurl")) {
  function ilist_hook_ajaxurl() {
     echo '<script type="text/javascript">
             var ajaxurl = "' . admin_url('admin-ajax.php') . '";
           </script>';
  }
}

/** Add QTY add to cart (AJAX)
============================================= */
add_action( 'wp_enqueue_scripts', 'ilist_qty_addtocart' );
if (!function_exists("ilist_qty_addtocart")) {
  function ilist_qty_addtocart() {
    wp_enqueue_script( 'ilist-qty-addtocart', ILIST_URL . 'js/ilist-qty-addtocart.js', array( 'jquery' ), ilist_get_version( 'Version' ), true );
  }
}

/** Add CSS stylesheets / JS Scripts to admin
============================================= */
add_action( 'admin_enqueue_scripts', 'ilist_admin_styles' );
if (!function_exists("ilist_admin_styles")) {
  function ilist_admin_styles() {
    wp_register_style( 'ilist-admin', ILIST_URL . 'css/ilist-style-admin.css', false, ilist_get_version( 'Version' ) );
    wp_enqueue_style( 'ilist-admin' );

  }
}
// ----------------------------------------
add_action( 'admin_enqueue_scripts', 'ilist_admin_framework_styles' );
if (!function_exists("ilist_admin_framework_styles")) {
  function ilist_admin_framework_styles() {
    wp_register_style( 'ilist-framework-admin', FRAMEWORK_ILIST_URL . 'assets/css/framework.css', false, ilist_get_version( 'Version' ) );
    wp_enqueue_style( 'ilist-framework-admin' );
  }
}
// ----------------------------------------
add_action( 'admin_enqueue_scripts', 'ilist_script_admin_method' );
if (!function_exists("ilist_script_admin_method")) {
  function ilist_script_admin_method() {
    // Only use SELECT2 on specific pages
    $currentScreen = get_current_screen();
    if ($currentScreen->id == 'ilist-kado_page_ilist_products') {
      wp_enqueue_script( 'ilist-script-admin', ILIST_URL . 'js/ilist-script-admin.js', array( 'jquery' ), ilist_get_version( 'Version' ) );
    }
    // AJAX Add product ADMIN
    wp_register_script( 'ilist_add_product', ILIST_URL . 'js/ilist-add-product-admin.js', array('jquery') );
    wp_localize_script( 'ilist_add_product', 'ilist_add_product_params', array(
        'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
        'select_text' => __('Select a product', ILIST_ID_LANGUAGES)
      ) );
    wp_enqueue_script( 'ilist_add_product' );
  }
}

/** AJAX
=================================================== */
add_action( 'wp_print_scripts', 'ilist_ajax_scripts' );
if (!function_exists("ilist_ajax_scripts")) {
  function ilist_ajax_scripts() {
    if ( is_admin() ) {
      // load our jquery file that sends the $.post request (ADMIN)
      wp_enqueue_script( "ilist-admin-ajax-script", ILIST_URL . 'js/admin-ajax.js', array( 'jquery' ) );
      // --- Initialize infos to pass
      // make the ajaxurl var available to the above script
      $ilist_admin_options_to_ajax = [
        'ajaxurl'  => admin_url( 'admin-ajax.php'),  // ilist_admin_ajax.ajaxurl
        'adminurl' => get_admin_url(),               // ilist_admin_ajax.adminurl
      ];
      wp_localize_script( 'ilist-admin-ajax-script', 'ilist_admin_ajax', $ilist_admin_options_to_ajax );
    } else {
      // load our jquery file that sends the $.post request (ADMIN)
      wp_enqueue_script( "ilist-ajax-script", ILIST_URL . 'js/ilist-ajax.js', array( 'jquery' ) );
      // --- Initialize infos to pass
      // make the ajaxurl var available to the above script
      $ilist_options_to_ajax = [
        'ajaxurl'  => admin_url( 'admin-ajax.php'),  // ilist_ajax_script.ajaxurl
      ];
      wp_localize_script( 'ilist-ajax-script', 'ilist_ajax_script', $ilist_options_to_ajax );
    }
  }
}

?>