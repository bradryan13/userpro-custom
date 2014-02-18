<?php

	/* get a global option */
	function userpro_sc_get_option( $option ) {
		$userpro_default_options = userpro_sc_default_options();
		$settings = get_option('userpro_sc');
		switch($option){
		
			default:
				if (isset($settings[$option])){
					return $settings[$option];
				} else {
					if (isset(  $userpro_default_options[$option] ) ) {
					return $userpro_default_options[$option];
					}
				}
				break;
	
		}
	}
	
	/* set a global option */
	function userpro_sc_set_option($option, $newvalue){
		$settings = get_option('userpro_sc');
		$settings[$option] = $newvalue;
		update_option('userpro_sc', $settings);
	}
	
	/* default options */
	function userpro_sc_default_options(){
		$array['slug_following'] = 'following';
		$array['slug_followers'] = 'followers';
		$array['activity_open_to_all'] = 1;
		$array['activity_per_page'] = 10;
		$array['excluded_post_types'] = 'nav_menu_item';
		$array['notification_on_follow'] = 1;
		$array['hide_admins'] = 0;
		return apply_filters('userpro_sc_default_options_array', $array);
	}