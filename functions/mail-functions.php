<?php

	/**
	Sends mail
	This function manage the Mail stuff sent by plugin
	to users
	**/
	function userpro_mail($id, $template=null, $var1=null, $form=null) {
		global $userpro;

		$user = get_userdata($id);
		$builtin = array(
			'{USERPRO_ADMIN_EMAIL}' => userpro_get_option('mail_from'),
			'{USERPRO_BLOGNAME}' => userpro_get_option('mail_from_name'),
			'{USERPRO_LOGIN_URL}' => $userpro->permalink(0, 'login'),
			'{USERPRO_USERNAME}' => $user->user_login,
			'{USERPRO_EMAIL}' => $user->user_email,
			'{USERPRO_PROFILE_LINK}' => $userpro->permalink( $user->ID ),
			'{USERPRO_VALIDATE_URL}' => $userpro->create_validate_url( $user->ID ),
			'{USERPRO_PENDING_REQUESTS_URL}' => admin_url() . '?page=userpro&tab=requests',
			'{USERPRO_ACCEPT_VERIFY_INVITE}' => $userpro->accept_invite_to_verify($user->ID),
		);
		
		if (isset($var1) && !empty($var1) ){
			$builtin['{VAR1}'] = $var1;
		}
		
		if (isset($form) && $form != ''){
		$builtin['{USERPRO_PROFILE_FIELDS}'] = $userpro->extract_profile_for_mail( $user->ID, $form );
		}
		
		$search = array_keys($builtin);
		$replace = array_values($builtin);

		$headers = 'From: '.userpro_get_option('mail_from_name').' <'.userpro_get_option('mail_from').'>' . "\r\n";

		/////////////////////////////////////////////////////////
		/* verify email/new registration */
		/////////////////////////////////////////////////////////
		if ($template == 'verifyemail'){
			$subject = __('Verify your Account','userpro');
			$message = userpro_get_option('mail_verifyemail');
			$message = str_replace( $search, $replace, $message );
		}
		
		/////////////////////////////////////////////////////////
		/* secret key request */
		/////////////////////////////////////////////////////////
		if ($template == 'secretkey'){
			$subject = __('Reset Your Password','userpro');
			$message = userpro_get_option('mail_secretkey');
			$message = str_replace( $search, $replace, $message );
		}
		
		/////////////////////////////////////////////////////////
		/* account being removed */
		/////////////////////////////////////////////////////////
		if ($template == 'accountdeleted'){
			$subject = __('Your profile has been removed!','userpro');
			$message = userpro_get_option('mail_accountdeleted');
			$message = str_replace( $search, $replace, $message );
		}
		
		/////////////////////////////////////////////////////////
		/* verification invite */
		/////////////////////////////////////////////////////////
		if ($template == 'verifyinvite'){
			$subject = sprintf(__('Get Verified at %s!','userpro'), userpro_get_option('mail_from_name'));
			$message = userpro_get_option('mail_verifyinvite');
			$message = str_replace( $search, $replace, $message );
		}
		
		/////////////////////////////////////////////////////////
		/* account being verified */
		/////////////////////////////////////////////////////////
		if ($template == 'accountverified'){
			$subject = __('Your account is now verified!','userpro');
			$message = userpro_get_option('mail_accountverified');
			$message = str_replace( $search, $replace, $message );
		}
		
		/////////////////////////////////////////////////////////
		/* account being unverified */
		/////////////////////////////////////////////////////////
		if ($template == 'accountunverified'){
			$subject = __('Your account is no longer verified!','userpro');
			$message = userpro_get_option('mail_accountunverified');
			$message = str_replace( $search, $replace, $message );
		}
		
		/////////////////////////////////////////////////////////
		/* new user's account */
		/////////////////////////////////////////////////////////
		if ($template == 'newaccount' && !$userpro->is_pending($user->ID) ) {
			$subject = sprintf(__('Welcome to %s!','userpro'), userpro_get_option('mail_from_name'));
			$message = userpro_get_option('mail_newaccount');
			$message = str_replace( $search, $replace, $message );
		}
		
		/////////////////////////////////////////////////////////
		/* email user except: profileupdate */
		/////////////////////////////////////////////////////////
		if ($template != 'profileupdate' && $template != 'pendingapprove') {
			wp_mail( $user->user_email, $subject, $message, $headers );
		}
		
		/////////////////////////////////////////////////////////
		/* admin emails notifications */
		/////////////////////////////////////////////////////////
	
		if ($template == 'pendingapprove'){
			$subject = __('[UserPro] User awaiting manual review','userpro');
			$message = userpro_get_option('mail_admin_pendingapprove');
			$message = str_replace( $search, $replace, $message );
			wp_mail( userpro_get_option('mail_from') , $subject, $message, $headers );
		}
		
		if ($template == 'newaccount') {
			$subject = __('[UserPro] New User Registration','userpro');
			$message = userpro_get_option('mail_admin_newaccount');
			$message = str_replace( $search, $replace, $message );
			wp_mail( userpro_get_option('mail_from') , $subject, $message, $headers );
		}
		
		if ($template == 'accountdeleted' && userpro_get_option('notify_admin_profile_remove') ) {
			$subject = __('[UserPro] A profile has been removed!','userpro');
			$message = userpro_get_option('mail_admin_accountdeleted');
			$message = str_replace( $search, $replace, $message );
			wp_mail( userpro_get_option('mail_from') , $subject, $message, $headers );
		}
		
		if ($template == 'profileupdate') {
			$subject = __('[UserPro] A profile has been updated!','userpro');
			$message = userpro_get_option('mail_admin_profileupdate');
			$message = str_replace( $search, $replace, $message );
			wp_mail( userpro_get_option('mail_from') , $subject, $message, $headers );
		}
		
	}