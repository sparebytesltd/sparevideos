jQuery(document).ready(function($){
    
	/********************************************/
    /**             COLOR PICKER               **/
    /********************************************/
    function initColorPicker(parent){
	$('.sparevideosMMColorPickerContainer', parent).each(function(){
	    var mySelf = $(this);
	    var colorWheel = $('.sparevideosMMColorPicker', this);
	    var colorInput = $('.sparevideosMMColor', this);
	    colorWheel.hide();
	    $('.sparevideosMMColorPicker .sparevideosMMColorPickerSelect', this).farbtastic(colorInput);
	    var sparevideosMMColorPicker = $.farbtastic($('.sparevideosMMColorPicker .sparevideosMMColorPickerSelect', this));
	    sparevideosMMColorPicker.setColor('#'+colorInput.val());
	    //Select Color (Open Color Wheel)
	    colorInput.click(function(){
		colorWheel.css({'top' : $(this).position().top-colorWheel.height()*0.5, 'left' : $(this).position().left}).fadeIn();
	    });
	    //Close Color Wheel
	    $('.sparevideosMMColorPickerClose', this).click(function(){
		colorInput.val(sparevideosMMColorPicker.color);
		$(this).parent().fadeOut();
	    });
	    //Close Color Wheel
	    $('.sparevideosMMColorPickerCancel', this).click(function(){
		sparevideosMMColorPicker.setColor('#ffffff');
		colorInput.val('');
		$(this).parent().fadeOut();
	    });
	    //Reset Color to Default
		
	    $('.sparevideosMMResetColor', this).click(function(e){
		var sparevideosWPColor_default ='#ffffff';	
		e.preventDefault();
		sparevideosMMColorPicker.setColor(sparevideosWPColor_default);
		colorInput.val(sparevideosWPColor_default).css('background-color', sparevideosWPColor_default);
		updateMe(parent, true);
	    });
	});
    }
   
       /******************************************/
    /**              SOUNDCLOUD              **/
    /******************************************/
    //Attach Events for Player Preview and Shortcode
    $('.sparevideosMMMainWrapper').each(function(){
	var mySelf = $(this);
	//On changing settings
	
	//Initialize color Picker
	initColorPicker(mySelf);
	//(Tab View) Event: Load First Time preview when show clicked
	
	/*if(!mySelf.hasClass('sparevideosMMOptions')){
	    $(".describe-toggle-on", mySelf.parent()).click(function(){
          updateMe(mySelf, true);
	    });
	}*/
    });
	
	
	

	
    /**INIT **/
    $(".sparevideosMMLoading").css('display', 'none');

    $("#sparevideosMMShowUsernames").click(function(e){
	e.preventDefault();
	$("#sparevideosMMUsermameTab").slideDown('fast');
	$("#sparevideosMMHideUsernames").removeClass('hidden');
	$(this).addClass('hidden');
    });
    $("#sparevideosMMHideUsernames").click(function(e){
	e.preventDefault();
	$("#sparevideosMMUsermameTab").slideUp('fast');
	$("#sparevideosMMShowUsernames").removeClass('hidden');
	$(this).addClass('hidden');
    });


});
