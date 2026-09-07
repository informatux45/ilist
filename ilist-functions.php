<?php
/** Blocking direct access to plugin
============================================= */
defined('ABSPATH') or die('Are you crazy!');

/**
 * Create pot product (Admin only - Call by AJAX Request)
 * This method return json
 *
 * @return string (JSON)
 */
add_action( 'wp_ajax_ilist_return_ajax', 'ilist_return_ajax' );
add_action( 'wp_ajax_nopriv_ilist_return_ajax', 'ilist_return_ajax' );
if (!function_exists("ilist_return_ajax")) {
	function ilist_return_ajax() {
		// Si ce n'est pas une requête ajax
		if ( ! defined( 'DOING_AJAX' ) ) {
			wp_safe_redirect( wp_get_referer() );
		}
		
		$_product_title = trim(ilist_stopXSS($_POST['title']));
		$_product_desc  = trim(ilist_stopXSS($_POST['description']));
		$_product_price = intval($_POST['price']);
		
		if ($_product_title != '' && $_product_price > 0) {
			$post_id = wp_insert_post( [
				 'post_title'   => $_product_title
				,'post_type'    => 'product'
				,'post_status'  => 'publish'
				,'post_content' => $_product_desc
				,'meta_input' => [
					 '_price'         => $_product_price
					,'_regular_price' => $_product_price
					,'_manage_stock'  => "no"
				]
			] );
		}
		
		// Visibility
		if ($post_id) wp_set_post_terms( $post_id, array( 'exclude-from-search', 'exclude-from-catalog' ), 'product_visibility', false ); // for hidden
		
		$message = ($post_id) ? __( 'Participation product successfully created', ILIST_ID_LANGUAGES ) : __( 'An error occurred while creating the product!', ILIST_ID_LANGUAGES );
		$success = ($post_id) ? true : false;
		$return = [
			 'success' => $success
			,'message' => $message
			,'post_id' => $post_id
		];
		
		echo json_encode( [ 'success' => $success, 'message' => $message, 'post_id' => $post_id ] );
		wp_die();
	}
}

/**
 * Ajax actions (Admin only - Call by AJAX Request)
 * This method return json
 *
 * @return string (JSON)
 */
add_action( 'wp_ajax_ilist_action_ajax', 'ilist_action_ajax' );
add_action( 'wp_ajax_nopriv_ilist_action_ajax', 'ilist_action_ajax' );
if (!function_exists("ilist_action_ajax")) {
	function ilist_action_ajax() {
		global $wpdb;
		// Switch
		switch($_POST['switch_a']) {
			default:
				$return = "";
			break;
			case "save_note_product":
				// Return
				$_return = 'nojson';
				// Get product ID
				$product_id = (isset($_POST['pid']) && $_POST['pid'] > 0) ? intval($_POST['pid']) : false;
				// Check product ID
				if (!$product_id) {
					$return = __('Error product ID !', ILIST_ID_LANGUAGES);
				} else {
					// Save note infos
					$note = trim($_POST['note']);	
					$updated = $wpdb->update(
						ILIST_TBL_PRODUCT, 
						 [ 'admin_note' => $note ] 
						,[ 'id' => $product_id ] 
						,[ '%s' ]
						,[ '%d' ] 
					);
					if ( false === $updated ) {
						$return = __('Error saving note!', ILIST_ID_LANGUAGES);
					} else {
						$return = __('Backup done!', ILIST_ID_LANGUAGES);
					}
				}
			break;
		}
		// Return results (json)
		echo ($_return == 'nojson') ? $return : json_encode( $return );
		wp_die();
	}
}

/**
 * Show date ISO in various format
 * This method return string to display
 *
 * @param	$date		Date ISO Format from MySQL
 * @param	$format		Format to return (ISO, US, UST, FR, FR2, FRT, FRH, YEAR)
 *
 * @return converted string
 */
if (!function_exists("ilistConvertDate")) {
	function ilistConvertDate($date, $format = 'ISO') {
		// strftime() est dépréciée depuis PHP 8.1 et sera supprimée en PHP 9.
		// Les formats numériques passent par date() (mêmes valeurs, même
		// fuseau qu'avant) ; seul le format "humain" passe par date_i18n()
		// pour obtenir le nom de mois traduit, ce que %B ne donnait que si
		// la locale système était posée (elle ne l'est pas sous WordPress).
		$timestamp = strtotime($date);
		if ($timestamp === false) return '';
		switch(strtoupper($format)) {
			// Format ISO (AAAA-MM-DD)
			default: return date("Y-m-d", $timestamp); break;
			// Format US (MM-DD-AAAA)
			case "US": return date("m/d/Y", $timestamp); break;
			// Format US (MM-DD-AAAA HH:mm:ss)
			case "UST": return date("m/d/Y H:i:s", $timestamp); break;
			// Format FR (DD/MM/AAAA)
			case "FR": return date("d/m/Y", $timestamp); break;
			// Format FR2 (DD-MM-AAAA)
			case "FR2": return date("d-m-Y", $timestamp); break;
			// Format FR (DD-MM-AAAA HH:mm:ss) with time
			case "FRT": return date("d-m-Y H:i:s", $timestamp); break;
			// Format FRH (DD MM AAAA) Human readable
			case "FRH": return date_i18n("j F Y", $timestamp); break;
			// Year (AAAA)
			case "YEAR": return date("Y", $timestamp); break;
		}
	}
}

/**
 * Get info session variable
 *
 * @return string
 */
if (!function_exists("ilist_get_session")) {
	function ilist_get_session($name) {
		$_get_session_var = "";
		switch(ILIST_SESSION) {
			case "wc":
				// WOOCOMMERCE SESSION
				$_get_session_var = WC()->session->get($name);
			break;
			case "php":
				// PHP SESSION
				$_get_session_var = $_SESSION[$name];
			break;
		}
		return $_get_session_var;
	}
}

/**
 * Set variable to session
 *
 * @return string
 */
if (!function_exists("ilist_set_session")) {
	function ilist_set_session($name, $data) {
		switch(ILIST_SESSION) {
			case "wc":
				// WOOCOMMERCE SESSION
				WC()->session->set($name , $data);
			break;
			case "php":
				// PHP SESSION
				$_SESSION[$name] = $data;
			break;
		}
	}
}

/**
 * Unset session variable
 *
 * @return string
 */
if (!function_exists("ilist_unset_session")) {
	function ilist_unset_session($name) {
		switch(ILIST_SESSION) {
			case "wc":
				// WOOCOMMERCE SESSION
				// WC()->session->__unset( "$name" );
				WC()->session->set($name , null);
			break;
			case "php":
				// PHP SESSION
				unset($_SESSION[$name]);
			break;
		}
	}
}

/**
 * Check if WooCommerce is activated
 *
 * @return void / Version of WooCommerce
 */
if ( ! function_exists( 'ilist_get_woo_version_number' ) ) {
	function ilist_get_woo_version_number() {
		// If get_plugins() isn't available, require it
		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
		// Get the plugin folder and file variables
		$plugin_folder = get_plugins( '/' . 'woocommerce' );
		$plugin_file = 'woocommerce.php';
		
		// If the plugin version number is set, return it 
		if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
			return $plugin_folder[$plugin_file]['Version'];
	
		} else {
		// Otherwise return null
			return NULL;
		}
	}
}

/**
 * Get all lists from Database
 * This method return Array
 *
 * @param	$user_id	User ID from Ilist
 *
 * @return array / void
 */
if (!function_exists("ilist_get_lists")) {
	function ilist_get_lists($user_id) {
		global $wpdb;
		// --- Initialize
		$all_lists = false;
		// --- Check if user_id
		if ($user_id > 0) {
			// Get all list from user_id
			$all_lists = $wpdb->get_results( "SELECT * FROM " . ILIST_TBL_MAIN . " WHERE user_id = '$user_id' AND active = '1' ORDER BY date DESC" );
		}
		return $all_lists;
	}
}

