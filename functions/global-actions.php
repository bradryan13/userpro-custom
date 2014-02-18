<?php

	/* Setup global redirection */
	add_action('userpro_super_get_redirect', 'userpro_super_get_redirect');
	function userpro_super_get_redirect($i){
		
		if (isset($_GET['redirect_to'])){
		?>
			<input type="hidden" name="global_redirect-<?php echo $i; ?>" id="global_redirect-<?php echo $i; ?>" value="<?php echo $_GET['redirect_to']; ?>" />
		<?php
		}
		
	}
	
	/* Antispam check on forms */
	add_filter('userpro_login_validation', 'userpro_antispam_check', 10, 2);
	add_filter('userpro_register_validation', 'userpro_antispam_check', 10, 2);
	add_filter('userpro_form_validation', 'userpro_antispam_check', 10, 2);
	function userpro_antispam_check($errors, $form) {
		extract($form);
		
		if (isset($antispam)) {
			if ( $form['antispam'] != $form['answer'] ){ 
				$errors['antispam'] = __('Incorrect answer. please try again.','userpro');
			}
		}
		
		return $errors;
	}