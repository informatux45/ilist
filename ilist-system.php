<?php
/** Blocking direct access to plugin
============================================= */
defined('ABSPATH') or die('Are you crazy!');

/** Plugin DB Version / Table name
============================================= */
global $ilist_db_version;
$ilist_db_version = ilist_get_version(); // Change version of the plugin to update DB

/**
* Install the plugin DB
* @return DB Insert/Upgrade
*/
// register_*_hook() attend le fichier PRINCIPAL du plugin : WordPress
// déclenche 'activate_ilist/ilist.php'. Passer __FILE__ depuis ce fichier
// inclus donnait 'activate_ilist/ilist-system.php' - un hook qui n'était
// jamais joué. L'installation ne devait son fonctionnement qu'au filet de
// sécurité ilist_update_db_check(), plus bas, sur plugins_loaded.
register_activation_hook( ILIST_FILE, 'ilist_install' );
if ( !function_exists('ilist_install') ) {
	function ilist_install() {
		global $wpdb, $ilist_db_version, $Ilist;
		
		$charset_collate = $wpdb->get_charset_collate();
	
		$sql = "CREATE TABLE " . ILIST_TBL_MAIN . " (
				id int(11) NOT NULL AUTO_INCREMENT,
				user_id int(11) NOT NULL COMMENT 'ID User WP',
				date datetime NOT NULL,
				event_date datetime DEFAULT NULL,
				name varchar(255) NOT NULL,
				description text,
				image text,
				firstname varchar(50) NOT NULL,
				lastname varchar(50) NOT NULL,
				email varchar(255) NOT NULL,
				keyaccess varchar(100) NOT NULL,
				password varchar(255) DEFAULT NULL,
				active tinyint(4) NOT NULL DEFAULT '1' COMMENT '0: inactive, 1: active',
				PRIMARY KEY (id)
			   ) $charset_collate;
			    CREATE TABLE " . ILIST_TBL_PRODUCT . " (
				id int(11) NOT NULL AUTO_INCREMENT,
				list_id int(11) NOT NULL COMMENT 'ID Ilist',
				order_id int(11) DEFAULT NULL COMMENT 'ID Commande',
				date datetime DEFAULT NULL COMMENT 'Date de commande',
				product_id int(11) NOT NULL COMMENT 'ID Produit',
				variation_id int(11) DEFAULT NULL COMMENT 'ID Variation',
				product_name varchar(255) NOT NULL,
				is_pot tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: no pot, 1: pot activated',
				customer varchar(255) DEFAULT NULL COMMENT 'Nom acheteur',
				admin_note text DEFAULT NULL COMMENT 'Admin Note',
				status tinyint(4) NOT NULL DEFAULT '1' COMMENT '1: A vendre, 2: Internet',
				favorite tinyint(4) NOT NULL DEFAULT 0 COMMENT '0: desactivated, 1: activated',
				PRIMARY KEY (id)
			   ) $charset_collate;
				CREATE TABLE " . ILIST_TBL_TYPE . " (
				id int(11) NOT NULL AUTO_INCREMENT,
				title varchar(200) NOT NULL,
				description text,
				color varchar(7),
				PRIMARY KEY (id)
				) $charset_collate;
				CREATE TABLE " . ILIST_TBL_CAGNOTTE . " (
				id int(11) NOT NULL AUTO_INCREMENT,
				date datetime NOT NULL COMMENT 'Date Achat',
				order_id int(11) NOT NULL COMMENT 'Order ID',
				list_id int(11) NOT NULL COMMENT 'List ID',
				list_product_id int(11) NOT NULL COMMENT 'List product ID',
				product_id int(11) NOT NULL COMMENT 'Product ID',
				variation_id int(11) DEFAULT NULL COMMENT 'Variation ID',
				participation_id int(11) NOT NULL,
				product_name varchar(255) NOT NULL,
				customer varchar(255) DEFAULT NULL COMMENT 'Nom acheteur',
				participation float(10,2) NOT NULL COMMENT 'Participation',
				PRIMARY KEY (id)				
				) $charset_collate;
		";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	
		ilist_add_option( 'ilist_db_version', $ilist_db_version );
		
		/**
		 * [OPTIONAL] Example of updating to x.x.x version
		 *
		 * If you develop new version of plugin
		 * just increment $ilist_db_version variable
		 * and add following block of code
		 */
		$installed_ver = ilist_get_option('ilist_db_version');
		if ($installed_ver != $ilist_db_version) {
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			// notice that we are updating option, rather than adding it
			ilist_update_option('ilist_db_version', $ilist_db_version);
		}
		
		/**
		 * [OPTIONAL] Check if options exist
		 *
		 * If the plugin options are not created
		 * install/update process create all options
		 */
		$ilist_options = [
			// --- OPTIONS
			 'ilist_widget_dashboard_summary'                 => '1'
			,'ilist_display_user_name'                        => '1'
			,'ilist_share_list'                               => '0'
			,'ilist_stock_management'                         => '0'
			,'ilist_stock_backorder'                          => '0'
			,'ilist_ttc_product_in_list'                      => '0'
			,'ilist_ajax_cart_button_remove_after_click'      => '0'
			,'ilist_add_to_my_list_button_class'              => '0'
			,'ilist_image_max_size'                           => 400
			,'ilist_image_height'                             => 150
			,'ilist_format_image'                             => 'simple'
			,'ilist_format_display_products'                  => 'table'
			,'ilist_hide_add_product'                         => '0'
			,'ilist_hide_add_to_my_list'                      => '0'
			,'ilist_disable_list_creation_registered_users'   => '0'
			,'ilist_disable_list_creation_visitors'           => '0'
			,'ilist_hide_add_to_my_list_xxx'                  => '0'
			,'ilist_product_add_method'                       => 'post'
			,'ilist_sorting_products'                         => 'alpha_asc'
			,'ilist_link_list_behavior'                       => 'lightbox'
			,'ilist_link_list_href_blank'                     => '0'
			,'ilist_add_products_to_lists'                    => '1'
			,'ilist_hide_comment'                             => '1'
			,'ilist_rewrite_url'                              => '0'
			,'ilist_front_color_lightbox'                     => 'white'
			,'ilist_quickview_title'                          => '1'
			,'ilist_quickview_price'                          => '1'
			,'ilist_quickview_description_short'              => '1'
			,'ilist_quickview_description_long'               => '1'
			,'ilist_quickview_description_truncate'           => 250
			,'ilist_debug_log'                                => '0'
			,'ilist_wc_display_table_list_column'             => '1'
			,'ilist_wc_display_detail_order'                  => '1'
			,'ilist_woocommerce_cancelled_action'             => '0'
			,'ilist_order_statuses_send_notification_default' => ''
			,'ilist_order_statuses_cancelled_action_default'  => ''
			,'ilist_notify_admins'                            => '0'
			// --- POT
			,'ilist_pot_is_active'            => '0'
			,'ilist_pot_min_price_product'    => 50
			,'ilist_pot_range_price'          => 50
			,'ilist_pot_user_choice'          => '0'
			,'ilist_id_participation_product' => ''
			// --- SHARE
			,'ilist_share_list'             => '0'
			,'ilist_share_icons'            => ''
			,'ilist_share_icons_method'     => 'rounded'
			,'ilist_print_logo'             => ''
			,'ilist_print_logo_height'      => 80
			,'ilist_print_thumbnail_height' => 150
			,'ilist_print_open'             => '0'
			,'ilist_print_head_text'        => ''
			,'ilist_print_footer_text'      => ''
			// --- SEARCH
			,'ilist_email_search'       => '1'
			,'ilist_name_search'        => '0'
			,'ilist_firstname_search'   => '0'
			,'ilist_lastname_search'    => '0'
			,'ilist_shorcode_bytpl'     => '0'
			,'ilist_selectize_limit'    => 10
			,'ilist_selectize_truncate' => 50
			// --- AUTOMATIC EMAILS
			,'ilist_email_admin_response'         => 'no-reply@' . str_replace("www.", "", $_SERVER['SERVER_NAME'])
			,'ilist_email_admin_name'             => 'Admin ' . $Ilist->get_bloginfo( 'name' )
			,'ilist_email_admin'                  => ilist_get_option( 'admin_email' )
			,'ilist_email_subject_wc_new_order'   => ''
			,'ilist_email_created_list_is_active'     => '1'
			,'ilist_email_subject_alert_created_list' => __('A new list is created ({ILIST_NAME})', ILIST_ID_LANGUAGES )
			,'ilist_email_body_alert_created_list'    => __( 'Hello,<br><br>A new list has just been created on your website:<br><br>List Name: {ILIST_NAME}<br><br>By: {ILIST_CREATOR}<br>Date: {ILIST_DATE}<br><br>{ILIST_WP_SITE_NAME} {ILIST_WP_SITE_DESC}', ILIST_ID_LANGUAGES )
			,'ilist_email_subject_alert_one'      => __('A product purchased on one of your lists ({ILIST_NAME})', ILIST_ID_LANGUAGES )
			,'ilist_email_body_alert_one'         => __( 'Hello,<br><br>A product (or more) has just been purchased on one of your lists:<br><br>List Name: {ILIST_NAME}<br>Product(s) Name: {ILIST_PRODUCT}<br>By: {ILIST_FRIEND}<br>Date: {ILIST_DATE}<br>Purchase Type: {ILIST_TYPE}<br>Comment: {ILIST_COMMENT}<br><br>{ILIST_WP_SITE_NAME} {ILIST_WP_SITE_DESC}<br>See you soon', ILIST_ID_LANGUAGES )
			,'ilist_email_subject_alert_one_admin' => __( 'A product purchased on the list ({ILIST_NAME})', ILIST_ID_LANGUAGES )
			,'ilist_email_body_alert_one_admin'    => __( 'Hello,<br><br>A product (or more) has just been purchased from a list:<br><br>List: {ILIST_NAME}<br>Product(s) Name: {ILIST_PRODUCT}<br>By: {ILIST_FRIEND}<br>Date: {ILIST_DATE}<br>Purchase Type: {ILIST_TYPE}<br>Order number: {ILIST_ORDER_ID}<br>Comment: {ILIST_COMMENT}<br><br>The administrator {ILIST_WP_SITE_NAME} {ILIST_WP_SITE_DESC}', ILIST_ID_LANGUAGES )
		];
		// Add options if not exist
		$deprecated = null;
		$autoload = 'no';
		foreach($ilist_options as $option => $value) {
			if ( !ilist_get_option( $option ) ) {
				ilist_add_option( $option, $value, $deprecated, $autoload );
			}
		}

		/**
		 * Nettoyage des options devenues obsolètes
		 *
		 * Exécuté à chaque changement de version via ilist_update_db_check().
		 */
		$ilist_obsolete_options = [
			'ilist_license_key' // Système de licence retiré (le plugin est public)
		];
		foreach($ilist_obsolete_options as $ilist_obsolete_option) {
			if ( ilist_get_option( $ilist_obsolete_option ) !== false ) {
				ilist_delete_option( $ilist_obsolete_option );
			}
		}

	}
}