/**
 * Get all the lists in the database from a search
 * This method return Array to display in FRONT page
 *
 * IMPORTANT : la clause $where ne doit JAMAIS contenir de valeur issue d'une
 * saisie utilisateur. Elle ne porte que des noms de colonnes, des opérateurs et
 * des marqueurs de substitution ($wpdb->prepare : %s, %d, %f) ; les valeurs
 * correspondantes sont passées séparément dans $params.
 *
 * @param	$where	Where SQL clause (marqueurs %s / %d uniquement, pas de valeur)
 * @param	$params	Valeurs à substituer aux marqueurs de $where
 *
 * @return array
 */
if (!function_exists("ilist_get_lists_search")) {
	function ilist_get_lists_search($where = false, $params = array()) {
		global $wpdb;
		// --- Initialize
		$all_lists = false;
		// --- Check if where clause
		if ($where) {
			// Get all list with Where clause
			$sql = "SELECT * FROM " . ILIST_TBL_MAIN . " WHERE ($where) AND active = '1' ORDER BY date DESC";
			// Substitution des valeurs par $wpdb->prepare (échappement SQL)
			if ( !empty($params) ) $sql = $wpdb->prepare( $sql, $params );
			$all_lists = $wpdb->get_results( $sql );
		} else {
			// Get all list
			$all_lists = $wpdb->get_results( "SELECT * FROM " . ILIST_TBL_MAIN . " WHERE active = '1' ORDER BY date DESC" );
		}
		return $all_lists;
	}
}

/**
 * Get List infos
 *
 * @param	$key 		List key access
 * @param	$column		Column to display (Default: date)
 *
 * @return string
 */
if (!function_exists("ilist_get_list_infos")) {
	function ilist_get_list_infos($key, $column = 'date') {
		global $wpdb;
		// --- Check if key
		if (!$key) return false;
		// --- Get info
		$list_info = $wpdb->get_var( $wpdb->prepare( "SELECT $column FROM " . ILIST_TBL_MAIN . " WHERE keyaccess = '%s'", $key ) );
		return $list_info;
	}
}

/**
 * Get List infos by ID
 *
 * @param	$id 		List ID
 * @param	$column		Column to display
 *
 * @return string
 */
if (!function_exists("ilist_get_list_infos_by_id")) {
	function ilist_get_list_infos_by_id($id, $column = 'date') {
		global $wpdb;
		// --- Check if ID
		if (!$id) return false;
		// --- Get info
		$list_info = $wpdb->get_var( $wpdb->prepare( "SELECT $column FROM " . ILIST_TBL_MAIN . " WHERE id = '%s'", $id ) );
		
		return $list_info;
	}
}

/**
 * Métadonnées de commande WooCommerce (lecture)
 *
 * Passe par le CRUD WooCommerce plutôt que par get_post_meta() : depuis
 * WooCommerce 8, le stockage des commandes peut être "HPOS" (tables dédiées
 * wc_orders*) et non plus wp_posts/wp_postmeta. get_post_meta() ne renvoie
 * alors plus rien. Cette fonction fonctionne dans les deux modes.
 *
 * @param	$order_id	Order ID
 * @param	$key		Meta key
 *
 * @return string (chaîne vide si la commande ou la métadonnée n'existe pas)
 */
if (!function_exists("ilist_get_order_meta")) {
	function ilist_get_order_meta($order_id, $key) {
		$order = ($order_id) ? wc_get_order( $order_id ) : false;
		if (!$order) return '';
		return $order->get_meta( $key, true );
	}
}

/**
 * Métadonnées de commande WooCommerce (écriture)
 *
 * @param	$order_id	Order ID
 * @param	$key		Meta key
 * @param	$value		Valeur à enregistrer
 *
 * @return bool
 */
if (!function_exists("ilist_update_order_meta")) {
	function ilist_update_order_meta($order_id, $key, $value) {
		$order = ($order_id) ? wc_get_order( $order_id ) : false;
		if (!$order) return false;
		$order->update_meta_data( $key, $value );
		$order->save();
		return true;
	}
}

/**
 * Métadonnées de commande WooCommerce (suppression)
 *
 * @param	$order_id	Order ID
 * @param	$key		Meta key
 *
 * @return bool
 */
if (!function_exists("ilist_delete_order_meta")) {
	function ilist_delete_order_meta($order_id, $key) {
		$order = ($order_id) ? wc_get_order( $order_id ) : false;
		if (!$order) return false;
		$order->delete_meta_data( $key );
		$order->save();
		return true;
	}
}

/**
 * Check if Order ID exists in ilist_products Database
 * This method return integer / void
 *
 * @param	$oid	Order ID (post ID)
 *
 * @return integer (list ID) / void
 */
if (!function_exists("ilist_is_order")) {
	function ilist_is_order($oid) {
		global $wpdb;
		// --- Check if OID
		if (!$oid) return false;
		// Get list id from order id (Post ID)
		$list_id = $wpdb->get_var( $wpdb->prepare( "SELECT list_id FROM " . ILIST_TBL_PRODUCT . " WHERE order_id = '%s'", $oid ) );

		return (isset($list_id) && $list_id > 0) ? $list_id : false;
	}
}

/**
 * Check if Order ID exists in ilist_cagnotte Database
 * This method return integer
 *
 * @param	$oid	Order ID (post ID)
 *
 * @return integer (list ID)
 */
if (!function_exists("ilist_is_participation")) {
	function ilist_is_participation($oid) {
		global $wpdb;
		// --- Check if OID
		if (!$oid) return false;
		// Get list id from order id (Post ID)
		$list_id = $wpdb->get_var( $wpdb->prepare( "SELECT list_id FROM " . ILIST_TBL_CAGNOTTE . " WHERE order_id = '%s'", $oid ) );

		return (isset($list_id) && $list_id > 0) ? $list_id : false;
	}
}

/**
 * Check if last participation of a cagnotte in ilist_cagnotte Database (Ilist)
 * This method return integer
 *
 * @param	$product_id 	Product ID
 * @param	$list_id 		List ID
 * @param	$order_id		Order ID
 *
 * @return void or formatted string (participation price)
 */
if (!function_exists("ilist_is_end_participation")) {
	function ilist_is_end_participation($product_id, $list_id, $order_id) {
		global $wpdb, $Ilist;
		// Check infos
		if (!$product_id || !$list_id || !$order_id) return false;
		// Get list id from ORDER ID
		$list_is_end = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM " . ILIST_TBL_CAGNOTTE . " WHERE variation_id = '%s' AND list_id = '%s'", $product_id, $list_id ) );
		
		if ( $list_is_end ) {
			$order = new WC_Order( $order_id );
			foreach ($order->get_items() as $item_id => $item ) {
				if ($item->get_product_id() == $product_id) return $Ilist->get_woocommerce_price_format( $item->get_total() );
				if ($item->get_variation_id() == $product_id) return $Ilist->get_woocommerce_price_format( $item->get_total() );
			}
			
		} else {
			return false;
		}
	}
}

/**
 * Get all products sold by list ID
 * This method return array / void
 *
 * @param	$list_id 	List ID
 * @param	$order_id	Order ID (post ID)
 *
 * @return array / void
 */
if (!function_exists("ilist_get_all_products_sold_by_list_id")) {
	function ilist_get_all_products_sold_by_list_id($list_id, $order_id) {
		global $wpdb;
		// --- Check if infos
		if (!$list_id || !$order_id) return false;
		// Get list id from order id (Post ID)
		$products = $wpdb->get_results( "SELECT product_name, product_id, variation_id, order_id, list_id FROM " . ILIST_TBL_PRODUCT . " WHERE list_id = '$list_id' AND order_id = '$order_id'" );

		return $products;
	}
}

/**
 * CGet all participations sold by list ID
 * This method return Array
 *
 * @param	$list_id 	List ID
 * @param	$order_id	Order ID (post ID)
 *
 * @return array / void
 */
