<?php global $userpro_badges; ?>

<h3><?php echo userpro_badges_admin_title(); ?></h3>

<p><?php printf(__('If you want to add more badges, please put your badges as PNG in <code>%s</code>. To give a new badge, or assign a new achievement, click on a badge below to start.','userpro'), userpro_dg_url . 'badges/'); ?></p>

<?php echo $userpro_badges->loop_badges(); ?>

<form action="" method="post">

<table class="form-table">

	<?php if (userpro_badges_admin_edit()){?>
	<input type="hidden" name="badge_url" id="badge_url" value="<?php echo userpro_badges_admin_edit_info('badge_url'); ?>" />
	<?php } else { ?>
	<input type="hidden" name="badge_url" id="badge_url" value="" />
	<?php } ?>
	
	<tr valign="top">
		<th scope="row"><label for="badge_title"><?php _e('Badge Title','userpro'); ?></label></th>
		<td>
			<input type="text" name="badge_title" id="badge_title" value="<?php if (userpro_badges_admin_edit()) echo userpro_badges_admin_edit_info('badge_title'); ?>" class="regular-text" />
			<span class="description"><?php _e('The title of badge will appear when user hovers over the badge e.g. Featured User, User of the Year, etc.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="badge_method"><?php _e('How users can get this badge?','userpro'); ?></label></th>
		<td>
			<select name="badge_method" id="badge_method" class="chosen-select" style="width:300px" data-placeholder="">
				<option value="manual" <?php if (userpro_badges_admin_edit()) echo 'disabled="disabled"'; ?>><?php _e('Give this badge to users (manual)','userpro'); ?></option>
				<option value="achievement"><?php _e('Require achievement (automatic)','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
</table>

<!-- Conditional Fields -->

<table class="form-table" data-type="conditional" rel="manual">
	<tr valign="top">
		<th scope="row"><label for="badge_to_users[]"><?php _e('Choose which users receive this badge','userpro'); ?></label></th>
		<td>
			<select name="badge_to_users[]" id="badge_to_users[]" multiple="multiple" class="chosen-select" style="width:300px" data-placeholder="<?php _e('Choose...','userpro'); ?>">
				<?php
				$users=userpro_badges_admin_users();
				foreach($users as $user) {
				?>
				<option value="<?php echo $user->ID; ?>"><?php echo userpro_profile_data('display_name', $user->ID); if ($user->user_email) echo ' ('. $user->user_email . ')'; ?></option>
				<?php } ?>
			</select>
			<span class="description"><?php _e('You can assign this badge to specific to the users you want by choosing them here.','userpro'); ?></span>
		</td>
	</tr>
</table>

<table class="form-table" data-type="conditional" rel="achievement">
	<tr valign="top">
		<th scope="row"><label><?php _e('Setup Achievement','userpro'); ?></label></th>
		<td>
			<label for="badge_achieved_num"><?php _e('User has completed','userpro'); ?></label>
			<input type="text" name="badge_achieved_num" id="badge_achieved_num" value="<?php if (userpro_badges_admin_edit()) echo $_GET['bid']; ?>" class="badge_achieved_num" />
			<select name="badge_achieved_type" id="badge_achieved_type" class="chosen-select" style="width:300px" data-placeholder="">
				<option value="any" <?php if ( userpro_badges_admin_edit() ) selected('any', $_GET['btype']); ?> ><?php _e('Posts (Any post type)','userpro'); ?></option>
				<option value="comments" <?php if ( userpro_badges_admin_edit() ) selected('comments', $_GET['btype']); ?> ><?php _e('Comments','userpro'); ?></option>
				<?php echo userpro_badges_admin_post_types(); ?>
			</select>
		</td>
	</tr>
</table>

<p class="submit">
	<input type="submit" name="insert-badge" id="insert-badge" class="button button-primary" value="<?php _e('Submit','userpro'); ?>"  />
</p>

</form>