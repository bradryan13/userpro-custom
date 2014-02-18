/* Custom JS starts here */
jQuery(document).ready(function() {

	// refresh activity
	jQuery('.userpro-sc-refresh').live('click',function(e){
		var container = jQuery(this).parents('.userpro');
		var link = jQuery(this);
		var loader = container.find('img.userpro-sc-refresh-loader');
		var per_page = container.data('activity_per_page');
		var activity_user = container.data('activity_user');
		var offset = 0;
		if ( container.find('.userpro-sc-load').data('user_id') ) {
			var user_id = container.find('.userpro-sc-load').data('user_id');
		} else {
			var user_id = 0;
		}
		link.hide();
		loader.show();
		jQuery.ajax({
			url: userpro_ajax_url,
			data: "action=userpro_sc_refreshactivity&per_page="+per_page+"&offset="+offset+'&user_id='+user_id+'&activity_user='+activity_user,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				if (data.res != ''){
				container.find('.userpro-sc').remove();
				container.find('.userpro-body-nopad').prepend( data.res );
				link.show();
				loader.hide();
				} else {
				link.show();
				loader.hide();
				}
			}
		});
	});

	// activity more
	jQuery('.userpro-sc-load a').live('click',function(e){
		var container = jQuery(this).parents('.userpro-body');
		var link = jQuery(this);
		var loader = jQuery(this).parents('.userpro-sc-load').find('img');
		var per_page = jQuery(this).data('activity_per_page');
		var activity_user = jQuery(this).data('activity_user');
		var offset = jQuery(this).parents('.userpro').find('.userpro-sc').length;
		if ( link.parents('.userpro-sc-load').data('user_id') ) {
			var user_id = link.parents('.userpro-sc-load').data('user_id');
		} else {
			var user_id = 0;
		}
		link.hide();
		loader.show();
		jQuery.ajax({
			url: userpro_ajax_url,
			data: "action=userpro_sc_loadactivity&per_page="+per_page+"&offset="+offset+'&user_id='+user_id+'&activity_user='+activity_user,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				if (data.res != ''){
				container.find('.userpro-sc:last').after( data.res );
				link.show();
				loader.hide();
				} else {
				link.show();
				loader.hide();
				}
			}
		});
	});

	// follow user
	jQuery('.userpro-follow.notfollowing:not(.processing)').live('click',function(e){

		jQuery(this).addClass('processing');
		var from = jQuery(this).data('follow-from');
		var to = jQuery(this).data('follow-to');
		
		jQuery(this).addClass('following').removeClass('.notfollowing').removeClass('secondary').html( jQuery(this).data('following-text') );
		jQuery(this).removeClass('processing');
		
		jQuery.ajax({
			url: userpro_ajax_url,
			data: "action=userpro_sc_follow&to="+to+"&from="+from,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				
			},
			error: function(){
				
			}
		});
		
	});
	
	// unfollow hover
	jQuery('.userpro-follow.following').live('mouseenter',function(){
		jQuery(this).addClass('red').removeClass('secondary').html( jQuery(this).data('unfollow-text') );
	}).live('mouseleave',function(){
		jQuery(this).removeClass('red').html( jQuery(this).data('following-text') );
	});
	
	// unfollow user
	jQuery('.userpro-follow.following:not(.processing)').live('click',function(e){
		jQuery(this).addClass('processing');
		var from = jQuery(this).data('follow-from');
		var to = jQuery(this).data('follow-to');
		
		jQuery(this).removeClass('following').addClass('notfollowing').removeClass('red').addClass('secondary').html('<i class="userpro-icon-share"></i>' + jQuery(this).data('follow-text') );
		jQuery(this).removeClass('processing');
		
		jQuery.ajax({
			url: userpro_ajax_url,
			data: "action=userpro_sc_unfollow&to="+to+"&from="+from,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){

			},
			error: function(){
			
			}
		});
		
	});
	
});