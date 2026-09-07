<?php
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// Blocking direct access to plugin      -=
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
defined('ABSPATH') or die('Are you crazy!');

/**
 * Shortcode GENERAL ILIST
 * This method return HTML to display in FRONT page with Shortcode
 *
 * @usage 	[ilist]
 *
 * @return HTML
 */
add_shortcode('ilist', 'ilist_shortcode_general');
if (!function_exists("ilist_shortcode_general")) {
	function ilist_shortcode_general($param, $content) {
		global $wpdb, $Ilist;
		// --- Initialize
		$ILIST_REQUEST_PROTOCOL = (ilist_is_secure()) ? 'https' : 'http';
		$ilist_url_current      = "$ILIST_REQUEST_PROTOCOL://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$ilist_page_used        = str_replace("//", "/", parse_url($ilist_url_current, PHP_URL_PATH) . '/');
		$ilist_general          = "";
		// --- Actions
		$action = (isset($_GET['a'])) ? trim($_GET['a']) : '';
		// --- Switch
		switch($action) {
			default: // Landing page Ilist
				// --- Templates path
				$action_default = 'default';
				$ilist_template = ILIST_PATH . 'templates' . DIRECTORY_SEPARATOR . ILIST_ID . '-template-' . $action_default . '.php';
				// --- Check if user is logged in
				$ilist_show_tabs = (is_user_logged_in()) ? ilist_get_lists(get_current_user_id()) : false;
				
				$ilist_general .= include_once($ilist_template);
			break;
		
			case "liste":
				// --- Templates path
				$action_default         = 'list';
				$ilist_template         = ILIST_PATH . 'templates' . DIRECTORY_SEPARATOR . ILIST_ID . '-template-' . $action_default . '.php';
				$ilist_keyaccess        = ilist_stopXSS($_GET['k']);
				$ilist_stock            = ilist_get_option( 'ilist_stock_management' );
				$ilist_general_bytpl    = ilist_get_option( 'ilist_general_bytpl' );
				$ilist_hide_add_product = ilist_get_option( 'ilist_hide_add_product' );
				$ilist_event_date       = ilist_get_list_infos($ilist_keyaccess, 'event_date');
				$message_success        = [];
				$message_error          = [];
				// Delete product
				if (isset($_POST['productdel']) && 'del' === $_POST['productdel'] && $_GET['k'] != '') {
					// Check if list exist
					$list_keyaccess      = ilist_stopXSS($_GET['k']);
					$ilist_product_exist = $wpdb->get_row( "SELECT id FROM " . ILIST_TBL_MAIN . " WHERE keyaccess = '$list_keyaccess' AND active = '1'" );
					// Product exists, we can remove item
					if ($ilist_product_exist) {
						$_product_id   = intval($_POST['product_id']);
						$_variation_id = (isset($_POST['variation_id']) && $_POST['variation_id'] > 0) ? intval($_POST['variation_id']) : NULL;
						$ilist_pid     = intval($_POST['pid']);
						$delete        = $wpdb->query("DELETE FROM " . ILIST_TBL_PRODUCT . " WHERE id = '$_product_id'");
						if (!$delete) {
							$message_error[] = __("An error occurred while deleting the product.<br>Retry or contact an administrator.", ILIST_ID_LANGUAGES);
						} else {
							$message_success[] = __("The product has been successfully removed from your list", ILIST_ID_LANGUAGES);
							// --------------------------------------------------------------------------
							// Check if management stock product AND management stock ILIST are activated
							// --------------------------------------------------------------------------
							$_product = ($_variation_id) ? wc_get_product( $_variation_id ) : wc_get_product( $ilist_pid );
							ilist_update_stock($_product, 'increase');
						}
					} else {
						$message_error[] = __("This list does not exist or is disabled", ILIST_ID_LANGUAGES);
					}
				}
				// Add product
				if ( ( (isset($_POST['productadd']) && $_POST['productadd'] > 0) || (isset($_POST['productaddpot']) && $_POST['productaddpot'] > 0) ) && $_POST['list_k'] != '' ) {
					// Check if list exist
					$list_keyaccess   = ilist_stopXSS($_POST['list_k']);
					$ilist_list_exist = $wpdb->get_row( "SELECT id FROM " . ILIST_TBL_MAIN . " WHERE keyaccess = '$list_keyaccess' AND active = '1'" );
					// List exists, we can insert item(s)
					if ($ilist_list_exist) {
						$productadd = (isset($_POST['productaddpot'])) ? $_POST['productaddpot'] : $_POST['productadd'];
						// Search if variable product
						if (strpos($productadd, "|") === false) {
							// Simple product - Item ID / Product ID
							$ilist_list_id      = $ilist_list_exist->id;
							$ilist_pid          = intval($productadd);
							$ilist_vid          = NULL;  
							$ilist_product_name = esc_html( get_the_title( $ilist_pid ) );
						} else {
							// Variable product
							$ilist_list_id      = $ilist_list_exist->id; 
							list($ilist_pid, $ilist_vid) = explode("|", $productadd);
							$ilist_product_name = esc_html( get_the_title( intval($ilist_vid) ) );
						}
						$_post_is_pot         = (isset($_POST['productaddpot'])) ? "1" : "0";
						$ilist_is_pot_product = (ilist_get_option( 'ilist_pot_is_active' )) ? $_post_is_pot : "0";
						// Insert product into the list
						$insert = $wpdb->insert( 
							ILIST_TBL_PRODUCT, 
							array( 
								 'list_id'      => $ilist_list_id
								,'product_id'   => $ilist_pid
								,'variation_id' => $ilist_vid
								,'product_name' => $ilist_product_name
								,'is_pot'       => $ilist_is_pot_product
							), 
							array( '%d', '%d', '%d', '%s', '%d' )
						);
						if (!$insert) {
							$message_error[] = __("The product was not added", ILIST_ID_LANGUAGES);
						} else {
							$message_success[] = __("The product has been added to your list successfully", ILIST_ID_LANGUAGES);
							// Check if management stock product AND management stock ILIST are activated
							$_product = ($ilist_vid) ? wc_get_product( $ilist_vid ) : wc_get_product( $ilist_pid );
							ilist_update_stock($_product, 'decrease');
						}
					} else {
						$message_error[] = __("This list does not exist or is disabled", ILIST_ID_LANGUAGES);
					}
				}
	
				// Get all products from a list
				$ilist_show_tabs = ilist_get_woocommerce_product_list_by_key($ilist_keyaccess);
	
				// --- Check if show TPL or not
				if ($ilist_general_bytpl) {
					//$ilist_general .= include_once($ilist_template);
					$return_shortcode_list = include_once($ilist_template);
					return $return_shortcode_list;
				} else {
					// -------------------------------------
					// Sets locale information (FR)
					// -------------------------------------
					ilist_get_locale();
					// -------------------------------------
					// ILIST Class
					// -------------------------------------
					global $Ilist;
					// -------------------------------------
					// Initialize return
					// -------------------------------------
					$return = "";
					// -------------------------------------
					// Instantiate 
					// -------------------------------------
					$current_user_id         = get_current_user_id();
					$current_list_keyaccess  = ilist_stopXSS($_GET['k']);
					$list_is_current_user_id = $wpdb->get_row( "SELECT * FROM " . ILIST_TBL_MAIN . " WHERE user_id = '$current_user_id' AND keyaccess = '$current_list_keyaccess'" );
					// ------------------------------------
					// Initialize SESSION
					// ------------------------------------
					// --- ilist customer infos
					$session_ilist_customer_products = ilist_get_session( 'ilist_customer_products' );
					if (!isset($session_ilist_customer_products)) {
						ilist_set_session( 'ilist_customer_products' , array() );
					}
					// --- List protected by password
					$session_ilist_password_protected = ilist_get_session( 'ilist_password_protected' );
					if (!isset($session_ilist_password_protected)) {
						ilist_set_session( 'ilist_password_protected' , "" );
					}
					// ------------------------------------
					// Initialize Password INFOS
					// ------------------------------------
					// --- Hash password (DB)
					$ilist_password = $Ilist->get_list_password($current_list_keyaccess);
					// --- PASSWORD Algorithm
					$ilist_pwd_algo = $Ilist->algo_password;
					// ------------------------------------
					// Initialize Form Messages
					// ------------------------------------
					if (isset($message_success) && array_keys( $message_success, true )) {
						$return .= '<div class="alert alert-success">';
						for ($i = 0; $i < count($message_success); ++$i) {
							$return .= $message_success[$i] . '<br>';
						}
						$return .= '</div>';
					}
					
					if (isset($message_error) && array_keys( $message_error, true )) {
						$return .= '<div class="alert alert-danger">';
						for ($i = 0; $i < count($message_error); ++$i) {
							$return .= $message_error[$i] . '<br>';
						}
						$return .= '</div>';
					}
					
					// ----------------------------------------
					//$ilist_add_products = ilist_get_option( 'ilist_add_products_to_lists' ); // OBSOLETE
					$ilist_add_products = true;
					// ----------------------------------------
					
					// ----------------------------------------
					// ONLY IF ACCOUNT PAGE (WooCommerce)
					// or if the list belongs to the currently logged in user
					// ----------------------------------------
					if (!function_exists("is_account_page")) {
						// ----------------------------------------
						// WooCommerce is not installed
						// ----------------------------------------
						$return .= __( "The WooCommerce Plugin is not installed", ILIST_ID_LANGUAGES );
						return $return;
					}
					// ----------------------------------------
					
					// ----------------------------------------
					// Check if event date exists
					// AND if event date is bigger than current date
					// ----------------------------------------
					$ilist_current_day = date("Y-m-d 00:00:00");
					if (isset($ilist_event_date) && $ilist_current_day > $ilist_event_date ) {
						// ----------------------------------------
						// Hide list
						// ----------------------------------------
						$return .= __( "Event date is over!", ILIST_ID_LANGUAGES );
						return $return;
					}
					// ----------------------------------------
					
					// ----------------------------------------
					// Add items to list if in account page
					// ----------------------------------------
					$ilist_add_products_to_list = '
					<div id="ilist-wrapper">
						<div class="ilist-select">
							<form method="post">
								<div class="control-group">
									<span id="loading">
										<div class="spinner">
											<div class="double-bounce1"></div>
											<div class="double-bounce2"></div>
										</div>
									</span>
									<label for="keysearch">' . __( "Add products to your list", ILIST_ID_LANGUAGES ) . '</label>
									<input autocomplete="off" name="keysearch" value="" placeholder="' . __( "Enter product name of your shop (3 characters min.)", ILIST_ID_LANGUAGES ) . '" id="keysearch" type="text" class="form-control">
									<input type="hidden" name="list_k" value="' . $_GET['k'] . '" />
									<!--<input type="hidden" name="productadd" value="add" />-->
									<div id="result"></div>
								</div>
							</form>
						</div>
					</div>
				
					<hr>';
					// ----------------------------------------
					// Check if list belongs to creator
					// ----------------------------------------
					if (isset($list_is_current_user_id->id)) {
						$return .= (!$ilist_hide_add_product) ? $ilist_add_products_to_list : '';
					}

					// --------------------------------------------------------------------------
					// --------------------------------------------------------------------------
					// --------------------------------------------------------------------------
					if (isset($ilist_show_tabs) && array_keys( $ilist_show_tabs, true ) ) {
						// ----------------------------------------
						// Initialise SESSION
						// ----------------------------------------
						// --- ilist customer infos
						$session_ilist_customer_products  = ilist_get_session( 'ilist_customer_products' );
						// --- List protected by password
						$session_ilist_password_protected = ilist_get_session( 'ilist_password_protected' );
						// ----------------------------------------
						// Check Password
						// ----------------------------------------
						// 1. Is there a password on the list? 
						// 2. Is password session empty ?
						// 3. If password, Check
						// 4. Does the list belong to the logged creator?
						// ----------------------------------------
						if ( $Ilist->get_list_password($current_list_keyaccess)
							&& ( $session_ilist_password_protected == "" || !password_verify( trim($session_ilist_password_protected), $ilist_password ) )
							&& ( !$current_user_id || ($current_user_id > 0 && $current_user_id != $list_is_current_user_id->user_id) )
						) {
							// ----------------------------------------
							// Password POST
							// ----------------------------------------
							if (isset($_POST['password_list']) && $_POST['password_list'] != '') {			
								// Verify password
								$ilist_pwd_verify = password_verify( trim($_POST['password_list']), $ilist_password );
								// Get result
								if ($ilist_pwd_verify) {
									$session_ilist_password_protected = trim($_POST['password_list']);
								} else {
									$return .= '<strong style="color: red;">' . __("Incorrect password!", ILIST_ID_LANGUAGES) . '</strong><br>';
								}
								
							}
							// --- Check if Password is ok
							if (!isset($session_ilist_password_protected) || $session_ilist_password_protected == '' || !password_verify( trim($session_ilist_password_protected), $ilist_password )) {
								// ----------------------------------------
								// List is protected by Password
								// ----------------------------------------
								$return .= '<h3>' . ilist_get_list_infos($current_list_keyaccess, 'name') . '</h3>';
								$return .= apply_filters( 'ilist_protected_password_list_text', 'filter_protected_password_list' );
								// ----------------------------------------
								// Password Form
								// ----------------------------------------
								$return .= '<form action="' . ilist_current_url() . '" method="post">';
								$return .= '<input type="password" name="password_list" placeholder="' . __('Password', ILIST_ID_LANGUAGES) . '">';
								$return .= '<input type="submit" value="valider">';
								$return .= '</form>';
								return $return;
							}
							
						}
						
						// ----------------------------------------
						// Initialize CSS Lightbox
						// ----------------------------------------
						$ilist_front_color_lightbox = ilist_get_option( 'ilist_front_color_lightbox' );
						switch($ilist_front_color_lightbox) {
							default:
								$ilist_modal_color_box = "da8025";
								$ilist_modal_img_close = "default";
							break;
							case "pink":
								$ilist_modal_color_box = "efa0da";
								$ilist_modal_img_close = "pink";
							break;
							case "red":
								$ilist_modal_color_box = "fe1717";
								$ilist_modal_img_close = "red";
							break;
							case "lightgreen":
								$ilist_modal_color_box = "54c451";
								$ilist_modal_img_close = "lightgreen";
							break;
							case "darkgreen":
								$ilist_modal_color_box = "136b12";
								$ilist_modal_img_close = "darkgreen";
							break;
							case "yellow":
								$ilist_modal_color_box = "dedb37";
								$ilist_modal_img_close = "yellow";
							break;
							case "grey":
								$ilist_modal_color_box = "808080";
								$ilist_modal_img_close = "grey";
							break;
							case "lightblue":
								$ilist_modal_color_box = "51c4c4";
								$ilist_modal_img_close = "lightblue";
							break;
							case "darkblue":
								$ilist_modal_color_box = "1f3e90";
								$ilist_modal_img_close = "darkblue";
							break;
							case "violet":
								$ilist_modal_color_box = "a181d4";
								$ilist_modal_img_close = "violet";
							break;
							case "brown":
								$ilist_modal_color_box = "b43709";
								$ilist_modal_img_close = "brown";
							break;
							case "white":
								$ilist_modal_color_box = "FFFFFF";
								$ilist_modal_img_close = "white";
							break;
						}
						$return .= '<style>
								.ilist_modal_box {background-color: #' . $ilist_modal_color_box . ' !important;}
								.ilist_modal_box .plainmodal-close {background: url("' . ILIST_URL . '/images/plainmodal-close-' . $ilist_modal_img_close . '.png") no-repeat !important;}';
								if ($ilist_front_color_lightbox == 'white') {
									$return .= '.ilist_modal_box, .ilist_modal_box h2, .ilist_modal_box h3, .ilist_modal_box h4, .ilist_modal_box p {
										color: black;
									}';
								}
						$return .= '</style>';
						// ----------------------------------------
						// Share list
						// ----------------------------------------
						$share_list                = ilist_get_option( 'ilist_share_list' );
						$ilist_url_complete        = ilist_get_option( 'ilist_url_complete' );
						$ilist_search_url_complete = ilist_get_option( 'ilist_search_url_complete' );
						$ilist_list_image_format   = ilist_get_option( 'ilist_format_image' );
						// ----------------------------------------
						// List User name
						// ----------------------------------------
						$user_id          = ilist_get_list_infos($current_list_keyaccess, 'user_id');
						$user_name        = (ilist_get_option( 'ilist_display_user_name' )) ? ' (' . ucfirst(ilist_get_list_infos($current_list_keyaccess, 'firstname')) . ' ' . strtoupper(ilist_get_list_infos($current_list_keyaccess, 'lastname')) . ')' : "";
						$description      = trim(ilist_get_list_infos($current_list_keyaccess, 'description'));
						$ilist_desc       = ($description != '') ? '<br>' . $description : '';
						$ilist_list_image = trim(ilist_get_list_infos($current_list_keyaccess, 'image'));
						// ----------------------------------------
						// Switch according to the chosen theme
						// ----------------------------------------
						switch($ilist_list_image_format) {
							default:
							case "simple":
								// ----------------------------------------
								// List name
								// ----------------------------------------
								$return .= '<h5 class="ilist-h5">' . __( "List", ILIST_ID_LANGUAGES ) . ' : ' . ilist_get_list_infos($current_list_keyaccess, 'name') . $user_name . $ilist_desc . '</h5>';
								// --- Check if image list
								if (isset($ilist_list_image) && $ilist_list_image != '') {
									$ilist_image_height = ilist_get_option( 'ilist_image_height' );
									$return .= '<div class="ilist_list_image">';
										$return .= '<img id="ilist_list_image" style="max-height: ' . $ilist_image_height . 'px; max-width: 100%;" src="' . $ilist_list_image . '" alt="ILIST">';
									$return .= '</div>';
								}
							break;
							case "theme_1":
							case "theme_2":
							case "theme_3":
								// --- Get theme
								$ilist_format_image_theme = "ilist-format-image-" . str_replace("_", "-", $ilist_list_image_format);
								// --- Check if image list
								$ilist_is_list_image = (isset($ilist_list_image) && $ilist_list_image != '') ? $ilist_list_image : ILIST_URL . 'images/image-default-ilist-theme.png';
								$return .= '<div id="ilist-format-image" style="background-image: url('.$ilist_is_list_image.');">';
									$return .= '<h5 class="ilist-format-image-h5 ' . $ilist_format_image_theme . '">';
										$return .= '<div class="ilist-format-image-title">' . __( "List", ILIST_ID_LANGUAGES ) . ' : ' . ilist_get_list_infos($current_list_keyaccess, 'name') . $user_name . '</div>';
										if ($ilist_desc != '') {
											$return .= '<div class="ilist-format-image-description">' . $ilist_desc . '</div>';
										}
									$return .= '</h5>';
								$return .= '</div>';
							break;
						}
						// --- Check if Share icons to display
						if ( $share_list ) {
							$return .= '<h4 class="ilist-share-h4">' . __( "Share this list", ILIST_ID_LANGUAGES ) . '</h4>';
							$return .= ilist_get_share_icons( $ilist_url_complete . '?a=liste&k=' . $current_list_keyaccess, $current_list_keyaccess );
						}
						// ----------------------------------------
						// Get POT Option
						// ----------------------------------------
						$is_pot_active = ilist_get_option( 'ilist_pot_is_active' );
						if ($is_pot_active) {
							// ----------------------------------------
							// Initialize POT
							// ----------------------------------------
							$ilist_pot_min_price_product = ilist_get_option( 'ilist_pot_min_price_product' );
							$ilist_pot_range_price       = ilist_get_option( 'ilist_pot_range_price' );
							$ilist_pot_product_id        = ilist_get_option( 'ilist_id_participation_product' );
						}
						// ----------------------------------------
						// Open TABLE
						// ----------------------------------------
						$return .= '<div style="overflow-x:auto;">';
						$return .= '<table class="shop_table cart ilist-table" cellspacing="0">
							<thead>
								<tr>
									<th class="ilist-image"></th>
									<th class="ilist-items">' . __( "Product", ILIST_ID_LANGUAGES ) . '</th>
									<th class="ilist-price">' . __( "Price", ILIST_ID_LANGUAGES ) . '</th>';
						if ($is_pot_active) $return .= '<th class="ilist-pot">' . __( "Participations", ILIST_ID_LANGUAGES ) . '</th>';
						$return .= '<th class="ilist-addtocart">' . __( "Status", ILIST_ID_LANGUAGES ) . '</th>';
						if ( ($ilist_add_products || is_account_page()) && isset($list_is_current_user_id->id)) $return .= '<th class="ilist-action">' . __( "Action", ILIST_ID_LANGUAGES ) . '</th>';
						$return .= '</tr>
							</thead>
							<tbody>';
						// ----------------------------------------
						// Get all product status
						// ----------------------------------------
						$ilist_get_all_status = ilist_get_status_list();
						// ----------------------------------------
						// Get Quickview infos
						// ----------------------------------------
						$ilist_link_list_behavior          = ilist_get_option( 'ilist_link_list_behavior' ); // --- Links Behavior
						$ilist_link_list_href_blank        = ilist_get_option( 'ilist_link_list_href_blank' ); // --- Links href (blank or not)
						$ilist_quickview_title             = ilist_get_option( 'ilist_quickview_title' ); // --- Title is visible (or not)
						$ilist_quickview_price             = ilist_get_option( 'ilist_quickview_price' ); // --- Price is visible (or not)
						$ilist_quickview_description_short = ilist_get_option( 'ilist_quickview_description_short' ); // --- Description (short) is visible (or not) and if exists
						$ilist_quickview_description_long  = ilist_get_option( 'ilist_quickview_description_long' ); // --- Description (long) is visible (or not)
						$ilist_get_description_lenght      = ilist_get_option( 'ilist_quickview_description_truncate' ); // --- Product Description: Get lenght of characters visible
						$ilist_product_with_vat            = ilist_get_option( 'ilist_ttc_product_in_list' ); // --- Product price with VAT
							
						foreach ( $ilist_show_tabs as $row_tab ) {
							$ilist_is_pot_active = ($is_pot_active && (isset($row_tab->is_pot) && $row_tab->is_pot == '1')) ? true : false;
							$_product = wc_get_product( $row_tab->ID );
							// ----------------------------------------
							// Check if product exists
							// ----------------------------------------
							if (!$_product) continue;
							// ----------------------------------------
							// Get price
							// ----------------------------------------
							// $_product->get_regular_price();
							// $_product->get_sale_price();
							// $_product->get_price();
							$_product_thumbnail_id = get_post_thumbnail_id( $row_tab->ID );
							$_product_img = ($_product_thumbnail_id) ? wp_get_attachment_image_src( get_post_thumbnail_id( $row_tab->ID ), 'thumbnail' ) : [ ILIST_URL . 'images/thumbnail-default.jpg' ];
							// ----------------------------------------
							// Check type product
							// ----------------------------------------
							switch ( $_product->get_type() ) {
								case "variable":
								case "variation": // WooCommerce > 5.0
									// Check if product will change ( Simple => Variable )
									if ($row_tab->variation_id) {
										// Always variable product
										$variations         = wc_get_product($row_tab->variation_id);
										$_product_url       = get_permalink($row_tab->variation_id);
										$_product_name      = $row_tab->product_name;
										$_product_get_price = ($variations) ? ( $ilist_product_with_vat ? $variations->get_price_including_tax() : $variations->get_price() ) : 0;
										$_product_price     = $Ilist->get_woocommerce_price_format( $_product_get_price );
										// Get image variable product if exists
										$_product_get_img = ($variations) ? $variations->get_image_id() : 0;
										$_v_product_img   = wp_get_attachment_image_src( $_product_get_img, 'thumbnail' ); // WC > 5.0
										$_product_image   = ($_v_product_img) ? $_v_product_img[0] : $_product_img[0];
									} else {
										// Show simple product
										$_product_url   = get_permalink($row_tab->ID);
										$_product_name  = $row_tab->product_name;
										$_product_price = $Ilist->get_woocommerce_price_format( ( $ilist_product_with_vat ? $_product->get_price_including_tax() : $_product->get_price() ) ) ;
										$_product_image = $_product_img[0];
									}
								break;
								case "grouped":
								case "external":
								default:
									$_product_url   = get_permalink($row_tab->ID);
									$_product_name  = $row_tab->product_name;
									$_product_price = $Ilist->get_woocommerce_price_format( ( $ilist_product_with_vat ? $_product->get_price_including_tax() : $_product->get_price() ) );
									$_product_image = $_product_img[0];
								break;
							}
							// ----------------------------------------
							$_tr_ilist_pot = (!$is_pot_active) ? "" : "ilist_pot_tr";
							$return .= '<tr rel="'.$_product->get_type().'" id="ilist-tr-'.$row_tab->ID.'" class="'.$_tr_ilist_pot.'">
									<td class="ilist-image">';
										if ($ilist_link_list_behavior == 'lightbox') {
											$return .= '<div id="ilist-img-' . $row_tab->ID . '" class="custom-img" style="background-image: url(\''.$_product_image.'\')"></div>';
										} else {
											if ($ilist_link_list_href_blank) {
												$return .= '<div id="ilist-img-' . $row_tab->ID . '" class="custom-img" style="background-image: url(\''.$_product_image.'\')" onclick="window.open(\'' . $_product_url . '\', \'_blank\', \'noopener,noreferrer\')"></div>';
											} else {
												$return .= '<div id="ilist-img-' . $row_tab->ID . '" class="custom-img" style="background-image: url(\''.$_product_image.'\')" onclick="window.location.href=\'' . $_product_url . '\'"></div>';
											}
										}
									$return .= '</td>';
									$return .= '<td class="ilist-items">';
										if ($ilist_link_list_behavior == 'lightbox') {
											$return .= '<a id="ilist-box-' . $row_tab->ID . '" href="' . $_product_url . '">' . esc_html($_product_name) . '</a>';
											$return .= '<div id="ilist-modal-box-' . $row_tab->ID . '" class="ilist_modal_box">
												<div class="plainmodal-close"></div>
												<img src="'.$_product_image.'" alt="">';
												if ($ilist_quickview_title) $return .= '<h2>' . $_product_name . '</h2>';
												// --- Show post excerpt if exists
												$_product_excerpt = get_the_excerpt($row_tab->ID);
												if ($ilist_quickview_description_short && (isset($_product_excerpt) && $_product_excerpt != '')) { 
													$return .= '<br><h3>' . wp_strip_all_tags($_product_excerpt) . '</h3><br>';
												} else {
													$return .= '<br>';
												}
												// --- Show price
												if ($_product_excerpt == '') $return .= '<br>';
														  
												if ($ilist_quickview_price) $return .= '<h4>' . $_product_price . '</h4>';
												
												// --- Show description
												$_product_content = get_the_content( null, false, $row_tab->ID );
												if ($ilist_quickview_description_long) $return .= '<p>' . ( (isset($ilist_get_description_lenght) && $ilist_get_description_lenght > 0) ? ilist_truncate($_product_content, $ilist_get_description_lenght, '...') : $_product_content ) . '</p>';
											$return .= '</div>';
										} else {
											// Href blank or not
											$target_blank  = ($ilist_link_list_href_blank) ? ' target="_blank"' : '';
											$return .= '<a id="ilist-box-' . $row_tab->ID . '" href="' . $_product_url . '"' . $target_blank . '>' . esc_html($_product_name) . '</a>';
										}
									$return .= '</td>';
									// Product price
									$return .= '<td class="ilist-price">&nbsp;' . $_product_price . '</td>';
									// ----------------------------------------
									// POT (Affichage de la colonne si option activee)
									// ----------------------------------------
									if ($is_pot_active) {
										switch ( $_product->get_type() ) {
											case "variable":
											case "variation": // WooCommerce > 5.0
												//$pot_product_price = $variations->get_price();
												$pot_product_price = ($variations) ? ( $ilist_product_with_vat ? $variations->get_price_including_tax() : $variations->get_price() ) : 0;
												// Get total participation
												$total = $Ilist->get_participation($row_tab->variation_id, $row_tab->keyaccess, $row_tab->id);
											break;
											case "grouped":
											case "external":
											default:
												$pot_product_price = ($ilist_product_with_vat) ? $_product->get_price_including_tax() : $_product->get_price();
												// Get total participation
												$total = $Ilist->get_participation($row_tab->product_id, $row_tab->keyaccess, $row_tab->id);
											break;
										}
										// Check if current user is list creator
										if ($list_is_current_user_id) {
											$return .= '<td class="ilist-pot">' . ( (isset($row_tab->is_pot) && $row_tab->is_pot == '1') ? __( 'Activated', ILIST_ID_LANGUAGES) : __( 'Desactivated', ILIST_ID_LANGUAGES) ) . '</td>';
										} else {
											// If pot is activated for this product
											if (isset($row_tab->is_pot) && $row_tab->is_pot == '1') {
												$return .= '<td class="ilist-pot">' . $Ilist->pot_text($row_tab->ID, $pot_product_price, $_product_name, $total, $row_tab->id) . '</td>';
											} else {
												$return .= '<td class="ilist-pot">' . apply_filters('ilist_pot_not_activated', 'filter_pot_not_activated') . '</td>';
											}
										}
									}
									// ----------------------------------------
									// Modal Box
									// ----------------------------------------
									if ($ilist_link_list_behavior == 'lightbox') {
										$return .= '<script>
												jQuery( document ).ready(function() {
													jQuery("#ilist-box-' . $row_tab->ID . ', #ilist-img-' . $row_tab->ID . '").click(function(event) {
													  jQuery("#ilist-modal-box-' . $row_tab->ID . '").plainModal( "open", { duration: 500, overlay: {fillColor: "#000", opacity: 0.7} } );
													  return false;
													});
												});
											</script>';
									}
									// ----------------------------------------
									// Switch beetween Status
									// 1: A vendre
									// 2: Achat Internet
									// 3: Achat Boutique
									// ----------------------------------------
									switch($row_tab->status) {
										case "1": // Toujours a vendre
											$return .= '<td class="ilist-addtocart">';
											if ( ($ilist_add_products || is_account_page()) && isset($list_is_current_user_id->id) ) {
												//---------------------------------------------
												// Check if participations
												//---------------------------------------------
												$product_id = (isset($row_tab->variation_id) && $row_tab->variation_id > 0) ? intval($row_tab->variation_id) : intval($row_tab->product_id);
												$list_id = intval($row_tab->list_id);
												//---------------------------------------------
												// Get participation from list ID AND product ID
												//---------------------------------------------
												$participations = ilist_get_all_participations_by_product($list_id, $product_id);
												if ($participations) {
													$return .= '<span class="ilist-description">' . __( 'Participations in progress', ILIST_ID_LANGUAGES ) . '</span><br>';
													$all_parts = "";
													foreach($participations as $participation) {
														$all_parts .= $participation->customer . ' (' . $Ilist->get_woocommerce_price_format( $participation->participation ) . ')' . apply_filters('ilist_pot_separator_text', 'filter_pot_separator_participation');
													}
													$return .= trim( rtrim($all_parts, apply_filters('ilist_pot_separator_text', 'filter_pot_separator_participation')) );
													$return .= '</td>';
													$return .= '<td class="ilist-action">';
													$return .= '</td>';
												} else {
													$return .= '<span class="ilist-bold">' . __( "Not still bought", ILIST_ID_LANGUAGES ) . '</span>';
													$return .= '</td>';
													$return .= '<td class="ilist-action">';
													$return .= '<form method="post" onclick="return confirm(\'' . addslashes( __('Want to remove this product from your list?', ILIST_ID_LANGUAGES) ) . '\')">';
													$return .= '<input type="submit" class="button" value="' . __( "Delete", ILIST_ID_LANGUAGES ) . '">';
													$return .= '<input type="hidden" name="pid" value="' . $row_tab->product_id . '" />';							
													$return .= '<input type="hidden" name="variation_id" value="' . ( ($_product->get_type() == 'variable' || $_product->get_type() == 'variation' ) ? $row_tab->variation_id : "") . '" />';
													$return .= '<input type="hidden" name="product_id" value="' . $row_tab->id . '" />';
													$return .= '<input type="hidden" name="productdel" value="del" />';
													$return .= '</form>';
												}
											} else {
												// ------------------------------------------------------------------------------
												// ------------------------------------------------------------------------------
												// ------------------------------------------------------------------------------
												switch ( $_product->get_type() ) {
													case "grouped": // Grouped product
														$link    = get_permalink($_product->get_id());
														$label   = apply_filters('grouped_add_to_cart_text', __('View options', 'woocommerce'));
														$return .= sprintf('<a href="%s" rel="nofollow" data-product_id="%s" data-variation_id="%s" data-bpid="%s" data-k="%s" class="button add_to_cart_button product_type_%s">%s</a>', $link, $_product->get_id(), NULL, $row_tab->id, $row_tab->keyaccess, $_product->get_type(), $label);
													break;
													case "external": // External product
														$link    = get_permalink($_product->get_id());
														$label   = apply_filters('external_add_to_cart_text', __('Read More', 'woocommerce'));
														$return .= sprintf('<a href="%s" rel="nofollow" data-product_id="%s" data-variation_id="%s" data-bpid="%s" data-k="%s" class="button add_to_cart_button product_type_%s">%s</a>', $link, $_product->get_id(), NULL, $row_tab->id, $row_tab->keyaccess, $_product->get_type(), $label);
													break;
													case "variable":  // WC < 5.0
													case "variation": // WC > 5.0
														if (!$ilist_is_pot_active) {
															//---------------------------------------------
															// --- NO POT (Variable product)
															//---------------------------------------------
															$link    = esc_url( $_product->add_to_cart_url() );
															$label   = apply_filters('add_to_cart_text', __('Add to cart', 'woocommerce'));
															$spinner = (ilist_get_option('ilist_ajax_cart_button_remove_after_click')) ? '<img src="' . admin_url('images/loading.gif') . '" class="ilist-spinner spinner_' . $row_tab->id . '">' : '';
															$spinner_onclick = (ilist_get_option('ilist_ajax_cart_button_remove_after_click')) ? 'onclick="return ilist_ajax_cart_button(\'' . $row_tab->id . '\')"' : '';
															$return .= '<form id="' . $row_tab->id . '" action="' . $link . '" class="cart ilistcart" method="post" enctype="multipart/form-data">					
																<button
																	id="ilist_button_' . $row_tab->id . '"
																	class="button alt ajax_add_to_cart add_to_cart_button product_type_simple"
																	type="submit"
																	data-quantity="1"
																	data-product_id="' . $row_tab->variation_id . '"
																	data-variation_id="' . $row_tab->variation_id . '"
																	data-bpid="' . $row_tab->id . '"
																	data-k="' . $row_tab->keyaccess . '"' . $spinner_onclick . '>'
																	. $label . '</button>' . $spinner . '
															</form>';
														} else {
															//---------------------------------------------
															// --- POT IS ACTIVE (Variable product)
															//---------------------------------------------
															$link    = esc_url( $_product->add_to_cart_url() );
															$label   = apply_filters('add_to_cart_text', __('Add to cart', 'woocommerce'));
															$spinner = (ilist_get_option('ilist_ajax_cart_button_remove_after_click')) ? '<img src="' . admin_url('images/loading.gif') . '" class="ilist-spinner spinner_' . $row_tab->variation_id . '">' : '';
															$spinner_onclick = (ilist_get_option('ilist_ajax_cart_button_remove_after_click')) ? 'onclick="return ilist_ajax_cart_button(\'' . $row_tab->variation_id . '\')"' : '';
															$return .= '<form id="' . $row_tab->variation_id . '" action="' . $link . '" class="cart ilistcart" method="post" enctype="multipart/form-data">
																' . $Ilist->pot_select($row_tab->variation_id, $pot_product_price, $_product_name, $ilist_pot_product_id, $total) . '
																<input type="hidden" id="quantity_' . $row_tab->variation_id . '" name="quantity" value="1">
																<button
																	id="pot_' . $row_tab->variation_id . '"
																	class="button alt ajax_add_to_cart add_to_cart_button product_type_simple"
																	type="submit"
																	data-quantity="1"
																	data-product_id="' . $row_tab->variation_id . '"
																	data-variation_id="' . $row_tab->variation_id . '"
																	data-bpid="' . $row_tab->id . '"
																	data-total="' . $total . '"
																	data-real_id="' . $row_tab->variation_id . '"
																	data-k="' . $row_tab->keyaccess . '"' . $spinner_onclick . '>'
																	. $label . '
																</button>' . $spinner . '
															</form>';
														}
													break;
													default: // Simple product
														if (!$ilist_is_pot_active) {
															//---------------------------------------------
															// --- NO POT (Simple product)
															//---------------------------------------------
															$link    = esc_url( $_product->add_to_cart_url() );
															$label   = apply_filters('add_to_cart_text', __('Add to cart', 'woocommerce'));
															$spinner = (ilist_get_option('ilist_ajax_cart_button_remove_after_click')) ? '<img src="' . admin_url('images/loading.gif') . '" class="ilist-spinner spinner_' . $row_tab->id . '">' : '';
															$spinner_onclick = (ilist_get_option('ilist_ajax_cart_button_remove_after_click')) ? 'onclick="return ilist_ajax_cart_button(\'' . $row_tab->id . '\')"' : '';
															$return .= '<form id="' . $row_tab->id . '" action="' . $link . '" class="cart ilistcart" method="post" enctype="multipart/form-data">					
																<button
																	id="ilist_button_' . $row_tab->id . '"
																	class="button alt ajax_add_to_cart add_to_cart_button product_type_simple"
																	type="submit"
																	data-quantity="1"
																	data-product_id="' . $_product->get_id() . '"
																	data-bpid="' . $row_tab->id . '"
																	data-k="' . $row_tab->keyaccess . '"' . $spinner_onclick . '>'
																	. $label . '
																</button>' . $spinner . '
															</form>';
														} else {
															//---------------------------------------------
															// --- POT IS ACTIVE (Simple product)
															//---------------------------------------------
															$link    = esc_url( $_product->add_to_cart_url() );
															$label   = apply_filters('add_to_cart_text', __('Add to cart', 'woocommerce'));
															$spinner = (ilist_get_option('ilist_ajax_cart_button_remove_after_click')) ? '<img src="' . admin_url('images/loading.gif') . '" class="ilist-spinner spinner_' . $row_tab->product_id . '">' : '';
															$spinner_onclick = (ilist_get_option('ilist_ajax_cart_button_remove_after_click')) ? 'onclick="return ilist_ajax_cart_button(\'' . $row_tab->product_id . '\')"' : '';
															$return .= '<form id="' . $row_tab->product_id . '" action="' . $link . '" class="cart ilistcart" method="post" enctype="multipart/form-data">
																' . $Ilist->pot_select($row_tab->product_id, $pot_product_price, $_product_name, $ilist_pot_product_id, $total) . '
																<input type="hidden" id="quantity_' . $row_tab->product_id . '" name="quantity" value="1">
																<button
																	id="pot_' . $row_tab->product_id . '"
																	class="button alt ajax_add_to_cart add_to_cart_button product_type_simple"
																	type="submit"
																	data-quantity="1"
																	data-product_id="' . $row_tab->product_id . '"
																	data-variation_id="' . $row_tab->product_id . '"
																	data-bpid="' . $row_tab->id . '"
																	data-total="' . $total . '"
																	data-real_id="' . $row_tab->product_id . '"
																	data-k="' . $row_tab->keyaccess . '"' . $spinner_onclick . '>'
																	. $label . '
																</button>' . $spinner . '
															</form>';
														}
													break;
												}
												// ------------------------------------------------------------------------------
												// ------------------------------------------------------------------------------
												// ------------------------------------------------------------------------------					
											}
											$return .= '</td>';
										break;
										default: // Others status than 1
											// ----------------------------------------
											// Check if current user is in account page (WooCommerce)
											// ----------------------------------------
											$return .= '<td class="ilist-addtocart">';
											if ( ($ilist_add_products || is_account_page()) && $list_is_current_user_id ) {
												$return .= '<span class="ilist-description">' . ilist_get_status_info(intval($row_tab->status), 'title') . '</span><br>';
												$return .= __( "Offered by ", ILIST_ID_LANGUAGES ) . ' ' . $row_tab->customer;
												//---------------------------------------------
												// Check if participations
												//---------------------------------------------
												$product_id = (isset($row_tab->variation_id) && $row_tab->variation_id > 0) ? intval($row_tab->variation_id) : intval($row_tab->product_id);
												$list_id = intval($row_tab->list_id);
												//---------------------------------------------
												// Get participation from list ID AND product ID
												//---------------------------------------------
												$participations = ilist_get_all_participations_by_product($list_id, $product_id);
												if ($participations) {
													foreach($participations as $participation) {
														$return .= apply_filters('ilist_pot_separator_text', 'filter_pot_separator_participation') . $participation->customer . ' (' . $Ilist->get_woocommerce_price_format( $participation->participation ) . ')';
													}
												}
											} else {
												$return .= __( "Offered product", ILIST_ID_LANGUAGES );
											}
											$return .= '</td>';
											if ( ($ilist_add_products || is_account_page()) && $list_is_current_user_id ) $return .= '<td class="ilist-action"></td>';
										break;
									}
							$return .= '</tr>';
						}
						// ----------------------------------------
						// Close TABLE
						// ----------------------------------------
						$return .= '</tbody>';
						$return .= '</table>';
						$return .= '</div>';
						// ----------------------------------------
						// Check if spinner
						// ----------------------------------------
						if (ilist_get_option('ilist_ajax_cart_button_remove_after_click')) {
							$spinner_id_prefix = (!$ilist_is_pot_active) ? 'ilist_button_' : 'pot_';
							$return .= '<script>
								function ilist_ajax_cart_button(id) {
									jQuery("#' . $spinner_id_prefix . '"+id).addClass("ilist-spinner");
									jQuery(".spinner_"+id).addClass("ilist-spinner-is-active");
									jQuery("#ilist_button_"+id).addClass("ilist-dnone");
									// If pot select
									jQuery("#ilist_pot_"+id).addClass("ilist-dnone");
									jQuery("#pot_"+id).addClass("ilist-dnone");
									setTimeout(function() {
										if (jQuery(".spinner_"+id).length > 0) {
											jQuery(".spinner_"+id).removeClass("ilist-spinner-is-active");
										}
									}, 2000);
								}
							</script>';
						}
						
					} else {
						// ----------------------------------------
						// Check if WooCommerce is installed
						// ----------------------------------------
						if (function_exists("is_account_page"))
							$return .= __( "No available product for this list", ILIST_ID_LANGUAGES );
						else
							$return .= __( "The WooCommerce Plugin is not installed", ILIST_ID_LANGUAGES );
						
					}
					
					// PHP Session Close
					// session_write_close();
					
					return $return;
				}
				
			break;
	
			case "creer-une-liste":
			case "modifier-une-liste":
			case "supprimer-une-liste":
				//---------------------------------------------
				// Check if form is to updated
				//---------------------------------------------
				if ($action == 'modifier-une-liste' && !$_POST) {
					// Get current user ID
					$ilist_current_user_id = get_current_user_id();
					// Get list to update
					$ilist_current_list_id = intval($_GET['lid']);
					// Request
					$ilist_list_user = $wpdb->get_row("SELECT * FROM " . ILIST_TBL_MAIN . " WHERE id = '$ilist_current_list_id' AND user_id = '$ilist_current_user_id'");
					// Instanciate Results
					$ilist_list_id     = $ilist_list_user->id;
					$ilist_user_id     = $ilist_list_user->user_id;
					$ilist_name        = $ilist_list_user->name;
					$ilist_description = $ilist_list_user->description;
					$ilist_image       = $ilist_list_user->image;
					$ilist_firstname   = $ilist_list_user->firstname;
					$ilist_lastname    = $ilist_list_user->lastname;
					$ilist_email       = $ilist_list_user->email;
					$ilist_password    = $ilist_list_user->password;
				}
				//---------------------------------------------
				// Check if form is to removed
				//---------------------------------------------
				if ($action == 'supprimer-une-liste' && !$_POST) {
					// Get current user ID
					$ilist_current_user_id = get_current_user_id();
					// Get list to update
					$ilist_current_list_id = intval($_GET['lid']);
					// Request
					$ilist_list_user = $wpdb->get_row("SELECT * FROM " . ILIST_TBL_MAIN . " WHERE id = '$ilist_current_list_id' AND user_id = '$ilist_current_user_id'");
					// Check if list is to this user
					if ($ilist_list_user) {
						// Initialize
						$message_success = [];
						$message_error   = [];
						// Delete the list
						$remove_list = $wpdb->query("DELETE FROM " . ILIST_TBL_MAIN . " WHERE id IN($ilist_current_list_id)");
						// Get all ID products from this list for stock management
						$pids = ilist_get_all_ids_for_stock($ilist_current_list_id, true);
						// Loop on all products
						foreach($pids as $pid) {
							// --------------------------------------------------------------------------
							// Check if management stock product AND management stock ILIST are activated
							// --------------------------------------------------------------------------
							$_product = wc_get_product( $pid );
							ilist_update_stock($_product, 'increase');
						}
						// Delete all products from list
						$remove_products = $wpdb->query("DELETE FROM " . ILIST_TBL_PRODUCT . " WHERE list_id IN($ilist_current_list_id) AND order_id == ''");
						// SUCCESS Message
						$message_success[] = __("Your list has been removed successfully", ILIST_ID_LANGUAGES) . '<br><a href="'.$ilist_page_used.'">' . __("See all my lists", ILIST_ID_LANGUAGES) . '</a>';
					}
					$ilist_list_id     = $ilist_list_user->id;
					$ilist_user_id     = $ilist_list_user->user_id;
					$ilist_name        = $ilist_list_user->name;
					$ilist_description = $ilist_list_user->description;
					$ilist_firstname   = $ilist_list_user->firstname;
					$ilist_lastname    = $ilist_list_user->lastname;
					$ilist_email       = $ilist_list_user->email;
					$ilist_password    = $ilist_list_user->password;
				}
				//---------------------------------------------
				// Check if post form
				//---------------------------------------------
				if ($_POST) {
					// Initialize
					$message_success = [];
					$message_error   = [];
					# Loop all $_POST and Stop XSS (Injection)
					foreach ($_POST as $key => $value) {
						${$key} = ilist_stopXSS($value);
					}
					// Check fields required
					if (empty($ilist_name))      $message_error[] = __( 'Name of the list is required', ILIST_ID_LANGUAGES );
					if (empty($ilist_firstname)) $message_error[] = __( 'Firstname is required', ILIST_ID_LANGUAGES );
					if (empty($ilist_lastname))  $message_error[] = __( 'Lastname is required', ILIST_ID_LANGUAGES );
					if (empty($ilist_email))     $message_error[] = __( "Email is required", ILIST_ID_LANGUAGES );
					if (!filter_var($ilist_email, FILTER_VALIDATE_EMAIL)) $message_error[] = __( "The email does not have a correct format", ILIST_ID_LANGUAGES );
					if ((isset($ilist_password) && trim($ilist_password) != '') && empty($ilist_password_confirm)) {
						$message_error[] = __('Enter password to confirm', ILIST_ID_LANGUAGES);
					}
					if ( (isset($ilist_password) && $ilist_password != '') && (isset($ilist_password_confirm) && $ilist_password_confirm != '') ) {
						if (trim($ilist_password_confirm) != trim($ilist_password)) {
							$message_error[] = __("Passwords do not match", ILIST_ID_LANGUAGES);
						}
					}
					// Check if errors
					if (!array_keys( $message_error, true )) {
						// PASSWORD Algorithm
						$algo_password = $Ilist->algo_password;
						// Checks are OK
						// CREATE OR EDIT
						if (isset($_POST['ilist_create'])) {
							// Require once if wp_handle_upload not exists
							if (!function_exists("wp_handle_upload")) {
								require_once(ABSPATH . 'wp-admin/includes/image.php');
								require_once(ABSPATH . 'wp-admin/includes/file.php');
								require_once(ABSPATH . 'wp-admin/includes/media.php');
							}
							// Initialize ILIST image
							$ilist_image = null;
							if ($_FILES) {
								// Initialize
								$file = $_FILES['ilist_image'];
								// wp_handle_upload
								$overrides   = [ 'test_form' => false ];
								$file_upload = wp_handle_upload($_FILES['ilist_image'], $overrides);
								/**
								 * Array (
								 *    [file] => /votre/chemin/de/fichier/wp-content/uploads/2021/10/fichier.jpg
								 *    [url]  => https://votre-domaine.com/wp-content/uploads/2021/10/fichier.jpg
								 *    [type] => image/jpeg
								 * )
								 */
								if ($_FILES['ilist_image']['size'] > 0 && isset( $file_upload['error'] )) {
									$message_error[] = 'ERROR: ' . $file_upload['error'];
								} else {
									$ilist_image = $file_upload['url'];
								}
							}
							// Check if new password
							$_password = NULL;
							if (isset($ilist_password) && $ilist_password != '') {
								$_password = password_hash( $ilist_password, $algo_password );
							} elseif (isset($ilist_password_hash) && $ilist_password_hash) {
								$_password = trim($ilist_password_hash);
							} else {
								$ilist_password = $ilist_password_confirm = $ilist_password_hash = "";
							}
							// Check if multiple insert
							$select_insert   = "SELECT * FROM " . ILIST_TBL_MAIN . "
												WHERE user_id = '".get_current_user_id()."'
												AND name = '".esc_html($ilist_name)."'
												AND description = '".esc_html($ilist_description)."'
												AND image = '".esc_html($ilist_image)."'
												AND firstname = '".esc_html($ilist_firstname)."'
												AND lastname = '".esc_html($ilist_lastname)."'
												AND email = '$ilist_email'";
							$multiple_insert = $wpdb->get_row( $select_insert );
							// ------------------------
							// BUG pierre-et-leon.fr
							// SQL Insert Repeat in DB
							// ------------------------
							if (null === $multiple_insert) {
								// Insert in DB
								$ilist_insert_date = date("Y-m-d H:i:s");
								$ilist_list_insert = $wpdb->insert( ILIST_TBL_MAIN,
									array( 'user_id'     => get_current_user_id()
										  ,'date'        => $ilist_insert_date
										  ,'name'        => esc_html($ilist_name)
										  ,'description' => esc_html($ilist_description)
										  ,'image'       => esc_html($ilist_image)
										  ,'firstname'   => esc_html($ilist_firstname)
										  ,'lastname'    => esc_html($ilist_lastname)
										  ,'email'       => $ilist_email
										  ,'keyaccess'   => ilist_rand_sha1(30)
										  ,'password'    => $_password
									)
								);
								if ($ilist_list_insert) {
									// Message success
									$ilist_all_lists_url = ( !is_account_page() ) ? $ilist_page_used : wc_get_endpoint_url('ilist');
									$message_success[] = __( 'Your new list has been successfully created', ILIST_ID_LANGUAGES) . '<br><a href="'.$ilist_all_lists_url.'">' . __('See all my lists', ILIST_ID_LANGUAGES) . '</a>';
									if (ilist_get_option( 'ilist_email_created_list_is_active' ) == '1') {
										// -------------------------------------------
										// -------------------------------------------
										// Send Email to ADMINISTRATOR(S)
										// -------------------------------------------
										// -------------------------------------------
										$ilist_customer = esc_html($ilist_firstname) . ' ' . esc_html($ilist_lastname) . ' - ' . $ilist_email;
										// --- Get options
										$ilist_emails_admin         = trim(ilist_get_option( 'ilist_email_admin' ));
										$ilist_email_admin_name     = trim(ilist_get_option( 'ilist_email_admin_name' ));
										$ilist_email_admin_response = trim(ilist_get_option( 'ilist_email_admin_response' ));
										$ilist_email_admin_subject  = trim(ilist_get_option( 'ilist_email_subject_alert_created_list' ));
										$ilist_email_admin_body     = trim(ilist_get_option( 'ilist_email_body_alert_created_list' ));
										// --- Replace Variables in email subject
										$ilist_email_admin_subject = @preg_replace("/{ILIST_NAME}/", esc_html($ilist_name), $ilist_email_admin_subject);
										// --- Replace Variables in email body
										$ilist_type = ilist_get_status_info(2, 'title'); // Internet purchase
										$ilist_email_admin_body = @preg_replace("/{ILIST_NAME}/", esc_html($ilist_name), $ilist_email_admin_body);
										$ilist_email_admin_body = @preg_replace("/{ILIST_CREATOR}/", esc_html($ilist_customer), $ilist_email_admin_body);
										$ilist_email_admin_body = @preg_replace("/{ILIST_DATE}/", ilistConvertDate($ilist_insert_date, 'FRT'), $ilist_email_admin_body);
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
										// Reset content-type to avoid conflicts -- https://core.trac.wordpress.org/ticket/23578
										remove_filter( 'wp_mail_content_type', 'ilist_set_html_mail_content_type' );
									}
									$ilist_name = $ilist_description = $ilist_image = $ilist_firstname = $ilist_lastname = $ilist_email = $ilist_customer = "";
								} else {
									// Message error
									$message_error[] = __( "Your new list was not created<br>Retry or contact an administrator.", ILIST_ID_LANGUAGES );
								}
							}
								
						} else {
							// Update in DB
							// Check if current_user_id is the owner of the list to update
							$ilist_list_id_to_update = intval($_POST['id']);
							$ilist_current_user_id   = get_current_user_id();
							$ilist_check_owner       = $wpdb->get_row("SELECT id FROM " . ILIST_TBL_MAIN . " WHERE id = '$ilist_list_id_to_update' AND user_id = '$ilist_current_user_id'");
							if ($ilist_check_owner) {
								// Initialize ilist_image
								$ilist_image = trim($_REQUEST['ilist_image']);
								// Check if remove image from list
								if (isset($ilist_remove_image) && $ilist_remove_image == '1') {
									$ilist_image = null;
								}
								if ($_FILES) {
									// Require once if wp_handle_upload not exists
									if (!function_exists("wp_handle_upload")) {
										require_once(ABSPATH . 'wp-admin/includes/image.php');
										require_once(ABSPATH . 'wp-admin/includes/file.php');
										require_once(ABSPATH . 'wp-admin/includes/media.php');
									}
									// Initialize
									$file = $_FILES['ilist_image'];
									// wp_handle_upload
									$overrides   = [ 'test_form' => false ];
									$file_upload = wp_handle_upload($_FILES['ilist_image'], $overrides);
									/**
									 * Array (
									 *    [file] => /votre/chemin/de/fichier/wp-content/uploads/2021/10/fichier.jpg
									 *    [url]  => https://votre-domaine.com/wp-content/uploads/2021/10/fichier.jpg
									 *    [type] => image/jpeg
									 * )
									 */
									if ($_FILES['ilist_image']['size'] > 0 && isset( $file_upload['error'] )) {
										$message_error[] = 'ERROR: ' . $file_upload['error'];
									} else {
										$ilist_image = $file_upload['url'];
									}
								}
								// Check password
								$_password = trim( $ilist_password_hash );
								if (isset($ilist_password) && $ilist_password != '') {
									$_password = password_hash( $ilist_password, $algo_password );
								} elseif (isset($ilist_password_remove)) {
									$_password = NULL;
								}
								// Update is ok to start
								$ilist_list_update = $wpdb->update( ILIST_TBL_MAIN,
																array('name'        => esc_html($ilist_name)
																	 ,'description' => esc_html($ilist_description)
																	 ,'image'       => esc_html($ilist_image)
																	 ,'firstname'   => esc_html($ilist_firstname)
																	 ,'lastname'    => esc_html($ilist_lastname)
																	 ,'email'       => $ilist_email
																	 ,'password'    => $_password
																),
																array('id' => $ilist_list_id_to_update),
																array('%s', '%s', '%s', '%s', '%s', '%s', '%s'),
																array('%d')
														);
								// Check result
								if ( false === $ilist_list_update ) {
									// Message error
									$message_error[] = __( "Your list was not updated<br>Retry or contact an administrator.", ILIST_ID_LANGUAGES );
								} else {
									// Message success
									$ilist_all_lists_url = ( !is_account_page() ) ? $ilist_page_used : wc_get_endpoint_url('ilist');
									$message_success[] = __( 'Your list was updated successfully', ILIST_ID_LANGUAGES) . '<br><a href="'.$ilist_all_lists_url.'">' . __('See all my lists', ILIST_ID_LANGUAGES) . '</a>';
									$ilist_name = $ilist_description = $ilist_image = $ilist_firstname = $ilist_lastname = $ilist_email = $ilist_list_id = "";
								}
								
							} else {
								// Message error
								$message_error[] = __( "You can not modify this list because you are not the creator", ILIST_ID_LANGUAGES );
							}
						}
						
					}
					
				}
				
				// --- Templates path
				$action_default = 'create';
				$ilist_template = ILIST_PATH . 'templates' . DIRECTORY_SEPARATOR . ILIST_ID . '-template-' . $action_default . '.php';
				$ilist_general .= include_once($ilist_template);
				
			break;
	
		}
	}
}


