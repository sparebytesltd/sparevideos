<?php
/*
 * Plugin Name: SpareVideos
 * Description: SpareVideos
 * Author: SpareVideos.com
 * Version: 1.0.6
 */
 
define ('SVID_PLUGIN_DIR', WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) );
define ('SVID_THUMB_URL', 'https://www.sparevideos.com/thumbnail');
define ('SVID_THUMB_SIZE', '480');


function svidf_custom_media_upload_tab_name( $tabs ) {    	
	$options = get_option('sparevideo_options');	
	$current_user = wp_get_current_user();
	 
	if( current_user_can('administrator') || array_intersect($options['who_can_use'], $current_user->roles))
	{		     
		$newtab = array( 'spare_video' => 'Insert Video');	
	}

	
    return array_merge( $tabs, $newtab );
}

add_action( 'media_upload_spare_video', 'svidf_olab_tab_iframe' );

function svidf_olab_tab_iframe() {
	
	$options = get_option('sparevideo_options');	
	$current_user = wp_get_current_user();
	 
	if( trim($current_user->roles[0])!='administrator' && !array_intersect($options['who_can_use'], $current_user->roles))
	{		     
		exit;
	}

    
    wp_enqueue_style('sparevideos-css',plugins_url('/sparevideos-css.css',__FILE__));      
    wp_enqueue_script('sparevideos-js-print', plugins_url('/sparevideos-css.css',__FILE__), array('jquery', 'farbtastic'));
	wp_enqueue_script('sparevideos-js-carou', plugins_url('/assets/jquery.carouFredSel-6.2.1.js',__FILE__), array('jquery'));
    @wp_iframe( 'svidf_custom_media_upload_tab_name' );
}
add_filter( 'media_upload_tabs', 'svidf_custom_media_upload_tab_name' );

function svidf_custom_media_upload_tab_content() {
?>
<script type="text/javascript">
    			
	jQuery(function($){		
	    	
		$('.media-item').each(function(){
			
			  $(this).find('.describe-toggle-on').click(function(){
				 
				  $('.media-item').removeClass('open');
				  $('.slidetoggle').hide();
				  $('this').parent('.media-item').addClass('open');				  
			  
			  });
			  
		});
		
		$('a[sec_id="video_upload"]').click(function(){
			     $('#remote_url').val('');
				 $('#upload_form_prog').html('');			
                 $('#upload_form_prog').removeClass('success_msg');
		});
		
		$('.media-menu-item').click(function(){
			 $('#upload_form_prog').html('');
  			  var selector = $(this).attr('sec_id');
			   $('.media-menu-item').removeClass('active');
			  $(this).addClass('active');			 
			  $('.sparevideo_sections').removeClass('active');
			  $('#'+selector).addClass('active');
			  $('#upload_form_prog').hide();
              $('#upload_form').show();
			  
		});
				
		$('.media_lib1').click(function(){
			 var data11 = {
							 action: 'load_videos',
                             post_id: <?php echo strip_tags($_GET['post_id']); ?>,
				 			 _wpnonce: '<?php echo wp_create_nonce( 'load_videos_'.$_GET['post_id'] ); ?>'
                            };
			$('#media_lib').html('<p style="display:table; vertical-align:middle; text-align:center;    min-width: 100%; margin:20% 0px 0px 0px;"><img src="<?php echo admin_url();?>/images/loading.gif" style="width:20px;"></p>');
		   $.ajax({  
											type: "POST",                               
											url: "admin-ajax.php", 
											data: data11,								
											success: function(status_upd) {        
											   $('#media_lib').html(status_upd);
											   return false;  
											}
							
		   });	
	    });
		<?php
		$ot = (!$opened_track) ? 'false' : $opened_track;
		echo 'var opened_track = '.$ot.';';
		?>
		var preloaded = $(".media-item.preloaded");
		var track_opened = false;
		if ( preloaded.length > 0 ) {
				preloaded.each(function(i){
			prepareMediaItem({id:this.id.replace(/[^0-9]/g, '')},'');
			if(opened_track!= false && !track_opened){
			  myID = this.id.match(/[0-9]+./);
			  if(opened_track == myID) {
				$('#show-'+myID).trigger("click");
				track_opened = true; // just so we don't keep on running this for nothing
			  }
			}			
		  });
			}
		});
	
	   
		
    </script>


<div id="spareVideoTabs" class="media-frame-router" style="float:left;width:98%; position:relative; top:14px;left:6px;">
	<div class="media-router">
	  <a href="javascript:void(0)" sec_id="video_upload" class="media-menu-item active">Upload Files</a>
	  <a href="javascript:void(0)" sec_id="media_lib" class="media_lib1 media-menu-item">Media Library</a>
	</div>
</div>
<script type="text/javascript">	
  jQuery(document).ready( function($) {
	sectionsElementOffset = jQuery('#spareVideoTabs').offset().top + jQuery('#spareVideoTabs').height();
	availHeight = window.innerHeight - sectionsElementOffset - 40; // 40 is the space of the submit/save settings line
	jQuery('.sparevideo_sections').css('height',availHeight+'px');
  });
</script>

<?php
   
   global $wpdb,$post;  
		
	$querystr = "
	SELECT  *
	FROM wp_sparevideo 
	WHERE wp_sparevideo.video_code='".strip_tags($_REQUEST['sparevideo_options']['video_code'])."'";
	
	$pageposts = $wpdb->get_results($querystr, OBJECT);		 
    $current_user         =  wp_get_current_user();	
												  
	$queryforuseronly = trim($current_user->roles[0])=='administrator'?'':'user_id='.$current_user->ID;
	$queryforuseronlyAND = trim($current_user->roles[0])=='administrator'?'':' AND';
	 
    if(  isset($_REQUEST['save_settings']) &&  $_REQUEST['save_settings']!='' ){
		
	  check_admin_referer( 'save_settings');
	  global $wpdb;			      
	  
	  $wpdb->query("UPDATE wp_sparevideo SET video_settings ='".serialize(strip_tags($_REQUEST['sparevideo_options']))."' WHERE ".$queryforuseronly.$queryforuseronlyAND." id=".strip_tags($_REQUEST['sparevideo_options']['video_id']));			 
	
	  ?>
	  <script type="text/javascript">	
	  jQuery(document).ready( function($) {
      		  $('.media-menu-item').removeClass('active');
			  $('.media_lib1').addClass('active');			 
			  $('.sparevideo_sections').removeClass('active');
			  $('#media_lib').addClass('active');
			 
			 var data11 = {
							 action: 'load_videos',
                             post_id: <?php echo strip_tags($_REQUEST['post_id']); ?>,
                             show_media: '<?php echo strip_tags($_REQUEST['media_view']); ?>',
                             paged: <?php echo strip_tags($_REQUEST['paged']); ?>,
				 			 _wpnonce: '<?php echo wp_create_nonce( 'load_videos_'.$_REQUEST['post_id'] ); ?>'
                            };
							
			$('#media_lib').html('<p style="display:table; vertical-align:middle; text-align:center;    min-width: 100%; margin:20% 0px 0px 0px;"><img src="<?php echo admin_url();?>/images/loading.gif" style="width:20px;"></p>');
		       
			$.ajax({  
											type: "POST",                               
											url: "admin-ajax.php", 
											data: data11,								
											success: function(status_upd) {        
											   $('#media_lib').html(status_upd);
											   return false;  
											}
							
		     });	
		  });	
		 </script>
       <?php	   
			
   } 
   
   // Setting as video page in the media library   
   else if( isset($_REQUEST['set_as_video_page']) &&  $_REQUEST['set_as_video_page']!='' ) {
	   	   						
		update_post_meta( strip_tags($_GET['post_id']), 'video_page_'.strip_tags($_GET['post_id']), strip_tags($_REQUEST['sparevideo_options']['video_code']));	
		set_post_format(strip_tags($_GET['post_id']), 'video' );
        echo '<script type="text/javascript">	  	
	    jQuery(document).ready( function($) { 
		parent.jQuery(".media-modal-close").click(); window.top.location.href="'.get_admin_url().'/post.php?post='.strip_tags($_GET['post_id']).'&action=edit" });</script>';
   }
   
   // Removing as video page from the media library   
   else if( isset($_REQUEST['remove_video_page']) &&  $_REQUEST['remove_video_page']!='' ){
	   	    
		update_post_meta( strip_tags($_GET['post_id']), 'video_page_'.strip_tags($_GET['post_id']), 0 );	
		set_post_format(strip_tags($_GET['post_id']), 0 );
		 echo '<script type="text/javascript">	  	
	    jQuery(document).ready( function($) { 
		parent.jQuery(".media-modal-close").click();window.top.location.href="'.get_admin_url().'/post.php?post='.strip_tags($_GET['post_id']).'&action=edit" });</script>';
   }
  
?>

<div id="media_lib" class="sparevideo_sections">


</div>
	
<div id="video_upload" class="active sparevideo_sections">

    
	<?php // Video Upload form start here ?>
	<div id="upload_form_prog"></div>
	<form action="" method="post" id="upload_form"  enctype="multipart/form-data">
	   <label><b>Insert Video URL&nbsp;&nbsp;<b><br>
	   <input type="text" name="remote_url" id="remote_url">
	   <input type="hidden" name="video_progress" id="video_progress" value="0">
	   </label>	   <br>	  <input type="button" value="Upload" name="video_upload" id="video_upload_butt" class="button button-primary button-large">	     
	   <br><br> -- OR Upload your local file --
       <br> 
	   <div class="fileUpload btn btn-primary"><span>Select File</span>
       <input type="file" name="fileSelect" id="fileSelect" value=""><br>   
	   </div>	  
	</form>	
    <?php // Video Upload form end here ?>
	
	<script type="text/javascript">	  	
	   jQuery(document).ready( function($) {
         
         $('#fileSelect').change(function(){
			 $('#video_upload_butt').click();
		 });
		 
		 $("#video_upload_butt").click( function() {
											 
				var get_remote_url = $('#remote_url').val();
				
				var data = {
							action: 'video_init',
							_wpnonce: '<?php echo wp_create_nonce( 'video_init_'.$_REQUEST['post_id'] ); ?>'
                };
				
				$('#upload_form').hide();
				$('#upload_form_prog').html('Upload is in progress...').show();
				
				$.ajax({
				// Video upload initialization	
				type: "POST",
				url: "admin-ajax.php",				
				data: data,
			
						success: function(result) {					  
						  
						  jq_json_obj = $.parseJSON(result);
                            
						    var data = {
							 action: 'video_upload',
							 post_id : '<?php echo intval($_REQUEST['post_id']); ?>',
                             remote_url: get_remote_url,
							 ref:jq_json_obj.ref,
							 server:jq_json_obj.server,
							 token:jq_json_obj.token,
                            };

						   
						   // Uploading video from local computer start here
						   
						   if( $('#fileSelect').val()!='' && $('#remote_url').val()=='' ){
							   
						      $('#upload_form_prog').html('<input type="hidden" name="video_progress" id="video_progress" value="0"><p id="video_progressbar">&nbsp;</p>');							  
							  var data = new FormData();
							  data.append('file', $('#fileSelect')[0].files[0]);		
                             
                               $.ajax({
									  url: jq_json_obj.server+'?ref='+jq_json_obj.ref+'&token='+jq_json_obj.token,
									  type: 'POST',
									  processData: false, // important
									  contentType: false, // important
									  data: data,
										xhr: function () {
										var xhr = $.ajaxSettings.xhr();
										xhr.upload.onprogress = function (e) {
											// For uploads
											
											if (e.lengthComputable) {
											  $("#video_progressbar").css('width', (Math.round(e.loaded / e.total*100))+'%');
											  $("#video_progress").attr("value", (Math.round(e.loaded / e.total*100)));
											}
										};
										return xhr;
										}
									}).done(function(data) {
									
                                    
									if(typeof  data.error != "undefined")
									{	
								       
									   $('#upload_form_prog').html('');$('#upload_form_prog').hide();
                                       $('#fileSelect').val();
									   $('#upload_form_prog').removeClass('success_msg');
                                       $('#upload_form').show();
									}	
									 if(data.error == 10001 )
									 {
										 alert("Fileserver process error"); return false;										
									 } else if(data.error == 10002) {
										 alert("Upload not valid"); return false;
									 }	
									 else if(data.error == 10004) {
										 alert("Internal system	error"); return false;
									 }
								     
									 var data_conversion = {
									  action: 'video_conversion',
									  file_code : data.code,	
                                      post_id : '<?php echo strip_tags($_REQUEST['post_id']); ?>',
									  local_upload: 'yes',
									  _wpnonce: '<?php echo wp_create_nonce( 'video_conversion_'.$_REQUEST['post_id'] ); ?>'
									};	
                                     
									// Video Conversion for local file upload 
                                    $.ajax({  
											type: "POST",                               
											url: "admin-ajax.php", 
											data: data_conversion,								
											success: function(status_upd) {  
                                           	   clearInterval(refreshId);								
											   $('.media_lib1').click();												   
											   return false;  
											}
							
							        });	
									}).fail(function (e) {
										alert(e.error);										
									});

							  
						   }
						   else {
						   
						  	// Video Upload from remote URL
							
							$('#upload_form_prog').html('<input type="hidden" name="video_progress" id="video_progress" value="0"><p id="video_progressbar">&nbsp;</p>');
							 
							var refreshId = window.setInterval(function(){
								      var repeat = $("#video_progress").attr("value"); 
									  
									  if (repeat<100){
										   getJquery(jq_json_obj.server+"/progress?ref="+jq_json_obj.ref, function(data){
										   $("#video_progressbar").css('width', (Math.round(data))+'%');
										   $("#video_progress").attr("value", (Math.round(data)));
									    });
                                     }else if(repeat==100){
										 								
										setTimeout(function(){
											
                                           $('#upload_form_prog').html('Video uploaded successfully').addClass('success_msg');										
                                           $('#upload_form_prog').addClass('success_msg');
												
										}, 1000);		

                                        								
										
										clearInterval(refreshId);
									 }
									  repeat = $("#video_progress").attr("value");		  							  
						    }, 1800);
						   
                          	$.ajax({  
								type: "POST",                               
								url: "admin-ajax.php", 
                                data: data,								
								success: function(status_upd) {        
                                  													 								  
                                  // Video Upload from remote URL   
								  if(status_upd.indexOf('error') != -1)
									{	
								       $('#remote_url').val('');
									   $('#upload_form_prog').html('');$('#upload_form_prog').hide();
                                       $('#fileSelect').val();
									   $('#upload_form_prog').removeClass('success_msg');
                                       $('#upload_form').show();
									}	
									 if(status_upd == 'error-10001' )
									 {
										 alert("Fileserver process error"); return false;										
									 } else if(status_upd == 'error-10002') {
										 alert("Upload not valid"); return false;
									 }	
									 else if(status_upd == 'error-10004') {
										 alert("Internal system	error"); return false;
									 }
								     
								  
								    // Video Conversion for remote URL   
								    var data_conversion = {
									  action: 'video_conversion',
									  file_code : status_upd,	
                                      post_id : '<?php echo strip_tags($_REQUEST['post_id']); ?>',
									  _wpnonce: '<?php echo wp_create_nonce( 'video_conversion_'.$_REQUEST['post_id'] ); ?>'
									};	
                                     
									 
                                    $.ajax({  
											type: "POST",                               
											url: "admin-ajax.php", 
											data: data_conversion,								
											success: function(status_upd) {  
                                           	   clearInterval(refreshId);								
											   $('.media_lib1').click();												   
											   return false;  
											}
							
							        });									
								   
								   
								   return false;  
								}
								
								 
								
						   });
						   
						 
						   } 
						   
						   
						  
						}
					 
					});
					return false;
				});
		 });			
		 function getJquery (formURL, callback){
		jQuery.ajax(
			{
				url : formURL,
				success:function(data, textStatus, jqXHR) 
				{
					callback(data);
				},
				error: function(jqXHR, textStatus, errorThrown) 
				{
					callback('{"status":"error","message":"Unexpected error"}');
				}});
		}

	</script>
</div>
<?php 
?>
<?php 
			
}
add_action( 'media_upload_spare_video', 'svidf_custom_media_upload_tab_content' );

