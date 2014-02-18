<?php
	
	/* Registers and display the shortcode */
	add_shortcode('userpro_fb', 'userpro_fb' );
	function userpro_fb( $args=array() ) {
		global $userpro;
		
		/* arguments */
		$defaults = apply_filters('userpro_shortcode_args_fb', array(
			'facebook_redirect' => '',
			'align' => 'center',
		) );
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );

			ob_start();
			
			echo '<div class="userpro-custom-'.$align.'">';
			$userpro->facebook_login( $args );
			echo '</div>';
			
			if ( in_array( $align, array('left','right') ) ) {
				echo '<div class="userpro-clear"></div>';
			}
			
			$output = ob_get_contents();
			ob_end_clean();
			return $output;

	}