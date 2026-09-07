<?php
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// Blocking direct access to plugin      -=
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
defined('ABSPATH') or die('Are you crazy!');

/**
 * Shortcode SEARCH from list (Ilist)
 * This method return HTML to display in FRONT page with Shortcode
 *
 * @return HTML
 */
function ilist_shortcode_search($param, $content) {
	global $wpdb;
	// --- Initialize
	$ILIST_REQUEST_PROTOCOL = (ilist_is_secure()) ? 'https' : 'http';
	$ilist_url_current      = "$ILIST_REQUEST_PROTOCOL://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$ilist_page_used        = str_replace("//", "/", parse_url($ilist_url_current, PHP_URL_PATH) . '/');
	// --- Actions
	$action = (isset($_GET['a'])) ? trim($_GET['a']) : '';
	// --- Switch
	switch($action) {
		default: // Search page Ilist
			// --- Templates path
			$action_default            = 'search';
			$ilist_template            = ILIST_PATH . 'templates' . DIRECTORY_SEPARATOR . ILIST_ID . '-template-' . $action_default . '.php';
			$ilist_url_complete        = ilist_get_option( 'ilist_url_complete' );
			$ilist_search_url_complete = ilist_get_option( 'ilist_search_url_complete' );
			$ilist_shorcode_bytpl      = ilist_get_option( 'ilist_shorcode_bytpl' );
			// Search in progress
			if ($_POST && isset($_POST['search-list']) && trim($_POST['search-list']) != '') {
				global $wpdb;
				// --- Initialize search options
				$search_where  = '';
				$search_params = array();
				$operand       = '';
				$search_text   = trim( wp_unslash( $_POST['search-list'] ) );
				// Motif pour les comparaisons LIKE : les caractères joker de
				// l'utilisateur (%, _) sont neutralisés par esc_like().
				$search_like   = '%' . $wpdb->esc_like( $search_text ) . '%';
				// --- Get search options
				$ilist_email_search     = trim(ilist_get_option( 'ilist_email_search' ));
				$ilist_name_search      = trim(ilist_get_option( 'ilist_name_search' ));
				$ilist_firstname_search = trim(ilist_get_option( 'ilist_firstname_search' ));
				$ilist_lastname_search  = trim(ilist_get_option( 'ilist_lastname_search' ));
				// Check if there is an activated option
				if (!$ilist_email_search && !$ilist_name_search && !$ilist_firstname_search && !$ilist_lastname_search) {
					$search_where .= "email = %s";
					$search_params[] = $search_text;
				} else {
					if ($ilist_email_search) {
						$search_where .= "email = %s";
						$search_params[] = $search_text;
					}
					if ($ilist_name_search) {
						if ($ilist_email_search)
							$operand = " OR";
						$search_where .= "$operand name LIKE %s";
						$search_params[] = $search_like;
					}
					if ($ilist_firstname_search) {
						if ($ilist_email_search || $ilist_name_search)
							$operand = " OR";
						$search_where .= "$operand firstname LIKE %s";
						$search_params[] = $search_like;
					}
					if ($ilist_lastname_search) {
						if ($ilist_email_search || $ilist_name_search || $ilist_firstname_search)
							$operand = " OR";
						$search_where .= "$operand lastname LIKE %s";
						$search_params[] = $search_like;
					}
				}
				// Search list
				$ilist_show_tabs = ilist_get_lists_search(trim($search_where), $search_params);
				
			}
				
			// -------------------------------------
			// Share list
			// -------------------------------------
			$share_list = ilist_get_option( 'ilist_share_list' );

			// --- Check if show TPL or not
			if ($ilist_shorcode_bytpl) {
				$return_shortcode = include_once($ilist_template);
				return $return_shortcode;
			} else {
				// -------------------------------------
				// Initialize return
				// -------------------------------------
				$return = "";
				// -------------------------------------
				// Show search form with results (if)
				// -------------------------------------
				$return .= '<form id="ilist-search" action="#ilist-search" class="ilist-search-form" method="post">
					<p class="form-row form-row-wide">
						<label for="search-list">' . __( "Find a list", ILIST_ID_LANGUAGES ) . '</label>
						<input name="search-list" id="search-list" class="find-input" minlength="3" value="" placeholder="' . __( "Enter name, email", ILIST_ID_LANGUAGES ) . '" type="text">
					</p>
					<p class="form-row form-row-wide">
						<input class="button" value="' . __( "Search", ILIST_ID_LANGUAGES ) . '" type="submit">
					</p>
				</form>';
				
				if (isset($ilist_show_tabs) && array_keys( $ilist_show_tabs, true )) {
					
					// Open TABLE
					$return .= '<table class="shop_table cart ilist-table" cellspacing="0">
						<thead>
							<tr>
								<th class="ilist-list-name">' . __( "Name of list", ILIST_ID_LANGUAGES ) . '</th>
								<th class="ilist-creator-name">' . __( "Name", ILIST_ID_LANGUAGES ) . '</th>
								<th class="ilist-date">' . __( "Created", ILIST_ID_LANGUAGES ) . '</th>';
								if ( $share_list ) $return .= '<th class="ilist-share">' . __( "Share", ILIST_ID_LANGUAGES ) . '</th>';
							$return .= '</tr>
						</thead>
						<tbody>';
					foreach ( $ilist_show_tabs as $row_tab ) {
						$list_description = (trim($row_tab->description) != '') ? '<br><em class="ilist-description">' . esc_html($row_tab->description) . '</em>' : '';
						$return .= '<tr>
								<td class="ilist-list-name"><a href="' . ilist_get_url_page('mylist', 'a=liste&k=' . $row_tab->keyaccess) . '">' . esc_html($row_tab->name) . '</a>' . $list_description . '</td>
								<td class="ilist-creator-name">' . esc_html($row_tab->firstname) . ' ' . esc_html($row_tab->lastname) . '</td>
								<td class="ilist-date">' . ilistConvertDate($row_tab->date, 'FRH') . '</td>';
								if ( $share_list ) $return .= '<td class="ilist-share"><a href="javascript:;" onclick="ilist_show_share_icons(\'ilist-share-' . $row_tab->keyaccess . '\')"><i class="fa fa-share-alt" aria-hidden="true"></i></a></td>';
								//$return .= '<td class="ilist-share"><a href="#"><i class="fa fa-share-alt" aria-hidden="true"></i></a></td>';
						$return .= '</tr>';
						if ($share_list) {
							$return .= '<tr id="ilist-share-' . $row_tab->keyaccess . '" class="ilist-share-nodisplay">';
								$return .= '<td colspan="5">';
									$return .= ilist_get_share_icons( $ilist_url_complete . '?a=liste&k=' . $row_tab->keyaccess, $row_tab->keyaccess );
								$return .= '</td>';
							$return .= '</tr>';
						}
					}
					// Close TABLE
					$return .= '</tbody>';
					$return .= '</table>';
					
				} elseif ($_POST) {
					$return .= __( "No list found", ILIST_ID_LANGUAGES );
				}
				
				return $return;
			}
			
		break;

		case "liste":
			// --- Templates path
			$action_default = 'list';
			$ilist_template = ILIST_PATH . 'templates' . DIRECTORY_SEPARATOR . ILIST_ID . '-template-' . $action_default . '.php';
			// -------------------
			// Get all products from a list
			$ilist_keyaccess = ilist_stopXSS($_GET['k']);
			$ilist_show_tabs = ilist_get_woocommerce_product_list_by_key($ilist_keyaccess);
			// -------------------
			$return_shortcode_list = include_once($ilist_template);
			return $return_shortcode_list;
			// -------------------
		break;
	}
	
}
add_shortcode('ilist_search', 'ilist_shortcode_search');

?>