/**
* Install the plugin DB datas (populate)
* @return DB Insert/Upgrade DB datas
*/
// Volontairement NON reconnectée à l'activation : cette fonction insère le
// contenu de démonstration sans aucune garde, elle le dupliquerait donc à
// chaque réactivation. Elle reste appelée par ilist_update_db_check(), qui
// ne la joue qu'à la toute première installation (ilist_db_version vide).
if ( ! function_exists('ilist_install_datas') ) {
	function ilist_install_datas() {
		global $wpdb;
		
		// Populate LIST DB	- SQL Request
		$wpdb->insert(ILIST_TBL_MAIN, [
				'user_id'     => get_current_user_id(),
				'date'        => date('Y-m-d h:i:s'),
				'name'        => 'Naissance de Rose', 
				'description' => 'Des cadeaux pour la naissance de ma jolie Rose',
				'firstname'   => 'Rose',
				'lastname'    => 'DUPOND',
				'email'       => 'noreply@domain.com',
				'keyaccess'   => 'thFIEWcie456f0984f457DSQf9ef5dwsZ7',
				'active'      => '1'
			] 
		);

		// Initialize populate TYPE DB
		// 1: A vendre, 2: Internet, 3: Boutique (retrait en boutique), 4: Boutique (emporte par le participant)
		$ilist_types = [ ['id'          => 1
						 ,'title'       => __('For sale', ILIST_ID_LANGUAGES)
						 ,'description' => __('Product available for sale', ILIST_ID_LANGUAGES)
						 ],
						 ['id'          => 2
						 ,'title'       => __('Internet purchase', ILIST_ID_LANGUAGES)
						 ,'description' => __('Purchase on e-commerce online shop', ILIST_ID_LANGUAGES)
						 ],
						 ['id'          => 3
						 ,'title'       => __('Purchase in proshop (withdrawal in prostore)', ILIST_ID_LANGUAGES)
						 ,'description' => __('Purchase in proshop can by retrieved by the owner of the list', ILIST_ID_LANGUAGES)
						 ],
						 ['id'          => 4
						 ,'title'       => __('Purchase in proshop (taken by the buyer)', ILIST_ID_LANGUAGES)
						 ,'description' => __('Purchase in proshop was taken by the buyer', ILIST_ID_LANGUAGES)
						 ],
					   ];
		// We're preparing each DB item on it's own. Makes the code cleaner.
		for ( $i = 0; $i < count($ilist_types); ++$i ) {
			$wpdb->insert(ILIST_TBL_TYPE, [
					'id'          => $ilist_types[$i]['id'],
					'title'       => $ilist_types[$i]['title'], 
					'description' => $ilist_types[$i]['description']
				]
			);
		}		
	}
}