add_action('media_buttons', 'svidf_add_my_media_button', 15);
function svidf_add_my_media_button() {
    echo '<button type="button" id="insert-media-button" class="button insert-media sparevideo_button add_media" data-editor="content"><span class="dashicons dashicons-format-video" style="font-size:18px; margin-top:4px; color:#82878c; -moz-osx-font-smoothing: grayscale;"></span> Add Video</button>';	
}
function svidf_include_media_button_js_file() {
    wp_enqueue_script('media_button', plugins_url('/media_button.js',__FILE__), array('jquery'), '1.0', true);
}
add_action('wp_enqueue_media', 'svidf_include_media_button_js_file');
/*** Add Admin Menu ***/


add_action('admin_menu', 'svidf_sparevideo_menu');
function svidf_sparevideo_menu() {
	
	  
	   
	$options = get_option('sparevideo_options');	
	$current_user = wp_get_current_user();
	
	if( empty($current_user->allcaps['level_2']) ) {    
     remove_menu_page( 'upload.php' );
	}
	 
	if( current_user_can('administrator'))
	{		     
		//Main
		add_menu_page('SpareVideos', 'SpareVideos', 'manage_options', 'spare_videos', 'svidf_sparevideos_options',SVID_PLUGIN_DIR.'/assets/images/video_icon.png',25 );
		add_submenu_page('spare_videos', 'Settings', 'Settings', 'manage_options', 'spare_videos' );
		add_submenu_page('spare_videos', 'Manage Videos', 'Manage Videos', 'manage_options', 'manage-videos','svidf_manage_videos' );
		
	} 
	else if( array_intersect($options['who_can_use'], $current_user->roles)){
		
		add_menu_page('SpareVideos', 'SpareVideos', 'read', 'spare_videos', 'svidf_manage_videos',SVID_PLUGIN_DIR.'/assets/images/video_icon.png',25 );				
		add_submenu_page('spare_videos', 'Manage Videos', 'Manage Videos', 'read', 'manage-videos','svidf_manage_videos' );		
	}
	
	/*else {	
	
		add_menu_page('SpareVideos', 'SpareVideos', 'edit_pages', 'spare_videos', 'svidf_manage_videos',SVID_PLUGIN_DIR.'/assets/images/video_icon.png',25 );		
		add_submenu_page('spare_videos', 'Manage Videos', 'Manage Videos', 'edit_pages', 'manage-videos','svidf_manage_videos' );		
		echo '<style type="text/css">#toplevel_page_spare_videos .wp-first-item{ display:none;}</style>';
		
	}*/
	add_action( 'admin_init', 'svidf_sparevideos_admin_init' );
	
}

