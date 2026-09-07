<?php
/**
 * Ilist
 *
 * @package     			ILIST Kado
 * @author      			DEV By INFORMATUX
 * @copyright   			2023 INFORMATUX
 * @license     			GPL-3.0+
 *
 * @ilist
 * Plugin Name: 			ILIST Kado
 * Plugin URI:  			https://dev.informatux.com/woocommerce-ilist
 * Description: 			Gestion de listes de naissance, mariage, anniversaire, noel, etc. pour WooCommerce avec possibilités de financement participatif (crowdfunding)
 * Version:     			2.2.0
 * Author:      			DEV By INFORMATUX
 * Author URI:  			https://dev.informatux.com
 * Text Domain: 			ilist-translate
 * Domain Path:				/languages
 * License:     			GPL-3.0+
 * License URI: 			http://www.gnu.org/licenses/gpl-3.0.txt
 * Requires at least:		6.0
 * Tested up to:			7.1
 * Requires PHP:			7.4
 * WC requires at least:	8.0
 * WC tested up to:			11.1
 *
 * ██╗███╗   ██╗███████╗ ██████╗ ██████╗ ███╗   ███╗ █████╗ ████████╗██╗   ██╗██╗  ██╗
 * ██║████╗  ██║██╔════╝██╔═══██╗██╔══██╗████╗ ████║██╔══██╗╚══██╔══╝██║   ██║╚██╗██╔╝
 * ██║██╔██╗ ██║█████╗  ██║   ██║██████╔╝██╔████╔██║███████║   ██║   ██║   ██║ ╚███╔╝
 * ██║██║╚██╗██║██╔══╝  ██║   ██║██╔══██╗██║╚██╔╝██║██╔══██║   ██║   ██║   ██║ ██╔██╗
 * ██║██║ ╚████║██║     ╚██████╔╝██║  ██║██║ ╚═╝ ██║██║  ██║   ██║   ╚██████╔╝██╔╝ ██╗
 * ╚═╝╚═╝  ╚═══╝╚═╝      ╚═════╝ ╚═╝  ╚═╝╚═╝     ╚═╝╚═╝  ╚═╝   ╚═╝    ╚═════╝ ╚═╝  ╚═╝
 * 
 */

/** Ne doit pas être mis en cache, ni par le client, ni par les proxy intermédiaires
=============================================== */
if (!headers_sent()) {
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");   // Date dans le passé
}

/** SESSION START (php OR wc)
=============================================== */
defined('ILIST_SESSION') or define('ILIST_SESSION', 'php'); // php, wc
add_action( 'init', 'ilist_init_session_start', 1 );
if (!function_exists("ilist_init_session_start")) {
    function ilist_init_session_start () {
        // Early initialize customer session
        switch(ILIST_SESSION) {
            case "wc":
                // WOOCOMMERCE Session start
                if ( isset(WC()->session) && ! WC()->session->has_session() ) {
                    WC()->session->set_customer_session_cookie( true );
                }
            break;
            case "php":
                // --------------------------------------------
                // PHP_SESSION_DISABLED (0) si les sessions sont désactivées
                // PHP_SESSION_NONE (1) si les sessions sont activées, mais qu'aucune n'existe
                // PHP_SESSION_ACTIVE (2) si les sessions sont activées, et qu'une existe
                // --------------------------------------------
                if (session_status() === PHP_SESSION_NONE || !session_id()) {
                    session_start();
                }
            break;
        }
    }
}

/** Blocking direct access to plugin
=============================================== */
defined('ABSPATH') or die('Are you crazy!');