/**
* Insert Post Pages
* @return Insert POST
*/
// Idem : appelée uniquement par ilist_update_db_check(), à la première
// installation.
if ( ! function_exists('ilist_install_post') ) {
	function ilist_install_post() {		
		/**
		 * [POST] Create POST Ilist Main
		 * Create array
		 * Insert POST in DB
		 */
		$ilist_post_main_guid = site_url() . "/mes-listes";
		// Check if page exists to avoid multiple insertions
		if ( !is_page( $ilist_post_main_guid ) ) {			
			$ilist_post_main = array( 'post_title'     => wp_strip_all_tags( __('My lists', ILIST_ID_LANGUAGES) ),
									  'post_type'      => 'page',
									  'post_name'      => 'mes-listes',
									  'post_content'   => '[ilist]',
									  'post_status'    => 'publish',
									  'comment_status' => 'closed',
									  'ping_status'    => 'closed',
									  'post_author'    => get_current_user_id(),
									  'menu_order'     => 0,
									  'guid'           => $ilist_post_main_guid );
			// Get Post ID - FALSE to return 0 instead of wp_error / Insert the post
			$ilist_post_main_ID = wp_insert_post( $ilist_post_main, FALSE );
			// Save Ilist main page url ID
			ilist_add_option( 'ilist_url_complete_id', $ilist_post_main_ID);
		}
		
		/**
		 * [POST] Create POST Ilist Search
		 * Create array
		 * Insert POST in DB
		 */
		$ilist_post_search_guid = site_url() . "/rechercher-une-liste";
		// Check if page exists to avoid multiple insertions
		if ( !is_page( $ilist_post_search_guid ) ) {
			$ilist_post_search = array( 'post_title'     => wp_strip_all_tags( __('Search list', ILIST_ID_LANGUAGES) ),
										'post_type'      => 'page',
										'post_name'      => 'rechercher-une-liste',
										'post_content'   => '[ilist_search]',
										'post_status'    => 'publish',
										'comment_status' => 'closed',
										'ping_status'    => 'closed',
										'post_author'    => get_current_user_id(),
										'menu_order'     => 0,
										'guid'           => $ilist_post_search_guid );
			// Get Post ID - FALSE to return 0 instead of wp_error
			$ilist_post_search_ID = wp_insert_post( $ilist_post_search, FALSE );
			// Save Ilist Search page url ID
			ilist_add_option( 'ilist_search_url_complete_id', $ilist_post_search_ID);
		}
	}
}
	

