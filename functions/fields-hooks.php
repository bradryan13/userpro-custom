<?php

	/* Check certain value filters (printing on profile) */
	add_filter('userpro_before_value_is_displayed', 'userpro_before_value_is_displayed', 10, 3);
	function userpro_before_value_is_displayed($value, $key, $array){
		
		if ($key == 'description'){
			$value = wpautop($value); // auto-p user description
		}
		
		if ($key == 'country' && userpro_get_option('show_flag_in_profile') ) {
			$flag_name = str_replace(' ','-',$value);
			$flag_name = iconv("utf-8", "ascii//TRANSLIT//IGNORE", $flag_name);
			$value = '<img src="'.userpro_url.'img/flags/'.strtolower($flag_name).'.png" alt="" title="'.$value.'" class="userpro-flag-normal" />'.$value;
		}
		
		return $value;
	}

	/* Maybe unverify display name changes for verified accounts */
	add_filter('userpro_field_filter','userpro_warn_verified_user', 10, 2);
	function userpro_warn_verified_user($key, $user_id){
		global $userpro;
		$res = '';
		
		// add custom notice to display name
		if ($key == 'display_name') {
			if (!userpro_is_admin($user_id) && userpro_get_option('unverify_on_namechange') && $userpro->get_verified_status($user_id) == 1  && !current_user_can('manage_options') ) {
				$res .= '<div class="userpro-notice">'.sprintf(__('<strong>Warning!</strong> Your account is %s verified. If you change your display name, <em>you will lose your verification status.</em>','userpro'), userpro_get_badge('verified')).'</div>';
			}
		}
		
		return $res;
	}
	
	/* action hooks before profile is updated */
	add_action('userpro_pre_profile_update', 'userpro_unverify_verified_account', 10, 2);
	function userpro_unverify_verified_account($form, $user_id){
		global $userpro;
		
		// validate display name change
		if (!userpro_is_admin($user_id) && userpro_get_option('unverify_on_namechange') && $userpro->get_verified_status($user_id) == 1 && !current_user_can('manage_options') ) {
			if (isset($form['display_name'])){
				$old_displayname = userpro_profile_data('display_name', $user_id);
				$new_displayname = $form['display_name'];
				if ($new_displayname != $old_displayname){
					$userpro->unverify($user_id);
				}
			}	
		}
	
	}
	
	/* filter hooks before profile is updated */
	add_filter('userpro_pre_profile_update_filters', 'userpro_prevent_duplicate_display_names', 10, 2);
	function userpro_prevent_duplicate_display_names($form, $user_id){
		global $userpro;
		
		// validate display name
		if (isset($form['display_name'])){
			$form['display_name'] = $userpro->remove_denied_chars($form['display_name'], 'display_name');
			if ($userpro->display_name_exists( $form['display_name'] )){
				$user = get_userdata($user_id);
				$form['display_name'] = $user->user_login;
			}
		}
		
		return $form;
	}