jQuery(document).ready(function() {

	/* deny user registration */
	jQuery('.upadmin-user-deny').live('click',function(e){
		e.preventDefault();
		var link = jQuery(this);
		var parent = jQuery(this).parents('.upadmin-pending-verify');
		jQuery.ajax({
			url: ajaxurl,
			data: 'action=userpro_admin_user_deny&user_id=' + jQuery(this).data('user'),
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				parent.fadeOut();
				if (data.count === '0' || data.count == '' || !data.count) {
					jQuery('.upadmin-bubble-new').remove();
				} else {
					jQuery('.upadmin-bubble-new').html( data.count );
				}
				jQuery('.toplevel_page_userpro').find('span.update-count').html( data.count );
			}
		});
		return false;
	});
	
	/* approve user registration */
	jQuery('.upadmin-user-approve').live('click',function(e){
		e.preventDefault();
		var link = jQuery(this);
		var parent = jQuery(this).parents('.upadmin-pending-verify');
		jQuery.ajax({
			url: ajaxurl,
			data: 'action=userpro_admin_user_approve&user_id=' + jQuery(this).data('user'),
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				parent.fadeOut();
				if (data.count === '0' || data.count == '' || !data.count) {
					jQuery('.upadmin-bubble-new').remove();
				} else {
					jQuery('.upadmin-bubble-new').html( data.count );
				}
				jQuery('.toplevel_page_userpro').find('span.update-count').html( data.count );
			}
		});
		return false;
	});
	
	/* Verify user */
	jQuery('.upadmin-verify-u').live('click',function(e){
		e.preventDefault();
		var link = jQuery(this);
		var parent = jQuery(this).parents('.upadmin-verify-v2');
		jQuery.ajax({
			url: ajaxurl,
			data: 'action=userpro_verify_user&user_id=' + link.data('user'),
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				parent.hide().html( data.admin_tpl ).fadeIn();
			}
		});
		return false;
	});
	
	/* Unverify user */
	jQuery('.upadmin-unverify-u').live('click',function(e){
		e.preventDefault();
		var link = jQuery(this);
		var parent = jQuery(this).parents('.upadmin-verify-v2');
		jQuery.ajax({
			url: ajaxurl,
			data: 'action=userpro_unverify_user&user_id=' + link.data('user'),
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				parent.hide().html( data.admin_tpl ).fadeIn();
			}
		});
		return false;
	});
	
	/* Verification invite */
	jQuery('.upadmin-invite-u').live('click',function(e){
		e.preventDefault();
		var link = jQuery(this);
		var parent = jQuery(this).parents('.upadmin-verify-v2');
		jQuery.ajax({
			url: ajaxurl,
			data: 'action=userpro_verify_invite&user_id=' + link.data('user'),
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				parent.hide().html( data.admin_tpl ).fadeIn();
			}
		});
		return false;
	});
	
	/* Verify user */
	jQuery('.upadmin-verify').live('click',function(e){
		e.preventDefault();
		var link = jQuery(this);
		var parent = jQuery(this).parents('.upadmin-pending-verify');
		jQuery.ajax({
			url: ajaxurl,
			data: 'action=userpro_verify_user&user_id=' + jQuery(this).data('user'),
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				parent.fadeOut();
				if (data.count === '0' || data.count == '' || !data.count) {
					jQuery('.upadmin-bubble-new').remove();
				} else {
					jQuery('.upadmin-bubble-new').html( data.count );
				}
				jQuery('.toplevel_page_userpro').find('span.update-count').html( data.count );
			}
		});
		return false;
	});
	
	/* Unverify user */
	jQuery('.upadmin-unverify').live('click',function(e){
		e.preventDefault();
		var link = jQuery(this);
		var parent = jQuery(this).parents('.upadmin-pending-verify');
		jQuery.ajax({
			url: ajaxurl,
			data: 'action=userpro_unverify_user&user_id=' + jQuery(this).data('user'),
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				parent.fadeOut();
				if (data.count === '0' || data.count == '' || !data.count) {
					jQuery('.upadmin-bubble-new').remove();
				} else {
					jQuery('.upadmin-bubble-new').html( data.count );
				}
				jQuery('.toplevel_page_userpro').find('span.update-count').html( data.count );
			}
		});
		return false;
	});
	
	/* Mouseenter/leave verify user */
	jQuery('.upadmin-unverify,.upadmin-verify').live('mouseenter',function(){
		jQuery(this).find('span').show();
	}).live('mouseleave',function(){
		jQuery(this).find('span').hide();
	});

	/* cancel field editing */
	jQuery('.upadmin-field-zone-cancel').live('click',function(e){
		e.preventDefault();
		jQuery(this).parents('.upadmin-field-zone').hide();
		return false;
	});
	
	/* chosen select */
	jQuery(".chosen-select").chosen({
		disable_search_threshold: 10
	});
	
	/* Setup field options (multi choice) */
	jQuery('#upadmin_n_type').live('change',function(){
		var type = jQuery(this).val();
		if( type == 'select' || type == 'multiselect' || type == 'radio' || type == 'radio-full' || type == 'checkbox' || type == 'checkbox-full' ) {
			jQuery('.choicebased').show();
		} else {
			jQuery('.choicebased').hide();
		}
		if ( type == 'file' ) {
			jQuery('.filetypes').show();
		} else {
			jQuery('.filetypes').hide();
		}
	});
	
	/* Custom input show/hide */
	if (jQuery('#dashboard_redirect_users').val()==2){
		jQuery('#dashboard_redirect_users').parents('td').find('.userpro-admin-hide-input').css({'display':'block'});
	}
	if (jQuery('#profile_redirect_users').val()==2){
		jQuery('#profile_redirect_users').parents('td').find('.userpro-admin-hide-input').css({'display':'block'});
	}
	jQuery('#dashboard_redirect_users,#profile_redirect_users').change(function(){
		if (jQuery(this).val() == 2) {
			jQuery(this).parents('td').find('.userpro-admin-hide-input').css({'display':'block'});
		} else {
			jQuery(this).parents('td').find('.userpro-admin-hide-input').css({'display':'none'});
		}
	});
	
	/* the main field list actions */
	jQuery('#upadmin-sortable-fields .upadmin-field-actions a').live('click',function(e){
		e.preventDefault();
		var act = jQuery(this).attr('class');
		var field = jQuery(this).parents('li').attr('id').replace('upadmin-','');
		var load = jQuery(this).parents('.upadmin-fieldlist').find('.upadmin-loader');
		
		if (act == 'upadmin-field-action-remove') {
			if (!confirm('Are you sure you want to delete field from your fields list?')) return false;
			load.addClass('loading');
			jQuery(this).parents('li').fadeOut();
			jQuery.ajax({
				url: ajaxurl,
				data: 'action=userpro_delete_field&field=' + field,
				dataType: 'JSON',
				type: 'POST',
				success:function(data){
					load.removeClass('loading');
					jQuery('span.upadmin-ajax-fieldcount').html( data.count );
				}
			});
		}
		
		if (act == 'upadmin-field-action-edit') {
			jQuery(this).parents('li').find('.upadmin-field-zone').toggle();
		}
		
		return false;
	});
	
	/* blur field edit */
	jQuery('#upadmin-sortable-fields .upadmin-field-zone input, #upadmin-sortable-fields .upadmin-field-zone select, #upadmin-sortable-fields .upadmin-field-zone textarea').live('change',function(){
		var load = jQuery(this).parents('.upadmin-fieldlist').find('.upadmin-loader');
		load.addClass('loading');
		var field = jQuery(this).parents('li').attr('id').replace('upadmin-','');		
		var str = '';
		jQuery(this).parents('li').find('input[type=text]').each(function(){
			str = str + '&' + jQuery(this).attr('id').replace(field + '-','') + '=' + jQuery(this).val();
		});
		jQuery(this).parents('li').find('textarea').each(function(){
			str = str + '&' + jQuery(this).attr('id').replace(field + '-','') + '=' + jQuery(this).val();
		});

		jQuery.ajax({
			url: ajaxurl,
			data: 'action=userpro_update_field&field=' + field + str,
			//dataType: 'JSON',
			type: 'POST',
			success:function(data){
				load.removeClass('loading');
			}
		});
		
	});

	/* click on action of field */
	jQuery('.upadmin-groups .upadmin-field-actions a').live('click',function(e){
		e.preventDefault();
		var form = jQuery(this).parents('.upadmin-tpl').find('form');
		var act = jQuery(this).attr('class');
		var proc = jQuery(this).data('proc');
		if (act == 'upadmin-field-action-remove') {
			if (!confirm('Are you sure you want to delete field from this group?')) return false;
			jQuery(this).parents('li').fadeOut(function(){jQuery(this).remove(); form.trigger('submit');});
		}
		if (act == 'upadmin-field-action-edit') {
			jQuery(this).parents('li').find('.upadmin-field-zone').toggle();
		}
		if (act == 'upadmin-field-action upadmin-field-action-hideable off') {
			jQuery(this).removeClass('off').addClass('on');jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(1);form.trigger('submit');}
		if (act == 'upadmin-field-action upadmin-field-action-hideable on') {
			jQuery(this).removeClass('on').addClass('off');jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(0);form.trigger('submit');}
		if (act == 'upadmin-field-action upadmin-field-action-hidden off') {
			jQuery(this).removeClass('off').addClass('on');jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(1);form.trigger('submit');}
		if (act == 'upadmin-field-action upadmin-field-action-hidden on') {
			jQuery(this).removeClass('on').addClass('off');jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(0);form.trigger('submit');}
		if (act == 'upadmin-field-action upadmin-field-action-required off') {
			jQuery(this).removeClass('off').addClass('on');jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(1);form.trigger('submit');}
		if (act == 'upadmin-field-action upadmin-field-action-required on') {
			jQuery(this).removeClass('on').addClass('off');jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(0);form.trigger('submit');}
		if (act == 'upadmin-field-action upadmin-field-action-locked off') {
			jQuery(this).removeClass('off').addClass('on');jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(1);form.trigger('submit');}
		if (act == 'upadmin-field-action upadmin-field-action-locked on') {
			jQuery(this).removeClass('on').addClass('off');jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(0);form.trigger('submit');}
		if (act == 'upadmin-field-action upadmin-field-action-private off') {
			jQuery(this).removeClass('off').addClass('on');jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(1);form.trigger('submit');}
		if (act == 'upadmin-field-action upadmin-field-action-private on') {
			jQuery(this).removeClass('on').addClass('off');jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(0);form.trigger('submit');}
		if (act == 'upadmin-field-action upadmin-field-action-html off') {
			jQuery(this).removeClass('off').addClass('on');jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(1);form.trigger('submit');}
		if (act == 'upadmin-field-action upadmin-field-action-html on') {
			jQuery(this).removeClass('on').addClass('off');jQuery(this).parents('li').find('input[name=' + jQuery(this).data('key') + '-' + jQuery(this).data('role') + ']').val(0);form.trigger('submit');}
		return false;
	});
	
	/* blur field edit */
	jQuery('.upadmin-groups .upadmin-field-zone input, .upadmin-groups .upadmin-field-zone select, .upadmin-groups .upadmin-field-zone textarea').live('change',function(){
		var form = jQuery(this).parents('.upadmin-tpl').find('form');
		form.trigger('submit');
	});

	/* toggle adding new field */
	jQuery('.upadmin-toggle-new').live('click',function(e){
		e.preventDefault();
		var new_field = jQuery('.upadmin-new');
		if (new_field.is(':hidden')){
		new_field.show();
		
			/* chosen dropdowns */
			jQuery(".upadmin-new select").removeClass("chzn-done").css('display', 'inline').data('chosen', null);
			jQuery("*[class*=chzn], .chosen-container").remove();
			jQuery(".upadmin-new .chosen-select").chosen({
				disable_search_threshold: 10
			});
			
		}else{
		new_field.hide();
		}
		return false;
	});
	
	/* icon clicks */
	jQuery('.upadmin-icon-abs a:not(.upadmin-noajax)').live('click',function(e){
		e.preventDefault();
		return false;
	});

	/* toggle/un-toggle field groups */
	jQuery('.upadmin-icon-abs a.max').live('click',function(){
		var tpl = jQuery(this).parents('.upadmin-tpl');
		tpl.find('.upadmin-tpl-body').removeClass('max').addClass('min');
		tpl.find('.upadmin-tpl-head').removeClass('max').addClass('min');
		jQuery(this).removeClass('max').addClass('min');
	});
	jQuery('.upadmin-icon-abs a.min').live('click',function(){
		var tpl = jQuery(this).parents('.upadmin-tpl');
		tpl.find('.upadmin-tpl-body').removeClass('min').addClass('max');
		tpl.find('.upadmin-tpl-head').removeClass('min').addClass('max');
		jQuery(this).removeClass('min').addClass('max');
	});
	
	/* cancel new field div */
	jQuery('#upadmin_n_cancel').live('click',function(e){
		e.preventDefault();
		var new_field = jQuery('.upadmin-new');
		new_field.hide();
		return false;
	});
	
	/* reset original fields */
	jQuery('.upadmin-reset-fields').live('click',function(e){
		e.preventDefault();
		form = jQuery(this).parents('.upadmin-fieldlist');
		if (!confirm('This will restore original plugin fields. Are you sure?')) return false;
		form.find('.upadmin-loader').addClass('loading');
		jQuery.ajax({
			url: ajaxurl,
			data: 'action=userpro_restore_builtin_fields',
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				form.find('.upadmin-loader').removeClass('loading');
				jQuery('span.upadmin-ajax-fieldcount').html( data.count );
				jQuery('ul#upadmin-sortable-fields').html( data.html );
			}
		});
		return true;
	});

	/* reset all groups */
	jQuery('.upadmin-reset-groups').live('click',function(e){
		e.preventDefault();
		form = jQuery(this).parents('.upadmin-groups');
		if (!confirm('This will restore original fields for ALL groups. Are you sure?')) return false;
		form.find('.upadmin-loader').addClass('loading');
		jQuery.ajax({
			url: ajaxurl,
			data: 'action=userpro_restore_builtin_groups',
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				form.find('.upadmin-loader').removeClass('loading');
				jQuery('.upadmin-groups-view').html( data.html );
				jQuery('.upadmin-tpl-body ul').sortable({
					receive: function(e,ui) {
						copyHelper= null;
					}
				});
			}
		});
		return true;
	});
	
	/* Publish new field */
	jQuery('.upadmin-new form').live('submit',function(e){
		e.preventDefault();
		form = jQuery(this);
		form.find('span.error-text').remove();
		form.find('input').removeClass('error');
		form.parents('.upadmin-fieldlist').find('.upadmin-loader').addClass('loading');
		jQuery.ajax({
			url: ajaxurl,
			data: form.serialize() + '&action=userpro_create_field',
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				form.parents('.upadmin-fieldlist').find('.upadmin-loader').removeClass('loading');
				if (data.error){
					jQuery.each( data.error, function(i, v) {
						jQuery('#'+i).addClass('error').focus().after('<span class="error-text">'+v+'</span>');
					});
				} else {
					form.find('input').removeClass('error');
					jQuery('ul#upadmin-sortable-fields').prepend( data.html );
					jQuery('span.upadmin-ajax-fieldcount').html( data.count );
				}
			}
		});
		return false;
	});
	
	/* reset single group */
	jQuery('.upadmin-tpl a.resetgroup').live('click',function(e){
		e.preventDefault();
		if (!confirm('This will restore original fields for this GROUP. Are you sure?')) return false;
		var form = jQuery(this).parents('.upadmin-tpl').find('form');
		var role = form.data('role');
		form.find('.upadmin-tpl-head').append('<img src="'+form.data('loading')+'" alt="" class="upadmin-miniload" />');
		jQuery.ajax({
			url: ajaxurl,
			data: form.serialize() + '&action=userpro_reset_group&role='+role,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				form.find('.upadmin-miniload').remove();
				form.parents('.upadmin-tpl').replaceWith( data.html );
				form.parents('.upadmin-tpl').find('.upadmin-tpl-body ul').sortable({
					receive: function(e,ui) {
						copyHelper= null;
					}
				});
			}
		});
		return false;
	});
	
	/* Save forms */
	jQuery('.upadmin-tpl a.saveform').live('click',function(){
		form = jQuery(this).parents('.upadmin-tpl').find('form');
		form.trigger('submit');
	});
	
	jQuery('.upadmin-tpl form').live('submit',function(e){
		e.preventDefault();
		form = jQuery(this);
		var role = jQuery(this).data('role');
		var group = jQuery(this).data('group');

		form.find('.upadmin-tpl-head').append('<img src="'+form.data('loading')+'" alt="" class="upadmin-miniload" />');
		
		jQuery.ajax({
			url: ajaxurl,
			data: form.serialize() + '&action=userpro_save_group&role='+role+'&group='+group,
			dataType: 'JSON',
			type: 'POST',
			success:function(data){
				form.find('.upadmin-miniload').remove();
			}
		});
		return false;
	});

	/* The groups that will receive fields */
	jQuery('.upadmin-tpl-body ul').sortable({
        receive: function(e,ui) {
            copyHelper= null;
			var list = jQuery(this).parents('.upadmin-tpl-body');
			jQuery.each( list.find("li[data-special^='newsection']"), function(i, v){
				section_word = 'newsection' + i;
				jQuery(this).data('special', section_word);
				jQuery(this).find('input').each(function(){
					jQuery(this).attr('name', jQuery(this).attr('name').replace('newsection', section_word));
					jQuery(this).attr('id', jQuery(this).attr('id').replace('newsection', section_word));
				});
			});
        }
	});
	
	/* Add new section field */
	jQuery('ul#upadmin-newsection').sortable({
		connectWith: ".upadmin-tpl-body ul",
		forcePlaceholderSize: false,
		helper: function(e,li) {
			copyHelper= li.clone().insertAfter(li);
			return li.clone();
		},
		stop: function() {
			copyHelper && copyHelper.remove();
		}
	});
	
	/* Moving out field/sorting between fields */
	var itemList = jQuery('ul#upadmin-sortable-fields');
	itemList.sortable({
		connectWith: ".upadmin-tpl-body ul",
		forcePlaceholderSize: false,
		helper: function(e,li) {
			copyHelper= li.clone().insertAfter(li);
			return li.clone();
		},
		stop: function() {
			copyHelper && copyHelper.remove();
		},
        update: function(event, ui) {
            opts = {
                url: ajaxurl,
                type: 'POST',
                async: true,
                cache: false,
                dataType: 'json',
                data:{
                    action: 'userpro_field_sort',
                    order: itemList.sortable('toArray').toString()
                },
                success: function(data) {
                    return; 
                },
                error: function(xhr,textStatus,e) {
                    return; 
                }
            };
            jQuery.ajax(opts);
        }
	});
	
});