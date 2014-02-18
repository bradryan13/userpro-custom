<?php

	function userpro_check_status_verified(){
		echo '<div class="userpro-bar-success">'.sprintf(__('Congratulations! Your account is now %s <strong>Verified</strong>. <i class="userpro-icon-remove"></i>','userpro'), userpro_get_badge('verified')).'</div>';
	}
	
	function userpro_failed_status_verified(){
		echo '<div class="userpro-bar-failed">'.__('This invitation request is invalid or has expired! <i class="userpro-icon-remove"></i>','userpro').'</div>';
	}
	
	function userpro_msg_login_to_post(){
		echo '<div class="userpro-message userpro-message-ajax"><p>'.__('Please log in to publish a new post.','userpro').'</p></div>';
	}

	function userpro_msg_account_validated(){
		echo '<div class="userpro-message userpro-message-ajax"><p>'.__('Your account has been successfully activated!','userpro').'</p></div>';
	}

	function userpro_msg_activate_pending(){
		echo '<div class="userpro-message userpro-message-ajax"><p>'.__('Your email is pending verification. Please activate your account.','userpro').'</p></div>';
	}
	
	function userpro_msg_activate_pending_admin(){
		echo '<div class="userpro-message userpro-message-ajax"><p>'.__('Your account is currently being reviewed. Thanks for your patience.','userpro').'</p></div>';
	}

	function userpro_msg_profile_saved(){
		echo '<div class="userpro-message userpro-message-ajax"><p>'.__('Your profile has been saved.','userpro').'</p></div>';
	}

	function userpro_msg_login_after_reg(){
		echo '<div class="userpro-message userpro-message-ajax"><p>'.__('Thank you for registering. Please login to continue.','userpro').'</p></div>';
	}
	
	function userpro_msg_login_to_view_yourprofile(){
		echo '<div class="userpro-message userpro-message-ajax"><p>'.__('Please login to view and manage your profile.','userpro').'</p></div>';
	}
	
	function userpro_msg_login_to_view_profile(){
		echo '<div class="userpro-message userpro-message-ajax"><p>'.__('Please login to view this user profile.','userpro').'</p></div>';
	}
	
	function userpro_msg_new_secret_key(){
		echo '<div class="userpro-message userpro-message-ajax"><p>'.__('We\'ll email you a secret key. Once you obtain the key, you can use it to Change your Password.','userpro').'</p></div>';
	}
	
	function userpro_msg_secret_key_sent(){
		echo '<div class="userpro-message userpro-message-ajax"><p>'.__('A secret key has been sent successfully.','userpro').'</p></div>';
	}
	
	function userpro_msg_login_after_passchange(){
		echo '<div class="userpro-message userpro-message-ajax"><p>'.__('Your password has been changed successfully. Please login.','userpro').'</p></div>';
	}