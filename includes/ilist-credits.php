<?php
/** Blocking direct access to plugin
=================================================== */
defined('ABSPATH') or die('Are you crazy!');

/** Create tab's CREDITS
=============================================== */
if (!function_exists("ilist_credits")) {
	function ilist_credits() {
		/** INFORMATUX Framework
		=================================================== */
		global $ilist_framework, $Ilist;
		
		/** Intialisation
		=============================================== */
		$ilist_page         = "credits";
        $informatux_url     = '<a href="https://informatux.com/" target="_blank">INFORMATUX</a>';
        $informatux_contact = '<a href="https://informatux.com/contact" target="_blank">Contact INFORMATUX</a>';
        $informatux_dev     = '<a href="https://dev.informatux.com/" target="_blank">DEV By INFORMATUX</a>';
		
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

		/** Content
        =================================================== */		
		echo $ilist_framework->openTable();
		// ----------------------------------------
		// Le plugin est public : plus de licence à renseigner, le support ne
		// demande donc plus d'informations de licence.
		$ilist_licence_infos = __( 'Support contact', ILIST_ID_LANGUAGES ) . ' <a href="https://dev.informatux.com/support" target="_blank">DEV By INFORMATUX</a>' . '<br><a href="https://github.com/informatux45/ilist" target="_blank">github.com/informatux45/ilist</a>';
		// ----------------------------------------
		$ilist_informatux_infos = '<strong>PLUG & BORNE</strong> : <a href="https://plugandborne.fun" target="_blank">Votre borne d\'arcade sur mesure</a>
			 <br><strong>SBUIADMIN</strong> : <a href="https://dev.informatux.com/cms-sbuiadmin" target="_blank">Le CMS informatux</a>
			 <br>Plugins WORDPRESS</strong> :
			 <br><strong>ICUSTOMIZER</strong> : <a href="https://dev.informatux.com/wordpress-icustomizer" target="_blank">Personnalisez Wordpress</a>
			 <br><strong>ILIST KADO</strong> : <a href="https://dev.informatux.com/woocommerce-ilist" target="_blank">"Fêtes" vos listes de cadeaux</a>
			 <br><strong>WCGATEWAYMP</strong> : <a href="https://dev.informatux.com/woocommerce-wcgatewaymp" target="_blank">Passerelle de paiement MangoPay</a>
			 <br><strong>NEXTCLOUD FILES</strong> : <a href="https://dev.informatux.com/wordpress-nextcloud" target="_blank">Vos fichiers Nextcloud sur Wordpress</a>
			 <br><strong>PIXEL STAT</strong> : <a href="https://dev.informatux.com/woocommerce-pixel-stat" target="_blank">Vos pixels Google et FB sur WooCommerce</a>
			 <br><strong>ICLOAK REDIRECTION</strong> : <a href="https://dev.informatux.com/wordpress-icloak" target="_blank">Gérer vos redirections</a>';
		// ----------------------------------------
		$_credits  = "";
		$_credits .= '<link href="' . FRAMEWORK_ILIST_URL . 'assets/css/framework-'.$ilist_page.'.css" rel="stylesheet">';
		$_credits .= '<div class="container-fluid">
						<div class="row">
							<div class="col-xl-12 wid-100">
								<div class="row" data-masonry=\'{"percentPosition": true }\'>
									
									<div class="col-lg-3 col-sm-6">
										<div class="card hovercard rounded shadow-sm h-100 mb-4">
											<div class="cardheader">
												
											</div>
											<div class="avatar">
												<img title="DEV By INFORMATUX" alt="DEV By INFORMATUX" src="' . ILIST_URL . 'images/avatar-informatux.gif">
											</div>
											<div class="info">
												<div class="desc"><strong style="font-size: 1.4em"><i> ' . ILIST_NAME . '</i></strong></div>
												<div class="desc">' . __( 'Current version', ILIST_ID_LANGUAGES ) . ' ' . ilist_get_version() . '</div>
												<div class="desc">' . __( 'Developed and maintained by', ILIST_ID_LANGUAGES ) . '</div>
												<div class="title">
													<a target="_blank" href="https://dev.informatux.com">DEV By INFORMATUX</a>
												</div>
											</div>
											<div class="bottom">
												<a class="btn btn-danger btn-sm" target="_blank" href="https://plus.google.com/109974847432830295737">
													<i class="fa-brands fa-google-plus-g"></i>
												</a>
												<a class="btn btn-primary btn-sm" target="_blank" href="https://www.facebook.com/devbyinformatux">
													<i class="fa-brands fa-facebook-f"></i>
												</a>
												<a class="btn btn-warning btn-sm" title="Contact INFORMATUX" target="_blank" href="https://informatux.com/contact">
													<i class="fa-solid fa-envelope"></i>
												</a>
												<a class="btn btn-success btn-sm" title="' . __( 'Find all our developments on ', ILIST_ID_LANGUAGES ) . 'DEV By INFORMATUX" target="_blank" href="https://dev.informatux.com">
													<i class="fa-solid fa-code"></i>
												</a>
												
											</div>
										</div>
									</div>
									
									<div class="col-lg-3 col-sm-6">
										<div class="card hovercard rounded shadow-sm h-100">
											<div class="cardheader">
												
											</div>
											<div class="avatar">
												<img title="ILIST Kado Support" alt="ILIST Kado Support" src="' . ILIST_URL . '/images/customer-ilist-support-white.png">
											</div>
											<div class="info">
												<div class="desc">' . $ilist_licence_infos . '</div>
											</div>
											<!--<div class="bottom"></div>-->
										</div>
									</div>
								
									<div class="col-lg-3 col-sm-6">
										<div class="card hovercard rounded shadow-sm h-100">
											<div class="cardheader">
												
											</div>
											<div class="avatar">
												<img title="INFORMATUX, ' . __( 'It is also', ILIST_ID_LANGUAGES ) . '" alt="INFORMATUX, ' . __( 'It is also', ILIST_ID_LANGUAGES ) . '" src="https://informatux.com/thumb.php?src=https://informatux.com/theme/informatux/assets/images/users/patrice2.jpg&size=100x">
											</div>
											<div class="info">
												<div class="desc text-start">' . $ilist_informatux_infos . '</div>
											</div>
											<!--<div class="bottom"></div>-->
										</div>
									</div>
						
								</div>
							</div>
							
							<div class="col-xl-12 wid-100 mb-3">
								&nbsp;
							</div>
						
						</div>
		
					  </div>';
		echo $ilist_framework->addAnything( $_credits );
        // ----------------------------------------
        echo $ilist_framework->closeTable();
    }
}
// ----------------------------------------
// ----------------------------------------
// ----------------------------------------
?>