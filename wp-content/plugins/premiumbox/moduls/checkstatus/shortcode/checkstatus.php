<?php
if( !defined( 'ABSPATH')){ exit(); }
 
add_action('pn_adminpage_quicktags_page','pn_adminpage_quicktags_page_checkstatus');
function pn_adminpage_quicktags_page_checkstatus(){
?>
edButtons[edButtons.length] = 
new edButton('premium_checkstatus', '<?php _e('Check order status','pn'); ?>','[checkstatus_form]');
<?php	
}

/* shortcode */
function pn_checkstatus_form_shortcode($atts) {
	
	$temp = '';
	
 	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	$items = get_checkstatus_form_filelds();
	$html = prepare_form_fileds($items, 'checkstatus_form_line', 'checkstatus');
	
	$array = array(
		'[form]' => '<form method="post" class="ajax_post_form" action="'. get_ajax_link('checkstatusform') .'">',
		'[/form]' => '</form>',
		'[result]' => '<div class="resultgo"></div>',
		'[html]' => $html,
		'[submit]' => '<input type="submit" formtarget="_top" name="submit" class="checkstatus_submit" value="'. __('Check', 'pn') .'" />',
	);	
	
	$temp = '
	<div class="checkstatus_div_wrap">
	[form]

		<div class="checkstatus_div_title">
			<div class="checkstatus_div_title_ins">
				'. __('Check order status','pn') .'
			</div>
		</div>
	
		<div class="checkstatus_div">
			<div class="checkstatus_div_ins">
				
				[html]
				
				<div class="checkstatus_line has_submit">
					[submit]
				</div>
				
				[result]
				
			</div>
		</div>
	
	[/form]
	</div>
	';
	
	$temp = apply_filters('checkstatus_form_temp',$temp);
	$temp = get_replace_arrays($array, $temp);
	
	return $temp;
}
add_shortcode('checkstatus_form', 'pn_checkstatus_form_shortcode');
/* end shortcode */

/* обработка формы */
add_action('myaction_site_checkstatusform', 'def_myaction_ajax_checkstatusform');
function def_myaction_ajax_checkstatusform(){
global $or_site_url, $premiumbox, $wpdb;	
	
	only_post();
	
	$log = array();
	$log['response'] = '';
	$log['status'] = '';
	$log['status_code'] = 0;
	$log['status_text'] = '';
	
	$premiumbox->up_mode();
	
	$log = apply_filters('before_ajax_form_field', $log, 'checkstatusform');
	$log = apply_filters('before_ajax_checkstatusform', $log);
	
	$email = is_email(is_param_post('email'));
	$idz = intval(is_param_post('idz')); if($idz < 1){ $idz = 0; }
	
	if($idz){
		$bid = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."bids WHERE status != 'auto' AND id = '$idz'");		
		if(isset($bid->id)){
			$user_email = $bid->user_email;
			if($email == $user_email){
				$log['status'] = 'success_clear';
				$log['status_text'] = __('You will be redirected to the order page now','pn');
				$log['url'] = get_bids_url($bid->hashed);
			} else {
				$log['status'] = 'error';
				$log['status_code'] = 1;
				$log['status_text'] = __('Error! You have entered an invalid e-mail address','pn');				
			}
		} else {
			$log['status'] = 'error';
			$log['status_code'] = 1;
			$log['status_text'] = __('Error! Order does not exist','pn');			
		}
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! You have not entered the order ID','pn');
	}
	
	echo json_encode($log);
	exit;
}
/* end обработка формы */