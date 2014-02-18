<?php global $userpro, $userpro_badges; ?>

<form action="" method="post">

<h3><?php _e('Edit/Delete User Badges','userpro'); ?></h3>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="badge_user"><?php _e('Select a user','userpro'); ?></label></th>
		<td>
			<select name="badge_user" id="badge_user" class="chosen-select" style="width:300px" data-placeholder="">
				<option value=""><?php _e('Select a user...','userpro'); ?></option>
				<?php
				$users=userpro_badges_admin_users(true);
				foreach($users as $user) {
				?>
				<option value="<?php echo $user->ID; ?>" <?php userpro_admin_post_value('badge_user', $user->ID, $_POST); ?>><?php echo userpro_profile_data('display_name', $user->ID); if ($user->user_email) echo ' ('. $user->user_email . ')'; ?></option>
				<?php } ?>
			</select>
		</td>
	</tr>
</table>

<p class="submit">
	<input type="submit" name="find-user-badges" id="find-user-badges" class="button button-primary" value="<?php _e('Find User Badges','userpro'); ?>"  />
</p>

</form>

<?php
if (isset($_POST['badge_user']) && $userpro->user_exists($_POST['badge_user']) ) {

	$user_id = $_POST['badge_user'];
	$badges = get_user_meta($user_id, '_userpro_badges', true);
	if (isset($badges) && is_array($badges) && !empty($badges)){
		echo '<h3>'.sprintf(__('%s\'s Given Badges','userpro'), userpro_profile_data('display_name', $user_id) ).'</h3>';
		foreach($badges as $k => $arr) {
			?>
			
			<div class="userpro-user-badge">
				<img src="<?php echo $arr['badge_url']; ?>" alt="" title="<?php echo $arr['badge_title']; ?>" /> <?php echo $arr['badge_title']; ?>
				<a href="#" class="button userpro-delete-badge" data-user="<?php echo $user_id; ?>" data-url="<?php echo $arr['badge_url']; ?>"><?php _e('Delete Badge','userpro'); ?></a>
			</div>
			
			<?php
		}
	} else {
		delete_user_meta($user_id,'_userpro_badges');
		echo '<p>'.__('This user does not have any manually assigned badges.','userpro').'</p>';
	}

}
?>