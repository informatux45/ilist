// Javascript functions
jQuery( document ).ready(function() {
    // -----------------------------------------
    // --- Add Variation ID when changed by user
    // -----------------------------------------
	jQuery('.variation_id').on('change', function() {
		var variation_id = jQuery(this).val();
		if (!variation_id) return false;
		jQuery('input[name=variationid]').val(variation_id);
	});
	
	// -----------------------------------------
	// --- Change POT select / quantity
	// -----------------------------------------
	jQuery('.change_pot').on('change', function() {
		// SELECT
		var select = jQuery(this);
		var product_id = select.attr('data-id');
		var participation_id = select.attr('data-partid');
		var quantity = jQuery(this).find(':selected').attr('data-quantity');
		// BUTTON
		var button = jQuery('#pot_'+product_id);
		// QUANTITY
		var quantity_hidden = jQuery('#quantity_'+product_id);
		// -----------------------------------------
		// Check if Product
		// -----------------------------------------
		if (quantity == 'no') {
			button.attr('data-product_id', product_id);
			button.attr('data-variation_id', product_id);
			button.attr('data-quantity', '1');
			quantity_hidden.val('1');
		} else {
			button.attr('data-product_id', participation_id);
			button.attr('data-variation_id', participation_id);
			button.attr('data-quantity', quantity);
			quantity_hidden.val(quantity);
		}
	});
});

function ilist_show_share_icons( id ) {
	jQuery("#"+id).toggle('slow');
}

function ilist_participation( id ) {
	alert('ID: '+id);
}

function ilist_modal_alert(modal_id) {
	// Get the modal
	var modal = document.getElementById( modal_id );
	modal.style.display = "block";
	// Get the <span> element that closes the modal
	var span = document.getElementById( modal_id + "_close");
	// When the user clicks on <span> (x), close the modal
	span.onclick = function() {
	  modal.style.display = "none";
	};
	// When the user clicks anywhere outside of the modal, close it
	//window.onclick = function(event) {
	//  if (event.target == modal) {
	//	modal.style.display = "none";
	//  }
	//};
}

function ilist_close_modal_alert(modal_id) {
	// close the modal
	var modal = document.getElementById( modal_id );
	modal.style.display = "none";
}

/**
 * Format bytes as human-readable text.
 * 
 * @param bytes Number of bytes.
 * @param si True to use metric (SI) units, aka powers of 1000. False to use 
 *           binary (IEC), aka powers of 1024.
 * @param dp Number of decimal places to display.
 * 
 * @return Formatted string.
 */
function ilist_file_size(bytes, si=false, dp=1) {
	const thresh = si ? 1000 : 1024;
  
	if (Math.abs(bytes) < thresh) {
		return bytes + ' B';
	}
  
	const units = si 
	  ? ['kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'] 
	  : ['KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
	let u = -1;
	const r = 10**dp;
  
	do {
		bytes /= thresh;
		++u;
	} while (Math.round(Math.abs(bytes) * r) / r >= thresh && u < units.length - 1);
  
	return bytes.toFixed(dp) + ' ' + units[u];
}

function ilist_get_extension(filename) {
  var parts = filename.split('.');
  return parts[parts.length - 1];
}

function ilist_is_image(filename) {
  var ext = ilist_get_extension(filename);
  switch (ext.toLowerCase()) {
		case 'jpg':
		case 'jpeg':
		//case 'gif':
		//case 'bmp':
		case 'png':
		return true;
  }
  return false;
}