<?php

echo $framework->addInput(
     'date'
    ,'Indiquez votre date'
    ,array(
         'name'        => 'yourplugin_date'
        ,'id'          => 'yourplugin_date'
        ,'value'       => '2021-03-21'
        ,'placeholder' => ''
        ,'min'         => '2021-03-01'
        ,'max'         => '2021-03-31'
        ,'step'        => 7
    )
    ,true
    ,'' // Description
);
        
?>