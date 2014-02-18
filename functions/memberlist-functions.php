<?php

	/* Search user by more criteria */
	function userpro_query_search_displayname( $query ) {
		global $wpdb;
		$search_string = esc_attr( trim( get_query_var('searchuser') ) );
		$query->query_where .= $wpdb->prepare( " OR $wpdb->users.display_name LIKE %s", '%' . like_escape( $search_string ) . '%' );
	}

	/* prepare loop of users list */
	function userpro_memberlist_loop($args){
		global $userpro;
		extract($args);
		
		if ( get_option('userpro_trial') == 1) {
			$per_page = 3;
			do_action('userpro_pre_form_message');
		}
	
		global $wpdb;
		$blog_id = get_current_blog_id();

		$page = (!empty($_GET['userp'])) ? $_GET['userp'] : 1;
		$offset = ( ($page -1) * $per_page);

		/** QUERY ARGS BEGIN **/
		
		$query['meta_query'] = array('relation' => strtoupper($relation) );
		
		if (isset($role)){
			$roles = explode(',',$role);
			if (count($roles) >= 2){
				$query['meta_query']['relation'] = 'or';
			}
			foreach($roles as $subrole){
			$query['meta_query'][] = array(
				'key' => $wpdb->get_blog_prefix( $blog_id ) . 'capabilities',
				'value' => $subrole,
				'compare' => 'like'
			);
			}
		}
		
		/* limited to userpro fields */
		if (userpro_retrieve_metakeys()){
			foreach(userpro_retrieve_metakeys() as $key){
			
				if ($userpro->field_type($key) == 'multiselect') {
					$like = 'like';
				} else {
					$like = '=';
				}
			
				if (isset($args[$key]) && $key != 'role' ){
				
					if (substr( trim( htmlspecialchars_decode($args[$key])  ) , 0, 1) === '>') {
						$choices = explode('>', trim(  htmlspecialchars_decode($args[$key]) ));
						$target = $choices[1];
						$query['meta_query'][] = array(
							'key' => $key,
							'value' => $target,
							'compare' => '>'
						);
					} elseif (substr( trim(  htmlspecialchars_decode($args[$key]) ) , 0, 1) === '<') {
						$choices = explode('<', trim(  htmlspecialchars_decode($args[$key]) ));
						$target = $choices[1];
						$query['meta_query'][] = array(
							'key' => $key,
							'value' => $target,
							'compare' => '<'
						);
					} elseif (strstr( esc_attr( trim(  $args[$key] ) ) , ':')){
						$choices = explode(':', esc_attr( trim(  $args[$key] ) ));
						$min = $choices[0];
						$max = $choices[1];
						$query['meta_query'][] = array(
							'key' => $key,
							'value' => array($min, $max),
							'compare' => 'between'
						);
					} elseif (strstr( esc_attr( trim( $args[$key] ) ) , ',')){
						$choices = explode(',', esc_attr( trim(  $args[$key] ) ));
						foreach($choices as $choice){
							$query['meta_query'][] = array(
								'key' => $key,
								'value' => $choice,
								'compare' => $like
							);
						}
					} else {
							$query['meta_query'][] = array(
								'key' => $key,
								'value' => esc_attr( trim( $args[$key] ) ),
								'compare' => $like
							);
					}
					
				}
				
			}
		}
		
		if ($memberlist_verified) {
			$query['meta_query'][] = array(
				'key' => 'userpro_verified',
				'value' => 1,
				'compare' => '='
			);
		}
		
		if (isset($memberlist_withavatar) && $memberlist_withavatar == 1){
			$query['meta_query'][] = array(
				'key' => 'profilepicture',
				'value' => '',
				'compare' => '!='
			);
		}
		
		/**
			CUSTOM SEARCH FILTERS UPDATE
		**
		**
		**/
		
		if (isset($_GET['searchuser'])) {
			global $userpro_emd;
			
			/* Searchuser query param */
			$search_string = esc_attr( trim( get_query_var('searchuser') ) );
			if ($search_string != '') {
			
				if (isset($args['memberlist_filters']) && !empty($args['memberlist_filters']) ){
					$customfilters = explode(',',$args['memberlist_filters']);
					if ($customfilters){
						foreach($customfilters as $customfilter){
							$query['meta_query'][] = array(
								'key' => $customfilter,
								'value' => $search_string,
								'compare' => '='
							);
						}
						$testkeys = new WP_User_Query($query);
					}
				}
				
				if ( empty( $testkeys->results )){
					$query['meta_query'][] = array(
						'key' => 'display_name',
						'value' => $search_string,
						'compare' => 'like'
					);
				}
			
			}
			/* Searchuser query param */

			parse_str($_SERVER['QUERY_STRING'], $params);
			foreach($params as $k => $v){
				$v = trim( strip_tags( esc_attr( $v ) ) );
				$cleankey = str_replace('emd-','',$k);
				
				if (strstr($cleankey, 'from_') ) {
					
					$rangekey = str_replace('from_','',$cleankey);
					if (is_numeric($v)){
						$rangefilter[$rangekey]['compare']['min'] = $v;
					}
				
				} elseif (strstr($cleankey, 'to_') ) {
					
					$rangekey = str_replace('to_','',$cleankey);
					if (is_numeric($v)){
						$rangefilter[$rangekey]['compare']['max'] = $v;
					}
				
				} else {

					if (in_array( 'emd_'.$cleankey, $userpro_emd->must_be_custom_fields )) {
						$cleanparams[$cleankey] = $v;
					} elseif ( $userpro->field_label($cleankey) != '') {
						$cleanparams[$cleankey] = $v;
					}
				
				}
			}
			
			if (isset($rangefilter)){
			
				foreach($rangefilter as $range_k => $arr ) {
				
					if ( ( isset($arr['compare']['min']) && isset($arr['compare']['max']) ) || isset($arr['compare']['min']) || isset($arr['compare']['max']) ) {
					
					if (!isset($arr['compare']['min'])){
						$split = explode(',',$args[$range_k . '_range']);
						$arr['compare']['min'] = $split[0];
					}
					
					if (!isset($arr['compare']['max'])){
						$split = explode(',',$args[$range_k . '_range']);
						$arr['compare']['max'] = $split[1];
					}
					
					$query['meta_query'][] = array(
						'key' => $range_k,
						'value' => array($arr['compare']['min'], $arr['compare']['max']),
						'compare' => 'between'
					);
					
					}
				}
				
			}

			if (isset($cleanparams)){
			foreach($cleanparams as $k => $v) {
				if ($k == 'photopreference') {
					if ($v === '1') {
					$query['meta_query'][] = array(
						'key' => 'profilepicture',
						'value' => '',
						'compare' => '!='
					);
					}
					if ($v === '2') {
					$query['meta_query'][] = array(
						'key' => 'profilepicture',
						'value' => '',
						'compare' => '=='
					);
					}
				} elseif ($k == 'accountstatus') {
					if ($v > 0) {
					$query['meta_query'][] = array(
						'key' => 'userpro_verified',
						'value' => 1,
						'compare' => '=='
					);
					}
				} elseif ($v != 'all') {
				
					if ($v != '') {
					
					$query['meta_query'][] = array(
						'key' => $k,
						'value' => $v,
						'compare' => '='
					);
					
					}
				}
			}
			}

		}
		
		/** DO **/
		
		if ($sortby) $query['orderby'] = $sortby;
		
		if ($order) $query['order'] = strtoupper($order); // asc to ASC
		
		/** QUERY ARGS END **/
		
		$query['number'] = $per_page;
		$query['offset'] = $offset;

		$count_args = array_merge($query, array('number'=>99999999999));
		unset($count_args['offset']);
		
		$user_count_query = $userpro->get_cached_query( $count_args );
		//$user_count_query = new WP_User_Query($count_args);

		if ($per_page) {
		$user_count = $user_count_query->get_results();
		$total_users = $user_count ? count($user_count) : 1;
		$total_pages = ceil($total_users / $per_page);
		}

		$wp_user_query = $userpro->get_cached_query( $query );
		//$wp_user_query = new WP_User_Query($query);
		
		remove_action( 'pre_user_query', 'userpro_query_search_displayname' );
		
		$url = parse_url(wp_guess_url());
		if (isset($url['query'])){
		$string_query = $url['query'];
		} else {
		$string_query = null;
		}
		if (! empty( $wp_user_query->results ))
			$arr['total'] = $total_users;
			$arr['paginate'] = paginate_links( array(
					'base'         => @add_query_arg('userp','%#%'),
					'total'        => $total_pages,
					'current'      => $page,
					'show_all'     => false,
					'end_size'     => 1,
					'mid_size'     => 2,
					'prev_next'    => true,
					'prev_text'    => __('« Previous','userpro'),
					'next_text'    => __('Next »','userpro'),
					'type'         => 'plain',
				));
			$arr['users'] = $wp_user_query->results;
			
		return $arr;
		
	}
	
	/* prepare loop of users list */
	function userpro_memberlist_listusers($args, $list_users=null){
		global $userpro, $wpdb;
		
		extract($args);
		$blog_id = get_current_blog_id();
		
		if ( get_option('userpro_trial') == 1) {
			$list_per_page = 3;
			do_action('userpro_pre_form_message');
		}
		
		// Show Verified accounts only
		if ($list_verified) {
			$query['meta_query'] = array('relation' => strtoupper($list_relation) );
			$query['meta_query'][] = array(
				'key' => 'userpro_verified',
				'value' => 1,
				'compare' => '='
			);
		}

		$query['orderby'] = $list_sortby;
		
		$query['order'] = strtoupper($list_order); // asc to ASC
		
		$query['number'] = $list_per_page;
		
		// Show/promote specific users
		if (isset($list_users) && !empty($list_users)){
			if ($list_users == 'author') {
				$author = get_the_author_meta('ID');
				$include[] = $author;
			} else {
				$usernames = explode(',',$list_users);
				foreach($usernames as $username){
					$user = get_user_by( 'login', $username );
					if ($user){
						$include[] = $user->ID;
					}
				}
			}
			
			if (isset($include) && is_array($include)){
			$query['include'] = $include;
			$query['number'] = 0;
			}
		}
		
		// Done, run query
		
		$wp_user_query = $userpro->get_cached_query( $query );
		//$wp_user_query = new WP_User_Query($query);
		
		if (! empty( $wp_user_query->results ))

			$arr['users'] = $wp_user_query->results;
			
		if (isset($arr)) return $arr;
		
	}