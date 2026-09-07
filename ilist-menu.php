<?php
/** Blocking direct access to plugin
=================================================== */
defined('ABSPATH') or die('Are you crazy!');

/** ILIST Admin Menu / Submenus
=================================================== */
add_action('admin_menu', 'ilist_add_pages');

/** Menu
=================================================== */
if (!function_exists("ilist_add_pages")) {
    function ilist_add_pages() {
        // Menu page
        // Position in menu (Default: bottom of menu structure):
        // 2  – Dashboard
        // 4  – Separator
        // 5  – Posts
        // 10 – Media
        // 15 – Links
        // 20 – Pages
        // 25 – Comments
        // 59 – Separator
        // 60 – Appearance
        // 65 – Plugins
        // 70 – Users
        // 75 – Tools
        // 80 – Settings
        // 99 – Separator

        add_menu_page(
             ILIST_NAME . ' - ' . __( 'Start', ILIST_ID_LANGUAGES ) // The text to be displayed in the title tags of the page when the menu is selected
            ,ILIST_NAME // The text to be used for the menu
            ,'manage_options' // 	The capability required for this menu to be displayed to the user
            ,ILIST_ID // The slug name to refer to this menu by. Should be unique.
            ,'ilist_general' // The function to be called to output the content for this page
            ,'data:image/svg+xml;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH5AUJEDQMWlMvYgAAAjBJREFUOMuN1M2LV3UUBvDPGXWKkkgydGVpKdFkUcKos2gVJJQF1SZqVeGm/oSgti1046JdFAVhRKK9LMKlTkYvEBFG06KhKFpEUcTgNM3T5kxcLvMbPat7z/me5z7n+zznlnUiyQEcw2WcrKqFzu/GC7gOb1XVBVeKJDNJLiXZ288Xk+xMsiXJmSRzSfYl+TzJLeP+qXUwD+Hdqlqoqm/wGg7idvxaVfNV9R3O4cGrYbg9yWKSo/2+J8mLSV5K8mjnHkiykGT7uL8mgN6AtzGHz/Abbu6J7sNXeLKqfr4qwAHwFO7B9bgWv+Prqlqe1FMjgKebwb+D9Ga80QwfxvJIgwtV9d7w8DAe6hF/7Pcl3ItZ7EHwaRO5jG04gomAf+P9ge9O4gf80wDzVXW2a89iB/4wGmcYq3giycctyDks4kDXDyZZwjRuxAm8spEPCx/gEfxSVWd6K9bOfYFduLuqjmPrWJQxw2nchjexOcldbejCJtyKD7GaZH+z3LSRyo9j30DlYAvO4ibc33eZ7q220UeTGH7fyq4MrmSqjb2EL7tWA9DFjRie6i1Ys80yZvBTj74V89230oafrapjkxj+hdNVdak/8FjbYqVH/aSqTg9+cXP4cyOV12yzu7dmB873xa9iNsnhJIdwFO+MSa1nm1N4HTur6tVRw3k8g+er6uXRiq478jW4E8+1bfZj78BSd7SRp7u2rfMTAb9tBisjb55olZ/qe1tTeKq36f/4Dyc58WPQ5jjpAAAAAElFTkSuQmCC' // The URL to the icon to be used for this menu
            ,65 // The position in the menu order this item should appear
        );
    
        // Submenu: Dashboard
        add_submenu_page( ILIST_ID, ILIST_NAME . ' - ' . __( 'General', ILIST_ID_LANGUAGES ), __( 'General', ILIST_ID_LANGUAGES ), 'manage_options', 'ilist-general', 'ilist_general');    
        // Submenu: Options
        add_submenu_page( ILIST_ID, ILIST_NAME . ' - ' . __( 'Options', ILIST_ID_LANGUAGES ), __( 'Options', ILIST_ID_LANGUAGES ), 'manage_options', 'ilist-options', 'ilist_options');
        // Submenu: Liste
        add_submenu_page( ILIST_ID, ILIST_NAME . ' - ' . __( 'List', ILIST_ID_LANGUAGES ), __( 'List', ILIST_ID_LANGUAGES ), 'manage_options', 'ilist-list', 'ilist_list');
        // Submenu: Help
        add_submenu_page( ILIST_ID, ILIST_NAME . ' - Woocommerce', 'Woocommerce', 'manage_options', 'ilist-woocommerce', 'ilist_woocommerce');
        // Submenu: Pot
        add_submenu_page( ILIST_ID, ILIST_NAME . ' - ' . __( 'Pot', ILIST_ID_LANGUAGES ), __( 'Pot', ILIST_ID_LANGUAGES ), 'manage_options', 'ilist-pot', 'ilist_pot');
        // Submenu: Share
        add_submenu_page( ILIST_ID, ILIST_NAME . ' - ' . __( 'Share', ILIST_ID_LANGUAGES ), __( 'Share', ILIST_ID_LANGUAGES ), 'manage_options', 'ilist-share', 'ilist_share');
        // Submenu: Search
        add_submenu_page( ILIST_ID, ILIST_NAME . ' - ' . __( 'Search', ILIST_ID_LANGUAGES ), __( 'Search', ILIST_ID_LANGUAGES ), 'manage_options', 'ilist-search', 'ilist_search');
        // Submenu: Automatic emails
        add_submenu_page( ILIST_ID, ILIST_NAME . ' - ' . __( 'Automatic emails', ILIST_ID_LANGUAGES ), __( 'Automatic emails', ILIST_ID_LANGUAGES ), 'manage_options', 'ilist-emails', 'ilist_emails');
        // Submenu: 
        add_submenu_page( ILIST_ID, ILIST_NAME . ' - ' . __( 'Changelog', ILIST_ID_LANGUAGES ), __( 'Changelog', ILIST_ID_LANGUAGES ), 'manage_options', 'ilist-changelog', 'ilist_changelog');
        // Submenu: Credits
        add_submenu_page( ILIST_ID, ILIST_NAME . ' - ' . __( 'Credits', ILIST_ID_LANGUAGES ), __( 'Credits', ILIST_ID_LANGUAGES ), 'manage_options', 'ilist-credits', 'ilist_credits');
    }
}