// --------------------------------------------------------------
// --------------------------------------------------------------
// --------------------------------------------------------------
add_action( 'wp_ajax_ilist_live_search', 'ilist_live_search' );
add_action( 'wp_ajax_nopriv_ilist_live_search', 'ilist_live_search' );
if (!function_exists("ilist_live_search")) {
	function ilist_live_search() {
		global $wpdb, $ilist_show_tabs;
		
		if (isset($_POST['keysearch'])) {
			$ilist_selectize_limit     = (ilist_get_option('ilist_selectize_limit')) ? ilist_get_option( 'ilist_selectize_limit' ) : 10;
			$ilist_selectize_trunc     = (ilist_get_option('ilist_selectize_truncate')) ? ilist_get_option('ilist_selectize_truncate') : 50;
			$ilist_db_post             = $wpdb->prefix . "posts";
			$is_pot_active             = ilist_get_option( 'ilist_pot_is_active' );
			$user_can_choose_pot       = ilist_get_option( 'ilist_pot_user_choice' );
			$_participation_product_id = ilist_get_option( 'ilist_id_participation_product' );
			// --- Motif de recherche
			// esc_like() neutralise d'abord les jokers saisis par l'utilisateur
			// (%, _), PUIS les espaces sont remplacés par des jokers voulus :
			// une recherche "robe bleue" trouve "robe en coton bleue".
			$ilist_search_datas = wp_unslash( $_POST['keysearch'] );
			$ilist_search_like  = '%' . str_replace( " ", "%", $wpdb->esc_like( $ilist_search_datas ) ) . '%';
			// --- Query live search
			$ilist_live_query   = $wpdb->prepare(
				"SELECT ID, post_title, post_content
									   FROM $ilist_db_post
									   WHERE post_title LIKE %s AND post_type = 'product' AND post_status = 'publish' AND ID != %d
									   ORDER BY post_title ASC
									   LIMIT %d"
				,$ilist_search_like
				,(int) $_participation_product_id
				,(int) $ilist_selectize_limit
			);
			// Get results
			$ilist_live_results = $wpdb->get_results( $ilist_live_query );
			
			if ($ilist_live_results) {
				foreach($ilist_live_results as $row) {
					if ($row) {
						// Product infos
						$_row_product         = wc_get_product( $row->ID );
						$_row_product_img     = wp_get_attachment_image_src( get_post_thumbnail_id( $row->ID ), 'thumbnail' );
						$_row_product_image   = ($_row_product_img) ? $_row_product_img[0] : ILIST_URL . 'images/image-default-ilist.png';
						$_row_product_content = ilist_truncate($row->post_content, $ilist_selectize_trunc, '...');
						
						switch( $_row_product->get_type() ) {
							case "variable":
							case "variation": // WooCommerce > 5.0
								// Get the post IDs of all the product variations
								$variation_ids = $_row_product->get_children();
								foreach( $variation_ids as $var_id ) {								
									// Get image variable product if exists
									$_variable_product_img   = wp_get_attachment_image_src( get_post_thumbnail_id( $var_id ), 'thumbnail' );
									$_variable_product_image = ($_variable_product_img) ? $_variable_product_img[0] : $_row_product_image;
									
									echo '<div class="ilist-select-infos">';
									echo '<span class="title">';
									echo '<img src="' . $_variable_product_image . '">';
									echo '<span class="name">' . strip_tags( get_the_title( $var_id ) ) . '</span>';
									echo '<br><span class="desc">' . strip_tags( $_row_product_content ) . '</span>';
									//echo '<br><span class="price">' . $_row_product_price . ' &euro;</span>';
									echo '<br><span class="show"><a href="'.get_permalink( $var_id ).'" target="_blank">'.__( "See this product", ILIST_ID_LANGUAGES ).'</a></span>';
									if ($is_pot_active) {
										if (!$user_can_choose_pot) {
											echo '<span class="toadd"><button name="productadd" value="' . $row->ID . '|' . $var_id . '" class="">'.__( "Add to my list", ILIST_ID_LANGUAGES ).'</button></span>';
										} else {
											echo '<span class="toadd"><button name="productadd" value="' . $row->ID . '|' . $var_id . '" data-pot="0" class="">'.__( "Add to my list", ILIST_ID_LANGUAGES ).'</button></span>';
											echo '<span class="toadd"><button name="productaddpot" value="' . $row->ID . '|' . $var_id . '" data-pot="1" class="">'.__( "Add to my list in participation mode", ILIST_ID_LANGUAGES ).'</button></span>';
										}
									} else {
										echo '<span class="toadd"><button name="productadd" value="' . $row->ID . '|' . $var_id . '" class="">'.__( "Add to my list", ILIST_ID_LANGUAGES ).'</button></span>';
									}
									echo '</span>';
									echo '</div>';
								}
							break;
							case "grouped":
							case "external":
							default:
								echo '<div class="ilist-select-infos">';
								echo '<span class="title">';
								echo '<img src="' . $_row_product_image . '">';
								echo '<span class="name">' . strip_tags($row->post_title) . '</span>';
								echo '<br><span class="desc">' . strip_tags($_row_product_content) . '</span>';
								//echo '<br><span class="price">' . $_row_product_price . ' &euro;</span>';
								echo '<br><span class="show"><a href="'.get_permalink( $row->ID ).'" target="_blank">'.__( "See this product", ILIST_ID_LANGUAGES ).'</a></span>';
								if ($is_pot_active) {
									if (!$user_can_choose_pot) {
										echo '<span class="toadd"><button name="productadd" value="'.$row->ID.'" class="">'.__( "Add to my list", ILIST_ID_LANGUAGES ).'</button></span>';
									} else {
										echo '<span class="toadd"><button name="productadd" value="' . $row->ID . '" data-pot="0" class="">'.__( "Add to my list", ILIST_ID_LANGUAGES ).'</button></span>';
										echo '<span class="toadd"><button name="productaddpot" value="'.$row->ID.'" data-pot="1" class="">'.__( "Add to my list in participation mode", ILIST_ID_LANGUAGES ).'</button></span>';
									}
								} else {
									echo '<span class="toadd"><button name="productadd" value="'.$row->ID.'" class="">'.__( "Add to my list", ILIST_ID_LANGUAGES ).'</button></span>';
								}
								echo '</span>';
								echo '</div>';
							break;
						}
	
					} else {
						echo __( "No product found", ILIST_ID_LANGUAGES );
					}
				}
			} else {
				echo __( "No product found", ILIST_ID_LANGUAGES );
			}
		}
		die();
	}
}
// --------------------------------------------------------------
// --------------------------------------------------------------
// --------------------------------------------------------------
/**
 * Add comment "ilist" field to the checkout
 */
