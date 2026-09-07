<?php

/** Initialize Nav Tabs
=================================================== */
if (!function_exists("yourplugin_nav_tabs_func")) {
	function yourplugin_nav_tabs_func() {
		$yourplugin_nav_tabs = [
			 "yourplugin-general" => __( 'General', YOUR_PLUGIN_ID_LANGUAGES )
			,"yourplugin-options" => __( 'Options', YOUR_PLUGIN_ID_LANGUAGES )
			,"yourplugin-pot"     => __( 'Pot', YOUR_PLUGIN_ID_LANGUAGES )
			,"yourplugin-share"   => __( 'Share', YOUR_PLUGIN_ID_LANGUAGES )
			,"yourplugin-search"  => __( 'Search', YOUR_PLUGIN_ID_LANGUAGES )
			,"yourplugin-emails"  => __( 'Automatic emails', YOUR_PLUGIN_ID_LANGUAGES )
			,"yourplugin-credits" => __( 'Credits', YOUR_PLUGIN_ID_LANGUAGES )
		];
		return $yourplugin_nav_tabs;
	}
}

?>