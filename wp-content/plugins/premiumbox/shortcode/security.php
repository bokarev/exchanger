<?php
if( !defined( 'ABSPATH')){ exit(); }

function security_page_shortcode($atts, $content) {
global $wpdb;

	$temp = '';
	
    $temp .= apply_filters('before_security_page','');
			
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);		
			
	if($user_id){
			
		$items = get_security_form_filelds();	
		$html = prepare_form_fileds($items, 'account_form_line', 'acf');
	
		$array = array(
			'[form]' => '<form method="post" class="ajax_post_form" action="'. get_ajax_link('securityform') .'">',
			'[/form]' => '</form>',
			'[result]' => '<div class="resultgo"></div>',
			'[html]' => $html,
			'[submit]' => '<input type="submit" formtarget="_top" name="submit" class="acf_submit" value="'. __('Save', 'pn') .'" />',
		);	
	
		$temp_form = '
		<div class="acf_div_wrap">
		[form]
			
			<div class="acf_div_title">
				<div class="acf_div_title_ins">
					'. __('Security settings','pn') .'
				</div>
			</div>
		
			<div class="acf_div">
				<div class="acf_div_ins">
					
					[html]
					
					<div class="acf_line has_submit">
						[submit]
					</div>
					
					[result]
				</div>
			</div>

		[/form]
		</div>
		';
	
		$temp_form = apply_filters('account_form_temp',$temp_form);
		$temp .= get_replace_arrays($array, $temp_form);		

	} else {
		$temp .= '<div class="resultfalse">'. __('Error! You must authorize','pn') .'</div>';
	}
	
    $after = apply_filters('after_security_page','');
    $temp .= $after;	
	
	return $temp;
}
add_shortcode('security_page', 'security_page_shortcode');

/* обработка формы сайта */
add_action('myaction_site_securityform', 'def_myaction_ajax_securityform');
function def_myaction_ajax_securityform(){
global $wpdb, $premiumbox;	
	
	only_post();
	
	$log = array();
	$log['response'] = ''; 
	$log['status'] = '';
	$log['status_code'] = 0;
	$log['status_text'] = '';
	
	$premiumbox->up_mode();
	
	$log = apply_filters('before_ajax_form_field', $log, 'securityform');
	$log = apply_filters('before_ajax_securityform', $log);
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	if(!$user_id){
		$log['status'] = 'error'; 
		$log['status_code'] = 1;
		$log['status_text']= __('Error! You must authorize','pn');
		echo json_encode($log);
		exit;		
	}
		
	$array = array();
	$array['sec_lostpass'] = intval(is_param_post('sec_lostpass'));
	$array['sec_login'] = intval(is_param_post('sec_login'));
	$array['email_login'] = intval(is_param_post('email_login'));
	$array['enable_ips'] = pn_maxf(pn_strip_input(is_param_post('enable_ips')),1500);
	$wpdb->update($wpdb->prefix ."users", $array, array('ID'=>$user_id));
		
    $log['status'] = 'success';
    $log['status_text'] = apply_filters('ajax_security_success_message', __('Data successfully saved','pn'));				
	
	echo json_encode($log);
	exit;
}
/* end обработка формы сайта */