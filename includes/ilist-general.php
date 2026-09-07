<?php
/** Blocking direct access to plugin
=================================================== */
defined('ABSPATH') or die('Are you crazy!');

/** Create tab's GENERAL
=============================================== */
if (!function_exists("ilist_general")) {
	function ilist_general() {
		/** INFORMATUX Framework
		=================================================== */
		global $ilist_framework, $Ilist, $wpdb, $wp_version;

		/** Check if WooCommerce is activated
		=============================================== */
		// In multisite WP use this instead Official Way
		$ilist_wc_is_activated = false;
		if ( is_multisite() ) {
			// Multisite activated
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) $ilist_wc_is_activated = true;
		} else {
			// Single Blog mode
			if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', ilist_get_option( 'active_plugins' ) ) ) )  $ilist_wc_is_activated = true;
		}
		// -----------------------------------
		// Check WC activation
		// -----------------------------------
		$ilist_wc_min_alert = "";
		if ( $ilist_wc_is_activated ) {
			// WooCommerce is activated
			$ilist_class_wc              = ( version_compare( ilist_get_woo_version_number(), ILIST_WC_MIN_VERSION, '<' ) ) ? 'ilist-red' : 'ilist-green';
			$ilist_woocommerce_is_active = '<span class="' . $ilist_class_wc . '">' . __( 'Enabled', ILIST_ID_LANGUAGES ) . ' (' . __( 'version', ILIST_ID_LANGUAGES ) . ' ' . ilist_get_woo_version_number() . ')</span>';
			if ($ilist_class_wc == 'ilist-red') $ilist_wc_min_alert = sprintf( __( "Recommended min. version %s", ILIST_ID_LANGUAGES ), ILIST_WC_MIN_VERSION );
		} else {
			// WooCommerce is not activated
			$ilist_woocommerce_is_active = '<span class="ilist-red">' . __( 'Disabled<br>The WooCommerce plugin must be installed and enabled to use this plugin', ILIST_ID_LANGUAGES ) . '</span>';
		}
		// -----------------------------------
		// Compare Worpdress versions
		// -----------------------------------
		$ilist_wordpress_color     = ( version_compare( ILIST_WORDPRESS_MIN_VERSION, $wp_version, '>' ) ) ? "red" : "green";
		$ilist_wordpress_text      = "<span style='color: $ilist_wordpress_color;'>$wp_version</span>";
		$ilist_wordpress_min_alert = ($ilist_wordpress_color == 'red') ? sprintf( __( "Recommended min. version %s", ILIST_ID_LANGUAGES ), ILIST_WORDPRESS_MIN_VERSION ) : "";
		// -----------------------------------
		$icon_wc_wordpress_color = (empty($ilist_wordpress_min_alert) && empty($ilist_wc_min_alert)) ? "success" : "danger";
		// -----------------------------------
		
		// -----------------------------------
		// Multisite
		// -----------------------------------
		$ilist_is_multisite = (ILIST_NETWORK_ACTIVATED) ? __( 'Multisite enabled' , ILIST_ID_LANGUAGES) : __( 'Multisite disabled', ILIST_ID_LANGUAGES );
		
		/** Migration
		=================================================== */
		if (isset($_GET['m']) && $_GET['m'] == 'titan') {
			// Do migration
			$do_migration = ilist_do_migration();
			if ($do_migration) {
				// Migration successful
				?>
				<div class="notice notice-success">
					<p><?php _e( "Great, your ILIST Kado plugin data migration was successful!", ILIST_ID_LANGUAGES ); ?></p>
				</div>
				<?php
			} else {
				// Migration error
				?>
				<div class="notice notice-error">
					<p><?php _e( "An error was encountered while migrating your data!", ILIST_ID_LANGUAGES ); ?></p>
				</div>
				<?php
			}
			?>
			<style>
				#ilist-migration { display: none !important; }
			</style>
			<?php
		}
		
		/** Intialisation
		=============================================== */
		$ilist_page = "general";
		
		/** Title
        =================================================== */
		echo $ilist_framework->general_infos(
			 'start'
			,[
				 'PLUGIN_ID'      => ILIST_ID
				,'PLUGIN_NAME'    => ILIST_NAME
				,'PLUGIN_VERSION' => ilist_get_version()
			  ]);
		
		/** Tabs
        =================================================== */
		echo $ilist_framework->nav_tabs("ilist-$ilist_page", ILIST_ID_LANGUAGES);
		
		/** Initialization STATS
		=================================================== */
		// --- All lists (created)
		$_lists_created    = $wpdb->get_var( "SELECT COUNT(id) FROM " . ILIST_TBL_MAIN );
		$all_created_lists = ($_lists_created > 0) ? $_lists_created : 0;
		// --- All lists (active)
		$_lists_active     = $wpdb->get_var( "SELECT COUNT(id) FROM " . ILIST_TBL_MAIN . " WHERE active = '1'" );
		$all_active_lists  = ($_lists_active > 0) ? $_lists_active : 0;
		// --- Lists created this month
		$month           = date('n'); // Mois sans les zéros initiaux
		$_lists_2         = $wpdb->get_var( "SELECT COUNT(id) FROM " . ILIST_TBL_MAIN . " WHERE MONTH(date) = '$month'" );
		$lists_month      = ($_lists_2 > 0) ? $_lists_2 : 0;
		// --- This month's sales
        $_sales_3         = $wpdb->get_var( "SELECT COUNT(id) FROM " . ILIST_TBL_PRODUCT . " WHERE date != '' AND MONTH(date) = '$month' AND status > 1" );
        $_sales_4         = $wpdb->get_var( "SELECT COUNT(id) FROM " . ILIST_TBL_CAGNOTTE . " WHERE MONTH(date) = '$month'" );
        $sales_month      = ($_sales_3 + $_sales_4 > 0) ? $_sales_3 + $_sales_4 : 0;
		// --- This month's sales (Amount)
        $_sales_5_request = $wpdb->get_results( "SELECT product_id, variation_id FROM " . ILIST_TBL_PRODUCT . " WHERE date != '' AND MONTH(date) = '$month' AND status > 1" );
		$_sales_5 = 0;
		if ( !empty($_sales_5_request) ) {
			foreach( $_sales_5_request as $k => $v ) {
				$product_id = (!empty($v->variation_id)) ? $v->variation_id : $v->product_id;
				$_product   = wc_get_product( $product_id );
				if ($_product) {
					$_sales_5 += $_product->get_price();
				}
			}
		}
        $_sales_6           = $wpdb->get_var( "SELECT SUM(participation) FROM " . ILIST_TBL_CAGNOTTE . " WHERE MONTH(date) = '$month'" );
        $sales_month_amount = ($_sales_5 + $_sales_6 > 0) ? $_sales_5 + $_sales_6 : 0;
		
		/** Content
        =================================================== */		
		echo $ilist_framework->openTable();
		// ----------------------------------------
		$_ilist_limit = 7;
		// ----------------------------------------
		$ilist_all_lists  = ilist_get_lists_limit( $_ilist_limit );
		$ilist_last_lists = "";
		if ( !$ilist_all_lists ) {
			$ilist_last_lists .= __( 'No list created', ILIST_ID_LANGUAGES );
		} else {
			$ilist_last_lists .= '<table class="ilist_widget_table table table-striped">';
			$ilist_last_lists .= '<thead>';
			$ilist_last_lists .= '<tr>';
			$ilist_last_lists .= '<td style="width: 25%; text-align: center;">' . __( 'Date', ILIST_ID_LANGUAGES ) . '</td>';
			$ilist_last_lists .= '<td style="width: 65%;">' . __( 'List Name', ILIST_ID_LANGUAGES ) . '</td>';
			$ilist_last_lists .= '<td style="width: 10%; text-align: center;">' . __( 'Products', ILIST_ID_LANGUAGES ) . '</td>';
			$ilist_last_lists .= '</tr>';
			$ilist_last_lists .= '</thead>';
			$ilist_last_lists .= '<tbody>';
			foreach($ilist_all_lists as $row) {
				$ilist_last_lists .= '<tr>';
				$ilist_last_lists .= '<td style="text-align: center;">';
					$ilist_last_lists .= date_i18n( get_option( 'date_format' ), strtotime( $row->date ) );
				$ilist_last_lists .= '</td>';
				$ilist_last_lists .= '<td>';
					$ilist_last_lists .= '<a href="' . get_admin_url(get_current_blog_id(), 'admin.php?page=ilist_form&action=edit&id=' . $row->id) . '">';
					$ilist_last_lists .= $row->name;
					$ilist_last_lists .= '</a>';
				$ilist_last_lists .= '</td>';
				$ilist_last_lists .= '<td class="ilist_widget_products">';
				$ilist_last_lists .= $row->count_products;
				$ilist_last_lists .= '</td>';
				$ilist_last_lists .= '</tr>';
			}
			$ilist_last_lists .= '</tbody>';
			$ilist_last_lists .= '</table>';
		}
		// ----------------------------------------
		$ilist_last_sold = "";
		$ilist_all_sold = $wpdb->get_results( "SELECT id AS id, list_id AS list_id, order_id AS order_id, date AS date, product_id AS product_id,
											   variation_id AS variation_id, product_name AS product_name, customer AS customer, status AS status, null AS participation
                                               FROM " . ILIST_TBL_PRODUCT . "
                                               WHERE date != '' AND status > 1
                                               UNION ALL
                                               SELECT t1.id AS id, t1.list_id AS list_id, t1.order_id AS order_id, t1.date AS date, t1.product_id AS product_id, t1.variation_id AS variation_id, t1.product_name AS product_name, t1.customer AS customer, null AS status, t1.participation AS participation
                                               FROM " . ILIST_TBL_CAGNOTTE . " AS t1
                                               ORDER BY date DESC LIMIT $_ilist_limit" );
		if ( !$ilist_all_sold ) {
			$ilist_last_sold .= __( 'No product offered', ILIST_ID_LANGUAGES );
		} else {
			$ilist_last_sold .= '<table class="ilist_widget_table table table-striped">';
			$ilist_last_sold .= '<thead>';
			$ilist_last_sold .= '<tr>';
			$ilist_last_sold .= '<td class="text-center">' . __( 'Date', ILIST_ID_LANGUAGES ) . '</td>';
			$ilist_last_sold .= '<td class="text-center">' . __( 'Order number', ILIST_ID_LANGUAGES ) . '</td>';
			$ilist_last_sold .= '<td style="">' . __( 'Product name', ILIST_ID_LANGUAGES ) . '</td>';
			$ilist_last_sold .= '<td style="">' . __( 'List', ILIST_ID_LANGUAGES ) . '</td>';
			$ilist_last_sold .= '<td style="">' . __( 'Customer', ILIST_ID_LANGUAGES ) . '</td>';
			$ilist_last_sold .= '<td class="text-center">' . __( 'Status', ILIST_ID_LANGUAGES ) . '</td>';
			$ilist_last_sold .= '</tr>';
			$ilist_last_sold .= '</thead>';
			$ilist_last_sold .= '<tbody>';
			foreach($ilist_all_sold as $row) {
				$ilist_last_sold .= '<tr>';
				// DATE
				$ilist_last_sold .= '<td class="text-center">';
					$ilist_last_sold .= date_i18n( get_option( 'date_format' ), strtotime( $row->date ) );
				$ilist_last_sold .= '</td>';
				// ORDER NUMBER
				$ilist_last_sold .= '<td class="text-center">';
					$ilist_last_sold .= '<a href="' . get_edit_post_link($row->order_id) . '" title="' . __('See order', ILIST_ID_LANGUAGES) . '" target="_blank">' . $row->order_id . '</a>';
				$ilist_last_sold .= '</td>';
				// PRODUCT NAME
				$ilist_last_sold .= '<td>';
					$_product_id = (isset($row->variation_id) && $row->variation_id > 0) ? $row->variation_id : $row->product_id;
					$ilist_last_sold .= '<a href="' . get_permalink($_product_id) . '" title="' . __('See product', ILIST_ID_LANGUAGES) . '">' . $row->product_name . '</a>';
				$ilist_last_sold .= '</td>';
				// LIST NAME
				$ilist_last_sold .= '<td>';
					$ilist_last_sold .= '<a href="?page=ilist_products&id=' . $row->list_id .'" title="' . __('See list', ILIST_ID_LANGUAGES) . '">' . ilist_get_list_infos_by_id($row->list_id, 'name') . '</a>';
				$ilist_last_sold .= '</td>';
				// CUSTOMER
				$ilist_last_sold .= '<td>';
					if (isset($row->status) && $row->status > 1) {
						if (isset($row->customer) && trim($row->customer) != "")
							$ilist_last_sold .= $row->customer;
						else
							return __('--', ILIST_ID_LANGUAGES);
					} else {
						if (isset($row->date)) {
							$ilist_last_sold .= $row->customer;
						} else {
							$ilist_last_sold .= __('--', ILIST_ID_LANGUAGES);
						}
					}
				$ilist_last_sold .= '</td>';
				// STATUS
				$ilist_last_sold .= '<td class="text-center">';
					$class_color = "";
					$item_status = ilist_get_status_info($row->status, 'title');
					$item_color  = ilist_get_status_info($row->status, 'color');
					switch($row->status) {
						case "":
							$item_status = __( 'Participation', ILIST_ID_LANGUAGES );
							$class_color = 'class="ilist_participation"';
						break;
						case "1":
							$class_color = (!isset($item_color)) ? 'class="buy_shop"' : 'style="color: '.$item_color.'"';
						break;
						case "2":
							$class_color = (!isset($item_color)) ? 'class="buy_internet"' : 'style="color: '.$item_color.'"';
						break;
						default:
							$class_color = (!isset($item_color)) ? 'class="buy_other"' : 'style="color: '.$item_color.'"';
						break;
					}
					$ilist_last_sold .= '<span '.$class_color.'>' . $item_status . '</span>';
				$ilist_last_sold .= '</td>';
				$ilist_last_sold .= '</tr>';
			}
			$ilist_last_sold .= '</tbody>';
			$ilist_last_sold .= '</table>';
		}
		// ----------------------------------------
		$_general  = "";
		$_general .= '<link href="' . FRAMEWORK_ILIST_URL . 'assets/css/framework-'.$ilist_page.'.css" rel="stylesheet">';
		$_general .= '<div class="container-fluid">
						<div class="row">
							<div class="col-xl-12 wid-100">
							
								<!-- ============= -->
								<!-- CHECK / INFOS -->
								<!-- ============= -->
								<div class="row mb-2">
								
									<div class="col-lg-12">
										<div class="card-group">
											<div class="card shadow-sm">
												<div class="row g-0 align-items-center">
													<div class="col-md-4 text-center">
														<i class="fa-rounded-bg fa-solid fa-sliders fa-3x text-bg-' . $icon_wc_wordpress_color . '"></i>
													</div>
													<div class="col-md-8">
														<div class="card-body">
															<h4 class="card-title">WooCommerce</h4>
															<p class="card-text">' . $ilist_woocommerce_is_active . '<br>' . $ilist_wc_min_alert . '</p>
															<h4 class="card-title">Wordpress</h4>
															<p class="card-text">' . $ilist_wordpress_text . '<br>' . $ilist_wordpress_min_alert . '</p>
														</div>
													</div>
												</div>
											</div>
											<div class="card shadow-sm">
												<div class="row g-0 align-items-center">
													<div class="col-md-4 text-center">
														<i class="fa-rounded-bg fa-solid fa-expand fa-3x text-bg-primary" style="padding: 0.5em 0.56em;"></i>
													</div>
													<div class="col-md-8">
														<div class="card-body">
															<h4 class="card-title">Shortcode ' . __('Main page', ILIST_ID_LANGUAGES) . '</h4>
															<p class="card-text"><strong>[ilist]</strong></p>
															<h4 class="card-title">Shortcode ' . __('Search page', ILIST_ID_LANGUAGES) . '</h4>
															<p class="card-text"><strong>[ilist_search]</strong></p>
														</div>
													</div>
												</div>
											</div>
											<div class="card shadow-sm">
												<div class="row g-0 align-items-center">
													<div class="col-md-4 text-center">
														<i class="fa-rounded-bg fa-solid fa-list-ul fa-3x text-bg-warning"></i>
													</div>
													<div class="col-md-8">
														<div class="card-body">
															<h4 class="card-title">' . __( 'All created lists', ILIST_ID_LANGUAGES ) . '</h4>
															<p class="card-text">'.$all_created_lists.'</p>
															<h4 class="card-title">' . __( 'All active lists', ILIST_ID_LANGUAGES ) . '</h4>
															<p class="card-text">'.$all_active_lists.'</p>
														</div>
													</div>
												</div>
											</div>
											<div class="card shadow-sm">
												<div class="row g-0 align-items-center">
													<div class="col-md-4 text-center">
														<i class="fa-rounded-bg fa-solid fa-info fa-3x text-bg-dark" style="padding: 0.5em 0.8em;"></i>
													</div>
													<div class="col-md-8">
														<div class="card-body">
															<h4 class="card-title">' . __( 'Multisite', ILIST_ID_LANGUAGES ) . '</h4>
															<p class="card-text"><span class="ilist-' . (ILIST_NETWORK_ACTIVATED ? 'green' : 'red') . ' ilist-decnone">' . $ilist_is_multisite . '</span></p>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div> <!-- End .col-lg-12 -->
								
								</div> <!-- End .row -->
							
							</div>	
							<div class="col-xl-12 wid-100">
							
								<!-- ============ -->
								<!-- STATISTIQUES -->
								<!-- ============ -->
								<div class="row mb-2">

									<div class="col-lg-4 col-sm-6">
									  <div class="card mw-100 shadow-sm">
										<div class="card-body">
										  <div class="d-flex justify-content-between p-md-1">
											<div class="d-flex flex-row">
											  <div class="align-self-center">
												<i class="fa fa-list-alt text-info fa-3x me-4"></i>
											  </div>
											  <div>
												<h4>' . __( 'Lists created', ILIST_ID_LANGUAGES ) . '</h4>
												<p class="mb-0">' . __( 'Monthly Lists created', ILIST_ID_LANGUAGES ) . '</p>
											  </div>
											</div>
											<div class="align-self-center">
											  <h2 class="h1 mb-0">' . $lists_month . '</h2>
											</div>
										  </div>
										</div>
									  </div>
									</div>
									<div class="col-lg-4 col-sm-6">
									  <div class="card mw-100 shadow-sm">
										<div class="card-body">
										  <div class="d-flex justify-content-between p-md-1">
											<div class="d-flex flex-row">
											  <div class="align-self-center">
												<i class="fa-solid fa-cart-arrow-down text-warning fa-3x me-4"></i>
											  </div>
											  <div>
												<h4>' . __( "Number of sales", ILIST_ID_LANGUAGES ) . '</h4>
												<p class="mb-0">' . __( "Monthly sales number", ILIST_ID_LANGUAGES ) . '</p>
											  </div>
											</div>
											<div class="align-self-center">
											  <h2 class="h1 mb-0">' . $sales_month . '</h2>
											</div>
										  </div>
										</div>
									  </div>
									</div>
									<div class="col-lg-4 col-sm-6">
									  <div class="card mw-100 shadow-sm">
										<div class="card-body">
										  <div class="d-flex justify-content-between p-md-1">
											<div class="d-flex flex-row">
											  <div class="align-self-center">
												<i class="fa-solid fa-arrow-up-wide-short text-success fa-3x me-4"></i>
											  </div>
											  <div>
												<h4>' . __( "Total sales", ILIST_ID_LANGUAGES ) . '</h4>
												<p class="mb-0">' . __( "Monthly total sales", ILIST_ID_LANGUAGES ) . '</p>
											  </div>
											</div>
											<div class="align-self-center">
											  <h2 class="h1 mb-0">' . $Ilist->get_woocommerce_price_format( $sales_month_amount ) . '</h2>
											</div>
										  </div>
										</div>
									  </div>
									</div>
								  
								</div> <!-- End .row -->
							
							</div>	
							<div class="col-xl-12 wid-100">
							
								<!-- ================== -->
								<!-- LAST CREATED LISTS -->
								<!-- ================== -->
								<div class="row mb-2">
						
									<div class="col-lg-4 col-sm-12 mb-4">
										<div class="card h-100 shadow-sm" style="max-width: 100%;">
										  <div class="card-body">
											<h5 class="card-title mb-3"><i class="fa fa-list-alt me-2"></i> ' . __( 'Latest created lists', ILIST_ID_LANGUAGES ) . '</h5>
											<!--<h6 class="card-subtitle mb-2 text-muted">Subtitle</h6>-->
											<p class="card-text">' . $ilist_last_lists . '</p>
											<!--<a href="#" class="card-link">Link</a>-->
											<!--<a href="#" class="card-link">Link</a>-->
										  </div>
										</div>
									</div> <!-- End .col-lg-4 -->
									
									<div class="col-lg-8 col-sm-12">
										<div class="card h-100 shadow-sm" style="max-width: 100%;">
										  <div class="card-body">
											<h5 class="card-title mb-3"><i class="fa-solid fa-shirt me-2"></i> ' . __( 'Latest products offered', ILIST_ID_LANGUAGES ) . '</h5>
											<!--<h6 class="card-subtitle mb-2 text-muted">Subtitle</h6>-->
											<p class="card-text">' . $ilist_last_sold . '</p>
											<!--<a href="#" class="card-link">Link</a>-->
											<!--<a href="#" class="card-link">Link</a>-->
										  </div>
										</div>
									</div> <!-- End .col-lg-8 -->
							
								</div> <!-- End .row -->
							
							</div>	
							<div class="col-xl-12 wid-100 mt-4">
								
								<!-- ============= -->
								<!-- MESSAGE PROMO -->
								<!-- ============= -->
								<div class="row mb-2">
							
									<div class="col-lg-12">
										<div class="card border-primary mw-100 px-0 pt-0 bg-dark bg-gradient text-white shadow-sm">
											<div class="card-header bg-primary">
												' . __( "A custom dashboard", ILIST_ID_LANGUAGES ) . '
											</div>
											<div class="card-body text-dark">
												<p class="card-text text-white fs-5 mb-3">' . __( "Want to add widgets to your dashboard?", ILIST_ID_LANGUAGES ) . '</p>
												<a href="https://doc.ilist-kado.com/helpdesk" class="btn btn-primary text-white">' . __( "Contact us", ILIST_ID_LANGUAGES ) . '</a>
											</div>
										</div>
									</div> <!-- End .col-lg-12 -->
									
								</div> <!-- End .row -->
							
							</div>
							
							<div class="col-xl-12 wid-100 mb-3">
								&nbsp;
							</div>
							
						</div>
					  </div>';
		echo $ilist_framework->addAnything( $_general );
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->closeTable();
		// ----------------------------------------
		
		/** End
        =================================================== */
		echo $ilist_framework->general_infos('end');
		
	}
}
// ----------------------------------------
// ----------------------------------------
// ----------------------------------------
?>