// Manage videos section
function svidf_manage_videos(){
?>
<script type="text/javascript">
	jQuery(document).ready( function($) {		  
       
	     $('.delete_videos').click(function(){
			 var answer = confirm("Also remove posts");
			 if(answer){				 
				$('#delete_posts').val(1);
			 }			
		 });
		 
		 $('#cb-select-all-1').click(function(){
			 
			 if($(this).is(':checked'))
				$('.video_codes_check').attr('checked','checked');
			 else
				$('.video_codes_check').removeAttr('checked');
			 
		 })
		 
	   
	});
</script>
<?php
    global $wpdb;
	$spareVideo   =    new SpareVideo();
	$videodel     =    $spareVideo->VideoDelete(strip_tags($_REQUEST['video_codes']));
	
	if( isset($_REQUEST['video_codes']) && $_REQUEST['video_codes']!=''  ){
		
	    foreach(strip_tags($_REQUEST['video_codes']) as $video_code){
		  
		  $querystr = "SELECT  * FROM wp_sparevideo WHERE video_code='".$video_code."'";		  
		  $pageposts = $wpdb->get_results($querystr, OBJECT);	
		  
			$current_user         =  wp_get_current_user();	
		
			$queryforuseronly = trim($current_user->roles[0])=='administrator'?'':'user_id='.$current_user->ID;
			$queryforuseronlyAND = trim($current_user->roles[0])=='administrator'?'':' AND';
	 

		  foreach($pageposts as $pagepost){
			 
		    if( isset($_REQUEST['delete_posts']) && $_REQUEST['delete_posts']==1 )	{
			  			  
			  if ( get_post_status( $pagepost->post_id ) == 'publish' ) {
				wp_delete_post( $pagepost->post_id , false );   
			  } 
			  
			}
			$querystr = "DELETE from wp_sparevideo where ".$queryforuseronly.$queryforuseronlyAND." id=".$pagepost->id;$wpdb->get_results($querystr, OBJECT);	     
			
		  }	
			
		}
		
		echo '<br><div class="updated notice"><p>Seleted Videos have been removed successfully</p></div>';
    } else if( isset($_REQUEST['del']) && $_REQUEST['del']!='' && $_REQUEST['id']!='') {
		  
		 /* global $wpdb;
		  
		  $querystr = "SELECT  * FROM wp_sparevideo WHERE video_code='".strip_tags($_REQUEST['id'])."'";
		  
		  $pageposts = $wpdb->get_results($querystr, OBJECT);
		  

		  foreach($pageposts as $pagepost){
			 
		    if( isset(strip_tags($_REQUEST['delete_posts'])) && strip_tags($_REQUEST['delete_posts'])==1 )	{
			  wp_delete_post( $pagepost->post_id , false ); 
			}
			$querystr = "DELETE from wp_sparevideo where id=".$pagepost->id;$wpdb->get_results($querystr, OBJECT);	     
			
		  }	
		*/	
		
	}  		
?>
<p>&nbsp;</p>
<?php

// Video Thumbnail edit
if( isset($_REQUEST['id']) && $_REQUEST['id']!='' ){
 
  $spareVideo   =    new SpareVideo();
  
  if(isset($_REQUEST['thumb_id']) && $_REQUEST['thumb_id']!=''){
	  
      $sdsd = json_decode($spareVideo->VideoThumbnailEdit(strip_tags($_REQUEST['id']),strip_tags($_REQUEST['thumb_id'])));	 	 	 	 
	  echo '<script type="text/javascript">window.location.href = "admin.php?page=manage-videos&paged='.urlencode(strip_tags($_REQUEST['paged'])).'";</script>';
	  
  }
  
  $thumbvideo_    =    json_decode($spareVideo->VideoThumblist(strip_tags($_REQUEST['id'])));
  $thumbvideos    =    array_splice(get_object_vars($thumbvideo_), 0, 6);
    
  echo "<h2>Video Code: ".strip_tags($_REQUEST['id'])."</h2>";
  echo "<p>Click any one of the image below to set as default image</p>";
  
  foreach($thumbvideos as $thumbvideo){
  ?>	
   <a href="admin.php?page=manage-videos&id=<?php echo urlencode(strip_tags($_REQUEST['id'])); ?>&thumb_id=<?php echo $thumbvideo;  ?>&paged=<?php echo urlencode(strip_tags($_REQUEST['paged']));  ?>">
     <img src="<?php echo SVID_THUMB_URL; ?>/<?php echo strip_tags($_REQUEST['id']); ?>/exact/<?php echo $thumbvideo; ?>" class="thumb_edit">
   </a>
<?php	  
} 
} else {
// Manage video section start here		
?>  
<form action="" method="post">
<input type="submit" class="button-primary delete_videos" name="delete_videos" value="Delete">
<p style="margin:0px;">&nbsp;</p>
<table class="wp-list-table widefat fixed striped posts" style="width:99%;">
<thead>
  
	<tr>
		<td style="width:50px" id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox" scope="col"></td>
		<td scope="col" id="title" class="manage-column column-title column-primary sortable desc"><span>Video Code</span></td>
		<td scope="col" id="px_post_thumb" class="manage-column column-px_post_thumb">Author</td>
		<td scope="col" id="px_post_thumb" class="manage-column column-px_post_thumb">Uploaded to</td>
		<td scope="col" id="px_post_thumb" class="manage-column column-px_post_thumb">Date</td>
	</tr>
	</thead>
	<tbody id="the-list">
	
	
	<?php
	
	global $paged, $max_num_pages;
	
	$spareVideo   =  new SpareVideo();	 
	$video_thumb = json_decode($spareVideo->VideoGetthumb('1494404841-LJ86uC')); 			 
    $current_user = wp_get_current_user();
	

	$paged = (is_int(strip_tags($_REQUEST['paged']))) ? strip_tags($_REQUEST['paged']) : 1;
    $post_per_page = 15;
    $offset = ($paged - 1)*$post_per_page;
	
	global $wpdb;   	
	$querystr = "SELECT  * FROM wp_sparevideo";	
     
    if($current_user->roles[0]!='administrator')	 
	$querystr .= " WHERE user_id=".$current_user->ID;

	$querystr .= " ORDER BY wp_sparevideo.id DESC";    	
	
	$querystr .= " LIMIT ".$offset.", ".$post_per_page;
	
		
	
	$pageposts        = $wpdb->get_results($querystr, OBJECT);
	
	if($current_user->roles[0]!='administrator')	 
	$add_query  = " WHERE user_id=".$current_user->ID;
	
	$sql_posts_total = $wpdb->get_var( "SELECT count(*) FROM wp_sparevideo".$add_query );
	
    $max_num_pages    = ceil($sql_posts_total / $post_per_page);
	
	
	
	if(!empty($pageposts)){
	foreach( $pageposts as $pagepost  ){ 
	
	$get_thumb_url = 'https://www.sparevideos.com/thumbnail/'.$pagepost->video_code.'/240/default.jpg?t='.time();
	
	if (@getimagesize($get_thumb_url)) {
		$img_src = $get_thumb_url;
		$no_thumb = false;
	} else {
	  $img_src = SVID_PLUGIN_DIR.'/assets/images/no-thumbnail.png';	  
	  $no_thumb = true;
	}
	
	?>
	<tr>
    <td style="width:50px" ><input id="cb-select-2509" class="video_codes_check" type="checkbox" name="video_codes[]" value="<?php echo $pagepost->video_code; ?>"></td>
	<td class="title column-title has-row-actions column-primary">
	<strong class="has-media-icon" style="float:left;">
                <a href=""><span class="media-icon image-icon">
			     <img width="60" height="60" src="<?php echo $img_src; ?>" class="attachment-60x60 size-60x60" alt="">
			    </a>
	</strong>
	<div style="float:left; padding-left:10px;">
	   <?php 
	    if ($no_thumb) {
	    ?>
		<p class="filename"><?php echo $pagepost->video_code; ?><br><a href="#">Progress</a></p>
		<?php } else { ?>
		<p class="filename"><?php echo $pagepost->video_code; ?><br><a href="admin.php?page=manage-videos&id=<?php echo $pagepost->video_code ?>&paged=<?php echo urlencode(strip_tags($_REQUEST['paged'])); ?>">Edit</a></p>
		<?php } ?>
	</div>
	</td>
	<td><a href="<?php echo get_the_author_link($pagepost->user_id); ?>"><?php echo get_author_name($pagepost->user_id); ?></a></td>
	<td><a href="<?php echo get_permalink($pagepost->post_id) ?>"><?php echo get_the_title($pagepost->post_id);?></a></td>
	<td><?php echo $pagepost->date;?></td>
	</tr>
	<?php } } else {
	?>	
	<tr><td colspan="5">No Videos</td></tr>
    <?php		
	} ?>
	
	
	</tbody>
</table> 
<p style="margin:0px;">&nbsp;</p>	
<input type="hidden" name="delete_posts" id="delete_posts">
<input type="submit" class="button-primary delete_videos" name="delete_videos" value="Delete"></form>
<div class="navigation">
	 <?php svidf_wordpress_numeric_post_nav($paged,$max_num_pages,0); ?>
	</div>
<?php	
}	
}




add_action('admin_init', 'svidf_allow_contributor_uploads');
 
function svidf_allow_contributor_uploads() {
    
	$current_user = wp_get_current_user();
	
	

   if( empty($current_user->allcaps['level_2']) ) {    
     $contributor = get_role( $current_user->roles[0]);
     $contributor->add_cap('upload_files');	
     	
   } 
	
}

add_filter( 'media_view_strings', 'svidf_cor_media_view_strings' );
		
function svidf_cor_media_view_strings( $strings ) {
	
   $current_user   =  wp_get_current_user();          
   
   if( empty($current_user->allcaps['level_2']) ) {     
   
    unset( $strings['insertMediaTitle'] );
    unset( $strings['createNewGallery'] );
	unset( $strings['mediaLibraryTitle'] );
	unset( $strings['createGalleryTitle'] );
    unset( $strings['insertFromUrlTitle'] );
	unset( $strings['setFeaturedImageTitle']);
	
  }	
  
  //exit;
  
  return $strings;
	
}

function svidf_RemoveAddMediaButtonsForNonAdmins(){
	
	
	$options = get_option('sparevideo_options');	
	$current_user = wp_get_current_user();
	
	if( trim($current_user->roles[0])!='administrator'  ){
	 echo '<style type="text/css">#toplevel_page_spare_videos .wp-first-item{ display:none;}</style>';
	}

	if( array_intersect($options['who_can_use'], $current_user->roles) || current_user_can('administrator')){		
	   add_action( 'media_buttons', 'media_buttons' );	
	   echo '<style type="text/css">.sparevideo_button.add_media{display:inline-block !important; visibility:hidden;}</style>';	   
	} else {		
		remove_action( 'media_buttons', 'media_buttons' );
		echo '<style type="text/css">body .sparevideo_button.add_media{display:none !important;}</style>';		
	}
	
	if( @!array_intersect($options['who_can_use'], $current_user->roles) && !empty($current_user->allcaps['level_2']) && trim($current_user->roles[0])!='administrator'){		
	   add_action( 'media_buttons', 'media_buttons' );	
	   echo '<style type="text/css">body .sparevideo_button.add_media{display:none !important;}</style>';
	}
	 
	if( empty($current_user->allcaps['level_2']) ) {   
	    echo '<script type="text/javascript">	  	
	    jQuery(document).ready( function($) { 
		  //  if (wp.media) {
          //   wp.media.view.Modal.prototype.on("open", function() {						
          //     $(".media-frame-menu .media-menu-item").click();
          //   });
          //  }
		});</script>';		
		echo '<style type="text/css">.add_media{display:none !important;} .sparevideo_button.add_media{display:block !important;}</style>';
    } 
}
add_action('admin_head', 'svidf_RemoveAddMediaButtonsForNonAdmins');

