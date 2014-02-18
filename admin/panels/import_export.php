<form method="post" action="">

<h3><?php _e('Import / Export (Settings only)','userpro'); ?></h3>

<table class="form-table">

	<tr valign="top">
		<th scope="row"><label><?php _e('Export Settings','userpro'); ?></label></th>
		<td>
			<?php $this->create_export_download_link(true, 'userpro_export_options'); ?>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label><?php _e('Import Settings','userpro'); ?></label></th>
		<td>
			<textarea name="userpro_import" id="userpro_import" class="large-text" rows="10"></textarea>
			<input type="submit" name="import_settings" id="import_settings" class="button button-primary" value="<?php _e('Import','userpro'); ?>"  />
		</td>
	</tr>

</table>

<h3><?php _e('Import / Export Fields','userpro'); ?></h3>

<table class="form-table">

	<tr valign="top">
		<th scope="row"><label><?php _e('Export Fields','userpro'); ?></label></th>
		<td>
			<?php $this->create_export_download_link(true, 'userpro_export_fields'); ?>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label><?php _e('Import Fields','userpro'); ?></label></th>
		<td>
			<textarea name="userpro_import_fields" id="userpro_import_fields" class="large-text" rows="10"></textarea>
			<input type="submit" name="import_fields" id="import_fields" class="button button-primary" value="<?php _e('Import','userpro'); ?>"  />
		</td>
	</tr>

</table>

<h3><?php _e('Import / Export Field Groups','userpro'); ?></h3>

<table class="form-table">

	<tr valign="top">
		<th scope="row"><label><?php _e('Export Field Groups','userpro'); ?></label></th>
		<td>
			<?php $this->create_export_download_link(true, 'userpro_export_groups'); ?>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label><?php _e('Import Field Groups','userpro'); ?></label></th>
		<td>
			<textarea name="userpro_import_groups" id="userpro_import_groups" class="large-text" rows="10"></textarea>
			<input type="submit" name="import_groups" id="import_groups" class="button button-primary" value="<?php _e('Import','userpro'); ?>"  />
		</td>
	</tr>

</table>

</form>