add_action( 'woocommerce_after_order_notes', 'ilist_custom_checkout_field' );
if (!function_exists("ilist_custom_checkout_field")) {
	function ilist_custom_checkout_field( $checkout ) {
		
		$session_ilist_customer_products = ilist_get_session('ilist_customer_products');
		$ilist_hide_comment = ilist_get_option( 'ilist_hide_comment' );
		$ilist_products     = ( isset($session_ilist_customer_products) && is_array($session_ilist_customer_products) && array_keys( $session_ilist_customer_products, true ) ) ? true : false;
		
		// Check if products from list in cart AND if option hide comment is activated
		if (!$ilist_hide_comment || $ilist_products) {
			echo '<div id="ilist_custom_checkout_field"><h3>' . ILIST_DEFAULT_NAME . '</h3>';
			woocommerce_form_field( 'ilist_comment_checkout', array(
				'type'          => 'textarea',
				'class'         => array('ilist-field-comment-class form-row-wide'),
				'label'         => __('Leave a comment if you order for a list', ILIST_ID_LANGUAGES ),
				'placeholder'   => __('Your message', ILIST_ID_LANGUAGES ),
				), $checkout->get_value( 'ilist_comment_checkout' ));
		
			echo '</div>';
		}
	}
}
	
/**
 * Update the order meta with field value
 */