function svidf_sparevideos_admin_init() {
	
    register_setting( 'sparevideo_options', 'sparevideo_options' );  
	
	wp_deregister_script('jquery');
    wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js', false, '1.4.4');
	
	// load our jquery file that sends the $.post request
	wp_enqueue_script( "ajax-test", plugin_dir_url( __FILE__ ) . '/assets/ajax_call.js', array( 'jquery' ) ); 
	// make the ajaxurl var available to the above script
	wp_localize_script( 'ajax-test', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );	
	
	
	wp_enqueue_style('sparevideos-css',plugins_url('/sparevideos-css.css',__FILE__));  	
	wp_enqueue_script('sparevideos-js-print', plugins_url('/assets/sparevideos-js.js',__FILE__), array('jquery', 'farbtastic'));
	wp_enqueue_script('sparevideos-js-carou', plugins_url('/assets/jquery.carouFredSel-6.2.1.js',__FILE__), array('jquery'));
	
	wp_enqueue_style('farbtastic');
	wp_enqueue_style('ChunkFive');
    wp_enqueue_style('Quicksand');
	 
	
	 
	if( isset($_REQUEST['remove_featured_image']) &&  $_REQUEST['remove_featured_image']!=''  ){
	   update_post_meta( strip_tags($_GET['post']), '_thumbnail_ext_url', '' );	   	  
   } 
	 
}

/**********************************************/
/**                                          **/
/**            THE OPTIONS PAGE              **/
/**                                          **/
/**********************************************/
function svidf_sparevideos_options(){ 
    $options = get_option('sparevideo_options');
					
		
    $api_key = isset($options['api_key']) ? $options['api_key'] : '';
    $api_token = isset($options['api_token']) ? $options['api_token'] : '';
    $who_can_use = isset($options['who_can_use']) ? $options['who_can_use'] : '';
    
	$auto_play = isset($options['auto_play']) ? 'checked=checked' : '';
	$enable_ad = isset($options['enable_ad']) ? 'checked=checked' : '';
	$is_always_detached = isset($options['is_always_detached']) ? 'checked=checked' : '';
    $is_detachable = isset($options['is_detachable']) ? 'checked=checked' : '';
	$detach_to     = isset($options['detach_to']) ? $options['detach_to'] : '';
	
	$custom_width = isset($options['custom_width']) ? $options['custom_width'] : '100%';	
	$custom_height = isset($options['custom_height']) ? $options['custom_height'] : '';
	$sparevideo_main_color = isset($options['sparevideo_main_color']) ? $options['sparevideo_main_color'] : '';
	$sparevideo_hover_color = isset($options['sparevideo_hover_color']) ? $options['sparevideo_hover_color'] : '';
	
	 if( isset($_REQUEST['settings-updated']) && $_REQUEST['settings-updated']!='' ){
	   
		$spareVideo   =  new SpareVideo();	   
		$request      = json_decode($spareVideo->UploadInit());	
     
		if($request->status=='error')
		{
		  echo "<p style='color:red;font-weight:bold;'>ERROR: API Key Pair ".$request->message."</p>";
		} 
	
   }
 

?>
 
  <div class="sparevideosMMWrapper sparevideosMMOptions sparevideosMMMainWrapper">
	
		<!-- Main -->
        <div id="sparevideosMMMain" class="">
            <form method="post" action="options.php" id="sparevideosMMMainForm" name="sparevideosMMMainForm" class="">
	    <p class="hidden sparevideosMMId" id="sparevideosMMId-<?php echo $soundcouldMMId ?>"><?php echo $soundcouldMMId ?></p>
            <?php 
			        settings_fields('sparevideo_options');
                    do_settings_sections( 'sparevideo_options' );
			?>
			<h1>SpareVideo Global Settings</h1>
                <ul id="sparevideosMMSettings">
              <!-- API Settings -->
                    <li class="sparevideosMMBox"><label class="optionLabel">API Key Pair</label>
                        <ul class="subSettings texts">
                            <li>
                              <input type="text" name="sparevideo_options[api_key]" value="<?php echo $api_key; ?>" style="width:20%;float:left;margin-left:15px;"><br><br>
							  <input type="text" name="sparevideo_options[api_token]" value="<?php echo $api_token; ?>" style="width:20%;float:left;margin-left:15px;">
                            </li>
                            <li style=" width:73%; float:right;">
                               <label style="margin-left:0px; line-height:30px;">Who can use the plugin </label>
							   <?php
							     global $wp_roles;
                                 $roles = $wp_roles->get_names();
								 foreach ( $wp_roles->roles as $key=>$value ):
								 if($key!='administrator'){
							   ?>
							   <br><input type="checkbox" name="sparevideo_options[who_can_use][]" <?php if(@	in_array($key,$options['who_can_use'])) echo 'checked=checked'; ?> id="" value="<?php echo $key; ?>">&nbsp;<?php echo $value['name']; ?> 
							    <?php 
								} 
							    endforeach;
							   ?>
                            </li>
                        </ul>
                    </li>
		    
		    <!-- Default Settings -->
                    <li class="sparevideosMMBox"><label class="optionLabel">Default Settings</label>
                        <ul class="subSettings texts">
                            <li>
                              <label><input value="1" type="checkbox" class="check_default" name="sparevideo_options[auto_play]" <?php echo $auto_play; ?>>Autoplay</label>
                            </li>
                            <li>
                              <label><input value="1" type="checkbox" class="check_ad" name="sparevideo_options[enable_ad]" <?php echo $enable_ad; ?>>Enable Ads</label>
                            </li>
                        </ul>
                    </li>
					
			 <!-- Detaching Player -->
                    <li class="sparevideosMMBox"><label class="optionLabel">Detaching Player</label>
                        <ul class="subSettings texts">
                            <li>
                              <label><input type="checkbox" value="1" class="check_default" name="sparevideo_options[is_always_detached]" <?php echo $is_always_detached; ?>>Is ALWAYS detached</label>
                            </li>
                            <li>
                              <label><input type="checkbox" value="1" class="check_default" name="sparevideo_options[is_detachable]"  <?php echo $is_detachable; ?>>Is Detachable <small>(when scrolls below player position)</small></label>
                            </li>
                        </ul>
                    </li>	
					
                   <li class="sparevideosMMBox"><label class="optionLabel">Is Detachable</label>
                         
						<ul class="subSettings texts">
                            <li>						 
             					<label><select attr="detach-to" name="sparevideo_options[detach_to]" style="font-size:13px;">
								<option value="">--Select--</option>
							    <option <?php if($detach_to == 'topright') echo 'selected'; ?> value="topright">Topright</option>
								<option <?php if($detach_to == 'topleft') echo 'selected'; ?> value="topleft">Topleft</option>	
								<option <?php if($detach_to == 'bottomright') echo 'selected'; ?> value="bottomright">Bottomright</option>
								<option <?php if($detach_to == 'bottomleft') echo 'selected'; ?>  value="bottomleft">Bottomleft</option>
							   </select></label>
                            </li>
							</ul>
					</li>	
					
					
				<!-- Default Size -->
                    <li class="sparevideosMMBox"><label class="optionLabel">Default Size</label>
                        <ul class="subSettings texts">
                            <li>
                              <label>Width can be either numeric value (size in pixels) or percentage</label>
                            </li>
                        </ul>
                        <ul class="subSettings texts">
                             <li>
                              <label>Height can be only numeric value (size in pixels)</label>
                            </li>
                        </ul>
                         <ul class="subSettings texts">
                            <li>
                              <label>Custom Width <i>(e.g. 480 or 95%)</i></label> <input type="text" id="sparevideosMMWpWidth" name="sparevideo_options[custom_width]" value="<?php echo $custom_width; ?>" style="width:15%;">
                            </li>
                            <li>
                              <label>Custom Height <i>(e.g. 360)</i></label> <input type="text" id="sparevideosMMWpheight" name="sparevideo_options[custom_height]" value="<?php echo $custom_height; ?>" style="width:15%;">
                            </li>
                        </ul>                       
                    </li>						
		          
				  <li class="sparevideosMMBox">
                             <label class="optionLabel">Player Colors</label>
					<ul class="subSettings texts">
					          <li><label id="sparevideosMMColorPickerContainer">Main Color</label>
                                <div class="sparevideosMMColorPickerContainer">
                                    <input type="text" class="sparevideosMMInput sparevideosMMColor" id="sparevideosMMColor" name="sparevideo_options[sparevideo_main_color]" value="<?php echo $sparevideo_main_color; ?>" style="color: rgba(255, 255, 255, 0.6);">
                                    <div id="sparevideosMMColorPicker" class="shadow sparevideosMMColorPicker" style="display: none;"><div id="sparevideosMMColorPickerSelect" class="sparevideosMMColorPickerSelect"><div class="farbtastic"><div class="color" style=""></div><div class="wheel"></div><div class="overlay"></div><div class="h-marker marker" style="left: 136px; top: 23px;"></div><div class="sl-marker marker" style="left: 47px; top: 97px;"></div></div></div><a id="sparevideosMMColorPickerClose" class="blue sparevideosMMBt sparevideosMMColorPickerClose">Apply</a><a id="sparevideosMMColorPickerCancel" class="sparevideosMMBt sparevideosMMColorPickerCancel">Remove Color</a></div>
                                </div></li>
								
							   <li><label id="sparevideosMMColorPickerContainer">Hover Color</label>
                                <div class="sparevideosMMColorPickerContainer">
                                    <input type="text" class="sparevideosMMInput sparevideosMMColor" id="sparevideosMMColor" name="sparevideo_options[sparevideo_hover_color]" value="<?php echo $sparevideo_hover_color; ?>" style="color: rgba(255, 255, 255, 0.6);">
                                    <div id="sparevideosMMColorPicker" class="shadow sparevideosMMColorPicker" style="display: none;"><div id="sparevideosMMColorPickerSelect" class="sparevideosMMColorPickerSelect"><div class="farbtastic"><div class="color" style=""></div><div class="wheel"></div><div class="overlay"></div><div class="h-marker marker" style="left: 136px; top: 23px;"></div><div class="sl-marker marker" style="left: 47px; top: 97px;"></div></div></div><a id="sparevideosMMColorPickerClose" class="blue sparevideosMMBt sparevideosMMColorPickerClose">Apply</a><a id="sparevideosMMColorPickerCancel" class="sparevideosMMBt sparevideosMMColorPickerCancel">Remove Color</a></div>
                                </div></li>
								  </ul>
								</li>
                   				
                </ul>
		        <!-- Submit -->
                <p id=""><input type="submit" name="Submit" value="<?php _e('Save Your Settings') ?>" class="sparevideosMMButton-primary button-primary"/></p>
	    </form>
        </div>
       
    </div>

    <?php
}