/**
* Trick to update plugin database
* @return DB Insert/Upgrade DB datas
*/
add_action('plugins_loaded', 'ilist_update_db_check');
if ( ! function_exists('ilist_update_db_check') ) {
	function ilist_update_db_check() {
		global $ilist_db_version, $wpdb;
		$ilist_version = ilist_get_option('ilist_db_version');
		if ($ilist_version != $ilist_db_version) {
			// Check if multisite
			if ( is_multisite() ) {
				$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					ilist_install();
					restore_current_blog();
				}
			} else {
				ilist_install();
			}
		}
		if (!isset($ilist_version) || $ilist_version == '') {
			
			// Check if multisite
			if ( is_multisite() ) {
				$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					ilist_install_datas();
					ilist_install_post();
					restore_current_blog();
				}
			} else {
				ilist_install_datas();
				ilist_install_post();
			}
		}
	}
}

/**
* Desactivation scheduled Jobs
* @return Clear scheduled Hooks
*/
// Même correction que pour l'activation : ce hook n'était jamais joué, la
// tâche planifiée ilist_ilist_changes n'était donc jamais nettoyée à la
// désactivation du plugin.
register_deactivation_hook( ILIST_FILE, 'ilist_deactivation' );
function ilist_deactivation() {
	wp_clear_scheduled_hook('ilist_ilist_changes');
}

