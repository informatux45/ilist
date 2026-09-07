
jQuery(document).ready(function() {
   
    jQuery('#product_id').select2({
        placeholder: ilist_add_product_params.select_text,
        ajax: {
            delay: 250, // wait 250 milliseconds before triggering the request
            url: ilist_add_product_params.ajaxurl,
            dataType: 'json',
            data: function (params) {
                var query = {
                    search: params.term,
                    action: 'ilist_get_products' // AJAX action for admin-ajax.php
                };
                return query;
            },
            processResults: function (data) {
                var options = [];
				if ( data ) {
					// data is the array of arrays, and each of them contains ID and the Label of the option
					jQuery.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
						options.push( { id: text[0], text: text[1]  } );
					});
				}
				return {
					results: options
				};
            },
            cache: true
        },
        minimumInputLength: 3 // the minimum of symbols to input before perform a search
    }); 
    
});