///////////////////////////
// START OF CLASS SPAREVIDEO
///////////////////////////

class SpareVideo{
	
	 // custom config variables
	 private static $options = array(
	'public_key'				=> '',
	'private_key'               => '',
	'api_domain'				=> 'https://devs.sparevideos.com/api/v1',
	'api_upload_initiate'		=> '/upload/initiate',
	'api_file_get'				=> '/me/file',
	'api_files_path'			=> '/me/files',
	'api_conversion_init'		=> '/upload/conversion/initiate',
	
	'spare_url'				=> 'https://www.sparevideos.com',
	'spare_thumb_path'		=> '/thumbnail', // /thumbnail/{code}/{size}/{action}
	);
	
	public static function config($key){
       
	    $sparevideo_options = get_option('sparevideo_options');
		
        if($key=='public_key')		 
		return $sparevideo_options['api_key'];
	    else if($key=='private_key')		 
		return $sparevideo_options['api_token'];
		else	
        return self::$options[$key];
    }

	
	// payload is the array of signature required data as the API documentation describes
	public function tokenGen($payload,$key,$algo='sha256') { 
	    $payload = trim(base64_encode(json_encode($payload)),'=');
	    $signature = hash_hmac($algo, $payload, $key);
		
		return $payload.'.'.$signature; 
    } 

	// method 0 = GET, 1 = POST, 2 = PUT, 3 = DELETE
	public function mycURL ($location, $fields, $method=0) {
		
		$ch = curl_init();
		
		if ($method==0){
		$location=$location.'?'.$fields;
		}

		curl_setopt($ch, CURLOPT_URL,$location);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		
		if ($method==1){
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		} elseif ($method==2){
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		} elseif ($method==3){
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		}
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds	
			
		$server_output = curl_exec ($ch);
		
		
		if ($errno = curl_errno($ch)) {
			 $errno;
		}
		
		curl_close ($ch);
		
		return $server_output;		
	}
	
	public function thumb_url ($video,$size,$action){
	
		return $this->config('spare_url').$this->config('spare_thumb_path').'/'.$video.'/'.$size.'/'.$action;
		
	}
	

// functions related to upload

	// 1. initiate the upload to retrive server URL, token and ref (ref used to track progress if upload from remote file instead of local)
	public function UploadInit()
    {
		$payloadData = array(
			'algo'			=> 'sha256',
			'method'		=> 'upload',
			'action'		=> 'initiate',
			'public_key'	=> $this->config('public_key'),
			'expires'		=> time()+(60*60)
        );

		$token = $this->tokenGen($payloadData,$this->config('private_key'));

		
		// this returns JSON Encoded UPLOAD DATA
		return $this->mycURL($this->config('api_domain').$this->config('api_upload_initiate'), 'token='.$token);
		
    }

	// 2. Send POST upload data and get a response of successful upload or fail
	public function UploadVideo($file_url,$local_file = null)
    {
        		

		$send_data = array(
		    'token'			=> strip_tags($_POST['token']),
			'ref'			=> strip_tags($_POST['ref'])
		);	
	    
		if($local_file==true){
		 $send_data['file'] =  $file_url;		 
		}
        else{
		 $send_data['remote_file'] =  $file_url;
        }		
		
		// this returns JSON Encoded UPLOAD DATA
		return $this->mycURL(strip_tags($_POST['server']),$send_data,1);
		
    }
	
	public function UploadProgress($upload_url)
    {

		$request = $this->UploadInit();
		$response = json_decode($request, true);

		$send_data = array(
			'remote_file'   => $file_url,
		);				
		
		// this returns JSON Encoded UPLOAD DATA
		return $this->mycURL($upload_url.'/progress',$send_data,1);
		
    }
	
	
	
	
	// 3. Use the generated video code and save it into DB, then UploadPost() function to convert video
    public function UploadPost($file_code)
    {
		// $videocode returned by upload POST response
		$payloadData = array(
			'algo'			=> 'sha256',
			'method'		=> 'upload',
			'action'		=> 'convert',			
			'file_code'		=> $file_code,
			'public_key'	=> $this->config('public_key'),
			'expires'		=> time()+30,
		);

		// function included in this file above
		$token = $this->tokenGen($payloadData,$this->config('private_key'));

		$request = $this->mycURL($this->config('api_domain').$this->config('api_conversion_init'), 'token='.$token);

		$response = json_decode($request, true);

		if (isset($response['status']) && $response['status']=='success'){
			return $response['code'];
		} 
		return '';

    }	

    public function VideoDelete($video_code)
    {
		
		// $codes_array is the array of video codes to delete ['code1', 'code2'...]
		$payloadData = array(
			'algo'			=> 'sha256',
			'method'		=> 'files',
			'action'		=> 'delete',
			'public_key'	=> $this->config('public_key'),
			'expires'		=> time()+120,
			'codes'			=> $video_code,
		);

		$token = $this->tokenGen($payloadData,$this->config('private_key'));

		$file_info = json_decode($this->mycURL($this->config('api_domain').$this->config('api_files_path').'?token='.$token, '_method=DELETE', 1), true);

		if(isset($file_info['status']) && $file_info['status']=='success'){
			// video deleted from the API, do sth
		}
    }
	
	
	public function VideoThumblist($file_code)
    {	
	
	   $result = $this->mycURL(SVID_THUMB_URL.'/'.$file_code.'/json/list','');
	   
	   
	   return $result;
	
	}
	
	
	public function VideoGetthumb($file_code)
    {	
	
		// $videothumbnail is the "thumbnail file code" as retrieved by thumbnail list method
		
			$payloadData = array(
				'algo'			=> 'sha256',
				'method'		=> 'file',
				'action'		=> 'get',
				'file_code'		=> $file_code,
				'public_key'	=> $this->config('public_key'),				
				'expires'		=> time()+30, // valid for 30 seconds
			);	

			$token = $this->tokenGen($payloadData,$this->config('private_key'));
			
			//execute thumbnail update	
			
				$result = $this->mycURL($this->config('api_domain').$this->config('api_file_get'), 'token='.$token);
            
            return $result;			
		
			
		
    }

    public function VideoThumbnailEdit($videocode,$videothumbnail)
    {
		
		
			$payloadData = array(
				'algo'			=> 'sha256',
				'method'		=> 'file',
				'action'		=> 'update',
				'file_code'		=> $videocode,
				'public_key'	=> $this->config('public_key'),
				'thumbnail'		=> $videothumbnail,
				'expires'		=> time()+30, // valid for 30 seconds
			);	

			$token = $this->tokenGen($payloadData,$this->config('private_key'));
			//execute thumbnail update
			$result = $this->mycURL($this->config('api_domain').$this->config('api_file_get').'?token='.$token, '_method=PUT', 1);		
             
            return $result;			 
		

    }

}

///////////////////////////
// END OF CLASS SPAREVIDEO
///////////////////////////

function svidf_sparevideo_ajax_video_upload() {
	
    $spareVideo   =  new SpareVideo();		
	
	
    if( isset($_FILES['file']) && !empty($_FILES['file']) )
	 $uploadinit   =  $spareVideo->UploadVideo($_FILES['file'],true);
    else
	 $uploadinit   =  $spareVideo->UploadVideo($_POST["remote_url"],false);
	
	 $uploadinit   =  json_decode($uploadinit, true);
	
	
	if($uploadinit['code']!='' && ctype_alnum($_POST['post_id'])){
		
		  check_admin_referer( 'video_conversion_'.$_POST['post_id'] );
          $current_user         =  wp_get_current_user();	 
		  //$video_thumb = json_decode($spareVideo->VideoGetthumb($uploadinit['code'])); 	
          $current_date = date('Y/m/d');
		  
		  global $wpdb;
          $wpdb->insert('wp_sparevideo',array('post_id'=> $_POST['post_id'],'user_id'=> $current_user->ID,'video_code'=>$uploadinit['code'],'date'=>$current_date),array('%s','%s','%s','%s'));
	 }	
	if($uploadinit['error']!='')
	echo 'error-'.$uploadinit['error'];
    else
    echo $uploadinit['code'];
	exit;
}

add_action('wp_ajax_video_upload', 'svidf_sparevideo_ajax_video_upload');


function svidf_sparevideo_ajax_video_init() {
	
     $spareVideo   =  new SpareVideo();
	   
	 $request = $spareVideo->UploadInit();
     
     echo $request;
     exit;
     
	
}
add_action('wp_ajax_video_init', 'svidf_sparevideo_ajax_video_init');


function svidf_sparevideo_ajax_upload_progress() {
	
     $upload_url  = strip_tags($_POST['upload_url']);
	 $ref         = strip_tags($_POST['ref']);
     
	 $send_data = array(
			'ref'			=> $ref		
	 );	
					
	 // this returns JSON Encoded UPLOAD DATA
	 echo $this->mycURL($upload_url.'/progress',$send_data,0);
	 
	 exit;
	
}
add_action('wp_ajax_upload_progress', 'svidf_sparevideo_ajax_upload_progress');


function svidf_sparevideo_ajax_video_conversion() {
	
     $spareVideo   =  new SpareVideo();	   
	 $request      =  $spareVideo->UploadPost($_POST['file_code']);	 

	 if($request!='' && $_POST['local_upload']=='yes' && ctype_alnum($_POST['post_id'])){
		 		 
		 check_admin_referer( 'video_conversion_'.$_POST['post_id'] );
		 
		 $current_user         =  wp_get_current_user();	 
		  
         $current_date = date('Y/m/d');
         
		 global $wpdb;
         
		 $wpdb->insert('wp_sparevideo',array('post_id'=> $_POST['post_id'],'user_id'=> $current_user->ID,'video_code'=>strip_tags($_POST['file_code']),'date'=>$current_date),array('%s','%s','%s','%s'));                

			   
	 }	
	    
     exit;
	
}
add_action('wp_ajax_video_conversion', 'svidf_sparevideo_ajax_video_conversion');




 
// function to create the DB / Options / Defaults					
function svidf_sparevideo_options_install() {
   	global $wpdb;
  	global $sparevideo_db;
    
	$sparevideo_db = $wpdb->prefix . 'sparevideo';
	
	// create the ECPT metabox database table
	if($wpdb->get_var("show tables like '$sparevideo_db'") != $sparevideo_db) 
	{
		$sql = "CREATE TABLE " . $sparevideo_db . " (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`post_id` mediumint(9),	
		`user_id` mediumint(9),	
		`video_code` mediumtext NOT NULL,	
		`video_settings` text,
		`date` DATE,
		 UNIQUE KEY id (id)
		);";

 
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
 
}
// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'svidf_sparevideo_options_install');


