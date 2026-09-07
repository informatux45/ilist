function ilist_get_variation_price(pid, btid) {
    
    jQuery.ajax({
        url: ilist_ajax_script.ajaxurl,
        type: "POST",
        data: {
            //_ajax_nonce: ilist_ajax_script.nonce, // nonce
            action: 'ilist_ajax_product_infos',
            switch_a: 'get_variation_price',
            variation_id: pid
        },
        datatype: 'json'
    })
    .done(function (data) {
        jQuery("#price"+btid).val(data);
        console.log(data);
    })
    .fail(function (jqXHR, textStatus, errorThrown) {
        console.log(errorThrown);
    });
}

function ilist_notify_admins( lid, text, response ) {
    if (confirm(text) == true) {
        jQuery.ajax({
            url: ilist_ajax_script.ajaxurl,
            type: "POST",
            data: {
                //_ajax_nonce: ilist_ajax_script.nonce, // nonce
                action: 'ilist_ajax_product_infos',
                switch_a: 'notify_admins',
                list_id: lid
            },
            datatype: 'json'
        })
        .done(function (data) {
            if (data == 'noid')
                alert('ERROR ID');
            else
                alert(response);
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        });
    }
}

jQuery( document ).ready(function() {

    jQuery('#ilist_create_product').click( function() {
        
    });
    
});