/**
 * Error Get Option !?
 * @return string
 */
if (!function_exists("ilist_get_wp_options")) {
	function ilist_get_wp_options() {
		global $wpdb;
		$options = $wpdb->get_var( "SELECT option_value FROM $wpdb->options WHERE option_name='ilist_options'" );
		return $options;
	}
}

/**
 * Migration from Titan Framework
 * @Return the old value of variable
 */
if (!function_exists("ilist_old_value_from_titan")) {
	function ilist_old_value_from_titan($string_to_search) {
		$old_options = ilist_get_wp_options();
		if (!$old_options || !$string_to_search) return;
		// Get result after string to search
		$after_string = strstr($old_options, '"'.$string_to_search.'"');
		// New string without ";
		$new_string = str_replace($string_to_search.'";', "", $after_string);
		// Explode in 3 values
		$s = $lenght = $value = "";
		list($s, $lenght, $value) = explode(":", $new_string, 3);
		// Clean string
		$clean_string = (substr($value, 0, strpos($value, '";s:')) == '') ? substr($value, 0, strpos($value, '";}')) : substr($value, 0, strpos($value, '";s:'));
		// Return good value (old value from Titan Framework)
		return trim($clean_string, '"');
	}
}

/**
 * Admin notice - Migration from Titan Framework
 * @Return string
 */
add_action( 'admin_notices', 'ilist_do_migration_admin_notice' );
if (!function_exists("ilist_do_migration_admin_notice")) {
	function ilist_do_migration_admin_notice() {
		$get_ilist_options = ilist_get_wp_options();
		if (isset($get_ilist_options) && $get_ilist_options != '') {
			?>
			<div id="ilist-migration" class="notice notice-info">
				<p><?php _e( "You have installed the new version of ILIST which no longer uses the TITAN Framework. To migrate your old data, simply click on the Migration button.", ILIST_ID_LANGUAGES ); ?></p>
				<p><a href="<?php echo admin_url('admin.php?page=ilist-general&m=titan'); ?>" class="button button-primary">Migration</a></p>
			</div>
			<?php
		}
	}
}

/**
 * Admin notice - Migration from Titan Framework
 * @Return string
 */
