jQuery( document ).ready(function() {

    jQuery('#ilist_create_product').on('click', function(e) {
        
        e.preventDefault();
        
        // Loader
        jQuery('#ilist_create_product_loader').css('display', 'block');
        // Button
        jQuery('#ilist_create_product').css('display', 'none');
        
        jQuery.ajax({
            url: ilist_admin_ajax.ajaxurl,
            type: "POST",
            data: {
                'action': 'ilist_return_ajax',
                'title': jQuery('#product_title').val(),
                'description': jQuery('#product_desc').val(),
                'price': jQuery('#product_price').val(),
            }
        })
        .done(function (response) {
           // Loader
           jQuery('#ilist_create_product_loader').css('display', 'none');
           // Modal
           jQuery("#modalIlist").css('display', 'none');
           // Button
           jQuery('#ilist_create_product').css('display', 'block');
           // Parse JSON
           var data = JSON.parse(response);
           alert(data.message);
       })
       .fail(function (jqXHR, textStatus, errorThrown) {
           // Loader
           jQuery('#ilist_create_product_loader').css('display', 'none');
           // Modal
           jQuery("#modalIlist").css('display', 'none');
           // Button
           jQuery('#ilist_create_product').css('display', 'block');
           alert('ERROR: '+errorThrown);
       });
        
    });
    
});