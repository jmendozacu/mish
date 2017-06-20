jQuery(document).ready(function(jQuery){
	//open popup
    jQuery('#cd-popup-trigger-confirm').on('click', function(event){
		event.preventDefault();
        jQuery('#cd-popup-confirm').addClass('is-visible');
	});

	//close popup
    jQuery('#cd-popup-confirm').on('click', function(event){
		if( jQuery(event.target).is('.cd-popup-close') ||jQuery(event.target).is('.cd-popup') ||jQuery(event.target).is('.cd-popup-cancel') ) {
			event.preventDefault();
            jQuery(this).removeClass('is-visible');
		}
	});

    jQuery('#cd-popup-trigger-cost').on('click', function(event){
        event.preventDefault();
        jQuery('#cd-popup-cost').addClass('is-visible');
    });

    //close popup
    jQuery('#cd-popup-cost').on('click', function(event){
        if( jQuery(event.target).is('.cd-popup-close') ||jQuery(event.target).is('.cd-popup') ||jQuery(event.target).is('.cd-popup-cancel') ) {
            event.preventDefault();
            jQuery(this).removeClass('is-visible');
        }
    });

	//close popup when clicking the esc keyboard button
    jQuery(document).keyup(function(event){
    	if(event.which=='27'){
            jQuery('#cd-popup-confirm').removeClass('is-visible');
            jQuery('#cd-popup-cost').removeClass('is-visible');
	    }
    });
});