if (!function_exists("ilist_do_migration")) {
	function ilist_do_migration() {
		$debug = false;
		$options_options = [
			// Options
			 'ilist_url_complete'
			,'ilist_search_url_complete'
			,'ilist_widget_dashboard_summary'
			,'ilist_display_user_name'
			,'ilist_stock_management'
			,'ilist_stock_backorder'
			,'ilist_ttc_product_in_list'
			,'ilist_add_to_my_list_button_class'
			,'ilist_ajax_cart_button_remove_after_click'
			,'ilist_image_max_size'
			,'ilist_image_height'
			,'ilist_format_image'
			,'ilist_format_display_products'
			,'ilist_hide_add_product'
			,'ilist_hide_add_to_my_list'
			,'ilist_disable_list_creation_registered_users'
			,'ilist_disable_list_creation_visitors'
			,'ilist_hide_add_to_my_list_xxx'
			,'ilist_product_add_method'
			,'ilist_sorting_products'
			,'ilist_link_list_behavior'
			,'ilist_link_list_href_blank'
			,'ilist_add_products_to_lists'
			,'ilist_hide_comment'
			,'ilist_rewrite_url'
			,'ilist_front_color_lightbox'
			,'ilist_quickview_title'
			,'ilist_quickview_price'
			,'ilist_quickview_description_short'
			,'ilist_quickview_description_long'
			,'ilist_quickview_description_truncate'
			,'ilist_debug_log'
			,'ilist_wc_display_table_list_column'
			,'ilist_wc_display_detail_order'
			,'ilist_notify_admins'
		];
		$options_pot = [
			// Pot
			 'ilist_pot_is_active'
			,'ilist_pot_min_price_product'
			,'ilist_pot_range_price'
			,'ilist_pot_user_choice'
			,'ilist_id_participation_product'
		];
		$options_share = [
			// Share
			 //'ilist_share_icons'
			 'ilist_share_list'
			,'ilist_share_icons_method'
			,'ilist_print_logo'
			,'ilist_print_logo_height'
			,'ilist_print_thumbnail_height'
			,'ilist_print_open'
			,'ilist_print_head_text'
			,'ilist_print_footer_text'
		];
		$options_search = [
			// Search
			 'ilist_email_search'
			,'ilist_name_search'
			,'ilist_firstname_search'
			,'ilist_lastname_search'
			,'ilist_shorcode_bytpl'
			,'ilist_selectize_limit'
			,'ilist_selectize_truncate'
		];
		$options_emails = [
			// Automatic emails
			 'ilist_email_admin_response'
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
		];
		$error = false;
		// Migration Options
		$cpt = 1;
		foreach ( $options_options as $option ) {
			// Get old value from option
			$old_var = ilist_old_value_from_titan( $option );
			// Insert old value in new option
			switch($option) {
				case 'ilist_widget_dashboard_summary':
				case 'ilist_display_user_name':
				case 'ilist_add_products_to_lists':
				case 'ilist_hide_comment':
				case 'ilist_quickview_title':
				case 'ilist_quickview_price':
				case 'ilist_quickview_description_short':
				case 'ilist_quickview_description_long':
				case 'ilist_wc_display_table_list_column':
				case 'ilist_wc_display_detail_order':
					if ($old_var == '') $old_var = '1';
				break;
				case 'ilist_share_list':
				case 'ilist_notify_admins':
				case 'ilist_stock_management':
				case 'ilist_stock_backorder':
				case 'ilist_ttc_product_in_list':
				case 'ilist_ajax_cart_button_remove_after_click':
				case 'ilist_hide_add_product':
				case 'ilist_hide_add_to_my_list':
				case 'ilist_disable_list_creation_registered_users':
				case 'ilist_disable_list_creation_visitors':
				case 'ilist_hide_add_to_my_list_xxx':
				case 'ilist_link_list_href_blank':
				case 'ilist_rewrite_url':
				case 'ilist_debug_log':
					if ($old_var == '') $old_var = '0';
				break;
				case 'ilist_sorting_products':
					if ($old_var == '') $old_var = 'alpha_asc';
				break;
				case 'ilist_product_add_method':
					if ($old_var == '') $old_var = 'post';
				break;
				case 'ilist_format_image':
					if ($old_var == '') $old_var = 'simple';
				break;
				case 'ilist_format_display_products':
					if ($old_var == '') $old_var = 'table';
				break;
				case 'ilist_link_list_behavior':
					if ($old_var == '') $old_var = 'lightbox';
				break;
				case 'ilist_front_color_lightbox':
					if ($old_var == '') $old_var = 'white';
				break;
			}
			if ($debug) echo $cpt . ' - ' . $option . ': ' . $old_var . '<br>';
			ilist_update_option( $option, stripslashes($old_var) );
			$cpt++;
		}
		// Migration Pot Options
		foreach ( $options_pot as $option ) {
			// Get old value from option
			$old_var = ilist_old_value_from_titan( $option );
			// Insert old value in new option
			switch($option) {
				case 'ilist_pot_is_active':
				case 'ilist_pot_user_choice':
					if ($old_var == '') $old_var = '0';
				break;
			}
			if ($debug) echo $cpt . ' - ' . $option . ': ' . $old_var . '<br>';
			ilist_update_option( $option, stripslashes($old_var) );
			$cpt++;
		}
		// Migration Share Options
		foreach ( $options_share as $option ) {
			// Get old value from option
			$old_var = ilist_old_value_from_titan( $option );
			switch($option) {
				case "ilist_print_logo":
					$old_var = wp_get_attachment_url( $old_var );
				break;
				case 'ilist_share_icons':
					// Do not change anything
					continue 2;
				break;
				case 'ilist_share_icons_method';
					if ($old_var == '') $old_var = 'rounded';
				break;
				case 'ilist_share_list':
				case 'ilist_print_open':
					if ($old_var == '') $old_var = '0';
				break;
			}
			// Insert old value in new option
			if ($debug) echo $cpt . ' - ' . $option . ': ' .stripslashes($old_var) . '<br>';
			ilist_update_option( $option, stripslashes($old_var) );
			$cpt++;
		}
		// Migration Search Options
		foreach ( $options_search as $option ) {
			// Get old value from option
			$old_var = ilist_old_value_from_titan( $option );
			// Insert old value in new option
			switch($option) {
				case 'ilist_email_search':
					if ($old_var == '') $old_var = '1';
				break;
				case 'ilist_name_search':
				case 'ilist_firstname_search':
				case 'ilist_lastname_search':
				case 'ilist_shorcode_bytpl':					
					if ($old_var == '') $old_var = '0';
				break;
			}
			if ($debug) echo $cpt . ' - ' . $option . ': ' . stripslashes($old_var) . '<br>';
			ilist_update_option( $option, $old_var );
			$cpt++;
		}
		// Migration Automatic Emails Options
		foreach ( $options_emails as $option ) {
			// Get old value from option
			$old_var = ilist_old_value_from_titan( $option );
			// Insert old value in new option
			switch($option) {
				case 'ilist_email_created_list_is_active':
					if ($old_var == '') $old_var = '1';
				break;
				case 'ilist_remove_visual_editor':
				case 'ilist_behavior_tag_p_editor':
				case 'ilist_shortcode_in_excerpt':
					if ($old_var == '') $old_var = '0';
				break;
				case 'ilist_email_body_alert_created_list':
					if ($old_var == '') $old_var = __( 'Hello,<br><br>A new list has just been created on your website:<br><br>List Name: {ILIST_NAME}<br><br>By: {ILIST_CREATOR}<br>Date: {ILIST_DATE}<br><br>{ILIST_WP_SITE_NAME} {ILIST_WP_SITE_DESC}', ILIST_ID_LANGUAGES );
				break;
				case 'ilist_email_body_alert_one':
					if ($old_var == '') $old_var = __( 'Hello,<br><br>A product (or more) has just been purchased on one of your lists:<br><br>List Name: {ILIST_NAME}<br>Product(s) Name: {ILIST_PRODUCT}<br>By: {ILIST_FRIEND}<br>Date: {ILIST_DATE}<br>Purchase Type: {ILIST_TYPE}<br>Comment: {ILIST_COMMENT}<br><br>{ILIST_WP_SITE_NAME} {ILIST_WP_SITE_DESC}<br>See you soon', ILIST_ID_LANGUAGES );
				break;
				case 'ilist_email_body_alert_one_admin':
					if ($old_var == '') $old_var = __( 'Hello,<br><br>A product (or more) has just been purchased from a list:<br><br>List: {ILIST_NAME}<br>Product(s) Name: {ILIST_PRODUCT}<br>By: {ILIST_FRIEND}<br>Date: {ILIST_DATE}<br>Purchase Type: {ILIST_TYPE}<br>Order number: {ILIST_ORDER_ID}<br>Comment: {ILIST_COMMENT}<br><br>The administrator {ILIST_WP_SITE_NAME} {ILIST_WP_SITE_DESC}', ILIST_ID_LANGUAGES );
				break;
			}
			if ($debug) echo $cpt . ' - ' . $option . ': ' . $old_var . '<br>';
			ilist_update_option( $option, stripslashes($old_var) );
			$cpt++;
		}
		if ($error) {
			// Don't remove options
			return false;
		} else {
			// Remove old option
			if (!$debug) ilist_delete_option( 'ilist_options' );
			return true;
		}
		
	}
}

?>