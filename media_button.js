jQuery(function($) {
    $(document).ready(function(){
			 if ($('.add_media:visible').length==1){
				 $('.sparevideo_button').css('visibility','visible');
			 }
			 $('.add_media').click(open_sparevideos_window);
    });

    function open_sparevideos_window() {
		
     if (wp.media && $(this).hasClass( "sparevideo_button" )) {
            
			wp.media.view.Modal.prototype.on("open", function() {

                 $('.media-menu .media-menu-item').hide();
				 $('.media-menu .separator').next().next('.media-menu-item').show();	
					if (!$('.sparevideo_button').hasClass( "sparevideos_overlay_open" )) {
				 		$(".media-frame-menu .media-menu-item").click();
						$('.sparevideo_button').addClass( "sparevideos_overlay_open" );
					}
				 $('.media-menu .media-menu-item.active').show();
				 $('.media-menu .separator').css('margin','0px');
				 $('.media-menu .separator').css('border','0px');
            });
			
	 } else {
		 
		 wp.media.view.Modal.prototype.on("open", function() {
		    $('body .media-menu .media-menu-item').not(".hidden").show();	
            $('.media-menu .separator').next().next('.media-menu-item').show();			
		    // $(".media-frame-menu .media-menu-item:first-child").click();
         });
		 
	 }
}
});