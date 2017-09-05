<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('siteplace_js','siteplace_js_zreserv');
function siteplace_js_zreserv(){	
	if(is_enable_zreserve()){
?>	
/* request reserve */
jQuery(function($){ 
	$(document).on('click', '.js_reserv', function(){
		var title = $(this).attr('data-title');
        var id = $(this).attr('data-id');		
		$('#reserv_box_title').html(title);	
		$('#reserv_box_id').attr('value',id);
		
		$('#the_shadow, .reserv_box').show();
		$('.reserv_box .resultgo').html(' ');	
			
	    var hei = Math.ceil(($(window).height() - $('.reserv_box_ins').height()) / 2);
	    $('.reserv_box').css({'top':hei});			
			
	    return false;
	});	
	
    $(document).on('click','.reserv_box_close', function(){
		$('#the_shadow, .reserv_box').hide();
    });	
});
/* end request reserve */	
<?php	
	}
} 

add_action('pn_js_error_response','zreserv_js_error_response');
function zreserv_js_error_response(){	
?>
	$('#the_shadow, .reserv_box').hide();
<?php	
} 

add_action('wp_footer','wp_footer_zreserv');
function wp_footer_zreserv(){
    if(is_front_page() and is_enable_zreserve()){
		global $wpdb;
		
		$ui = wp_get_current_user();
		$user_id = intval($ui->ID);
		$items = get_zreserv_form_filelds();
		$html = prepare_form_fileds($items, 'reserv_form_line', 'rb');			
		
		$array = array(
			'[form]' => '<form method="post" class="ajax_post_form" action="'. get_ajax_link('reservform') .'"><input type="hidden" name="id" id="reserv_box_id" value="0" />',
			'[/form]' => '</form>',
			'[result]' => '<div class="resultgo"></div>',
			'[html]' => $html,
			'[techtitle]' => '<span id="reserv_box_title"></span>',
			'[submit]' => '<input type="submit" formtarget="_top" name="submit" value="'. __('Send a request', 'pn') .'" />',
		);
		$temp = ' 
		<div class="reserv_box">
			<div class="reserv_box_ins">
				<div class="reserv_box_title">'. __('Request to reserve','pn') .' "[techtitle]"</div>
				<div class="reserv_box_close"></div>
				[form]
					[result]
					
					[html]
					
					<p>[submit]</p>
				[/form]
			</div>
		</div>		
		';
		
		$temp = apply_filters('zreserv_form_temp',$temp);
		echo get_replace_arrays($array, $temp);		
    } 
}

add_action('myaction_site_reservform', 'def_myaction_site_reservform');
function def_myaction_site_reservform(){
global $wpdb, $premiumbox;	
	
	only_post();
	
	$log = array();
	$log['response'] = '';
	$log['status'] = '';
	$log['status_code'] = 0;
	$log['status_text'] = '';
	
	$premiumbox->up_mode();
	
	if(is_enable_zreserve()){
	
		$log = apply_filters('before_ajax_form_field', $log, 'reservform');
		$log = apply_filters('before_ajax_reservform', $log);
		$id = intval(is_param_post('id'));
		$sum = is_my_money(is_param_post('sum'),2);
		$email = is_email(is_param_post('email'));
		$comment = pn_maxf_mb(pn_strip_input(is_param_post('comment')),500);
		
		if($sum > 0){
			if($email){
				$naps = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."naps WHERE id='$id' AND naps_status='1' AND autostatus='1'");
				if(isset($naps->id)){
					$last = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."reserve_requests WHERE user_email = '$email' AND naps_id='$id'");
					
					$array = array();
					$array['rdate'] = current_time('mysql');
					$array['naps_id'] = $id;
					$array['naps_title'] = pn_strip_input($naps->tech_name);
					$array['user_email'] = $email;
					$array['comment'] = $comment;
					$array['amount'] = $sum;
					$array['locale'] = get_locale();
					
					if(isset($last->id)){
						$wpdb->update($wpdb->prefix ."reserve_requests", $array, array('id'=>$last->id));
					} else {
						$wpdb->insert($wpdb->prefix ."reserve_requests", $array);
					}
					
					$mailtemp = get_option('mailtemp');
					if(isset($mailtemp['zreserv_admin'])){
						$data = $mailtemp['zreserv_admin'];
						if($data['send'] == 1){
							
							$locale = $array['locale'];
							$ot_mail = is_email($data['mail']);
							$ot_name = pn_strip_input($data['name']);
							$sitename = pn_strip_input(get_bloginfo('sitename'));
							$subject = pn_strip_input(ctv_ml($data['title'],$locale));		
							$html = pn_strip_text(ctv_ml($data['text'],$locale));

							if($data['tomail']){
		
								$to_mail = $data['tomail'];
									
								$sarray = array(
									'[sitename]' => $sitename,
									'[email]' => $array['email'],
									'[sum]' => $array['zsum'],
									'[direction]' => $array['naps_title'],
									'[comment]' => $comment,
								);							
								$subject = get_replace_arrays($sarray, $subject);									
								$subject = apply_filters('mail_zreserv_subject',$subject);
								
								$html = get_replace_arrays($sarray, $html);
								$html = apply_filters('mail_zreserv_text',$html);
								$html = apply_filters('comment_text',$html);
													
								pn_mail($to_mail, $subject, $html, $ot_name, $ot_mail);	
							}
						}
					}						
					
					$log['status'] = 'success_clear';
					$log['status_text'] = __('Request has been successfully created','pn');						
		
				} else {
					$log['status'] = 'error';
					$log['status_code'] = 1;
					$log['status_text'] = __('Error! Direction does not exist','pn');			
				}
			} else {
				$log['status'] = 'error';
				$log['status_code'] = 1;
				$log['status_text'] = __('Error! You have not entered e-mail','pn');
			}
		} else {	
			$log['status'] = 'error';
			$log['status_code'] = 1;
			$log['status_text'] = __('Error! Requested amount is lesser than zero','pn');
		}	
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! You have not entered e-mail','pn');		
	}
	
	echo json_encode($log);
	exit;
}