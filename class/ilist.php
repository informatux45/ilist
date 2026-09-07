<?php
/** blocking direct access to plugin
============================================= */
defined( 'ABSPATH' ) or die( 'Are you crazy!' );

if ( !class_exists("Ilist") ) {

	class Ilist {	

		public static $instance;

		// PASSWORD_DEFAULT
		// PASSWORD_BCRYPT
		// PASSWORD_ARGON2I (=> PHP 7.2.0)
		// PASSWORD_ARGON2ID ( => PHP 7.3.0)
		public $algo_password = PASSWORD_BCRYPT;

		// URLs (bannière / logos), affectées dans le constructeur.
		// Déclarées explicitement : depuis PHP 8.2 la création d'une propriété
		// non déclarée émet une dépréciation, et deviendra une erreur en PHP 9.
		public $ilist_url;
		public $ilist_url_support;
		public $ilist_changelog;
		public $ilist_url_banner;
		public $ilist_url_logo_60;
		public $ilist_url_logo;
		public $ilist_url_logo_wt;

		/**
		 * Constructor
		 */
		public function __construct() {
			self::$instance = $this;
			// ILIST Url (Banner / Logos)
			$this->ilist_url         = "https://dev.informatux.com/";
			$this->ilist_url_support = $this->ilist_url . "support/";
			$this->ilist_changelog   = $this->ilist_url . "dev/updates/changelogs/" . ILIST_ID . "/changelog.html";
			$this->ilist_url_banner  = $this->ilist_url . "upload/dev/ilist-banner.jpg";
			$this->ilist_url_logo_60 = $this->ilist_url . "upload/dev/ilist-logo-violet-60.png";
			$this->ilist_url_logo    = $this->ilist_url . "upload/dev/ilist-logo-violet.png";
			$this->ilist_url_logo_wt = $this->ilist_url . "upload/dev/ilist-logo-violet-wt.png";
			// WC order message
			add_action( 'add_meta_boxes', array( $this, 'order_message' ) );
			// Send email notification
			add_action("woocommerce_order_status_changed", array( $this, 'order_changed_send_notification' ) );
			// Admin notices
			add_action( 'admin_notices', array( $this, 'email_auto_check' ) );
			// WooCommerce (Orders / Detail)
			// --- Écran des commandes historique (stockage wp_posts)
			add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_wc_order_column' ), 20 );
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'add_wc_order_column_content' ) );
			add_filter( "manage_edit-shop_order_sortable_columns", array( $this, 'add_wc_order_column_sortable' ) );
			// --- Écran des commandes HPOS (tables wc_orders) : autres hooks,
			//     et la commande est passée en 2e argument de la colonne.
			add_filter( 'woocommerce_shop_order_list_table_columns', array( $this, 'add_wc_order_column' ), 20 );
			add_action( 'woocommerce_shop_order_list_table_custom_column', array( $this, 'add_wc_order_column_content' ), 10, 2 );
			// --- Fiche commande (commun aux deux modes)
			add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'add_wc_order_meta_infos' ) );
			//add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'add_wc_order_meta_billing' ) );
			// WooCommerce new order email subject
			add_filter( 'woocommerce_email_subject_new_order', array( $this, 'customizing_new_order_subject' ), 10, 2 );
			// Set custom product price when adding to cart
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'cart_items_prices' ), 10, 1 );
			// Change name if participation
			add_filter( "woocommerce_cart_item_name", array( $this, "cart_product_title" ), 20, 3 );
			// Check if Crowdfunding (Email order)
			//add_action( 'woocommerce_email_before_order_table', array($this, 'add_order_instruction_email'), 10, 4 ); // Before table
			add_action( 'woocommerce_order_item_meta_start', array($this, 'order_item_information_email'), 10, 4 );   // After item name in Table
			
			/** CUSTOM Filters
			============================================= */
			add_filter( 'ilist_pot_no_min', array( $this, 'filter_pot_value_no_min'), 10 ); // Value if pot no min price
			add_filter( 'ilist_pot_display_participation', array( $this, 'filter_pot_display_participation'), 10, 2 ); // Format display participation
			add_filter( 'ilist_pot_product_cart_title', array( $this, 'filter_pot_product_cart_title_text' ), 10 );  // Return text after participation product
			add_filter( 'ilist_pot_separator_text', array( $this, 'filter_pot_separator_participation' ), 10 );  // Return participation separator text
			add_filter( 'ilist_protected_password_list_text', array( $this, 'filter_protected_password_list' ), 10 ); // Return protected password list text
			add_filter( 'ilist_pot_not_activated', array( $this, 'filter_pot_not_activated'), 10 ); // Value if pot no min price
			/** CUSTOM Actions
			============================================= */
			add_action( 'ilist_before_login', array( $this, 'add_content_before_login' ), 10, 6 );
			add_action( 'ilist_login', array( $this, 'add_content_login' ), 10, 6 );
			add_action( 'ilist_after_login', array( $this, 'add_content_after_login' ), 10, 6 );
			add_action( 'ilist_admin_list_products_after', array( $this, 'admin_list_products_after' ), 10, 1 );
		}
	
		/**
		 * Start Instance
		 */
		public static function get() {
			if (self::$instance === null) {
				self::$instance = new self();
			}
			return self::$instance;
		}
		
		/**
		 * Common function - Convert date
		 *
		 * @return string
		 */
		public function convert_date($date, $format = 'ISO') {
			// Cette méthode dupliquait à l'identique ilistConvertDate()
			// (ilist-functions.php), strftime() dépréciée comprise. Une seule
			// implémentation désormais, celle de ilist-functions.php.
			return ilistConvertDate( $date, $format );
		}
		
		/**
		 * Wordpress function - Convert date
		 *
		 * @param 	$date 		Date ISO (Ex: 2021-08-06)
		 * @param 	$format 	false | withtime
		 *
		 * @return string
		 */
		public function convert_wp_date($date, $format = false) {
			switch(strtoupper($format)) {
				default:
					return date(ilist_get_option('date_format'), strtotime($date));
				break;
				case "withtime":
					return date(ilist_get_option('date_format') . ' ' . ilist_get_option('time_format'), strtotime($date));
				break;
			}
		}
		
		/**
		 * Wordpress blog detail (get_blog_detail OR get_bloginfo)
		 * If Multisite is installed
		 *
		 * @return string
		 */
		public function get_bloginfo($option) {
			if ( ILIST_NETWORK_ACTIVATED == true ) {
				$blog_details = get_blog_details( get_current_blog_id() );
				return $blog_details->{$option};
			} else {
				return get_bloginfo( $option );
			}
		}

		/**
		 * Send notification to list owner after payment
		 * WC order status changed
		 *
		 * @return void
		 */
		public function order_changed_send_notification($order_id, $checkout=null) {
			global $informatux, $woocommerce, $wpdb;
			// -----------------------------------------------------
			// Get all products where is order_id and status is equal 1
			// -----------------------------------------------------
			$ilist_is_cagnotte = false;
			$ilist_cagnotte = $wpdb->get_results( "SELECT * FROM " . ILIST_TBL_CAGNOTTE . " WHERE order_id = '$order_id'" );
			if ($ilist_cagnotte) {
				$ilist_all_products = $ilist_cagnotte;
				$ilist_is_cagnotte  = true;
			} else {
				//$ilist_all_products = $wpdb->get_results( "SELECT order_id FROM " . ILIST_TBL_PRODUCT . " WHERE status = '1' AND order_id > 0 GROUP BY order_id ORDER BY order_id" );
				$ilist_all_products = $wpdb->get_results( "SELECT * FROM " . ILIST_TBL_PRODUCT . " WHERE status = '1' AND order_id = '$order_id'" );
			}
			
			// Option log (if activated)
			if (ilist_get_option('ilist_debug_log')) {
				ilist_log_start();
				ilist_log("Cron start");
				if ($ilist_cagnotte)
					ilist_log("SQL1 : "."SELECT * FROM " . ILIST_TBL_CAGNOTTE . " WHERE order_id = '$order_id'");
				else 
					ilist_log("SQL2 : "."SELECT order_id FROM " . ILIST_TBL_PRODUCT . " WHERE status = '1' AND order_id > 0 GROUP BY order_id ORDER BY order_id");
				ilist_log($ilist_all_products);
			}
		   
			// wc_get_order() plutôt que new WC_Order() : renvoie false sur un
			// id inexistant au lieu de lever une exception, et respecte le
			// mode de stockage actif (HPOS ou wp_posts).
			$order = wc_get_order( $order_id );
			if ( !$order ) return;
			// Option log (if activated)
			if (ilist_get_option('ilist_debug_log')) ilist_log("Order status (oid=$order_id) => ".$order->get_status());
			// -----------------------------------------------------
			$send_notification = false;
			$ilist_order_statuses_send_notification_default = ilist_get_option('ilist_order_statuses_send_notification_default');
			if ( is_array($ilist_order_statuses_send_notification_default) && array_keys( $ilist_order_statuses_send_notification_default, true ) ) {
				if (ilist_get_option('ilist_debug_log')) {
					ilist_log("Send notification (1): ".$order->get_status());
					ilist_log($ilist_order_statuses_send_notification_default);
				}
				if (in_array($order->get_status(), $ilist_order_statuses_send_notification_default)) $send_notification = true;
			} elseif ($order->get_status() === 'completed') {
				$send_notification = true;
				if (ilist_get_option('ilist_debug_log')) {
					ilist_log("Send notification (2): ".$order->get_status());
				}
			}
			// -----------------------------------------------------
			$cancelled_order = false;
			$ilist_order_statuses_cancelled_action_default = ilist_get_option('ilist_order_statuses_cancelled_action_default');
			if ( is_array($ilist_order_statuses_cancelled_action_default) && array_keys( $ilist_order_statuses_cancelled_action_default, true ) ) {
				if (ilist_get_option('ilist_debug_log')) {
					ilist_log("Cancelled order (1): ".$order->get_status());
					ilist_log($ilist_order_statuses_cancelled_action_default);
				}
				if (in_array($order->get_status(), $ilist_order_statuses_cancelled_action_default)) $cancelled_order = true;
			} elseif ($order->get_status() === 'cancelled' || $order->get_status() === 'failed') {
				$cancelled_order = true;
				if (ilist_get_option('ilist_debug_log')) {
					ilist_log("Cancelled order (2): cancelled / failed");
				}
			}
			// -----------------------------------------------------
			// Check if order status is CANCELLED / FAILED / CUSTOM
			// -----------------------------------------------------
			if ($cancelled_order === true) {
				// Check if option woocommerce cancelled is activated
				if (ilist_get_option('ilist_woocommerce_cancelled_action')) {
					// Option log (if activated)
					if (ilist_get_option('ilist_debug_log')) ilist_log("The status of the order $order_id has changed to canceled mode");
					// -----------------------------------------------------
					// Check if the products sold in this order belong to a list
					// -----------------------------------------------------
					$are_products = $wpdb->get_results( "SELECT id, order_id, list_id, product_name FROM " . ILIST_TBL_PRODUCT . " WHERE order_id = '$order_id'" );
					if ($are_products) {
						foreach ( $are_products as $is_product ) {
							// Change status of the product in list
							$wpdb->update(
								 ILIST_TBL_PRODUCT
								,array( 'order_id' => NULL, 'date' => NULL, 'customer' => NULL, 'status' => 1 )
								,array( 'id' => $is_product->id )
							);
							// Option log (if activated)
							if (ilist_get_option('ilist_debug_log')) {
								ilist_log("Product ".strtoupper($is_product->product_name)." relisted on list ".strtoupper( ilist_get_list_infos_by_id($is_product->list_id, 'name') ));
							}
						}
					}
					// Delete entries in SQL Cagnotte Table if exist
					$wpdb->delete( ILIST_TBL_CAGNOTTE, array( 'order_id' => $order_id ) );
					// Delete message ILIST if exists
					ilist_delete_order_meta( $order_id, 'order-meta-ilist' );
				}
			}
			
			// -----------------------------------------------------
			// Check if order status is COMPLETED / CUSTOM
			// -----------------------------------------------------
			if ( $send_notification === true ) {
				// if ($order->get_status() === 'completed' || $order->get_status() === 'on-hold' || $order->get_status() === 'processing' )
				if (ilist_get_option('ilist_debug_log')) ilist_log("Order processing (".$order->get_status().")");
				// -----------------------------------------------------
				// Check if results product pending
				// -----------------------------------------------------
				if (array_keys( $ilist_all_products, true )) {
					// -----------------------------------------------------
					// Update DB Products list if order status changed
					// -----------------------------------------------------
					foreach($ilist_all_products as $ilist_product) {
						$product_order_id = $ilist_product->order_id;
						// -----------------------------------------------------
						// Get order + status
						// Passe par le CRUD WooCommerce : en HPOS les commandes
						// ne sont plus dans wp_posts, la requête directe sur
						// post_status ne renvoyait plus rien.
						// -----------------------------------------------------
						$order = wc_get_order( $product_order_id );
						if ( !$order ) continue;
						$order_status = $order->get_status();
						// -----------------------------------------------------
						// Option log (if activated)
						// -----------------------------------------------------
						if (ilist_get_option('ilist_debug_log')) {
							ilist_log("Cron action");
							ilist_log('Order id ('.$order->get_date_created().'): '.$ilist_product->order_id);
							ilist_log('Order Status changed to: '.$order_status);
						}
						// -----------------------------------------------------
						// Get order items infos ($order est déjà chargée ci-dessus)
						// -----------------------------------------------------
						$items = $order->get_items();
						// -----------------------------------------------------
						// Initialize
						// -----------------------------------------------------
						$ilist_list_products = "";
						$participation_id    = ilist_get_option('ilist_id_participation_product');
						// -----------------------------------------------------
						// Loop on products from order
						// -----------------------------------------------------
						foreach ( $items as $item ) {
							//$product_name         = $item['name'];
							//$product_id           = $item['product_id'];
							//$product_variation_id = $item['variation_id'];
							$product_id = $item['product_id'];
							// --- Update DB product of the List
							$ilist_update_status = $wpdb->update( 
								ILIST_TBL_PRODUCT, 
								array( 'status' => '2' ), 
								array( 'order_id' => $product_order_id, 'product_id' => $product_id ),
								array( '%s' ),
								array( '%s', '%s' )
							);
							// --- Construct product's list of the email
							if ($ilist_is_cagnotte) {
								$product_title        = __( 'Participation in the purchase of one of your choices', ILIST_ID_LANGUAGES );
								// Get price
								$price_participation  = $item['total'];
								//$ilist_list_products .= ' ' . $product_title . ' (' . $ilist_product->product_name . ')' . ',';
								$ilist_list_products .= ' ' . $product_title . ' (' . ( ($participation_id == $product_id) ? $ilist_product->product_name : get_the_title($product_id) ) . ' - ' . $this->get_woocommerce_price_format($price_participation) . ')' . ',';
							} else {
								$_product_id = (isset($item['variation_id']) && $item['variation_id'] > 0) ? $item['variation_id'] : $item['product_id'];
								$product_title = get_the_title($_product_id);
								$ilist_list_products .= ' <a href="'.get_permalink($_product_id).'">'.$product_title.'</a>';
								// If pot AND end of participation
								$_end_of_participation = ilist_is_end_participation($_product_id, ilist_is_order($product_order_id), $product_order_id);
								if ($_end_of_participation) $ilist_list_products .= " (" . __( 'End of participation', ILIST_ID_LANGUAGES ) . ": " . $_end_of_participation . ")";
								$ilist_list_products .= ',';
							}
							// Option log (if activated)
							if (ilist_get_option('ilist_debug_log')) {
								$__product_id = (isset($item['variation_id']) && $item['variation_id'] > 0) ? $item['variation_id'] : $item['product_id'];
								ilist_log('Product ('.$__product_id.'): '.get_the_title($__product_id));
							}
						}
						
						// ----------------------------------------------------- 
						// Update product of list
						// Exception hook woocommerce_checkout_order_processed
						// who add several products from cart and session
						// -----------------------------------------------------
						$ilist_update_status = $wpdb->update( 
							ILIST_TBL_PRODUCT, 
							array( 'customer' => NULL, 'date' => NULL, 'order_id' => NULL ), 
							array( 'order_id' => $product_order_id, 'status' => '1' ),
							array( '%s', '%s', '%s' ),
							array( '%s', '%s' )
						);					
						
						// --- Get comment checkout (if exists)
						$ilist_order_comment    = ilist_get_order_meta( $product_order_id, 'order-meta-ilist' );
						$ilist_comment_checkout = ($ilist_order_comment) ? $ilist_order_comment : __("None", ILIST_ID_LANGUAGES);
						// --- Get all infos products
						$request_infos_products = "";
						if ($ilist_is_cagnotte) {
							$request_infos_products = "SELECT id, list_id, date, customer, product_name, product_id FROM " . ILIST_TBL_CAGNOTTE . " WHERE order_id = '$product_order_id'";
						} else {
							$request_infos_products = "SELECT id, list_id, date, customer, product_name, product_id FROM " . ILIST_TBL_PRODUCT . " WHERE status = '2' AND order_id = '$product_order_id'" ;
						}
						$ilist_infos_products = $wpdb->get_results( $request_infos_products );
						$ilist_list_customer = "";
						$ilist_list_list_id  = "";
						foreach($ilist_infos_products as $ilist_row) {
							$ilist_list_customer   = $ilist_row->customer;
							$ilist_list_list_id    = $ilist_row->list_id;
							$ilist_list_order_date = $ilist_row->date;
						}
						$ilist_list_products = rtrim($ilist_list_products, ",");
						$ilist_list_name     = $this->get_list_info($ilist_list_list_id, 'name');
						// -------------------------------------------
						// -------------------------------------------
						// Send Email to ADMINISTRATOR(S)
						// -------------------------------------------
						// -------------------------------------------
						// --- Get options
						$ilist_emails_admin         = trim(ilist_get_option( 'ilist_email_admin' ));
						$ilist_email_admin_name     = trim(ilist_get_option( 'ilist_email_admin_name' ));
						$ilist_email_admin_response = trim(ilist_get_option( 'ilist_email_admin_response' ));
						$ilist_email_admin_subject  = trim(ilist_get_option( 'ilist_email_subject_alert_one_admin' ));
						$ilist_email_admin_body     = trim(ilist_get_option( 'ilist_email_body_alert_one_admin' ));
						// --- Replace Variables in email subject
						$ilist_email_admin_subject = @preg_replace("/{ILIST_NAME}/", esc_html($ilist_list_name), $ilist_email_admin_subject);
						// --- Replace Variables in email body
						$ilist_type = ilist_get_status_info(2, 'title'); // Internet purchase
						$ilist_email_admin_body = @preg_replace("/{ILIST_NAME}/", esc_html($ilist_list_name), $ilist_email_admin_body);
						$ilist_email_admin_body = @preg_replace("/{ILIST_PRODUCT}/", $ilist_list_products, $ilist_email_admin_body);
						$ilist_email_admin_body = @preg_replace("/{ILIST_FRIEND}/", esc_html($ilist_list_customer), $ilist_email_admin_body);
						$ilist_email_admin_body = @preg_replace("/{ILIST_DATE}/", $this->convert_date($ilist_list_order_date, 'FRT'), $ilist_email_admin_body);
						$ilist_email_admin_body = @preg_replace("/{ILIST_TYPE}/", $ilist_type, $ilist_email_admin_body);
						$ilist_email_admin_body = @preg_replace("/{ILIST_ORDER_ID}/", $product_order_id, $ilist_email_admin_body);
						$ilist_email_admin_body = @preg_replace("/{ILIST_COMMENT}/", $ilist_comment_checkout, $ilist_email_admin_body);
						$ilist_email_admin_body = @preg_replace("/{ILIST_WP_SITE_NAME}/", esc_html(get_bloginfo('name')), $ilist_email_admin_body);
						$ilist_email_admin_body = @preg_replace("/{ILIST_WP_SITE_DESC}/", esc_html(get_bloginfo('description')), $ilist_email_admin_body);
						// --- Send email
						if (!function_exists('ilist_set_html_mail_content_type')) {
							function ilist_set_html_mail_content_type() {
								return 'text/html';
							}
						}
						add_filter( 'wp_mail_content_type', 'ilist_set_html_mail_content_type' );
						// Initialize
						//$attachments = array( WP_CONTENT_DIR . '/uploads/file_to_attach.zip' );
						$headers   = ["From: $ilist_email_admin_name <$ilist_email_admin_response>",
									  ];
						$to_admins = $ilist_emails_admin;
						$subject   = $ilist_email_admin_subject;
						$body      = nl2br($ilist_email_admin_body);
						// -----------------------------
						wp_mail( $to_admins, $subject, $body, $headers, $attachments );
						// Option log (if activated)
						if (ilist_get_option('ilist_debug_log')) {
							ilist_log('Email Admin: '.$to_admins);
						}
						// -------------------------------------------
						// -------------------------------------------
						// Send Email to CLIENT
						// -------------------------------------------
						// -------------------------------------------
						// --- Get options
						$ilist_email_client         = $this->get_list_info($ilist_list_list_id, 'email');
						$ilist_email_client_subject = trim(ilist_get_option( 'ilist_email_subject_alert_one' ));
						$ilist_email_client_body    = trim(ilist_get_option( 'ilist_email_body_alert_one' ));
						// --- Replace Variables in email subject
						$ilist_email_client_subject = @preg_replace("/{ILIST_NAME}/", esc_html($ilist_list_name), $ilist_email_client_subject);
						// --- Replace Variables in email body
						$ilist_email_client_body = @preg_replace("/{ILIST_NAME}/", esc_html($ilist_list_name), $ilist_email_client_body);
						$ilist_email_client_body = @preg_replace("/{ILIST_PRODUCT}/", $ilist_list_products, $ilist_email_client_body);
						$ilist_email_client_body = @preg_replace("/{ILIST_FRIEND}/", esc_html($ilist_list_customer), $ilist_email_client_body);
						$ilist_email_client_body = @preg_replace("/{ILIST_DATE}/", $this->convert_date($ilist_list_order_date, 'FRT'), $ilist_email_client_body);
						$ilist_email_client_body = @preg_replace("/{ILIST_TYPE}/", $ilist_type, $ilist_email_client_body);
						$ilist_email_client_body = @preg_replace("/{ILIST_COMMENT}/", $ilist_comment_checkout, $ilist_email_client_body);
						$ilist_email_client_body = @preg_replace("/{ILIST_WP_SITE_NAME}/", esc_html(get_bloginfo('name')), $ilist_email_client_body);
						$ilist_email_client_body = @preg_replace("/{ILIST_WP_SITE_DESC}/", esc_html(get_bloginfo('description')), $ilist_email_client_body);
						// --- Send email
						// Initialize
						//$attachments = array( WP_CONTENT_DIR . '/uploads/file_to_attach.zip' );
						$to_client = $ilist_email_client;
						$subject_2 = $ilist_email_client_subject;
						$body_2    = nl2br($ilist_email_client_body);
						// -----------------------------
						wp_mail( $to_client, $subject_2, $body_2, $headers, $attachments );
						// Option log (if activated)
						if (ilist_get_option('ilist_debug_log')) {
							ilist_log('Email Client: '.$to_client);
						}
						// Reset content-type to avoid conflicts -- https://core.trac.wordpress.org/ticket/23578
						remove_filter( 'wp_mail_content_type', 'ilist_set_html_mail_content_type' );
					}
				}			
			}
		}

		/**
		 * Set custom product price when adding to cart (on the fly)
		 *
		 * @param 	$cart 	Cart object
		 *
		 * @return void
		 */
		public function cart_items_prices( $cart ) {
		
			if ( is_admin() && ! defined( 'DOING_AJAX' ) )
				return;
		
			if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 )
				return;
		
			// Loop Through cart items
			foreach ( $cart->get_cart() as $cart_item ) {
				// Get an instance of the WC_Product object
		        $product    = $cart_item['data'];
				// Get the product id (or the variation id)
				$product_id = $cart_item['data']->get_id();
				$price      = $cart_item['data']->get_price();
				$name       = $cart_item['data']->post->post_title;
		
				// PHP Session Start
				$session_ilist_customer_products = ilist_get_session( 'ilist_customer_products' );
				// if (session_status() === PHP_SESSION_NONE) session_start();
				// SET THE NEW PRICE
				if (isset($session_ilist_customer_products)) {
					foreach($session_ilist_customer_products as $row) {
						// Check if product come from SESSION
						if ($product_id == $row['variation_id']) {
							$list_id = $row['list_id'];
							$real_id = $row['participation'];
							// Check if it's a new participation or second or more
							$is_additional_participations = ilist_get_all_participations_by_product($list_id, $real_id);
							if (!$is_additional_participations) {
								$participation = $row['total'];
								$set_new_price = $price - $row['total'];
								// Updated cart item PRICE
								$cart_item['data']->set_price( $set_new_price );
							}
						}
					}
				}
				// PHP Session Close
				// session_write_close();
			}
		}
		
		/**
		 * Update product name if participation product
		 */
		public function cart_product_title( $title, $values, $cart_item_key ) {
			global $wpdb;
			// PHP Session Start
			$session_ilist_customer_products = ilist_get_session( 'ilist_customer_products' );
			// if (session_status() === PHP_SESSION_NONE) session_start();
			// SET THE NEW NAME
			if (isset($session_ilist_customer_products)) {
				foreach($session_ilist_customer_products as $row) {
					// Check if product come from SESSION
					if ($values['product_id'] == $row['variation_id']) {
						$list_id = $row['list_id'];
						$real_id = $row['participation'];
						$total   = $row['total'];
						// Get product name
						$query = "SELECT product_name FROM " . ILIST_TBL_PRODUCT . " WHERE list_id = '$list_id' AND (product_id = '$real_id' OR variation_id = '$real_id') LIMIT 1";
						// Get A product with a specific key
						$product = $wpdb->get_row( $query );
						// Updated cart item NAME if participation product
						if ($product) {
							return $title . ' (' . $product->product_name . ')';
						} else {
							if (ilist_get_option( 'ilist_pot_is_active' )) return $title . ' (' . apply_filters('ilist_pot_product_cart_title', 'filter_pot_product_cart_title_text') . ')';
						}
					}
				}
			}
			// PHP Session Close
			// session_write_close();
			return $title;
		}
		
		/**
		 * Show help icon
		 *
		 * @param 	$url 	Help url
		 *
		 * @return string
		 */
		public function helpicon($url = '') {
			$help_icon = '<a href="https://doc.ilist-kado.com/' . $url . '" target="_blank" class="dashicons-before dashicons-editor-help ilist-doc-help" title="' . __('Help', ILIST_ID_LANGUAGES) . '"></a>';
			return $help_icon;
		}
		
		/**
		 * Update product name if participation product
		 */
		public function get_list_password( $keyaccess = false ) {
			global $wpdb;
			// Check if List ID
			if (!$keyaccess) return false;
			// Get password from list
			$password = $wpdb->get_var( "SELECT password FROM " . ILIST_TBL_MAIN . " WHERE keyaccess = '$keyaccess'" );

			return (trim($password) != '') ? trim($password) : false;
		}
     
		/**
		 * Add note if participation product
		 */
		public function add_order_instruction_email( $order, $sent_to_admin, $plain_text, $email ) {
			echo '<p><strong>' . __( 'Note', ILIST_ID_LANGUAGES ) . ':</strong> ' . __( 'Crowdfunding', ILIST_ID_LANGUAGES ) . '</p>';
		}
		
		/**
		 * Add item information if participation product in email order
		 */
		public function order_item_information_email($item_id, $item, $order, $plain_text) {
			global $wpdb;
			// ADD ADDITIONAL INFORMATIONS in PRODUCT NAME
			$order_id = $order->get_id();
			// -----------------------------
			$participation_id = ilist_get_option('ilist_id_participation_product');
			// -----------------------------
			// Example:
			// $item[order_id]     => 349
			// $item[name]         => Polo T-shirt - Black
			// $item[product_id]   => 19
			// $item[variation_id] => 198
			// $item[quantity]     => 1
			// $item[tax_class]    =>
			// $item[subtotal]     => 5
			// $item[subtotal_tax] => 0
			// $item[total]        => 5
			// $item[total_tax]    => 0
			// -----------------------------
			$product_id = ($item['variation_id'] > 0) ? $item['variation_id'] : $item['product_id'];
			// -----------------------------
			// Check if crowdfunding (Cagnotte TBL)
			// -----------------------------
			$query   = "SELECT product_name FROM " . ILIST_TBL_CAGNOTTE . " WHERE order_id = '$order_id' LIMIT 1";
			$product = $wpdb->get_row( $query );
			// Updated cart item NAME if participation product
			if ($product) {
				if ($participation_id == $product_id) echo '<p style="color: grey; font-style: italic;">' . $product->product_name . '</p>';
			} else {
				// -----------------------------
				// Check if crowdfunding (Product TBL)
				// -----------------------------
				$query   = "SELECT product_name FROM " . ILIST_TBL_PRODUCT . " WHERE order_id = '$order_id' LIMIT 1";
				$product = $wpdb->get_row( $query );
				// Updated cart item NAME if participation product
				if ($product) {
					if ($participation_id == $product_id) echo '<p style="color: grey; font-style: italic;">' .apply_filters('ilist_pot_product_cart_title', 'filter_pot_product_cart_title_text' ) . '</p>';
				}
			}
		}
		
		/**
		 * Add pot select ranges
		 */
		public function pot_select($pid, $price, $name, $participation_id = false, $total = 0) {
			// Options
			$ilist_pot_min_price_product = ilist_get_option( 'ilist_pot_min_price_product' );
			$ilist_pot_range_price       = ilist_get_option( 'ilist_pot_range_price' );
			// --- Check if product price is equal or granter than POT min price
			if ($price < $ilist_pot_min_price_product) {
				// No select to display
				//return __( "No Min.", ILIST_ID_LANGUAGES );
				return;
			} else {
				if ($total > 0) $price = $price - $total; // Recalculate the participation price 
				// Display select
				$select  = "";
				
				$select .= '<select class="change_pot" id="ilist_pot_'.$pid.'" name="ilist_pot_'.$pid.'" data-id="'.$pid.'" data-partid="'.$participation_id.'">';
				
					//$select .= '<option value="">' . __( "Your participation", ILIST_ID_LANGUAGES ) . '</option>';
					$cpt = 1;
					for($i = $ilist_pot_range_price; $i < $price; $i += $ilist_pot_range_price) {
						$select .= '<option data-quantity="' . $cpt . '" value="' . $i . '">' . __('Participation of', ILIST_ID_LANGUAGES) . ' ' . $this->get_woocommerce_price_format( $i ) . '</option>';
						$cpt++;
					}
					// Display only if TOTAL participation is equal to 0
					$text = ($total == 0) ? __('Purchase product at', ILIST_ID_LANGUAGES) . ' ' . $this->get_woocommerce_price_format( $price ) : __('Participation of', ILIST_ID_LANGUAGES) . ' ' . $this->get_woocommerce_price_format( $price );
					$select .= '<option data-quantity="no" selected="" value="' . $price . '">' . $text . '</option>';
					
				
				$select .= '</select>';
				
				return $select;
			}
		}
		
		/**
		 * Add pot text
		 */
		public function pot_text($pid, $price, $name, $total, $id) {
			global $wpdb;
			// --- Options
			$ilist_pot_min_price_product = ilist_get_option( 'ilist_pot_min_price_product' );
			// --- Check if product is sold
			$is_sold = $wpdb->get_var( $wpdb->prepare( "SELECT status FROM " . ILIST_TBL_PRODUCT . " WHERE id = '%s'", $id ) );
			if ($is_sold > 1) return "--";
			// --- Check if product price is equal or granter than POT min price
			if ($price < $ilist_pot_min_price_product) {
				// No select to display
				return apply_filters('ilist_pot_no_min', 'ilist_pot_value_no_min' );
			} else {
				return apply_filters('ilist_pot_display_participation', $total, $price);
			}
		}
		
		/**
		 * Check if pot is active
		 */
		public function product_pot_is_active($pid) {
			//ILIST_TBL_PRODUCT
		}
		
		/**
		 * Check if email new order (WC) needs to be updated
		 *
		 * @param 	$formated_subject
		 * @param 	$order
		 *
		 * @return formatted email subject (string)
		 */
		public function customizing_new_order_subject( $formated_subject, $order ) {
			// Initialize
			$ilist_email_subject_wc_new_order = ilist_get_option('ilist_email_subject_wc_new_order');
			// Get an instance of the WC_Email_New_Order object
			$email = WC()->mailer->get_emails()['WC_Email_New_Order'];
			// Get unformatted subject from settings
			$subject = $email->get_option( 'subject', $email->get_default_subject() );
			// Check if subject had to be updated
			if (isset($ilist_email_subject_wc_new_order) && trim($ilist_email_subject_wc_new_order) != '') {
				// Check if the order was made from a list
				$is_list_full_buy    = ilist_is_order(  $order->get_id() );
				$is_list_partial_buy = ilist_is_participation(  $order->get_id() );
				if ($is_list_full_buy || $is_list_partial_buy) {
					// Get list name
					$ilist_get_name = ($is_list_full_buy) ? ilist_get_list_infos_by_id( $is_list_full_buy, 'name' ) : ilist_get_list_infos_by_id( $is_list_partial_buy, 'name' );
					// Replace string if necessary
					$ilist_email_subject_wc_new_order = str_replace( '{ILIST_NAME}', $ilist_get_name, $ilist_email_subject_wc_new_order );
					// Customizing email
					$subject = $subject . ' ' . $ilist_email_subject_wc_new_order;
					// Format and return the custom formatted subject
					return $email->format_string( $subject );
				}
			}
			// Format and return subject not modified
			return $email->format_string( $subject );
		}
		
		/**
		 * Format price with WooCommerce method
		 *
		 * @param 	$price 		Float price (or not)
		 *
		 * @return formatted price (string)
		 */
		public function get_woocommerce_price_format($price) {
			return sprintf( get_woocommerce_price_format(), get_woocommerce_currency_symbol(), $price );
		}
		
		/**
		 * Get participation
		 *
		 * @param 	$pid 			Product ID
		 * @param 	$keyaccess 		Key Access List
		 *
		 * @return amount (float)
		 */
		public function get_participation($pid, $keyaccess, $list_product_id = false) {
			global $wpdb;
			if (!$pid || !$keyaccess) return;
			// Get list ID
			$list_id = $this->get_list_info_by_keyaccess($keyaccess, 'id');
			// Total
			if (!$list_product_id) {
				$total = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(participation) as total FROM " . ILIST_TBL_CAGNOTTE . " WHERE list_id = '%s' AND variation_id = '%s'", $list_id, $pid ) );
			} else {
				$total = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(participation) as total FROM " . ILIST_TBL_CAGNOTTE . " WHERE list_id = '%s' AND variation_id = '%s' AND list_product_id = '%s'", $list_id, $pid, $list_product_id ) );
			}
			return ($total) ? $total : false;
		}
		
		/**
		 * Get List info by Keyaccess
		 *
		 * @param 	$key 		Key access
		 * @param	$column		Column to display
		 *
		 * @return string
		 */
		public function get_list_info_by_keyaccess($key, $column = 'date') {
			global $wpdb;
			
			// --- Get info
			$list_info = $wpdb->get_var( $wpdb->prepare( "SELECT $column FROM " . ILIST_TBL_MAIN . " WHERE keyaccess = '%s'", $key ) );
			return $list_info;
		}
		
		/**
		 * Get infos from a list by ID
		 */
		public function get_list_info($list_id = NULL, $column = '') {
			global $wpdb;
			$table_column = ($column != '') ? $column : '*';
			
			$list_info = $wpdb->get_row( "SELECT $table_column FROM " . ILIST_TBL_MAIN . " WHERE id = $list_id" );
		
			// Check result    
			if ( null !== $list_info ) {
				return $list_info->{$table_column};
			} else {
				// no info found
				return false;
			}
			
		}
		
		/**
		 * Add custom column to WooCommerce admin orders list
		 */
		public function add_wc_order_column( $columns ) {
			// Initialize
			$new_columns = array();
			// Check if ilist_details column AND if display list column is activated
			if ( ilist_get_option( 'ilist_wc_display_table_list_column' ) ) {
				if ($columns) {
					foreach ( $columns as $column_name => $column_info ) {
						$new_columns[ $column_name ] = $column_info;
						if ( 'order_status' === $column_name ) {
							$new_columns['ilist_details'] = __( 'List', ILIST_ID_LANGUAGES );
						}
					}
					// Add new columns
					return $new_columns;
				}
			} else {
				// Return default columns
				return $columns;
			}
		}

		/**
		 * Add custom column content to WooCommerce admin orders list
		 */
		public function add_wc_order_column_content( $column, $post_or_order = null ) {
			// En HPOS, la commande est passée en 2e argument ; sur l'écran
			// historique le hook ne passe que la colonne et s'appuie sur $post.
			if ( $post_or_order instanceof WC_Order ) {
				$order_id = $post_or_order->get_id();
			} else {
				global $post;
				$order_id = ( $post ) ? $post->ID : 0;
			}
			if ( !$order_id ) return;
			// Check if ilist_details column AND if display list column is activated
			if ( 'ilist_details' === $column && ilist_get_option( 'ilist_wc_display_table_list_column' ) ) {
				// Search if order concerns a list - full purchase OR partial purchase (ILIST)
				$is_list_full_buy    = ilist_is_order( $order_id );
				$is_list_partial_buy = ilist_is_participation( $order_id );
				if ($is_list_full_buy || $is_list_partial_buy) {
					if ($is_list_full_buy) {
						// ------------------------------------
						// Achat du produit complet
						// ------------------------------------
						echo '<a target="_blank" href="' . admin_url('admin.php?page=ilist_products&id='.$is_list_full_buy) . '">' . ilist_get_list_infos_by_id( $is_list_full_buy, 'name' ) . '</a>';
						$all_products_sold = ilist_get_all_products_sold_by_list_id($is_list_full_buy, $order_id);
						$all_products_text = "";
						if ($all_products_sold) {
							$all_products_text = '<br>';
							foreach($all_products_sold as $product) {
								$participation = "";
								// If pot AND end of participation
								$_product_id = (isset($product->variation_id) && $product->variation_id > 0) ? $product->variation_id : $product->product_id;
								$_end_of_participation = ilist_is_end_participation($_product_id, $product->list_id, $product->order_id);
								if ($_end_of_participation) $participation .= " (<strong>" . __( 'End of participation', ILIST_ID_LANGUAGES ) . "</strong>)";
								
								//$participation = ($is_list_partial_buy) ? " (Participation)" : "";

								$all_products_text .= $product->product_name . $participation . ' / ';
							}
							echo rtrim($all_products_text, ' / ');
						}
						echo '<br>';
					}
					if ($is_list_partial_buy) {
						// ------------------------------------
						// Participations à l'achat du produit
						// ------------------------------------
						echo '<a target="_blank" href="' . admin_url('admin.php?page=ilist_products&id='.$is_list_partial_buy) . '">' . ilist_get_list_infos_by_id( $is_list_partial_buy, 'name' ) . '</a> (' . __( 'Participation', ILIST_ID_LANGUAGES ) . ')';
						$all_products_sold = ilist_get_all_participations_sold_by_list_id($is_list_partial_buy, $order_id);
						$all_products_text = "-$is_list_partial_buy-";
						if ($all_products_sold) {
							$all_products_text = '<br>';
							foreach($all_products_sold as $product) {
								$all_products_text .= $product->product_name . ' / ';
							}
							echo rtrim($all_products_text, ' / ');
						}
						echo '<br>';
					}
				} else {
					echo '--';
				}
			}
		}
		
		/**
		 * Make custom column sortable to WooCommerce admin orders list
		 */
		public function add_wc_order_column_sortable( $columns ) {
			$custom_columns = [
				'ilist_details' => 'ilist_details'
			];
			return wp_parse_args( $custom_columns, $columns );
		}
		
		/**
		 * Add custom infos to WooCommerce admin orders details (Column GENERAL)
		 */
		public function add_wc_order_meta_infos( $order ) {
			// Check if display order detail is activated
			if ( ilist_get_option( 'ilist_wc_display_detail_order' ) ) {
				$get_order_id         = $order->get_id();
				$get_list_full_buy    = ilist_is_order( $get_order_id );
				$get_list_partial_buy = ilist_is_participation( $get_order_id );
				// Search if order concerns a list (ILIST)
				if ($get_list_full_buy || $get_list_partial_buy) {
					echo '<br class="clear" />';
					echo '<h3>' . __('Products ordered from list', ILIST_ID_LANGUAGES) . '</h4>';
				}
				if ($get_list_full_buy) {
					// Get all products from this order
					$all_products_sold = ilist_get_all_products_sold_by_list_id($get_list_full_buy, $get_order_id);
					echo '<ul>';
					if ($all_products_sold) {
						foreach($all_products_sold as $product) {
							$list_name = ilist_get_list_infos_by_id($product->list_id, 'name');
							echo '<li>' . $product->product_name . ' : <a target="_blank" href="' . admin_url('admin.php?page=ilist_products&id='.$product->list_id) . '">' . $list_name . '</a></li>';
						}
					}
					echo '<ul>';
				}
				if ($get_list_partial_buy) {
					// Get all participations from this order
					$all_products_sold = ilist_get_all_participations_sold_by_list_id($get_list_partial_buy, $get_order_id);
					echo '<ul>';
					if ($all_products_sold) {
						foreach($all_products_sold as $product) {
							$list_name = ilist_get_list_infos_by_id($product->list_id, 'name');
							echo '<li>' . $product->product_name . ' : <a target="_blank" href="' . admin_url('admin.php?page=ilist_products&id='.$product->list_id) . '">' . $list_name . '</a>  (' . __( 'Participation', ILIST_ID_LANGUAGES ) . ')</li>';
						}
					}
					echo '<ul>';
				}
				// Initialize
				$meta_field_message = ilist_get_order_meta( $get_order_id, 'order-meta-ilist' );
				// Check if ILIST Message
				if (isset($meta_field_message) && $meta_field_message != '') {
					echo '<br class="clear" />';
					echo '<h3>' . __('ILIST Message', ILIST_ID_LANGUAGES) . '</h3>';
			
					echo $meta_field_message;
				}
			}
		}
		/**
		 * Add custom infos to WooCommerce admin orders details (Column BILLING)
		 */
		public function add_wc_order_meta_billing( $order ) {
		
		}
		
		
		
		/**
		 * Notice ERROR if automatic email settings is not completed
		 */
		public function email_auto_check() {

			if ( !ilist_get_option( 'ilist_email_admin_response' )
				|| !ilist_get_option( 'ilist_email_admin_name' )
				|| !ilist_get_option( 'ilist_email_admin' )
				|| !ilist_get_option( 'ilist_email_subject_alert_one' )
				|| !ilist_get_option( 'ilist_email_body_alert_one' )
				|| !ilist_get_option( 'ilist_email_subject_alert_one_admin' )
				|| !ilist_get_option( 'ilist_email_body_alert_one_admin' )
			) {
				$class   = 'notice notice-error';
				$message = sprintf( __( 'ILIST: You must complete your settings Automatic emails before using the plugin (Save your <a href="%s">email settings</a>)', ILIST_ID_LANGUAGES ), get_admin_url(get_current_blog_id(), 'admin.php?page=ilist-emails') );
			
				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), ( $message ) );
			}
		}
		
		
		
		/**
		 * Adding Meta Box Admin Order page
		 *
		 * @return void
		 */
		function order_message() {
			// L'identifiant d'écran de la fiche commande dépend du mode de
			// stockage : 'shop_order' (post) ou 'woocommerce_page_wc-orders'
			// (HPOS). wc_get_page_screen_id() renvoie le bon dans les deux cas.
			$screen = function_exists( 'wc_get_page_screen_id' ) ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order';
			add_meta_box( 'ilist_message_field', __( 'ILIST Message', ILIST_ID_LANGUAGES ), array( $this, 'message_field_for_order' ), $screen, 'side', 'core' );
		}

		/**
		 * Adding Meta info in the meta container admin shop_order pages
		 *
		 * @param	$post_or_order	WP_Post (stockage historique) ou WC_Order (HPOS)
		 *
		 * @return string
		 */
		function message_field_for_order( $post_or_order = null ) {
			// En HPOS, la callback reçoit l'objet commande ; sur l'écran
			// historique elle reçoit le WP_Post.
			if ( $post_or_order instanceof WC_Order ) {
				$order_id = $post_or_order->get_id();
			} elseif ( $post_or_order && isset( $post_or_order->ID ) ) {
				$order_id = $post_or_order->ID;
			} else {
				global $post;
				$order_id = ( $post ) ? $post->ID : 0;
			}

			$meta_field_message = ilist_get_order_meta( $order_id, 'order-meta-ilist' );

			if ($meta_field_message != '') {
				echo '<p style="border-bottom: solid 1px #eee; padding-bottom: 13px;">';
					echo $meta_field_message;
				echo '</p>';
			} else {
				echo '<em style="color: darkgrey;">' . __( 'No message for this order', ILIST_ID_LANGUAGES ) . '</em>';
			}
	
		}
		
		/**
		 * Add formatted anchors to table
		 *
		 * @param 	$string   string to parse
		 *
		 * @return string (html)
		 */
		public function parse_to_anchor($string) {
			if (!$string) return;
			return strtolower( strip_tags( ilist_rewrite_string($string) ) );
		}

		/** **********************************
		 * ***********************************
		 *     WORDPRESS FILTERS / ACTIONS
		 * ***********************************
		 * ********************************* */
		
		/**
		 * Filter value pot if minimum price not reached 
		 *
		 * @param 	$string 	Text if minimum price not reached 
		 *
		 * @return string
		 * --------------------------------
		 * Hook USAGE:
		 * function example_new_value( $string ) {
		 *     return $string . ' + new value';
		 * }
		 * add_filter( 'ilist_pot_no_min', 'example_new_value', 10 );
		 */
		public function filter_pot_value_no_min() {
			return '-';
		}
		
		/**
		 * Filter value pot if not activated for this product
		 *
		 * @param 	$string 	Text if pot not activated
		 *
		 * @return string
		 * --------------------------------
		 * Hook USAGE:
		 * function example_pot_not_activated( $string ) {
		 *     return $string . ' + new value';
		 * }
		 * add_filter( 'ilist_pot_not_activated', 'example_pot_not_activated', 10 );
		 */
		public function filter_pot_not_activated() {
			return '-';
		}
		
		/**
		 * Filter add value cart title if product is participation product
		 *
		 * @param 	$string 	text add to title participation product
		 *
		 * @return string
		 * --------------------------------
		 * Hook USAGE:
		 * function example_add_cart_title_value( $string ) {
		 *     return ' add new value';
		 * }
		 * add_filter( 'ilist_pot_product_cart_title', 'example_add_cart_title_value', 10 );
		 */
		public function filter_pot_product_cart_title_text() {
			return __( 'Participation', ILIST_ID_LANGUAGES );
		}
		
		/**
		 * Filter participation text (participation, total)
		 *
		 * @param 	$participation 		participation amount in progress
		 * @param 	$total				total price to pay
		 *
		 * @return string
		 * --------------------------------
		 * Hook USAGE:
		 * function example_new_format( $participation, $total ) {
		 *     return number_format($participation, 0, ".", " ") . '€ SUR '. number_format($total, 0, ".", " ") . '€';
		 * }
		 * add_filter( 'ilist_pot_display_participation', 'example_new_format', 10, 2 );
		 */
		public function filter_pot_display_participation($participation, $total) {
			return $this->get_woocommerce_price_format( ($participation > 0 ? $participation : 0) ) . ' / ' . $this->get_woocommerce_price_format( $total );
		}
		
		/**
		 * Filter participation separator text
		 *
		 * @return string
		 * --------------------------------
		 * Hook USAGE:
		 * function example_new_pot_separator_text() {
		 *     return " / ";
		 * }
		 * add_filter( 'ilist_pot_separator_text', 'example_new_pot_separator_text', 10 );
		 */
		public function filter_pot_separator_participation() {
			return " + ";
		}
		
		/**
		 * Filter protected password list text
		 *
		 * @return string
		 * --------------------------------
		 * Hook USAGE:
		 * function example_new_protected_password_list_text() {
		 *     return "New text";
		 * }
		 * add_filter( 'ilist_protected_password_list_text', 'example_new_protected_password_list_text', 10 );
		 */
		public function filter_protected_password_list() {
			return __("Password protected list", ILIST_ID_LANGUAGES);
		}
		
		/**
		 * Action add content before login
		 *
		 * @param 	$content	 string (HTML)
		 *
		 * @return string
		 * --------------------------------
		 * Hook USAGE:
		 * function example_add_content_before_login( $content ) {
		 *     echo $content . ' add content'
		 * }
		 * add_action( 'ilist_before_login', 'example_add_content_before_login', 10, 1 );
		 */
		public function add_content_before_login($content) {
			echo '';
		}
		
		/**
		 * Action add content after login
		 *
		 * @param 	$content	 string (HTML)
		 *
		 * @return string
		 * --------------------------------
		 * Hook USAGE:
		 * function example_add_content_after_login( $content ) {
		 *     echo $content . ' add content'
		 * }
		 * add_action( 'ilist_after_login', 'example_add_content_after_login', 10, 1 );
		 */
		public function add_content_after_login($content) {
			echo '';
		}
		
		/**
		 * Add content login
		 *
		 * @param 	$content	 string (HTML)
		 *
		 * @return string
		 * --------------------------------
		 * Hook USAGE:
		 * function example_change_content_login( $content ) {
		 *     echo 'My new content';
		 * }
		 * add_action( 'ilist_login', 'example_change_content_login', 10, 1 );
		 */
		public function add_content_login($content) {
			$content  = "";
			$content .= '<div class="ilist_user_logged_out">';
			$content .= __( 'You must be logged in to view your lists and create new ones', ILIST_ID_LANGUAGES );
			$content .= '</div>';
			$content .= '<p>';
			$content .= '<a class="button alt" href="' . get_permalink( ilist_get_option('woocommerce_myaccount_page_id') ) . '" title="' . __("Connexion", ILIST_ID_LANGUAGES ) . '">';
			$content .= __("Log in", ILIST_ID_LANGUAGES );
			$content .= '</a>';
			$content .= '&nbsp;&nbsp;';
			$content .= '<a class="button alt" href="' . get_permalink( ilist_get_option('woocommerce_myaccount_page_id') ) . '" title="' . __("Create account", ILIST_ID_LANGUAGES ) . '">';
			$content .= __("Create account", ILIST_ID_LANGUAGES );
			$content .= '</a>';
			$content .= '</p>';
			
			echo $content;
		}
		
		/**
		 * [ADMIN] Add content after status filter (ILIST - Products list)
		 *
		 * @param 	$content	 string (HTML)
		 *
		 * @return string
		 * --------------------------------
		 * Hook USAGE:
		 * function example_change_admin_list_products_after( $content ) {
		 *     echo 'My new content';
		 * }
		 * add_action( 'ilist_admin_list_products_after', 'example_change_admin_list_products_after', 10, 1 );
		 */
		public function admin_list_products_after($content) {
			$content  = "";
			echo $content;
		}
		
	} // end ./Ilist class

}

$Ilist = Ilist::get();