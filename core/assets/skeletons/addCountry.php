<?php

echo $framework->addCountry(
     'Votre pays'
    ,array(
         'id'      => 'yourplugin_country'
        ,'name'    => 'yourplugin_country'
        ,'default' => __( "Choose your country", YOUR_PLUGIN_ID_LANGUAGES )
        ,'value'   => 'FR' // Alpha3: FRA / Alpha2: FR
        ,'alpha'   => 2 // Option value - 3: alpha3 (Ex: FRA), 2: alpha2 (Ex: FR)
    )
    ,true // Required: true OR false
    ,'Description votre pays'
);

echo $framework->addCountry(
     'Votre pays'
    ,array(
         'id'      => 'yourplugin_country'
        ,'name'    => 'yourplugin_country'
        ,'default' => __( "Choose your country", YOUR_PLUGIN_ID_LANGUAGES )
        ,'value'   => 'FRA' // Alpha3: FRA / Alpha2: FR
        ,'alpha'   => 3 // Option value - 3: alpha3 (Ex: FRA), 2: alpha2 (Ex: FR)
    )
    ,true // Required: true OR false
    ,'Description votre pays'
);

?>