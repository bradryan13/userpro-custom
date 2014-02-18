<?php

	/* allow dashboard redirect */
	function userpro_allow_dashboard_redirect(){
		if (!current_user_can('manage_options') && userpro_get_option('dashboard_redirect_users') )
			return true;
		return false;
	}
	
	/* allow profile redirect */
	function userpro_allow_profile_redirect(){
		if (!current_user_can('manage_options') && userpro_get_option('profile_redirect_users') )
			return true;
		return false;
	}
	
	/* allow login redirect */
	function userpro_allow_login_redirect(){
		if ( userpro_get_option('login_redirect_users') )
			return true;
		return false;
	}
	
	/* allow register redirect */
	function userpro_allow_register_redirect(){
		if ( userpro_get_option('register_redirect_users') )
			return true;
		return false;
	}
	
	/* dashboard redirect url */
	function userpro_dashboard_redirect_uri(){
		global $userpro;
		$possible = userpro_get_option('dashboard_redirect_users');
		if ($possible == 1)
			return $userpro->permalink();
		if ($possible == 2)
			return userpro_get_option('dashboard_redirect_users_url');
	}
	
	/* profile redirect url */
	function userpro_profile_redirect_uri(){
		global $userpro;
		$possible = userpro_get_option('profile_redirect_users');
		if ($possible == 1)
			return $userpro->permalink(0, 'edit');
		if ($possible == 2)
			return userpro_get_option('profile_redirect_users_url');
	}
	
	/* login redirect url */
	function userpro_login_redirect_uri(){
		global $userpro;
		$possible = userpro_get_option('login_redirect_users');
		if ($possible == 1)
			$pages = get_option('userpro_pages');
			if (!$userpro->page_exists($pages['login'])){
				userpro_set_option('login_redirect_users', 0);
				return admin_url();
			} else {
				return $userpro->permalink(0, 'login');
			}
		if ($possible == 2)
			return userpro_get_option('login_redirect_users_url');
	}
	
	/* register redirect url */
	function userpro_register_redirect_uri(){
		global $userpro;
		$possible = userpro_get_option('register_redirect_users');
		if ($possible == 1)
			return $userpro->permalink(0, 'register');
		if ($possible == 2)
			return userpro_get_option('register_redirect_users_url');
	}

	/* runs link thru any special filter */
	function userpro_link_filter($value, $key) {
		if (is_email($value)) {
			return 'mailto:'.$value;
		}
		
		if ($key == 'phone_number'){
			return 'tel:'.$value;
		}
		
		return $value;
	}
	
	/* check if passed value is URL */
	function userpro_filter_url($value, $target) {
		if(filter_var($value, FILTER_VALIDATE_URL)){
			$value = $value . '<a href="'.$value.'" target="'.$target.'"><i class="userpro-icon-external-link userpro-meta-value"></i></a>';
		} elseif (is_email($value)) {
			$value = $value . '<a href="mailto:'.$value.'"><i class="userpro-icon-envelope userpro-meta-value"></i></a>';
		}
		return $value;
	}