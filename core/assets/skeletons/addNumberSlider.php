<?php

echo $framework->addNumber(
     __( "Range price", YOUR_PLUGIN_ID_LANGUAGES )
    ,array(
         'id'      => 'yourplugin_pot_range_price'
        ,'name'    => 'yourplugin_pot_range_price'
        ,'step'    => 1
        ,'min'     => 1
        ,'max'     => 2000
        ,'value'   => get_option('yourplugin_pot_min_price_product', 50) // Default: 50
    )
    ,true // Required: true OR false
    ,'' // Description
);

?>