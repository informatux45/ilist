<?php

$tab_add_method  = [];
$tab_add_methods = array(
     ['id' => 'post', 'title' => 'POST']
    ,['id' => 'get',  'title' => 'GET']
                   );
foreach($tab_add_methods as $key => $val) {
    if (get_option('yourplugin_product_add_method') == $val['id']) $add_method_checked = $val['id'];
    $tab_add_method[$key]['text']  = $val['title'];
    $tab_add_method[$key]['value'] = $val['id'];
}
echo $framework->addRadio(
     __( 'Product add method in product detail page', YOUR_PLUGIN_ID_LANGUAGES )
    ,$tab_add_method
    ,array(
         'id'      => 'yourplugin_product_add_method'
        ,'name'    => 'yourplugin_product_add_method'
        ,'checked' => "$add_method_checked"
    )
    ,true // Required: true OR false
    ,'<br>'
    ,__( 'First method - Default: POST', YOUR_PLUGIN_ID_LANGUAGES ) // Description
);

?>