function svidf_sparevideo_ajax_load_videos($all=null){
?>
<script type='text/javascript' src='<?php echo plugins_url('/assets/sparevideos-js.js',__FILE__); ?>'></script>
<script type='text/javascript'>
	
	jQuery(function($){		
        
		$(".check_default").each(function(){ 
			
		  $(this).change(function(){
		  
			if($(this).prop("checked")!=true){
			  $(this).next().val(0);
			  $(this).next().prop("checked", true);
			} else{
				$(this).next().val(1);
			  $(this).next().prop("checked", true);
			}
			
		  });
			
		});
		
		$(".check_ad").each(function(){ 
		
		   $(this).change(function(){
			   
			 if($(this).prop("checked")!=true){
			   $(this).next().val(1);	 
			  $(this).next().prop("checked", true);
			 }else{
				$(this).next().val(0);
			  $(this).next().prop("checked", true);
			}
			 
			});
			
		});
  
	   	$('.sparevideosMMInsert').click(function(){
						var sparevideo_code =  $(this).parents('#sparevideosMMSettings').find('#sparevideo_code').val();
						
						var sparevideo_shortcode = '[sparevideos  code=!'+sparevideo_code+'!';												
						
						$(this).parents('#sparevideosMMMainForm').find('[attr]').each(function() {
							
							var curr_elem = $(this);                                                         
							
							if(  curr_elem.attr('type')=='checkbox' && curr_elem.attr('attr')!=''){
								
								if(curr_elem.prop('checked')){									
									sparevideo_shortcode = sparevideo_shortcode+' '+curr_elem.attr('attr')+'="'+curr_elem.val()+'"';		
								}								
							}
							else if(curr_elem.val()!='' && curr_elem.attr('attr')!=''){   
								
							  if (curr_elem.attr('attr')!='width' || (curr_elem.attr('attr')=='width' && curr_elem.val()!='100%')){
								  sparevideo_shortcode = sparevideo_shortcode+' '+curr_elem.attr('attr')+'="'+curr_elem.val()+'"';						
							  }
							}	
								
						});
						
						sparevideo_shortcode = sparevideo_shortcode+']';												
                        								
						get_video = sparevideo_shortcode.replace(/!/g, '"');		
																						
						parent.tinyMCE.activeEditor.execCommand('mceInsertRawHTML', false,get_video);
						parent.jQuery('.media-modal-close').click();
					
		});
		
		$('.media_lib1').click(function(){
			 var data11 = {
							 action: 'load_videos',
                             post_id: <?php echo strip_tags($_REQUEST['post_id']); ?>	,
				 			 _wpnonce: '<?php echo wp_create_nonce( 'load_videos_'.$_REQUEST['post_id'] ); ?>'
                            };
			$('#media_lib').html('<p style="display:table; vertical-align:middle; text-align:center;    min-width: 100%; margin:20% 0px 0px 0px;"><img src="<?php echo admin_url();?>/images/loading.gif" style="width:20px;"></p>');
		   $.ajax({  
											type: "POST",                               
											url: "admin-ajax.php", 
											data: data11,								
											success: function(status_upd) {        
											   $('#media_lib').html(status_upd);
											   return false;  
											}
							
		   });	
		 });

         
		$('.navigation a').click(function(){
			 var data11 = {
							 action: 'load_videos',
							 paged: $(this).attr('paged_no'),
                             post_id: <?php echo strip_tags($_REQUEST['post_id']); ?>,
                             show_media: '<?php echo strip_tags($_REQUEST['show_media']); ?>',	
				 			 _wpnonce: '<?php echo wp_create_nonce( 'load_videos_'.$_REQUEST['post_id'] ); ?>'
                            };
			
			$('#media_lib').html('<p style="display:table; vertical-align:middle; text-align:center;    min-width: 100%; margin:20% 0px 0px 0px;"><img src="<?php echo admin_url();?>/images/loading.gif" style="width:20px;"></p>');
		   $.ajax({  
											type: "POST",                               
											url: "admin-ajax.php", 
											data: data11,								
											success: function(status_upd) {        
											   $('#media_lib').html(status_upd);
											   return false;  
											}
							
		   });	
		 });  
		   


		 
		$('.media-item').each(function(){
			  
			  $(this).find('.describe-toggle-on').click(function(){
				
				  $('.media-item').removeClass('open');
				  $('.slidetoggle').hide();
				  $(this).parent('.media-item').addClass('open');				  
			      
			  });
			  $(this).find('.describe-toggle-off').click(function(){
				
				  $('.media-item').removeClass('open');
				  $('.slidetoggle').hide();
				  $(this).prev('a').show();				  
			      
			  });
			  
		});
		
		$('a[sec_id="video_upload"]').click(function(){
			     $('#remote_url,#fileSelect').val('');
				 $('#upload_form_prog').html('');							 
                 $('#upload_form_prog').removeClass('success_msg');
		});
		
		$('.media-menu-item').click(function(){
			  
			  
  			  var selector = $(this).attr('sec_id');			
			  
      		  $('.media-menu-item').removeClass('active');
			  $(this).addClass('active');			 
			  $('.sparevideo_sections').removeClass('active');
			  $('#'+selector).addClass('active');
		});
		
		$('.media_lib_post').click(function(){
			 $('.media_lib1').click();
		});
		$('#all_media').change(function(){
			
			 
			 var data_change = {
							 action: 'load_videos',                       
							 show_media: 'all',
							 post_id: '<?php echo strip_tags($_REQUEST['post_id']); ?>',
                            };
			
            if( $(this).val() == 'post_media')	{
				
				 var data_change = {
							 action: 'load_videos',                       
							 show_media: 'post_media',
							 post_id: '<?php echo strip_tags($_REQUEST['post_id']); ?>',
					 		_wpnonce: '<?php echo wp_create_nonce( 'load_videos_'.$_REQUEST['post_id'] ); ?>'
                     };
				
			}	
 
 
			$('#media_lib').html('<p style="display:table; vertical-align:middle; text-align:center;    min-width: 100%; margin:20% 0px 0px 0px;"><img src="<?php echo admin_url();?>/images/loading.gif" style="width:20px;"></p>');
		   $.ajax({  
											type: "POST",                               
											url: "admin-ajax.php", 
											data: data_change,								
											success: function(status_upd) {        
											   $('#media_lib').html(status_upd);
											   return false;  
											}
							
		   });	
	    });
		
		
		});
		
		
</script>
<?php
	// Media Library edit section in page / posts
	echo '<div id="library-form" class="media-upload-form validate" action="" method="post" enctype="multipart/form-data"><div id="media-items" class="media-items-'.$post_id.'">';
    
	$all_media = (isset($_REQUEST['show_media']) && $_REQUEST['show_media'])=='all' ? 'selected' : '';
	$post_media  = ( (isset($_REQUEST['show_media']) && $_REQUEST['show_media']=='post_media') ||  $_REQUEST['show_media']==''  ) ? 'selected' : '';
	
	
	echo '<select name="all_media" id="all_media" class="attachment-filters" style="margin:5px 0px 10px 0px;">
		<option value="all" '.$all_media.'>All media</option>
		<option value="post_media" '.$post_media.'>Uploaded to this page</option>		
	</select><br>';
		
	global $wpdb,$max_num_pages;
    
	$current_user = wp_get_current_user();
	
	$paged = (is_int(strip_tags($_REQUEST['paged']))) ? strip_tags($_REQUEST['paged']) : 1;
    $post_per_page = 12;
    $offset = ($paged - 1)*$post_per_page;
	
    $querystr = "
    SELECT  *
    FROM wp_sparevideo";	
     
    if( strip_tags($_REQUEST['show_media'])=='' || (isset($_REQUEST['show_media']) && $_REQUEST['show_media']!='all') )	 {
		
		$querystr .= " WHERE post_id = ".strip_tags($_REQUEST['post_id']);
		if($current_user->roles[0]!='administrator')	 
	    $querystr .= " AND user_id=".$current_user->ID;
	
	} else {
		
		if($current_user->roles[0]!='administrator')	 
	    $querystr .= " WHERE user_id=".$current_user->ID;
	}
	
	
    $querystr .= " ORDER BY wp_sparevideo.id DESC";
    
	$querystr .= " LIMIT ".$offset.", ".$post_per_page;
	
	
    $pageposts = $wpdb->get_results($querystr, OBJECT);
		
    
	if( strip_tags($_REQUEST['show_media'])=='' || (isset($_REQUEST['show_media']) && $_REQUEST['show_media']!='all') )	 {
		$add_cond = " WHERE post_id = ".strip_tags($_REQUEST['post_id']);
		if($current_user->roles[0]!='administrator')	 
	    $add_cond .= " AND user_id=".$current_user->ID;
	
	} else{
		$add_cond .= " WHERE user_id=".$current_user->ID;
		
	}
	
	
	$sql_posts_total = $wpdb->get_var( "SELECT count(*) FROM wp_sparevideo".$add_cond );
	
    $max_num_pages    = ceil($sql_posts_total / $post_per_page);
	
	
	if(empty($pageposts))
	echo "No videos have been uploaded yet.";
   
	$options = get_option('sparevideo_options');
	
	
	foreach( $pageposts as $pagepost  ){ 
	
	        $video_settings = unserialize($pagepost->video_settings);  
		    
   
			 
			$auto_play = ( !empty($video_settings['auto_play']) || $options['auto_play'] ) ? 'checked=checked' : '';
			
			if($video_settings['auto_play']==0  && $video_settings['auto_play']!='')
			$auto_play = '';
		
		
			$enable_ad = (!empty($video_settings['enable_ad']) || (isset($options['enable_ad']) && $options['enable_ad']==0)) ? 'checked=checked' : '';			
			
			if($video_settings['enable_ad']==1 && $video_settings['enable_ad']!='')
			$enable_ad = '';	
			else if($video_settings['enable_ad']==0 && $video_settings['enable_ad']!='')
			$enable_ad = 'checked=checked';
			
			$is_always_detached = (!empty($video_settings['is_always_detached']) || $options['is_always_detached']) ? 'checked=checked' : '';
			
			if($video_settings['is_always_detached']==0 && $video_settings['is_always_detached']!='')
			$is_always_detached = '';
			
			$is_detachable = (!empty($video_settings['is_detachable']) || $options['is_detachable']  ) ? 'checked=checked' : '';
			
			if($video_settings['is_detachable']==0 && $video_settings['is_detachable']!='')			
			$is_detachable = '';
			
			
			$custom_width = (!empty($video_settings['custom_width'])) ? $video_settings['custom_width'] : $options['custom_width'];
			
			$detach_to    = ( !empty($video_settings['detach_to']) ) ? $video_settings['detach_to'] : $options['detach_to'];
			
			$custom_height = ( !empty($video_settings['custom_height'])  ) ? $video_settings['custom_height'] :  $options['custom_height'];
			
			$sparevideo_main_color = (!empty($video_settings['sparevideo_main_color'])) ? $video_settings['sparevideo_main_color'] : $options['sparevideo_main_color'];
			
			$sparevideo_hover_color = (!empty($video_settings['sparevideo_hover_color'])) ? $video_settings['sparevideo_hover_color'] : $options['sparevideo_hover_color'];
			
			$get_thumb_url = SVID_THUMB_URL.'/'.$pagepost->video_code.'/'.SVID_THUMB_SIZE.'/default.jpg?t='.time();
			
			if (@getimagesize($get_thumb_url)) {
			  $img_src = $get_thumb_url; $no_thumb = false;
			} else {
			  $img_src = SVID_PLUGIN_DIR.'/assets/images/no-thumbnail.png';
			  
			  $no_thumb = true;
			}
	
	   ?>
			<form method="post" action="" id="sparevideosMMMainForm" name="sparevideosMMMainForm" class="">	<div class="media-item preloaded" id="media-item-<?php echo $pagepost->id; ?>">

					<?php
					if ($no_thumb) {
					?>
					<a href="javascript:void(0)" class="toggle describe-toggle-off sparevideosMM" style="display:block;" id="show-<?php echo $pagepost->id; ?>">In Progress</a>		
                    <a href="javascript:void(0)" style="display:block;"  class="toggle describe-toggle-off media_lib1  sparevideosMM">Refresh</a>                     
                    <?php					
					} else {
					?>					 
					<a href="javascript:void(0)" class="toggle describe-toggle-on sparevideosMM" id="show-<?php echo $pagepost->id; ?>"> <input type="radio"  onClick="this.checked = false;" /></a>
					<a href="javascript:void(0)" class="toggle describe-toggle-off sparevideosMM"> <input onClick="this.checked = true;"  checked="checked" type="radio" /></a>
					<?php } ?>
					
				    <div class="filename new"><span class="title sparevideosMMTitle" id="sparevideosMMTitle-<?php echo $pagepost->id; ?>">
					   
					   <img src="<?php echo $img_src; ?>" class="pinkynail">
					   
					</span></div>
					<table class="slidetoggle describe startclosed sparevideosMMWrapper sparevideosMMMainWrapper <?php echo $soundcloudIsGoldSelectedFormat ?>">
						<thead id="media-head-<?php echo $pagepost->id; ?>" class="media-item-info">
							<tr valign="top">
								<td id="thumbnail-head-<?php echo $pagepost->id; ?>" class="A1B1">
								
               
	 
                <ul id="sparevideosMMSettings">
                <!-- API Settings -->
				<?php
				
				$options = get_option('sparevideo_options');
				
				 
		        		
			    $short_codeattr = '';			
						
			    $shortcode = '[sparevideos  code=!'.$pagepost->video_code.'!';
				
				
				if(isset($video_settings['auto_play']))
				$shortcode .= ' auto_play=!'.$video_settings['auto_play'].'!';
			
				if(isset($video_settings['enable_ad']))
				$shortcode .= ' ads=!'.$video_settings['enable_ad'].'!';
			
			    if(isset($video_settings['is_detachable']))
				$shortcode .= ' detachable=!'.$video_settings['is_detachable'].'!';
			
			    if( $video_settings['custom_width']!='' )
				$shortcode .= ' width=!'.$video_settings['custom_width'].'!';
			
			    if( $video_settings['custom_height']!='' )
				$shortcode .= ' height=!'.$video_settings['custom_height'].'!';
			
			    if( $video_settings['sparevideo_main_color']!='' )
				$shortcode .= ' main_color=!'.$video_settings['sparevideo_main_color'].'!';
			
			    if( $video_settings['sparevideo_hover_color']!='' )
				$shortcode .=  ' hover_color=!'.$video_settings['sparevideo_hover_color'].'!';
																		             
				$shortcode .= ']';

				
				?> 
			
                 <input type="hidden" id="sparevideo_shortcode" name="sparevideo_shortcode" value="<?php echo $shortcode; ?>">
		         <input type="hidden" name="sparevideo_options[video_id]" value="<?php echo $pagepost->id; ?>">
				 <input type="hidden" id="sparevideo_code" name="sparevideo_options[video_code]" value="<?php echo $pagepost->video_code; ?>">
				 <b style="color: #0085ba;font-size: 13px;padding-bottom: 5px;float: left;">Override Global Settings below</b>
		        <!-- Default Settings -->
                    <li class="sparevideosMMBox"><label class="optionLabel">Default Settings</label>
                        <ul class="subSettings texts">
                            <li>
                              <label><input type="checkbox"  attr="autoplay" name="" value="1" <?php echo $auto_play; ?> class="check_default"><input type="hidden"  name="sparevideo_options[auto_play]" value="<?php echo ($auto_play!='') ? '1':'0'; ?>" >Autoplay</label>
                            </li>
                            <li>
                              <label><input type="checkbox" name=""  attr="ads" value="1" <?php echo $enable_ad; ?> class="check_ad"><input type="hidden" name="sparevideo_options[enable_ad]"  value="<?php echo ($enable_ad!='') ? '0':'1'; ?>">Enable Ads</label>
                            </li>
                        </ul>
                    </li>
					
			    <!-- Detaching Player -->
                    <li class="sparevideosMMBox"><label class="optionLabel">Detaching Player</label>
                        <ul class="subSettings texts">
                            <li>
                              <label><input type="checkbox" name="" attr="detached" value="1" <?php echo $is_always_detached; ?> class="check_default"><input  type="hidden" class="check_default" name="sparevideo_options[is_always_detached]" value="<?php echo ($is_always_detached!='') ? '1':'0'; ?>">Is ALWAYS detached</label>
                            </li>
                            <li>
                              <label><input type="checkbox" name="" attr="detachable" value="1" <?php echo $is_detachable; ?> class="check_default"><input type="hidden" class="check_default" name="sparevideo_options[is_detachable]"   value="<?php echo ($is_detachable!='') ? '1':'0'; ?>">Is Detachable <small>(when scrolls below player position)</small></label>
                            </li>							
                        </ul>
                    </li>	
					
					 <li class="sparevideosMMBox"><label class="optionLabel">Detached to</label>
                         
						<ul class="subSettings texts">
                            <li>						 
             					<label><select attr="detach-to" name="sparevideo_options[detach_to]" style="font-size:13px;">
								<option value="">--Select--</option>
							    <option <?php if($detach_to == 'topright') echo 'selected'; ?> value="topright">Topright</option>
								<option <?php if($detach_to == 'topleft') echo 'selected'; ?> value="topleft">Topleft</option>	
								<option <?php if($detach_to == 'bottomright') echo 'selected'; ?> value="bottomright">Bottomright</option>
								<option <?php if($detach_to == 'bottomleft') echo 'selected'; ?>  value="bottomleft">Bottomleft</option>
							   </select></label>
                            </li>
							</ul>
					</li>	

				<!-- Default Size -->
                    <li class="sparevideosMMBox"><label class="optionLabel">Default Size</label>
                        <ul class="subSettings texts">
                            <li>
                              <label>Custom Width <i>(e.g. 480 or 95%)</i></label> <input attr="width" type="text" id="sparevideosMMWpWidth" name="sparevideo_options[custom_width]" value="<?php echo $custom_width; ?>" style="width:15%;">
                            </li>
                            <li>
                              <label>Custom Height <i>(e.g. 360)</i></label><input attr="height" type="text" id="sparevideosMMWpheight" name="sparevideo_options[custom_height]" value="<?php echo $custom_height; ?>" style="width:15%;">
                            </li>
                        </ul>
                    </li>						
		          
				  <li class="sparevideosMMBox">
                             <label class="optionLabel">Player Colors</label>
							  <ul class="subSettings texts">
					          <li><label id="sparevideosMMColorPickerContainer">Main Color</label>
                                <div class="sparevideosMMColorPickerContainer">
                                    <input type="text" attr="main_color" class="sparevideosMMInput sparevideosMMColor" id="sparevideosMMColor" name="sparevideo_options[sparevideo_main_color]" value="<?php echo $sparevideo_main_color; ?>" style="color: rgba(255, 255, 255, 0.6);">
                                    <div id="sparevideosMMColorPicker" class="shadow sparevideosMMColorPicker" style="display: none;"><div id="sparevideosMMColorPickerSelect" class="sparevideosMMColorPickerSelect"><div class="farbtastic"><div class="color" style=""></div><div class="wheel"></div><div class="overlay"></div><div class="h-marker marker" style="left: 136px; top: 23px;"></div><div class="sl-marker marker" style="left: 47px; top: 97px;"></div></div></div><a id="sparevideosMMColorPickerClose" class="blue sparevideosMMBt sparevideosMMColorPickerClose">Apply</a><a id="sparevideosMMColorPickerCancel" class="sparevideosMMBt sparevideosMMColorPickerCancel">Remove Color</a></div>
                                </div>
                                </li>
								
							   <li><label id="sparevideosMMColorPickerContainer">Hover Color</label>
                                <div class="sparevideosMMColorPickerContainer">
                                    <input type="text" attr="hover_color" class="sparevideosMMInput sparevideosMMColor" id="sparevideosMMColor" name="sparevideo_options[sparevideo_hover_color]" value="<?php echo $sparevideo_hover_color; ?>" style="color: rgba(255, 255, 255, 0.6);">
                                    <div id="sparevideosMMColorPicker" class="shadow sparevideosMMColorPicker" style="display: none;"><div id="sparevideosMMColorPickerSelect" class="sparevideosMMColorPickerSelect"><div class="farbtastic"><div class="color" style=""></div><div class="wheel"></div><div class="overlay"></div><div class="h-marker marker" style="left: 136px; top: 23px;"></div><div class="sl-marker marker" style="left: 47px; top: 97px;"></div></div></div><a id="sparevideosMMColorPickerClose" class="blue sparevideosMMBt sparevideosMMColorPickerClose">Apply</a><a id="sparevideosMMColorPickerCancel" class="sparevideosMMBt sparevideosMMColorPickerCancel">Remove Color</a></div>
                                </div>
                                </li>
								 </ul>
								</li>
								
								
								<li>
								  
								    <a class="button sparevideosMMInsert" href="#" id="insert" style="display: block; line-height: 24px;">Insert Into Post</a>
								
								</li>
								<?php 
								   $media_view = strip_tags($_REQUEST['show_media']);
								   if($media_view=='')
								   $media_view = 'post_media';
							   							       
								   $no_page    =    (isset($_POST['paged']) && $_POST['paged']!='') ? strip_tags($_POST['paged']) : 0;
								?>
								<input type="hidden" name="paged" value="<?php echo $no_page; ?>" id="paged">
								<input type="hidden" name="media_view" value="<?php echo $media_view; ?>" id="media_view">
                   				
                  </ul>
		        <!-- Submit -->
				   <script type="text/javascript" src="<?php echo includes_url('/js/tinymce/tiny_mce_popup.js'); ?>"></script>				 
		          
                  <p id="sparevideosMMSubmit">
				 
				   <input type="submit" name="save_settings" value="<?php _e('Save Your Settings') ?>" class=" button-primary"/>&nbsp;&nbsp;&nbsp;
                   
				      <?php
					  wp_nonce_field( 'save_settings' );
                      $check_video_setup = get_post_meta( strip_tags($_REQUEST['post_id']), 'video_page_'.strip_tags($_REQUEST['post_id']), true );			   
					  					  
					  
					  if( $pagepost->post_id == strip_tags($_REQUEST['post_id']) ){
						  if($check_video_setup  == $pagepost->video_code){
						  ?>
						  <input type="submit" id="remove_video_page" value="Remove Video Page" name="remove_video_page" class="button-primary" style="margin-right:20px; margin-left:10px;"/>		
						  <?php } else { ?>
						  <input type="submit" id="set_as_video_page" value="Make Video Page" name="set_as_video_page" class=" button-primary" style="margin-right:20px; margin-left:10px;"/>		
						  <?php } 
					  }
					  ?>
				      
				   
				  </p>
	                
					</td>							
				</tr>
			</thead>
		</table>
		
	</div>
</form>
	<?php }	
    echo svidf_wordpress_numeric_post_nav($paged,$max_num_pages,1);	
	echo '<div id="colorpicker"></div>';
	echo '</div></div>';
	exit;
	
}
add_action('wp_ajax_load_videos', 'svidf_sparevideo_ajax_load_videos');

