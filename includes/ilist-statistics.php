<?php
/** Blocking direct access to plugin
=================================================== */
defined('ABSPATH') or die('Are you crazy!');

/**
 * Statistiques de liste (ventes réalisées)
 *
 * Ce module vient de l'addon séparé "ILIST Kado - List Statistics" v1.1,
 * intégré au plugin principal le 2026-09-07. L'addon n'a jamais contenu de
 * code de calcul : seuls l'onglet d'options et les deux points d'entrée
 * (action de ligne dans le tableau des listes, bouton sous les produits)
 * existaient. Son système de licence, sa copie de vendor/Puc et son amorçage
 * n'ont pas été repris.
 *
 * Le module s'accroche volontairement aux MÊMES filtres publics que l'addon
 * utilisait (ilist_list_extra_row_actions, ilist_admin_list_products_after) :
 * ces points d'extension restent ainsi exercés et documentés pour d'éventuels
 * addons tiers.
 *
 * Le contenu des statistiques reste à définir : il se branche sur l'action
 * 'ilist_statistics_content', déclenchée par ilist_statistics_render().
 */

/** Create tab's STATISTICS
=============================================== */
if (!function_exists("ilist_statistics")) {
	function ilist_statistics() {

		/** INFORMATUX Framework
		=================================================== */
		global $ilist_framework;

		/** Intialisation
		=============================================== */
		$ilist_page = "statistics";

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
		echo $ilist_framework->addBreak( __( "Statistics activation", ILIST_ID_LANGUAGES ) );
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'List Statistics (Admin)', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_statistics_admin'
				,'name'    => 'ilist_statistics_admin'
				,'checked' => ilist_get_option('ilist_statistics_admin', '1') // Default: 1
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'Yes - Default: Enabled', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		echo $ilist_framework->addRadioYN(
			 __( 'List Statistics (Client)', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_statistics_front'
				,'name'    => 'ilist_statistics_front'
				,'checked' => ilist_get_option('ilist_statistics_front', '1') // Default: 1
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'Yes - Default: Enabled', ILIST_ID_LANGUAGES ) // Description
		);
		// ----------------------------------------
		echo $ilist_framework->addNote(
			 __( 'Statistics content', ILIST_ID_LANGUAGES )
			,'<em>' . __( "The content of the statistics has not been defined yet. The two switches above already control the display of the entry points (list table action and button under the products).", ILIST_ID_LANGUAGES ) . '</em>'
		);
		// ----------------------------------------
		// --- Hiddens / Buttons
		// ----------------------------------------
		echo $ilist_framework->addInput( 'submit', '', array('value' => __( "Save changes", ILIST_ID_LANGUAGES)) );
		// ----------------------------------------
		echo $ilist_framework->closeTable();
		echo $ilist_framework->closeForm();
		// ----------------------------------------

		/** End
		=================================================== */
		echo $ilist_framework->general_infos('end');

	}
}

/**
 * Lien vers les statistiques d'une liste
 *
 * @param	$text		Libellé du lien
 * @param	$list_id	ID de la liste ; à défaut, l'URL courante est réutilisée
 * @param	$class		Classe CSS
 * @param	$style		Style inline
 *
 * @return string
 */
if (!function_exists("ilist_statistics_link")) {
	function ilist_statistics_link($text = false, $list_id = 0, $class = false, $style = false) {
		$_text  = (!$text)  ? '' : $text;
		$_class = (!$class) ? '' : ' class="' . esc_attr($class) . '"';
		$_style = (!$style) ? '' : ' style="' . esc_attr($style) . '"';

		// Les statistiques s'affichent sur l'écran des produits d'une liste,
		// seul endroit où le hook de rendu est déclenché.
		if ( $list_id ) {
			$url = admin_url( 'admin.php?page=ilist_products&id=' . (int) $list_id . '&sl=1' );
		} else {
			$url = add_query_arg( 'sl', '1', ilist_current_url() );
		}

		return '<a href="' . esc_url( $url ) . '"' . $_class . $_style . '><span>' . $_text . '</span></a>';
	}
}

/** Action "Statistiques" dans le tableau des listes (admin)
=============================================== */
add_filter( 'ilist_list_extra_row_actions', 'ilist_statistics_row_actions', 10, 2 );
if (!function_exists("ilist_statistics_row_actions")) {
	function ilist_statistics_row_actions( $actions, $item = array() ) {
		if ( !ilist_get_option( 'ilist_statistics_admin' ) ) return $actions;

		$list_id = ( isset($item['id']) ) ? (int) $item['id'] : 0;
		$actions['statistics'] = ilist_statistics_link( __( 'Statistics', ILIST_ID_LANGUAGES ), $list_id );

		return $actions;
	}
}

/** Bouton sous la liste des produits (admin)
=============================================== */
add_action( 'ilist_admin_list_products_after', 'ilist_statistics_admin_button', 10, 1 );
if (!function_exists("ilist_statistics_admin_button")) {
	function ilist_statistics_admin_button( $content = '' ) {
		if ( !ilist_get_option( 'ilist_statistics_admin' ) ) return;

		$list_id = ( isset($_GET['id']) ) ? (int) $_GET['id'] : 0;

		echo '<div style="display: block; clear: both; padding: 10px 0 0 0;">';
		echo ilist_statistics_link(
			 __( 'Printing list statistics - Sales made', ILIST_ID_LANGUAGES )
			,$list_id
			,'ilist-button'
			,'background-color: deepskyblue;'
		);
		echo '</div>';

		// Affichage des statistiques quand le lien a été suivi
		if ( isset($_GET['sl']) && $_GET['sl'] == '1' ) {
			ilist_statistics_render();
		}
	}
}

/**
 * Rendu des statistiques d'une liste
 *
 * Le contenu reste à définir. Le point d'extension est en place pour que le
 * calcul et l'affichage soient écrits ici (ou greffés depuis l'extérieur)
 * sans retoucher au câblage.
 *
 * @return void
 */
if (!function_exists("ilist_statistics_render")) {
	function ilist_statistics_render() {
		$list_id = ( isset($_GET['id']) ) ? (int) $_GET['id'] : 0;

		echo '<div class="ilist-panel ilist-leftbar ilist-sand" style="margin-top: 1em;">';
		if ( has_action( 'ilist_statistics_content' ) ) {
			do_action( 'ilist_statistics_content', $list_id );
		} else {
			echo '<p><strong>' . esc_html__( 'List statistics', ILIST_ID_LANGUAGES ) . '</strong><br>';
			echo '<em>' . esc_html__( 'The content of the statistics has not been defined yet.', ILIST_ID_LANGUAGES ) . '</em></p>';
		}
		echo '</div>';
	}
}

?>