add_action( 'woocommerce_checkout_update_order_meta', 'ilist_custom_checkout_field_update_order_meta' );
if (!function_exists("ilist_custom_checkout_field_update_order_meta")) {
	function ilist_custom_checkout_field_update_order_meta( $order_id ) {
		if ( ! empty( $_POST['ilist_comment_checkout'] ) ) {
			ilist_update_order_meta( $order_id, 'order-meta-ilist', sanitize_text_field( wp_unslash( $_POST['ilist_comment_checkout'] ) ) );
		}
	}
}
// --------------------------------------------------------------
// --------------------------------------------------------------
// --------------------------------------------------------------
add_action( 'woocommerce_after_add_to_cart_form', 'ilist_extra_buttons_on_product_page', 1 );
//add_action( 'woocommerce_before_add_to_cart_form', 'ilist_extra_buttons_on_product_page', 1 );
if (!function_exists("ilist_extra_buttons_on_product_page")) {
	function ilist_extra_buttons_on_product_page() {
		global $wpdb, $product, $Ilist;
	
		// Bouton creer une liste (offline)
		if (!is_user_logged_in()) {
			// --- Get options
			$ilist_url_complete = ilist_get_option( 'ilist_url_complete' );
			$ilist_disable_list_creation_visitors = ilist_get_option( 'ilist_disable_list_creation_visitors' );
			if (!$ilist_disable_list_creation_visitors) {
				echo '<a id="ilist_create_new_ilist" href="'.$ilist_url_complete.'" class="button alt">';
				echo  __("Create a list", ILIST_ID_LANGUAGES);
				echo '</a>';
			}
		}
		
		// Get lists of owner if logged in
		$ilist_show_button             = (is_user_logged_in()) ? ilist_get_lists(get_current_user_id()) : false;
		//$ilist_product_add_method = (is_user_logged_in()) ? ilist_get_option( 'ilist_product_add_method') : 'post'; // !!!!! OBSOLETE !!!!!
		$ilist_product_add_method      = 'post';
		$is_pot_active                 = ilist_get_option( 'ilist_pot_is_active' );
		$user_can_choose_pot           = ilist_get_option( 'ilist_pot_user_choice' );
		$_participation_min_price      = ilist_get_option( 'ilist_pot_min_price_product' );
		$ilist_hide_add_to_my_list_xxx = ilist_get_option( 'ilist_hide_add_to_my_list_xxx' );
		$ilist_class_add_to_mylist     = ilist_get_option('ilist_add_to_my_list_button_class');
		$_class_add_to_mylist          = (isset($ilist_class_add_to_mylist) && $ilist_class_add_to_mylist == '1') ? '' : 'single_add_to_cart_button ';
		if ($ilist_show_button && !$ilist_hide_add_to_my_list_xxx) {
			foreach($ilist_show_button as $ilist_button) {
				if (strtolower($ilist_product_add_method) == 'get') {
					// Method href GET (arguments passed thru url) -- !!!!! OBSOLETE !!!!!
					echo '<div class="single_add_to_cart_button_div" id="ilist_add_to_my_ilist_'.$ilist_button->id.'">';
						echo '<a class="'.$_class_add_to_mylist.'button alt ilist_add_to_my_ilist" href="'.get_the_permalink().'?addtoilist=productadd&listid='.$ilist_button->id.'">' . __('Add to my list ', ILIST_ID_LANGUAGES ) . $ilist_button->name . '</a>';
					echo '</div>';
				} else {
					// Method POST (form)
					echo '
					<form id="ilist-product-add-'.$ilist_button->id.'" onsubmit="return ilist_check_product('.$ilist_button->id.');" class="cart ilist-cart-button" method="post" action="' . ilist_current_url() . '#ilist-product-add-'.$ilist_button->id.'" enctype="multipart/form-data">';
					if ($is_pot_active) {
						echo '<select class="ilist_select_add_to_my_ilist" id="ilistselect'.$ilist_button->id.'" data-btid="'.$ilist_button->id.'">';
						echo '<option value="">' . __('Add to my list ', ILIST_ID_LANGUAGES ) . $ilist_button->name . '</option>';
						if ($user_can_choose_pot == '1') echo '<option value="yes">&rAarr; ' . __('with participation mode', ILIST_ID_LANGUAGES ) . '</option>';
						echo '<option value="no">&rAarr; ' . __('without participation mode', ILIST_ID_LANGUAGES ) . '</option>';
						echo '</select>';
					} else {
						echo '<input type="submit" id="ilist_add_to_my_ilist_'.$ilist_button->id.'" class="'.$_class_add_to_mylist.'button alt ilist_add_to_my_ilist" value="' . __('Add to my list ', ILIST_ID_LANGUAGES ) . $ilist_button->name . '">';
					}					
					echo '
						<input type="hidden" id="variationid'.$ilist_button->id.'" name="variationid" value="">
						<input type="hidden" name="productid" value="'.intval($product->get_id()).'">
						<input type="hidden" name="listid" value="'.$ilist_button->id.'">
						<input type="hidden" id="ispot'.$ilist_button->id.'" name="ispot" value="0">
						<input type="hidden" id="minprice'.$ilist_button->id.'" name="minprice" value="'.$_participation_min_price.'">
						<input type="hidden" id="price'.$ilist_button->id.'" name="price" value="'.$product->get_price().'">
						<input type="hidden" name="addtoilist" value="productadd">';
					echo '</form>';
				}
			}
			// Scripts
			echo '<script>
				jQuery( document ).ready(function() {
					jQuery("select.ilist_select_add_to_my_ilist").on("change", function() {
						var valueSelected  = this.value;
						if (valueSelected == "") return false;
						// Form btid
						var btid = jQuery(this).attr("data-btid");
						// Choice add method
						var choice = this.value;
						// Check if variation choice is made
						var variation_id = jQuery(".variation_id").val();
						if (variation_id < 1 || variation_id == "") {
							// Add an Option
							alert("' . __("You must choose an option to add this product to your list", ILIST_ID_LANGUAGES) . '");
							jQuery(this).val("").trigger("chosen:updated"); // reset select
							return false;
						} else if (variation_id > 0) {
							// Populate variation in forms
							jQuery("#variation_id"+btid).val(variation_id);
						}
						// Confirm action
						var confirmodetext = (valueSelected == "yes") ? "'.__("Would you like to add this product to your list in Crowdfunding mode?", ILIST_ID_LANGUAGES).'" : "'.__("Would you like to add this product to your list without Crowdfunding mode?", ILIST_ID_LANGUAGES).'";
						if (confirm(confirmodetext) === false) {
							jQuery(this).val("").trigger("chosen:updated"); // reset select
							return false;
						}
						// Product price / Min price to add product if is pot
						if (choice === "yes") { // Is pot
							var minprice = parseFloat( jQuery("#minprice"+btid).val() );
							var price    = 0;
							// If variation, change price
							if (variation_id > 0) {
								jQuery.ajax({
									url: ilist_ajax_script.ajaxurl,
									type: "POST",
									data: {
										//_ajax_nonce: ilist_ajax_script.nonce, // nonce
										action: \'ilist_ajax_product_infos\',
										switch_a: \'get_variation_price\',
										variation_id: variation_id
									},
									datatype: \'json\'
								})
								.done(function (data) {
									jQuery("#price"+btid).val(data);
									price = parseFloat( jQuery("#price"+btid).val() );
									if (price < minprice ) {
										alert("' . html_entity_decode( sprintf( __("The minimum to add a product in participation mode is %s", ILIST_ID_LANGUAGES), $Ilist->get_woocommerce_price_format( $_participation_min_price ) ) ) . ' - votre choix: "+price);
										return false;
									}
								})
								.fail(function (jqXHR, textStatus, errorThrown) {
									jQuery(this).val("").trigger("chosen:updated"); // reset select
									//alert("Error: "+errorThrown);
									return false;
								});
							} else {
								price = parseFloat( jQuery("#price"+btid).val() );
								console.log("price jquery: "+price);
								if (price < minprice ) {
									alert("' . html_entity_decode( sprintf( __("The minimum to add a product in participation mode is %s", ILIST_ID_LANGUAGES), $Ilist->get_woocommerce_price_format( $_participation_min_price ) ) ) . ' - votre choix: "+price);
									jQuery(this).val("").trigger("chosen:updated"); // reset select
									return false;
								}
							}
						}
						// Reset select
						jQuery(this).val("").trigger("chosen:updated"); // reset select
						// Confirm choice and submit form
						if (choice === "yes") {
							jQuery("#ispot"+btid).val("1");
						} else {
							jQuery("#ispot"+btid).val("0");
						}
						// Submit form
						document.forms["ilist-product-add-"+btid].submit();
					});
				});
			</script>';
		}
		if ( (isset($_POST['addtoilist']) && $_POST['addtoilist'] == 'productadd') || (isset($_GET['addtoilist']) && $_GET['addtoilist'] == 'productadd') ) {
			// Initialize
			$ilist_product_id     = (isset($_POST['addtoilist'])) ? intval($_POST['productid']) : intval($_GET['productid']);
			$ilist_variation_id   = (isset($_POST['addtoilist'])) ? ( ( isset($_POST['variationid']) && $_POST['variationid'] != '' ) ? intval($_POST['variationid']) : NULL ) : ( ( isset($_GET['variationid']) && $_GET['variationid'] != '' ) ? intval($_GET['variationid']) : NULL );
			$ilist_list_id        = (isset($_POST['addtoilist'])) ? intval($_POST['listid']) : intval($_GET['listid']);
			$ilist_product_name   = (isset($_POST['addtoilist']) && $ilist_variation_id) ? esc_html(get_the_title( $ilist_variation_id )) : esc_html(get_the_title( $ilist_product_id ));
			$ilist_is_pot_product = (isset($_POST['addtoilist'])) ? intval($_POST['ispot']) : intval($_POST['ispot']);
			// Insert DB
			$insert = $wpdb->insert( 
				ILIST_TBL_PRODUCT, 
				array( 
					 'list_id'      => $ilist_list_id
					,'product_id'   => $ilist_product_id
					,'variation_id' => $ilist_variation_id
					,'product_name' => $ilist_product_name
					,'is_pot'       => $ilist_is_pot_product
				), 
				array( '%d', '%d', '%d', '%s', '%d'	)
			);
			//echo '<div id="ilist-product-add">';
			if (!$insert) {
				wc_print_notice( __("The product was not added", ILIST_ID_LANGUAGES), 'error');
			} else {
				wc_print_notice( __("The product has been added to your list successfully", ILIST_ID_LANGUAGES), 'success' );
				// --------------------------------------------------------------------------
				// Check if management stock product AND management stock ILIST are activated
				// --------------------------------------------------------------------------
				// --- Get option infos (Stock management)
				$_product = ($ilist_variation_id) ? wc_get_product( $ilist_variation_id ) : wc_get_product( $ilist_product_id );
				ilist_update_stock($_product, 'decrease');
			}
			//echo '</div>';
		}
	}
}

