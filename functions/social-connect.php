<?php

	add_action('userpro_inside_form_submit','userpro_social_connect');
	function userpro_social_connect($array){
		global $userpro;
		// only in login/register
		if ($array['template'] == 'login' || $array['template'] == 'register' ) {
			
			echo '<div class="userpro-social-connect">';
		
			if (userpro_get_option('facebook_app_id') != '' && userpro_get_option('facebook_connect') == 1) {
				?>
				
				<div id="fb-root" class="userpro-column"></div>
				<script>
				window.fbAsyncInit = function() {
					
					FB.init({
						appId      : "<?php echo userpro_get_option('facebook_app_id'); ?>", // Set YOUR APP ID
						status     : true, // check login status
						cookie     : true, // enable cookies to allow the server to access the session
						xfbml      : true  // parse XFBML
					});
			 
					FB.Event.subscribe('auth.authResponseChange', function(response)
					{
					if (response.status === 'connected')
					{
					
					//SUCCESS
			 
					}   
					else if (response.status === 'not_authorized')
					{
					
					//FAILED
					
					} else
					{
					
					//UNKNOWN ERROR
					
					}
					});
			 
				};
			 
				// Login user
				function Login(element){
					
					var form = jQuery(element).parents('.userpro').find('form');
					userpro_init_load( form );
					
					if ( element.data('redirect')) {
						var redirect = element.data('redirect');
					} else {
						var redirect = '';
					}

					FB.login(function(response) {
						if (response.authResponse){
						
							// post to wall
							<?php if (userpro_get_option('facebook_autopost')) { ?>
							
							<?php if ( userpro_get_option('facebook_autopost_name') ) { ?>
							var name = "<?php echo userpro_get_option('facebook_autopost_name'); ?>"; // post title
							<?php } ?>
							
							<?php if ( userpro_get_option('facebook_autopost_body') ) { ?>
							var body = "<?php echo userpro_get_option('facebook_autopost_body'); ?>"; // post body
							<?php } ?>
							
							<?php if ( userpro_get_option('facebook_autopost_caption') ) { ?>
							var caption = "<?php echo userpro_get_option('facebook_autopost_caption'); ?>"; // caption, url, etc.
							<?php } ?>
							
							<?php if ( userpro_get_option('facebook_autopost_description') ) { ?>
							var description = "<?php echo userpro_get_option('facebook_autopost_description'); ?>"; // full description
							<?php } ?>
							
							<?php if ( userpro_get_option('facebook_autopost_link') ) { ?>
							var link = "<?php echo userpro_get_option('facebook_autopost_link'); ?>"; // link
							<?php } ?>
							
							FB.api('/me/feed', 'post', { message:body,caption:caption,link:link,name:name,description:description}, function (response) { });
							<?php } ?>
							
							// get profile picture
							FB.api('/me/picture?type=large', function(response) {
								profilepicture = response.data.url;
							});
							
							// connect via facebook
							FB.api('/me', function(response) {
								
								jQuery.ajax({
									url: userpro_ajax_url,
									data: "action=userpro_fbconnect&id="+response.id+"&username="+response.username+"&first_name="+response.first_name+"&last_name="+response.last_name+"&gender="+response.gender+"&email="+response.email+"&name="+response.name+"&link="+response.link+"&profilepicture="+profilepicture+"&redirect="+redirect,
									dataType: 'JSON',
									type: 'POST',
									success:function(data){
									
										userpro_end_load( form );
										
										/* custom message */
										if (data.custom_message){
										form.parents('.userpro').find('.userpro-body').prepend( data.custom_message );
										}
										
										/* redirect after form */
										if (data.redirect_uri){
											if (data.redirect_uri =='refresh') {
												document.location.href=jQuery(location).attr('href');
											} else {
												document.location.href=data.redirect_uri;
											}
										}
										
									},
									error: function(){
										alert('Something wrong happened.');
									}
								});
							
							});
							
						// cancelled
						} else {
							alert( 'Unauthorized or cancelled' );
							userpro_end_load( form );
						}
					},{scope: 'email,user_photos,publish_stream'});
			 
				}
				
				// Logout
				function Logout(){
					FB.logout(function(){document.location.reload();});
				}
			 
				// Load the SDK asynchronously
				(function(d){
				 var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
				 if (d.getElementById(id)) {return;}
				 js = d.createElement('script'); js.id = id; js.async = true;
				 js.src = "//connect.facebook.net/en_US/all.js";
				 ref.parentNode.insertBefore(js, ref);
				}(document));
			 
				</script>

				<a href="#" class="userpro-social-facebook userpro-tip" data-redirect="<?php echo $array['facebook_redirect']; ?>" title="<?php _e('Login with Facebook','userpro'); ?>"></a>

				<?php
			}
			
			if ( userpro_get_option('twitter_connect') == 1 && userpro_get_option('twitter_consumer_key') && userpro_get_option('twitter_consumer_secret') ) {
				$url = $userpro->get_twitter_auth_url();
				?>
			
				<a href="<?php echo $url; ?>" class="userpro-social-twitter userpro-tip" title="<?php _e('Login with Twitter','userpro'); ?>"></a>
		
				<?php
			}
				
			if ( userpro_get_option('google_connect') == 1 && userpro_get_option('google_client_id') && userpro_get_option('google_client_secret') && userpro_get_option('google_redirect_uri') ) {
				$url = $userpro->get_google_auth_url();
				?>
			
				<a href="<?php echo $url; ?>" class="userpro-social-google userpro-tip" title="<?php _e('Login with Google+','userpro'); ?>"></a>
				
				<?php
			}
			
			// hook to add more networks
			do_action('userpro_social_connect_buttons');
			
			echo '</div><div class="userpro-clear"></div>';
		
		}
	}
	
	/* Manual implementation */
	function userpro_facebook_connect_manual( $array='' ) {
		if (userpro_get_option('facebook_app_id') != '' && userpro_get_option('facebook_connect') == 1) {
	?>
	
	<div id="fb-root" class="userpro-column"></div>
	<script>
	window.fbAsyncInit = function() {
		
		FB.init({
			appId      : "<?php echo userpro_get_option('facebook_app_id'); ?>", // Set YOUR APP ID
			status     : true, // check login status
			cookie     : true, // enable cookies to allow the server to access the session
			xfbml      : true  // parse XFBML
		});
 
		FB.Event.subscribe('auth.authResponseChange', function(response)
		{
		if (response.status === 'connected')
		{
		
        //SUCCESS
 
		}   
		else if (response.status === 'not_authorized')
		{
        
        //FAILED
		
		} else
		{
		
        //UNKNOWN ERROR
		
		}
		});
 
	};
 
	// Login user
	function Login(element){
		
		var form = jQuery(element).parents('.userpro').find('form');
		if (form.length){
		userpro_init_load( form );
		}
		
		if ( element.data('redirect')) {
			var redirect = element.data('redirect');
		} else {
			var redirect = '';
		}

		FB.login(function(response) {
			if (response.authResponse){
			
                // get user info
				FB.api('/me/picture?type=large', function(response) {
					profilepicture = response.data.url;
				});

				FB.api('/me', function(response) {

					jQuery.ajax({
						url: userpro_ajax_url,
						data: "action=userpro_fbconnect&id="+response.id+"&username="+response.username+"&first_name="+response.first_name+"&last_name="+response.last_name+"&gender="+response.gender+"&email="+response.email+"&name="+response.name+"&link="+response.link+"&profilepicture="+profilepicture+"&redirect="+redirect,
						dataType: 'JSON',
						type: 'POST',
						success:function(data){

							if (form.length){
							userpro_end_load( form );
							}
							
							/* custom message */
							if (form.length){
							if (data.custom_message){
							form.parents('.userpro').find('.userpro-body').prepend( data.custom_message );
							}
							}
							
							/* redirect after form */
							if (data.redirect_uri){
								if (data.redirect_uri =='refresh') {
									document.location.href=jQuery(location).attr('href');
								} else {
									document.location.href=data.redirect_uri;
								}
							}
							
						},
						error: function(){
							alert('Something wrong happened.');
						}
					});
				
				});

			} else {
				// cancelled
				alert( 'Unauthorized or cancelled' );
				if (form.length){
				userpro_end_load( form );
				}
            }
		},{scope: 'email,user_photos,publish_stream'});
 
	}
	
	// Logout
	function Logout(){
		FB.logout(function(){document.location.reload();});
	}
 
	// Load the SDK asynchronously
	(function(d){
     var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = "//connect.facebook.net/en_US/all.js";
     ref.parentNode.insertBefore(js, ref);
	}(document));
 
	</script>

	<div class='userpro-field'><div class='userpro-label'></div><div class='userpro-input'><a href="#" class="userpro-social-facebook" data-redirect="<?php echo $array['facebook_redirect']; ?>"></a><div class='userpro-clear'></div></div></div><div class='userpro-clear'></div>

	<?php
		}
	}