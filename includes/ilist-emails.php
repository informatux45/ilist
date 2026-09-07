<?php
/** Blocking direct access to plugin
=================================================== */
defined('ABSPATH') or die('Are you crazy!');

/** Create tab's POT
=============================================== */
if (!function_exists("ilist_emails")) {
	function ilist_emails() {
		
		/** INFORMATUX Framework
		=================================================== */
		global $ilist_framework, $Ilist;
		
		/** Intialisation
		=============================================== */
		$ilist_page = "emails";
		$url_help   = $Ilist->helpicon('configuration/emails-automatiques/');
        
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
		echo $ilist_framework->nav_tabs('ilist-'.$ilist_page, ILIST_ID_LANGUAGES);
		
		/** Form construct
		=================================================== */
		echo $ilist_framework->openForm(
			array(
				 'action'  => admin_url("admin.php?page=ilist-$ilist_page")
				,'name'    => "$ilist_page"
				,'id'      => "$ilist_page"
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
			 __( "Email header", ILIST_ID_LANGUAGES )
			,__( "Email(s)", ILIST_ID_LANGUAGES )
			,__( "Email new order (WooCommerce)", ILIST_ID_LANGUAGES )
			,__( "Automatic emails (Subject / Body) - When a list is created (Admin only)", ILIST_ID_LANGUAGES )
			,__( "Automatic emails (Subject / Body) - Creator of the list", ILIST_ID_LANGUAGES )
			,__( 'Automatic emails (Subject / Body) - Administrators', ILIST_ID_LANGUAGES )
		] );
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
        echo $ilist_framework->addBreak( $url_help . __( "Email header", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addInput(
			 'text'
			,__( "Administrator Response Email", ILIST_ID_LANGUAGES )
			,array(
				 'name'        => 'ilist_email_admin_response'
				,'id'          => 'ilist_email_admin_response'
				,'value'       => ilist_get_option('ilist_email_admin_response', 'noreply@' . str_replace("www.", "", $_SERVER['SERVER_NAME'])) // Default: 'no-reply@' . str_replace("www.", "", $_SERVER['SERVER_NAME'])
				,'placeholder' => ''
				,'style'       => 'width: 100%; max-width: 400px;'
			)
			,true
			,'' // Description
		);
		// ----------------------------------------
		echo $ilist_framework->addInput(
			 'text'
			,__( "Name of Administrator", ILIST_ID_LANGUAGES )
			,array(
				 'name'        => 'ilist_email_admin_name'
				,'id'          => 'ilist_email_admin_name'
				,'value'       => ilist_get_option('ilist_email_admin_name', 'Admin ' . get_bloginfo( 'name' )) // Default: 'Admin ' . get_bloginfo( 'name' )
				,'placeholder' => ''
				,'style'       => 'width: 100%; max-width: 400px;'
			)
			,true
			,'' // Description
		);
		// ----------------------------------------
        echo $ilist_framework->addBreak( $url_help . __( "Email(s)", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addInput(
			 'text'
			,__( "Administrator(s) Email(s)", ILIST_ID_LANGUAGES )
			,array(
				 'name'        => 'ilist_email_admin'
				,'id'          => 'ilist_email_admin'
				,'value'       => ilist_get_option('ilist_email_admin', ilist_get_option( 'admin_email' )) // Default: ilist_get_option( 'admin_email' )
				,'placeholder' => ''
				,'style'       => 'width: 100%; max-width: 400px;'
			)
			,true
			,__( "If multiple emails, separate them with commas without space", ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
        echo $ilist_framework->addBreak( $url_help . __( "Email new order (WooCommerce)", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addInput(
			 'text'
			,__( 'Email subject', ILIST_ID_LANGUAGES ) . "<br><span class='ilist-style-italic ilist-style-description'>" . __( "Will be added to the subject of a new order email if completed and if the order is for a purchase made on a list", ILIST_ID_LANGUAGES ) . "</span>"
			,array(
				 'name'        => 'ilist_email_subject_wc_new_order'
				,'id'          => 'ilist_email_subject_wc_new_order'
				,'value'       => ilist_get_option('ilist_email_subject_wc_new_order', '') // Default: void
				,'placeholder' => 'Ex: Liste {ILIST_NAME}'
				,'style'       => 'width: 100%; max-width: 400px;'
			)
			,false
			,__( "Variable to place:", ILIST_ID_LANGUAGES ) . "<br><span style='font-weight: bold;'>{ILIST_NAME}</span>: " . __( "List name", ILIST_ID_LANGUAGES ) . "<br>" // Description
		);
		// ----------------------------------------
        echo $ilist_framework->addBreak( $url_help . __( "Automatic emails (Subject / Body) - When a list is created (Admin only)", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		global $Ilist;
		echo $ilist_framework->addRadioYN(
			 __( 'Alert admin(s) when a list is created', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_email_created_list_is_active'
				,'name'    => 'ilist_email_created_list_is_active'
				,'checked' => ilist_get_option('ilist_email_created_list_is_active', '1') // Default: 1
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'Yes - Default: enabled', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		echo $ilist_framework->addInput(
			 'text'
			,__( 'Subject (When a list is created)', ILIST_ID_LANGUAGES )
			,array(
				 'name'        => 'ilist_email_subject_alert_created_list'
				,'id'          => 'ilist_email_subject_alert_created_list'
				,'value'       => ilist_get_option('ilist_email_subject_alert_created_list', __('A new list is created ({ILIST_NAME})', ILIST_ID_LANGUAGES )) // Default: __('A new list is created ({ILIST_NAME})', ILIST_ID_LANGUAGES )
				,'placeholder' => ''
				,'style'       => 'width: 100%; max-width: 400px;'
			)
			,true
			,__( "Variable to place:", ILIST_ID_LANGUAGES ) . "<br><span style='font-weight: bold;'>{ILIST_NAME}</span>: " . __( "List name", ILIST_ID_LANGUAGES ) . "<br>" // Description
		);
		// ----------------------------------------
        $ilist_email_body_alert_created_list_default = __( 'Hello,<br><br>A new list has just been created on your website:<br><br>List Name: {ILIST_NAME}<br><br>By: {ILIST_CREATOR}<br>Date: {ILIST_DATE}<br><br>{ILIST_WP_SITE_NAME} {ILIST_WP_SITE_DESC}', ILIST_ID_LANGUAGES );
		echo $ilist_framework->addEditor(
			 __( "Email body (When a list is created)", ILIST_ID_LANGUAGES )
			,array(
				 'id'    => 'ilist_email_body_alert_created_list'
				,'name'  => 'ilist_email_body_alert_created_list'
				,'value' => ilist_get_option('ilist_email_body_alert_created_list', $ilist_email_body_alert_created_list_default) // Default: __( 'Hello,<br><br>A new list has just been created on your website:<br><br>List Name: {ILIST_NAME}<br><br>By: {ILIST_CREATOR}<br>Date: {ILIST_DATE}<br><br>{ILIST_WP_SITE_NAME} {ILIST_WP_SITE_DESC}', ILIST_ID_LANGUAGES )
				,'media_buttons' => true   // Whether to show the Add Media/other media buttons
				,'wpautop'       => true   // Whether to use wpautop()
				,'textarea_rows' => 20     // Number rows in the editor textarea
				,'teeny'         => false  // Whether to output the minimal editor config
			)
			,true // Required: true OR false
			,__( "Variables to place:", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_NAME}</span>: " . __( "List name (Creator of the list)", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_CREATOR}</span>: " . __( "Creator of the list", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_DATE}</span>: " . __( "Order date", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_WP_SITE_NAME}</span>: " . __( "Wordpress Site Name", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_WP_SITE_DESC}</span>: " . __( "Wordpress Site Description", ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
        echo $ilist_framework->addBreak( $url_help . __( "Automatic emails (Subject / Body) - Creator of the list", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addInput(
			 'text'
			,__( 'Subject (Creator of the list)', ILIST_ID_LANGUAGES )
			,array(
				 'name'        => 'ilist_email_subject_alert_one'
				,'id'          => 'ilist_email_subject_alert_one'
				,'value'       => ilist_get_option('ilist_email_subject_alert_one', __('A product purchased on one of your lists ({ILIST_NAME})', ILIST_ID_LANGUAGES )) // Default: __('A product purchased on one of your lists ({ILIST_NAME})', ILIST_ID_LANGUAGES )
				,'placeholder' => ''
				,'style'       => 'width: 100%; max-width: 400px;'
			)
			,true
			,__( "Variable to place:", ILIST_ID_LANGUAGES ) . "<br><span style='font-weight: bold;'>{ILIST_NAME}</span>: " . __( "List name", ILIST_ID_LANGUAGES ) . "<br>" // Description
		);
		// ----------------------------------------
        $ilist_email_body_alert_one_default = __( 'Hello,<br><br>A product (or more) has just been purchased on one of your lists:<br><br>List Name: {ILIST_NAME}<br>Product(s) Name: {ILIST_PRODUCT}<br>By: {ILIST_FRIEND}<br>Date: {ILIST_DATE}<br>Purchase Type: {ILIST_TYPE}<br>Comment: {ILIST_COMMENT}<br><br>{ILIST_WP_SITE_NAME} {ILIST_WP_SITE_DESC}<br>See you soon', ILIST_ID_LANGUAGES );
		echo $ilist_framework->addEditor(
			 __( "Email body (Creator of the list)", ILIST_ID_LANGUAGES )
			,array(
				 'id'    => 'ilist_email_body_alert_one'
				,'name'  => 'ilist_email_body_alert_one'
				,'value' => ilist_get_option('ilist_email_body_alert_one', $ilist_email_body_alert_one_default) // Default: __( 'Hello,<br><br>A product (or more) has just been purchased on one of your lists:<br><br>List Name: {ILIST_NAME}<br>Product(s) Name: {ILIST_PRODUCT}<br>By: {ILIST_FRIEND}<br>Date: {ILIST_DATE}<br>Purchase Type: {ILIST_TYPE}<br>Comment: {ILIST_COMMENT}<br><br>{ILIST_WP_SITE_NAME} {ILIST_WP_SITE_DESC}<br>See you soon', ILIST_ID_LANGUAGES )
				,'media_buttons' => true   // Whether to show the Add Media/other media buttons
				,'wpautop'       => true   // Whether to use wpautop()
				,'textarea_rows' => 20     // Number rows in the editor textarea
				,'teeny'         => false  // Whether to output the minimal editor config
			)
			,true // Required: true OR false
			,__( "Variables to place:", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_NAME}</span>: " . __( "List name (Creator of the list)", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_PRODUCT}</span>: " . __( "Name of the offered product(s)", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_FRIEND}</span>: " . __( "Offered by", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_DATE}</span>: " . __( "Order date", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_TYPE}</span>: " . __( "Purchase Type", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_COMMENT}</span>: " . __( "Comment", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_WP_SITE_NAME}</span>: " . __( "Wordpress Site Name", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_WP_SITE_DESC}</span>: " . __( "Wordpress Site Description", ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
        echo $ilist_framework->addBreak( $url_help . __( 'Automatic emails (Subject / Body) - Administrators', ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addInput(
			 'text'
			,__( 'Subject (Administrator(s))', ILIST_ID_LANGUAGES )
			,array(
				 'name'        => 'ilist_email_subject_alert_one_admin'
				,'id'          => 'ilist_email_subject_alert_one_admin'
				,'value'       => ilist_get_option('ilist_email_subject_alert_one_admin', __( 'A product purchased on the list ({ILIST_NAME})', ILIST_ID_LANGUAGES )) // Default: __( 'A product purchased on the list ({ILIST_NAME})', ILIST_ID_LANGUAGES )
				,'placeholder' => ''
				,'style'       => 'width: 100%; max-width: 400px;'
			)
			,true
			,__( "Variable to place:", ILIST_ID_LANGUAGES ) . "<br><span style='font-weight: bold;'>{ILIST_NAME}</span>: " . __( "List name", ILIST_ID_LANGUAGES ) . "<br>" // Description
		);
		// ----------------------------------------
        $ilist_email_body_alert_one_admin_default = __( 'Hello,<br><br>A product (or more) has just been purchased from a list:<br><br>List: {ILIST_NAME}<br>Product(s) Name: {ILIST_PRODUCT}<br>By: {ILIST_FRIEND}<br>Date: {ILIST_DATE}<br>Purchase Type: {ILIST_TYPE}<br>Order number: {ILIST_ORDER_ID}<br>Comment: {ILIST_COMMENT}<br><br>The administrator {ILIST_WP_SITE_NAME} {ILIST_WP_SITE_DESC}', ILIST_ID_LANGUAGES );
		echo $ilist_framework->addEditor(
			 __( "Email body (Administrator(s))", ILIST_ID_LANGUAGES )
			,array(
				 'id'    => 'ilist_email_body_alert_one_admin'
				,'name'  => 'ilist_email_body_alert_one_admin'
				,'value' => ilist_get_option('ilist_email_body_alert_one_admin', $ilist_email_body_alert_one_admin_default) // Default: __( 'Hello,<br><br>A product (or more) has just been purchased from a list:<br><br>List: {ILIST_NAME}<br>Product(s) Name: {ILIST_PRODUCT}<br>By: {ILIST_FRIEND}<br>Date: {ILIST_DATE}<br>Purchase Type: {ILIST_TYPE}<br>Order number: {ILIST_ORDER_ID}<br>Comment: {ILIST_COMMENT}<br><br>The administrator {ILIST_WP_SITE_NAME} {ILIST_WP_SITE_DESC}', ILIST_ID_LANGUAGES )
				,'media_buttons' => true  // Whether to show the Add Media/other media buttons
				,'wpautop'       => true  // Whether to use wpautop()
				,'textarea_rows' => 20    // Number rows in the editor textarea
				,'teeny'         => false // Whether to output the minimal editor config
			)
			,true // Required: true OR false
			,__( "Variables to place:", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_NAME}</span>: " . __( "List name", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_PRODUCT}</span>: " . __( "Name of the offered product(s)", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_FRIEND}</span>: " . __( "Offered by", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_DATE}</span>: " . __( "Order date", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_TYPE}</span>: " . __( "Purchase Type", ILIST_ID_LANGUAGES ) . "<br>
					<span style='font-weight: bold;'>{ILIST_ORDER_ID}</span>: " . __( "Order ID", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_COMMENT}</span>: " . __( "Comment", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_WP_SITE_NAME}</span>: " . __( "Wordpress Site Name", ILIST_ID_LANGUAGES ) . "<br>
                    <span style='font-weight: bold;'>{ILIST_WP_SITE_DESC}</span>: " . __( "Wordpress Site Description", ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		// --- Hiddens / Buttons
		// ----------------------------------------
		echo $ilist_framework->addInput( 'submit', '', array('value' => __( "Save changes", ILIST_ID_LANGUAGES)) );
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

?>