add_filter( 'woocommerce_cart_item_quantity', 'wc_cart_item_quantity', 10, 3 );
function wc_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
	$session_ilist_customer_products = ilist_get_session('ilist_customer_products');
    if ( is_cart() && isset($session_ilist_customer_products) ) {
		foreach($session_ilist_customer_products as $row) {
			$_product_id = (isset($row['variation_id']) && $row['variation_id'] > 0) ? $row['variation_id'] : $row['product_id'];
			$_cart_item_id = $cart_item['data']->get_id();
			if ($_product_id == $_cart_item_id) {
				$new_product_quantity = sprintf( '%2$s <input type="hidden" name="cart[%1$s][qty]" value="%2$s" />', $cart_item_key, $cart_item['quantity'] );
				return $new_product_quantity;
			} else {
			    return $product_quantity;
			}
		}
    }
    return $product_quantity;
}

/**
 * Enable backorders on all products from list with stock 0 (Process: Stock 0 => 1 => 0)
 * Help from Wordpress Stackexchange:
 * Woocommerce: Is it possible to overide the settings for allowing to purchase out of stock products
 * https://wordpress.stackexchange.com/questions/334083/woocommerce-is-it-possible-to-overide-the-settings-for-allowing-to-purchase-out
 * -------------------------------------------------------------------------------------
 * WooCommerce API (abstract): https://woocommerce.wp-a2z.org/oik_api/wc_productbackorders_allowed/
 */
