<?php
/** Blocking direct access to plugin
=================================================== */
defined('ABSPATH') or die('Are you crazy!');

/** Create tab's OPTIONS
=============================================== */
if (!function_exists("ilist_options")) {
	function ilist_options() {
		
		/** INFORMATUX Framework
		=================================================== */
		global $ilist_framework, $Ilist;
		
		/** Intialisation
		=============================================== */
		$page     = "options";
		$url_help = $Ilist->helpicon('configuration/options/');
		
		/** Get options
		=============================================== */
		$ilist_main_url_complete   = ilist_get_option( 'ilist_url_complete' );
		$ilist_search_url_complete = ilist_get_option( 'ilist_search_url_complete' );
		
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
		echo $ilist_framework->nav_tabs("ilist-$page", ILIST_ID_LANGUAGES);
		
		/** Form construct
		=================================================== */
		echo $ilist_framework->openForm(
			array(
				 'action'  => admin_url("admin.php?page=ilist-$page")
				,'name'    => "$page"
				,'id'      => "$page"
				,'method'  => "post"
				,'enctype' => "multipart/form-data"
			)
		);
		
		/** Content
		=================================================== */
		echo $ilist_framework->openTable();
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addSubMenu( [
			 __( "Ilists Pages Url (WooCommerce Product page)", ILIST_ID_LANGUAGES )
			,__( "Stock management", ILIST_ID_LANGUAGES )
			,__( "VAT", ILIST_ID_LANGUAGES )
			,__( "Product add method", ILIST_ID_LANGUAGES )
			,__( "Add to my list button CSS class", ILIST_ID_LANGUAGES )
			,__( "Correction of urls", ILIST_ID_LANGUAGES )
			,__( "Shortcodes behavior", ILIST_ID_LANGUAGES )
			,__( "ILIST Log", ILIST_ID_LANGUAGES )
		] );
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Ilists Pages Url (WooCommerce Product page)", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addNote( __( 'Visible in the Wordpress dashboard', ILIST_ID_LANGUAGES ) . '<br><a style="font-weight: normal;" href="' . get_admin_url(get_current_blog_id()) . '">' . __( 'Go to dashboard', ILIST_ID_LANGUAGES ) . '</a>', '<img src="' . ILIST_URL . 'images/help/help-dashboard-widget-wp.png" alt="Live search" />' );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'WP Dashboard Widget', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_widget_dashboard_summary'
				,'name'    => 'ilist_widget_dashboard_summary'
				,'checked' => ilist_get_option('ilist_widget_dashboard_summary', '1') // Default: 1
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'Default: enabled', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		$value_ilist_url_complete = (isset($ilist_main_url_complete) && $ilist_main_url_complete != '') ? $ilist_main_url_complete : ( (get_permalink(ilist_get_option( 'ilist_url_complete_id'))) ? esc_url( @get_page_link( ilist_get_option( 'ilist_url_complete_id') ) ) : false );
		echo $ilist_framework->addInput(
			 'text'
			,__( "Url of the main page list", ILIST_ID_LANGUAGES )
			,array(
				 'name'        => 'ilist_url_complete'
				,'id'          => 'ilist_url_complete'
				,'value'       => "$value_ilist_url_complete"
				,'placeholder' => ""
				,'style'       => 'width: 100%; max-width: 400px;'
			)
			,true
			,'Ex: ' . get_site_url() . '/mes-listes/' // Description
		);
		// ----------------------------------------
		$value_ilist_search_url_complete = (isset($ilist_search_url_complete) && $ilist_search_url_complete != '') ? $ilist_search_url_complete : ( (get_permalink(ilist_get_option( 'ilist_search_url_complete_id'))) ? esc_url( @get_page_link( ilist_get_option( 'ilist_search_url_complete_id') ) ) : false );
		echo $ilist_framework->addInput(
			 'text'
			,__( "Url of the search page list", ILIST_ID_LANGUAGES )
			,array(
				 'name'        => 'ilist_search_url_complete'
				,'id'          => 'ilist_search_url_complete'
				,'value'       => "$value_ilist_search_url_complete"
				,'placeholder' => ""
				,'style'       => 'width: 100%; max-width: 400px;'
			)
			,true
			,'Ex: ' . get_site_url() . '/rechercher-une-liste/' // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Stock management", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Stock management when adding and removing a product from a list', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_stock_management'
				,'name'    => 'ilist_stock_management'
				,'checked' => ilist_get_option('ilist_stock_management', '0') // Default: 0
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'Default: disabled', ILIST_ID_LANGUAGES ) . '<br>' . __("If the option is activated, a product added to a list will decrease its stock and if it is removed from the list, the stock will be increased.", ILIST_ID_LANGUAGES) . '<br><span style="color: grey; font-style: italic; font-weight: normal; font-size: 1.2em;"><img src="' . ILIST_URL . 'images/ilist-warning-icon.png" style="vertical-align: middle;" alt="">&nbsp;' . __("This option is active if stock management is effective on the added and / or deleted product.", ILIST_ID_LANGUAGES) . '</span>' // Description
		);
		// ----------------------------------------
		//echo $ilist_framework->addRadioYN(
		//	 __( 'Add products out of stock', ILIST_ID_LANGUAGES )
		//	,true
		//	,array(
		//		 'id'      => 'ilist_stock_backorder'
		//		,'name'    => 'ilist_stock_backorder'
		//		,'checked' => ilist_get_option('ilist_stock_backorder', '0') // Default: 0
		//	)
		//	,__( 'Enabled', ILIST_ID_LANGUAGES )
		//	,__( 'Disabled', ILIST_ID_LANGUAGES )
		//	,__( 'Default: disabled', ILIST_ID_LANGUAGES ) . '<br><i>' . __("If you enable this option, list creators will be able to add out-of-stock products to their list", ILIST_ID_LANGUAGES) . '</i>' // Description
		//);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "VAT", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		$tab_add_vat_method  = [];
		$tab_add_vat_methods = array(
			 ['id' => '1', 'title' => __( 'Price with VAT', ILIST_ID_LANGUAGES )]
			,['id' => '0', 'title' => __( 'Price without VAT', ILIST_ID_LANGUAGES )]
		);
		foreach($tab_add_vat_methods as $key => $val) {
			if (ilist_get_option('ilist_ttc_product_in_list') == $val['id']) $add_vat_method_checked = $val['id'];
			$tab_add_vat_method[$key]['text']  = $val['title'];
			$tab_add_vat_method[$key]['value'] = $val['id'];
		}
		echo $ilist_framework->addRadio(
			 __( 'If VAT is activated, display the price of the product including VAT in lists', ILIST_ID_LANGUAGES )
			,$tab_add_vat_method
			,array(
				 'id'      => 'ilist_ttc_product_in_list'
				,'name'    => 'ilist_ttc_product_in_list'
				,'checked' => "$add_vat_method_checked" // Default: 0
			)
			,true
			,'<br>'
			,__( 'Default: Price without VAT', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Product add method", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		$tab_add_method  = [];
		$tab_add_methods = array(
			 ['id' => 'post', 'title' => 'POST']
			,['id' => 'get',  'title' => 'GET']
						   );
		foreach($tab_add_methods as $key => $val) {
			if (ilist_get_option('ilist_product_add_method') == $val['id']) $add_method_checked = $val['id'];
			$tab_add_method[$key]['text']  = $val['title'];
			$tab_add_method[$key]['value'] = $val['id'];
		}
		echo $ilist_framework->addRadio(
			 __( 'Product add method in product detail page', ILIST_ID_LANGUAGES )
			,$tab_add_method
			,array(
				 'id'      => 'ilist_product_add_method'
				,'name'    => 'ilist_product_add_method'
				,'checked' => "$add_method_checked"
			)
			,true
			,'<br>'
			,__( 'First method - Default: POST', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Add to my list button CSS class", ILIST_ID_LANGUAGES ) );
		$_ilist_add_to_my_list_button_class = '<span style="color: red;">single_add_to_cart_button</span>';
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 sprintf( __( "Remove %s class from buttons add to my list from a product detail page", ILIST_ID_LANGUAGES ), $_ilist_add_to_my_list_button_class )
			,true
			,array(
				 'id'      => 'ilist_add_to_my_list_button_class'
				,'name'    => 'ilist_add_to_my_list_button_class'
				,'checked' => ilist_get_option('ilist_add_to_my_list_button_class', '0') // Default: 0
			)
			,__( 'Yes, remove class', ILIST_ID_LANGUAGES )
			,__( 'No, keep class', ILIST_ID_LANGUAGES )
			,__( 'Default: No, keep class', ILIST_ID_LANGUAGES ) . '<br><i>' . sprintf( __("If you enable this option (remove), the Add to my list button class %s will be removed on product detail page. Use if you have issue with this button.", ILIST_ID_LANGUAGES), $_ilist_add_to_my_list_button_class ) . '</i>' // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Correction of urls", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Add a "/" at the end of the ILIST plugin urls', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_rewrite_url'
				,'name'    => 'ilist_rewrite_url'
				,'checked' => ilist_get_option('ilist_rewrite_url', '0') // Default: 0
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'Default: disabled', ILIST_ID_LANGUAGES ) . '<br>' . '<span style="color: grey; font-style: italic; font-weight: normal;">' . __( 'Error encountered when using SEO plugins', ILIST_ID_LANGUAGES ) . '</span>' // Description
		);
		// ----------------------------------------
		// ----------------------------------------		
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Shortcodes behavior", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Display shortcodes by template', ILIST_ID_LANGUAGES )
			,true // Required: true OR false
			,array(
				 'id'      => 'ilist_shorcode_bytpl'
				,'name'    => 'ilist_shorcode_bytpl'
				,'checked' => ilist_get_option('ilist_shorcode_bytpl', '0') // Default: 0
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'No - Default: disabled', ILIST_ID_LANGUAGES ) . "<br>" . __( "Option to activate only if display issue", ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "ILIST Log", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Log Activation', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_debug_log'
				,'name'    => 'ilist_debug_log'
				,'checked' => ilist_get_option('ilist_debug_log', '0') // Default: 0
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'No - Default: disabled', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		$ilist_file_path_log = ILIST_PATH . 'log.txt';
		if (file_exists($ilist_file_path_log) && ilist_get_option( 'ilist_debug_log' )) {
			// Le journal est lu côté serveur et affiché échappé. Auparavant une
			// <iframe> pointait sur ILIST_URL . 'log.txt', c'est-à-dire une URL
			// publique et prévisible : le fichier, qui contient des données de
			// commandes et de listes, était lisible par n'importe qui, y compris
			// après désactivation de l'option (il n'est jamais purgé).
			// L'accès HTTP direct est désormais refusé par le .htaccess du plugin.
			$ilist_log_contents = file_get_contents( $ilist_file_path_log );
			if ($ilist_log_contents === false) $ilist_log_contents = '';
			echo $ilist_framework->addNote(
				 __( 'Log file', ILIST_ID_LANGUAGES )
				,'<pre style="width:100%; height:400px; overflow:auto; border:1px solid black; margin:0; padding:.5em; background:#fff;">' . esc_html( $ilist_log_contents ) . '</pre>'
			);
		} else {
			echo $ilist_framework->addNote( __( 'Log File', ILIST_ID_LANGUAGES ), ( (ilist_get_option( 'ilist_debug_log' )) ? __( 'No log file', ILIST_ID_LANGUAGES ) : __( 'Logs disabled', ILIST_ID_LANGUAGES ) ) );
		}
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		// --- Hiddens / Buttons
		// ----------------------------------------
		echo $ilist_framework->addInput('submit', '', array('value' => __( "Save changes", ILIST_ID_LANGUAGES)));
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->closeTable();
		echo $ilist_framework->closeForm();
		// ----------------------------------------
		
		/** End
        =================================================== */
		echo $ilist_framework->general_infos('end');
	}
}

/** Add Widgets in Dashboard (Ilist Summary Widget)
=================================================== */
if ( ! function_exists("ilist_widget_admin_function") ) {
	function ilist_widget_admin_function( $post, $callback_args ) {
		$ilist_all_lists = ilist_get_lists_limit();
		if ( ! $ilist_all_lists ) {
			echo __( 'No lists created', ILIST_ID_LANGUAGES );
		} else {
			?>
			<style>
				.ilist_widget_p {
					display: flex;
					align-items:center;
				}
				.ilist_widget_p {
					font-style: italic;
					margin: 0 0 1em 0;
					color: #72777c;
				}
				.ilist_widget_p img {
					margin: 0 0.5em 0 0;
				}
				table.ilist_widget_table {
					border-top: 1px solid #ddd;
					border-left: 1px solid #ddd;
					border-right: 1px solid #ddd;
					width: 100%;
					border-spacing: 0;
				}
				table.ilist_widget_table thead td {
					font-weight: bold;
					background-color: lightgoldenrodyellow;
				}
				table.ilist_widget_table td {
					border-bottom: 1px solid #ddd;
					padding: 4px;
				}
				.ilist_widget_products {
					text-align: center;
					font-weight: bold;
				}
			</style>
			<?php
			echo '<p class="ilist_widget_p"><img src="'.ILIST_URL.'images/icon-ilist-25.png" alt="ILIST">' . __( 'Last created lists', ILIST_ID_LANGUAGES ) . '</p>';
			echo '<table class="ilist_widget_table">';
			echo '<thead>';
			echo '<tr>';
			echo '<td style="width: 20%; text-align: center;">' . __( 'Date', ILIST_ID_LANGUAGES ) . '</td>';
			echo '<td style="width: 65%;">' . __( 'List Name', ILIST_ID_LANGUAGES ) . '</td>';
			echo '<td style="width: 15%;">' . __( 'Products', ILIST_ID_LANGUAGES ) . '</td>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			foreach($ilist_all_lists as $row) {
				echo '<tr>';
				echo '<td style="text-align: center;">';
					echo date_i18n( get_option( 'date_format' ), strtotime( $row->date ) );
				echo '</td>';
				echo '<td>';
					echo '<a href="' . get_admin_url(get_current_blog_id(), 'admin.php?page=ilist_form&action=edit&id=' . $row->id) . '">';
					echo $row->name;
					echo '</a>';
				echo '</td>';
				echo '<td class="ilist_widget_products">';
				echo $row->count_products;
				echo '</td>';
				echo '</tr>';
			}
			echo '</tbody>';
			echo '</table>';
		}
	}
}

/** Check if widget is enabled
=================================================== */
if ( ilist_get_option( 'ilist_widget_dashboard_summary' ) ) {
	add_action('wp_dashboard_setup', 'add_ilist_widgets');
	if ( ! function_exists("add_ilist_widgets") ) {
		function add_ilist_widgets() {
			if ( ilist_get_option( 'ilist_widget_dashboard_summary' ) ) {
				wp_add_dashboard_widget(
					'ilist_widget_admin',
					__( 'Ilist KADO', ILIST_ID_LANGUAGES ),
					'ilist_widget_admin_function'
				);
			}
		}
	}
}

?>