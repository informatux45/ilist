<?php
/** Blocking direct access to plugin
=================================================== */
defined('ABSPATH') or die('Are you crazy!');

/** Create tab's
=============================================== */
if (!function_exists("function_menu")) {
	function function_menu() {
		
		/** PLUGIN Framework
		=================================================== */
		global $framework;
		
		/** Intialisation
		=============================================== */
		$framework_page = "share";
		
		/** Title
		=================================================== */
		echo $framework->general_infos(
			 'start'
			,[
				 'PLUGIN_ID'      => PLUGIN_ID
				,'PLUGIN_NAME'    => PLUGIN_NAME
				,'PLUGIN_VERSION' => function_to_get_version()
			  ]);
		
		/** Tabs
		=================================================== */
		echo $framework->nav_tabs( nav_tabs_func(), 'yourplugin-'.$framework_page );
		
		/** Form construct (only if you have a form)
		=================================================== */
		echo $framework->openForm(
			array(
				 'action'  => admin_url("admin.php?page=yourplugin-$framework_page")
				,'name'    => "$framework_page"
				,'id'      => "$framework_page"
				,'method'  => "post"
				,'enctype' => "multipart/form-data"
			)
		);
		
		/** Content
		=================================================== */
		echo $framework->openTable();
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		
		// Your FORM INPUTS
		
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		// --- Hiddens / Buttons  (only if you have a form)
		// ----------------------------------------
		echo $framework->addInput( 'submit', '', array('value' => __( "Save changes", YOUR_PLUGIN_ID_LANGUAGES)) );
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $framework->closeTable();
		echo $framework->closeForm(); //  (only if you have a form)
		// ----------------------------------------
		
		/** End
        =================================================== */
		echo $framework->general_infos('end');
		
	}
}

?>