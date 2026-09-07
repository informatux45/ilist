<?php

echo $framework->addInput(
     'time'
    ,'Indiquez votre heure'
    ,array(
         'name'        => 'yourplugin_time'
        ,'id'          => 'yourplugin_time'
        ,'value'       => '17:07' // Ou 17:07:59
        ,'placeholder' => ''
        ,'min'         => '17:00' // Ou 17:00:00
        ,'max'         => '17:59' // Ou 17:59:59
    )
    ,true // Required: true OR false
    ,'' // Description
);

?>