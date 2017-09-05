<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]JS alert[:ru_RU][en_US:]JS alert[:en_US]
description: [ru_RU:]Окошко с сообщением об ошибке[:ru_RU][en_US:]Window with an error message[:en_US]
version: 1.0
category: [ru_RU:]Javascript[:ru_RU][en_US:]Javascript[:en_US]
cat: js
*/

remove_action('pn_js_alert_response', 'jserror_js_alert_response');

add_action('pn_js_alert_response', 'jsalert_js_alert_response');
function jsalert_js_alert_response(){
?>
	<?php if(is_admin()){ ?>
		$('.js_shadow, .js_techwindow').hide();
	<?php } ?>
	$('.jserror_shad:first, #jserror_alert').show();		
	var hei = Math.ceil(($(window).height() - $('#jserror_alert .jserror_box').height()) / 2);
	$('#jserror_alert').css({'top':hei});
	if(res['status_text']){
		$('.jserror_alert').html(res['status_text']);
	}
<?php
}

add_action('wp_footer','jsalert_wp_footer');
function jsalert_wp_footer(){
?>
<div class="jserror_shad"></div>
<div class="jserror_wrap" id="jserror_alert">
	<div class="jserror_box">
		<div class="jserror_box_title"><?php _e('Attention!','pn'); ?></div>
		<div class="jserror_box_close" id="jsalert_box_close"></div>
		<div class="jserror_box_ins">
			<div class="jserror_box_text jserror_alert"></div>
		</div>	
	</div>
</div>
<script type="text/javascript">
jQuery(function($){ 	
    $('#jsalert_box_close').on('click', function(){
		$('.jserror_shad, .jserror_wrap').hide();
    });	
});	
</script>
<?php	
}

add_action('admin_footer','jsalert_admin_footer');
function jsalert_admin_footer(){
?>
<div class="jserror_shad"></div>
<div class="jserror_wrap" id="jserror_alert">
	<div class="jserror_box">
		<div class="jserror_box_title"><?php _e('Attention!','pn'); ?></div>
		<div class="jserror_box_close" id="jsalert_box_close"></div>
		<div class="jserror_box_ins">
			<div class="jserror_box_text jserror_alert"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
jQuery(function($){ 	
    $('#jsalert_box_close').on('click', function(){
		$('.jserror_shad, .jserror_wrap').hide();
    });	
});	
</script>
<?php	
}