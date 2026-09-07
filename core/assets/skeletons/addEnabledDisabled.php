<?php

echo $framework->addRadioYN(
     __( 'WP Dashboard Widget', YOUR_PLUGIN_ID_LANGUAGES )
    ,true // Required: true OR false
    ,array(
         'id'      => 'yourplugin_widget_dashboard_summary'
        ,'name'    => 'yourplugin_widget_dashboard_summary'
        ,'checked' => get_option('yourplugin_widget_dashboard_summary', '1') // Default: 1
    )
    ,__( 'Enabled', YOUR_PLUGIN_ID_LANGUAGES )
    ,__( 'Disabled', YOUR_PLUGIN_ID_LANGUAGES )
    ,__( 'Yes - Default: enabled', YOUR_PLUGIN_ID_LANGUAGES ) // Description
);

?>