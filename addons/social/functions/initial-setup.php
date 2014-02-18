<?php

	add_action('init', 'userpro_sc_setup', 99);
	function userpro_sc_setup($rebuild=0) {
		
		/* Create optional pages */
		if ($rebuild) { delete_option('userpro_sc_pages'); }
		
		/* Find pages */
		$pages = get_option('userpro_sc_pages');
		
		/* Create pages if they do not exist */
		if (!isset($pages['following'])) {
		
			$slug_following = userpro_sc_get_option('slug_following');
			$slug_followers = userpro_sc_get_option('slug_followers');

			$following = array(
				  'post_title'  		=> __('Following','userpro'),
				  'post_content' 		=> '[userpro template=following]',
				  'post_name'			=> $slug_following,
				  'comment_status' 		=> 'closed',
				  'post_type'     		=> 'page',
				  'post_status'   		=> 'publish',
				  'post_author'   		=> 1
			);
			$following = wp_insert_post($following);
			$pages['following'] = $following;
			$post = get_post($following, ARRAY_A);
			userpro_sc_set_option('slug_following', $post['post_name']);
			
			$followers = array(
				  'post_title'  		=> __('Followers','userpro'),
				  'post_content' 		=> '[userpro template=followers]',
				  'post_name'			=> $slug_followers,
				  'comment_status' 		=> 'closed',
				  'post_type'     		=> 'page',
				  'post_status'   		=> 'publish',
				  'post_author'   		=> 1
			);
			$followers = wp_insert_post($followers);
			$pages['followers'] = $followers;
			$post = get_post($followers, ARRAY_A);
			userpro_sc_set_option('slug_followers', $post['post_name']);
		
			update_option('userpro_sc_pages', $pages);
			
			/* Rewrite rules */
			$slug_following = userpro_sc_get_option('slug_following');
			$slug_followers = userpro_sc_get_option('slug_followers');
			add_rewrite_rule("$slug_following/([^/]+)/?",'index.php?pagename='.$slug_following.'&up_username=$matches[1]', 'top');
			add_rewrite_rule("$slug_followers/([^/]+)/?",'index.php?pagename='.$slug_followers.'&up_username=$matches[1]', 'top');
			
			flush_rewrite_rules();
			
		} else {
		
			// pages installed
			$slug_following = userpro_sc_get_option('slug_following');
			$slug_followers = userpro_sc_get_option('slug_followers');
			add_rewrite_rule("$slug_following/([^/]+)/?",'index.php?pagename='.$slug_following.'&up_username=$matches[1]', 'top');
			add_rewrite_rule("$slug_followers/([^/]+)/?",'index.php?pagename='.$slug_followers.'&up_username=$matches[1]', 'top');
			
		}
	
	}