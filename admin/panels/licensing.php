<form method="post" action="">

<h3><?php _e('Activate UserPro','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="userpro_code"><?php _e('Enter your Item Purchase Code','userpro'); ?></label></th>
		<td>
			<input type="text" name="userpro_code" id="userpro_code" value="<?php echo userpro_get_option('userpro_code'); ?>" class="regular-text" />
		</td>
	</tr>

</table>

<p class="submit">
	<input type="submit" name="verify-license" id="verify-license" class="button button-primary" value="<?php _e('Save Changes','userpro'); ?>"  />
</p>

</form>