/** Define constants
=============================================== */
defined('ILIST_PATH') or define('ILIST_PATH', plugin_dir_path(__FILE__));
defined('ILIST_URL') or define('ILIST_URL', plugin_dir_url(__FILE__));
defined('ILIST_BASE') or define('ILIST_BASE', plugin_basename(__FILE__));
defined('ILIST_FILE') or define('ILIST_FILE', __FILE__);
defined('ILIST_ID') or define('ILIST_ID', 'ilist');
defined('ILIST_ID_LANGUAGES') or define('ILIST_ID_LANGUAGES', 'ilist-translate');
// --- SQL Tables
defined('ILIST_TBL_MAIN') or define('ILIST_TBL_MAIN', $wpdb->prefix . 'ilist');
defined('ILIST_TBL_PRODUCT') or define('ILIST_TBL_PRODUCT', $wpdb->prefix . 'ilist_product');
defined('ILIST_TBL_TYPE') or define('ILIST_TBL_TYPE', $wpdb->prefix . 'ilist_type');
defined('ILIST_TBL_CAGNOTTE') or define('ILIST_TBL_CAGNOTTE', $wpdb->prefix . 'ilist_cagnotte');
// --- Infos
defined('ILIST_DEFAULT_NAME') or define('ILIST_DEFAULT_NAME', __('List', ILIST_ID_LANGUAGES));
defined('ILIST_NAME') or define('ILIST_NAME', 'ILIST Kado');
// --- Updates
defined('ILIST_SERVER_UPDATES_URL') or define('ILIST_SERVER_UPDATES_URL', 'https://dev.informatux.com');
// --- Wordpress / WC Min. Version
defined('ILIST_WC_MIN_VERSION') or define('ILIST_WC_MIN_VERSION', '8.0.0');
defined('ILIST_WORDPRESS_MIN_VERSION') or define('ILIST_WORDPRESS_MIN_VERSION', '6.0');
// --- Font Awesome version in use
defined('ILIST_AWESOME_VERSION') or define('ILIST_AWESOME_VERSION', '6.3.0');

/* Compatibilité HPOS (High-Performance Order Storage)
 *
 * Sans cette déclaration, WooCommerce considère le plugin comme incompatible
 * et affiche un avertissement, voire empêche l'activation de HPOS.
 * Doit être déclarée sur 'before_woocommerce_init'.
=============================================== */
add_action( 'before_woocommerce_init', 'ilist_declare_hpos_compatibility' );
if ( !function_exists( 'ilist_declare_hpos_compatibility' ) ) {
	function ilist_declare_hpos_compatibility() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', ILIST_FILE, true );
		}
	}
}

/* IS multisite (Network)
=============================================== */
if ( !function_exists( 'is_plugin_active_for_network' ) ) {
    // Makes sure the plugin is defined before trying to use it
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}
// Is the plugin active for the entire network
if ( is_plugin_active_for_network( 'ilist/ilist.php' ) ) {
	define("ILIST_NETWORK_ACTIVATED", true);
} else {
    define("ILIST_NETWORK_ACTIVATED", false);
}

/* Include new functions (multisite for ILIST)
=============================================== */
$ilistIncludeFiles = ['multisite'];
if (array_keys( $ilistIncludeFiles, true )) {
	foreach ($ilistIncludeFiles as $ilistIncludeFile) {
		$file = ILIST_PATH . ILIST_ID . '-' . $ilistIncludeFile . '.php';
		if (file_exists($file)) require_once($file);
	}
}

