<?php
/** Blocking direct access to plugin
============================================= */
defined('ABSPATH') or die('Are you crazy!');

/**
 * Wordpress function get_site_option and get_option
 * to differentiate between multisite and non-multisite installs
 *
 * @param	$option_name		Option name
 * @param 	$default_value		Default value - Default: false
 * @param 	$deprecated			To use cache - Multisite only - Always set to true - Default: true
 *
 * @return string
 */
if (!function_exists("ilist_get_option")) {
	function ilist_get_option($option_name, $default = false, $deprecated = true) {
		if ( ILIST_NETWORK_ACTIVATED == true ) {
			// Get network site option
			return get_site_option($option_name, $default, $deprecated);
		} else {
			// Get blog option (No deprecated value)
			return get_option($option_name, $default);
		}
	}
}

/**
 * Wordpress function add_site_option and add_option
 * to differentiate between multisite and non-multisite installs
 *
 * @param	$option_name		Option name
 * @param 	$option_value		Option value
 * @param 	$deprecated			Not used anymore - Default: void
 * @param 	$autoload			Load the option when WordPress starts up - Default: yes - Accepts yes|no
 *
 * @return bool (True if the option was added, false otherwise)
 */
if (!function_exists("ilist_add_option")) {
	function ilist_add_option($option_name, $option_value, $deprecated = '', $autoload = 'yes') {
		if ( ILIST_NETWORK_ACTIVATED == true ) {
			// Add network site option (no deprecated value AND no autoload value)
			return add_site_option($option_name, $option_value);
		} else {
			// Add blog option
			return add_option($option_name, $option_value, $deprecated, $autoload);
		}
	}
}

/**
 * Wordpress function update_site_option and update_option
 * to differentiate between multisite and non-multisite installs
 *
 * @param	$option_name		Option name
 * @param 	$option_value		Option value
 * @param 	$autoload			Load the option when WordPress starts up - Default: null -  Accepts yes|true to enable or no|false to disable
 *
 * @return bool (True if the option was updated, false otherwise)
 */
if (!function_exists("ilist_update_option")) {
	function ilist_update_option($option_name, $option_value, $autoload = null) {
		if ( ILIST_NETWORK_ACTIVATED == true ) {
			// Update network site option (no autoload value)
			return update_site_option($option_name, $option_value);
		} else {
			// Update blog option
			return update_option($option_name, $option_value, $autoload);
		}
	}
}

/**
 * Wordpress function delete_site_option and delete_option
 * to differentiate between multisite and non-multisite installs
 *
 * @param	$option_name		Option name
 *
 * @return bool (True if the option was deleted, false otherwise)
 */
if (!function_exists("ilist_delete_option")) {
	function ilist_delete_option($option_name) {
		if ( ILIST_NETWORK_ACTIVATED == true ) {
			// Delete network site option
			return delete_site_option($option_name);
		} else {
			// Delete blog option
			return delete_option($option_name);
		}
	}
}

?>