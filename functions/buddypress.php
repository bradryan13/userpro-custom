<?php

	// Override BuddyPress avatar
	add_filter( 'bp_core_fetch_avatar', 'revert_to_default_wp_avatar', 80, 3 );//late load
	function revert_to_default_wp_avatar( $img, $params, $item_id ){
		// we are concerned only with users
		if( $params['object']!='user' )
			return $img;

		//check if user has uploaded an avatar
		//if not then revert back to wordpress core get_avatar method
		//remove the filter first, or else it will go in infinite loop
		remove_filter( 'bp_core_fetch_avatar', 'revert_to_default_wp_avatar', 80, 3 );

		if( !userpro_user_has_avatar( $item_id ) ){
			$width = $params['width'];
			// Set image width
			if ( false !== $width ) {
				$img_width = $width;
						} elseif ( 'thumb' == $type ) {
				$img_width = bp_core_avatar_thumb_width();
			} else {
				$img_width = bp_core_avatar_full_width();
			}
			$img = get_avatar( $item_id, $img_width );

		 }

		//add the filter back again
		add_filter( 'bp_core_fetch_avatar', 'revert_to_default_wp_avatar', 80, 3 );

		return $img;
	}

	/**
	* Check if the given user has an uploaded avatar
	* @return boolean
	*/
	function userpro_user_has_avatar( $user_id=false ) {
		// $user_id = bp_loggedin_user_id();
		if ( bp_core_fetch_avatar( array( 'item_id' => $user_id, 'no_grav' => true,'html'=> false ) ) != bp_core_avatar_default() ) {
			   return true;
		 }
		return false;
	}