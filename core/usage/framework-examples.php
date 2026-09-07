<?php
/** Blocking direct access to plugin
=================================================== */
defined('ABSPATH') or die('Are you crazy!');

/** Framework Examples usage
=================================================== */

// ---------------------------------------------------
// ---------------------------------------------------
// --- Break section
// ---------------------------------------------------
// ---------------------------------------------------
echo $framework->addBreak( 'Create a non Admin Widget' );

// ---------------------------------------------------
// ---------------------------------------------------
// --- Note
// ---------------------------------------------------
// ---------------------------------------------------
echo $framework->addNote(
     'Titre de la note'
    ,'Description de la note'
    ,'top' // top, bottom, middle - Tag TR valign - Alignement de la cellule
);

// ---------------------------------------------------
// ---------------------------------------------------
// --- Anything
// ---------------------------------------------------
// ---------------------------------------------------
echo $framework->addAnything(
     'Absolument tout ce que vous voulez y compris du html'
);

// ---------------------------------------------------
// ---------------------------------------------------
// --- Upload image
// ---------------------------------------------------
// ---------------------------------------------------
echo $framework->addUpload(
     'Background'
    ,[
         'name'            => 'login_background'
        ,'id'              => 'login_background'
        ,'value'           => ilist_get_option('login_background', '') // Default: void
        ,'uploadButtonTxt' => 'Upload image'
        ,'removeButtonTxt' => 'Remove image'
    ]
    ,false // true, false - Required
    ,'Upload your Login background image'
);

// ---------------------------------------------------
// ---------------------------------------------------
// --- Radio Y/N
// ---------------------------------------------------
// ---------------------------------------------------
echo $framework->addRadioYN(
     'Enable Widget'
    ,true
    ,[
         'id'      => 'widget_non_admin_enable'
        ,'name'    => 'widget_non_admin_enable'
        ,'checked' => ilist_get_option('widget_non_admin_enable', '0') // Default: 0
    ]
    ,'Enabled'
    ,'Disabled'
    ,'Default: disabled' // Description
);

// ---------------------------------------------------
// ---------------------------------------------------
// --- Radio multiple choices
// ---------------------------------------------------
// ---------------------------------------------------
$tab_text_visual  = [];
$tab_text_visuals = [
     ['id' => '1', 'title' => 'Visual']
    ,['id' => '2', 'title' => 'Text']
];
foreach($tab_text_visuals as $key => $val) {
    if (ilist_get_option('text_visual_default') == $val['id']) $text_visual_method_checked = $val['id'];
    $tab_text_visual[$key]['text']  = $val['title'];
    $tab_text_visual[$key]['value'] = $val['id'];
}
echo $framework->addRadio(
     'Product add method in product detail page'
    ,$tab_text_visual
    ,[
         'id'      => 'text_visual_default'
        ,'name'    => 'text_visual_default'
        ,'checked' => "$text_visual_method_checked"
        ,'default' => 1
    ]
    ,true
    ,'<br>'
    ,'Default: Visual' // Description
);

// ---------------------------------------------------
// ---------------------------------------------------
// --- Checkbox multiple choices
// ---------------------------------------------------
// ---------------------------------------------------
$all_wc_order_statuses = wc_get_order_statuses();
// array (size=8)
// 'wc-pending' => string 'Attente paiement' (length=16)
// 'wc-processing' => string 'En cours' (length=8)
// 'wc-on-hold' => string 'En attente' (length=10)
// 'wc-completed' => string 'Terminée' (length=9)
// 'wc-cancelled' => string 'Annulée' (length=8)
// 'wc-refunded' => string 'Remboursée' (length=11)
// 'wc-failed' => string 'Échouée' (length=9)
// 'wc-checkout-draft' => string 'Brouillon' (length=9)
// ----------------------------------------
$tab_order_statuses = [];
// ----------------------------------------
foreach($all_wc_order_statuses as $key => $val) {
    array_push($tab_order_statuses, ['id' => str_replace("wc-", "", $key), 'id2' => $key,  'title' => $val]);
}
foreach($tab_order_statuses as $key => $val) {
    if (ilist_get_option('ilist_order_statuses_default') == $val['id']) $order_status_checked = $val['id'];
    $tab_order_status[$key]['text']    = $val['title'];
    $tab_order_status[$key]['inputid'] = $val['id2'];
    $tab_order_status[$key]['name']    = 'ilist_order_statuses_default';
    $tab_order_status[$key]['value']   = $val['id'];
}
echo $ilist_framework->addCheckbox(
     __( 'Choose order statuses in the lists', ILIST_ID_LANGUAGES )
    ,$tab_order_status
    ,array(
         'id'      => 'ilist_order_statuses_default'
        ,'name'    => 'ilist_order_statuses_default'
        ,'checked' => "$order_status_checked"
    )
    ,true
    ,'<br>'
    ,'' // Description
);