add_filter( 'wp_default_editor', create_function('', 'return "visual";') );


// SpareVideo Shortcode 
function svidf_sparevideos_code($attr,$content){
	
	$url       =  get_post_meta( get_the_id(), '_thumbnail_ext_url', TRUE );
	$video_id  =  get_post_meta( get_the_id(),'video_page_'.get_the_id(), TRUE);
	
	//if(empty($url)){
		
		$options = get_option('sparevideo_options');
		

		$sc_auto_play               =  $attr['autoplay'];
		$sc_enable_ad               =  $attr['ads'];
		$sc_is_always_detached      =  ($attr['detached']==0 ? '': $attr['detached']);
		$sc_is_detachable           =  ($attr['detachable']==0 ? '': $attr['detachable']);
		$detach_to                  =  (is_array($attr)!='' ? $attr['detach-to'] : $options['detach_to']);
		$sc_custom_width            =  (is_array($attr)!='' ? $attr['width'] : $options['custom_width']);
		$sc_custom_height           =  (is_array($attr)!='' ? $attr['height'] : $options['custom_height']);
		$sc_sparevideo_main_color   =  (is_array($attr)!='' ? $attr['main_color'] : $options['sparevideo_main_color']);
		$sc_sparevideo_hover_color  =  (is_array($attr) ? $attr['hover_color'] : $options['sparevideo_hover_color']);
		
		$main_color                 =  str_replace('#','',$sc_sparevideo_main_color);
		$hover_color                =  str_replace('#','',$sc_sparevideo_hover_color);	
		
		$video_code                 =  $attr['code']; 
		
		if($attr['code']=='' && $video_id!=''){	
      
           $sc_auto_play               =  ($options['auto_play']=='' ? 0 : $options['auto_play']);
		   $sc_enable_ad              =   ($options['enable_ad']=='' ? 0 : $options['enable_ad']);
		   $sc_is_always_detached      =  ($options['is_always_detached']==0 ? '': $options['is_always_detached']);
		   $sc_is_detachable           =  ($options['is_detachable']==0 ? '': $options['is_detachable']);		   
		   
           $video_code                 =  $video_id;		
		}
		
		
		
	  $output = '<script type="application/javascript" height="'.$sc_custom_height.'" width="'.$sc_custom_width.'"  hover_color="'.$hover_color.'" main_color="'.$main_color.'" autoplay="'.$sc_auto_play.'"  detach-to="'.$detach_to.'" detached="'.$sc_is_always_detached.'" detachable="'.$sc_is_detachable.'" ads="'.$sc_enable_ad.'" src="https://www.sparevideos.com/embed/'.$video_code.'.js"></script>';
		
		return $output;
	
	//}
	
}
add_shortcode('sparevideos','svidf_sparevideos_code');

