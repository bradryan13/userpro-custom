<?php global $userpro, $userpro_badges; ?>

<p><?php _e('You can find the achievement badges that you have created and edit the rules of achievement from this page.','userpro'); ?></p>

<table class="wp-list-table widefat fixed">

	<thead>
		<tr>
			<th scope='col' class='manage-column'><?php _e('Achievement Type','userpro'); ?></th>
			<th scope='col' class='manage-column'><?php _e('Required','userpro'); ?></th>
			<th scope='col' class='manage-column'><?php _e('Badge Title','userpro'); ?></th>
			<th scope='col' class='manage-column'><?php _e('Badge','userpro'); ?></th>
			<th scope='col' class='manage-column'><?php _e('Actions','userpro'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th scope='col' class='manage-column'><?php _e('Achievement Type','userpro'); ?></th>
			<th scope='col' class='manage-column'><?php _e('Required','userpro'); ?></th>
			<th scope='col' class='manage-column'><?php _e('Badge Title','userpro'); ?></th>
			<th scope='col' class='manage-column'><?php _e('Badge','userpro'); ?></th>
			<th scope='col' class='manage-column'><?php _e('Actions','userpro'); ?></th>
		</tr>
	</tfoot>

	<?php
	$achievement = get_option('_userpro_badges');
	if ($achievement){
	?>

		<?php foreach($achievement as $k => $badge) { ?>
		
			<tr>
				<td valign="top"><?php echo $k; ?></td>
				<?php foreach($badge as $n => $arr) { ?>
				<td valign="top"><?php echo $n; ?></td>
				<td valign="top"><?php echo $arr['badge_title']; ?></td>
				<td valign="top"><img src="<?php echo $arr['badge_url']; ?>" alt="" /></td>
				<td valign="top"><a href="<?php echo admin_url(); ?>admin.php?page=userpro-badges&tab=manage&btype=<?php echo $k; ?>&bid=<?php echo $n; ?>"><?php _e('Edit','userpro'); ?></a> | <a href="#" class="userpro-badge-remove" data-btype="<?php echo $k; ?>" data-bid="<?php echo $n; ?>"><?php _e('Remove','userpro'); ?></a></td>
				<?php } ?>
			</tr>
			
		<?php } ?>
		
	<?php
		} else {
	?>
	
			<tr>
				<td valign="top" colspan="5"><?php printf(__('You did not create any achievement badges yet. Click <a href="%s">here</a> to add some badges.','userpro'), admin_url(). 'admin.php?page=userpro-badges&tab=manage'); ?></td>
				<td valign="top"></td>
				<td valign="top"></td>
				<td valign="top"><img src="<?php echo $arr['badge_url']; ?>" alt="" /></td>
				<td valign="top"><a href="<?php echo admin_url(); ?>/admin.php?page=userpro-badges&tab=manage&btype=<?php echo $k; ?>&bid=<?php echo $n; ?>"><?php _e('Edit','userpro'); ?></a> | <a href="#" class="userpro-badge-remove" data-btype="<?php echo $k; ?>" data-bid="<?php echo $n; ?>"><?php _e('Remove','userpro'); ?></a></td>
				<?php } ?>
			</tr>

</table>