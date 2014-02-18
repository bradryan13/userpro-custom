<?php

	/* LOCK ENTIRE SITE for guests */
	function userpro_entire_not_logged_in(){
		global $userpro, $post;
		$locked = userpro_get_option('site_guest_lockout');
		$page_id = userpro_get_option('site_guest_lockout_pageid');
		if ( $locked && is_numeric($page_id) && !userpro_is_logged_in() ) {
		
			$condition = false;
			$page_data = get_page($page_id);
			if($page_data->post_status == 'publish') $condition = true;
			
			if ($condition == false) return;
			if (isset($post->ID) && $post->ID == $page_id && $condition == true) return;
			
			if (isset($post->ID)){
				$redirect_to = get_permalink($post->ID);
			}
			
			if (isset($redirect_to)){
				wp_redirect( add_query_arg('redirect_to', $redirect_to, get_permalink($page_id) ) );
			} else {
				wp_redirect( get_permalink($page_id) );
			}
			exit;
			
		}
	}
	add_action('template_redirect', 'userpro_entire_not_logged_in');

	/* LOCK homepage only for users */
	function userpro_homepage_logged_in(){
		global $userpro;
		$url = userpro_get_option('homepage_member_lockout');
		if ( !empty($url) && strstr($url, 'http') && is_front_page() && userpro_is_logged_in() ) {
			wp_redirect( $url );
			exit;
		}
	}
	add_action('template_redirect', 'userpro_homepage_logged_in');
	
	/* LOCK homepage only for guests */
	function userpro_homepage_not_logged_in(){
		global $userpro;
		$url = userpro_get_option('homepage_guest_lockout');
		if ( !empty($url) && strstr($url, 'http') && is_front_page() && !userpro_is_logged_in() ) {
			wp_redirect( $url );
			exit;
		}
	}
	add_action('template_redirect', 'userpro_homepage_not_logged_in');
	
	/* Logged in users trying to see login/register */
	function userpro_accessing_login_when_logged(){
		global $userpro;
		if ( ( is_page() || is_single() ) && userpro_is_logged_in() ) {
			global $post;
			$pages = get_option('userpro_pages');
			if ($post->ID == $pages['login'] && userpro_get_option('show_logout_login') ) {
				wp_redirect( $userpro->permalink() );
				exit;
			} elseif ($post->ID == $pages['register'] && userpro_get_option('show_logout_register') ) {
				wp_redirect( $userpro->permalink() );
				exit;
			}
		}
	}
	add_action('template_redirect', 'userpro_accessing_login_when_logged');
	
	/* Logout page */
	function userpro_logout_page(){
		global $userpro;
		if ( is_page() || is_single() ) {
			global $post;
			$pages = get_option('userpro_pages');
			if ($post->ID == $pages['logout_page'] ) {
				if (userpro_is_logged_in()){
				
					$logout = userpro_get_option('logout_uri');
					if ($logout == 1) $url = home_url();
					if ($logout == 2) $url = $userpro->permalink(0, 'login');
					if ($logout == 3) $url = userpro_get_option('logout_uri_custom');
					if (isset($_REQUEST['redirect_to'])){
						$url = $_REQUEST['redirect_to'];
					}
					wp_logout();
					wp_redirect( $url );
					exit;
					
				} else {
				
					wp_redirect( $userpro->permalink(0, 'login') );
					exit;
					
				}
			}
		}
	}
	add_action('template_redirect', 'userpro_logout_page');