// ---------------------------------------------------
// ---------------------------------------------------
// --- Input text
// ---------------------------------------------------
// ---------------------------------------------------
echo $framework->addInput(
     'text'
    ,"Widget Title"
    ,[
         'name'        => 'widget_non_admin_title'
        ,'id'          => 'widget_non_admin_title'
        ,'value'       => ilist_get_option('widget_non_admin_title', '') // Default: void
        ,'placeholder' => ""
        ,'style'       => 'width: 100%; max-width: 400px;'
    ]
    ,true
    ,'' // Description
);

// ---------------------------------------------------
// ---------------------------------------------------
// --- Wordpress Editor
// ---------------------------------------------------
// ---------------------------------------------------
echo $framework->addEditor(
     "Widget Text"
    ,[
         'id'    => 'widget_non_admin'
        ,'name'  => 'widget_non_admin'
        ,'value' => ilist_get_option('widget_non_admin', '') // Default: void
        ,'media_buttons' => true   // Whether to show the Add Media/other media buttons
        ,'wpautop'       => true   // Whether to use wpautop()
        ,'textarea_rows' => 10     // Number rows in the editor textarea
        ,'teeny'         => false  // Whether to output the minimal editor config
    ]
    ,true // Required: true OR false
    ,'' // Description
);

// ---------------------------------------------------
// ---------------------------------------------------
// --- Textarea
// ---------------------------------------------------
// ---------------------------------------------------
echo $framework->addTextarea(
     'New text' . ' <br><i>( authorized HTML )</i>'
    ,ilist_get_option('remove_footer_admin_txt', '') // Default: void
    ,[
         'name'        => 'remove_footer_admin_txt'
        ,'id'          => 'remove_footer_admin_txt'
        ,'value'       => ilist_get_option('remove_footer_admin_txt', '') // Default: void
        ,'placeholder' => ""
        ,'style'       => 'width: 100%; height: 200px; max-height: 100%;'
    ]
    ,false // true, false (required)
    ,'' // Description
);

// ---------------------------------------------------
// ---------------------------------------------------
// --- Code area
// ---------------------------------------------------
// ---------------------------------------------------
echo $framework->addCode(
     'Custom code'
    ,ilist_get_option('custom_css_admin', '') // Default: void
    ,[
        'id'     => 'custom_css_admin'
       ,'height' => 400 // Height in pixels
       ,'fsize'  => 16 // Font-size in pixels
       ,'lang'   => 'css' // css, html, javascript, json, less, lua, markdown, mysql, php, plain_text, python, ruby, sass, scss, sh, text, xml
       ,'theme'  => 'terminal' // ambiance, chaos, chrome, clouds, clouds_midnight, cobalt, crimson_editor, dawn, dreamweaver, eclipse, github, idle_fingers, iplastic, katzenmilch, kr_theme, kuroir, merbivore, merbivore_soft, mono_industrial, monokai, pastel_on_dark, solarized_dark, solarized_light, sqlserver, terminal, textmate, tomorrow, tomorrow_night, tomorrow_night_blue, tomorrow_night_bright, tomorrow_night_eighties, twilight, vibrant_ink, xcode
    ]
    ,false // true, false (required)
    ,'' // Description
);

// ---------------------------------------------------
// ---------------------------------------------------
// --- Number Slider
// ---------------------------------------------------
// ---------------------------------------------------
echo $framework->addNumber(
     "Maximum image size of a list"
    ,[
         'id'      => 'ilist_image_max_size'
        ,'name'    => 'ilist_image_max_size'
        ,'step'    => 10
        ,'min'     => 100
        ,'max'     => 2000
        ,'value'   => ilist_get_option('ilist_image_max_size', 400) // Default: 400
    ]
    ,true // Required: true OR false
    ,'' // Description
);

// ---------------------------------------------------
// ---------------------------------------------------
// --- Select Country
// ---------------------------------------------------
// ---------------------------------------------------
echo $framework->addCountry(
     "Select your country"
    ,[
         'id'    => 'selected_country'
        ,'name'  => 'selected_country'
        ,'value' => ilist_get_option('selected_country', '') // Default: void
    ]
    ,false // true, false - Required
    ,'' // Description
);

// ---------------------------------------------------
// ---------------------------------------------------
// --- Color
// ---------------------------------------------------
// ---------------------------------------------------
echo $framework->addColor(
     'Login Form Background Color'
    ,[
         'name'  => 'login_form_bg_color'
        ,'id'    => 'login_form_bg_color'
        ,'value' => ilist_get_option( 'login_form_bg_color', '#ffffff' ) // Default: #fff
    ]
    ,false // true, false - Required
    ,'Pick a color'
);

// ---------------------------------------------------
// ---------------------------------------------------
// --- Submit, Hidden inputs
// ---------------------------------------------------
// ---------------------------------------------------
echo $framework->addInput(
     'submit'
    ,''
    ,[
        'value' => "Save changes"
    ]
);
echo $framework->addInput(
     'hidden'
    ,''
    ,[
         'id'    => 'widget_non_admin'
        ,'name'  => 'widget_non_admin'
        ,'value' => ilist_get_option('widget_non_admin_title', '')
    ]
);

?>