if (!function_exists("ilist_get_all_participations_sold_by_list_id")) {
	function ilist_get_all_participations_sold_by_list_id($list_id, $order_id) {
		global $wpdb;
		// --- Check if infos
		if (!$list_id || !$order_id) return false;
		// Get list id from order id (Post ID)
		$products = $wpdb->get_results( "SELECT product_name, order_id, list_id, customer FROM " . ILIST_TBL_CAGNOTTE . " WHERE list_id = '$list_id' AND order_id = '$order_id'" );

		return $products;
	}
}

/**
 * Get all participations by product
 * This method return array
 *
 * @param	$list_id			List ID
 * @param 	$product_id 		Product ID (Post ID)
 * @param 	$list_product_id 	Product ID in SQL Table ilist_product
 *
 * @return array / void
 */
if (!function_exists("ilist_get_all_participations_by_product")) {
	function ilist_get_all_participations_by_product($list_id, $product_id, $list_product_id = false) {
		global $wpdb;
		// Check if infos
		if (!$list_id || !$product_id) return false;
		// Check if $list_product_id
		if (!$list_product_id) {
			// Get participation from list ID AND product ID
			$request = "SELECT date, order_id, customer, participation FROM " . ILIST_TBL_CAGNOTTE . " WHERE list_id = '".intval($list_id)."' AND product_id = '".intval($product_id)."'";
			$participations = $wpdb->get_results( $request );
		} else {
			// Get participation from list ID AND product ID
			$request = "SELECT date, order_id, customer, participation FROM " . ILIST_TBL_CAGNOTTE . " WHERE list_id = '".intval($list_id)."' AND product_id = '".intval($product_id)."' AND list_product_id = '" . intval($list_product_id) . "'";
			$participations = $wpdb->get_results( $request );
			// Search in old ILIST version to display participations
			// !!!!!!!! Obsolete return after 2.1.28 version !!!!!!!
			if ( version_compare(ilist_get_version(), '2.1.28', '<=') ) {
				if (!$participations) {
					// Get participation from list ID AND product ID
					$request = "SELECT date, order_id, customer, participation FROM " . ILIST_TBL_CAGNOTTE . " WHERE list_id = '".intval($list_id)."' AND product_id = '".intval($product_id)."'";
					$participations = $wpdb->get_results( $request );
				}
			}
		}

		return $participations;
	}
}

/**
 * Get all lists from Database with limit
 * This method return Array to display
 *
 * @param	$limit	Limit SQL clause
 *
 * @return array
 */
if (!function_exists("ilist_get_lists_limit")) {
	function ilist_get_lists_limit($limit = 5) {
		global $wpdb;
		// --- Initialize
		$all_lists = false;
		// Get all list with limit
		$sql_request =
			"SELECT t1.id, t1.date, t1.name, count(t2.id) AS count_products
			 FROM " . ILIST_TBL_MAIN . " AS t1
			 LEFT JOIN " . ILIST_TBL_PRODUCT . " AS t2 ON (t1.id = t2.list_id)
			 WHERE t1.active = '1'
			 GROUP BY t1.id, t1.date, t1.name
			 ORDER BY t1.date DESC
			 LIMIT $limit";
		$all_lists = $wpdb->get_results( $sql_request );

		return $all_lists;
	}
}

/**
* Get all products from WooCommerce
* This method return Array to display
*
* @param 	$not_in 	Exclude IDs (Optionnal)
* 
* @return Array
*/
if (!function_exists("ilist_get_woocommerce_product_list")) {
	function ilist_get_woocommerce_product_list($not_in = null) {
		// Get all products in WooCommerce with exclude IDs
		if (isset($not_in) && $not_in != '') {
			global $wpdb;
			// Post DB
			$ilist_db_post = $wpdb->prefix . "posts";
			$query = "SELECT * FROM $ilist_db_post WHERE post_type = 'product' AND ID NOT IN ($not_in) ORDER BY post_name ASC LIMIT 10";
			// Get all products from a specific list
			$products = $wpdb->get_results( $query );
		} else {
			// Get all products in WooCommerce
			$args     = array( 'post_type' => 'product', 'posts_per_page' => -1, 'orderby' => 'post_name', 'order' => 'ASC',);
			$products = get_posts( $args );
		}
		
		return $products; 
	}
}

/**
 * Get sorting products method
 * 
 * @return sorting main method
 */
if (!function_exists("ilist_get_order_products_by")) {
	function ilist_get_order_products_by() {
		$ilist_sorting = (ilist_get_option( 'ilist_sorting_products' )) ? ilist_get_option( 'ilist_sorting_products' ) : "alpha_asc";
		return $ilist_sorting;
	}
}