add_filter( 'woocommerce_product_get_backorders', 'ilist_filter_get_backorders_callback_stock_0', 10, 2 );
add_filter( 'woocommerce_product_variation_get_backorders', 'ilist_filter_get_backorders_callback_stock_0', 10, 2 );
if (!function_exists("ilist_filter_get_backorders_callback_stock_0")) {
	function ilist_filter_get_backorders_callback_stock_0( $backorders_status, $product ) {
		$session_ilist_customer_products = ilist_get_session('ilist_customer_products');
		// Check if products in cart come from ILIST
		if (isset($session_ilist_customer_products)) {
			foreach($session_ilist_customer_products as $row) {
				// If stock management 0 => 1 => 0
				if (isset($row['stock01']) && $row['stock01'] === true) {
					return 'yes'; // Enable without notifications ( yes | notify )
				}
			}
		}
		return $backorders_status;
	}
}

/**
 * Action after adding product to cart (Process: Stock 0 => 1 => 0)
 */
add_action( 'woocommerce_add_to_cart', 'ilist_stock_0_1_after_add_to_cart', 10, 6 );
if (!function_exists("ilist_stock_0_1_after_add_to_cart")) {
	function ilist_stock_0_1_after_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
		if (did_action( 'woocommerce_add_to_cart' ) === 2) return;
		// Initialize
		$session_ilist_customer_products = ilist_get_session('ilist_customer_products');
		// Check if products in cart come from ILIST
		if (isset($session_ilist_customer_products)) {
			foreach($session_ilist_customer_products as $row) {
				// If stock management 0 => 1 => 0
				if (isset($row['stock01']) && $row['stock01'] === true) {
					$_product_id = (isset($variation_id) && $variation_id > 0) ? $variation_id : $product_id;
					$_product = wc_get_product( $_product_id );
					wc_update_product_stock($_product, 0);
				}
				if (ilist_get_option('ilist_debug_log')) {
					ilist_log('===================================');
					ilist_log('Stock management: Product ID => ' . $_product_id . ' (After add to cart)');
					ilist_log('Stock management: 1 => 0');
				}
			}
		}
	}
}

