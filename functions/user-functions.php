<?php

	/* Find if user is admin by ID */
	function userpro_is_admin($user_id) {
		$user = get_userdata($user_id);
		if ( $user->user_level >= 10 ) {
			return true;
		}
		return false;
	}

	/* Add logout link */
	function userpro_logout_link( $user_id, $redirect='current', $logout_redirect=null ){
		global $current_user, $wp;
		get_currentuserinfo();
		if ($user_id ==  $current_user->ID ) {
			if ($redirect == 'current' || !$redirect){
				$url = get_permalink();
			} else {
				$url = $redirect;
			}
			if ($logout_redirect){
				$url = $logout_redirect;
			}
			?>
			<a class="userpro-small-link" href="<?php echo wp_logout_url( $url ); ?>" title="<?php _e('Logout','userpro'); ?>"><?php _e('Logout','userpro'); ?></a>
			<?php
		}
	}
	
	/* Get logout url */
	function userpro_logout_url ($user_id, $redirect='current', $logout_redirect=null) {
		global $current_user, $wp;
		get_currentuserinfo();
		if ($user_id ==  $current_user->ID ) {
			if ($redirect == 'current' || !$redirect){
				$url = get_permalink();
			} else {
				$url = $redirect;
			}
			if ($logout_redirect){
				$url = $logout_redirect;
			}
			return wp_logout_url( $url );
		}
	}
	
	/* Add a class to open profile via popup */
	function userpro_user_via_popup($args){
		if ($args['memberlist_popup_view'])
			echo 'popup-view';
	}

	/* Find if a user cannot edit field */
	function userpro_user_cannot_edit($array){
		global $current_user;
		if (isset($array['locked']) && $array['locked']==1 && !current_user_can('manage_options') )
			return true;
		return false;
	}
	
	/* Privacy of fields */
	function userpro_private_field_class($array){
		global $current_user;
		if (isset($array['private']) && $array['private']==1 && !current_user_can('manage_options') )
			return 'userpro-field-private';
		return '';
	}

	/* Auto login user */
	function userpro_auto_login( $username, $remember=true ) {
		ob_start();
		if ( !is_user_logged_in() ) {
			$user = get_user_by('login', $username );
			$user_id = $user->ID;
			wp_set_current_user( $user_id, $username );
			wp_set_auth_cookie( $user_id, $remember );
			do_action( 'wp_login', $username );
		} else {
			wp_logout();
			$user = get_user_by('login', $username );
			$user_id = $user->ID;
			wp_set_current_user( $user_id, $username );
			wp_set_auth_cookie( $user_id, $remember );
			do_action( 'wp_login', $username );
		}
		ob_end_clean();
	}
	
	/* Can edit user profile */
	function userpro_can_edit_user( $user_id ){
		if ( current_user_can('manage_options') || $user_id == get_current_user_id() )
			return true;
		return false;
	}
	
	/* Can delete user profile */
	function userpro_can_delete_user( $user_id ){
		if (userpro_get_option('user_can_delete_profile')) {
			if ( current_user_can('manage_options') || $user_id == get_current_user_id() ) {
				return true;
			}
		}
		return false;
	}
	
	/* Get user id to edit */
	function userpro_get_edit_user(){
		global $userpro;
		if (userpro_is_logged_in()) {
			if ( get_query_var('up_username') ) {
				$user = $userpro->get_member_by( get_query_var('up_username') );
				if ($user->ID && current_user_can('manage_options') || $user->ID == get_current_user_id() ) {
					$user_id = $user->ID;
				} elseif ( !$user->ID && current_user_can('manage_options') ) {
					$user_id = 'not_found';
				} elseif ( $user->ID && !current_user_can('manage_options') ) {
					$user_id = 'not_authorized';
				}
			} else {
				global $current_user;
				get_currentuserinfo();
				$user_id = $current_user->ID;
			}
			return $user_id;
		}
	}

	/* Get user id to view */
	function userpro_get_view_user( $arg=null, $force=0 ){
		global $userpro;
		$user_id =0;
		if ( $arg ) {
			$user = $userpro->get_member_by( $arg, $force );
			if ($user->ID || ( $user->ID > 0 && $user->ID == get_current_user_id() ) ) {
				$temp_user = $user->ID;
			} elseif ( !$user->ID ) {
				$temp_user = 'not_found';
			}
		}
		if (userpro_is_logged_in()) {
			if ( $arg ) {
				if ( !$userpro->can_view_profile( $arg ) ){
					$user_id = 'not_authorized';
				} else {
					$user_id = $temp_user;
				}
			} else {
				global $current_user;
				get_currentuserinfo();
				$user_id = $current_user->ID;
			}
		} elseif ( $arg ) {
			if ( !$userpro->can_view_profile() ){
				$user_id = 'not_authorized';
			} elseif (userpro_get_option('allow_guests_view_profiles')){
				$user_id = $temp_user;
			} else {
				$user_id = 'login_to_view_others';
			}
		} else {
			/* show him the login form */
			$user_id = 'login_to_view';
		}
		return $user_id;
	}

	/* Checks if a user is logged in */
	function userpro_is_logged_in(){
		if (is_user_logged_in())
			return true;
		return false;
	}
	
	/* Get a profile data for user id */
	function userpro_profile_data( $field, $user_id ) {
		global $userpro;
		$user = get_userdata( $user_id );
		$output = '';
		if ($user != false) {
			switch($field){
				default:
					$output = get_user_meta( $user_id, $field, true );
					break;
				case 'display_name':
					$output = $user->display_name;
					if (userpro_get_option('user_display_name') == 'name') $output = $userpro->get_full_name($user_id);
					if (userpro_get_option('user_display_name_key')) $output = userpro_profile_data( userpro_get_option('user_display_name_key'), $user_id);
					break;
				case 'user_url':
					$output = $user->user_url;
					break;
				case 'user_email':
					$output = $user->user_email;
					break;
				case 'user_login':
					$output = $user->user_login;
					break;
				case 'role':
					$user_roles = $user->roles;
					$user_role = array_shift($user_roles);
					$output = $user_role;
					break;
			}
		}
		return $output;
	}
	
	/* nicer user role */
	function userpro_user_role($role){
		global $wp_roles;
		$roles = $wp_roles->get_names();
		return $roles[$role];
	}
	
	/* Get nice name of profile field value */
	function userpro_profile_data_nicename($field, $value) {
		$output = '';
		$get_fields = get_option('userpro_fields');
		if (is_array($value)){
			foreach($value as $s=>$l){
				$output[] = $l;
			}
			return implode(', ', $output);
		} else {
			if (isset($get_fields[$field]['options'][$value])){
			return $get_fields[$field]['options'][$value];
			} else {
			return $value;
			}
		}
	}
	
	/* HTML returned values */
	function userpro_profile_nohtml( $value ) {
		return wp_strip_all_tags($value);
	}
	
	/* default hidden state for field */
	function userpro_field_default_hidden( $key, $template, $group){
		$groups = get_option('userpro_fields_groups');
		if (isset( $groups[$template][$group][$key]['hidden'] ) ) {
			$ret = $groups[$template][$group][$key]['hidden'];
			if ($ret == 1) {
				return true;
			}
		}
		return false;
	}
	
	/* no args field is viewable */
	function userpro_field_is_viewable_noargs($key, $user_id) {
		if (current_user_can('manage_options'))
			return true;
			
		if ($user_id == get_current_user_id())
			return true;
			
		$test = get_user_meta($user_id, 'hide_'.$key, true);
		if ($test == 1 && $user_id != get_current_user_id() )
			return false;
			
		return true;
	}
	
	/* Check if field can be viewed */
	function userpro_field_is_viewable( $key, $user_id, $args ) {

		if (current_user_can('manage_options'))
			return true;
			
		if ($user_id == get_current_user_id())
			return true;
		
		if ( isset( $args[ $args['template'] . '_group'] ) && userpro_field_default_hidden($key, $args['template'], $args[ $args['template'] . '_group']))
			return false;
			
		$test = get_user_meta($user_id, 'hide_'.$key, true);
		if ($test == 1 && $user_id != get_current_user_id() )
			return false;
			
		return true;
	}
	
	/* Update user profile from google */
	function userpro_update_profile_via_google($user_id, $array) {
		global $userpro;
		extract($array);

		if ( userpro_is_logged_in() && ( $user_id != get_current_user_id() ) && !current_user_can('manage_options') )
			die();
			
		if ($id) { update_user_meta($user_id, 'userpro_google_id', $id); }
		
		/* begin display name */
		if ($displayName) {
			$display_name = $displayName;
		} else if (isset($name) && is_array($name)){
			$display_name = $name['givenName'] . ' ' . $name['familyName'];
		} else {
			$display_name = $email;
		}
		
		if ($display_name) {
			if ($userpro->display_name_exists( $display_name )){
				$display_name = $userpro->unique_display_name($display_name);
			}
			$display_name = $userpro->remove_denied_chars($display_name);
			wp_update_user( array('ID' => $user_id, 'display_name' => $display_name ) );
			update_user_meta($user_id, 'display_name', $display_name);
		}
		/* end display name */
			
		if ($url) {
			update_user_meta($user_id, 'google_plus', $url );
		}
		
		if ($email) {
			wp_update_user( array('ID' => $user_id, 'user_email' => $email ) );
			update_user_meta($user_id, 'user_email', $email );
		}
		
		if ($gender) {
			update_user_meta($user_id, 'gender', ucfirst($gender) );
		}
		
		if (isset($name) && is_array($name)) {
			update_user_meta($user_id, 'first_name', $name['givenName']);
			update_user_meta($user_id, 'last_name', $name['familyName']);
		}
		
		do_action('userpro_after_profile_updated_google');
		
	}
	
	/* Update user profile from twitter */
	function userpro_update_profile_via_twitter($user_id, $array) {
		global $userpro;
		extract($array);

		if ( userpro_is_logged_in() && ( $user_id != get_current_user_id() ) && !current_user_can('manage_options') )
			die();
			
		if ($id) { update_user_meta($user_id, 'twitter_oauth_id', $id); }
		
		if ($screen_name) {
			update_user_meta($user_id, 'twitter', 'http://twitter.com/'.$screen_name);
		}
		
		/* begin display name */
		if ($screen_name) {
			$display_name = $screen_name;
		}
		
		if ($display_name) {
			if ($userpro->display_name_exists( $display_name )){
				$display_name = $userpro->unique_display_name($display_name);
			}
			$display_name = $userpro->remove_denied_chars($display_name);
			wp_update_user( array('ID' => $user_id, 'display_name' => $display_name ) );
			update_user_meta($user_id, 'display_name', $display_name);
		}
		/* end display name */
		
		if ($location) {
			update_user_meta($user_id, 'country', $location);
		}
		
		if ($url) {
			wp_update_user( array('ID' => $user_id, 'user_url' => $url ) );
			update_user_meta($user_id, 'user_url', $url);
		}
		
		if ($description) {
			update_user_meta($user_id, 'description', $description);
		}
		
		do_action('userpro_after_profile_updated_twitter');
		
	}
	
	/* Update user profile from facebook */
	function userpro_update_profile_via_facebook($user_id, $array) {
		global $userpro;
		extract($array);
		
		if ( userpro_is_logged_in() && ( $user_id != get_current_user_id() ) && !current_user_can('manage_options') )
			die();
		
		if ($id && $id != 'undefined') { update_user_meta($user_id, 'userpro_facebook_id', $id); }
		
		if ($first_name && $first_name != 'undefined'){ update_user_meta($user_id, 'first_name', $first_name); }
		if ($last_name && $last_name != 'undefined') { update_user_meta($user_id, 'last_name', $last_name); }
		
		if ($gender && $gender != 'undefined') { update_user_meta($user_id, 'gender', $gender); }
		
		if ($link && $link != 'undefined') { update_user_meta($user_id, 'facebook', $link); }
		
		/* begin display name */
		if ($name && $name != 'undefined') {
			$display_name = $name;
		} else if ($first_name && $last_name) {
			$display_name = $first_name . ' ' . $last_name;
		} else if ($email) {
			$display_name = $email;
		}
		
		if ($display_name) {
			if ($userpro->display_name_exists( $display_name )){
				$display_name = $userpro->unique_display_name($display_name);
			}
			$display_name = $userpro->remove_denied_chars($display_name);
			wp_update_user( array('ID' => $user_id, 'display_name' => $display_name ) );
			update_user_meta($user_id, 'display_name', $display_name);
		}
		/* end display name */
		
		do_action('userpro_after_profile_updated_fb');
		
	}
	
	/* Update user profile data */
	function userpro_update_user_profile($user_id, $form, $action=null) {
		global $userpro;
		extract($form);

		if ($action == 'new_user' && !$userpro->user_exists($user_id) )
			die();
			
		if (!$userpro->user_exists($user_id))
			die();
		
		if ( $action == 'ajax_save' && $user_id != get_current_user_id() && !current_user_can('manage_options') )
			die();
			
		if (!$template) die();
		
		/* hooks before saving profile fields */
		do_action('userpro_pre_profile_update', $form, $user_id);
		$form = apply_filters('userpro_pre_profile_update_filters', $form, $user_id);
		
		$fields = userpro_fields_group_by_template( $template, $group );
		foreach($form as $key => $form_value) {
			
			/* hidden from public */
			if (!isset($form["hide_$key"])) {
				update_user_meta( $user_id, 'hide_'.$key, 0 );
			} elseif (isset($form["hide_$key"])){
				update_user_meta( $user_id, 'hide_'.$key, 1 );
			}
			
			/* UPDATE PRIMARY META */
			if ( isset($key) && in_array($key, array('user_url', 'display_name', 'role', 'user_login', 'user_pass', 'user_pass_confirm', 'user_email')) ) {
				
				/* Save passwords */
				if ($key == 'user_pass') {
					if (!empty($form_value)) {
						wp_update_user( array ( 'ID' => $user_id, $key => $form_value ) ) ;
					}
				} else {
					wp_update_user( array ( 'ID' => $user_id, $key => $form_value ) ) ;
				}
				
			}
			
			/* UPDATE USER META TABLE */
			if (isset($key) && !strstr($key, 'pass')){
				update_user_meta( $user_id, $key, $form_value );
			} else {
				delete_user_meta( $user_id, $key );
			}
			
			/* move user pics to his folder */
			if ( ( isset($fields[$key]['type']) && $fields[$key]['type'] == 'picture' || isset($fields[$key]['type']) && $fields[$key]['type'] == 'file'  ) && isset($form_value) && !empty($form_value) ) {
			
				$userpro->do_uploads_dir( $user_id );
				
				if ( file_exists( $userpro->get_uploads_dir() . basename( userpro_profile_data( $key, $user_id ) ) ) ) {
					rename( $userpro->get_uploads_dir() . basename( userpro_profile_data( $key, $user_id ) ),  $userpro->get_uploads_dir($user_id) . basename( userpro_profile_data( $key, $user_id ) ) );
					update_user_meta($user_id, $key, $userpro->get_uploads_url($user_id) . basename( userpro_profile_data( $key, $user_id ) ) );
				}
				
			}
			
			/* MailChimp Integration */
			if ( ( isset($fields[$key]['type']) && $fields[$key]['type'] == 'mailchimp') ) {
				if ($form[$key] == 'unsubscribed'){
				$userpro->mailchimp_subscribe( $user_id, $fields[$key]['list_id'] );
				} elseif ($form[$key] == 'subscribed') {
				$userpro->mailchimp_unsubscribe( $user_id, $fields[$key]['list_id'] );
				}
			}
			
		}
		
		/* do action while updating profile (use $form) */
		do_action('userpro_profile_update', $form, $user_id);
		
		/* after profile update no args */
		do_action('userpro_after_profile_updated');
				
	}