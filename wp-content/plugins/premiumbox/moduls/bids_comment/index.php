<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Комментарии к заявкам[:ru_RU][en_US:]Requests comments[:en_US]
description: [ru_RU:]Комментарии к заявкам для администратора и клиентов[:ru_RU][en_US:]Requests comments for admin and users[:en_US]
version: 1.0
category: [ru_RU:]Заявки[:ru_RU][en_US:]Orders[:en_US]
cat: req
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_filter('onebid_icons','onebid_icons_bidscomment',199,3);
function onebid_icons_bidscomment($onebid_icon, $item, $data_fs){
	
	$comment_user = trim(get_bids_meta($item->id,'comment_user'));
	$c_u = '';
	if($comment_user){
		$c_u = 'active';
	}	
	
	$comment_admin = trim(get_bids_meta($item->id,'comment_admin'));
	$c_a = '';
	if($comment_admin){
		$c_a = 'active';
	}	
	
	$user_comm = '<div class="bs_comus js_comment_0 js_comment '. $c_u .'" data-id="0">'. __('user comm.','pn') .'</div>';
	$onebid_icon['user_comm'] = array(
		'type' => 'html',
		'html' => $user_comm,
	);

	$admin_comm = '<div class="bs_comad js_comment_1 js_comment '. $c_a .'" data-id="1">'. __('admin comm.','pn') .'</div>';
	$onebid_icon['admin_comm'] = array(
		'type' => 'html',
		'html' => $admin_comm,
	);	
	
	return $onebid_icon;
} 

add_action('pn_adminpage_content_pn_bids', 'change_bids_filter_after_bidscomment');
function change_bids_filter_after_bidscomment(){
?>
<div class="notshadow_window" id="window_to_comment">
	<div class="notshadow_window_ins">
		<div class="standart_window_close"></div>
		<div class="standart_window_title" id="comment_the_title"></div>
					
		<div class="standart_windowcontent">
			<form action="<?php pn_the_link_post('bid_user_comment'); ?>" class="ajaxed_comment_form" method="post">

				<p><textarea id="comment_the_text" name="comment"></textarea></p>
				<p><input type="submit" name="submit" class="button-primary" value="<?php _e('Save','pn'); ?>" /></p>
				<input type="hidden" name="id" id="comment_the_id" value="" />
				<input type="hidden" name="vid" id="comment_the_wid" value="0" />
				
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
jQuery(function($){  	

	$(document).on('click', '.js_comment', function(){
		var vid = parseInt($(this).attr('data-id'));
		var id = $(this).parents('.one_bids').attr('id').replace('bidid_','');
		$('#comment_the_id').val(id);
		$('#comment_the_wid').val(vid);
		$('.apply_loader').show();
		$('#window_to_comment').show();
		$('#window_to_comment input[type=submit]').attr('disabled',true);
		
		if(vid == 0){
			$('#comment_the_title').html('<?php _e('Comment to user','pn'); ?>');
		} else {
			$('#comment_the_title').html('<?php _e('Comment to admin','pn'); ?>');
		}
		
		var param = 'id=' + id +'&vid='+ vid;
		$.ajax({
			type: "POST",
			url: "<?php pn_the_link_post('bid_comment_get');?>",
			dataType: 'json',
			data: param,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{		
				$('.apply_loader').hide();
				$('#window_to_comment input[type=submit]').attr('disabled',false);
				if(res['status'] == 'error'){
					<?php do_action('pn_js_alert_response'); ?>
				} else if(res['status'] == 'success'){
					$('#comment_the_text').val(decodeURIComponent(res['comment']));						
				}					
			}
		});		
		
	});
	
	$(document).on('click', '.standart_window_close', function(){
		$('.standart_shadow, #window_to_comment').hide();
	});
	
	$('.ajaxed_comment_form').ajaxForm({
	    dataType:  'json',
        beforeSubmit: function(a,f,o) {
		    $('#window_to_comment input[type=submit]').attr('disabled',true);
        },
		error: function(res, res2, res3) {
			<?php do_action('pn_js_error_response', 'form'); ?>
		},			
        success: function(res) {
			$('#window_to_comment input[type=submit]').attr('disabled',false);
			if(res['status'] == 'error'){ 
				<?php do_action('pn_js_alert_response'); ?>
			} else if(res['status'] == 'success'){
				var vid = $('#comment_the_wid').val();
				var id = $('#comment_the_id').val();
				
				if(res['comment'] == 'true'){
					$('#bidid_'+id).find('.js_comment_'+ vid).addClass('active');
				} else {
					$('#bidid_'+id).find('.js_comment_'+ vid).removeClass('active');
				}
				$('.standart_shadow, #window_to_comment').hide();
			}
        }
    });
	
});	
</script>

<?php	
}

/* comments */
add_action('premium_action_bid_comment_get', 'pn_premium_action_bid_comment_get');
function pn_premium_action_bid_comment_get(){
global $wpdb;
	only_post();
	$log = array();
	$log['status'] = '';
	$log['response'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = __('Error','pn');
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);
	if(current_user_can('administrator') or current_user_can('pn_bids')){
		$id = intval(is_param_post('id'));
		$vid = intval(is_param_post('vid'));
		if($vid == 0){
			$comment = pn_strip_text(get_bids_meta($id,'comment_user'));
		} else {
			$comment = pn_strip_text(get_bids_meta($id,'comment_admin'));
		}
		$log['comment'] = $comment;
		$log['status'] = 'success';
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Authorisation Error','pn');
	}	
	echo json_encode($log);
	exit;
}

add_action('premium_action_bid_user_comment', 'pn_premium_action_bid_user_comment');
function pn_premium_action_bid_user_comment(){
global $wpdb;
	only_post();
	$log = array();
	$log['status'] = '';
	$log['response'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = __('Error','pn');
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);
	if(current_user_can('administrator') or current_user_can('pn_bids')){
		$id = intval(is_param_post('id'));
		$vid = intval(is_param_post('vid'));
		$text = pn_strip_text(is_param_post('comment'));
		if($vid == 0){
			update_bids_meta($id,'comment_user',$text);
		} else {
			update_bids_meta($id,'comment_admin',$text);
		}
		if($text){
			$log['comment'] = 'true';
		} else {
			$log['comment'] = 'false';
		}
		$log['status'] = 'success';
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Authorisation Error','pn');
	}	
	echo json_encode($log);
	exit;	
}
/* end comments */

add_filter('bid_instruction_tags', 'bidscomment_bid_instruction_tags', 100, 2);
function bidscomment_bid_instruction_tags($instruction, $item){
	$comment_user = trim(get_bids_meta($item->id, 'comment_user'));
	if($comment_user){
		if($instruction){ $instruction .= '<br />'; }
		$instruction .= '<span class="comment_user">'. $comment_user .'</span>';
	}	
	return $instruction;
}

add_filter('mail_bids_subject', 'bidscomment_mail_bids_subject', 99, 2);
add_filter('mail_bids_text', 'bidscomment_mail_bids_subject', 99, 2);
function bidscomment_mail_bids_subject($html, $obmen){
	if(strstr($html, '[comment_user]')){
		$comment_user = trim(get_bids_meta($obmen->id, 'comment_user'));
		$html = str_replace('[comment_user]', $comment_user ,$html);
	}
	return $html;
}

add_filter('mailtemp_tags_bids','bidscomment_mailtemp_tags_bids');
function bidscomment_mailtemp_tags_bids($tags){
	$tags['comment_user'] = __('Comment to user','pn');
	return $tags;
}