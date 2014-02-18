<?php
/*
Plugin Name: UserPro
Plugin URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
Description: The ultimate user profiles and memberships plugin for WordPress.
Version: 1.0.67
Author: Deluxe Themes
Author URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
*/

define('userpro_url',plugin_dir_url(__FILE__ ));
define('userpro_path',plugin_dir_path(__FILE__ ));

	/* init */
	function userpro_init() {
		
		session_start();
		
		global $userpro;
		
		$userpro->do_uploads_dir();
		
		load_plugin_textdomain('userpro', false, dirname(plugin_basename(__FILE__)) . '/languages');
		
		/* include libs */
		require_once userpro_path . '/lib/envato/Envato_marketplaces.php';
		if (!class_exists('MailChimp')){
			require_once userpro_path . '/lib/mailchimp/MailChimp.php';
		}
		
	}
	add_action('init', 'userpro_init');

	/* functions */
	foreach (glob(userpro_path . 'functions/*.php') as $filename) {
		require_once userpro_path . "functions/_trial.php";
		require_once userpro_path . "functions/ajax.php";
		require_once userpro_path . "functions/api.php";
		require_once userpro_path . "functions/badge-functions.php";
		require_once userpro_path . "functions/common-functions.php";
		require_once userpro_path . "functions/custom-alerts.php";
		require_once userpro_path . "functions/defaults.php";
		require_once userpro_path . "functions/fields-filters.php";
		require_once userpro_path . "functions/fields-functions.php";
		require_once userpro_path . "functions/fields-hooks.php";
		require_once userpro_path . "functions/fields-setup.php";
		require_once userpro_path . "functions/frontend-publisher-functions.php";
		require_once userpro_path . "functions/global-actions.php";
		require_once userpro_path . "functions/buddypress.php";
		require_once userpro_path . "functions/hooks-actions.php";
		require_once userpro_path . "functions/hooks-filters.php";
		require_once userpro_path . "functions/icons-functions.php";
		require_once userpro_path . "functions/initial-setup.php";
		require_once userpro_path . "functions/mail-functions.php";
		require_once userpro_path . "functions/member-search-filters.php";
		require_once userpro_path . "functions/memberlist-functions.php";
		require_once userpro_path . "functions/msg-functions.php";
		require_once userpro_path . "functions/security.php";
		require_once userpro_path . "functions/shortcode-extras.php";
		require_once userpro_path . "functions/shortcode-fb.php";
		require_once userpro_path . "functions/shortcode-functions.php";
		require_once userpro_path . "functions/shortcode-main.php";
		require_once userpro_path . "functions/shortcode-private-content.php";
		require_once userpro_path . "functions/social-connect.php";
		require_once userpro_path . "functions/template_redirects.php";
		require_once userpro_path . "functions/terms-agreement.php";
		require_once userpro_path . "functions/user-functions.php";
	}
	
	/* administration */
	if (is_admin()){
		foreach (glob(userpro_path . 'admin/*.php') as $filename) { include $filename; }
	}
	
	/* updates */
	foreach (glob(userpro_path . 'updates/*.php') as $filename) { include $filename; }
	
	/* load addons */
	require_once userpro_path . 'addons/multiforms/index.php';
	require_once userpro_path . 'addons/badges/index.php';
	require_once userpro_path . 'addons/social/index.php';
	require_once userpro_path . 'addons/emd/index.php';
	require_once userpro_path . 'addons/redirects/index.php';