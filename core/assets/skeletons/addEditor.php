<?php

echo $framework->addEditor(
     __( 'Head text', YOUR_PLUGIN_ID_LANGUAGES )
    ,array(
         'id'    => 'yourplugin_print_head_text'
        ,'name'  => 'yourplugin_print_head_text'
        ,'value' => get_option('yourplugin_print_head_text', '') // Default: void
        ,'media_buttons' => false  // Whether to show the Add Media/other media buttons
		,'wpautop'       => true   // Whether to use wpautop()
		,'textarea_rows' => 20     // Number rows in the editor textarea
		,'teeny'         => false  // Whether to output the minimal editor config
    )
    ,true // Required: true OR false
    ,'' // Description
);

?>