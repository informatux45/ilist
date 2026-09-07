<?php
/** Blocking direct access to plugin
=================================================== */
defined('ABSPATH') or die('Are you crazy!');

/** Create tab's CREDITS
=============================================== */
if (!function_exists("ilist_changelog")) {
	function ilist_changelog() {
		/** INFORMATUX Framework
		=================================================== */
		global $ilist_framework, $Ilist;
        
		/** Intialisation
		=============================================== */
		$ilist_page = "changelog";
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

		/** Content
		=================================================== */		
		echo $ilist_framework->openTable();
		// ----------------------------------------
		$changelog_content = file_get_contents( $Ilist->ilist_changelog );
		// ----------------------------------------
		echo $ilist_framework->addNote( __( 'CHANGELOG', ILIST_ID_LANGUAGES ) . "<br>ILIST Kado", $changelog_content);
		// ----------------------------------------
		echo $ilist_framework->closeTable();
    }
}
// ----------------------------------------
// ----------------------------------------
// ----------------------------------------
?>