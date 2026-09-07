<?php

echo $framework->addInput(
     'text' // text, email, file, hidden, number, password, tel, url, ...
    ,__( "Url of the search page list", YOUR_PLUGIN_ID_LANGUAGES )
    ,array(
         'name'        => 'yourplugin_search_url_complete'
        ,'id'          => 'yourplugin_search_url_complete'
        ,'value'       => get_option('yourplugin_search_url_complete')
        ,'placeholder' => ""
        ,'style'       => 'width: 100%; max-width: 400px;'
    )
    ,true // Required: true OR false
    ,'' // Description
);

?>