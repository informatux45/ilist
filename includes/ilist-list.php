<?php
/** Blocking direct access to plugin
=================================================== */
defined('ABSPATH') or die('Are you crazy!');

/** Create tab's OPTIONS
=============================================== */
if (!function_exists("ilist_list")) {
	function ilist_list() {
		
		/** INFORMATUX Framework
		=================================================== */
		global $ilist_framework, $Ilist;
		
		/** Intialisation
		=============================================== */
		$page     = "list";
		$url_help = $Ilist->helpicon('configuration/liste/');
		
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
			 __( "Display username in list title", ILIST_ID_LANGUAGES )
			,__( "AJAX Cart", ILIST_ID_LANGUAGES )
			,__( "List image", ILIST_ID_LANGUAGES ) . ' (Upload)'
			,__( "Request for items to be retrieved by a creator", ILIST_ID_LANGUAGES )
			,__( "List image format", ILIST_ID_LANGUAGES )
			,__( "Displaying products in a list", ILIST_ID_LANGUAGES )
			,__( "Hide add product", ILIST_ID_LANGUAGES )
			,__( "Disable list creation", ILIST_ID_LANGUAGES )
			,__( "Hide add to my list xxx", ILIST_ID_LANGUAGES )
			,__( "Sorting products in lists", ILIST_ID_LANGUAGES )
			,__( "Behavior of links and photos in a product listing", ILIST_ID_LANGUAGES )
			,__( "Front lightbox color", ILIST_ID_LANGUAGES )
			,__( "Lightbox content", ILIST_ID_LANGUAGES )
		] );
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Display username in list title", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Display username', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_display_user_name'
				,'name'    => 'ilist_display_user_name'
				,'checked' => ilist_get_option('ilist_display_user_name', '1') // Default: 1
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'Yes - Default: enabled', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "AJAX Cart", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Remove button after click', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_ajax_cart_button_remove_after_click'
				,'name'    => 'ilist_ajax_cart_button_remove_after_click'
				,'checked' => ilist_get_option('ilist_ajax_cart_button_remove_after_click', '0') // Default: 0
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'No - Default: disabled', ILIST_ID_LANGUAGES ) . '<br>' . __("If the option is enabled, in the lists, the Add to cart button will disappear after a buyer clicks to add a product to the cart", ILIST_ID_LANGUAGES) . '</span>' // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "List image", ILIST_ID_LANGUAGES ) . ' (Upload)' );
		// ----------------------------------------
		echo $ilist_framework->addNumber(
			 __( "Maximum image size of a list", ILIST_ID_LANGUAGES ) . ' <i>(&nbsp;' . __( 'Ko', ILIST_ID_LANGUAGES ) . '&nbsp;)</i>'
			,array(
				 'id'      => 'ilist_image_max_size'
				,'name'    => 'ilist_image_max_size'
				,'step'    => 10
				,'min'     => 100
				,'max'     => 2000
				,'value'   => ilist_get_option('ilist_image_max_size', 400) // Default: 400
			)
			,true // Required: true OR false
			,__( 'Maximum size of an image - Default: 400Kb - Maximum 2Mo', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		echo $ilist_framework->addNumber(
			 __( "Image height", ILIST_ID_LANGUAGES ) . ' <i>(&nbsp;' . __( 'Pixels', ILIST_ID_LANGUAGES ) . '&nbsp;)</i>'
			,array(
				 'id'      => 'ilist_image_height'
				,'name'    => 'ilist_image_height'
				,'step'    => 10
				,'min'     => 100
				,'max'     => 500
				,'value'   => ilist_get_option('ilist_image_height', 150) // Default: 150
			)
			,true // Required: true OR false
			,__( 'Setting of the height of the image - Default: 150px', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Request for items to be retrieved by a creator", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addNote(
            __( "Creator lists", ILIST_ID_LANGUAGES )
            ,'<img style="max-height: 184px; border: 1px solid grey;" src="' . ILIST_URL . 'images/help/help-notify-admins.jpg" alt="">'
        );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Notification by a list creator', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_notify_admins'
				,'name'    => 'ilist_notify_admins'
				,'checked' => ilist_get_option('ilist_notify_admins', '0') // Default: 0
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'No - Default: disabled', ILIST_ID_LANGUAGES ) . '<br>' . __("If the option is enabled, in the lists, the creator of a list can send you an email asking you to retrieve the items from one of his lists", ILIST_ID_LANGUAGES) . '</span>' // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "List image format", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addNote(
            __( "Themes", ILIST_ID_LANGUAGES )
            ,'<div style="text-align: center; margin-right: 1em; float: left;"><a href="' . ILIST_URL . 'images/help/help-format-image-theme-simple.jpg" target="_blank"><img style="max-height: 95px; border: 1px solid grey;" src="' . ILIST_URL . 'images/help/help-format-image-theme-simple.jpg" alt="CSS - List format image - Theme simple"></a><span style="display: block">' . __('Simple', ILIST_ID_LANGUAGES) . '</span></div>'
            .'<div style="text-align: center; margin-right: 1em; float: left;"><a href="' . ILIST_URL . 'images/help/help-format-image-theme-1.jpg" target="_blank"><img style="max-height: 95px; border: 1px solid grey;" src="' . ILIST_URL . 'images/help/help-format-image-theme-1.jpg" alt="CSS - List format image - Theme 1"></a><span style="display: block">' . __('Theme 1', ILIST_ID_LANGUAGES) . '</span></div>'
            .'<div style="text-align: center; margin-right: 1em; float: left;"><a href="' . ILIST_URL . 'images/help/help-format-image-theme-2.jpg" target="_blank"><img style="max-height: 95px; border: 1px solid grey;" src="' . ILIST_URL . 'images/help/help-format-image-theme-2.jpg" alt="CSS - List format image - Theme 2"></a><span style="display: block">' . __('Theme 2', ILIST_ID_LANGUAGES) . '</span></div>'
            .'<div style="text-align: center; margin-right: 1em; float: left;"><a href="' . ILIST_URL . 'images/help/help-format-image-theme-3.jpg" target="_blank"><img style="max-height: 95px; border: 1px solid grey;" src="' . ILIST_URL . 'images/help/help-format-image-theme-3.jpg" alt="CSS - List format image - Theme 3"></a><span style="display: block">' . __('Theme 3', ILIST_ID_LANGUAGES) . '</span></div>'
        );
		// ----------------------------------------
		$tab_format_image  = [];
		$tab_format_images = array(
			 [ 'id' => 'simple',  'title' => __( "Simple", ILIST_ID_LANGUAGES ) ]
			,[ 'id' => 'theme_1', 'title' => __( "Theme 1", ILIST_ID_LANGUAGES ) ]
			,[ 'id' => 'theme_2', 'title' => __( "Theme 2", ILIST_ID_LANGUAGES ) ]
			,[ 'id' => 'theme_3', 'title' => __( "Theme 3", ILIST_ID_LANGUAGES ) ]
		);
		foreach($tab_format_images as $key => $val) {
			if (ilist_get_option('ilist_format_image') == $val['id']) $sorting_format_image_checked = $val['id'];
			$tab_format_image[$key]['text']  = $val['title'];
			$tab_format_image[$key]['value'] = $val['id'];
		}
		echo $ilist_framework->addRadio(
			 __( 'Choose how list image will be displayed', ILIST_ID_LANGUAGES )
			,$tab_format_image
			,array(
				 'id'      => 'ilist_format_image'
				,'name'    => 'ilist_format_image'
				,'checked' => "$sorting_format_image_checked"
			)
			,true
			,'<br>'
			,'' // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Displaying products in a list", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addNote(
            __( "Themes", ILIST_ID_LANGUAGES )
            ,'<div style="text-align: center; margin-right: 1em; float: left;"><a href="' . ILIST_URL . 'images/help/help-format-list-css-table.jpg" target="_blank"><img style="max-height: 150px; border: 1px solid grey;" src="' . ILIST_URL . 'images/help/help-format-list-css-table.jpg" alt="CSS - List format products - Table"></a><span style="display: block">' . __('Table', ILIST_ID_LANGUAGES) . '</span></div>'
            .'<div style="text-align: center; margin-right: 1em; float: left;"><a href="' . ILIST_URL . 'images/help/help-format-list-css-grid.jpg" target="_blank"><img style="max-height: 150px; border: 1px solid grey;" src="' . ILIST_URL . 'images/help/help-format-list-css-grid.jpg" alt="CSS - List format products - Grid"></a><span style="display: block">' . __('Grid', ILIST_ID_LANGUAGES) . '</span></div>'
        );
		// ----------------------------------------
		$tab_format_list  = [];
		$tab_format_lists = array(
			 [ 'id' => 'table', 'title' => __( "Table", ILIST_ID_LANGUAGES ) ]
			,[ 'id' => 'grid',  'title' => __( "Grid", ILIST_ID_LANGUAGES ) ]
		);
		foreach($tab_format_lists as $key => $val) {
			if (ilist_get_option('ilist_format_display_products') == $val['id']) $sorting_format_list_checked = $val['id'];
			$tab_format_list[$key]['text']  = $val['title'];
			$tab_format_list[$key]['value'] = $val['id'];
		}
		echo $ilist_framework->addRadio(
			 __( 'Choose how list products will be displayed', ILIST_ID_LANGUAGES )
			,$tab_format_list
			,array(
				 'id'      => 'ilist_format_display_products'
				,'name'    => 'ilist_format_display_products'
				,'checked' => "$sorting_format_list_checked"
			)
			,true
			,'<br>'
			,'' // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Hide add product", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addNote( __( "Hide add product", ILIST_ID_LANGUAGES ), '<img src="' . ILIST_URL . 'images/help/help-hide-add-product.jpg" alt="Hide add products" style="max-width: 100%;" />' );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Hide the ability to add a product to the list creator', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_hide_add_product'
				,'name'    => 'ilist_hide_add_product'
				,'checked' => ilist_get_option('ilist_hide_add_product', '0') // Default: 0
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'No - Default: disabled', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Disable list creation", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addNote( __( "Disable list creation", ILIST_ID_LANGUAGES ), '<img src="' . ILIST_URL . 'images/help/help-hide-add-to-my-list.jpg" alt="Hide add to my list" style="max-width: 100%;" />' );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Disable list creation for visitors', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_disable_list_creation_visitors'
				,'name'    => 'ilist_disable_list_creation_visitors'
				,'checked' => ilist_get_option('ilist_disable_list_creation_visitors', '0') // Default: 0
			)
			,__( 'YES', ILIST_ID_LANGUAGES )
			,__( 'NO', ILIST_ID_LANGUAGES )
			,__( 'Default: NO', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Disable list creation for registered users', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_disable_list_creation_registered_users'
				,'name'    => 'ilist_disable_list_creation_registered_users'
				,'checked' => ilist_get_option('ilist_disable_list_creation_registered_users', '0') // Default: 0
			)
			,__( 'YES', ILIST_ID_LANGUAGES )
			,__( 'NO', ILIST_ID_LANGUAGES )
			,__( 'Default: NO', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Hide add to my list xxx", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addNote( __( "Hide add to my list xxx", ILIST_ID_LANGUAGES ), '<img src="' . ILIST_URL . 'images/help/help-ilist-hide-add-to-my-list-xxx.jpg" alt="Hide add to my list xxx" style="max-width: 100%;" />' );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Hide the "Add to my list xxxx" buttons on the product pages', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_hide_add_to_my_list_xxx'
				,'name'    => 'ilist_hide_add_to_my_list_xxx'
				,'checked' => ilist_get_option('ilist_hide_add_to_my_list_xxx', '0') // Default: 0
			)
			,__( 'Yes', ILIST_ID_LANGUAGES )
			,__( 'No', ILIST_ID_LANGUAGES )
			,__( 'Default: No', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Sorting products in lists", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Show unpurchased products at the top of the list', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_show_unpurchased_products_top_list'
				,'name'    => 'ilist_show_unpurchased_products_top_list'
				,'checked' => ilist_get_option('ilist_show_unpurchased_products_top_list', '0') // Default: 0
			)
			,__( 'Yes', ILIST_ID_LANGUAGES )
			,__( 'No', ILIST_ID_LANGUAGES )
			,__( 'Default: No', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		$tab_sorting_product  = [];
		$tab_sorting_products = array(
			 ['id' => 'alpha_asc',  'title' => __( "alphabetical order", ILIST_ID_LANGUAGES ) . " (ASC - " . __( "Ascending", ILIST_ID_LANGUAGES ) . ")"]
			,['id' => 'alpha_desc', 'title' => __( "alphabetical order", ILIST_ID_LANGUAGES ) . " (DESC - " . __( "Descending", ILIST_ID_LANGUAGES ) . ")"]
			,['id' => 'id_asc',     'title' => __( "order of arrival in the list", ILIST_ID_LANGUAGES ) . " (ASC - " . __( "Ascending", ILIST_ID_LANGUAGES ) . ")"]
			,['id' => 'id_desc',    'title' => __( "order of arrival in the list", ILIST_ID_LANGUAGES ) . " (DESC - " . __( "Descending", ILIST_ID_LANGUAGES ) . ")"]
			,['id' => 'date_asc',   'title' => __( "date of the products (creation)", ILIST_ID_LANGUAGES ) . " (ASC - " . __( "Ascending", ILIST_ID_LANGUAGES ) . ")"]
			,['id' => 'date_desc',  'title' => __( "date of the products (creation)", ILIST_ID_LANGUAGES ) . " (DESC - " . __( "Descending", ILIST_ID_LANGUAGES ) . ")"]
			,['id' => 'price_asc',  'title' => __( "price", ILIST_ID_LANGUAGES ) . " (ASC - " . __( "Ascending", ILIST_ID_LANGUAGES ) . ")"]
			,['id' => 'price_desc', 'title' => __( "price", ILIST_ID_LANGUAGES ) . " (DESC - " . __( "Descending", ILIST_ID_LANGUAGES ) . ")"]
		);
		foreach($tab_sorting_products as $key => $val) {
			if (ilist_get_option('ilist_sorting_products') == $val['id']) $sorting_product_checked = $val['id'];
			$tab_sorting_product[$key]['text']  = $val['title'];
			$tab_sorting_product[$key]['value'] = $val['id'];
		}
		echo $ilist_framework->addRadio(
			 __( 'Choose how the products will be sorted in the lists', ILIST_ID_LANGUAGES )
			,$tab_sorting_product
			,array(
				 'id'      => 'ilist_sorting_products'
				,'name'    => 'ilist_sorting_products'
				,'checked' => "$sorting_product_checked"
			)
			,true
			,'<br>'
			,'' // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Behavior of links and photos in a product listing", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addNote( __( 'Links behavior', ILIST_ID_LANGUAGES ), '<img src="' . ILIST_URL . 'images/help/help-links-list-behavior.png" alt="Links behavior" />' );
		// ----------------------------------------
		$tab_link_behavior  = [];
		$tab_link_behaviors = array(
			 ['id' => 'lightbox', 'title' => __( 'Display lightbox', ILIST_ID_LANGUAGES )]
			,['id' => 'link',     'title' => __( 'Redirect to product page (link)', ILIST_ID_LANGUAGES )]
						   );
		foreach($tab_link_behaviors as $key => $val) {
			if (ilist_get_option('ilist_link_list_behavior') == $val['id']) $link_behavior_checked = $val['id'];
			$tab_link_behavior[$key]['text']  = $val['title'];
			$tab_link_behavior[$key]['value'] = $val['id'];
		}
		echo $ilist_framework->addRadio(
			 __( 'Choices links behavior', ILIST_ID_LANGUAGES )
			,$tab_link_behavior
			,array(
				 'id'      => 'ilist_link_list_behavior'
				,'name'    => 'ilist_link_list_behavior'
				,'checked' => "$link_behavior_checked"
			)
			,true
			,'<br>'
			,'' // Description
		);
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Behavior of the link to another page  if active', ILIST_ID_LANGUAGES ) . '<br><span style="color: grey; font-style: italic; font-weight: normal;">target="_blank"</span>'
			,true
			,array(
				 'id'      => 'ilist_link_list_href_blank'
				,'name'    => 'ilist_link_list_href_blank'
				,'checked' => ilist_get_option('ilist_link_list_href_blank', '0') // Default: 0
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'No - Default: disabled', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Front lightbox color", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addNote( __( "Default lightbox color", ILIST_ID_LANGUAGES ) . '<br><span style="color: grey; font-style: italic; font-weight: normal;">' . __( "Lighbox preview when you click on items in a list", ILIST_ID_LANGUAGES ) . '</span>', '<img src="' . ILIST_URL . 'images/help/help-lightbox-sample.png" alt="Front lightbox" />' );
		// ----------------------------------------
		$tab_color_lightbox  = [];
		$tab_color_lightboxs = array(
			 ['id' => 'white',      'title' => '<img src="' . ILIST_URL . '/images/plainmodal-close-white-text.png' . '" style="vertical-align: middle;" alt="white">']
			,['id' => 'default',    'title' => '<img src="' . ILIST_URL . '/images/plainmodal-close-default-text.png' . '" style="vertical-align: middle;" alt="default">']
			,['id' => 'lightgreen', 'title' => '<img src="' . ILIST_URL . '/images/plainmodal-close-lightgreen-text.png' . '" style="vertical-align: middle;" alt="lightgreen">']
			,['id' => 'darkgreen',  'title' => '<img src="' . ILIST_URL . '/images/plainmodal-close-darkgreen-text.png' . '" style="vertical-align: middle;" alt="darkgreen">']
			,['id' => 'grey',       'title' => '<img src="' . ILIST_URL . '/images/plainmodal-close-grey-text.png' . '" style="vertical-align: middle;" alt="grey">']
			,['id' => 'lightblue',  'title' => '<img src="' . ILIST_URL . '/images/plainmodal-close-lightblue-text.png' . '" style="vertical-align: middle;" alt="lightblue">']
			,['id' => 'darkblue',   'title' => '<img src="' . ILIST_URL . '/images/plainmodal-close-darkblue-text.png' . '" style="vertical-align: middle;" alt="darkblue">']
			,['id' => 'pink',       'title' => '<img src="' . ILIST_URL . '/images/plainmodal-close-pink-text.png' . '" style="vertical-align: middle;" alt="pink">']
			,['id' => 'red',        'title' => '<img src="' . ILIST_URL . '/images/plainmodal-close-red-text.png' . '" style="vertical-align: middle;" alt="red">']
			,['id' => 'yellow',     'title' => '<img src="' . ILIST_URL . '/images/plainmodal-close-yellow-text.png' . '" style="vertical-align: middle;" alt="yellow">']
			,['id' => 'violet',     'title' => '<img src="' . ILIST_URL . '/images/plainmodal-close-violet-text.png' . '" style="vertical-align: middle;" alt="violet">']
			,['id' => 'brown',      'title' => '<img src="' . ILIST_URL . '/images/plainmodal-close-brown-text.png' . '" style="vertical-align: middle;" alt="brown">']
		);
		foreach($tab_color_lightboxs as $key => $val) {
			if (ilist_get_option('ilist_front_color_lightbox') == $val['id']) $color_lightbox_checked = $val['id'];
			$tab_color_lightbox[$key]['text']  = $val['title'];
			$tab_color_lightbox[$key]['value'] = $val['id'];
		}
		echo $ilist_framework->addRadio(
			 __( "Select your lightbox color", ILIST_ID_LANGUAGES )
			,$tab_color_lightbox
			,array(
				 'id'      => 'ilist_front_color_lightbox'
				,'name'    => 'ilist_front_color_lightbox'
				,'checked' => "$color_lightbox_checked"
			)
			,true
			,'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
			,'' // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Lightbox content", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Title Activation', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_quickview_title'
				,'name'    => 'ilist_quickview_title'
				,'checked' => ilist_get_option('ilist_quickview_title', '1') // Default: 1
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'Yes - Default: enabled', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Price Activation', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_quickview_price'
				,'name'    => 'ilist_quickview_price'
				,'checked' => ilist_get_option('ilist_quickview_price', '1') // Default: 1
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'Yes - Default: enabled', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Description Activation (short)', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_quickview_description_short'
				,'name'    => 'ilist_quickview_description_short'
				,'checked' => ilist_get_option('ilist_quickview_description_short', '1') // Default: 1
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'Yes - Default: enabled', ILIST_ID_LANGUAGES ) . '<br>' . __( 'if enabled, it will be visible if it has content', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Description Activation (long)', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_quickview_description_long'
				,'name'    => 'ilist_quickview_description_long'
				,'checked' => ilist_get_option('ilist_quickview_description_long', '1') // Default: 1
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'Yes - Default: enabled', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		echo $ilist_framework->addNumber(
			 __( "Truncate long description", ILIST_ID_LANGUAGES )
			,array(
				 'id'      => 'ilist_quickview_description_truncate'
				,'name'    => 'ilist_quickview_description_truncate'
				,'step'    => 10
				,'min'     => 50
				,'max'     => 2000
				,'value'   => ilist_get_option('ilist_quickview_description_truncate', 250) // Default: 250
			)
			,true
			,__( "Number of characters visible in the description", ILIST_ID_LANGUAGES ) // Description
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

?>