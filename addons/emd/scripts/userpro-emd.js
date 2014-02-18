/* Custom JS starts here */
jQuery(document).ready(function() {

	/**
		custom radio buttons
	**/
	jQuery('.emd-filters input[type=radio]').live('click',function(){
		var field = jQuery(this).parents('.emd-filter');
		field.find('span').removeClass('checked');
		jQuery(this).parents('label').find('span').addClass('checked');
	});
	
	/**
		custom checkbox buttons
	**/
	jQuery('.emd-filters input[type=checkbox]').live('change',function(){
		if (jQuery(this).is(':checked')) {
			jQuery(this).parents('label').find('span').addClass('checked');
		} else {
			jQuery(this).parents('label').find('span').removeClass('checked');
		}
	});
	
	/**
	masonry
	**/
	jQuery('.emd-list').imagesLoaded( function(){
		jQuery(this).isotope({
			itemSelector: '.emd-user',
			layoutMode: 'masonry',
		});
	});
	
});