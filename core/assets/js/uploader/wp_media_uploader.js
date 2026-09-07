/**
 * 
 * wpMediaUploader v1.0 2016-11-05
 * Copyright (c) 2016 Smartcat
 * 
 */
( function( $) {
    $.wpMediaUploader = function( options ) {
        
        var wpMediaUploader_default_img = " data:image/jpeg;base64,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";
        
        var settings = $.extend({
            
            target : '.smartcat-uploader', // The class wrapping the textbox
            inputId : '#smartcat-input', // Input ID
            uploaderTitle : 'Select or upload image', // The title of the media upload popup
            uploaderButton : 'Set image', // the text of the button in the media upload popup
            removeButton : '#smartcat-remove', // ID remove class
            removeText : 'Remove image', // Remove Text Button
            multiple : false, // Allow the user to select multiple images
            buttonText : 'Upload image', // The text of the upload button
            buttonClass : '.smartcat-upload', // the class of the upload button
            previewValue : wpMediaUploader_default_img, // Image preview Value
            previewImg : '#smartcat-image', // ID Preview image
            previewSize : '150px', // The preview image size (Width / Height)
            modal : false, // is the upload button within a bootstrap modal ?
            buttonStyle : { // style the button
                color : '#fff',
                background : '#3bafda',
                fontSize : '16px',                
                padding : '10px 15px',                
            },
            
        }, options );
        
        // Image (Default OR from input value)
        $( settings.target ).append('<div style="margin: 0 0 1em 0;"><img id="' + settings.previewImg.replace('#','') + '" src="' + settings.previewValue + '" style="border: 1px solid lightgrey; width: 100%; max-width: ' + settings.previewSize + '; height: ' + settings.previewSize + '; object-fit: cover; background-color: lightgrey;"/></div>');
        // Buttons (Upload / Remove)
        $( settings.target ).append( '<a href="#" class="' + settings.buttonClass.replace('.','') + '">' + settings.buttonText + '</a>&nbsp;&nbsp;<a class="" id="' + settings.removeButton.replace('#','') + '" style="cursor: pointer;">' + settings.removeText + '</a><br><div style="margin: 0 0 0.2em 0;">&nbsp;</div>' );
        
        $( settings.buttonClass ).css( settings.buttonStyle );
        
        $(settings.removeButton).on('click', function(e) {
            e.preventDefault();
            
            $(settings.previewImg).attr('src', wpMediaUploader_default_img);
            $(settings.inputId).val(null);
        });
        
        $('body').on('click', settings.buttonClass, function(e) {
            
            e.preventDefault();
            var selector = $(this).parent( settings.target );
            var custom_uploader = wp.media({
                title: settings.uploaderTitle,
                button: {
                    text: settings.uploaderButton
                },
                multiple: settings.multiple
            })
            .on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                selector.find( 'img' ).attr( 'src', attachment.url).show();
                selector.find( 'input' ).val(attachment.url);
                if( settings.modal ) {
                    $('.modal').css( 'overflowY', 'auto');
                }
            })
            .open();
        });
        
        
    }
})(jQuery);
