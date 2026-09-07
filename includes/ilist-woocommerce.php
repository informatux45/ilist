<?php
/** Blocking direct access to plugin
=================================================== */
defined('ABSPATH') or die('Are you crazy!');

/** Create tab's CREDITS
=============================================== */
if (!function_exists("ilist_woocommerce")) {
	function ilist_woocommerce() {
		/** INFORMATUX Framework
		=================================================== */
		global $ilist_framework, $Ilist;
        
		/** Intialisation
		=============================================== */
		$page     = "woocommerce";
		$url_help = $Ilist->helpicon('configuration/woocommerce/');
		
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
			 __( "Orders table", ILIST_ID_LANGUAGES )
			,__( "Order detail", ILIST_ID_LANGUAGES )
			,__( "Hide comment on checkout page", ILIST_ID_LANGUAGES )
			,__( "WooCommerce canceled orders action", ILIST_ID_LANGUAGES )
			,__( "WooCommerce Status", ILIST_ID_LANGUAGES )
			,
		] );
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Orders table", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addNote( __( 'Orders list', ILIST_ID_LANGUAGES ), '<img src="' . ILIST_URL . 'images/help/help-wc-commandes.jpg" alt="WooCommerce" style="max-width: 100%;" />' );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Display list column', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_wc_display_table_list_column'
				,'name'    => 'ilist_wc_display_table_list_column'
				,'checked' => ilist_get_option('ilist_wc_display_table_list_column', '1') // Default: 1
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'Yes - Default: enabled', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Order detail", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addNote( __( 'Order detail', ILIST_ID_LANGUAGES ), '<img src="' . ILIST_URL . 'images/help/help-wc-detail.jpg" alt="WooCommerce" style="max-width: 100%;" />' );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Display order detail', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_wc_display_detail_order'
				,'name'    => 'ilist_wc_display_detail_order'
				,'checked' => ilist_get_option('ilist_wc_display_detail_order', '1') // Default: 1
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'Yes - Default: enabled', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Hide comment on checkout page", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Hide Leave a message on the checkout page when there is no product from a list in cart', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_hide_comment'
				,'name'    => 'ilist_hide_comment'
				,'checked' => ilist_get_option('ilist_hide_comment', '1') // Default: 1
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'Yes - Default: enabled', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "WooCommerce canceled orders action", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'This option allows products to be put back on sale when an order goes to Canceled or Failed status', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_woocommerce_cancelled_action'
				,'name'    => 'ilist_woocommerce_cancelled_action'
				,'checked' => ilist_get_option('ilist_woocommerce_cancelled_action', '0') // Default: 0
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'No - Default: disabled', ILIST_ID_LANGUAGES ) . '<br>' . __( 'This option will put the products back on sale and remove the participations', ILIST_ID_LANGUAGES )  // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		$all_wc_order_statuses = wc_get_order_statuses();
		// array (size=8)
		// 'wc-pending' => string 'Attente paiement' (length=16)
		// 'wc-processing' => string 'En cours' (length=8)
		// 'wc-on-hold' => string 'En attente' (length=10)
		// 'wc-completed' => string 'Terminée' (length=9)
		// 'wc-cancelled' => string 'Annulée' (length=8)
		// 'wc-refunded' => string 'Remboursée' (length=11)
		// 'wc-failed' => string 'Échouée' (length=9)
		// 'wc-checkout-draft' => string 'Brouillon' (length=9)
		// ----------------------------------------
		$get_order_statuses_cancelled = ilist_get_option('ilist_order_statuses_cancelled_action_default');
		$order_statuses_cancelled     = '';
		// ----------------------------------------
		$order_statuses_cancelled .= '<select name="ilist_order_statuses_cancelled_action_default[]" id="ilist_order_statuses_cancelled_action_default" multiple="multiple" class="ilist-select-2-ads">';
			$order_statuses_cancelled .= '<option value="">&mdash; ' . __('Choose', ILIST_ID_LANGUAGES) . ' &mdash;</option>';
			foreach ($all_wc_order_statuses as $key => $value) {
				$key = str_replace("wc-", "", $key);
				$order_statuses_cancelled .= '<option value="'.$key.'" '; // Hide prefix "wc-"
				if ( is_array($get_order_statuses_cancelled) && in_array( $key, $get_order_statuses_cancelled ) && array_keys( $get_order_statuses_cancelled, true ) ) $order_statuses_cancelled .= 'selected="selected"';
				$order_statuses_cancelled .= '>';
				$order_statuses_cancelled .= ucfirst($value);
				$order_statuses_cancelled .= '</option>';
			}
		$order_statuses_cancelled .= '</select>';
		// ----------------------------------------
		echo $ilist_framework->addNote(
			 __( "Canceled order: Choose the statuses you want to use", ILIST_ID_LANGUAGES )
			,'<link href="' . ILIST_URL . 'vendor/Select2/select2.min.css" rel="stylesheet">
				<style>.select2-container { width: 500px !important; }</style>
				<script src="' . ILIST_URL . 'vendor/Select2/select2.min.js"></script>
				
				' . $order_statuses_cancelled . '

				<br><em class="description">
					' . __( 'The "WooCommerce canceled orders action" option must be enabled to use your choices' , ILIST_ID_LANGUAGES ) . '
					<br>
					' . sprintf( __( 'If you do not choose a status and if option is activated, these statuses will be used: "%s" / "%s"', ILIST_ID_LANGUAGES ), $all_wc_order_statuses['wc-cancelled'], $all_wc_order_statuses['wc-failed'] ) . '
				</em>
				<script>
					jQuery(function($) {
						$("#ilist_order_statuses_cancelled_action_default").select2();
					});
				</script>'
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "WooCommerce Status", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		$ilist_get_order_statuses_send_notification = ilist_get_option('ilist_order_statuses_send_notification_default');
		$ilist_order_statuses_send_notification     = '';
		// ----------------------------------------
		$ilist_order_statuses_send_notification .= '<select name="ilist_order_statuses_send_notification_default[]" id="ilist_order_statuses_send_notification_default" multiple="multiple" class="ilist-select-2-ads">';
			$ilist_order_statuses_send_notification .= '<option value="">&mdash; ' . __('Choose', ILIST_ID_LANGUAGES) . ' &mdash;</option>';
			foreach ($all_wc_order_statuses as $key => $value) {
				$key = str_replace("wc-", "", $key);
				$ilist_order_statuses_send_notification .= '<option value="'.$key.'" '; // Hide prefix "wc-"
				if ( is_array($ilist_get_order_statuses_send_notification) && in_array( $key, $ilist_get_order_statuses_send_notification ) && array_keys( $ilist_get_order_statuses_send_notification, true ) ) $ilist_order_statuses_send_notification .= 'selected="selected"';
				$ilist_order_statuses_send_notification .= '>';
				$ilist_order_statuses_send_notification .= ucfirst($value);
				$ilist_order_statuses_send_notification .= '</option>';
			}
		$ilist_order_statuses_send_notification .= '</select>';
		// ----------------------------------------
		echo $ilist_framework->addNote(
			 __( "Completed order: Choose the statuses you want to use for sending the notification", ILIST_ID_LANGUAGES )
			,$ilist_order_statuses_send_notification . '

				<br><em class="description">
					' . sprintf( __( 'If you do not choose a status, the "%s" status will be used', ILIST_ID_LANGUAGES ), $all_wc_order_statuses['wc-completed'] ) . '
				</em>
				<script>
					jQuery(function($) {
						$("#ilist_order_statuses_send_notification_default").select2();
					});
				</script>'
		);
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
// ----------------------------------------
// ----------------------------------------
// ----------------------------------------
?>