/**
 * WooCommerce Filter - When adding product to cart
 */
add_filter( 'woocommerce_add_to_cart_validation', 'ilist_validate_add_cart_item', 10, 5 );
if (!function_exists("ilist_validate_add_cart_item")) {
	function ilist_validate_add_cart_item( $passed, $product_id, $quantity, $variation_id = '', $variations = '' ) {
		global $wpdb;		
		// ---------------------------------
		// Initialize
		// ---------------------------------
		$ilist_keyaccess_get = $ilist_product_bpid = $ilist_product_list_id = $ilist_variation_id = $ilist_product_participation_id = "";
		// ---------------------------------
		// Get list infos
		// ---------------------------------
		if (isset($_REQUEST['k']) || isset($_REQUEST['bpid'])) {
			$ilist_keyaccess_get = ilist_stopXSS($_REQUEST['k']);
			$ilist_query_list    = "SELECT id FROM " . ILIST_TBL_MAIN . " WHERE keyaccess = '$ilist_keyaccess_get'";
			$ilist_list          = $wpdb->get_row( $ilist_query_list );
			// ---------------------------------
			// Get product ID (ilist_product)
			// ---------------------------------
			$ilist_product_bpid        = intval($_REQUEST['bpid']);
			$ilist_variation_id        = intval($_REQUEST['variation_id']);
			$ilist_product_real_id     = intval($_REQUEST['real_id']);
			$ilist_participation_total = intval($_REQUEST['total']);
			$ilist_product_list_id     = $ilist_list->id;
			// ---------------------------------		
			// Get product participation ID
			// ---------------------------------
			$ilist_product_participation_id = intval($_REQUEST['product_participation_id']);
			// ---------------------------------
			// Check if the purchase is a participation
			// ---------------------------------
			$ilist_participation = (ilist_get_option('ilist_id_participation_product') == $ilist_variation_id) ? $ilist_product_real_id : "0";
			// ---------------------------------
			$new_product = [
				 'bpid'            => "$ilist_product_bpid"
				,'product_id'      => "$product_id"
				,'variation_id'    => "$ilist_variation_id"
				,'real_id'         => "$ilist_product_real_id"
				,'list_id'         => "$ilist_product_list_id"
				,'participation'   => "$ilist_participation"
				,'quantity'        => "$quantity"
				,'total'           => "$ilist_participation_total"
			];
			// ---------------------------------
			// Check if stock is activated (Process: Stock 0 => 1 => 0)
			// ---------------------------------
			if (ilist_get_option( 'ilist_stock_management' )) {
				// --- Initialize			
				$_product_id            = (isset($variation_id) && $variation_id > 0) ? $variation_id : $product_id;
				$_product               = wc_get_product( $_product_id );
				$_product_initial_stock = $_product->get_stock_quantity();
				
				if ($_product_initial_stock == 0) {
					$_product_ajust_stock = wc_update_product_stock($_product, 1); // Change stock to 1
					$new_product = array_merge($new_product, ['stock01' => true]);
				}
				
				if (ilist_get_option('ilist_debug_log')) {
					ilist_log('===================================');
					ilist_log('Stock management: Product ID => ' . $product_id . ' (Adding to cart)');
					ilist_log('Stock management: ' . $_product_initial_stock . ' => ' . $_product_ajust_stock);
				}
				
				//$_product_ajust_stock = wc_update_product_stock($_product, 0);
			}
			// ---------------------------------
			// Initialize ILIST SESSION
			// ---------------------------------
			$session_ilist_customer_products = ilist_get_session('ilist_customer_products');
			if (!isset($session_ilist_customer_products)) ilist_set_session( 'ilist_customer_products', array() );
			// ---------------------------------
			// Push new product in SESSION Infos
			// ---------------------------------
			if ($ilist_product_bpid > 0) array_push($session_ilist_customer_products, $new_product);
			// ---------------------------------
		}
	
		return $passed;
	}
}

