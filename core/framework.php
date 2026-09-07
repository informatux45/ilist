<?php
/** *****************************************************************************
*               INFORMATUX Framework wordpress class (UTF8)                     *
/** *****************************************************************************
* @author     Patrice BOUTHIER                                                  *
* @copyright  1996-2023 INFORMATUX                                              *
* @link       informatux.com                                                    *
* @version    1.0.3                                                             *
* ----------------------------------------------------------------------------- *
* Copyright (c) 2023, INFORMATUX Solutions and web development                  *
* All rights reserved.                                                          *
***************************************************************************** **/

/** Blocking direct access to plugin
======================================================= */
defined('ABSPATH') or die('Are you crazy!');


if (!class_exists("ilist_framework")) {
	
	class ilist_framework extends Ilist {

		// Affectées dans le constructeur. Déclarées explicitement : depuis
		// PHP 8.2 la création d'une propriété non déclarée émet une
		// dépréciation, et deviendra une erreur en PHP 9.
		private $plugin_id;
		private $errors;

		/**
		* Class form's attributes
		* private properties
		* All the elements and attributes usables
		* Some values are entered by default
		*/
		private $eventArr = array ('onfocus' => ''
								  ,'onblur' => ''
								  ,'onselect' => ''
								  ,'onchange' => ''
								  ,'onclick' => ''
								  ,'ondblclick' => ''
								  ,'onmousedown' => ''
								  ,'onmouseup' => ''
								  ,'onmouseover' => ''
								  ,'onmousemove' => ''
								  ,'onmouseout' => ''
								  ,'onkeypress' => ''
								  ,'onkeydown' => ''
								  ,'onkeyup' => ''
								  );
		private $commonArr = array ('id' => ''
								   ,'class' => ''
								   ,'title' => ''
								   ,'style' => ''
								   ,'dir' => ''
								   ,'lang' => ''
								   ,'xml:lang' => ''
								   );
		private $formArr = array ('name' => 'formNew'
								 ,'id' => 'formNew'
								 ,'method' => 'post'
								 ,'action' => ''
								 ,'enctype' => 'application/x-www-form-urlencoded'
								 ,'accept' => ''
								 ,'onsubmit' => ''
								 ,'onclick' => ''
								 ,'onreset' => ''
								 ,'accept-charset' => 'unknown'
								 ,'style' => ''
								 ,'postid' => ''
								 ,'reloadpage' => ''
								 ,'submitpage' => ''
								 ,'reloadatas' => ''
								 ,'class' => ''
								 );
		private $inputArr = array ('text' => array ('value' => ''
												   ,'name' => ''
												   ,'alt' => ''
												   ,'tabindex' => ''
												   ,'accesskey' => ''
												   ,'readonly' => ''
												   ,'disabled' => ''
												   ,'width' => ''
												   ,'maxlength' => ''
												   ,'required' => ''
												   ,'size ' => ''
												   ,'valid' => ''
												   ,'class' => ''
												   ,'mask' => ''
												   ,'placeholder' => ''
												   ,'icon' => ''
												   ,'icon2' => ''
												   ,'medias' => ''
												   ,'dir' => ''
												   ,'subdir' => ''
												   ,'extension' => ''
												   ,'into' => ''
												   ,'limitfiles' => ''
												   ),
								   'number' => array ('name' => ''
													 ,'value' => ''
													 ,'alt' => ''
													 ,'tabindex' => ''
													 ,'accesskey' => ''
													 ,'disabled' => ''
													 ,'min'
													 ,'max'
													 ,'step'
													 ,'default'
													),
								   'button' => array ('name' => ''
													 ,'value' => ''
													 ,'alt' => ''
													 ,'class' => ''
													 ,'tabindex' => ''
													 ,'accesskey' => ''
													 ,'disabled' => ''
													 ),
								   'hidden' => array ('name' => ''
													 ,'value' => ''
													 ,'alt' => ''
													 ,'disabled' => ''
													 ,'size ' => ''
													 ),
								   'password' => array ('name' => ''
													   ,'value' => ''
													   ,'alt' => ''
													   ,'tabindex' => ''
													   ,'accesskey' => ''
													   ,'readonly' => ''
													   ,'disabled' => ''
													   ,'width' => ''
													   ,'maxlength' => ''
													   ,'size ' => ''
													   ,'valid' => ''
													   ,'mask' => ''
													   ),
								   'submit' => array ('name' => ''
													 ,'value' => 'Valider'
													 ,'alt' => ''
													 ,'tabindex' => ''
													 ,'accesskey' => ''
													 ,'disabled' => ''
													 ),
								   'checkbox' => array ('name' => ''
													   ,'value' => ''
													   ,'alt' => ''
													   ,'tabindex' => ''
													   ,'accesskey' => ''
													   ,'disabled' => ''
													   ,'checked' => ''
													   ,'valid' => ''
													   ,'class' => ''
													   ),
								   'radio' => array ('name' => ''
													,'value' => ''
													,'alt' => ''
													,'tabindex' => ''
													,'accesskey' => ''
													,'disabled' => ''
													,'checked' => ''
													,'title' => ''
													,'valid' => ''
													,'class' => ''
													),
								   'reset' => array ('name' => ''
													,'class' => ''
													,'value' => ''
													,'alt' => ''
													,'tabindex' => ''
													,'accesskey' => ''
													,'disabled' => ''
													,'title' => ''
													),
								   'file' => array ('name' => ''
												   ,'value' => ''
												   ,'alt' => ''
												   ,'tabindex' => ''
												   ,'accesskey' => ''
												   ,'disabled' => ''
												   ,'accept' => ''
												   ,'size ' => ''
												   ,'max_file_size' => ''
												   ,'mask' => ''
												   ),
								   'image' => array ('name' => ''
													,'value' => ''
													,'alt' => ''
													,'tabindex' => ''
													,'accesskey' => ''
													,'disabled' => ''
													,'src' => ''
													,'usemap' => ''
													,'ismap' => ''
													,'valid' => ''
													),
									'date' => array('name' => ''
												   ,'disabled' => ''
												   ,'form' => ''
												   ,'type' => ''
												   ,'autocomplete' => ''
												   ,'autofocus' => ''
												   ,'list' => ''
												   ,'min' => ''
												   ,'max' => ''
												   ,'step' => ''
												   ,'readonly' => ''
												   ,'value' => ''
												   ,'pattern' => ''
												   ),
									'time' => array('name' => ''
												   ,'disabled' => ''
												   ,'form' => ''
												   ,'type' => ''
												   ,'autocomplete' => ''
												   ,'autofocus' => ''
												   ,'list' => ''
												   ,'min' => ''
												   ,'max' => ''
												   ,'step' => ''
												   ,'readonly' => ''
												   ,'value' => ''
												   ,'pattern' => ''
												   ),
								);
		private $textareaArr = array ('rows' => ''
									 ,'cols' => ''
									 ,'disabled' => ''
									 ,'readonly' => ''
									 ,'accesskey' => ''
									 ,'tabindex' => ''
									 ,'name' => ''
									 ,'valid' => ''
									 ,'class' => ''
									 ,'mask' => ''
									 ,'language' => ''
									 ,'theme' => ''
									 ,'style' => ''
									 );
		private $codeArr = array ('id' => ''
								 ,'lang' => ''
								 ,'theme' => ''
								 ,'height' => ''
								 ,'fsize' => ''
								 );
		private $selectArr = array ('disabled' => ''
								   ,'multiple' => ''
								   ,'selected' => ''
								   ,'size' => ''
								   ,'name' => ''
								   ,'class' => ''
								   ,'mask' => ''
								   ,'alpha' => ''
								   ,'default' => ''
								   );
		private $optionArr = array ('disabled' => ''
								   ,'label' => ''
								   ,'selected' => ''
								   ,'value' => ''
								   ,'rel' => ''
								   );
		private $optgroupArr      = array ('label' => ''
										  ,'disabled' => ''
										  );
		private $formBuffer       = array ();
		private $formElementArr   = array ();
		private $formAttributeArr = array ();
		
		/**
		* Class form's operations
		* Constructor
		*/
		public function __construct($plugin_id) {
			/** Add admin scripts
			======================================================= */
			add_action( 'admin_enqueue_scripts', array( $this, 'wp_upload' ) );
			
			/** Initialize
			======================================================= */
			$this->plugin_id = strtolower($plugin_id);
			$this->errors    = array();
			
			/** If POST form (From framework informatux)
			======================================================= */
			if (isset($_POST['framework-ilist-form'])) {
				// Check plugin ID
				if ($this->sTrim($plugin_id) == '') {
					// Plugin ID error
					add_action( 'admin_notices', array( $this, 'plugin_id_error' ) );
				} else {
					// Loop POST
					foreach($_POST as $key => $val) {
						// Only plugin ID
						if (strpos(strtolower($key), strtolower($plugin_id)) !== false) {
							$update_option = ilist_update_option( $key, $val );
							if (!$update_option) $this->errors[] = $update_option;
						}
					}
					// Check errors
					if (array_keys( $this->errors, true )) {
						// Error
						add_action( 'admin_notices', array( $this, 'post_options_errors' ), 10, 1 );
					} else {
						// Success
						add_action( 'admin_notices', array( $this, 'post_options_success' ) );

					}
				}
			}
		}
		
		/**
		 * Add JS loader + Bootstrap Library
		 */
		public function wp_upload() {
			wp_enqueue_media();
			wp_enqueue_script(
				 'framework-ilist-' . $this->plugin_id . '-wp-media-uploader'
				,FRAMEWORK_ILIST_URL . 'assets/js/uploader/wp_media_uploader.js'
				,array( 'jquery' )
				,1.0
			);
			// Bootstrap CSS / JS
			wp_register_style( 'ilist-admin-bootstrap', ILIST_URL . 'vendor/bootstrap/css/bootstrap.min.css', false, '5.2.3' );
			wp_enqueue_style( 'ilist-admin-bootstrap' );
			wp_enqueue_script( 'ilist-admin-bootstrap', ILIST_URL . 'vendor/bootstrap/js/bootstrap.min.js', false, '5.2.3' );
		}
		
		
		/**
		 * Admin notice error
		 * PLUGIN ID
		 * @return html code
		 */
		public function plugin_id_error() {
			$plugin_id_error  = "";
			$plugin_id_error .= '<div class="notice notice-error is-dismissible framework-ilist-notice mb-3">';
			$plugin_id_error .= '<p>Plugin ID error (Cannot be empty)</p>';
			$plugin_id_error .= '</div>';
			echo $plugin_id_error;
		}
		
		
		/**
		 * Admin notice success
		 * POST Options success
		 * @return html code
		 */
		public function post_options_success() {
			$post_options  = "";
			$post_options .= '<div class="notice notice-success is-dismissible framework-ilist-notice mb-3">';
			$post_options .= '<p>Changes saved successfully</p>';
			$post_options .= '</div>';
			echo $post_options;
		}
		
		
		/**
		 * Admin notice error
		 * POST Options error
		 * @return html code
		 */
		public function post_options_errors() {
			$post_options  = "";
			$post_options .= '<div class="notice notice-error is-dismissible framework-ilist-notice mb-3">';
			$post_options .= '<p>';
			$post_options .= '<h1>ERROR</h1>';
			if (array_keys( $this->errors, true )) {
				foreach($this->errors as $error) {
					if ($this->sTrim($error) != '') $post_options .= $error . '<br>';
				}
			}
			$post_options .= '</p>';
			$post_options .= '</div>';
			echo $post_options;
		}
		
		
		/**
		 * General infos (Construct HTML Interface)
		 *
		 * @param  $switch  Name of html to return
		 * @param  $datas   Array of datas to insert
		 *
		 * @return html
		 */
		public function general_infos($switch = 'title', $datas = array()) {
			$html = "";
			switch(strtolower($switch)) {
				default:
				case "start":
					$html .= "<div class='wrap'>";
					$html .= '<h2><span id="' . $datas['PLUGIN_ID'] . '-span-title">' . $datas['PLUGIN_NAME'] . '</span>&nbsp;<span id="' . $datas['PLUGIN_ID'] . '-span-version">' .  $datas['PLUGIN_VERSION'] . '</span></h2>';
					return $html;
				break;
				case "end":
					$html .= "</div>";
					return $html;
				break;
			}
		}
		
		/**
		 * Nav Tabs
		 *
		 * @param  $navs  Array of nav tabs
		 * @param  $tab_active  ID of active tab
		 * @param  $id  LANG ID
		 *
		 * @return html
		 */
		public function nav_tabs($tab_active = '', $id = '') {
			$_navs    = (!function_exists("ilist_nav_tabs")) ? false : ilist_nav_tabs();
			$navtabs  = '';
			$navtabs .= '<link href="' . ILIST_URL . 'vendor/fontawesome/' . ILIST_AWESOME_VERSION . '/css/fontawesome.min.css" rel="stylesheet">';
			$navtabs .= '<link href="' . ILIST_URL . 'vendor/fontawesome/' . ILIST_AWESOME_VERSION . '/css/brands.css" rel="stylesheet">';
			$navtabs .= '<link href="' . ILIST_URL . 'vendor/fontawesome/' . ILIST_AWESOME_VERSION . '/css/solid.css" rel="stylesheet">';
			$navtabs .= '<link rel="stylesheet" href="' . FRAMEWORK_ILIST_URL . 'assets/css/framework-navbar.css">';
			$navtabs .= '<nav class="navbar navbar-icon-top navbar-expand-lg navbar-dark bg-dark ps-4">
							<!--<a class="navbar-brand pe-4 mt-n1" href="#">
								<img style="margin-top: -7px; max-height: 50px; height: 100%" src="'.ILIST_URL.'images/icon-ilist-white.png" alt="ILIST Kado" title="ILIST Kado">
							</a>-->
							<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
								<span class="navbar-toggler-icon"></span>
							</button>

							<div class="collapse navbar-collapse pt-1" id="navbarSupportedContent">
								<ul class="navbar-nav">';
			if ($_navs) {
				$navtabs_icons = [
					 'ilist-general'     => 'fa fa-home'
					,'ilist-options'     => 'fa fa-wrench'
					,'ilist-list'        => 'fa-solid fa-gear'
					,'ilist-woocommerce' => 'fa-solid fa-store'
					,'ilist-pot'         => 'fa-solid fa-hand-holding-dollar'
					,'ilist-share'       => 'fa fa-share-alt'
					,'ilist-search'      => 'fa fa-search'
					,'ilist-emails'      => 'fa-solid fa-envelope-circle-check'
					,'ilist-changelog'   => 'fa-solid fa-bug'
					,'ilist-credits'     => 'fa-solid fa-hand-holding-heart'
					,'ilist_list'        => 'fa fa-list-alt'
					,'ilist_types'       => 'fa-solid fa-tags'
					,'ilist_sold'        => 'fa-solid fa-money-bill-wave'
				];
				// ------------------------------------------------------
				// SAMPLE WITH BADGE
				// badge-info, badge-danger, badge-warning, badge-success
				// ------------------------------------------------------
				// <li class="nav-item">
				//	  <a class="nav-link" href="#">
				//		<i class="fa fa-globe">
				//		  <span class="badge badge-success">11</span>
				//		</i>
				//		Test
				//	  </a>
				// </li>
				// ------------------------------------------------------
				foreach($_navs as $key => $val) {
					$_active = ($tab_active == $key) ? ' active' : '';
					$navtabs .= '<li class="nav-item'.$_active.'">
									<a class="nav-link" href="' . admin_url('admin.php?page=' . $key) . '">
										<i class="' . $navtabs_icons[$key] . '"></i> ' . $val . '
									</a>
								</li>';
					if ($key == 'ilist-emails') {
						$navtabs .= '<li class="nav-item">
										<a class="nav-link" href="' . admin_url('admin.php?page=ilist_list') . '">
											<i class="' . $navtabs_icons['ilist_list'] . '"></i> ' . __( 'The lists', ILIST_ID_LANGUAGES )  . '
										</a>
									 </li>
									 <li class="nav-item">
										<a class="nav-link" href="' . admin_url('admin.php?page=ilist_sold') . '">
											<i class="' . $navtabs_icons['ilist_sold'] . '"></i> ' . __( 'The sales', ILIST_ID_LANGUAGES )  . '
										</a>
									 </li>
									 <li class="nav-item">
										<a class="nav-link" href="' . admin_url('admin.php?page=ilist_types') . '">
											<i class="' . $navtabs_icons['ilist_types'] . '"></i> ' . __( 'The status', ILIST_ID_LANGUAGES )  . '
										</a>
									 </li>';
					}
				}
			}
			$navtabs .= '</ul></div></nav>';
			
			// ------------------------------------------------------
			// --- OLD NAVBAR (TABS)
			// ------------------------------------------------------
			//$navtabs .= '<nav class="nav-tab-wrapper framework-ilist-nav-tab-wrapper">';
			//if ($_navs) {
			//	foreach($_navs as $key => $val) {
			//		if ($tab_active == $key)
			//			$navtabs .= '<a href="' . admin_url('admin.php?page=' . $key) . '" class="nav-tab nav-tab-active">' . $val . '</a>';
			//		else
			//			$navtabs .= '<a href="' . admin_url('admin.php?page=' . $key) . '" class="nav-tab">' . $val . '</a>';
			//	}
			//}
			//$navtabs .= '</nav>';
			// ------------------------------------------------------
			
			return $navtabs;
		}
		
		/**
		 * Open HTML Table
		 *
		 * @return HTML
		 */
		public function openTable() {
			$table  = "";
			$table .= '<table class="form-table framework-ilist-table">';
			$table .= '<tbody>';
			
			return $table;
		}	
	
		/**
		 * Close HTML Table
		 *
		 * @return HTML
		 */
		public function closeTable() {
			$table  = "";
			$table .= '</tbody>';
			$table .= '</table>';
			
			return $table;
		}
		
		/**
		 * Generate Submenu
		 *
		 * @return HTML
		 */
		public function addSubMenu( $rows ) {
			$submenu = "";
			if (array_keys( $rows, true )) {
				$count_subentry = count($rows)-1;
				$submenu .= "<div id='framework-ilist-submenu'>";
				foreach($rows as $cpt => $row) {
					$submenu .= '<a href="#' . $this->parse_to_anchor($row) . '">' . $row . '</a>';
					if ($count_subentry > $cpt) $submenu .= '&nbsp;|&nbsp;';
				}
				$submenu .= "</div>";
			}
			return $submenu;
		}
		
		/**
		* Construct form (header)
		* @return html code
		*/
		public function openForm($arrArgs = array ()) {
			foreach ($this->formArr as $clef => $val) {
				if (array_key_exists ($clef, $arrArgs)) {
					$this->formAttributeArr[$clef] = $arrArgs[$clef];
				} else if (!empty ($val)) {
					$this->formAttributeArr[$clef] = $val;
				}
			}
	
			$chaineTemp = "";
			// Form initialisation
			$chaineTemp .= '<script src="' . FRAMEWORK_ILIST_URL . 'assets/js/colorpicker/dist/spectrum.min.js"></script>';
			$chaineTemp .= '<link rel="stylesheet" href="' . FRAMEWORK_ILIST_URL . 'assets/js/colorpicker/dist/spectrum.min.css">';
			$chaineTemp .= '<script src="' . FRAMEWORK_ILIST_URL . 'assets/vendor/ace/ace.js" type="text/javascript" charset="utf-8"></script>';
			$chaineTemp .= '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">';
			$chaineTemp .= '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>';
			$chaineTemp .= '<form ';
			
			foreach ($this->formAttributeArr as $clef => $val) {
				if ($clef == 'id')
					$chaineTemp .= 'id="' . $val . '" ';
				if ($clef == 'name')
					$chaineTemp .= 'name="' . $val . '" ';
				if ($clef != 'reloadpage' && $clef != 'submitpage')
					$chaineTemp .= $clef.'="'.$val.'" ';
			}
			$chaineTemp .= '>';
		
			return $chaineTemp;
		}
		
		// fermer le formulaire
		public function closeForm() {
			$chaineTemp  = "";
			$chaineTemp .= $this->addInput( 'hidden', '', array('value' => 'form', 'name' => 'framework-ilist-form') );
			$chaineTemp .= '</form>';
			
			return $chaineTemp;
		}
	
	
		/**
		* Construct form (body)
		* add a form element (input)
		* @return html code
		*/
		public function addInput($elem, $label = '', $arrArgs = array (), $isRequired = false, $helpDsc = '') {
			if (!array_key_exists ($elem, $this->inputArr)) {
				throw new Exception ($elem . ' n\'est pas un élément valide');
			}
			
			$chaineTemp = "";
			
			if (!array_key_exists ('name', $arrArgs) && $elem !== 'submit' && $elem !== 'reset') {
				$arrArgs['name'] = 'default'.time();
			}
			
			$cpt = count ($this->formElementArr);
			$this->formElementArr[$cpt][$elem] = array ();
			$arrTemp = array_merge ($this->eventArr, $this->commonArr, $this->inputArr[$elem]);
	
			foreach ($arrTemp as $clef => $val) {
				if (array_key_exists ($clef, $arrArgs)) {
					if ($clef == 'id')
						$labelFor = $arrArgs[$clef];
					
					$this->formElementArr[$cpt][$elem][$clef] = $arrArgs[$clef];
				}
			}
			
			// Show the label for the element
			// If $elem = hidden, so do not create a table row
			$input_password = false;
			if ($elem == 'password') $input_password = true;
			if ($elem == 'hidden') {
				$chaineTemp .= '<input type="'.$elem.'" ';
	
				foreach ($this->formElementArr[$cpt][$elem] as $clef => $val) {
					$chaineTemp .= $clef.'="'.$val.'" ';
				}
	
				$chaineTemp .= '/>';
			} elseif ($elem == 'submit' || $elem == 'reset') {
	
				$chaineTemp .= '<tr valign="top">
									<th scope="row" class="framework-ilist-th" colspan="2">';
			
				// Show the form element
				$chaineTemp .= '&nbsp;&nbsp;<input type="'.$elem.'" ';
	
				foreach ($this->formElementArr[$cpt][$elem] as $clef => $val) {
					if ($clef == 'class')
						$chaineClass = $val;
					$chaineTemp .= $clef.'="'.$val.'" ';
				}
	
				if (!isset($chaineClass)) {
					$submitClass = ($elem == 'submit') ? ' button-submit' : '';
					$chaineTemp .= 'class="framework-ilist-button button button-primary' . $submitClass . '" ';
				}
	
				$chaineTemp .= '/>&nbsp;&nbsp;';
				$chaineTemp .= '</th>';
					
			} else {
				
			$chaineTemp .= '<tr valign="top">
								<th scope="row" class="framework-ilist-th">
									' . $this->isRequired ($isRequired, $label, $labelFor) . '
								</th>';
	
				// Show label (required fields)
				$chaineTemp .= '<td class="">';
	
				$chaineTemp .= '<input class="form-control" type="'.$elem.'" ';
	
				foreach ($this -> formElementArr[$cpt][$elem] as $clef => $val) {
					if ($clef == 'max_file_size')
						$fileSize = $val;
					if ($clef == 'value')
						$photo = $val;
					if ($clef == 'id')
						$id = $val;
					
					$chaineTemp .= $clef.'="'.$val.'" ';
				}
	
				$chaineRequired = ($isRequired == true) ? 'required="true" ' : '';
				$chaineTemp    .= $chaineRequired;
				
				$chaineTemp .= '/>';
				
				if ($input_password) {
					$chaineTemp .= ' <img style="cursor: pointer;" src="' . NEXTCLOUD_FRAMEWORK_ILIST_URL . 'assets/img/eye24.png" onmouseover="'.$id.'_mouseoverPass(\''.$id.'\');" onmouseout="'.$id.'_mouseoutPass(\''.$id.'\');" />';
					$chaineTemp .= "<script>
						function ".$id."_mouseoverPass(id) {
							var obj = document.getElementById(id);
							obj.type = \"text\";
						}
						function ".$id."_mouseoutPass(id) {
							var obj = document.getElementById(id);
							obj.type = \"password\";
						}
					</script>";
				}
				
				// If help
				if ($helpDsc != '')
					$chaineTemp .= '<p class="description">' . $helpDsc . '</p>';
	
				$chaineTemp .= '</td></tr>';
					
			}
	
			return $chaineTemp;
		}
		
		
		/**
		* Construct form (body)
		* add a form element (input radio)
		* @return html code
		*/
		public function addRadio($label = '', $tabRadio = array(), $arrArgs = array (), $isRequired = false, $separator = '&nbsp;', $helpDsc = '') {
			$chaineTemp = "";
			$elem       = 'radio';
			if (!array_key_exists ($elem, $this->inputArr)) {
				throw new Exception ($elem . ' n\'est pas un élément valide');
			}
	
			if (!array_key_exists ('name', $arrArgs) && $elem !== 'submit' && $elem !== 'reset') {
				$arrArgs['name'] = 'default';
			}
	
			$cpt = count ($this->formElementArr);
			$this->formElementArr[$cpt][$elem] = array();
			$arrTemp = array_merge ($this->eventArr, $this->commonArr, $this->inputArr[$elem]);
	
			foreach ($arrTemp as $clef => $val) {
				if (array_key_exists ($clef, $arrArgs)) {
					$this->formElementArr[$cpt][$elem][$clef] = $arrArgs[$clef];
				}
			}
	
			// Show the label for the element
			$chaineTemp .= '<tr valign="top">
								<th scope="row" class="framework-ilist-th">
									' . $this->isRequired ($isRequired, $label) . '
								</th>';
	
			$chaineTemp .= '<td class="">';
			
			$chaineTemp .= '<fieldset>';
	
			// Show the form element
			for ($i = 0; $i < count($tabRadio); $i++) {
				$chaineTemp .= '<label for="'.$arrArgs['name'].$tabRadio[$i]['value'].'">';
				$chaineTemp .= '<input type="'.$elem.'" ';
	
				$chaineTemp .= 'value="'.$tabRadio[$i]['value'].'" ';
	
				foreach ($this->formElementArr[$cpt][$elem] as $clef => $val) {
					if ($clef == 'checked')
						$chaineValue = $val;
					elseif ($clef == 'id')
						$chaineTemp .= 'id="'.$arrArgs['name'].$tabRadio[$i]['value'].'" ';
					else
						$chaineTemp .= $clef.'="'.$val.'" ';
				}
	
				if ( ( isset($arrArgs['checked']) && $tabRadio[$i]['value'] == $arrArgs['checked'] ) || ( isset($arrArgs['default']) && $tabRadio[$i]['value'] == $arrArgs['default'] ) )
					$chaineTemp .= 'checked="" ';
	
				if ($isRequired == true && $i == count($tabRadio)-1)
					$chaineTemp .= ' required="true" ';
	
				$chaineTemp .= '/>&nbsp;&nbsp;';
				$chaineTemp .= $tabRadio[$i]['text'].'&nbsp;';
				$chaineTemp .= '</label>';
				$chaineTemp .= $separator;
			}
			
			$chaineTemp .= '</fieldset>';
			
			// If help
			if ($helpDsc != '') $chaineTemp .= '<p class="description">' . $helpDsc . '</p>';
				
			$chaineTemp .= '</td></tr>';
	
			return $chaineTemp;
		}
	
	
		/**
		* Construct form (body)
		* add a form element (input radio)
		* @return html code
		*/
		public function addRadioYN($label = '', $isRequired = false, $arrArgs = array (), $yes = 'OUI', $no = 'NON', $helpDsc = '', $separator = '&nbsp;&nbsp;&nbsp;') {
			$chaineTemp = "";
			$elem       = 'radio';
			if (!array_key_exists ($elem, $this->inputArr)) {
				throw new Exception ($elem . ' n\'est pas un élément valide');
			}
	
			if (!array_key_exists ('name', $arrArgs) && $elem !== 'submit' && $elem !== 'reset') {
				$arrArgs['name'] = 'default';
			}
	
			$cpt = count ($this->formElementArr);
			$this->formElementArr[$cpt][$elem] = array ();
			$arrTemp = array_merge ($this->eventArr, $this->commonArr, $this->inputArr[$elem]);
	
			foreach ($arrTemp as $clef => $val) {
				if (array_key_exists ($clef, $arrArgs)) {
					$this->formElementArr[$cpt][$elem][$clef] = $arrArgs[$clef];
				}
			}
	
			// Show the label for the element
			$chaineTemp .= '<tr valign="top">
						<th scope="row" class="framework-ilist-th">
							' . $this->isRequired ($isRequired, $label) . '
						</th>';
	
			$chaineTemp .= '<td class="">';
			
			// Show the form element radio YES
			// if checked => value="1"
			$chaineTemp .= '<input type="'.$elem.'" ';
	
			foreach ($this -> formElementArr[$cpt][$elem] as $clef => $val) {
				if ($clef === 'checked' && $val == 1) {
					$chaineTemp .= 'checked="checked" ';
					
				} elseif ($clef !== 'checked') {
					$chaineTemp .= $clef.'="'.$val.'" ';
				}
				$chaineTemp .= 'value="1" ';
			}
	
			$chaineRequired = ($isRequired == true) ? ' required="true" ' : '';
			$chaineTemp    .= $chaineRequired;
	
			$chaineTemp .= '/>&nbsp;'.$yes;
	
			// Show the form element radio NO
			// if checked => value="0"
			$chaineTemp .= $separator;
			$chaineTemp .= '<input type="'.$elem.'" ';
	
			foreach ($this -> formElementArr[$cpt][$elem] as $clef => $val) {
				if ($clef === 'checked' && $val === '0')
					$chaineTemp .= 'checked="checked" ';
				elseif ($clef !== 'checked')
					$chaineTemp .= $clef.'="'.$val.'" ';
			}
			$chaineTemp .= 'value="0" ';
	
			$chaineTemp .= '/>&nbsp;'.$no;
			
			// If help
			if ($helpDsc != '') $chaineTemp .= '<p class="description">' . $helpDsc . '</p>';
	
			$chaineTemp .= '</td></tr>';
				
			return $chaineTemp;
		}
	
		
		/**
		* Construct form (body)
		* add a form element (checkbox)
		* @return html code
		*/
		public function addCheckbox($label = '', $tabCheck = array(), $arrArgs = array (), $isRequired = false, $separator = '&nbsp;', $helpDsc = '') {
			$chaineTemp = "";
			$rand = rand();
			$elem = 'checkbox';
			if (!array_key_exists ($elem, $this->inputArr)) {
				throw new Exception ($elem . ' n\'est pas un élément valide');
			}
	
			$cpt = count ($this->formElementArr);
			$this->formElementArr[$cpt][$elem] = array ();
			$arrTemp = array_merge ($this->eventArr, $this->commonArr);
	
			// Show the label for the element
			$chaineTemp .= '<tr valign="top">
						<th scope="row" class="framework-ilist-th">
							' . $this->isRequired ($isRequired, $label) . '
						</th>';
	
			$chaineTemp .= '<td class="">';
			
			$chaineTemp .= '<fieldset>';
	
			// Show the tab form elements
			for ($i = 0; $i < count($tabCheck); $i++) {
				$chaineTemp .= '<label for="'.$tabCheck[$i]['inputid'].'-'.$rand.'">';
				$chaineTemp .= '<input type="'.$elem.'" ';
				$chaineTemp .= 'name="'.$tabCheck[$i]['name'].'" ';
	
				foreach ($this -> formElementArr[$cpt][$elem] as $clef => $val) {
					$chaineTemp .= $clef.'="'.$val.'" ';
				}
				
				if (isset($tabCheck[$i]['value']))
					$chaineTemp .= 'value="' .$tabCheck[$i]['value'].'" ';
					
				if (isset($tabCheck[$i]['inputid']))
					$chaineTemp .= 'id="' .$tabCheck[$i]['inputid'].'-'.$rand.'" ';
	
				if ($i == count($tabCheck)-1 && $isRequired == true)
					$chaineTemp .= 'required="true" ';
	
				if (isset($tabCheck[$i]['checked']) && $tabCheck[$i]['checked'] == '1')
					$chaineTemp .= 'checked="checked" ';
				
				if (isset($tabCheck[$i]['disabled']) && $tabCheck[$i]['disabled'] == 'disabled')
					$chaineTemp .= 'disabled="" ';
				
				$chaineTemp .= '/>&nbsp;&nbsp;';
				$chaineTemp .= $tabCheck[$i]['text'].'&nbsp;';
				$chaineTemp .= '</label>';
				$chaineTemp .= $separator;
			}
	
			$chaineTemp .= '<fieldset>';
			
			// If help
			if ($helpDsc != '') $chaineTemp .= '<p class="description">' . $helpDsc . '</p>';
	
			$chaineTemp .= '</td></tr>';
			
			return $chaineTemp;
		}
		
		
		/**
		* Construct form (body)
		* add a form element (textarea)
		* @return html code
		*/
		public function addTextarea($label = '', $txt = '', $arrArgs = array (), $isRequired = false, $helpDsc = '') {
			$chaineTemp = "";
			$cpt = count ($this->formElementArr);
			$this -> formElementArr[$cpt]['textarea']['innerHTML'] = $txt;
			$arrTemp = array_merge ($this->eventArr, $this->commonArr, $this->textareaArr);
	
			foreach ($arrTemp as $clef => $val) {
				if (array_key_exists ($clef, $arrArgs)) {
					$this->formElementArr[$cpt]['textarea'][$clef] = $arrArgs[$clef];
				}
			}
	
			// Show the label for the element
			$chaineTemp .= '<tr valign="top">
						<th scope="row" class="framework-ilist-th">
							' . $this->isRequired ($isRequired, $label) . '
						</th>';
	
			$chaineTemp .= '<td class="">';
	
			// Show the form element
			$chaineTemp    .= '<textarea ';
	
			$chaineClasses = "";
			foreach ($this -> formElementArr[$cpt]['textarea'] as $clef => $val) {
				if ($clef !== 'innerHTML') {
					if ($clef == 'class')
						$chaineClasses = $val;
					else 
						$chaineTemp .= $clef.'="'.$val.'" ';
				}
			}
			
			// Classes
			$chaineTemp .= ($chaineClasses != '') ? ' class="form-control ' . $chaineClasses . '"' : ' class="form-control"';
	
			$chaineTemp .= ($isRequired == true) ? ' required="true"' : '';
	
			$chaineTemp .= '>'.$txt.'</textarea>';
	
			$chaineTemp .= '</div>';
			
			// If help
			if ($helpDsc != '') $chaineTemp .= '<p class="description">' . $helpDsc . '</p>';
	
			$chaineTemp .= '</td></tr>';
	
			return $chaineTemp;
		}
		
		
		/**
		* Construct form (body)
		* add a form element (code with ACE JS)
		* @return html code
		*/
		public function addCode($label = '', $code = '', $arrArgs = array (), $isRequired = false, $helpDsc = '') {
			$chaineTemp = "";
			$rand = rand();
			$cpt = count ($this->formElementArr);
			$this->formElementArr[$cpt]['code']['innerHTML'] = $code;
			$arrTemp = array_merge ($this->eventArr, $this->commonArr, $this->codeArr);
			//
			foreach ($arrTemp as $clef => $val) {
				if (array_key_exists ($clef, $arrArgs)) {
					$this->formElementArr[$cpt]['code'][$clef] = $arrArgs[$clef];
				}
			}
	
			// Show the label for the element
			$chaineTemp .= '<tr valign="top">
						<th scope="row" class="framework-ilist-th">
							' . $this->isRequired ($isRequired, $label) . '
						</th>';
	
			$chaineTemp .= '<td class="">';
	
			// Show div editor
			$chaineTemp .= '<div ';
	
			// css, html, javascript, json, less, lua, markdown, mysql, php, plain_text, python, ruby, sass, scss, sh, text, xml
			$code_id = $code_language = $code_theme = $code_font_size = $code_height = "";
			foreach ($this -> formElementArr[$cpt]['code'] as $clef => $val) {
				if ($clef !== 'innerHTML') {
					if ($clef == 'id') $code_id = $val;
					elseif ($clef == 'lang') $code_language = $val;
					elseif ($clef == 'theme') $code_theme = $val;
					elseif ($clef == 'fsize') $code_font_size = $val;
					elseif ($clef == 'height') $code_height = $val;
					else $chaineTemp .= $clef.'="'.$val.'" ';
				}
			}
	
			$chaineTemp .= ($isRequired == true) ? ' required="true"' : '';
	
			$chaineTemp .= ' id="div_' . $code_id . '" style="height: ' . $code_height . 'px;">' . $this->displayText($code) . '</div>';
			$chaineTemp .= '<input type="hidden" id="' . $code_id . '" name="' . $code_id . '" value="' . $code . '">';
							
			$chaineTemp .= '<script>
								var editor = ace.edit("div_' . $code_id . '");
								editor.setTheme("ace/theme/' . $code_theme . '");
								editor.setFontSize("' . $code_font_size . 'px");
								editor.getSession().setMode("ace/mode/' . $code_language . '");
								var editor_content = jQuery("#' . $code_id . '");
								var editor_code    = jQuery("#div_' . $code_id . '");
								editor_code.keyup(function() {
									var code = editor.getSession().getValue();
									editor_content.val(code);
								});
							</script>';
			
			// If help
			if ($helpDsc != '') $chaineTemp .= '<p class="description">' . $helpDsc . '</p>';
	
			$chaineTemp .= '</td></tr>';
	
			return $chaineTemp;
		}
		
		
		/**
		* Construct form (body)
		* add a form element (textarea)
		* @return html code
		*/
		public function addEditor($label = '', $arrArgs = array (), $isRequired = false, $helpDsc = '') {
			$chaineTemp = "";
	
			// Show the label for the element
			$chaineTemp .= '<tr valign="top">
						<th scope="row" class="framework-ilist-th">
							' . $this->isRequired ($isRequired, $label) . '
						</th>';
	
			$chaineTemp .= '<td class="framework-ilist-editor">';
	
			// Initialize Editor Options
			$editor_options = [
				 'media_buttons' => (isset($arrArgs['media_buttons'])) ? $arrArgs['media_buttons'] : false  // Whether to show the Add Media/other media buttons
				,'wpautop'       => (isset($arrArgs['wpautop'])) ? $arrArgs['wpautop'] : true               // Whether to use wpautop()
				,'textarea_rows' => (isset($arrArgs['textarea_rows'])) ? $arrArgs['textarea_rows'] : 20     // Number rows in the editor textarea
				,'teeny'         => (isset($arrArgs['teeny'])) ? $arrArgs['teeny'] : false                  // Whether to output the minimal editor config
			];
	
			// Show the form element
			// - editor
			// - getwpeditor
			$editor_choice = 'editor';
			switch($editor_choice) {
				case "getwpeditor":
					$chaineTemp .= $this->getWpEditor( ilist_get_option( $arrArgs['name'] ), $arrArgs['id'], $editor_options );
				break;
				case "editor":
					ob_start();
					wp_editor( ilist_get_option( $arrArgs['name'] ), $arrArgs['id'], $editor_options );
					$chaineTemp .= ob_get_clean();
				break;
			}
					
			// If help
			if ($helpDsc != '') $chaineTemp .= '<p class="description">' . $helpDsc . '</p>';
	
			$chaineTemp .= '</td></tr>';
	
			return $chaineTemp;
		}
		
		
		/**
		 * Construct form (body)
		 * Other method to add WP Editor
		 * @return html code
		 */
		public function getWpEditor( $content = '', $editor_id = '', $options = array() ) {
			ob_start();
		 
			wp_editor( $content, $editor_id, $options );
		 
			$temp  = ob_get_clean();
			$temp .= _WP_Editors::enqueue_scripts();
			$temp .= print_footer_scripts();
			$temp .= _WP_Editors::editor_js();
		 
			return $temp;
		}
		
		
		/**
		* Construct form (body)
		* add a form element (select)
		* @return html code
		*/
		public function openSelect($label = '', $arrArgs = array (), $isRequired = false) {
			$chaineTemp = "";
			$cpt = count ($this->formElementArr);
			$this->formElementArr[$cpt]['select'] = array ();
			$arrTemp = array_merge ($this->eventArr, $this->commonArr, $this->selectArr);
	
			foreach ($arrTemp as $clef => $val) {
				if (array_key_exists ($clef, $arrArgs)) {
					$this->formElementArr[$cpt]['select'][$clef] = $arrArgs[$clef];
				}
			}
	
			// Show the label for the element
			$chaineTemp .= '<tr valign="top">
						<th scope="row" class="framework-ilist-th">
							' . $this->isRequired ($isRequired, $label) . '
						</th>';
	
			$chaineTemp .= '<td class="">';
	
			// Show the form element
			$chaineTemp .= '<select ';
	
			$chaineClasses = "";
			foreach ($this -> formElementArr[$cpt]['select'] as $clef => $val) {
				if ($clef == 'class')
					$chaineClasses = $val;
				else 
					$chaineTemp .= $clef.'="'.$val.'" ';
			}
			
			// Classes
			$chaineTemp .= ($chaineClasses != '') ? ' class="form-control ' . $chaineClasses . '"' : ' class="form-control"';
	
			$chaineTemp .= ($isRequired == true) ? ' required="true"' : '';
	
			$chaineTemp .= '>';
			
			return $chaineTemp;
		}
	
	
		/**
		* Construct form (body)
		* add a form element (close select)
		* @return html code
		*/
		public function closeSelect($helpDsc = '') {
			$cpt = count ($this->formElementArr);
			$this->formElementArr[$cpt]['/select'] = array ();
			$chaineTemp = '</select>';
			
			// If help
			if ($helpDsc != '') $chaineTemp .= '<p class="description">' . $helpDsc . '</p>';
	
			$chaineTemp .= '</td></tr>';
	
			return $chaineTemp;
		}
	
	
		/**
		* Construct form (body)
		* add a option to a form element (select)
		* @return html code
		*/
		public function addOption($txt, $arrArgs = array ()) {
			$chaineTemp = "";
			$cpt = count ($this->formElementArr);
			$this->formElementArr[$cpt]['option']['innerHTML'] = $txt;
			$arrTemp = array_merge ($this->eventArr, $this->commonArr, $this->optionArr);
	
			foreach ($arrTemp as $clef => $val) {
				if (array_key_exists ($clef, $arrArgs)) {
					$this->formElementArr[$cpt]['option'][$clef] = $arrArgs[$clef];
				}
			}
	
			$chaineTemp .= '<option ';
	
			foreach ($this->formElementArr[$cpt]['option'] as $clef => $val) {
				if ($clef !== 'innerHTML') {
					$chaineTemp .= $clef.'="'.$val.'" ';
				}
			}
	
			$chaineTemp .= '>'.$txt.'</option>';
	
			return $chaineTemp;
		}
		
		
		/**
		 * Construct form (body)
		 * add a form element (upload image)
		 * @return html code
		 */
		public function addUpload($label = '', $arrArgs = array (), $isRequired = false, $helpDsc = '') {
			$chaineTemp = "";
			
			$idTmp = rand();
			
			// Url de l'image si renseignee (preview)
			$previewValue = (isset($arrArgs['value']) && trim($arrArgs['value']) != '') ? $arrArgs['value'] : '';
			$previewValueBefore = (!$previewValue) ? '//' : '';
			$previewSize  = (isset($arrArgs['previewSize']) && trim($arrArgs['previewSize']) != '') ? $arrArgs['previewSize'] : '150px';
			
			// Text buttons
			$uploadButtonTxt = (isset($arrArgs['uploadButtonTxt']) && trim($arrArgs['uploadButtonTxt']) != '') ? trim($arrArgs['uploadButtonTxt']) : 'Upload image';
			$removeButtonTxt = (isset($arrArgs['removeButtonTxt']) && trim($arrArgs['removeButtonTxt']) != '') ? trim($arrArgs['removeButtonTxt']) : 'Remove image';

			// Show the label for the element
			$chaineTemp .= '<tr valign="top">
						<th scope="row" class="framework-ilist-th">
							' . $this->isRequired ($isRequired, $label) . '
						</th>';
	
			$chaineTemp .= '<td class="">';
			
			$chaineTemp .= '<div class="form-group smartcat-uploader-'.$idTmp.'">
								<input type="hidden" id="'.$arrArgs['id'].'" name="'.$arrArgs['name'].'" value="'.$arrArgs['value'].'">
							</div>
							<script>
								jQuery( document ).ready(function() {
									jQuery.wpMediaUploader({
										target : ".smartcat-uploader-'.$idTmp.'", // The class wrapping the textbox
										inputId : "#'.$arrArgs['id'].'", // Input ID
										uploaderTitle : "Select or upload image", // The title of the media upload popup
										uploaderButton : "Set image", // the text of the button in the media upload popup
										multiple : false, // Allow the user to select multiple images
										buttonText : "'.$uploadButtonTxt.'", // The text of the upload button
										buttonClass : ".smartcat-upload-'.$idTmp.'", // the class of the upload button
										removeButton : "#smartcat-remove-'.$idTmp.'", // ID remove class
										removeText : "'.$removeButtonTxt.'", // Remove Text Button
										'.$previewValueBefore.'previewValue : "'.$previewValue.'", // Image preview Value
										previewImg : "#smartcat-image-'.$idTmp.'", // ID Preview image
										previewSize : "'.$previewSize.'", // The preview image size (Width / Height)
										modal : true, // is the upload button within a bootstrap modal ?
										buttonStyle : { // style the button
											color : "#fff",
											background : "#2ea2cc",
											fontSize : "inherit",                
											padding : "7px 15px",
											textDecoration: "none"
										},
									});
								});
							</script>';
			
			// If help
			if ($helpDsc != '') $chaineTemp .= '<p class="description">' . $helpDsc . '</p>';
				
			$chaineTemp .= '</td></tr>';
	
			return $chaineTemp;
		}
		
		
		/**
		* Construct form (body)
		* add a form element (country select)
		* COL0: id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
		* COL1: code int(3) NOT NULL,
		* COL2: alpha2 varchar(2) NOT NULL,
		* COL3: alpha3 varchar(3) NOT NULL,
		* COL4: nom_fr_fr varchar(45) NOT NULL,
		* COL5: nom_en_gb varchar(45) NOT NULL,
		* @return html code
		*/
		public function addCountry($label = '', $arrArgs = array (), $isRequired = false, $helpDsc = '') {
			$chaineTemp  = "";
			$chaineTemp .= $this->openSelect ($label, $arrArgs, $isRequired, $helpDsc);
	
			// Select option
			$default     = (isset($arrArgs['default']) && $arrArgs['default'] != '') ? $arrArgs['default'] : 'Choose country';
			$chaineTemp .= $this->addOption ($default, array('value'=>''));
			
			// Extract all the countries (options html)
			$all_countries = FRAMEWORK_ILIST_PATH . 'assets/xml/sql-pays.csv';
			if (file_exists($all_countries)) {
				$row = 1;
				if (($handle = fopen("$all_countries", "r")) !== false) {
					while (($data = fgetcsv($handle, 1000, ",")) !== false) {
						$code    = $data[1];
						$alpha2  = $data[2];
						$alpha3  = $data[3];
						$name_fr = $data[4];
						$name_en = $data[5];
						// --------------------------------
						$alpha = ($arrArgs['alpha'] == 2) ? $alpha2 : $alpha3;
						$name  = (get_bloginfo('language') == 'fr-FR') ? $name_fr : $name_en;
						// --------------------------------
						if (strtoupper($arrArgs['value']) === $alpha3 || strtoupper($arrArgs['value']) === $alpha2) {
							$chaineTemp .= $this->addOption($name, array('value' => $alpha, 'selected' => ''));
						} else {
							$chaineTemp .= $this->addOption($name, array('value' => $alpha));
						}
					}
					fclose($handle);
				}
			} else {
				$chaineTemp .= $this->addOption('No country available', array('value' => 'nocountry'));
			}
	
			$chaineTemp .= $this->closeSelect();
			return $chaineTemp;
		}
	
	
		/**
		* Construct form (body)
		* add a form slider with number input)
		* @return html code
		*/
		public function addNumber($label = '', $arrArgs = array (), $isRequired = false, $helpDsc = '') {
			$chaineTemp = "";
			$elem       = 'number';
			if (!array_key_exists ($elem, $this->inputArr)) {
				throw new Exception ($elem . ' n\'est pas un élément valide');
			}
	
			if (!array_key_exists ('name', $arrArgs) && $elem !== 'submit' && $elem !== 'reset') {
				$arrArgs['name'] = 'default';
			}
	
			$cpt = count ($this->formElementArr);
			$this->formElementArr[$cpt][$elem] = array ();
			$arrTemp = array_merge ($this->eventArr, $this->commonArr, $this->inputArr[$elem]);
	
			foreach ($arrTemp as $clef => $val) {
				if (array_key_exists ($clef, $arrArgs)) {
					$this->formElementArr[$cpt][$elem][$clef] = $arrArgs[$clef];
				}
			}
	
			// Show the label for the element
			$chaineTemp .= '<tr valign="top">
								<th scope="row" class="framework-ilist-th">
									' . $this -> isRequired ($isRequired, $label) . '
								</th>';
	
			$chaineTemp .= '<td class="">';	
			
			// Show the form element
			$chaineTemp .= '<div class="framework-ilist-slidecontainer">';
			$chaineTemp .= '<input type="range" min="'.$arrArgs['min'].'" max="'.$arrArgs['max'].'" step="'.$arrArgs['step'].'" value="'.$arrArgs['value'].'" class="framework-ilist-slider" ';
			
			$idtmp = rand();
	
			foreach ($this->formElementArr[$cpt][$elem] as $clef => $val) {
				if ($clef == 'id')
					$id = $val;
				if ($clef != 'value') $chaineTemp .= $clef.'="'.$val.'" ';
			}
			
			if (!isset($id)) {
				$validName   = 'Number' . rand();
				$chaineTemp .= 'id = "' . $validName . '" ';
			} else {
				$validName = $id;
			}
	
			$chaineRequired = ($isRequired == true) ? ' required="true" ' : '';
			$chaineTemp    .= $chaineRequired;
	
			$chaineTemp .= '>';
			
			$chaineTemp .= '<input type="number" min="'.$arrArgs['min'].'" max="'.$arrArgs['max'].'" step="'.$arrArgs['step'].'" id="rangenumber_'.$idtmp.'" name="rangenumber_'.$idtmp.'" value="'.$arrArgs['value'].'" class="framework-ilist-slider-number">';
			
			$chaineTemp .= '</div>';
	
			$chaineTemp .= '<script>
								var slider_'.$idtmp.' = document.getElementById("'.$validName.'");
								var rangen_'.$idtmp.' = document.getElementById("rangenumber_'.$idtmp.'");
								rangen_'.$idtmp.'.value = slider_'.$idtmp.'.value;
								
								slider_'.$idtmp.'.oninput = function() {
									rangen_'.$idtmp.'.value = this.value;
								}
								rangen_'.$idtmp.'.onchange = function() {
									slider_'.$idtmp.'.value = this.value;
								}
							</script>';
			
			// If help
			if ($helpDsc != '') $chaineTemp .= '<p class="description">' . $helpDsc . '</p>';
				
			$chaineTemp .= '</td></tr>';
	
			return $chaineTemp;
		}
	
		
		/**
		* Construct form (body)
		* add a form element (input)
		* @return html code
		*/
		public function addColor($label = '', $arrArgs = array (), $isRequired = false, $helpDsc = '') {
			$chaineTemp = '';
			$elem = 'text';
			if (!array_key_exists($elem, $this->inputArr)) {
				throw new Exception($elem . ' n\'est pas un élément valide');
			}
	
			if (!array_key_exists('name', $arrArgs) && $elem !== 'submit' && $elem !== 'reset') {
				$arrArgs['name'] = 'default';
			}
	
			$cpt = count ($this->formElementArr);
			$this->formElementArr[$cpt][$elem] = array ();
			$arrTemp = array_merge ($this->eventArr, $this->commonArr, $this->inputArr[$elem]);
	
			foreach ($arrTemp as $clef => $val) {
				if (array_key_exists ($clef, $arrArgs)) {
					$this->formElementArr[$cpt][$elem][$clef] = $arrArgs[$clef];
				}
			}
	
			// Show the label for the element
			$chaineTemp .= '<tr valign="top">
								<th scope="row" class="framework-ilist-th">
									' . $this->isRequired ($isRequired, $label) . '
								</th>';
	
			$chaineTemp .= '<td class="">';	
	
			$idTmp = rand();
	
			// Show the form element
			$chaineTemp .= '<input type="'.$elem.'" ';
	
			foreach ($this -> formElementArr[$cpt][$elem] as $clef => $val) {
				if ($clef == 'id')
					$nameColorP = $val;
				$chaineTemp .= $clef.'="'.$val.'" ';
			}
			
			if (!isset($nameColorP)) {
				$validNameColorP = 'Color' . $idTmp;
				$chaineTemp     .= 'id = "' . $validNameColorP . '" ';
			} else {
				$validNameColorP = $nameColorP;
			}
	
			$chaineRequired = ($isRequired == true) ? ' required="true" ' : '';
			$chaineTemp    .= $chaineRequired;
			//$chaineTemp .= ($isRequired == true) ? 'class="required"' : '';
	
			$chaineTemp .= '/>';
			
			$chaineTemp .= '<script>
								jQuery( document ).ready(function() {
									jQuery("#' . $validNameColorP . '").spectrum({
										color: "' . $arrArgs['value'] . '"
									});
								});
							</script>';
	
			$chaineTemp .= '</div>';
			
			// If help
			if ($helpDsc != '') $chaineTemp .= '<p class="description">' . $helpDsc . '</p>';
	
			$chaineTemp .= '</td></tr>';
	
			return $chaineTemp;
		}
		
		
		/**
		* Construct form (body)
		* add a note
		* @return html code (string)
		*/
		public function addNote($title = '', $description = '', $valign = 'top') {
			$chaineTemp  = "";
			$chaineTemp .= '<tr valign="' . $valign . '">
								<th scope="row" class="framework-ilist-th">
									' . $title . '
								</th>
								<td class="">
									' . $description . '
								</td>
							</tr>';
							
			return $chaineTemp;
		}
		
	
		/**
		* Construct form (body)
		* add a break to the form (line with title)
		* @return html code (string)
		*/
		public function addBreak($title = '', $valign = 'top') {
			$chaineTemp  = "";
			$chaineTemp .= '<tr valign="' . $valign . '">
								<th scope="row" colspan="2" class="framework-ilist-th-break bg-dark">
									<span id="' . $this->parse_to_anchor( $title ) . '">' . $title . '</span>
								</th>
							</tr>';
							
			return $chaineTemp;
		}
	
	
		/**
		* Construct form (body)
		* add a * for form fields required
		* @return html code (string)
		*/
		public function isRequired($fieldRequired, $label, $labelFor = '', $colorRequired = 'red') {
			$chaineTemp = "";
			
			if ($labelFor != '') $chaineTemp .= '<label for="' . $labelFor . '">';
	
			if ($fieldRequired === true) {
				$chaineTemp .= $label . '&nbsp;<span style="color: ' . $colorRequired . '">*</span>';
			} else {
				$chaineTemp .= $label;
			}
			
			if ($labelFor != '') $chaineTemp .= '</label>';
			$chaineTemp .= '<br>';
			
			return $chaineTemp;
		}
		
		
		/**
		* Construct form (body)
		* show the entire form
		* @return html code
		*/
		public function __toString() {
			$chaineTemp = $this->formBuffer['open'];
			
			foreach ($this->formBuffer['elements'] as $clef => $val) {
				if (isset ($this->formBuffer['anything'][$clef])) {
					$chaineTemp .= $this->formBuffer['anything'][$clef];
				}
				$chaineTemp .= $val;
			}
			$chaineTemp .= $this->formBuffer['close'];
			
			return $chaineTemp;
		}
		
		/**
		* Construct form (method)
		* free up resources and create a new form
		* form any previously created and not displayed will be lost
		* @return empty array
		*/
		public function freeForm() {
			$this->formBuffer       = array ();
			$this->formElementArr   = array ();
			$this->formAttributeArr = array ();
		}
	
	
		/**
		* Construct form (method)
		* Destructor
		* @return bool false
		*/
		public function __destruct() {
			//unset($this);
		}
		
		// ouvrir un fieldset
		public function openFieldset($label, $arrArgs = array (), $isRequired = false) {
			$chaineTemp = "";
			$cpt = count ($this->formElementArr);
			$this->formElementArr[$cpt]['fieldset'] = array ();
			$arrTemp = array_merge($this->eventArr, $this->commonArr, $this->fieldsetArr);
			foreach ($arrTemp as $clef => $val) {
			  if (array_key_exists ($clef, $arrArgs)) {
				$this->formElementArr[$cpt]['fieldset'][$clef] = $arrArgs[$clef];
			  }
			}
			// Show the label for the element
			$chaineTemp .= '<tr valign="top">
								<th scope="row" class="framework-ilist-th">
									' . $this -> isRequired ($isRequired, $label) . '
								</th>';
	
			$chaineTemp .= '<td class="">';	
			$chaineTemp .= '<fieldset ';
			foreach ($this->formElementArr[$cpt]['fieldset'] as $clef => $val) {
			  $chaineTemp .= $clef.'="'.$val.'" ';
			}
			$chaineTemp .= '>';
			
			return $chaineTemp;
		}
		
		// fermer un fieldset
		public function closeFieldset($helpDsc = '') {
			$chaineTemp = "";
			$cpt = count ($this -> formElementArr);
			$this->formElementArr[$cpt]['/fieldset'] = array ();
			$chaineTemp .= '</fieldset>';
			
			// If help
			if ($helpDsc != '') $chaineTemp .= '<p class="description">' . $helpDsc . '</p>';
	
			$chaineTemp .= '</td></tr>';
	
			return $chaineTemp;
		}
	
			
		/**
		* Construct form (body)
		* add a option to a form element (optgroup)
		* @return html code
		*/
		public function openOptgroup($label, $arrArgs = array (), $isRequired = false) {
			$chaineTemp = "";
			$cpt = count ($this->formElementArr);
			$this->formElementArr[$cpt]['optgroup']['label'] = $label;
			$arrTemp = array_merge ($this->eventArr, $this->commonArr, $this->optgroupArr);
			foreach ($arrTemp as $clef => $val) {
				if (array_key_exists ($clef, $arrArgs)) {
					$this->formElementArr[$cpt]['select'][$clef] = $arrArgs[$clef];
				}
			}
			
			// Show the label for the element
			$chaineTemp .= '<tr valign="top">
								<th scope="row" class="framework-ilist-th">
									' . $this -> isRequired ($isRequired, $label) . '
								</th>';
			
			$chaineTemp = '<optgroup ';
			foreach ($this->formElementArr[$cpt]['optgroup'] as $clef => $val) {
				$chaineTemp .= $clef.'="'.$val.'" ';
			}
			$chaineTemp .= '>';
			
			return $chaineTemp;
		}
		
		/**
		* Construct form (body)
		* add a option to a form element (close optgroup)
		* @return html code
		*/
		public function closeOptgroup($helpDsc = '') {
			$chaineTemp = "";
			$cpt = count ($this->formElementArr);
			$this->formElementArr[$cpt]['/optgroup'] = array ();
			$chaineTemp .= '</optgroup>';
			
			// If help
			if ($helpDsc != '') $chaineTemp .= '<p class="description">' . $helpDsc . '</p>';
	
			$chaineTemp .= '</td></tr>';
		  
			return $chaineTemp;
		}
		
		/**
		* Construct form (body)
		* add anything you want
		* @return html code
		*/
		public function addAnything($any) {
			$chaineTemp  = "";
			$chaineTemp .= '<tr valign="top">
								<th scope="row" colspan="2" class="framework-ilist-th-anything">
									' . $any . '
								</th>
							</tr>';
							
			return $chaineTemp;
		}
		
		/** ============================================
		 * ==============================================
		 * ================== SANITIZE ==================
		 * ==============================================
		 * ============================================*/
		
		/**
		* Make links in the text clickable
		* @param  string  $text
		* @return string
		**/
		function makeClickable($text) {
			$patterns = array("/(^|[^]_a-z0-9-=\"'\/])([a-z]+?):\/\/([^, \r\n\"\(\)'<>]+)/i", "/(^|[^]_a-z0-9-=\"'\/])www\.([a-z0-9\-]+)\.([^, \r\n\"\(\)'<>]+)/i", "/(^|[^]_a-z0-9-=\"'\/])ftp\.([a-z0-9\-]+)\.([^, \r\n\"\(\)'<>]+)/i", "/(^|[^]_a-z0-9-=\"'\/:\.])([a-z0-9\-_\.]+?)@([^, \r\n\"\(\)'<>\[\]]+)/i");
			$replacements = array("\\1<a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a>", "\\1<a href=\"http://www.\\2.\\3\" target=\"_blank\">www.\\2.\\3</a>", "\\1<a href=\"ftp://ftp.\\2.\\3\" target=\"_blank\">ftp.\\2.\\3</a>", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>");
			return preg_replace($patterns, $replacements, $text);
		}
	
	
		/**
		* Convert linebreaks to <br /> tags
		* @param  string  $text
		* @return string
		*/
		function nl2Br($text) {
			return preg_replace("/(\015\012)|(\015)|(\012)/","<br />",$text);
		}
	
	
		/**
		* Convert <br /> to linebreaks tags
		* @param  string  $text
		* @return string
		*/
		function undoNl2Br($text) {
			return preg_replace("/(\015\012)|(\015)|(\012)/","\n",$text);
		}
	
	
		/**
		* Add slashes to the text if magic_quotes_gpc is turned off.
		* @param  string  $text
		* @return string
		**/
		function addSlashes($text) {
			// get_magic_quotes_gpc() est supprimée depuis PHP 8.0. Le garde
			// était inversé : quand la fonction n'existe PAS, le premier
			// membre vaut true et PHP évaluait le second, appelant donc une
			// fonction inexistante (erreur fatale). Les magic quotes ayant
			// disparu en PHP 5.4, il faut toujours échapper.
			return addslashes($text);
		}
	
	
		/**
		* if magic_quotes_gpc is on, strip back slashes
		* @param  string  $text
		* @return string
		*/
		function stripSlashesGPC($text) {
			// Les magic quotes ont disparu en PHP 5.4 et get_magic_quotes_gpc()
			// est supprimée depuis PHP 8.0. Le plugin exige PHP 7.4+ : cette
			// méthode était déjà un simple passe-plat (elle retournait $text
			// dès la première ligne), les branches restantes étaient mortes.
			return $text;
		}
		
	
		/**
		* Stripslashes on array
		* @param  array  $value
		* @return array
		*/
		function stripslashes_deep($value) {
			$value = is_array($value) ?
						array_map('stripslashes_deep', $value) :
						$this->stripSlashesGPC($value);
		
			return $value;
		}
	
	
		/**
		* for displaying data without html tags
		* @param  string  $text
		* @return string
		*/
		function stripTags($text) {
			return strip_tags($text);
		}
	
	
		/**
		* Convert all applicable characters to HTML entities
		* @param  strings $text, $encode
		* @return string
		*
		*				Supported charsets
		* ------------------------------------------------------------------------------
		* Charset  	Aliases  			Description
		* ------------------------------------------------------------------------------
		* ISO-8859-1  	ISO8859-1  			Western European, Latin-1
		* ISO-8859-15 	ISO8859-15 			Western European, Latin-9. Adds the Euro sign, French and Finnish letters missing in Latin-1(ISO-8859-1).
		* UTF-8 					ASCII compatible multi-byte 8-bit Unicode.
		* cp866 	ibm866, 866 			DOS-specific Cyrillic charset. This charset is supported in 4.3.2.
		* cp1251 	Windows-1251, win-1251, 1251 	Windows-specific Cyrillic charset. This charset is supported in 4.3.2.
		* cp1252 	Windows-1252, 1252 		Windows specific charset for Western European.
		* KOI8-R 	koi8-ru, koi8r			Russian. This charset is supported in 4.3.2.
		* BIG5		950				Traditional Chinese, mainly used in Taiwan.
		* GB2312 	936				Simplified Chinese, national standard character set.
		* BIG5-HKSCS					Big5 with Hong Kong extensions, Traditional Chinese.
		* Shift_JIS 	SJIS, 932			Japanese
		* EUC-JP 	EUCJP				Japanese
		* ------------------------------------------------------------------------------
		*/
		function htmlEntities($text, $encode = 'UTF-8') {
			return htmlentities($text, ENT_QUOTES, $encode);
		}
	
	
		/**
		* Convert all HTML entities to their applicable characters
		* @param  strings $text, $encode
		* @return string
		*
		*				Supported charsets
		* ------------------------------------------------------------------------------
		* Charset  	Aliases  			Description
		* ------------------------------------------------------------------------------
		* ISO-8859-1  	ISO8859-1  			Western European, Latin-1
		* ISO-8859-15 	ISO8859-15 			Western European, Latin-9. Adds the Euro sign, French and Finnish letters missing in Latin-1(ISO-8859-1).
		* UTF-8 					ASCII compatible multi-byte 8-bit Unicode.
		* cp866 	ibm866, 866 			DOS-specific Cyrillic charset. This charset is supported in 4.3.2.
		* cp1251 	Windows-1251, win-1251, 1251 	Windows-specific Cyrillic charset. This charset is supported in 4.3.2.
		* cp1252 	Windows-1252, 1252 		Windows specific charset for Western European.
		* KOI8-R 	koi8-ru, koi8r			Russian. This charset is supported in 4.3.2.
		* BIG5		950				Traditional Chinese, mainly used in Taiwan.
		* GB2312 	936				Simplified Chinese, national standard character set.
		* BIG5-HKSCS					Big5 with Hong Kong extensions, Traditional Chinese.
		* Shift_JIS 	SJIS, 932			Japanese
		* EUC-JP 	EUCJP				Japanese
		* ------------------------------------------------------------------------------
		*/
		function htmlEntitiesDecode($text, $encode = 'UTF-8') {
			return html_entity_decode($text, ENT_QUOTES, $encode);
		}
	
	
		/**
		* Convert special characters to HTML entities
		* @param   string  $text           string being converted
		* @param   int     $quote_style
		* @param   string  $charset        character set used in conversion
		* @param   bool    $double_encode
		* @return    string
		*/
		function htmlSpecialChars($text, $charset = "UTF-8", $double_encode = true) {
			if ( version_compare( phpversion(), "5.2.3", ">=" ) ) {
				$text = htmlspecialchars( $text, ENT_QUOTES, $charset, $double_encode );
			} else {
				$text = htmlspecialchars( $text, ENT_QUOTES);
			}
			return preg_replace(array("/&amp;/i", "/&nbsp;/i"),
						array('&', '&amp;nbsp;'),
						$text);
		}
	
	
		/**
		* Reverses {@link htmlSpecialChars()}
		* @param  string  $text
		* @return string
		**/
		function undoHtmlSpecialChars($text) {
			return preg_replace(array("/&gt;/i", "/&lt;/i", "/&quot;/i", "/&#039;/i", '/&amp;nbsp;/i'), array(">", "<", "\"", "'", "&nbsp;"), $text);
		}
	
		
		/**
		* Strip whitespace (or other characters) from the beginning and end of a string
		* @param  string  $text
		* @return string
		*
		*------------------------------------------------------------------------------
		*                 Character Mask
		* ------------------------------------------------------------------------------
		* " " (ASCII 32 (0x20)), an ordinary space.
		* "\t" (ASCII 9 (0x09)), a tab.
		* "\n" (ASCII 10 (0x0A)), a new line (line feed).
		* "\r" (ASCII 13 (0x0D)), a carriage return.
		* "\0" (ASCII 0 (0x00)), the NUL-byte.
		* "\x0B" (ASCII 11 (0x0B)), a vertical tab.
		*------------------------------------------------------------------------------
		**/
		function sTrim($text, $character_mask = '') {
			if ($character_mask != '')
				return trim($text, $character_mask);
			else
				return trim($text);
		}
	
		/**
		* Validate a website address
		* @param  string  $url
		* @return string url website
		*/
		function validateWebsite($url) {
			if ($url === 'http://') {
				return '';
			} else if (!preg_match('#^[a-z0-9]+://#i', $url) && strlen($url) > 0) {
				return 'http://' . $url;
			}
			return $url;
		}
		
		/**
		* Search for unavailable caracters and replace them
		* @param  string  $string
		* @return string string
		*/	
		function rewriteString($string, $lowupp = false) {
			$noValidString = $this->sTrim($this->displayText($string));
			$noValidString = preg_replace('`\s+`', '-', $this->sTrim($noValidString));
			$noValidString = str_replace("'", "-", $noValidString);
			$noValidString = str_replace('"', '-', $noValidString);
			$noValidString = preg_replace('`_+`', '-', $this->sTrim($noValidString));
			$caracters_in  = array(' ', '?', '!', '.', ',', ':', "'", '&', '(', ')', '-', '/', '%', '=', '[', ']');
			$caracters_out = array('-', '', '', '', '', '-', '-', '-', '', '', '-', '-', '-', '', '', '');
			$noValidString = str_replace($caracters_in, $caracters_out, $noValidString);
			$noValidString = str_replace("------", "-", $noValidString);
			$noValidString = str_replace("-----", "-", $noValidString);
			$noValidString = str_replace("----", "-", $noValidString);
			$noValidString = str_replace("---", "-", $noValidString);
			$noValidString = str_replace("--", "-", $noValidString);
			$accents       = array('À','Á','Â','Ã','Ä','Å','à','á','â','ã','ä','å','Ò','Ó','Ô','Õ','Ö','Ø','ò','ó','ô','õ','ö','ø','È','É','Ê','Ë','è','é','ê','ë','Ç','ç','Ì','Í','Î','Ï','ì','í','î','ï','Ù','Ú','Û','Ü','ù','ú','û','ü','ÿ','Ñ','ñ');
			$ssaccents     = array('A','A','A','A','A','A','a','a','a','a','a','a','O','O','O','O','O','O','o','o','o','o','o','o','E','E','E','E','e','e','e','e','C','c','I','I','I','I','i','i','i','i','U','U','U','U','u','u','u','u','y','N','n');
			$validString   = str_replace($accents, $ssaccents, $noValidString);
		
			return (!$lowupp) ? $validString : strtolower($validString);
		}
	
	
		/**
		* A quick solution for filtering XSS scripts
		* @TODO: To be improved
		*/
		function filterXss($text) {
			$patterns = array();
			$replacements = array();
	
			$text           = str_replace( "\x00", "", $text );
			$c              = "[\x01-\x1f]*";
			$patterns[]     = "/\bj{$c}a{$c}v{$c}a{$c}s{$c}c{$c}r{$c}i{$c}p{$c}t{$c}[\s]*:/si";
			$replacements[] = "javascript;";
			$patterns[]     = "/\ba{$c}b{$c}o{$c}u{$c}t{$c}[\s]*:/si";
			$replacements[] = "about;";
			$patterns[]     = "/\bx{$c}s{$c}s{$c}[\s]*:/si";
			$replacements[] = "xss;";
	
			$text = preg_replace($patterns, $replacements, $text);
	
			return $text;
		}
	
	
		/**
		* Searches text for unwanted tags and removes them
		* @param string $text String to purify
		* @return string $text The purified text
		*/
		function stopXSS($text) {
			if (!is_array($text)) {
				$text = preg_replace("/\(\)/si", "", $text);
				$text = strip_tags($text);
				$text = str_replace(array("\"",">","<","\\"), "", $text);
			} else {
				foreach($text as $k=>$t) {
					if (is_array($t)) {
						StopXSS($t);
					} else {
						$t = preg_replace("/\(\)/si", "", $t);
						$t = strip_tags($t);
						$t = str_replace(array("\"",">","<","\\"), "", $t);
						$text[$k] = $t;
					}
				}
			}
			return $text;
		}
	
	
		/**
		* Filters textarea form data in DB for display
		* @param   string  $text
		* @param   bool    $html   allow html?
		* @param   bool    $smiley allow smileys?
		* @param   bool    $br     convert linebreaks?
		* @param   bool    $xss    allow filtering XSS scripts?
		* @return  string
		**/
		function displayText($text, $encode = 'UTF-8', $entities = 0, $decode_entities = 1, $html = 0, $br = 0, $clickable = 0, $xss = 1) {
	
			// Trim text
			$text = $this->sTrim($text);
		
			if ($entities != 0) {
				// Convert all applicable characters to HTML entities
				$text = $this->htmlEntities($text, $encode);
			}
	
			if ($decode_entities != 0) {
				// Convert all HTML entities to their applicable characters
				$text = $this->htmlEntitiesDecode($text, $encode);
			}
	
			if ($html != 0) {
				// html not allowed
				$text = $this->htmlSpecialChars($text, $encode);
			}
	
			if ($br != 0) {
				$text = $this->nl2Br($text);
			}
	
			if ($clickable != 0) {
				$text = $this->makeClickable($text);
			}
	
			if ($xss != 0) {
				$text = $this->filterXss($text);
			}
	
			$text = $this->stripSlashesGPC($text);
	
			return $text;
		}
		
		
		/**
		* Filters textarea form data in DB for display
		* @param   string  $text
		* @param   string  $lang   string to search
		* @return  string
		*
		*				Supported charsets
		* ------------------------------------------------------------------------------
		* Charset  	Aliases  			Description
		* ------------------------------------------------------------------------------
		* ISO-8859-1  	ISO8859-1  			Western European, Latin-1
		* ISO-8859-15 	ISO8859-15 			Western European, Latin-9. Adds the Euro sign, French and Finnish letters missing in Latin-1(ISO-8859-1).
		* UTF-8 					ASCII compatible multi-byte 8-bit Unicode.
		* cp866 	ibm866, 866 			DOS-specific Cyrillic charset. This charset is supported in 4.3.2.
		* cp1251 	Windows-1251, win-1251, 1251 	Windows-specific Cyrillic charset. This charset is supported in 4.3.2.
		* cp1252 	Windows-1252, 1252 		Windows specific charset for Western European.
		* KOI8-R 	koi8-ru, koi8r			Russian. This charset is supported in 4.3.2.
		* BIG5		950				Traditional Chinese, mainly used in Taiwan.
		* GB2312 	936				Simplified Chinese, national standard character set.
		* BIG5-HKSCS					Big5 with Hong Kong extensions, Traditional Chinese.
		* Shift_JIS 	SJIS, 932			Japanese
		* EUC-JP 	EUCJP				Japanese
		* ------------------------------------------------------------------------------
		**/
		function displayLang($string, $lang = "fr", $encode = "UTF-8") {
			// Show the language session (fr OR en OR ...)
			$string = $this->htmlEntitiesDecode($string, $encode);
			$string = $this->stripSlashesGPC($string);
	
			if (preg_match('#\[' . $lang . '\](.*)\[/' . $lang . ']#Us', $string, $match)) {
				$text = $match[1];
			} else {
				$text = $string;
			}
	
			return $text;
		}
	
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	
	} // End class form
	
}

?>