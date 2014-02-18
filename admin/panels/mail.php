<form method="post" action="">

<h3><?php _e('Outgoing Mail Settings','userpro'); ?></h3>
<table class="form-table">
	
	<tr valign="top">
		<th scope="row"><label for="mail_from_name"><?php _e('The name that appears on mails sent by UserPro','userpro'); ?></label></th>
		<td><input type="text" name="mail_from_name" id="mail_from_name" value="<?php echo userpro_get_option('mail_from_name'); ?>" class="regular-text" /></td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="mail_from"><?php _e('The address that appears on mails sent by UserPro','userpro'); ?></label></th>
		<td><input type="text" name="mail_from" id="mail_from" value="<?php echo userpro_get_option('mail_from'); ?>" class="regular-text" /></td>
	</tr>
	
</table>

<h3><?php _e('Email Notifications','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="notify_user_verified"><?php _e('Send an e-mail when user is verified','userpro'); ?></label></th>
		<td>
			<select name="notify_user_verified" id="notify_user_verified" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('notify_user_verified')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('notify_user_verified')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="notify_user_unverified"><?php _e('Send an e-mail when user is unverified','userpro'); ?></label></th>
		<td>
			<select name="notify_user_unverified" id="notify_user_unverified" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('notify_user_unverified')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('notify_user_unverified')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="notify_admin_profile_save"><?php _e('Send admin an e-mail when user updates profile','userpro'); ?></label></th>
		<td>
			<select name="notify_admin_profile_save" id="notify_admin_profile_save" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('notify_admin_profile_save')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('notify_admin_profile_save')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="notify_admin_profile_remove"><?php _e('Send admin an e-mail when profile gets removed','userpro'); ?></label></th>
		<td>
			<select name="notify_admin_profile_remove" id="notify_admin_profile_remove" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('notify_admin_profile_remove')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('notify_admin_profile_remove')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
</table>

<h4><?php _e('Variables you can use to build your mail templates','userpro'); ?></h4>
<p><?php _e('The variables in {CURLY BRACKETS} are used to present data and info in email. You can use them to customize your email template.','userpro'); ?><?php userpro_admin_list_builtin_vars('{VAR1}'); ?></p>

<h3><?php _e('User Awaiting Manual Review Template (Admin Notification)','userpro'); ?></h3>
<table class="form-table">
	
	<tr valign="top">
		<th scope="row"><label for="mail_admin_pendingapprove"><?php _e('Email Content','userpro'); ?></label></th>
		<td><textarea name="mail_admin_pendingapprove" id="mail_admin_pendingapprove" class="large-text code" rows="10"><?php echo userpro_get_option('mail_admin_pendingapprove'); ?></textarea></td>
	</tr>
	
</table>

<h3><?php _e('New Registration Template (Admin Notification)','userpro'); ?></h3>
<table class="form-table">
	
	<tr valign="top">
		<th scope="row"><label for="mail_admin_newaccount"><?php _e('Email Content','userpro'); ?></label></th>
		<td><textarea name="mail_admin_newaccount" id="mail_admin_newaccount" class="large-text code" rows="10"><?php echo userpro_get_option('mail_admin_newaccount'); ?></textarea></td>
	</tr>
	
</table>

<h3><?php _e('Customize "Email Validation" Mail','userpro'); ?></h3>
<table class="form-table">
	
	<tr valign="top">
		<th scope="row"><label for="mail_verifyemail"><?php _e('Email Content','userpro'); ?></label></th>
		<td><textarea name="mail_verifyemail" id="mail_verifyemail" class="large-text code" rows="10"><?php echo userpro_get_option('mail_verifyemail'); ?></textarea></td>
	</tr>
	
</table>

<h3><?php _e('Customize "New Account/Welcome" Mail','userpro'); ?></h3>
<table class="form-table">
	
	<tr valign="top">
		<th scope="row"><label for="mail_newaccount"><?php _e('Email Content','userpro'); ?></label></th>
		<td><textarea name="mail_newaccount" id="mail_newaccount" class="large-text code" rows="10"><?php echo userpro_get_option('mail_newaccount'); ?></textarea></td>
	</tr>
	
</table>

<h3><?php _e('Customize "Reset Password" Mail','userpro'); ?></h3>
<table class="form-table">
	
	<tr valign="top">
		<th scope="row"><label for="mail_secretkey"><?php _e('Email Content','userpro'); ?></label></th>
		<td><textarea name="mail_secretkey" id="mail_secretkey" class="large-text code" rows="10"><?php echo userpro_get_option('mail_secretkey'); ?></textarea></td>
	</tr>
	
</table>

<h3><?php _e('Customize "Account Removal" Mail','userpro'); ?></h3>
<table class="form-table">
	
	<tr valign="top">
		<th scope="row"><label for="mail_accountdeleted"><?php _e('Email Content','userpro'); ?></label></th>
		<td><textarea name="mail_accountdeleted" id="mail_accountdeleted" class="large-text code" rows="10"><?php echo userpro_get_option('mail_accountdeleted'); ?></textarea></td>
	</tr>
	
</table>

<h3><?php _e('Customize "Account Verified" Mail','userpro'); ?></h3>
<table class="form-table">
	
	<tr valign="top">
		<th scope="row"><label for="mail_accountverified"><?php _e('Email Content','userpro'); ?></label></th>
		<td><textarea name="mail_accountverified" id="mail_accountverified" class="large-text code" rows="10"><?php echo userpro_get_option('mail_accountverified'); ?></textarea></td>
	</tr>
	
</table>

<h3><?php _e('Customize "Account Un-verified" Mail','userpro'); ?></h3>
<table class="form-table">
	
	<tr valign="top">
		<th scope="row"><label for="mail_accountunverified"><?php _e('Email Content','userpro'); ?></label></th>
		<td><textarea name="mail_accountunverified" id="mail_accountunverified" class="large-text code" rows="10"><?php echo userpro_get_option('mail_accountunverified'); ?></textarea></td>
	</tr>
	
</table>

<h3><?php _e('Customize "Invitation to Get Verified" Mail','userpro'); ?></h3>
<table class="form-table">
	
	<tr valign="top">
		<th scope="row"><label for="mail_verifyinvite"><?php _e('Email Content','userpro'); ?></label></th>
		<td><textarea name="mail_verifyinvite" id="mail_verifyinvite" class="large-text code" rows="10"><?php echo userpro_get_option('mail_verifyinvite'); ?></textarea></td>
	</tr>
	
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','userpro'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','userpro'); ?>"  />
</p>

</form>