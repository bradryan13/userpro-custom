<form method="post" action="">

<h3><?php _e('Global Restrict/Lock Settings','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="site_guest_lockout"><?php _e('Do you want to lock entire site for guests?','userpro'); ?></label></th>
		<td>
			<select name="site_guest_lockout" id="site_guest_lockout" class="chosen-select" style="width:300px">
				<option value="1" <?php selected(1, userpro_get_option('site_guest_lockout')); ?>><?php _e('Yes','userpro'); ?></option>
				<option value="0" <?php selected(0, userpro_get_option('site_guest_lockout')); ?>><?php _e('No','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="site_guest_lockout_pageid"><?php _e('The page ID that guests will be redirected to (If you locked the entire site above)','userpro'); ?></label></th>
		<td>
			<input type="text" name="site_guest_lockout_pageid" id="site_guest_lockout_pageid" value="<?php echo userpro_get_option('site_guest_lockout_pageid'); ?>" class="regular-text" />
			<span class="description"><?php _e('This is typically your custom login page. The only page that guests can access if you lock entire site','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="homepage_guest_lockout"><?php _e('Redirect guests from homepage to custom URL','userpro'); ?></label></th>
		<td>
			<input type="text" name="homepage_guest_lockout" id="homepage_guest_lockout" value="<?php echo userpro_get_option('homepage_guest_lockout'); ?>" class="regular-text" />
			<span class="description"><?php _e('This option allow you to lock the homepage completely for guests and auto-redirect them to any page you want.','userpro'); ?></span>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for="homepage_member_lockout"><?php _e('Redirect members from homepage to custom URL','userpro'); ?></label></th>
		<td>
			<input type="text" name="homepage_member_lockout" id="homepage_member_lockout" value="<?php echo userpro_get_option('homepage_member_lockout'); ?>" class="regular-text" />
			<span class="description"><?php _e('This option allow you to lock the homepage completely for members and auto-redirect them to any page you want.','userpro'); ?></span>
		</td>
	</tr>
	
</table>

<h3><?php _e('Content-Restricted Posts and Pages','userpro'); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row"><label for="restricted_page_verified"><?php _e('Make Restricted Content Available To','userpro'); ?></label></th>
		<td>
			<select name="restricted_page_verified" id="restricted_page_verified" class="chosen-select" style="width:300px">
				<option value="1" <?php selected('1', userpro_get_option('restricted_page_verified')); ?>><?php _e('Only Verified Members','userpro'); ?></option>
				<option value="0" <?php selected('0', userpro_get_option('restricted_page_verified')); ?>><?php _e('All Registered Members','userpro'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><label for=""><?php _e('Check all pages that you want to restrict content to','userpro'); ?></label></th>
		<td>
			<div class="up-admin-heightlimit" style="max-width: 300px !important;max-height: 150px !important; overflow: auto;">
			<?php
				$pages = get_posts('post_type=any&posts_per_page=-1');
				foreach ($pages as $page) {
				if ($page->post_parent == 0) {
				
					if (is_array( userpro_get_option('userpro_restricted_pages')) && in_array($page->ID,  userpro_get_option('userpro_restricted_pages') )) { $checked = ' checked="checked"'; } else { $checked = null; }
				?>
				
					<div class="upadmin-parent">
					
						<label class='userpro-checkbox'>
							<input type='checkbox' value='<?php echo $page->ID; ?>' name='userpro_restricted_pages[]' <?php echo $checked; ?> />
							<?php echo $page->post_title; ?>
						</label>
						
						<?php $sub1 = get_pages('child_of='.$page->ID.'&parent='.$page->ID); ?>
						<?php foreach($sub1 as $page) { ?>
						
							<?php if (is_array( userpro_get_option('userpro_restricted_pages')) && in_array($page->ID,  userpro_get_option('userpro_restricted_pages') )) { $checked = ' checked="checked"'; } else { $checked = null; } ?>
						
							<div class="upadmin-sub">
								<label class='userpro-checkbox'>
									<input type='checkbox' value='<?php echo $page->ID; ?>' name='userpro_restricted_pages[]' <?php echo $checked; ?> />
									<?php echo $page->post_title; ?>
								</label>
								
								<?php $sub2 = get_pages('child_of='.$page->ID); ?>
								<?php foreach($sub2 as $page) { ?>
								
									<?php if (is_array( userpro_get_option('userpro_restricted_pages')) && in_array($page->ID,  userpro_get_option('userpro_restricted_pages') )) { $checked = ' checked="checked"'; } else { $checked = null; } ?>
								
									<div class="upadmin-sub">
										<label class='userpro-checkbox'>
											<input type='checkbox' value='<?php echo $page->ID; ?>' name='userpro_restricted_pages[]' <?php echo $checked; ?> />
											<?php echo $page->post_title; ?>
										</label>
									</div>
									
								<?php } ?>
						
							</div>
							
						<?php } ?>
						
					</div>
				
				<?php
				}
				}
			?>
			</div>
		</td>
	</tr>
	
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','userpro'); ?>"  />
	<input type="submit" name="reset-options" id="reset-options" class="button" value="<?php _e('Reset Options','userpro'); ?>"  />
</p>

</form>