/** Initialize Nav Tabs
=================================================== */
if (!function_exists("ilist_nav_tabs")) {
	function ilist_nav_tabs() {
        // Main tabs
        $ilist_nav_tabs = [
			 ILIST_ID . "-general"     => __( 'General', ILIST_ID_LANGUAGES )
			,ILIST_ID . "-options"     => __( 'Options', ILIST_ID_LANGUAGES )
			,ILIST_ID . "-list"        => __( 'List', ILIST_ID_LANGUAGES )
			,ILIST_ID . "-woocommerce" => 'WooCommerce'
			,ILIST_ID . "-pot"         => __( 'Pot', ILIST_ID_LANGUAGES )
			,ILIST_ID . "-share"       => __( 'Share', ILIST_ID_LANGUAGES )
			,ILIST_ID . "-search"      => __( 'Search', ILIST_ID_LANGUAGES )
			,ILIST_ID . "-emails"      => __( 'Emails', ILIST_ID_LANGUAGES )
            ,ILIST_ID . "-changelog"   => __( 'Changelog', ILIST_ID_LANGUAGES )
            ,ILIST_ID . "-credits"     => __( 'Credits', ILIST_ID_LANGUAGES )
		];
        if (has_filter('ilist_extra_nav_tabs')) {
            // Extra tabs
            $ilist_extra_nav_tabs = apply_filters( 'ilist_extra_nav_tabs', $ilist_nav_tabs );
            return $ilist_extra_nav_tabs;
        } else {
            // Only main tabs
            return $ilist_nav_tabs;
        }
	}
}

?>