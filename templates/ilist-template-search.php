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
// Initialize return
// -------------------------------------
$return = "";

// -------------------------------------
// Share list
// -------------------------------------
$share_list                = ilist_get_option( 'ilist_share_list' );
$ilist_url_complete        = ilist_get_option( 'ilist_url_complete' );
$ilist_search_url_complete = ilist_get_option( 'ilist_search_url_complete' );
// -------------------------------------
// Show search form with results (if)
// -------------------------------------
$return .= '<form id="ilist-search" action="#ilist-search" class="ilist-search-form" method="post">
	<p class="form-row form-row-wide">
		<label for="search-list">' . __( "Find a list", ILIST_ID_LANGUAGES ) . '</label>
		<input name="search-list" id="search-list" class="find-input" minlength="3" value="" placeholder="' . __( "Enter name, email", ILIST_ID_LANGUAGES ) . '" type="text">
	</p>
	<p class="form-row form-row-wide">
		<input class="button" value="' . __( "Search", ILIST_ID_LANGUAGES ) . '" type="submit">
	</p>
</form>';

if (isset($ilist_show_tabs) && array_keys( $ilist_show_tabs, true )) {
	
	// Open TABLE
	$return .= '<table class="shop_table cart ilist-table" cellspacing="0">
		<thead>
			<tr>
				<th class="ilist-list-name">' . __( "Name of list", ILIST_ID_LANGUAGES ) . '</th>
				<th class="ilist-creator-name">' . __( "Name", ILIST_ID_LANGUAGES ) . '</th>
				<th class="ilist-date">' . __( "Created", ILIST_ID_LANGUAGES ) . '</th>';
				if ( $share_list ) $return .= '<th class="ilist-share">' . __( "Share", ILIST_ID_LANGUAGES ) . '</th>';
			$return .= '</tr>
		</thead>
		<tbody>';
	foreach ( $ilist_show_tabs as $row_tab ) {
		$list_description = (trim($row_tab->description) != '') ? '<br><em class="ilist-description">' . esc_html($row_tab->description) . '</em>' : '';
		$return .= '<tr>
				<td class="ilist-list-name"><a href="' . ilist_get_url_page('search', 'a=liste&k=' . $row_tab->keyaccess) . '">' . esc_html($row_tab->name) . '</a>' . $list_description . '</td>
				<td class="ilist-creator-name">' . esc_html($row_tab->firstname) . ' ' . esc_html($row_tab->lastname) . '</td>
				<td class="ilist-date">' . ilistConvertDate($row_tab->date, 'FRH') . '</td>';
				if ( $share_list ) $return .= '<td class="ilist-share"><a href="javascript:;" onclick="ilist_show_share_icons(\'ilist-share-' . $row_tab->keyaccess . '\')"><i class="fa fa-share-alt" aria-hidden="true"></i></a></td>';
				//$return .= '<td class="ilist-share"><a href="#"><i class="fa fa-share-alt" aria-hidden="true"></i></a></td>';
		$return .= '</tr>';
		if ($share_list) {
			$return .= '<tr id="ilist-share-' . $row_tab->keyaccess . '" class="ilist-share-nodisplay">';
				$return .= '<td colspan="5">';
					$return .= ilist_get_share_icons( $ilist_search_url_complete . '?a=liste&k=' . $row_tab->keyaccess, $row_tab->keyaccess );
				$return .= '</td>';
			$return .= '</tr>';
		}
	}
	// Close TABLE
	$return .= '</tbody>';
	$return .= '</table>';
	
} elseif ($_POST) {
	$return .= __( "No list found", ILIST_ID_LANGUAGES );
}

return $return;