jQuery(document).ready(function() {

	/* Delete achievement badge */
	jQuery('.userpro-badge-remove').live('click',function(){
		btype = jQuery(this).data('btype');
		bid = jQuery(this).data('bid');
		td = jQuery(this).parents('tr');
		jQuery.ajax({
			url: ajaxurl,
			data: 'action=userpro_delete_achievement_badge&btype=' + btype + '&bid=' + bid,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				td.fadeOut();
			},
			error: function(data){
				alert('error');
			}
		});
		return false;
	});
	
	/* Badge selection */
	jQuery('.userpro-admin-badge').live('click',function(){
		jQuery('.userpro-admin-badge').removeClass('active');
		jQuery(this).addClass('active');
		jQuery('#badge_url').val( jQuery(this).find('img').attr('src') );
	});
	
	/* conditional fields */
	jQuery('table[data-type=conditional]').hide();
	jQuery('table[rel=' + jQuery('#badge_method').val() + ']').show();
	
	jQuery('#badge_method').live('change',function(){
		jQuery('table[data-type=conditional]').hide();
		jQuery('table[rel=' + jQuery(this).val() + ']').show();
		jQuery('table[rel=' + jQuery(this).val() + ']').find('select').removeClass("chzn-done").css('display', 'inline').data('chosen', null);
		jQuery('table[rel=' + jQuery(this).val() + ']').find("*[class*=chzn], .chosen-container").remove();
		jQuery('table[rel=' + jQuery(this).val() + ']').find(".chosen-select").chosen({
			disable_search_threshold: 10
		});
	});
	
	/* Delete user badge */
	jQuery('.userpro-delete-badge').live('click',function(e){
		e.preventDefault();
		user_id = jQuery(this).data('user');
		badge_url = jQuery(this).data('url');
		element = jQuery(this);
		
		jQuery.ajax({
			url: ajaxurl,
			data: 'action=userpro_delete_user_badge&user_id=' + user_id + '&badge_url=' + badge_url,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				element.parents('div.userpro-user-badge').fadeOut();
			},
			error: function(data){
				alert('error');
			}
		});
		return false;
	});
	
});