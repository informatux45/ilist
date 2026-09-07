<?php
/** Blocking direct access to plugin
=================================================== */
defined('ABSPATH') or die('Are you crazy!');

/** Create tab's POT
=============================================== */
if (!function_exists("ilist_share")) {
	function ilist_share() {
		
		/** INFORMATUX Framework
		=================================================== */
		global $ilist_framework, $Ilist;
		
		/** Intialisation
		=============================================== */
		$ilist_page = "share";
		$url_help   = $Ilist->helpicon('configuration/partager/');
		$ilist_get_share_icons = ilist_get_option('ilist_share_icons');
		$_ilist_share_icons = array(
			 'copypaste'   => __('Copy Paste', ILIST_ID_LANGUAGES)
			,'buffer'      => 'Buffer'
			,'digg'        => 'Diggit'
			,'email'       => 'Email'
			,'gmail'       => 'Gmail'
			,'facebook'    => 'Facebook'
			,'google'      => 'Google'
			,'linkedin'    => 'Linkedin'
			,'pinterest'   => 'Pinterest'
			,'print'       => 'Print'
			,'reddit'      => 'Reddit'
			,'stumbleupon' => 'Stumbleupon'
			,'tumblr'      => 'Tumblr'
			,'twitter'     => 'Twitter'
			,'vk'          => 'VK'
			,'whatsapp'    => 'WhatsApp'
			,'yummly'      => 'Yummly'
		);
		
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
		
		/** Form construct
		=================================================== */
		echo $ilist_framework->openForm(
			array(
				 'action'  => admin_url("admin.php?page=ilist-$ilist_page")
				,'name'    => "$ilist_page"
				,'id'      => "$ilist_page"
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
			 __( "Share lists", ILIST_ID_LANGUAGES )
			,__( "Share Icons", ILIST_ID_LANGUAGES )
			,__( 'Only if you activate PRINT sharing', ILIST_ID_LANGUAGES )
		] );
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Share lists", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Share', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_share_list'
				,'name'    => 'ilist_share_list'
				,'checked' => ilist_get_option('ilist_share_list', '0') // Default: 0
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'No - Default: disabled', ILIST_ID_LANGUAGES ) . '<br>' . __("Don't forget to add share icons", ILIST_ID_LANGUAGES) // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Share Icons", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		$ilist_icons  = '';
		// ----------------------------------------
		$ilist_icons .= '<select name="ilist_share_icons[]" id="ilist_share_icons" multiple="multiple" class="ilist-select-2-ads">';
			$ilist_icons .= '<option value="">&mdash; ' . __('Choose', ILIST_ID_LANGUAGES) . ' &mdash;</option>';
			foreach ($_ilist_share_icons as $key => $value) {
				$ilist_icons .= '<option value="'.$key.'" ';
				if ( is_array($ilist_get_share_icons) && in_array( $key, $ilist_get_share_icons ) && array_keys( $ilist_get_share_icons, true ) ) $ilist_icons .= 'selected="selected"';
				$ilist_icons .= '>';
				$ilist_icons .= ucfirst($value);
				$ilist_icons .= '</option>';
			}
		$ilist_icons .= '</select>';
		// ----------------------------------------
		echo $ilist_framework->addNote(
			 __( "Choose your share icons", ILIST_ID_LANGUAGES )
			,'<link href="' . ILIST_URL . 'vendor/Select2/select2.min.css" rel="stylesheet">
				<style>.select2-container { width: 500px !important; }</style>
				<script src="' . ILIST_URL . 'vendor/Select2/select2.min.js"></script>
				<form action="?" method="post">
				' . $ilist_icons . '
				</form>
				<br><em class="description">
					' . __( "Choose the sharing icons you want to display", ILIST_ID_LANGUAGES ) . '
				</em>
				<script>
					jQuery(function($) {
						$("#ilist_share_icons").select2();
					});
				</script>'
		);
		// ----------------------------------------
		$tab_share_icon  = [];
		$tab_share_icons = array(
			 ['id' => 'rounded', 'title' => '/images/share/twitter-rounded.png']
			,['id' => 'square',  'title' => '/images/share/twitter-square.png']
						   );
		foreach($tab_share_icons as $key => $val) {
			if (ilist_get_option('ilist_share_icons_method') == $val['id']) $share_icons_method_checked = $val['id'];
			$tab_share_icon[$key]['text']  = '<img style="width: 28px; margin-top: -6px; position: absolute;" src="' . ILIST_URL . $val['title'] . '" alt="Social network">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $val['id'];
			$tab_share_icon[$key]['value'] = $val['id'];
		}
		echo $ilist_framework->addRadio(
			 __( 'Type of icons', ILIST_ID_LANGUAGES )
			,$tab_share_icon
			,array(
				 'id'      => 'ilist_share_icons_method'
				,'name'    => 'ilist_share_icons_method'
				,'checked' => "$share_icons_method_checked"
				,'default' => 'rounded'
			)
			,true
			,'<br>'
			,__( 'Default: Rounded', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( 'Only if you activate PRINT sharing', ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addNote(
			__( 'Available options for sharing: PRINT', ILIST_ID_LANGUAGES )
			,'<img src="' . ILIST_URL . 'images/help/help-print-options.png" alt="The options for sharing: PRINT" />'
		);
		// ----------------------------------------
		echo $ilist_framework->addUpload(
			 __( 'Logo', ILIST_ID_LANGUAGES )
			,array(
				 'name'        => 'ilist_print_logo'
				,'id'          => 'ilist_print_logo'
				,'value'       => ilist_get_option('ilist_print_logo') // Url Image
				,'uploadButtonTxt' => __( 'Upload image', ILIST_ID_LANGUAGES ) // Optional
				,'removeButtonTxt' => __( 'Remove image', ILIST_ID_LANGUAGES ) // Optional
				,'previewSize'     => '150px' // Optional - height in pixels - Default: 150px (Width / Height)
			)
			,true
			,__( 'Upload your Logo image for lists to print', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		echo $ilist_framework->addNumber(
			 __( 'Logo height', ILIST_ID_LANGUAGES ) . ' <i>( ' . __( 'Pixels', ILIST_ID_LANGUAGES ) . ' )</i>'
			,array(
				 'id'      => 'ilist_print_logo_height'
				,'name'    => 'ilist_print_logo_height'
				,'step'    => 1
				,'min'     => 10
				,'max'     => 500
				,'value'   => ilist_get_option('ilist_print_logo_height', 80) // Default: 80
			)
			,true // Required: true OR false
			,__( 'Setting of the height of the logo - Default: 80px', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		echo $ilist_framework->addNumber(
			 __( 'Product thumbnail height', ILIST_ID_LANGUAGES ) . ' <i>( ' . __( 'Pixels', ILIST_ID_LANGUAGES ) . ' )</i>'
			,array(
				 'id'      => 'ilist_print_thumbnail_height'
				,'name'    => 'ilist_print_thumbnail_height'
				,'step'    => 1
				,'min'     => 10
				,'max'     => 150
				,'value'   => ilist_get_option('ilist_print_thumbnail_height', 150) // Default: 150
			)
			,true // Required: true OR false
			,__( 'Setting of the height of the thumbnail- Default: 150px', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Popup / New tab', ILIST_ID_LANGUAGES ) . '<br><span style="color: grey; font-style: italic; font-weight: normal;">' . __('Choose how to open the printing of a list when a user clicks the print icon', ILIST_ID_LANGUAGES) . '</span>'
			,true // Required: true OR false
			,array(
				 'id'      => 'ilist_print_open'
				,'name'    => 'ilist_print_open'
				,'checked' => ilist_get_option('ilist_print_open', '0') // Default: 0
			)
			,__( 'Popup', ILIST_ID_LANGUAGES )
			,__( 'New tab', ILIST_ID_LANGUAGES )
			,__( 'Default: popup', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		echo $ilist_framework->addEditor(
			 __( 'Head text', ILIST_ID_LANGUAGES ) . '<br><span style="color: grey; font-style: italic; font-weight: normal;">' . __('If not filled in, the title and description of the site will be indicated (Wordpress settings)', ILIST_ID_LANGUAGES) . '<br><br>' . __( 'To format your text, use the editor options (bold, underline, italics, alignment, ...)', ILIST_ID_LANGUAGES)
			,array(
				 'id'    => 'ilist_print_head_text'
				,'name'  => 'ilist_print_head_text'
				,'value' => ilist_get_option('ilist_print_head_text', '') // Default: void
				,'media_buttons' => false  // Whether to show the Add Media/other media buttons
				,'wpautop'       => false  // Whether to use wpautop()
				,'textarea_rows' => 4      // Number rows in the editor textarea
				,'teeny'         => true   // Whether to output the minimal editor config
			)
			,true // Required: true OR false
			,__( 'Put content under your logo here', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		echo $ilist_framework->addEditor(
			 __( 'Footer text', ILIST_ID_LANGUAGES ) . '<br><span style="color: grey; font-style: italic; font-weight: normal;">' . __( 'To format your text, use the editor options (bold, underline, italics, alignment, ...)', ILIST_ID_LANGUAGES ) . '</span>'
			,array(
				 'id'    => 'ilist_print_footer_text'
				,'name'  => 'ilist_print_footer_text'
				,'value' => ilist_get_option('ilist_print_footer_text', '') // Default: void
				,'media_buttons' => false  // Whether to show the Add Media/other media buttons
				,'wpautop'       => false  // Whether to use wpautop()
				,'textarea_rows' => 4      // Number rows in the editor textarea
				,'teeny'         => true   // Whether to output the minimal editor config
			)
			,true // Required: true OR false
			,__( 'Put content after list here', ILIST_ID_LANGUAGES ) // Description
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