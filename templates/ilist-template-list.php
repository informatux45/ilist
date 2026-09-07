<?php
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// Blocking direct access to plugin      -=
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
defined('ABSPATH') or die('Are you crazy!');

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
// Initialize $_SESSION
// ------------------------------------
// --- ilist customer infos
if (!isset($_SESSION['ilist_customer_products'])) $_SESSION['ilist_customer_products'] = array();
// --- List protected by password
if (!isset($_SESSION['ilist_password_protected'])) $_SESSION['ilist_password_protected'] = "";
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
	// Check Password
	// ----------------------------------------
	// 1. Is there a password on the list? 
	// 2. Is password session empty ?
	// 3. If password, Check
	// 4. Does the list belong to the logged creator?
	// ----------------------------------------
	if ( $Ilist->get_list_password($current_list_keyaccess)
		&& ( $_SESSION['ilist_password_protected'] == "" || !password_verify( trim($_SESSION['ilist_password_protected']), $ilist_password ) )
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
				$_SESSION['ilist_password_protected'] = trim($_POST['password_list']);
			} else {
				$return .= '<strong style="color: red;">' . __("Incorrect password!", ILIST_ID_LANGUAGES) . '</strong><br>';
			}
			
		}
		// --- Check if Password is ok
		if (!isset($_SESSION['ilist_password_protected']) || $_SESSION['ilist_password_protected'] == '' || !password_verify( trim($_SESSION['ilist_password_protected']), $ilist_password )) {
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

return $return;