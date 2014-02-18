<?php

	add_action('wp_head','userpro_ajax_url');
	function userpro_ajax_url() { ?>
		<script type="text/javascript">
		var userpro_ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
		</script>
	<?php
	}
	
	add_action('wp_head','userpro_upload_url');
	function userpro_upload_url() { ?>
		<script type="text/javascript">
		var userpro_upload_url = '<?php echo userpro_url . 'lib/fileupload/fileupload.php'; ?>';
		</script>
	<?php
	}
	
	/* Process a form */
	add_action('wp_ajax_nopriv_userpro_process_form', 'userpro_process_form');
	add_action('wp_ajax_userpro_process_form', 'userpro_process_form');
	function userpro_process_form(){
		global $userpro;
		
		if ( !isset($_POST['_myuserpro_nonce']) ||
			!wp_verify_nonce($_POST['_myuserpro_nonce'], '_myuserpro_nonce_'.$_POST['template'].'_'.$_POST['unique_id'] ) ) {
		   die();
		}
		
		if (!isset($_POST) || $_POST['action'] != 'userpro_process_form')
			die();
			
		if ( !userpro_is_logged_in() && $_POST['template'] == 'edit')
			die();
		
		extract($_POST);
		foreach($_POST as $key=>$val) {
			$key = explode('-',$key);
			$key = $key[0];
			$form[$key] = $val;
		} extract($form);
		
		/* form action */
		switch($template) {
		
			/* publish */
			case 'publish':
				$output['error'] = '';
				
				if (!$post_title) {
					$output['error']['post_title'] = __('You must enter a post title.','userpro');
				}
				
				if (!$userpro_editor){
					$output['error']['userpro_editor'] = __('You must enter some content.','userpro');
				}

				/*
					publish post
				*/
				if ( empty($output['error']) ) {
					$array = array(
						'post_title'		=> $post_title,
						'post_content'		=> @wp_kses($userpro_editor),
						'post_author'		=> $user_id
					);
					if ($post_type){ $array['post_type'] = $post_type; }

					if (userpro_is_admin($user_id)){
					
						$array['post_status'] = 'publish';
						$post_id = wp_insert_post( $array );

						$output['custom_message'] = '<div class="userpro-message userpro-message-ajax"><p>'.sprintf(__('Your post has been published. You can view it %s.','userpro'), '<a href="'.get_permalink($post_id).'">here</a>').'</p></div>';
						
					} else { // under review
					
						$array['post_status'] = 'pending';
						$post_id = wp_insert_post( $array );
						
						$output['custom_message'] = '<div class="userpro-message userpro-message-ajax"><p>'.__('Your post has been sent for review. It will be checked by our staff.','userpro').'</p></div>';
						
					}
					
					/*
						empty category first
					*/
					wp_set_object_terms( $post_id, NULL, 'category' );
					
					/*
						taxonomy
						and category
					*/
					if (isset($taxonomy) && isset($category)){
						$categories = explode(',',$category);
						if (is_array($categories)){
							foreach($categories as $cat){
								if (is_numeric($cat)){
								$cat = (int)$cat;
								}
								$cats[] = $cat;
							}
							wp_set_object_terms( $post_id, $cats, $taxonomy );
						} else {
							if (is_numeric($categories)){
								$categories = (int)$categories;
							}
							wp_set_object_terms( $post_id, $categories, $taxonomy );
						}
					}
					
					/*
						multiple taxonomy
						category insertion
					*/
					if (isset($post_categories)){
						$i = 0;
						foreach($post_categories as $cat){
							$i++;
							$split = explode('#',$cat);
							$tax = $split[1];
							$id = $split[0];
							$terms[$tax][] = $id;
						}
						if (is_array($terms)){
							foreach($terms as $k => $arr){
								wp_set_object_terms( $post_id, $terms[$k], $k, true );
							}
						}
					}
					
					/*
						assign featured
						image for post
					*/
					if ($post_featured_image){
						$attach_id = $userpro->new_attachment($post_id, $post_featured_image);
						$userpro->set_thumbnail($post_id, $attach_id);
					}

				}
				
				break;
				
			/* delete profile */
			case 'delete':
				$output['error'] = '';
				
				$user = get_userdata($user_id);
				
				$user_roles = $user->roles;
				$user_role = array_shift($user_roles);
				
				if (!$confirmdelete){
				$output['error']['confirmdelete'] = __('Nothing was deleted. You must choose yes to confirm deletion.','userpro');
				} elseif ( $user_role == 'administrator' ){
				$output['error']['confirmdelete'] = __('For security reasons, admin accounts cannot be deleted.','userpro');
				} elseif ($user->user_login == 'test') {
				$output['error']['confirmdelete'] = __('You cannot remove test accounts from frontend!','userpro');
				} else {
				
					require_once(ABSPATH.'wp-admin/includes/user.php' );
					userpro_mail($user_id, 'accountdeleted');
					
					// Delete user
					if ( is_multisite()  ) {
						
						// Multisite: Deletes user's Posts and Links, then deletes from WP Users|Usermeta
						// ONLY IF "Delete From Network" setting checked and user only belongs to this blog	
						wpmu_delete_user( $user_id );
						
					} else {
						
						// Deletes user's Posts and Links
						// Multisite: Removes user from current blog
						// Not Multisite: Deletes user from WP Users|Usermeta	
						wp_delete_user( $user_id );
						
					}
					
					$output['custom_message'] = '<div class="userpro-message userpro-message-ajax"><p>'.__('This account has been deleted successfully.','userpro').'</p></div>';
					$output['redirect_uri'] = home_url();
					
				}
				
				break;
		
			/* change pass */
			case 'change':
				$output['error'] = '';
				
				if (!$secretkey){
					$output['error']['secretkey'] = __('You did not provide a secret key.','userpro');
				} elseif (strlen($secretkey) != 20) {
					$output['error']['secretkey'] = __('The secret key you entered is invalid.','userpro');
				}
				
				/* Form validation */
				/* Here you can process custom "errors" before proceeding */
				$output['error'] = apply_filters('userpro_form_validation', $output['error'], $form);
				
				if (empty($output['error'])) {
					
					$users = get_users(array(
						'meta_key'     => 'userpro_secret_key',
						'meta_value'   => $secretkey,
						'meta_compare' => '=',
					));
					
					if (!$users[0]) {
						$output['error']['secretkey'] = __('The secret key is invalid or expired.','userpro');
					} else {
						
						$user_id = $users[0]->ID;
						wp_update_user( array( 'ID' => $user_id, 'user_pass' => $user_pass ) );
						delete_user_meta($user_id, 'userpro_secret_key');
						
						add_action('userpro_pre_form_message', 'userpro_msg_login_after_passchange');
						$shortcode = stripslashes($shortcode);
						$modded = str_replace('template="change"','template="login"', $shortcode);
						$output['template'] = do_shortcode( $modded );
						
					}
				}
				
				break;
		
			/* send secret key */
			case 'reset':
				$output['error'] = '';
				
				if (!$username_or_email){
					$output['error']['username_or_email'] = __('You should provide your email or username.','userpro');
				} else {
				
					if (is_email($username_or_email)) {
						$user = get_user_by_email($username_or_email);
						$username_or_email = $user->user_login;
					}
				
					if (!username_exists($username_or_email)){
						$output['error']['username_or_email'] = __('There is not such user in our system.','userpro');
					} elseif ( !$userpro->can_reset_pass( $username_or_email ) ) {
						$output['error']['username_or_email'] = __('Resetting admin password is not permitted!','userpro');
					}
					
				}
				
				/* Form validation */
				/* Here you can process custom "errors" before proceeding */
				$output['error'] = apply_filters('userpro_form_validation', $output['error'], $form);
				
				/* email user with secret key and update
					his user meta */
				if (empty($output['error'])) {

					$user = get_user_by('login', $username_or_email);
					$uniquekey =  wp_generate_password(20, $include_standard_special_chars=false);
					
					update_user_meta( $user->ID, 'userpro_secret_key', $uniquekey);
					userpro_mail($user->ID, 'secretkey', $uniquekey);
					
					add_action('userpro_pre_form_message', 'userpro_msg_secret_key_sent');
					$shortcode = stripslashes($shortcode);
					$modded = str_replace('template="reset"','template="change"', $shortcode);
					$output['template'] = do_shortcode( $modded );
				
				}
				
				break;
				
			/* login */
			case 'login':
				
				$output['error'] = '';
				if (!$username_or_email){
					$output['error']['username_or_email'] = __('You should provide your email or username.','userpro');
				}
				if (!$user_pass){
					$output['error']['user_pass'] = __('You should provide your password.','userpro');
				}
				
				if (email_exists($username_or_email)) {
					$user = get_user_by('email', $username_or_email);
					$username_or_email = $user->user_login;
				}
				
				/* Form validation */
				/* Here you can process custom "errors" before proceeding */
				$output['error'] = apply_filters('userpro_login_validation', $output['error'], $form);
				
				if (empty($output['error']) && $username_or_email && $user_pass) {
				
				$creds = array();
				$creds['user_login'] = $username_or_email;
				$creds['user_password'] = $user_pass;
				$creds['remember'] = true;
				$user = wp_signon( $creds, false );
				if ( is_wp_error($user) ) {
					if ( $user->get_error_code() == 'invalid_username') {
					$output['error']['username_or_email'] = __('Invalid email or username entered','userpro');
					} elseif ( $user->get_error_code() == 'incorrect_password') {
					$output['error']['user_pass'] = __('The password you entered is incorrect','userpro');
					}
				} else {
					
					/* check the account is active first */
					if ($userpro->is_pending( $user->ID )) {

						if (userpro_get_option('users_approve') === '2') {
							$output['custom_message'] = '<div class="userpro-message userpro-message-ajax"><p>'.__('Your email is pending verification. Please activate your account.','userpro').'</p></div>';
						} else {
							$output['custom_message'] = '<div class="userpro-message userpro-message-ajax"><p>'.__('Your account is currently being reviewed. Thanks for your patience.','userpro').'</p></div>';
						}
						wp_logout();
							
					} else {
				
					/* a good login */
					userpro_auto_login( $user->user_login, true );
										
					if (isset($force_redirect_uri) && !empty($force_redirect_uri) ) {
					
						$output['redirect_uri'] = 'refresh';
						
					} else {
					
						if (current_user_can('manage_options') && userpro_get_option('show_admin_after_login') ) {
							$output['redirect_uri'] = admin_url();
						} else {
						
							if (isset($redirect_uri) && !empty($redirect_uri) ) {
								$output['redirect_uri'] = $redirect_uri;
							} else {
								if (userpro_get_option('after_login') == 'no_redirect'){
									$output['redirect_uri'] = 'refresh';
								}
								if (userpro_get_option('after_login') == 'profile'){
									$output['redirect_uri'] = $userpro->permalink();
								}
							}
						
						}
						
						/* hook the redirect URI */
						$output['redirect_uri'] = apply_filters('userpro_login_redirect', $output['redirect_uri']);

					}
					
						/* super redirection */
						if (isset($global_redirect)){
							$output['redirect_uri'] = $global_redirect;
						}
					
					} // active/pending
					
				}
				
				}
				
				break;
		
			/* editing */
			case 'edit':
			
				if ($user_id != get_current_user_id() && !current_user_can('manage_options') )
					die();
			
				userpro_update_user_profile( $user_id, $form, $action='ajax_save' );
				if (userpro_get_option('notify_admin_profile_save') && !current_user_can('manage_options') ){
					userpro_mail( $user_id , 'profileupdate', null, $form );
				}
				
				add_action('userpro_pre_form_message', 'userpro_msg_profile_saved');
				
				if ($_POST['up_username']){
				set_query_var('up_username',  $_POST['up_username'] );
				}

				$shortcode = stripslashes($shortcode);
				$modded = $shortcode;
				$output['template'] = do_shortcode( $modded );
				
				break;
		
			/* registering */
			case 'register':
			
				$output['error'] = '';
				
				/* Form validation */
				/* Here you can process custom "errors" before proceeding */
				$output['error'] = apply_filters('userpro_register_validation', $output['error'], $form);
			
				if ( empty($output['error']) && ( 
				
					(isset($user_login) && isset($user_email) && isset($user_pass) ) || 
					(isset($user_login) && isset($user_email) ) ||
					(isset($user_email))
				
				) ) {
				
				if (isset($user_login) ) {
					$user_exists = username_exists( $user_login );
				} else {
					$user_exists = username_exists( 'the_cow_that_did_run_after_the_elephant' );
					$user_login = $user_email;
				}
				
				if ( !isset($user_exists) and email_exists($user_email) == false ) {
					
					if (!isset($user_pass)) $user_pass = wp_generate_password( $length=12, $include_standard_special_chars=false );
					
					/* not auto approved? */
					if ( userpro_get_option('users_approve') !== '1') {
					
						/* require email validation */
						if (userpro_get_option('users_approve') === '2') {
						
							$user_id = $userpro->new_user( $user_login, $user_pass, $user_email, $form, $type='standard', $approved=0 );
							$userpro->pending_email_approve( $user_id, $user_pass, $form );
							
							add_action('userpro_pre_form_message', 'userpro_msg_activate_pending');
							$shortcode = stripslashes($shortcode);
							$modded = str_replace('template="register"','template="login"', $shortcode);
							$output['template'] = do_shortcode( $modded );
							
						}
						
						/* require admin validation */
						if (userpro_get_option('users_approve') === '3') {
						
							$user_id = $userpro->new_user( $user_login, $user_pass, $user_email, $form, $type='standard', $approved=0 );
							$userpro->pending_admin_approve( $user_id, $user_pass, $form );
							
							add_action('userpro_pre_form_message', 'userpro_msg_activate_pending_admin');
							$shortcode = stripslashes($shortcode);
							$modded = str_replace('template="register"','template="login"', $shortcode);
							$output['template'] = do_shortcode( $modded );
							
						}
					
					} else {
					
						$user_id = $userpro->new_user( $user_login, $user_pass, $user_email, $form, $type='standard' );

						/* auto login */
						if (userpro_get_option('after_register_autologin')) {
												
							$creds = array();
							$creds['user_login'] = $user_login;
							$creds['user_password'] = $user_pass;
							$creds['remember'] = true;
							$user = wp_signon( $creds, false );
							
							if (isset($user->user_login)){
								
								userpro_auto_login( $user->user_login, true );
							
							}
							
							if ($redirect_uri) {
								$output['redirect_uri'] = $redirect_uri;
							} else {
								if (userpro_get_option('after_register') == 'no_redirect'){
									$output['redirect_uri'] = 'refresh';
								}
								if (userpro_get_option('after_register') == 'profile'){
									$output['redirect_uri'] = $userpro->permalink();
								}
							}
							
							/* hook the redirect URI */
							$output['redirect_uri'] = apply_filters('userpro_register_redirect', $output['redirect_uri']);
						
						/* manual login form */
						} else {
						
							add_action('userpro_pre_form_message', 'userpro_msg_login_after_reg');
							$shortcode = stripslashes($shortcode);
							$modded = str_replace('template="register"','template="login"', $shortcode);
							$output['template'] = do_shortcode( $modded );
						
						}
					
					}
				
				}
				
				}
				
				break;
				
		}
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* Side validate input */
	add_action('wp_ajax_nopriv_userpro_side_validate', 'userpro_side_validate');
	add_action('wp_ajax_userpro_side_validate', 'userpro_side_validate');
	function userpro_side_validate(){
		global $userpro;
		
		if ( $_POST['action'] != 'userpro_side_validate')
			die();
			
		extract($_POST);
		$output['error'] = '';
		switch($ajaxcheck) {
		
			case 'envato_purchase_code':
				if ( !$userpro->verify_purchase($input_value) ) {
						$output['error'] = __('Invalid purchase code or Envato API is down.','userpro');
				} else {
					$output['error'] = '';
				}
				break;
		
			case 'display_name_exists':
				if ($userpro->display_name_exists($input_value)) {
					$output['error'] = __('The display name is already in use.','userpro');
				}
				break;
			
			case 'username_exists':
				if (username_exists($input_value)){
					$output['error'] = __('Username already taken.','userpro');
				} else if ( !preg_match("/^[A-Za-z0-9_]+$/", $input_value) ) {
					$output['error'] = __('Illegal characters are not allowed in username.','userpro');
				}
				break;
			
			case 'email_exists':
				if (!is_email($input_value)) {
					$output['error'] = __('Please enter a valid email.','userpro');
				} else if (email_exists($input_value)) {
					$output['error'] = __('Email is taken. Is that you? Try to <a href="#" data-template="login">login</a>','userpro');
				}
				break;
			
			case 'validatesecretkey':
				if (strlen($input_value) != 20) {
					$output['error'] = __('The secret key you entered is invalid.','userpro');
				} else {
					$users = get_users(array(
						'meta_key'     => 'userpro_secret_key',
						'meta_value'   => $input_value,
						'meta_compare' => '=',
					));
					if (!$users[0]) {
						$output['error'] = __('The secret key is invalid or expired.','userpro');
					}
				}
				break;
				
		}
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* Crop user image upload */
	add_action('wp_ajax_nopriv_userpro_crop_picupload', 'userpro_crop_picupload');
	add_action('wp_ajax_userpro_crop_picupload', 'userpro_crop_picupload');
	function userpro_crop_picupload(){
		if (!isset($_POST['src'])) die();
		
		extract($_POST);
		
		if ($filetype == 'picture') {
		$crop = userpro_url . "lib/timthumb.php?src=$src&w=$width&h=$height&a=c&amp;q=100";
		if (!$width) $crop = $src;
		$output['response'] = $crop;
		}
		
		if ($filetype == 'file'){
		$output['response'] = '<div class="userpro-file-input"><a href="'.$src.'" '.userpro_file_type_icon($src).'>'.basename( $src ).'</a></div>';
		}
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* save user data form */
	add_action('wp_ajax_nopriv_userpro_save_userdata', 'userpro_save_userdata');
	add_action('wp_ajax_userpro_save_userdata', 'userpro_save_userdata');
	function userpro_save_userdata(){
		global $userpro;
		extract($_POST);
		
		if (!isset($_POST) || $_POST['action'] != 'userpro_save_userdata' || ( $user_id != get_current_user_id() && !current_user_can('manage_options') ) )
			die();
			
		$output = '';
		
		$userpro->set($field, $value, $user_id);
		
		if ( $userpro->get($field, $user_id) ) {
			$output['res'] = $userpro->get($field, $user_id);
		} else {
			$output['res'] = __('No custom notice is set for this account.','userpro');
		}
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* Get shortcode template */
	add_action('wp_ajax_nopriv_userpro_shortcode_template', 'userpro_shortcode_template');
	add_action('wp_ajax_userpro_shortcode_template', 'userpro_shortcode_template');
	function userpro_shortcode_template(){
		global $wp, $wp_query;
		extract($_POST);
		
		ob_start();
		
		if (isset($_POST['up_username'])){
		set_query_var('up_username',  $_POST['up_username'] );
		}

		echo do_shortcode( stripslashes( $shortcode ) );
		$output['response'] = ob_get_contents();
		
		ob_end_clean();
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}
	
	/* Facebook Connect */
	add_action('wp_ajax_nopriv_userpro_fbconnect', 'userpro_fbconnect');
	add_action('wp_ajax_userpro_fbconnect', 'userpro_fbconnect');
	function userpro_fbconnect(){
		global $userpro;
		$output = '';
		
		if (!isset($_POST)) die();
		if ($_POST['action'] != 'userpro_fbconnect') die();
		
		if (!isset($_POST['id'])) die();
		
		extract($_POST);
	
		if (!isset($username) || $username == '' || $username == 'undefined') $username = $email;
		
		/* Check if facebook uid exists */
		if (isset($id) && $id != '' && $id != 'undefined'){
			$users = get_users(array(
				'meta_key'     => 'userpro_facebook_id',
				'meta_value'   => $id,
				'meta_compare' => '='
			));
			if (isset($users[0]->ID) && is_numeric($users[0]->ID) ){
				$returning = $users[0]->ID;
				$returning_user_login = $users[0]->user_login;
			} else {
				$returning = '';
			}
		} else {
			$returning = '';
		}
		
		/* If facebook uid exists */
		if ( $returning != '' ) {
				
				userpro_auto_login( $returning_user_login, true );
				
				if ($redirect == '') {
				$output['redirect_uri'] = 'refresh';
				} elseif ($redirect != 'profile') {
				$output['redirect_uri'] = $redirect;
				} else {
				$output['redirect_uri'] = $userpro->permalink();
				}
				$output['redirect_uri'] = apply_filters('userpro_login_redirect', $output['redirect_uri']);
			
		/* Email is same, connect them together */
		} else if ( $email != '' && email_exists($email)) {
		
				$user_id = email_exists($email);
				$user = get_userdata($user_id);
				
				userpro_auto_login( $user->user_login, true );
				$userpro->update_fb_id($user_id, $id);
				
				if ($redirect == '') {
				$output['redirect_uri'] = 'refresh';
				} elseif ($redirect != 'profile') {
				$output['redirect_uri'] = $redirect;
				} else {
				$output['redirect_uri'] = $userpro->permalink();
				}
				$output['redirect_uri'] = apply_filters('userpro_login_redirect', $output['redirect_uri']);
		
		/* This user already exists! connect them together */
		} else if ($username != '' && username_exists($username)) {
		
				$user_id = username_exists($username);
				$user = get_userdata($user_id);
				
				userpro_auto_login( $user->user_login, true );
				$userpro->update_fb_id($user_id, $id);
				
				if ($redirect == '') {
				$output['redirect_uri'] = 'refresh';
				} elseif ($redirect != 'profile') {
				$output['redirect_uri'] = $redirect;
				} else {
				$output['redirect_uri'] = $userpro->permalink();
				}
				$output['redirect_uri'] = apply_filters('userpro_login_redirect', $output['redirect_uri']);
		
		/* FBID not found, email/user not found - fresh user */
		} else {

				$user_pass = wp_generate_password( $length=12, $include_standard_special_chars=false );
				$user_id = $userpro->new_user( $username, $user_pass, $email, $_POST, $type='facebook' );
				userpro_auto_login( $username, true );

				if ($redirect == '') {
				$output['redirect_uri'] = 'refresh';
				} elseif ($redirect != 'profile') {
				$output['redirect_uri'] = $redirect;
				} else {
				$output['redirect_uri'] = $userpro->permalink();
				}
				$output['redirect_uri'] = apply_filters('userpro_register_redirect', $output['redirect_uri']);
			
		}
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}