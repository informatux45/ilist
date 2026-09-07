<?php
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// Blocking direct access to plugin      -=
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
defined('ABSPATH') or die('Are you crazy!');

// ------------------------------------
// Sets locale information (FR)
// ------------------------------------
ilist_get_locale();

// -------------------------------------
// Show message next to logged in or not
// -------------------------------------
if (!is_user_logged_in()) {
	
	do_action( 'ilist_before_login' );
	
	do_action( 'ilist_login' );

	do_action( 'ilist_after_login' );
	
} else {

	// -------------------------------------
	// Share list
	// -------------------------------------
	$share_list                  = ilist_get_option( 'ilist_share_list' );
	$notify_admins               = ilist_get_option( 'ilist_notify_admins' );
	$ilist_url_complete          = ilist_get_option( 'ilist_url_complete' );
	$ilist_search_url_complete   = ilist_get_option( 'ilist_search_url_complete' );
	$ilist_hide_add_product      = ilist_get_option( 'ilist_hide_add_product' );
	$ilist_disable_list_creation = ilist_get_option( 'ilist_disable_list_creation_registered_users' );

	// Button "creer une liste
	if (!$ilist_disable_list_creation) {
		echo '<a href="' . $ilist_page_used . '?a=creer-une-liste" class="button alt ilist-create">';
		echo __( 'Create a new list', ILIST_ID_LANGUAGES );
		echo '</a>';
		echo '<br><br>';
	}
	
	if (isset($ilist_show_tabs) && array_keys( $ilist_show_tabs, true )) {
		
		// Open TABLE
		echo '<table class="shop_table cart ilist-table" cellspacing="0">
            <thead>
                <tr>
                    <th class="ilist-list-name">' . __( "Your lists", ILIST_ID_LANGUAGES ) . '</th>
                    <th class="ilist-creator-name">' . __( "Name", ILIST_ID_LANGUAGES ) . '</th>
                    <th class="ilist-date">' . __( "Created on", ILIST_ID_LANGUAGES ) . '</th>
                    <th class="ilist-items">' . __( "Products", ILIST_ID_LANGUAGES ) . '</th>
                    <th class="ilist-update">' . __( "Edit", ILIST_ID_LANGUAGES ) . '</th>
                </tr>
            </thead>
            <tbody>';
		foreach ( $ilist_show_tabs as $row_tab ) {
			echo '<tr>
				<td class="ilist-list-name"><a href="' . $ilist_url_current . '?a=liste&k=' . $row_tab->keyaccess . '">' . esc_html($row_tab->name) . '</a></td>
				<td class="ilist-creator-name">' . esc_html($row_tab->firstname) . ' ' . esc_html($row_tab->lastname) . '</td>
				<td class="ilist-date">' . ilistConvertDate($row_tab->date, 'FRH') . '</td>
				<td class="ilist-items">' .ilist_get_nb_products_by_list($row_tab->id) . '</td>
				<td class="ilist-update ilist-acenter">';
					if ($row_tab->password != '') {
						echo '<i class="fa fa-lock" title="' . __('Password protected list', ILIST_ID_LANGUAGES ) . '"></i>&nbsp;&nbsp;&nbsp;';
					} else {
						echo '<i class="fa fa-unlock" title="' . __('No Password needed', ILIST_ID_LANGUAGES ) . '"></i>&nbsp;&nbsp;&nbsp;';
					}
					if ($share_list) {
						echo '<a href="javascript:;" onclick="ilist_show_share_icons(\'ilist-share-' . $row_tab->keyaccess . '\')"><i class="fa fa-share-alt" aria-hidden="true" title="' . __('Share this list', ILIST_ID_LANGUAGES ) . '"></i></a>&nbsp;&nbsp;&nbsp;';
					}
					if ($notify_admins) {
						echo '<a href="javascript:;" onclick="ilist_notify_admins(\'' . $row_tab->id . '\', \'' . __('Notify administrators that you want to retrieve items from this list?', ILIST_ID_LANGUAGES ) . '\', \'' . __('Your request has been sent to the administrators', ILIST_ID_LANGUAGES ) . '\')"><i class="fa fa-envelope-o" aria-hidden="true" title="' . __('Collect your items', ILIST_ID_LANGUAGES ) . '"></i></a>&nbsp;&nbsp;&nbsp;';
					}
			  echo '<a href="' . $ilist_url_current . '?a=supprimer-une-liste&lid=' . $row_tab->id . '" title="' . __( "Remove list", ILIST_ID_LANGUAGES ) . '" onclick="return confirm(\'' . __( "Want to remove your list?", ILIST_ID_LANGUAGES ) . '\');"><i class="fa fa-trash" aria-hidden="true"></i></a>
					&nbsp;
					<a href="' . $ilist_url_current . '?a=modifier-une-liste&lid=' . $row_tab->id . '" title="' . __( "Edit list", ILIST_ID_LANGUAGES ) . '"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
					&nbsp;
					<a href="' . $ilist_url_current . '?a=liste&k=' . $row_tab->keyaccess . '" title="' .  ( (!$ilist_hide_add_product) ? __( "Add products to the list", ILIST_ID_LANGUAGES ) : __( "Show products from your list", ILIST_ID_LANGUAGES ) ) . '"><i class="fa fa-plus" aria-hidden="true"></i></a>
				</td>
			</tr>';
			if ($share_list) {
				echo '<tr id="ilist-share-' . $row_tab->keyaccess . '" class="ilist-share-nodisplay">';
					echo '<td colspan="5">';
						echo ilist_get_share_icons( $ilist_url_complete . '?a=liste&k=' . $row_tab->keyaccess, $row_tab->keyaccess );
					echo '</td>';
				echo '</tr>';
			}
		}
		// Close TABLE
		echo '</tbody>';
		echo '</table>';
		
	} else {
		
		echo __( "You have not created lists yet", ILIST_ID_LANGUAGES );
	
	}
}