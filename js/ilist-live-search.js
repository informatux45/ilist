jQuery(document).ready(function(){
	
	var req = null;
	jQuery('#keysearch').on('keyup', function(){
		var key = jQuery('#keysearch').val();
		if (key && key.length > 2) {
			jQuery('#loading').css('display', 'block');
			if (req)
				req.abort();

			var data = {
				'action': 'ilist_live_search',
				'keysearch': key
			};
			
			req = jQuery.post(
				ajaxurl,
				data,
				function(response){
					jQuery('#loading').css('display', 'none');
					jQuery("#result").html(response).show();
				}
			);					
		} else {
			jQuery('#loading').css('display', 'none');
			jQuery('#result').css('display', 'none');
		}
	});
});