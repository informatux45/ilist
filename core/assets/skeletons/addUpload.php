<?php

echo $framework->addUpload(
     __( 'Logo', YOUR_PLUGIN_ID_LANGUAGES )
    ,array(
         'name'        => 'yourplugin_logo'
        ,'id'          => 'yourplugin_logo'
        ,'value'       => get_option('yourplugin_logo') // Url de l'image (Ex: http://localhost/clients/ilist/wp-content/uploads/2021/04/monimage.jpg)
        ,'uploadButtonTxt' => __( 'Upload Image', YOUR_PLUGIN_ID_LANGUAGES ) // Optional
        ,'removeButtonTxt' => __( 'Remove Image', YOUR_PLUGIN_ID_LANGUAGES ) // Optional
        ,'previewSize'     => '300px' // Optional height in pixels - Default: 150px
    )
    ,true
    ,__( 'Upload your Logo image', YOUR_PLUGIN_ID_LANGUAGES ) // Description
);

?>