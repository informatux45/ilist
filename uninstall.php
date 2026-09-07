<?php
/** if uninstall.php is not called by WordPress, die
============================================= */
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

/** Initialization
============================================= */
global $wpdb;

/** Drop database table
============================================= */
// --- Table LIST
$wpdb->query("DROP TABLE IF EXISTS " . ILIST_TBL_MAIN);
// ---- Table DATA
$wpdb->query("DROP TABLE IF EXISTS " . ILIST_TBL_PRODUCT);
// --- Table TYPE
$wpdb->query("DROP TABLE IF EXISTS " . ILIST_TBL_TYPE);
// --- Table CAGNOTTE
$wpdb->query("DROP TABLE IF EXISTS " . ILIST_TBL_CAGNOTTE);

/* Include new functions (multisite for ILIST)
=============================================== */
$file = 'multisite.php';
if (file_exists($file)) require_once($file);

/** Delete Options
============================================= */
$options = [
    // --- OPTIONS
	 'ilist_widget_dashboard_summary'
    ,'ilist_url_complete'
    ,'ilist_search_url_complete'
    ,'ilist_stock_management'
    ,'ilist_add_to_my_list_button_class'
    ,'ilist_stock_backorder'
    ,'ilist_ttc_product_in_list'
    ,'ilist_product_add_method'
    ,'ilist_rewrite_url'
    ,'ilist_shorcode_bytpl'
    ,'ilist_debug_log'
    // --- LIST
    ,'ilist_display_user_name'
    ,'ilist_ajax_cart_button_remove_after_click'
    ,'ilist_image_max_size'
	,'ilist_image_height'
    ,'ilist_format_image'
    ,'ilist_format_display_products'
    ,'ilist_hide_add_product'
    ,'ilist_disable_list_creation_visitors'
    ,'ilist_disable_list_creation_registered_users'
    ,'ilist_hide_add_to_my_list_xxx'    
    ,'ilist_sorting_products'
    ,'ilist_link_list_behavior'
    ,'ilist_link_list_href_blank'
    ,'ilist_front_color_lightbox'
    ,'ilist_quickview_title'
    ,'ilist_quickview_price'
    ,'ilist_quickview_description_short'
    ,'ilist_quickview_description_long'
    ,'ilist_quickview_description_truncate'
    ,'ilist_notify_admins'
    // --- WOOCOMMERCE
    ,'ilist_wc_display_table_list_column'
    ,'ilist_wc_display_detail_order'
    ,'ilist_hide_comment'
    ,'ilist_woocommerce_cancelled_action'
    ,'ilist_order_statuses_send_notification_default'
    ,'ilist_order_statuses_cancelled_action_default'
    // --- POT
    ,'ilist_pot_is_active'
    ,'ilist_id_participation_product'
    ,'ilist_pot_min_price_product'
    ,'ilist_pot_range_price'
    ,'ilist_pot_user_choice'
    // --- SHARE
    ,'ilist_share_list'
    ,'ilist_share_icons'
    ,'ilist_share_icons_method'
    ,'ilist_print_logo'
    ,'ilist_print_logo_height'
    ,'ilist_print_thumbnail_height'
    ,'ilist_print_open'
    ,'ilist_print_head_text'
    ,'ilist_print_footer_text'
    // --- SEARCH
    ,'ilist_email_search'
    ,'ilist_name_search'
    ,'ilist_firstname_search'
    ,'ilist_lastname_search'
    ,'ilist_selectize_limit'
    ,'ilist_selectize_truncate'
    // --- AUTOMATIC EMAILS
    ,'ilist_email_admin_response'
    ,'ilist_email_admin_name'
    ,'ilist_email_admin'
    ,'ilist_email_subject_wc_new_order'
    ,'ilist_email_created_list_is_active'
    ,'ilist_email_subject_alert_created_list'
    ,'ilist_email_body_alert_created_list'
    ,'ilist_email_subject_alert_one'
    ,'ilist_email_body_alert_one'
    ,'ilist_email_subject_alert_one_admin'
    ,'ilist_email_body_alert_one_admin'
    // OBSOLETE
    ,'ilist_hide_add_to_my_list'
    ,'ilist_add_products_to_lists'
    ,'ilist_license_key' // Système de licence retiré (plugin public)
    ,'ilist_rewrite_rules_flushed' // Marqueur de flush des règles de réécriture
];
foreach ( $options as $option ) {
	// Test sur !== false : une bonne partie des options valent '0' ou '' - donc
	// falsy - et n'étaient jamais supprimées, la désinstallation laissant
	// l'essentiel des réglages en base.
	if ( ilist_get_option( $option ) !== false ) {
		ilist_delete_option( $option );
	}
}

/** Remove CRON
============================================= */
remove_action( 'ilist_ilist_changes', 'ilist_do_cron' );
// clean the scheduler
function ilist_cron_job_delete() {
	wp_clear_scheduled_hook('ilist_ilist_changes');
}

?>