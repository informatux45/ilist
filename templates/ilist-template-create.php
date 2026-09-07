<?php
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
// Blocking direct access to plugin      -=
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
defined('ABSPATH') or die('Are you crazy!');

// -------------------------------------
// Show message next to logged in or not
// -------------------------------------
if (!is_user_logged_in()) {
	
	echo '<div class="">';
	echo __( 'You must be logged in to view your lists and create new ones', ILIST_ID_LANGUAGES );
	echo '</div>';
	
} else {

	if (isset($message_success) && array_keys( $message_success, true )) {
		echo '<div class="alert alert-success">';
		for ($i = 0; $i < count($message_success); ++$i) {
			echo $message_success[$i] . '<br>';
		}
		echo '</div>';
	}
	
	if (isset($message_error) && array_keys( $message_error, true )) {
		echo '<div class="alert alert-danger">';
		for ($i = 0; $i < count($message_error); ++$i) {
			echo $message_error[$i] . '<br>';
		}
		echo '</div>';
	}
	
	// Show form
	if (!isset($message_success) || !array_keys( $message_success, true )) {
	?>
	
	<form action="" enctype="multipart/form-data" method="post">
	
		<input id="nonce" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>" type="hidden">
		<input type="hidden" name="password_hash" value="<?php if (isset($ilist_password)) echo $ilist_password; ?>">
		
		<p class="form-row form-row-wide">
			<label for="ilist_name"><?php echo __( 'Name of your list', ILIST_ID_LANGUAGES ); ?> <abbr class="required" title="required">*</abbr></label>
			<input name="ilist_name" id="ilist_name" class="input-text" value="<?php if (isset($ilist_name)) echo $ilist_name; ?>" type="text" minlength="3" required>
		</p>
		
		<p class="form-row form-row-wide">
			<label for="ilist_description"><?php echo __( 'Description', ILIST_ID_LANGUAGES ); ?></label>
			<textarea name="ilist_description" id="ilist_description"><?php if (isset($ilist_description)) echo $ilist_description; ?></textarea>
		</p>
		
		<?php if (isset($ilist_image) && $ilist_image != '') { ?>
			<p class="form-row form-row-wide">
				<label for="ilist_image"><?php echo __( 'Image', ILIST_ID_LANGUAGES ); ?></label>
				<img id="ilist_image_display" src="<?php echo $ilist_image; ?>" alt="">
				<input type="hidden" name="ilist_image" value="<?php echo $ilist_image; ?>">
				<input type="checkbox" name="ilist_remove_image" id="ilist_remove_image" value="1">
				<label id="ilist_remove_image_label" for="ilist_remove_image"><?php echo __( 'Remove image', ILIST_ID_LANGUAGES ); ?></label>
			</p>
		<?php } else { ?>
			<p class="form-row form-row-wide">
				<label for="ilist_image"><?php echo __( 'Image', ILIST_ID_LANGUAGES ); ?></label>
				<input type="file" name="ilist_image" id="ilist_image" accept="image/png, image/jpeg">
				<input type="hidden" id="ilist_image_max_size" name="ilist_image_max_size" value="<?php echo intval(ilist_get_option('ilist_image_max_size')) * 1024; ?>" />
				<script>
					jQuery('#ilist_image').bind('change', function() {
						// Check file size
						var filesize = this.files[0].size;
						var maxSize  = jQuery('#ilist_image_max_size').val();
						if (maxSize < filesize) {
							alert("<?php echo __( 'The maximum authorized weight is', ILIST_ID_LANGUAGES ); ?> " + ilist_file_size(maxSize) + "\n<?php echo __( 'Your file weighs', ILIST_ID_LANGUAGES ); ?> " + ilist_file_size(filesize));
							jQuery(this).val('');
							return false;
						}
						// Check file type
						var fullPath = jQuery('#ilist_image').val();
						var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
						var filename = fullPath.substring(startIndex);
						if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
							filename = filename.substring(1);
						}
						var filetype = ilist_is_image(filename);
						if (!filetype) {
							alert("<?php echo __( 'Only JPG and PNG files are allowed!', ILIST_ID_LANGUAGES ); ?>");
							jQuery(this).val('');
							return false;
						}
					});
				</script>
			</p>
		<?php } ?>
		
		<hr>
		<p class="form-row form-row-wide">
			<label id="ilist_password_label" for="ilist_password"><?php echo __( 'List password', ILIST_ID_LANGUAGES ); ?></label>
            <input id="ilist_password" name="ilist_password" type="password" value="" placeholder="<?php echo __( 'Password', ILIST_ID_LANGUAGES ); ?>">
            &nbsp;&nbsp;
            <input id="ilist_password_confirm" name="ilist_password_confirm" type="password" value="" placeholder="<?php echo __('Confirm password', ILIST_ID_LANGUAGES ); ?>">
			<small id="ilist_password_help" class="form-text text-muted"><?php echo __( 'You can add a password to make your list private', ILIST_ID_LANGUAGES ); ?></small>
			<?php if ( (isset($ilist_password) && trim($ilist_password) != '')  ) { ?>
				<input id="ilist_password_remove" type="checkbox" name="ilist_password_remove" value="1">
				<label id="ilist_password_remove_label" for="ilist_password_remove"><?php _e('Remove password', ILIST_ID_LANGUAGES)?></label>
			<?php } ?>
		</p>
		
		<hr>
		<p><?php echo __( 'Enter a name which you wish to associate with this list.', ILIST_ID_LANGUAGES ); ?></p>
		<p class="form-row form-row-first">
			<label for="ilist_firstname"><?php echo __( 'Firstname', ILIST_ID_LANGUAGES ); ?> <abbr class="required" title="required">*</abbr></label>
			<input name="ilist_firstname" id="ilist_firstname" class="input-text" value="<?php if (isset($ilist_firstname)) echo $ilist_firstname; ?>" type="text" minlength="3" required>
		</p>
		
		<p class="form-row form-row-last">
			<label for="ilist_lastname"><?php echo __( 'Lastname', ILIST_ID_LANGUAGES ); ?> <abbr class="required" title="required">*</abbr></label>
			<input name="ilist_lastname" id="ilist_lastname" class="input-text" value="<?php if (isset($ilist_lastname)) echo $ilist_lastname; ?>" type="text" minlength="3" required>
		</p>
		
		<p class="form-row form-row-wide">
			<label for="ilist_email"><?php echo __( 'Email associated with the list', ILIST_ID_LANGUAGES ); ?> <abbr class="required" title="required">*</abbr></label>
			<input name="ilist_email" id="ilist_email" value="<?php if (isset($ilist_email)) echo $ilist_email; ?>" class="input-text" type="email" required>
			<small id="ilist_email_help" class="form-text text-muted"><?php echo __( 'Users will be able to find your list by searching with this email', ILIST_ID_LANGUAGES ); ?></small>
		</p>
		
		<div class="ilist-clear"></div>
		
		<p class="form-row"></p>
		
		<p class="form-row">
			<?php
				if ($action == 'modifier-une-liste') {
					$ilist_button_submit = __( 'Edit list', ILIST_ID_LANGUAGES );
					$ilist_button_name   = "ilist_update";
					echo '<input type="hidden" name="id" value="'.$ilist_list_id.'">';
				} else {
					$ilist_button_submit = __( 'Create a list', ILIST_ID_LANGUAGES );
					$ilist_button_name   = "ilist_create";
				}
			?>
			<input class="button alt" name="<?php echo $ilist_button_name; ?>" value="<?php echo $ilist_button_submit; ?>" type="submit">
		</p>
				
	</form>
	<?php
	}
}