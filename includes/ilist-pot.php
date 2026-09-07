<?php
/** Blocking direct access to plugin
=================================================== */
defined('ABSPATH') or die('Are you crazy!');

/** Create tab's POT
=============================================== */
if (!function_exists("ilist_pot")) {
	function ilist_pot() {
		
		/** INFORMATUX Framework
		=================================================== */
		global $ilist_framework, $Ilist;
        
		/** Intialisation
		=============================================== */
		$ilist_page = "pot";
		$url_help   = $Ilist->helpicon('configuration/cagnotte/');
		
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
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addSubMenu( [
			__( "Pot options (Valid only if the pot is activated)", ILIST_ID_LANGUAGES )
		] );
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		// --- Check if POT PRODUCT ID Exists
		if ((!ilist_get_option('ilist_id_participation_product') || ilist_get_option('ilist_id_participation_product') == '') && ilist_get_option('ilist_pot_is_active') == '1') {
			echo $ilist_framework->addAnything('<div class="ilist_help_message"><img src="' . ILIST_URL . 'images/ilist-warning-icon.png" style="vertical-align: middle;" alt="">&nbsp;<span>Pour pouvoir utiliser le mode cagnotte, vous devez indiquer l\'ID du produit participation</span></div>');
		}
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->addNote( __( 'Help', ILIST_ID_LANGUAGES ), $url_help . __( "Click on the icon to access the online help", ILIST_ID_LANGUAGES ) );		
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		if (ilist_get_option('ilist_pot_is_active') == '1') {
			$ilist_pot_is_active_description = '<br><span style="color: grey; font-style: italic; font-weight: normal; font-size: 1em;"><img src="' . ILIST_URL . 'images/ilist-warning-icon.png" style="vertical-align: middle; height: 1.5em;" alt="">&nbsp;' . __("When this option is activated and participatory purchases have been made, it is STRONGLY ADVISED not to deactivate this option. Increase the minimum price of the product, this will disable the option on your store.", ILIST_ID_LANGUAGES) . '</span>'; 
		} else {
			$ilist_pot_is_active_description = "";
		}
		echo $ilist_framework->addRadioYN(
			 __( 'Pot', ILIST_ID_LANGUAGES )
			,true
			,array(
				 'id'      => 'ilist_pot_is_active'
				,'name'    => 'ilist_pot_is_active'
				,'checked' => ilist_get_option('ilist_pot_is_active', '0') // Default: 0
			)
			,__( 'Enabled', ILIST_ID_LANGUAGES )
			,__( 'Disabled', ILIST_ID_LANGUAGES )
			,__( 'No - Default: disabled', ILIST_ID_LANGUAGES ) . $ilist_pot_is_active_description  // Description
		);
		// ----------------------------------------
		// --- Check if pot is active to create pot product
		// ----------------------------------------
		if (ilist_get_option('ilist_pot_is_active') == '1') {
			// ----------------------------------------
			$value_ilist_pot_min_price_product        = ilist_get_option('ilist_pot_min_price_product');
			$value_ilist_pot_range_price              = ilist_get_option('ilist_pot_range_price');
			$value_ilist_pot_id_participation_product = ilist_get_option('ilist_id_participation_product');
			// ----------------------------------------
			$is_ilist_pot_min_price_product = (isset($value_ilist_pot_min_price_product) && $value_ilist_pot_min_price_product > 0) ? true : false;
			$is_ilist_pot_range_price       = (isset($value_ilist_pot_range_price) && $value_ilist_pot_range_price > 0) ? true : false;
			$is_ilist_pot_options           = (true === $is_ilist_pot_min_price_product && true === $is_ilist_pot_range_price) ? true : false;
			// ----------------------------------------
			$is_ilist_pot_id_participation_product = (isset($value_ilist_pot_id_participation_product) && $value_ilist_pot_id_participation_product > 0) ? true : false;
			// ----------------------------------------
			$ilist_1_crowdfunding_options = "help-icon-check-mark.png";
			$ilist_2_crowdfunding_id      = "help-icon-check-mark.png";			
			// ----------------------------------------
			if (true === $is_ilist_pot_options && true === $is_ilist_pot_id_participation_product) {
				$ilist_crowdfunding_help_img  = "help-ilist-kado-crowdfunding-ok.png";
			} elseif (false === $is_ilist_pot_options && true === $is_ilist_pot_id_participation_product) {
				$ilist_crowdfunding_help_img  = "help-ilist-kado-crowdfunding-ko-options.png";
				$ilist_1_crowdfunding_options = "help-icon-cross-mark.png";
			} elseif (true === $is_ilist_pot_options && false === $is_ilist_pot_id_participation_product) {
				$ilist_crowdfunding_help_img  = "help-ilist-kado-crowdfunding-ko-idparticipationproduct.png";
				$ilist_2_crowdfunding_id      = "help-icon-cross-mark.png";
			} elseif (false === $is_ilist_pot_options && false === $is_ilist_pot_id_participation_product) {
				$ilist_crowdfunding_help_img  = "help-ilist-kado-crowdfunding-ko.png";
				$ilist_1_crowdfunding_options = "help-icon-cross-mark.png";
				$ilist_2_crowdfunding_id      = "help-icon-cross-mark.png";
			}
			echo $ilist_framework->addNote(
				__( 'Crowdfunding STEPS', ILIST_ID_LANGUAGES )
				,'<img src="' . ILIST_URL . 'images/help/' . $ilist_crowdfunding_help_img . '" alt="Help Crowdfunding" />' . '<br>' .
				 
				 '<div style="padding: 0.5em; border: 1px solid lightgrey; border-radius: 7px;">' .
				 
				 '<img src="' . ILIST_URL . 'images/help/' . $ilist_1_crowdfunding_options . '" style="vertical-align: middle; height: 1.5em;" alt="" />&nbsp;' .
				 __( "Step 1: you must indicate the minimum price of a product and the price range to be able to use the crowdfunding function and save the options", ILIST_ID_LANGUAGES ) . "<br>" .
				 "<div style='font-style: italic; padding: 0.8em 0; margin-bottom: 0.8em;'>" .
				 __( "The minimum price of a product: The products in the list must reach this price to be able to use the crowdfunding mode", ILIST_ID_LANGUAGES ) . "<br>" .
				 __( "Price range: These are the participation ranges that will be offered to buyers wishing to participate in the purchase of a product", ILIST_ID_LANGUAGES ) . "<br>" .
				 __( "Ex: Range to 10 - A buyer wants to participate in the product costing 150, he can add to his cart a participation of 50 for example among choices of 10, 20, 30, 40, 50, 60, 70... from 10 in 10 to the price of the product", ILIST_ID_LANGUAGES ) . "<br>" .
				 "</div>" .
				 
				 '<img src="' . ILIST_URL . 'images/help/' . $ilist_2_crowdfunding_id . '" style="vertical-align: middle; height: 1.5em;" alt="" />&nbsp;' .
				 __( "Step 2: you need to create a Participation product to be able to use the crowdfunding function", ILIST_ID_LANGUAGES ) . "<br>" .
				 "<div style='font-style: italic; padding: 0.8em 0;'>" .
				 __( "Use the Create button and choose the name of the Participation product (Ex: Participation or Contribution or other)", ILIST_ID_LANGUAGES ) . "<br>" .
				 __( "This Entry item must have the same price as your Price range.", ILIST_ID_LANGUAGES ) . "<br>" .
				 __( "Indicate only the ID (Identifier) of the product that will be created.", ILIST_ID_LANGUAGES ) .
				 "</div>" .
				 
				 "</div>"
			);
			// ----------------------------------------
			// ----------------------------------------
			// ----------------------------------------
			echo $ilist_framework->addInput(
				 'button'
				,__( "Create WooCommerce product", ILIST_ID_LANGUAGES )
				,array(
					 'name'        => 'ilist_modal'
					,'id'          => 'ilist_modal'
					,'value'       => __( 'Create', ILIST_ID_LANGUAGES )
					,'class'       => 'button button-primary'
					,'onclick'     => "return ilist_create_pot_product();"
				)
				,false
				,__( "You must create a WooCommerce product to be able to use the Fund mode.<br>You must first configure your price range.<br>This product will allow the addition of participation for the products of your customers lists. This is based on the price of your price range.<br>If you change your price range, you will need to create another WooCommerce product with the price of this new price range.", ILIST_ID_LANGUAGES ) // Description
			);
			// ----------------------------------------
			echo $ilist_framework->addNote( __( 'WooCommerce - Pot Product ID', ILIST_ID_LANGUAGES ), '<img src="' . ILIST_URL . 'images/help/help-pot-product.jpg" alt="WooCommerce product" style="max-width: 100%;" />' );
			// ----------------------------------------
			$_price_range   = ilist_get_option('ilist_pot_range_price');
			// ----------------------------------------
			// Check if Product Participation ID exists
			// ----------------------------------------
			if (!ilist_get_option('ilist_id_participation_product')) { // Not entered
				$_compare_price = '<span class="ilist-red ilist-italic">' . __( 'You did not enter the participation product ID', ILIST_ID_LANGUAGES) . '</span>';
			} else { // ID Present
				$product = wc_get_product( ilist_get_option('ilist_id_participation_product') );
				// Check if ID is WooCommerce product
				if ($product) {
					$_price_product = $product->get_price();
					$_compare_price = ($_price_range == $_price_product) ? '<span class="ilist-green ilist-italic">'.__( 'The price of the participation product corresponds to the price range you have defined', ILIST_ID_LANGUAGES).'</span>' : '<span class="ilist-red ilist-italic">'.sprintf( __( 'The price of the participation product (%s) does not correspond to the price range (%s) you have defined', ILIST_ID_LANGUAGES), sprintf( get_woocommerce_price_format(), get_woocommerce_currency_symbol(), $_price_product ), sprintf( get_woocommerce_price_format(), get_woocommerce_currency_symbol(), $_price_range ) ).'</span>';
				} else {
					$_compare_price = '<span class="ilist-red ilist-italic">' . __( 'Error product ID', ILIST_ID_LANGUAGES) . '</span>';
				}
			}
			// ----------------------------------------
			echo $ilist_framework->addInput(
				 'text'
				,__( "ID of participation product", ILIST_ID_LANGUAGES )
				,array(
					 'name'        => 'ilist_id_participation_product'
					,'id'          => 'ilist_id_participation_product'
					,'value'       => ilist_get_option('ilist_id_participation_product') // Default: void
					,'placeholder' => ""
					,'style'       => 'width: 100%; max-width: 100px;'
				)
				,false
				,__( 'Once your participation product has been created, enter the product ID here (see screenshot above)', ILIST_ID_LANGUAGES) . '<br>' . $_compare_price // Description
			);
			// ----------------------------------------
			// ----------------------------------------
			// ----------------------------------------
			echo $ilist_framework->addBreak( $url_help . __( "Pot options (Valid only if the pot is activated)", ILIST_ID_LANGUAGES ) );
			// ----------------------------------------
			echo $ilist_framework->addNumber(
				 __( "Minimum product price", ILIST_ID_LANGUAGES )
				,array(
					 'id'      => 'ilist_pot_min_price_product'
					,'name'    => 'ilist_pot_min_price_product'
					,'step'    => 1
					,'min'     => 1
					,'max'     => 2000
					,'value'   => ilist_get_option('ilist_pot_min_price_product', 50) // Default: 50
				)
				,true
				,__( "Minimum product price for the activation of the pot", ILIST_ID_LANGUAGES ) // Description
			);
			// ----------------------------------------
			echo $ilist_framework->addNumber(
				 __( "Range price", ILIST_ID_LANGUAGES )
				,array(
					 'id'      => 'ilist_pot_range_price'
					,'name'    => 'ilist_pot_range_price'
					,'step'    => 1
					,'min'     => 1
					,'max'     => 2000
					,'value'   => ilist_get_option('ilist_pot_range_price', 50) // Default: 50
				)
				,true
				,__( "Price range per product", ILIST_ID_LANGUAGES ) . '<br>' . __( "Current currency", ILIST_ID_LANGUAGES ) . ' : <strong>' . get_woocommerce_currency_symbol() . '</strong> - <a href="' . admin_url('admin.php?page=wc-settings') . '">' . __( "Currency options", ILIST_ID_LANGUAGES ) // Description
			);
			// ----------------------------------------
			echo $ilist_framework->addRadioYN(
				 __( 'Creator of list can choose', ILIST_ID_LANGUAGES ) . '<br><span style="color: orange; font-style: italic; font-weight: 100; font-size: 0.9em;"></span>'
				,false
				,array(
					 'id'       => 'ilist_pot_user_choice'
					,'name'     => 'ilist_pot_user_choice'
					,'checked'  => ilist_get_option('ilist_pot_user_choice', '0') // Default: 0
				)
				,__( 'Enabled', ILIST_ID_LANGUAGES )
				,__( 'Disabled', ILIST_ID_LANGUAGES )
				,__( 'No - Default: disabled', ILIST_ID_LANGUAGES ) . '<br>' . __( 'Enabled: The creator of a list can choose to add products to his list in prize pool mode or not', ILIST_ID_LANGUAGES ) . '<br>' . __( 'Disabled: All products added by creators to their list are still in prize pool mode', ILIST_ID_LANGUAGES ) // Description
			);
			// ----------------------------------------
			// ----------------------------------------
			// ----------------------------------------
		}
		// ----------------------------------------
		// --- Hiddens / Buttons
		// ----------------------------------------
		echo $ilist_framework->addInput( 'submit', '', array('value' => __( "Save changes", ILIST_ID_LANGUAGES)) );
		// ----------------------------------------
		// ----------------------------------------
		// ----------------------------------------
		echo $ilist_framework->closeTable();
		echo $ilist_framework->closeForm();
		// ----------------------------------------
		
		/** End
        =================================================== */
		echo $ilist_framework->general_infos('end');
		
		echo '<!-- The Modal -->
			<div id="modalIlist" class="ilist-modal">			
			  <!-- Modal content -->
			  <div class="ilist-modal-content">
				<span class="ilist-modal-close">&times;</span>
					
				<!--<form method="post" action="'.admin_url("admin.php?page=ilist-$ilist_page").'">-->
				
					<table class="form-table">
				
						<tr valign="middle">
						<th scope="row" colspan="2">
							<h1 id="tableau-general">' . __( 'Creating a WooCommerce product to use Fund Mode', ILIST_ID_LANGUAGES ) . '</h1>
							<h2 class="ilist-grey ilist-italic ilist-normal ilist-font-09">' . __( 'This product will not be visible in the catalog and will not be visible in search results', ILIST_ID_LANGUAGES ) . '</h2>
						</th>
						</tr>
						
						<tr valign="middle">
						<th scope="row">' . __( 'Product Name', ILIST_ID_LANGUAGES ) . '</th>
						<td>
							<input type="text" id="product_title" name="product_title" value="Participation">
						</td>
						</tr>
						
						<tr valign="middle">
						<th scope="row">' . __( 'Product Description', ILIST_ID_LANGUAGES ) . '</th>
						<td>
							<textarea id="product_desc" name="product_desc">' . __( 'Participation product', ILIST_ID_LANGUAGES ) . ' (' . sprintf( get_woocommerce_price_format(), get_woocommerce_currency_symbol(), ilist_get_option('ilist_pot_range_price', 50) ) . ')</textarea>
						</td>
						</tr>
						
						<tr valign="middle">
						<th scope="row">' . __( 'Participation product price', ILIST_ID_LANGUAGES ) . '</th>
						<td>' . sprintf( get_woocommerce_price_format(), get_woocommerce_currency_symbol(), ilist_get_option('ilist_pot_range_price', 50) ) . ' (' . __( "Range price", ILIST_ID_LANGUAGES ) . ')</td>
						</tr>
						
						<tr valign="middle">
						<th scope="row"></th>
						<td>
							<img id="ilist_create_product_loader" src="' . ILIST_URL . 'images/loader.gif" alt="">
							<input type="hidden" id="product_price" name="product_price" value="' . ilist_get_option('ilist_pot_range_price', 50) . '">
							<input type="submit" id="ilist_create_product" class="button button-primary" value="' . __( 'Create product', ILIST_ID_LANGUAGES ) . '">
						</td>
						</tr>
						
					</table>
				
				<!--</form>-->
					
			  </div>
			</div>
		
			<script>
				function ilist_create_pot_product() {
					var modal = document.getElementById("modalIlist");			
					var btn = document.getElementById("ilist_modal");
					var span = document.getElementsByClassName("ilist-modal-close")[0]; 
					btn.onclick = function() {
					  modal.style.display = "block";
					  return false;
					}
					span.onclick = function() {
					  modal.style.display = "none";
					  return false;
					}
					window.onclick = function(event) {
					  if (event.target == modal) {
						modal.style.display = "none";
					  }
					};
				}
			</script>';
        
    }
}

?>