/**
 * Get order_id before payment
 * And insert Infos in each DB Product Ilist
 */
if (!function_exists("ilist_wc_checkout_order")) {
	function ilist_wc_checkout_order( $order_id, $posted_data, $order ) {
		global $wpdb;
		// Initialize
		$session_ilist_customer_products = ilist_get_session('ilist_customer_products');
		// Option log (if activated)
		if (ilist_get_option('ilist_debug_log')) {
			ilist_log_start();
			ilist_log('Order id (WooCommerce): '.$order_id);
			ilist_log($session_ilist_customer_products);
		}
		
		if ( ! $order_id ) {
		  return;
		}
		// Get order infos   
		$order = new WC_Order( $order_id );
		// Get option infos (Stock management)
		$ilist_stock = ilist_get_option( 'ilist_stock_management' );
		// Update DB product
		if (isset($session_ilist_customer_products) && count($session_ilist_customer_products) > 0) {
			
			// Option log (if activated)
			if (ilist_get_option('ilist_debug_log')) {
				$count = (is_array($session_ilist_customer_products)) ? count($session_ilist_customer_products) : false;
				ilist_log('Products counter / Produits commandes: '.$count);
			}
	
			foreach($session_ilist_customer_products as $ilist_buy) {
				$ilist_ID                  = $ilist_buy['bpid'];
				$ilist_product_id          = $ilist_buy['product_id'];
				$ilist_variation_id        = $ilist_buy['variation_id'];
				$ilist_real_id             = $ilist_buy['real_id'];
				$ilist_list_id             = $ilist_buy['list_id'];
				$ilist_participation       = $ilist_buy['participation'];
				$ilist_quantity            = $ilist_buy['quantity'];
				$ilist_participation_total = $ilist_buy['total'];
				$ilist_display_name        = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
				// -------------------------------------------
				if (ilist_get_option('ilist_debug_log')) ilist_log('Participation: '.$ilist_buy['participation']);
				// -------------------------------------------
				// Check if participation product
				// -------------------------------------------
				if ($ilist_participation == "0") {
					// -------------------------------------------
					// Purchase the product at his price
					// -------------------------------------------
					$ilist_list_update = $wpdb->update(
						ILIST_TBL_PRODUCT,
						array('order_id' => $order_id
							 ,'date'     => date( 'Y-m-d H:i:s', strtotime( $order->get_date_created() ) )
							 ,'customer' => $ilist_display_name
							 //,'status'   => '2'
						),
						array("id" => $ilist_ID, "list_id" => $ilist_list_id),
						array('%d', '%s', '%s'),
						array('%d', '%d')
					);
					// --------------------------------------------------------------------------
					// Check if management stock product AND management stock ILIST are activated
					// --------------------------------------------------------------------------
					$_product = wc_get_product( $ilist_variation_id );
					ilist_update_stock($_product, 'increase');
					
				} else {
					// -------------------------------------------
					// Purchase product with participation
					// -------------------------------------------
					$product_variation       = wc_get_product( $ilist_participation );
					$product_participation   = wc_get_product( $ilist_variation_id );
					$_price                  = $ilist_buy['quantity'] * $product_participation->get_price();
					$ilist_sql_participation = $wpdb->insert(
						ILIST_TBL_CAGNOTTE,
						array('date'             => date( 'Y-m-d H:i:s', strtotime( $order->get_date_created() ) )
							 ,'order_id'         => $order_id
							 ,'list_id'          => $ilist_list_id
							 ,'list_product_id'  => $ilist_ID
							 ,'product_id'       => $ilist_participation
							 ,'variation_id'     => $ilist_participation
							 ,'participation_id' => $ilist_variation_id
							 ,'product_name'     => $product_variation->get_name()
							 ,'customer'         => $ilist_display_name
							 ,'participation'    => $_price
							 //,'status'   => '2'
						),
						array('%s', '%d', '%d', '%d', '%d', '%d', '%d', '%s', '%s', '%s')
					);
					// Check if is the first participation
					if ($ilist_participation_total > 0) {
						// --------------------------------------------------------------------------
						// No need to manage stock (second or more participation)
						// --------------------------------------------------------------------------
					} else {
						// --------------------------------------------------------------------------
						// Stock management product AND management stock ILIST are activated
						// --------------------------------------------------------------------------
						ilist_update_stock($product_participation, 'increase');
					}
				}
				// -------------------------
				// Option log (if activated)
				// -------------------------
				if (ilist_get_option('ilist_debug_log')) {
					ilist_log('bpid: '.$ilist_ID);
					ilist_log('product_id: '.$ilist_product_id);
					ilist_log('variation_id: '.$ilist_variation_id);
					ilist_log('list_id: '.$ilist_list_id);
					ilist_log('Update: '."id => $ilist_ID, list_id => $ilist_list_id");
					ilist_log('Customer / Client: '.$ilist_display_name);
					// Get Order Dates
					// $order->get_date_created();
					// $order->get_date_modified();
					// $order->get_date_completed();
					// $order->get_date_paid();
					ilist_log('Order date / Date de commande: '.date( 'Y-m-d H:i:s', strtotime( $order->get_date_created() ) ));
					ilist_log('Payment method / Paiement choisi: '.$order->get_payment_method().' - '.$order->get_payment_method_title());
				}
			}
		}
		
		// -------------------------------------------
		// Reset SESSION Infos
		// -------------------------------------------
		ilist_unset_session( 'ilist_customer_products' );
		
	}
}

// ------------------------------------------------------
// ------------------------------------------------------
// ----------------- BACKORDER ALLOWED? -----------------
// ------------------------------------------------------
// ------------------------------------------------------

/** Returns whether or not the product can be backordered
 *
 * @param 	$backorders_allowed
 * @param 	$product_id
 * @param 	$product
 * @return bool
*/
add_filter( 'woocommerce_product_backorders_allowed', 'ilist_products_backorders_allowed', 10, 3 );
if (!function_exists("ilist_products_backorders_allowed")) {
	function ilist_products_backorders_allowed( $backorder_allowed, $product_id, $product ) {
		// Initialize
		$ilist_stock_backorder = ilist_get_option( 'ilist_stock_backorder' );
		// Check if product can be backordered by option ILIST_STOCK_BACKORDER
		if ($ilist_stock_backorder) {
			ilist_log('===================================');
			ilist_log('Backorder allowed: yes - Product ID => ' . $product_id . ' (Filter)');
			return true; // Allow using the ILIST option
		}
		return $backorder_allowed; // Return backend choice
	}
}
/**
 * Return the stock status. Should be 'onbackorder' for backorders
 *
 * wc_get_product_stock_status_options() – Get stock status options
 * instock     => In stock
 * outofstock  => Out of stock
 * onbackorder => On backorder
 *
 * @param $stock_status
 * @param $product
 * @return string
*/
add_filter( 'woocommerce_product_get_stock_status', 'ilist_product_get_stock_status', 10, 2 );
if (!function_exists("ilist_product_get_stock_status")) {
	function ilist_product_get_stock_status( $stock_status, $product ) {
		// Initialize
		$ilist_stock_backorder = ilist_get_option( 'ilist_stock_backorder' );
		// Product infos
		$product_id = $product->get_id();
		
		if ($ilist_stock_backorder) {
			ilist_log('Backorder allowed (stock status changed): onbackorder - Product ID => ' . $product_id);
			return 'onbackorder'; // Allow using the ILIST option
		}
		
		return $stock_status; // Return backend choice
	}
}

?>