/**
* Get all products from a list by keyaccess
* 
* @param string $keyaccess	List Key Access
*
* @return array $products
*/
if (!function_exists("ilist_get_woocommerce_product_list_by_key")) {
	function ilist_get_woocommerce_product_list_by_key($keyaccess = '') {
		global $wpdb;
		// Check key
		if (!$keyaccess) return false;
		// Sorting
		$get_order_by = ilist_get_order_products_by();
		switch($get_order_by) {
			default: case "alpha_asc": $order_by = "t2.product_name ASC"; break;
			case "alpha_desc": $order_by = "t2.product_name DESC"; break;
			case "id_asc": $order_by = "t2.id ASC"; break;
			case "id_desc": $order_by = "t2.id DESC"; break;
			case "date_asc": $order_by = "t3.post_date ASC"; break;
			case "date_desc": $order_by = "t3.post_date DESC"; break;
			case "price_asc": $order_by = "product_price ASC"; break;
			case "price_desc": $order_by = "product_price DESC"; break;
		}
		// Initialize
		$products = [];
		// Post DB
		$ilist_db_post = $wpdb->prefix . "posts";
		$ilist_db_meta = $wpdb->prefix . 'postmeta';
		// Check if the option unpurchased products displayed at the top of the list is activated
		$_order_by = (ilist_get_option('ilist_show_unpurchased_products_top_list')) ? "t2.status ASC,$order_by" : $order_by;
		// Check if keyaccess
		if (isset($keyaccess) && $keyaccess != '') {
			try {
				// Bug des jointures
				// Erreur dans la requête (1104):
				// The SELECT would examine more than MAX_JOIN_SIZE rows; check your WHERE and use SET SQL_BIG_SELECTS=1 or SET MAX_JOIN_SIZE=# if the SELECT is okay
				$wpdb->query('SET SQL_BIG_SELECTS=1');
				// Delete GROUP BY after WHERE clause
				// --> GROUP BY t2.id ASC
				$query = "SELECT DISTINCT t1.name, t1.keyaccess AS keyaccess, t1.active AS active,
							t2.id, t2.list_id, t2.product_id, t2.variation_id, t2.product_name, t2.is_pot, t2.customer, t2.status, t2.date, t2.favorite,
							t3.ID, t3.post_content, t3.post_title, t3.post_type,
							t4.meta_value AS product_price
							FROM " . ILIST_TBL_MAIN . " AS t1
							LEFT JOIN " . ILIST_TBL_PRODUCT . " AS t2 ON (t1.id = t2.list_id)
							LEFT JOIN $ilist_db_post AS t3 ON (t2.product_id = t3.ID AND t2.variation_id IS NULL) OR (t2.variation_id = t3.ID)
							LEFT JOIN $ilist_db_meta AS t4 ON (t3.ID = t4.post_ID)
							WHERE t1.keyaccess = '$keyaccess'
							  AND t1.active = '1'
							  AND t2.product_id > 0
							  AND (t2.product_id > 0 OR t2.variation_id > 0)
							  AND t3.post_type IN ('product', 'product_variation')
							  AND t4.meta_key = '_price'
							  AND t3.post_status = 'publish'
							ORDER BY $_order_by LIMIT 200";
				$products = $wpdb->get_results( $query );
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
		// Return array
		return $products; 
	}
}

/**
* Get A product by keyaccess
* 
* @param string $keyaccess	List Key Access
*
* @return array $product
*/
if (!function_exists("ilist_get_woocommerce_product_by_key")) {
	function ilist_get_woocommerce_product_by_key($keyaccess = '') {
		global $wpdb;
		// Check key
		if (!$keyaccess) return false;
		// Post DB
		$ilist_db_post = $wpdb->prefix . "posts";
		// Check if keyaccess
		if (isset($keyaccess) && $keyaccess != '') {
			$query = "SELECT t1.name, t1.keyaccess AS keyaccess, t1.active AS active,
						t2.id, t2.list_id, t2.product_id, t2.product_name, t2.customer, t2.status, t2.date,
						t3.ID, t3.post_content, t3.post_title, t3.post_type
						FROM " . ILIST_TBL_MAIN . " AS t1
						LEFT JOIN " . ILIST_TBL_PRODUCT . " AS t2 ON (t1.id = t2.list_id)
						LEFT JOIN $ilist_db_post AS t3 ON (t2.product_id = t3.ID)
						WHERE t1.keyaccess = '$keyaccess' AND t1.active = '1' AND t2.product_id > 0
						ORDER BY t2.product_name ASC LIMIT 1";
			// Get A product with a specific key
			$product = $wpdb->get_row( $query );
		}
		// Return array
		return $product; 
	}
}

/**
* Get all products by list ID
* 
* @param 	int 	 $id 	List ID
*
* @return array $product
*/
if (!function_exists("ilist_get_woocommerce_product_list_by_id")) {
	function ilist_get_woocommerce_product_list_by_id($list_id = '') {
		global $wpdb;
		// Check key
		if (!$list_id) return false;
		// Sorting
		$get_order_by = ilist_get_order_products_by();
		switch($get_order_by) {
			default: case "alpha_asc": $order_by = "t2.product_name ASC"; break;
			case "alpha_desc": $order_by = "t2.product_name DESC"; break;
			case "id_asc": $order_by = "t2.id ASC"; break;
			case "id_desc": $order_by = "t2.id DESC"; break;
			case "date_asc": $order_by = "t3.post_date ASC"; break;
			case "date_desc": $order_by = "t3.post_date DESC"; break;
			case "price_asc": $order_by = "product_price ASC"; break;
			case "price_desc": $order_by = "product_price DESC"; break;
		}
		// Initialize
		$products = [];
		// Post DB
		$ilist_db_post = $wpdb->prefix . "posts";
		$ilist_db_meta = $wpdb->prefix . 'postmeta';
		// Check if the option unpurchased products displayed at the top of the list is activated
		$_order_by = (ilist_get_option('ilist_show_unpurchased_products_top_list')) ? "t2.status ASC,$order_by" : $order_by;
		// Check if keyaccess
		if (isset($list_id) && $list_id != '') {
			try {
				// Delete GROUP BY after WHERE clause
				// --> GROUP BY t2.id ASC
				$query = "SELECT DISTINCT t1.name, t1.keyaccess AS keyaccess, t1.active AS active,
							t2.id, t2.list_id, t2.product_id, t2.variation_id, t2.product_name, t2.is_pot, t2.customer, t2.status, t2.date, t2.favorite,
							t3.ID, t3.post_content, t3.post_title, t3.post_type,
							t4.meta_value AS product_price
							FROM " . ILIST_TBL_MAIN . " AS t1
							LEFT JOIN " . ILIST_TBL_PRODUCT . " AS t2 ON (t1.id = t2.list_id)
							LEFT JOIN $ilist_db_post AS t3 ON (t2.product_id = t3.ID AND t2.variation_id IS NULL) OR (t2.variation_id = t3.ID)
							LEFT JOIN $ilist_db_meta AS t4 ON (t3.ID = t4.post_ID)
							WHERE t1.id = '$list_id'
							  AND t1.active = '1'
							  AND t2.product_id > 0
							  AND (t2.product_id > 0 OR t2.variation_id > 0)
							  AND t3.post_type IN ('product', 'product_variation')
							  AND t4.meta_key = '_price'
							  AND t3.post_status = 'publish'
							ORDER BY $_order_by LIMIT 200";
				$products = $wpdb->get_results( $query );
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
		// Return array
		return $products; 
	}
}

/**
 * Get permalink of ILIST page
 *
 * @param	$type 	Type of url
 * @param	$args	Args of url
 * 
 * @return string (url)
 */
if (!function_exists("ilist_get_url_page")) {
	function ilist_get_url_page($type, $args = '') {
		$ilist_end_url = (ilist_get_option( 'ilist_rewrite_url' )) ? "/" : "";
		switch($type) {
			default:
			case "search":
				$permalink = ilist_get_option( 'ilist_search_url_complete' ) . $ilist_end_url;
				if ( trim( substr($permalink, -1) ) == '/' ) {
					return $permalink . '?' . $args;
				} else {
					return $permalink . '&' . $args;
				}
			break;
			case "mylist":
				$permalink = ilist_get_option( 'ilist_url_complete' ) . $ilist_end_url;
				if ( trim( substr($permalink, -1) ) == '/' ) {
					return $permalink . '?' . $args;
				} else {
					return $permalink . '&' . $args;
				}
			break;
		}
	}
}

/**
* Get all status from DB
* 
* @return array $status
*/
if (!function_exists("ilist_get_status_list")) {
	function ilist_get_status_list() {
		global $wpdb;
		// Get all status in DB
		$query = "SELECT id, title, description, color FROM " . ILIST_TBL_TYPE . " ORDER BY id ASC";
		// Get all products from a specific list
		$status = $wpdb->get_results( $query );
		
		return $status; 
	}
}

/**
 * Get Status Information
 * 
 * @return string
 */
if (!function_exists("ilist_get_status_info")) {
	function ilist_get_status_info($status_id = NULL, $column = '') {
		global $wpdb;
		// Check if status ID
		if (!isset($status_id)) return false;
		// Initialize
		$table_column = ($column != '') ? $column : '*';
		$ilist_info = $wpdb->get_row( "SELECT $table_column FROM " . ILIST_TBL_TYPE . " WHERE id = $status_id" );
		// Check result    
		if ( null !== $ilist_info ) {
			return $ilist_info->{$table_column};
		} else {
			// no info found
			return false;
		}
	}
}

/**
 * Update stock if activated
 *
 * @param	$_product 	Woocommerce Product Object
 * @param 	$action		increase OR decrease stock (Default: increase)
 * 
 * @return void
 */
if (!function_exists("ilist_update_stock")) {
	function ilist_update_stock($_product, $action = 'increase') {
		$ilist_stock = ilist_get_option( 'ilist_stock_management' );
		$ilist_debug = ilist_get_option( 'ilist_debug_log' );
		// Check if option Stock Manage ILIST is activated
		if (!$ilist_stock) return;
		// Check if product object
		if (!$_product) return;
		// Get product stock quantity and stock status
		$stock_quantity = $_product->get_stock_quantity();
		$stock_status   = $_product->get_stock_status();
		// Option log (if activated)
		if ($ilist_debug) {
			ilist_log_start();
			ilist_log('Action ' . ($action == 'increase' ? 'DEL' : 'ADD') . ' PRODUCT in list');
			ilist_log('Option stock: '. ($ilist_stock ? 'Activé' : 'Désactivé'));
			if ($_product->get_manage_stock()) {
				ilist_log('Option stock produit (status) : '. $stock_status);
				ilist_log('Option stock produit (quantité) : '. $stock_quantity);
				ilist_log('Produit manage stock: Activé');
			} else {
				ilist_log('Produit manage stock: Désactivé');
			}
		}

		// Check if all stock options are activated			
		if ( $_product->get_manage_stock() && $ilist_stock ) {
			// --- Check type product
			switch ( $_product->get_type() ) {
				case "variable":
				case "variation": // WooCommerce > 5.0
					if ($action == 'increase') {
						// in stock, so increase product stock
						$stock_quantity++; // DEL
					} else {
						// in stock, so decrease product stock
						$stock_quantity--; // ADD
					}
				break;
				case "grouped":  // Pas de gestion de stock pour ce type
				case "external": // Pas de gestion de stock pour ce type
				default:
					if ($action == 'increase') {
						// in stock, so increase product stock
						$stock_quantity++; // DEL
					} else {
						// in stock, so decrease product stock
						$stock_quantity--; // ADD
					}
				break;
			}
			// Update product's stock amount
			$_product_update_stock = wc_update_product_stock($_product, $stock_quantity);
			// Option log (if activated)
			if ($ilist_debug) ilist_log('Product stock (new qty): '.$_product_update_stock);
		}
	}
}

/**
 * Get product infos from ilist product for increase / decrease stock
 *
 * @param 	$list_id	List ID
 * @param	$status 	Where SQL Clause
 * 
 * @return product IDs - OR - variation IDs
 */
if (!function_exists("ilist_get_all_ids_for_stock")) {
	function ilist_get_all_ids_for_stock($list_id, $status = false) {
		global $wpdb;
		// --- Initialize
		$all_products = false;
		// --- Get status
		$where_status = ($status === true) ? " AND status = '1'" : ""; 
		// --- Get all list from user_id
		$sql_request  = "SELECT product_id, variation_id, status FROM " . ILIST_TBL_PRODUCT . " WHERE list_id = '$list_id'$where_status";
		$sql_result   = $wpdb->get_results( $sql_request );
		
		foreach($sql_result as $key => $product) {
			$all_products[$key] = ($product->variation_id > 0) ? $product->variation_id : $product->product_id;
		}

		return $all_products;
	}
}

/**
 * Get all share icons
 *
 * @param 	$url 						Url to share
 * @param 	$current_list_keyaccess 	Key Access
 * 
 * @return HTML string
 */
if (!function_exists("ilist_get_share_icons")) {
	function ilist_get_share_icons( $url = false, $current_list_keyaccess = false ) {
		global $Ilist;
		// initialize
		$site_title       = get_bloginfo( 'name' );
		$site_description = get_bloginfo( 'description' );
		$get_share_icons  = ilist_get_option('ilist_share_icons');
		$site_list_url    = (isset($url) && $url != '') ? $url : ilist_get_option( 'ilist_search_url_complete' );
		$icon_type        = (ilist_get_option('ilist_share_icons_method')) ? ilist_get_option('ilist_share_icons_method') : 'rounded';
		
		// Share icons string
		$share_icons  = "";
		$share_icons .= '<p class="ilist-share-buttons">';
		// Copy / Paste
		if (is_array($get_share_icons) && in_array('copypaste', $get_share_icons)) {
			$copypasterand = rand();
			$share_icons .= '<a style="cursor: pointer;" onclick="_copypaste' . $copypasterand . '(\'' . $site_list_url . '\')" title="' . __('Copy Paste URL', ILIST_ID_LANGUAGES) . '">
								<img src="' . ILIST_URL . '/images/share/copypaste-' . $icon_type . '.png" alt="' . __('Copy Paste URL', ILIST_ID_LANGUAGES) . '" />
							 </a>';
			$share_icons .= '<script>
								function _copypaste'.$copypasterand.'(copyText) {
									navigator.clipboard.writeText(copyText).then(function() {
										alert("' . __('URL copied', ILIST_ID_LANGUAGES) . '");
									}, function() {
										alert("' . __('Error while copying', ILIST_ID_LANGUAGES) . '");
									});
								}
							 </script>';
		}
		// Buffer
		if (is_array($get_share_icons) && in_array('buffer', $get_share_icons)) {
			$share_icons .= '<a href="https://bufferapp.com/add?url=' . urlencode($site_list_url) . '&amp;text=' . urlencode(__('A list to share at ', ILIST_ID_LANGUAGES) . ' ' . $site_title  . ' ' . ilist_current_url()) . '" title="Buffer" target="_blank">
								<img src="' . ILIST_URL . '/images/share/buffer-' . $icon_type . '.png" alt="Buffer" />
							 </a>';
		}
		// Digg
		if (is_array($get_share_icons) && in_array('digg', $get_share_icons)) {
			$share_icons .= '<a href="http://www.digg.com/submit?url=' . urlencode($site_list_url) . '" target="_blank" title="Digg">
								<img src="' . ILIST_URL . '/images/share/diggit-' . $icon_type . '.png" alt="Digg" />
							 </a>';
		}
		// Email
		if (is_array($get_share_icons) && in_array('email', $get_share_icons)) {
			$url_encode   = 'mailto:?Subject=' . $site_title . ' - ' . $site_description . '&amp;Body=' . __('A list to share at ', ILIST_ID_LANGUAGES) . ' ' . $site_title  . ' ' . urlencode($site_list_url);
			$share_icons .= '<a href="' . $url_encode . '" title="Email" target="_blank">
								<img src="' . ILIST_URL . '/images/share/email-' . $icon_type . '.png" alt="Email" />
							 </a>';
		}
		// Gmail
		if (is_array($get_share_icons) && in_array('gmail', $get_share_icons)) {
			$url_encode   = 'https://mail.google.com/mail/?view=cm&fs=1&tf=1&source=mailto&su=' . urlencode( $site_title . ' - ' . $site_description ) . '&body=' . urlencode( __('A list to share at ', ILIST_ID_LANGUAGES) . ' ' . $site_title  . ' ' . $site_list_url );
			$share_icons .= '<a href="' . $url_encode . '" target="_blank" title="Gmail">
								<img src="' . ILIST_URL . '/images/share/gmail-' . $icon_type . '.png" alt="Gmail" />
							 </a>';
		}
		// Facebook
		if (is_array($get_share_icons) && in_array('facebook', $get_share_icons)) {
			$share_icons .= '<a href="http://www.facebook.com/sharer.php?u=' . urlencode($site_list_url) . '" target="_blank" title="Facebook">
								<img src="' . ILIST_URL . '/images/share/facebook-' . $icon_type . '.png" alt="Facebook" />
							 </a>';
		}
		// Google+
		if (is_array($get_share_icons) && in_array('google', $get_share_icons)) {
			$share_icons .= '<a href="https://plus.google.com/share?url=' . urlencode($site_list_url) . '" target="_blank" title="Google">
								<img src="' . ILIST_URL . '/images/share/google-' . $icon_type . '.png" alt="Google" />
							 </a>';
		}
		// LinkedIn
		if (is_array($get_share_icons) && in_array('linkedin', $get_share_icons)) {
			$share_icons .= '<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=' . urlencode($site_list_url) . '" target="_blank" title="LinkedIn">
								<img src="' . ILIST_URL . '/images/share/linkedin-' . $icon_type . '.png" alt="LinkedIn" />
							 </a>';
		}
		// Pinterest
		if (is_array($get_share_icons) && in_array('pinterest', $get_share_icons)) {
			$share_icons .= '<a href="javascript:void((function()%7Bvar%20e=document.createElement(\'script\');e.setAttribute(\'type\',\'text/javascript\');e.setAttribute(\'charset\',\'UTF-8\');e.setAttribute(\'src\',\'http://assets.pinterest.com/js/pinmarklet.js?r=\'+Math.random()*99999999);document.body.appendChild(e)%7D)());" title="Pinterest">
								<img src="' . ILIST_URL . '/images/share/pinterest-' . $icon_type . '.png" alt="Pinterest" />
							 </a>';
		}
		// Reddit
		if (is_array($get_share_icons) && in_array('reddit', $get_share_icons)) {
			$share_icons .= '<a href="http://reddit.com/submit?url=' . urlencode($site_list_url) . '&amp;title=' . urlencode(__('A list to share at ', ILIST_ID_LANGUAGES) . ' ' . $site_title  . ' ' . $site_list_url) . '" title="Reddit" target="_blank">
								<img src="' . ILIST_URL . '/images/share/reddit-' . $icon_type . '.png" alt="Reddit" />
							 </a>';
		}
		// StumbleUpon
		if (is_array($get_share_icons) && in_array('stumbleupon', $get_share_icons)) {
			$share_icons .= '<a href="http://www.stumbleupon.com/submit?url=' . urlencode($site_list_url) . '&amp;title=' . urlencode(__('A list to share at ', ILIST_ID_LANGUAGES) . ' ' . $site_title  . ' ' . $site_list_url) . '" title="StumbleUpon" target="_blank">
								<img src="' . ILIST_URL . '/images/share/stumbleupon-' . $icon_type . '.png" alt="StumbleUpon" />
							 </a>';
		}
		// Tumblr
		if (is_array($get_share_icons) && in_array('tumblr', $get_share_icons)) {
			$share_icons .= '<a href="http://www.tumblr.com/share/link?url=' . urlencode($site_list_url) . '&amp;title=' . urlencode(__('A list to share at ', ILIST_ID_LANGUAGES) . ' ' . $site_title  . ' ' . $site_list_url) . '" title="Tumblr" target="_blank">
								<img src="' . ILIST_URL . '/images/share/tumblr-' . $icon_type . '.png" alt="Tumblr" />
							 </a>';
		}
		// Twitter
		if (is_array($get_share_icons) && in_array('twitter', $get_share_icons)) {
			$share_icons .= '<a href="https://twitter.com/share?url=' . urlencode($site_list_url) . '&amp;text=' . urlencode(__('A list to share at ', ILIST_ID_LANGUAGES) . ' ' . $site_title  . ' ' . $site_list_url) . '&amp;hashtags=simplesharebuttons" target="_blank" title="Twitter">
								<img src="' . ILIST_URL . '/images/share/twitter-' . $icon_type . '.png" alt="Twitter" />
							 </a>';
		}
		// VK
		if (is_array($get_share_icons) && in_array('vk', $get_share_icons)) {
			$share_icons .= '<a href="http://vkontakte.ru/share.php?url=' . urlencode($site_list_url) . '" title="VK" target="_blank">
								<img src="' . ILIST_URL . '/images/share/vk-' . $icon_type . '.png" alt="VK" />
							 </a>';
		}
		// WhatsApp
		if (is_array($get_share_icons) && in_array('whatsapp', $get_share_icons)) {
			//$share_icons .= '<a href="whatsapp://send?text=' . urlencode($site_list_url) . '" data-action="share/whatsapp/share" target="_blank" title="WhatsApp">
			//				    <img src="' . ILIST_URL . '/images/share/whatsapp.png" alt="WhatsApp" />
			//				 </a>'; // Only Whats App Application on mobile
			$share_icons .= '<a href="https://api.whatsapp.com/send?text=' . urlencode($site_list_url) . '" data-action="share/whatsapp/share" target="_blank" title="WhatsApp">
								<img src="' . ILIST_URL . '/images/share/whatsapp-' . $icon_type . '.png" alt="WhatsApp" />
							 </a>'; // Open Whats App Application on mobile if exists, if not, open the browser
		}
		// Yummly
		if (is_array($get_share_icons) && in_array('yummly', $get_share_icons)) {
			$share_icons .= '<a href="http://www.yummly.com/urb/verify?url=' . urlencode($site_list_url) . '&amp;title=' . urlencode(__('A list to share at ', ILIST_ID_LANGUAGES) . ' ' . $site_title  . ' ' . $site_list_url) . '" title="Yummly" target="_blank">
								<img src="' . ILIST_URL . '/images/share/yummly-' . $icon_type . '.png" alt="Yummly" />
							 </a>';
		}
		// Print
		if (is_array($get_share_icons) && in_array('print', $get_share_icons)) {
			$ilist_popup_id = ilist_rand_sha1();
			$share_icons .= '<a href="javascript:;" onclick="ilist_popup_' . $ilist_popup_id . '()" title="' . __('Print', ILIST_ID_LANGUAGES) . '">
								<img src="' . ILIST_URL . '/images/share/print-' . $icon_type . '.png" alt="Print" />
							 </a>';
			$share_icons .= '<script>
				function ilist_popup_' . $ilist_popup_id . '() {';
					if (ilist_get_option( 'ilist_print_open' ))
						$share_icons .= 'ilist_' . $ilist_popup_id . ' = window.open("", "popup", "width=800,height=400,toolbar=no,scrollbars=no,resizable=yes");';
					else
						$share_icons .= 'ilist_' . $ilist_popup_id . ' = window.open();';
					$share_icons .= 'ilist_' . $ilist_popup_id . '.document.write("<body>");';
					// ---------------------------------
					// --- LOGO
					// ---------------------------------
					if (ilist_get_option( 'ilist_print_logo' )) {
						$share_icons .= 'ilist_' . $ilist_popup_id . '.document.write("<div style=\'text-align: center; margin: 0 0 1em 0;\'><img src=\'' . ilist_get_option( 'ilist_print_logo' ) . '\' style=\'height: ' . ilist_get_option( 'ilist_print_logo_height' ) . 'px;\'></div>");';
					}
					// ---------------------------------
					// --- HEAD TEXT
					// ---------------------------------
					if (trim(ilist_get_option( 'ilist_print_head_text' )) != '') {
						$ilist_print_head_text = str_replace('"', "'", trim(ilist_get_option( 'ilist_print_head_text' )));
						$ilist_print_head_text = str_replace("\n", "<br>", $ilist_print_head_text);
						$share_icons .= 'ilist_' . $ilist_popup_id . '.document.write("' . $ilist_print_head_text . '");';
					} else {
						// Default text
						$share_icons .= 'ilist_' . $ilist_popup_id . '.document.write("<div style=\'text-align: center; margin: 0 0 1em 0;\'><h1 style=\'margin: 0;\'>' . addslashes($site_title) . '</h1><h3>' . addslashes($site_description) . '</h3></div>");';
					}
					// ---------------------------------
					// --- TITLE
					// ---------------------------------
					// --- List User name
					$user_id     = ilist_get_list_infos($current_list_keyaccess, 'user_id');
					$user_name   = (ilist_get_option( 'ilist_display_user_name' )) ? ' (' . ucfirst(ilist_get_list_infos($current_list_keyaccess, 'firstname')) . ' ' . strtoupper(ilist_get_list_infos($current_list_keyaccess, 'lastname')) . ')' : "";
					$description = trim(ilist_get_list_infos($current_list_keyaccess, 'description'));
					$ilist_desc  = ($description != '') ? '<br>' . __( "Description", ILIST_ID_LANGUAGES ) . ' : ' . esc_html( ilist_undoNl2Br($description, " ") ) : '';
					// --- List name
					$share_icons .= '
						ilist_' . $ilist_popup_id . '.document.write("<div style=\'text-align: center;\'><h5 class=\'ilist-h5\'>' . __( "List", ILIST_ID_LANGUAGES ) . ' : ' . ilist_get_list_infos($current_list_keyaccess, 'name') . $user_name . $ilist_desc . '</h5></div>");';
					// ----------------------------------------
					// --- Open TABLE
					// ----------------------------------------
					$share_icons .= 'ilist_' . $ilist_popup_id . '.document.write("<table style=\'width: 100%;\' cellspacing=\'5\'><thead><tr><th>' . __( "Photo", ILIST_ID_LANGUAGES ) . '</th><th>' . __( "Product", ILIST_ID_LANGUAGES ) . '</th><th>' . __( "Price", ILIST_ID_LANGUAGES ) . '</th><th>' . __( "Status", ILIST_ID_LANGUAGES ) . '</th></tr></thead><tbody>");';
					// --- Options
					$ilist_product_with_vat = ilist_get_option( 'ilist_ttc_product_in_list' ); // --- Product price with VAT
					// --- Get all products from a list
					$ilist_show_tabs = ilist_get_woocommerce_product_list_by_key($current_list_keyaccess);
					foreach ( $ilist_show_tabs as $row_tab ) {
						$_product = wc_get_product( $row_tab->ID );
						// ----------------------------------------
						// Check if product exists
						// ----------------------------------------
						if (!$_product) continue;
						// ----------------------------------------
						// Main product image
						// ----------------------------------------
						$_product_img = wp_get_attachment_image_src( get_post_thumbnail_id( $row_tab->ID ), 'thumbnail' );
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
									$_product_image = (isset($_product_img[0])) ? $_product_img[0] : false;
								}
							break;
							case "grouped":
							case "external":
							default:
								$_product_url   = get_permalink($row_tab->ID);
								$_product_name  = $row_tab->product_name;
								$_product_price = sprintf( get_woocommerce_price_format(), get_woocommerce_currency_symbol(), $_product->get_price() );
								$_product_image = (isset($_product_img[0])) ? $_product_img[0] : false;
							break;
						}
						// ----------------------------------------
						// --- Get price
						// ----------------------------------------
						// $_product->get_regular_price();
						// $_product->get_sale_price();
						// $_product->get_price();
						$_product_thumb_height = (ilist_get_option( 'ilist_print_thumbnail_height' )) ? intval( ilist_get_option( 'ilist_print_thumbnail_height' ) ) : 150;
						$share_icons .= 'ilist_' . $ilist_popup_id . '.document.write("<tr id=\'ilist-tr-'.$row_tab->id.'\'><td style=\'text-align: center;\'><img style=\'height: '.$_product_thumb_height.'px;\' src=\''.$_product_image.')\'></td><td>' . esc_html($_product_name) . '</td><td style=\'text-align: center;\'>' . $_product_price . '</td>");';
								// ----------------------------------------
								// Switch beetween Status
								// 1: A vendre
								// 2: Achat Internet
								// ----------------------------------------
								switch($row_tab->status) {
									case "1": // Toujours a vendre
										$share_icons .= 'ilist_' . $ilist_popup_id . '.document.write("<td style=\'text-align: center;\'>' . __( "Not still bought", ILIST_ID_LANGUAGES ) . '</td>");';
									break;
									default: // Others status than 1
										$share_icons .= 'ilist_' . $ilist_popup_id . '.document.write("<td style=\'text-align: center;\'>' . __( "Offered product", ILIST_ID_LANGUAGES ) . '</td>");';
									break;
								}
						$share_icons .= 'ilist_' . $ilist_popup_id . '.document.write("</tr>");';
					}
						
					// ----------------------------------------
					// --- Close TABLE
					// ----------------------------------------
					$share_icons .= 'ilist_' . $ilist_popup_id . '.document.write("</tbody>");';
					$share_icons .= 'ilist_' . $ilist_popup_id . '.document.write("</table>");';
					// ----------------------------------------
					// --- FOOTER TEXT
					// ----------------------------------------
					if (trim(ilist_get_option( 'ilist_print_footer_text' )) != '') {
						$ilist_print_footer_text = str_replace('"', "'", trim(ilist_get_option( 'ilist_print_footer_text' )));
						$ilist_print_footer_text = str_replace("\n", "<br>", $ilist_print_footer_text);
						$share_icons .= 'ilist_' . $ilist_popup_id . '.document.write("' . $ilist_print_footer_text . '");';
					}
					// ----------------------------------------
					// --- End of print
					// ----------------------------------------
					$share_icons .= '
						ilist_' . $ilist_popup_id . '.document.write("</body>");
						ilist_' . $ilist_popup_id . '.document.close();
						ilist_' . $ilist_popup_id . '.window.print();
				}
				</script>';
		}
		
		$share_icons .= '</p>';
		
		return $share_icons;
	}
}

/**
 * Get Variable product infos (AJAX Method)
 *
 * @return JSON Array
 */
add_action( 'wp_ajax_ilist_ajax_product_infos', 'ilist_ajax_product_infos' ); // wp_ajax_{action}
add_action( 'wp_ajax_nopriv_ilist_ajax_product_infos', 'ilist_ajax_product_infos' ); // wp_ajax_nopriv_{action}
if (!function_exists("ilist_ajax_product_infos")) {
	function ilist_ajax_product_infos() {
	// Switch
		switch($_POST['switch_a']) {
			default:
			case "get_variation_price":
				// Get Product ID
				$product_id = $_POST['variation_id'];
				// Get Product infos
				$product = wc_get_product( $product_id );
				// Return infos
				$return = $product->get_price();
			break;
			case "notify_admins":
				$id = (isset($_POST['list_id']) && $_POST['list_id'] > 0) ? intval($_POST['list_id']) : false;
				// Check ID
				if (!$id) {
					$return = 'noid';
				} else {
					// -------------------------------------------
					// -------------------------------------------
					// Send Email to ADMINISTRATOR(S)
					// -------------------------------------------
					// -------------------------------------------
					$ilist_list_name     = ilist_get_list_infos_by_id($id, 'name');
					$ilist_list_customer = esc_html( ilist_get_list_infos_by_id($id, 'firstname') ) . ' ' . esc_html( ilist_get_list_infos_by_id($id, 'lastname') );
					$ilist_list_email    = ilist_get_list_infos_by_id($id, 'email');
					// --- Get options
					$ilist_emails_admin         = trim(get_bloginfo('admin_email'));
					$ilist_email_admin_name     = trim(get_bloginfo('name'));
					$ilist_email_admin_subject  = sprintf( __('Customer wanting to retrieve his items (List: %s)', ILIST_ID_LANGUAGES), $ilist_list_name );
					$ilist_email_admin_body     = __('Hi Admin,<br><br>{ILIST_CREATOR} wants to retrieve his items from the list {ILIST_NAME}.<br>Email: {ILIST_EMAIL}<br><br>{ILIST_WP_SITE_NAME} - {ILIST_WP_SITE_DESC}', ILIST_ID_LANGUAGES);
					// --- Replace Variables in email body
					$ilist_type = ilist_get_status_info(2, 'title'); // Internet purchase
					$ilist_email_admin_body = @preg_replace("/{ILIST_NAME}/", esc_html($ilist_list_name), $ilist_email_admin_body);
					$ilist_email_admin_body = @preg_replace("/{ILIST_CREATOR}/", esc_html($ilist_list_customer), $ilist_email_admin_body);
					$ilist_email_admin_body = @preg_replace("/{ILIST_EMAIL}/", $ilist_list_email, $ilist_email_admin_body);
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
					$headers   = ["From: $ilist_email_admin_name <$ilist_emails_admin>",
								  ];
					$to_admins = $ilist_emails_admin;
					$subject   = $ilist_email_admin_subject;
					$body      = nl2br($ilist_email_admin_body);
					// -----------------------------
					wp_mail( $to_admins, $subject, $body, $headers );
					// Option log (if activated)
					if (ilist_get_option('ilist_debug_log')) {
						ilist_log("Email Admin (Retrieve items from $ilist_list_customer)");
					}
					// Reset content-type to avoid conflicts -- https://core.trac.wordpress.org/ticket/23578
					remove_filter( 'wp_mail_content_type', 'ilist_set_html_mail_content_type' );
					$return = "ok";
				}
			break;
		}
		// Return results (json)
		echo json_encode( $return );
		wp_die();
	}
}

/**
 * Get products (AJAX Method)
 *
 * @return JSON Array
 */
add_action( 'wp_ajax_ilist_get_products', 'ilist_get_products_ajax_callback' ); // wp_ajax_{action}
add_action( 'wp_ajax_nopriv_ilist_get_products', 'ilist_get_products_ajax_callback' ); // wp_ajax_nopriv_{action}
if (!function_exists("ilist_get_products_ajax_callback")) {
	function ilist_get_products_ajax_callback() {
		// Get all products in WooCommerce
		$ilist_all_products = ilist_get_woocommerce_product_list();
		$return = [];
		// Search term
		$search = trim($_REQUEST['search']);
		// Check if products
		$test_if_array_is_not_empty = array_keys( $ilist_all_products, true );
		if ($test_if_array_is_not_empty) {
			
			foreach($ilist_all_products as $product_row) {
				// Get product infos
				$_product = wc_get_product( $product_row->ID );
				switch( $_product->get_type() ) {
					case "variable":
					case "variation": // WooCommerce > 5.0
						// Get the post IDs of all the product variations
						$variation_ids = $_product->get_children();
						if ($variation_ids) {
							foreach( $variation_ids as $var_id ) {
								$title = html_entity_decode( get_the_title( $var_id ) );
								if (strpos( strtolower($title), strtolower($search) ) !== false) {
									$return[] = array( $product_row->ID . '|' . $var_id, $title ); // array( Post ID, Post Title )
								}
							}
						}
					break;
					case "grouped":
					case "external":
					default:
						$title = html_entity_decode( get_the_title( $product_row->ID ) );
						if (strpos( strtolower($title), strtolower($search) ) !== false) {
							$return[] = array( $product_row->ID, $title ); // array( Post ID, Post Title )
						}
					break;
				}
			}
			
		}
		// Return results (json)
		echo json_encode( $return );
		wp_die();
		
	}
}

/**
 * Get file size (human readable)
 *
 * @param 	$size 	Size in bytes
 *
 * @return string
 */
if (!function_exists("ilist_display_file_size")) {
	function ilist_display_file_size($size) {
		if ($size >= 1073741824)
			$size = round($size / 1073741824 * 100) / 100 . " Go";
		elseif ($size >= 1048576)
			$size = round($size / 1048576 * 100) / 100 . " Mo";
		elseif ($size >= 1024)
			$size = round($size / 1024 * 100) / 100 . " KO";
		else
			$size = $size . " bytes";
		return $size;
	}
}

/**
 * Get current full url
 *
 * @return string (full url)
 */
if (!function_exists("ilist_current_url")) {
	function ilist_current_url() {
		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	}
}

/**
* Return a string to truncate
* 
* @param string $string
* @param int $max
* @param string $replacement
*
* @return string truncated
*/
if (!function_exists("ilist_truncate")) {
	function ilist_truncate($string, $max = 20, $replacement = '') {
		if (strlen($string) <= $max) {
			return $string;
		}
		$leave = $max - strlen ($replacement);
		return substr_replace($string, $replacement, $leave);
	}
}

/**
* Convert <br /> to linebreaks tags
* 
* @param  string  $text
* @param  string  $replace - Remplacement
*
* @return string
*/
if (!function_exists("ilist_undoNl2Br")) {
	function ilist_undoNl2Br($text, $replace = false) {
		return (!$replace) ? preg_replace("/(\015\012)|(\015)|(\012)/","\n", $text) : preg_replace("/(\015\012)|(\015)|(\012)/", "$replace", $text);
	}
}

/**
* Searches text for unwanted tags and removes them
* 
* @param string $text String to purify
*
* @return string $text The purified text
*/
if (!function_exists("ilist_stopXSS")) {
	function ilist_stopXSS($text) {
		if (!is_array($text)) {
			$text = preg_replace("/\(\)/si", "", $text);
			$text = strip_tags($text);
			$text = str_replace(array("\"",">","<","\\"), "", $text);
		} else {
			foreach($text as $k=>$t) {
				if (is_array($t)) {
					ilist_StopXSS($t);
				} else {
					$t = preg_replace("/\(\)/si", "", $t);
					$t = strip_tags($t);
					$t = str_replace(array("\"",">","<","\\"), "", $t);
					$text[$k] = $t;
				}
			}
		}
		return $text;
	}
}

/**
 * Check whether URL is HTTPS/HTTP
 * 
 * @return boolean [description]
 */
if (!function_exists("ilist_is_secure")) {
	function ilist_is_secure() {
		if (
			( ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
			|| ( ! empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
			|| ( ! empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')
			|| (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
			|| (isset($_SERVER['HTTP_X_FORWARDED_PORT']) && $_SERVER['HTTP_X_FORWARDED_PORT'] == 443)
			|| (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https')
		) {
			return true;
		} else {
			return false;
		}
	}
}

/**
 * Returns a rewrite string
 *
 * @param	$string		String to rewrite
 * 
 * @return string
 */
if (!function_exists("ilist_rewrite_string")) {
	function ilist_rewrite_string($string = "") {
		$noValidString = trim($string);
		$noValidString = preg_replace('`\s+`', '-', trim($noValidString));
		$noValidString = str_replace("'", "-", $noValidString);
		$noValidString = str_replace('"', '-', $noValidString);
		$noValidString = preg_replace('`_+`', '-', trim($noValidString));
		$caracters_in  = array(' ', '?', '!', '.', ',', ':', "'", '&', '(', ')', '-', '/', '%', '=', '+', '[', ']', '~', '"', '{', '}', '|', "`", '@', '$', '£', '*');
		$caracters_out = array('-', '', '', '', '', '-', '-', '-', '', '', '-', '-', '-', '-', '-', '', '', '', '', '', '', '', '-', '-', '-', '-', '');
		$noValidString = str_replace($caracters_in, $caracters_out, $noValidString);
		$noValidString = str_replace("------", "-", $noValidString);
		$noValidString = str_replace("-----", "-", $noValidString);
		$noValidString = str_replace("----", "-", $noValidString);
		$noValidString = str_replace("---", "-", $noValidString);
		$noValidString = str_replace("--", "-", $noValidString);
		$accents       = array('À','Á','Â','Ã','Ä','Å','à','á','â','ã','ä','å','Ò','Ó','Ô','Õ','Ö','Ø','ò','ó','ô','õ','ö','ø','È','É','Ê','Ë','è','é','ê','ë','Ç','ç','Ì','Í','Î','Ï','ì','í','î','ï','Ù','Ú','Û','Ü','ù','ú','û','ü','ÿ','Ñ','ñ');
		$ssaccents     = array('A','A','A','A','A','A','a','a','a','a','a','a','O','O','O','O','O','O','o','o','o','o','o','o','E','E','E','E','e','e','e','e','C','c','I','I','I','I','i','i','i','i','U','U','U','U','u','u','u','u','y','N','n');
		$validString   = str_replace($accents, $ssaccents, $noValidString);
	
		return ($validString);
	}
}

/**
 * Create random string
 *
 * @param 	$length 	Max length string to return (Default: 10)
 * 
 * @return string
 */
if (!function_exists("ilist_rand_sha1")) {
	function ilist_rand_sha1($length = 10) {
		$max = ceil($length / 40);
		$random = '';
		for ($i = 0; $i < $max; $i ++) {
		  $random .= sha1(microtime(true).mt_rand(10000,90000));
		}
		return substr($random, 0, $length);
	}
}

/**
 * Create / Start to Increment Log file
 * 
 * @return string
 */
if (!function_exists("ilist_log_start")) {
	function ilist_log_start() {
		file_put_contents ( __DIR__ . '/log.txt' , "==========================\n", FILE_APPEND);
	}
}

/**
 * Increment Log File
 *
 * @param 	$var 	Variable / Array to add to log
 *
 * @return string
 */
if (!function_exists("ilist_log")) {
	function ilist_log ($var) {
		file_put_contents ( __DIR__ . '/log.txt' , var_export($var , true), FILE_APPEND);
		file_put_contents ( __DIR__ . '/log.txt' , "\n", FILE_APPEND);
	}
}

/**
 * Check locale WP Site
 * 
 * @return LC_TIME (setlocale)
 */
if (!function_exists("ilist_get_locale")) {
	function ilist_get_locale() {
		$ilist_locale_wp = get_locale();
		if ($ilist_locale_wp == 'fr_FR')
			setlocale(LC_TIME, "fr_FR.utf8", "fra");
		else {
			setlocale(LC_TIME, "$ilist_locale_wp.utf8", "fra");
		}
	}
}

?>