/* Make sure WooCommerce is active
=============================================== */
$ilist_wc_is_activated = false;
if ( is_multisite() ) {
    if ( is_plugin_active( 'woocommerce/woocommerce.php' ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) $ilist_wc_is_activated = true;
} else {
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', ilist_get_option( 'active_plugins' ) ) ) )  $ilist_wc_is_activated = true;
}
if ( !$ilist_wc_is_activated ) {
	// --- WooCommerce is not installed
	add_action( 'admin_notices', 'ilist_notice_no_wc' );
	if ( !function_exists("ilist_notice_no_wc") ) {
		function ilist_notice_no_wc() {
			?>
			<div class="notice-error notice">
				<img src="https://dev.informatux.com/upload/dev/ilist-logo-wt.png" style="height: 50px; float: left; padding: 9px 10px 0 0;" alt="ILIST">
				<p style="display: block;">
					<h3 style="margin: 0; line-height: 24px;">ILIST Plugin</h3>
					<?php echo sprintf( __( "<strong>WooCommerce</strong> must be installed before using ILIST!", ILIST_ID_LANGUAGES ), get_admin_url() . 'admin.php?page=wc-settings&tab=checkout&section=wcgatewaymp' ); ?>
				</p>
			</div>
			<?php
		}
	}
	return;
}

/** ILIST Get Infos
 * --------------------------------------------
 * Name: Name of the plugin, must be unique.
 * Title: Title of the plugin and the link to the plugin's web site.
 * Description: Description of what the plugin does and/or notes from the author.
 * Author: The author's name
 * AuthorURI: The authors web site address.
 * Version: The plugin version number.
 * PluginURI: Plugin web site address.
 * TextDomain: Plugin's text domain for localization.
 * DomainPath: Plugin's relative directory path to .mo files.
 * Network: Boolean. Whether the plugin can only be activated network wide.
=============================================== */
if ( !function_exists( 'get_plugin_data' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
if ( !function_exists( 'ilist_get_version' ) ) {
    function ilist_get_version( $ilist_infos = 'Version' ) { 
        $plugin_data = get_plugin_data( __FILE__ );
        $plugin_version = $plugin_data[ "$ilist_infos" ];
        return $plugin_version;
    }
}

/* Updates
=============================================== */
require ILIST_PATH . ILIST_ID . '-update.php';
// Le plugin est public : plus de clé de licence transmise au serveur de mises
// à jour. Seuls restent les éléments nécessaires au choix de la bonne version.
$ilist_update_checker = Puc_v4_Factory::buildUpdateChecker(
	 ILIST_SERVER_UPDATES_URL . '/updates/' . ILIST_ID . '.php?h=' . rawurlencode( $_SERVER['SERVER_NAME'] ) . '&w=' . rawurlencode( get_bloginfo('version') ) . '&v=' . rawurlencode( ilist_get_version() )
	,__FILE__
	,ILIST_ID
);

/** Load plugin translations
=============================================== */
add_action( 'plugins_loaded', 'ilist_translate_load_textdomain', 1 );
if ( !function_exists( 'ilist_translate_load_textdomain' ) ) {
	function ilist_translate_load_textdomain() {
		$path = basename( dirname( __FILE__ ) ) . '/languages/';
		load_plugin_textdomain( ILIST_ID_LANGUAGES, false, $path );
	}
}

/** Initialize CLASS Ilist
=============================================== */
$ilist_class = ILIST_PATH . 'class' . DIRECTORY_SEPARATOR . 'ilist.php';
if (file_exists($ilist_class)) {
	// Call class ILIST: $Ilist
	require_once($ilist_class);
}

/** ILIST Framework
=================================================== */
// --- Framework Path / URL
defined('FRAMEWORK_ILIST_PATH') or define('FRAMEWORK_ILIST_PATH', ILIST_PATH . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR);
defined('FRAMEWORK_ILIST_URL') or define('FRAMEWORK_ILIST_URL', ILIST_URL . 'core/');
$ilistFrameworkExist = FRAMEWORK_ILIST_PATH . "framework.php";
if (file_exists($ilistFrameworkExist)) {
	require_once($ilistFrameworkExist);
	$ilist_framework = new ilist_framework(ILIST_ID);
}

/** Load plugin files
=============================================== */
if ( !function_exists( 'is_plugin_active' ) )
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/** Initialize plugin Debug Mode (Dev Only)
=============================================== */
defined('_ILIST_DEBUG') or define('_ILIST_DEBUG', false);

/** Initialize plugin Files
=============================================== */
$ilistFiles = ['system', 'functions', 'style', 'menu'];
if (array_keys( $ilistFiles, true )) {
	foreach ($ilistFiles as $ilistFile) {
		$file = ILIST_PATH . ILIST_ID . '-' . $ilistFile . '.php';
		if (file_exists($file)) require_once($file);
	}
}

/** API RESTful
============================================= */
$ilistAPIFiles = ['api'];
if (array_keys( $ilistAPIFiles, true )) {
	foreach ($ilistAPIFiles as $ilistAPIFile) {
		$file = ILIST_PATH . 'apirest/' . $ilistAPIFile . '.php';
		if (file_exists($file)) require_once($file);
	}
}

/** Create tab's plugin
============================================= */
$ilistOptions = [ 'general', 'lists', 'list-types', 'list-products', 'sold', 'options', 'list', 'woocommerce', 'pot', 'share', 'search', 'emails', 'changelog', 'credits' ];
foreach ($ilistOptions as $ilistOption) {
	$ilistOptionFile = ILIST_PATH . 'includes/' . ILIST_ID . '-' . $ilistOption . '.php';
	if (file_exists($ilistOptionFile)) require_once($ilistOptionFile);
}

/** Include CLASSES
============================================= */
$ilistClasses = ['lists', 'list-types', 'list-products', 'sold', 'widget'];
if (array_keys( $ilistClasses, true )) {
	foreach ($ilistClasses as $ilistClass) {
		$class = ILIST_PATH . 'class' . DIRECTORY_SEPARATOR . ILIST_ID . '-class-' . $ilistClass . '.php';
		if (file_exists($class)) require_once($class);
	}
}

/** Shortcodes
============================================= */
$ilistShortcodes = ['general', 'search'];
if (array_keys( $ilistShortcodes, true )) {
	foreach ($ilistShortcodes as $ilistShortcode) {
		$shortcode = ILIST_PATH . 'shortcodes' . DIRECTORY_SEPARATOR .  ILIST_ID . '-shortcode-' . $ilistShortcode . '.php';
		if (file_exists($shortcode)) require_once($shortcode);
	}
}

/** ICustomizer Meta links in plugins page
=============================================== */
add_filter( 'plugin_row_meta', 'ilist_plugin_row_meta', 10, 2 );
if ( !function_exists( 'ilist_plugin_row_meta' ) ) {
    function ilist_plugin_row_meta( $links, $file ) {
        if (strpos($file, ILIST_BASE) !== false) {
            $new_links = array(
                'faq' => '<a href="https://doc.ilist-kado.com" target="_blank">' . __( 'Docs & FAQs', ILIST_ID_LANGUAGES ) . '</a>',
            );
            $links = array_merge($links, $new_links);
        }
        return $links;
    }
}

/** Adds plugin page links
=============================================== */
add_filter( 'plugin_action_links_' . ILIST_BASE, 'ilist_plugin_links' );
if ( !function_exists("ilist_plugin_links") ) {
	function ilist_plugin_links( $links ) {
		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=ilist' ) . '">' . __( 'Configure', ILIST_ID_LANGUAGES ) . '</a>'
		);
		return array_merge( $plugin_links, $links );
	}
}

// ----------------------------------------------------------------------
// ----------------------------------------------------------------------
// ----------------------------------------------------------------------
/**
 * Add new endpoint to WooCommerce account menu
 *
 * 2 methodes au choix :
 * add_action('woocommerce_init', 'ilist_wc_end_point');
 * add_action( 'init', 'ilist_wc_end_point' );
 *
 * -----------------------------------------------
 *
 * IMPORTANT :
 * Une fois les actions et filtres en place,
 * il faut aller sur la page admin -> reglages -> permaliens
 * et sauvegarder les paramètres, sinon vous aurez
 * un page 404 comme resultat (en toute logique)
 */
add_action( 'init', 'ilist_wc_end_point' );
if ( !function_exists("ilist_wc_end_point") ) {
	function ilist_wc_end_point() {
		if(class_exists('WooCommerce')){
			// L'endpoint doit être (re)déclaré à chaque requête : c'est ce qui
			// alimente les règles de réécriture.
			add_rewrite_endpoint( 'ilist', EP_ROOT | EP_PAGES );
			// En revanche flush_rewrite_rules() recalcule TOUTES les règles du
			// site et réécrit l'option rewrite_rules en base. L'appeler à
			// chaque chargement de page (front comme admin) est très coûteux.
			// On ne le fait donc qu'une fois par version du plugin : l'endpoint
			// est ainsi enregistré à la première requête suivant une
			// installation ou une mise à jour, sans passage manuel par
			// Réglages > Permaliens, et sans coût sur les requêtes suivantes.
			if ( ilist_get_option( 'ilist_rewrite_rules_flushed' ) !== ilist_get_version() ) {
				flush_rewrite_rules();
				ilist_update_option( 'ilist_rewrite_rules_flushed', ilist_get_version() );
			}
		}
	}
}
// ----------------------------------------------------------------------
add_filter( 'query_vars', 'ilist_endpoint_query_vars', 0 );
if ( !function_exists("ilist_endpoint_query_vars") ) {
	function ilist_endpoint_query_vars( $vars ) {
		$vars[] = 'ilist';
		return $vars;
	}
}
// ----------------------------------------------------------------------
add_action( 'after_switch_theme', 'ac_ilist_flush_rewrite_rules' );
if ( !function_exists("ac_ilist_flush_rewrite_rules") ) {
	function ac_ilist_flush_rewrite_rules() {
		flush_rewrite_rules();
	}
}
// ----------------------------------------------------------------------
/**
 * Re-order WooCommerce Menu ACCOUNT
 * Ajoute le custom endpoint comme un nouvel item au menu du compte client
 *
 * 2 methodes :
 * La premiere place l'entree juste avant "deconnexion"
 * La seconde reorganise a votre guise le menu "my-account"
 * 
 */
add_filter( 'woocommerce_account_menu_items', 'ilist_endpoint_acct_menu_item' );
if ( !function_exists("ilist_endpoint_acct_menu_item") ) {
	function ilist_endpoint_acct_menu_item( $items ) {
		$logout = $items['customer-logout'];
		unset( $items['customer-logout'] );
		// ILIST_DEFAULT_NAME est déjà le résultat d'un __() (voir sa
		// définition) : la repasser à __() sur le domaine 'woocommerce'
		// ne traduisait rien et n'était pas extractible par les outils i18n.
		$items['ilist'] = ILIST_DEFAULT_NAME;
		$items['customer-logout'] = $logout;
			return $items;
	}
}
// ----------------------------------------------------------------------
/**
 * Affichage du contenu du nouveau "endpoint"
 */
add_action( 'woocommerce_account_ilist_endpoint', 'ilist_fetch_content_ilist_endpoint' );
if ( !function_exists("ilist_fetch_content_ilist_endpoint") ) {
	function ilist_fetch_content_ilist_endpoint() {
		// Show ilist Tab
		echo do_shortcode( '[ilist]' );
	
	}
}
// ----------------------------------------------------------------------
// ----------------------------------------------------------------------
// ----------------------------------------------------------------------
// --- Add action when payment process is complete
add_action( 'woocommerce_checkout_order_processed', 'ilist_wc_checkout_order', 10, 3 );
// ----------------------------------------------------------------------
// ----------------------------------------------------------------------
// ----------------------------------------------------------------------
add_action( 'woocommerce_remove_cart_item', 'ilist_cart_updated', 10, 2 );
if ( !function_exists("ilist_cart_updated") ) {
	function ilist_cart_updated( $cart_item_key, $cart ) {
		// Initialize
		$product_id = $cart->cart_contents[ $cart_item_key ]['product_id'];
		// Check if product_id exists
		if (!$product_id) {
			return;
		}
        // PHP Session Start
        if (ILIST_SESSION == 'php') session_start(['read_and_close'=>1]);
        // Initialize
        $session_ilist_customer_products = ilist_get_session( 'ilist_customer_products' );
		// Loop for searching product id in Session
		foreach($session_ilist_customer_products as $index => $value) {
			if ( $value['product_id'] == $product_id) {
                switch(ILIST_SESSION) {
                    case "wc":
                        // WOOCOMMERCE SESSION
                        WC()->session->set( $session_ilist_customer_products[$index] , null );
                    break;
                    case "php":
                        // PHP SESSION
                        unset($_SESSION['ilist_customer_products'][$index]);
                    break;
                }
			}
		}
        // PHP Session Close
        if (ILIST_SESSION == 'php') session_commit();
	}
}
// ----------------------------------------------------------------------
// ----------------------------------------------------------------------
// ----------------------------------------------------------------------
//session_write_close();
// ----------------------------------------------------------------------
// ----------------------------------------------------------------------
// ----------------------------------------------------------------------
?>