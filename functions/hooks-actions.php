<?php

	/* Verify an Envato purchase */
	add_action('userpro_profile_update', 'userpro_verify_envato_purchase', 10, 2);
	function userpro_verify_envato_purchase($form, $user_id){
		global $userpro;
		if (isset($form['envato_purchase_code'])){
			$code = $form['envato_purchase_code'];
			if ($userpro->verify_purchase($code)) {
				$userpro->do_envato($user_id);
			} else {
				$userpro->undo_envato($user_id);
			}
		}
	}

	/* Enqueue Scripts */
	add_action('wp_enqueue_scripts', 'userpro_enqueue_scripts');
	function userpro_enqueue_scripts(){

		if ( userpro_get_option('googlefont') && !userpro_get_option('customfont') ) {
			wp_register_style('userpro_google_font', 'http://fonts.googleapis.com/css?family='.userpro_get_option('googlefont').':400,400italic,700,700italic,300italic,300');
			wp_enqueue_style('userpro_google_font');
		}
		
		/* CSS */
		wp_register_style('userpro_min', userpro_url . 'css/userpro.min.css');
		wp_enqueue_style('userpro_min');
		
		/* JavaScript */
		wp_register_script('userpro_min', userpro_url . 'scripts/scripts.min.js', array('jquery') );
		wp_enqueue_script('userpro_min');
		
		/* Include datapicker */
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_style('userpro_jquery_ui_style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/'.userpro_get_option('jquery_ui_style').'/jquery-ui.min.css');
		
		///////////////
		$skin = userpro_get_option('skin');
		if (class_exists('userpro_sk_api') && is_dir( userpro_sk_path . 'skins/'.$skin ) ) {
			wp_register_style('userpro_skin_min', userpro_sk_url . 'skins/'.$skin.'/style.css');
			wp_enqueue_style('userpro_skin_min');
		} else {
			wp_register_style('userpro_skin_min', userpro_url . 'skins/'.$skin.'/style.css');
			wp_enqueue_style('userpro_skin_min');
		}
		if (locate_template('userpro/skins/'.$skin.'/style.css') ) {
			wp_register_style('userpro_skin_custom', get_template_directory_uri() . '/userpro/skins/'.$skin.'/style.css' );
			wp_enqueue_style('userpro_skin_custom');
		}

	}
	
	/* Hook into content: Restrict Content */
	function userpro_global_page_restrict($content){
		global $post;
		$restrict = (array)userpro_get_option('userpro_restricted_pages');
		if (isset($post->ID) && in_array($post->ID, $restrict)){
			
			if (userpro_get_option('restricted_page_verified') == 1) {
				$shortcode = '[userpro_private restrict_to_verified=1]'.$content.'[/userpro_private]';
			} else {
				$shortcode = '[userpro_private]'.$content.'[/userpro_private]';
			}
			
			// Locked page
			$content = do_shortcode($shortcode);
			
		}
		return $content;
	}
	add_action('the_content', 'userpro_global_page_restrict');

	/* Remove bar except for admins */
	add_action('init', 'userpro_remove_admin_bar');
	function userpro_remove_admin_bar() {
		if (!current_user_can('manage_options') && !is_admin()) {
			if (userpro_get_option('hide_admin_bar')) {
				show_admin_bar(false);
			}
		}
	}
	
	/* Setup redirections */
	add_action('init','userpro_redirects');
	function userpro_redirects(){
		global $pagenow;

		// redirect dashboard
		if ('index.php' == $pagenow && is_admin()) {
			if (userpro_is_logged_in() && userpro_allow_dashboard_redirect() ){
				wp_redirect( userpro_dashboard_redirect_uri() );
				exit();
			}
		}
		
		// redirect dashboard profile
		if( 'profile.php' == $pagenow ) {
			if (userpro_is_logged_in() && userpro_allow_profile_redirect() ){
				wp_redirect( userpro_profile_redirect_uri() );
				exit();
			}
		}
		
		// redirect login
		if ('wp-login.php' == $pagenow && !isset($_REQUEST['action']) ) {
			if (userpro_allow_login_redirect() ){
				if (isset($_GET['redirect_to'])){
					$url = add_query_arg('redirect_to', $_GET['redirect_to'], userpro_login_redirect_uri() );
				} else {
					$url = userpro_login_redirect_uri();
				}
				wp_redirect( $url );
				exit();
			}
		}
		
		// redirect lostpassword
		if ('wp-login.php' == $pagenow && isset($_REQUEST['action']) && $_REQUEST['action'] == 'lostpassword') {
			if (userpro_allow_login_redirect() ){
				wp_redirect( userpro_login_redirect_uri() );
				exit();
			}
		}
		
		// redirect register
		if ('wp-login.php' == $pagenow && isset($_REQUEST['action']) && $_REQUEST['action'] == 'register') {
			if (userpro_allow_register_redirect() ){
				wp_redirect( userpro_register_redirect_uri() );
				exit();
			}
		}
		
	}
	
	/**
	Clear cache on some actions
	**/
	
	add_action ('userpro_after_account_verified', "userpro_cache_clear");
	add_action ('userpro_after_account_unverified', "userpro_cache_clear");
	
	add_action('userpro_after_profile_updated_fb', 'userpro_cache_clear');
	add_action('userpro_after_profile_updated','userpro_cache_clear');
	add_action ('user_register', "userpro_cache_clear");
	add_action ('delete_user', "userpro_cache_clear");
	function userpro_cache_clear(){
		global $userpro;
		$userpro->clear_cache();
	}

	add_action( 'profile_update', 'userpro_profile_updated', 10, 2 );
	function userpro_profile_updated( $user_id, $old_user_data ) {
		global $userpro;
		$userpro->clear_cache();
	}
	
	add_action('edit_user_profile_update', 'userpro_edit_user_profile_update');
	function userpro_edit_user_profile_update($user_id) {
		global $userpro;
		$userpro->clear_cache();
	}
	
	add_action('personal_options_update', 'userpro_personal_options_update');
	function userpro_personal_options_update($user_id) {
		global $userpro;
		$userpro->clear_cache();
	}