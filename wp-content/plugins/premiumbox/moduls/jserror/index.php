<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]JS error[:ru_RU][en_US:]JS error[:en_US]
description: [ru_RU:]Вывод ошибок на сайте[:ru_RU][en_US:]Display errors on the website[:en_US]
version: 1.0
category: [ru_RU:]Javascript[:ru_RU][en_US:]Javascript[:en_US]
cat: js
*/

remove_action('pn_js_error_response', 'jserror_js_error_response');

add_action('pn_js_error_response', 'modul_jserror_js_error_response');
function modul_jserror_js_error_response($type){
?>
	<?php if(is_admin()){ ?>
		$('.js_shadow, .js_techwindow').hide();
	<?php } ?>
	$('.jserror_shad:first, #jserror_response').show();		
	var hei = Math.ceil(($(window).height() - $('#jserror_response .jserror_box').height()) / 2);
	$('#jserror_response').css({'top':hei});
	<?php if($type == 'ajax'){ ?>
		$('.jserror_text').html(res2);
	<?php } else { ?>
		$('.jserror_text').html(res3);
	<?php } ?>
<?php
}

add_action('admin_footer','modul_jserror_admin_footer');
function modul_jserror_admin_footer(){
?>
<div class="jserror_shad"></div>
<div class="jserror_wrap" id="jserror_response">
	<div class="jserror_box">
		<div class="jserror_box_title"><?php _e('An error has occurred','pn'); ?></div>
		<div class="jserror_box_close" id="jserror_box_close"></div>
		<div class="jserror_box_ins">
			<div class="jserror_box_text"><?php _e('Sorry, but the request has not been executed due to an error','pn'); ?></div>
			<div class="jserror_box_code"><?php _e('Error','pn'); ?>: <strong class="jserror_text"></strong></div>
		</div>	
	</div>
</div>
<script type="text/javascript">
jQuery(function($){ 	
    $('#jserror_box_close').on('click', function(){
		window.location.href= "";
    });	
});	
</script>
<?php	
}

add_action('wp_footer','jserror_wp_footer');
function jserror_wp_footer(){
?>
<div class="jserror_shad"></div>
<div class="jserror_wrap" id="jserror_response">
	<div class="jserror_box">
		<div class="jserror_box_title"><?php _e('An error has occurred','pn'); ?></div>
		<div class="jserror_box_close" id="jserror_box_close"></div>
		<div class="jserror_box_ins">
			<div class="jserror_box_text"><?php _e('Sorry, but the request has not been executed due to an error','pn'); ?></div>
			<div class="jserror_box_code"><?php _e('Error','pn'); ?>: <strong class="jserror_text"></strong></div>
		</div>
	</div>
</div>
<script type="text/javascript">
jQuery(function($){ 	
    $('#jserror_box_close').on('click', function(){
		window.location.href= "";
    });	
});	
</script>
<?php	
}