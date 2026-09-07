<?php
/** Blocking direct access to plugin
=================================================== */
defined('ABSPATH') or die('Are you crazy!');

/** Create tab's SEARCH
=============================================== */
if (!function_exists("ilist_search")) {
	function ilist_search() {
		
		/** INFORMATUX Framework
		=================================================== */
		global $ilist_framework, $Ilist;
		
		/** Intialisation
		=============================================== */
		$ilist_page = "search";
		$url_help   = $Ilist->helpicon('configuration/recherche/');
		
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
		
		/** Form construct (only if you have a form)
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
			 __( "Search Choice possible", ILIST_ID_LANGUAGES )
			,__( "Adding products (Live search)", ILIST_ID_LANGUAGES )
		] );
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Search Choice possible", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addNote(
			 __( "Choice(s) possible(s)", ILIST_ID_LANGUAGES )
			,'<img src="' . ILIST_URL . 'images/help/help-search-choices.png" alt="Live search" />'
		);
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Search by email<br><span class="ilist-style-italic ilist-style-description">Strict search</span>', ILIST_ID_LANGUAGES )
			,true // Required: true OR false
			,array(
				 'id'      => 'ilist_email_search'
				,'name'    => 'ilist_email_search'
				,'checked' => ilist_get_option('ilist_email_search', '1') // Default: 1
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'Yes - Default: enabled', ILIST_ID_LANGUAGES ) // Description
		);        
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			__( 'Search by list name<br><span class="ilist-style-italic ilist-style-description">Permissive search</span>', ILIST_ID_LANGUAGES )
			,true // Required: true OR false
			,array(
				 'id'      => 'ilist_name_search'
				,'name'    => 'ilist_name_search'
				,'checked' => ilist_get_option('ilist_name_search', '0') // Default: 0
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'No - Default: disabled', ILIST_ID_LANGUAGES ) // Description
		);        
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Search by firstname<br><span class="ilist-style-italic ilist-style-description">Permissive search</span>', ILIST_ID_LANGUAGES )
			,true // Required: true OR false
			,array(
				 'id'      => 'ilist_firstname_search'
				,'name'    => 'ilist_firstname_search'
				,'checked' => ilist_get_option('ilist_firstname_search', '0') // Default: 0
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'No - Default: disabled', ILIST_ID_LANGUAGES ) // Description
		);        
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'Search by lastname<br><span class="ilist-style-italic ilist-style-description">Permissive search</span>', ILIST_ID_LANGUAGES )
			,true // Required: true OR false
			,array(
				 'id'      => 'ilist_lastname_search'
				,'name'    => 'ilist_lastname_search'
				,'checked' => ilist_get_option('ilist_lastname_search', '0') // Default: 0
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'No - Default: disabled', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		echo $ilist_framework->addBreak( $url_help . __( "Adding products (Live search)", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addNote(
			 __( "Live search after entering 3 characters", ILIST_ID_LANGUAGES )
            ,'<img src="' . ILIST_URL . 'images/help/help-live-search.png" alt="Live search" />'
			
		);
        // ----------------------------------------
		echo $ilist_framework->addNumber(
			 __( "Maximum visible results (Live search)", ILIST_ID_LANGUAGES )
			,array(
				 'id'      => 'ilist_selectize_limit'
				,'name'    => 'ilist_selectize_limit'
				,'step'    => 1
				,'min'     => 5
				,'max'     => 20
				,'value'   => ilist_get_option('ilist_selectize_limit', 10) // Default: 10
			)
			,true // Required: true OR false
			,'' // Description
		);
		// ----------------------------------------
		echo $ilist_framework->addNumber(
			 __( "Truncate description", ILIST_ID_LANGUAGES )
			,array(
				 'id'      => 'ilist_selectize_truncate'
				,'name'    => 'ilist_selectize_truncate'
				,'step'    => 1
				,'min'     => 20
				,'max'     => 100
				,'value'   => ilist_get_option('ilist_selectize_truncate', 50) // Default: 50
			)
			,true // Required: true OR false
			,__( 'Maximum number of characters followed by ... ', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		// --- Hiddens / Buttons  (only if you have a form)
		// ----------------------------------------
		echo $ilist_framework->addInput( 'submit', '', array('value' => __( "Save changes", ILIST_ID_LANGUAGES)) );
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->closeTable();
		echo $ilist_framework->closeForm(); //  (only if you have a form)
		// ----------------------------------------
		
		/** End
        =================================================== */
		echo $ilist_framework->general_infos('end');
		
	}
}

?>