add_filter( 'admin_post_thumbnail_html', 'svidf_thumbnail_url_field' );
function svidf_thumbnail_url_field( $html ) {
  
    global $post;
	
	$vid_code   = get_post_meta( strip_tags($_GET['post']),'video_page_'.strip_tags($_GET['post']), TRUE);	
    $value      = SVID_THUMB_URL.'/'.$vid_code.'/'.SVID_THUMB_SIZE.'/default.jpg';
	
	
	
    //$nonce = wp_create_nonce( 'thumbnail_ext_url_' . strip_tags($_GET['post']) . get_current_blog_id() );
	
    if ( $vid_code!=0) {  	 
      $html = '<div>';
      $html .= '<p><img style="width:100%;height:auto;" src="'. esc_url($value) . '"></p><a href="'.get_edit_post_link(strip_tags($_GET['post'])).'&remove_featured_image=1">Remove image</a>';
      $html .= '</div>';	 
    }
    
    return $html;
} 


add_filter( 'post_thumbnail_html', 'svidf_thumbnail_external_replace', 10, PHP_INT_MAX );
function svidf_thumbnail_external_replace( $html, $post_id ) {
    
	$vid_code = get_post_meta( $post_id,'video_page_'.$post_id, TRUE);
	
    $url = SVID_THUMB_URL.'/'.$vid_code.'/'.SVID_THUMB_SIZE.'/default.jpg';
	
    if ( empty( $vid_code ) ) {
        return $html;
    }
    
    $html = '<img width="100%" src="'.$url.'">';
    
    return $html;
}

if( isset($_REQUEST['remove_featured_image']) &&  $_REQUEST['remove_featured_image']!=''  ){
	   update_post_meta( strip_tags($_GET['post']), '_thumbnail_ext_url', '' );
	   update_post_meta( strip_tags($_GET['post']), 'video_page_'.strip_tags($_GET['post']),0 );	   
}

// Media Library Pagination
function svidf_wordpress_numeric_post_nav($paged,$max,$ajax){
	   
	if( $max <= 1 )
        return;
	
    /*Add current page into the array */
    if ( $paged >= 1 )
        $links[] = $paged;
    /*Add the pages around the current page to the array */
    if ( $paged >= 3 ) {
        $links[] = $paged - 1;
        $links[] = $paged - 2;
    }
    if ( ( $paged + 2 ) <= $max ) {
        $links[] = $paged + 2;
        $links[] = $paged + 1;
    }
    echo '<div class="navigation"><ul>' . "\n";
    /*Display Previous Post Link */
    if ( get_previous_posts_link() )
        printf( '<li>%s</li>' . "\n", get_previous_posts_link('«') );
    /*Display Link to first page*/
    if ( ! in_array( 1, $links ) ) {
        $class = 1 == $paged ? ' class="active"' : '';
		
		
		if($ajax==1)
		$add_link = 'javascript:void(0)';
	    else
		$add_link =  esc_url( get_pagenum_link( 1 ) );
		
        printf( '<li%s><a href="%s" paged_no="1">%s</a></li>' . "\n", $class,$add_link, '1' );
        if ( ! in_array( 2, $links ) )
            echo '<li>…</li>';
    }
    /* Link to current page */
    sort( $links );
    foreach ( (array) $links as $link ) {
        $class = $paged == $link ? ' class="active"' : '';
		
		if($ajax==1)
		$add_link = 'javascript:void(0)';
	    else
		$add_link = esc_url( get_pagenum_link( $link ) );
		
		
        printf( '<li%s><a href="%s" paged_no="'.$link.'">%s</a></li>' . "\n", $class, $add_link, $link );
    }
    /* Link to last page, plus ellipses if necessary */
    if ( ! in_array( $max, $links ) ) {
        if ( ! in_array( $max - 1, $links ) )
            echo '<li>…</li>' . "\n";
		
		if($ajax==1)
		$add_link = 'javascript:void(0)';
	    else
		$add_link = esc_url( get_pagenum_link( $max ) );
		
        $class = $paged == $max ? ' class="active"' : '';
        printf( '<li%s><a href="%s" paged_no="'.$max.'">%s</a></li>' . "\n", $class,$add_link , $max );
    }
    /** Next Post Link */
    if ( get_next_posts_link() )
        printf( '<li>%s</li>' . "\n", get_next_posts_link() );
    echo '</ul></div>' . "\n";
	
}

add_action('do_meta_boxes', 'svidf_be_rotator_image_metabox' );

function svidf_be_rotator_image_metabox() {
    $current_user = wp_get_current_user();
	
    if( !empty($current_user->allcaps['level_2']) ) {     	
		remove_meta_box( 'postimagediv', 'page', 'side' );	
		add_meta_box('postimagediv', __('Featured Image'), 'post_thumbnail_meta_box', 'page', 'side', 'high');
	 } else {
		remove_meta_box( 'postimagediv', 'post', 'side' );
	 }
}		