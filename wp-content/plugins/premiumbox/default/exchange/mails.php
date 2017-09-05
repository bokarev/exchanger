<?php
if( !defined( 'ABSPATH')){ exit(); }
	
add_filter('admin_mailtemp','admin_mailtemp_bids');
function admin_mailtemp_bids($places_admin){
	
	$bid_status_list = apply_filters('bid_status_list',array());
	$bid_status_list['realdelete'] = __('Completely deleted order','pn');
	
	foreach($bid_status_list as $k => $v){
		$t = $v;
		if($k != 'realdelete'){ $t = sprintf(__('Status of order is "%s"','pn'), $v); }
		$places_admin[$k . '_bids1'] = $t;
	}
	
	return $places_admin;
}
	
add_filter('user_mailtemp','user_mailtemp_bids');
function user_mailtemp_bids($places_admin){
	
	$bid_status_list = apply_filters('bid_status_list',array());
	$bid_status_list['realdelete'] = __('Completely deleted order','pn');
	
	foreach($bid_status_list as $k => $v){
		$t = $v;
		if($k != 'realdelete'){ $t = sprintf(__('Status of order is "%s"','pn'), $v); }
		$places_admin[$k . '_bids2'] = $t;
	}
	
	return $places_admin;
}

add_action('init', 'def_bid_mailtemp_init');
function def_bid_mailtemp_init(){
	$bid_status_list = apply_filters('bid_status_list',array());
	$bid_status_list['realdelete'] = __('Completely deleted order','pn');
	foreach($bid_status_list as $k => $v){
		add_filter('mailtemp_tags_'. $k .'_bids1','def_mailtemp_tags_bids');
		add_filter('mailtemp_tags_'. $k .'_bids2','def_mailtemp_tags_bids');
	}
}
function def_mailtemp_tags_bids($tags){
	
	$tags['id'] = __('ID Order','pn');
	$tags['createdate'] = __('Date','pn');
	$tags['curs1'] = __('Rate Send','pn');
	$tags['curs2'] = __('Rate Receive','pn');
	$tags['valut1'] = __('PS name Send','pn');
	$tags['valut2'] = __('PS name Receive','pn');
	$tags['vtype1'] = __('Currency code Send','pn');
	$tags['vtype2'] = __('Currency code Receive','pn');
	$tags['account1'] = __('Account To send','pn');
	$tags['account2'] = __('Account To receive','pn');
	$tags['first_name'] = __('First name','pn');
	$tags['last_name'] = __('Last name','pn');
	$tags['second_name'] = __('Second name','pn');
	$tags['user_phone'] = __('Phone no.','pn');
	$tags['user_skype'] = __('Skype','pn');
	$tags['user_email'] = __('E-mail','pn');
	$tags['user_passport'] = __('Passport number','pn');
	$tags['summ1'] = __('Amount To send','pn');
	$tags['summ1_dc'] = __('Amount To send (add. fees)','pn');
	$tags['summ1c'] = __('Amount Send (PS fee)','pn');
	$tags['summ2'] = __('Amount Receive','pn');
	$tags['summ2_dc'] = __('Amount To receive (add. fees)','pn');
	$tags['summ2c'] = __('Amount Receive (PS fee)','pn');
	$tags['bidurl'] = __('Exchange URL','pn');
	$tags = apply_filters('mailtemp_tags_bids', $tags);
	
	return $tags;
}	

function get_replace_bidmail($html, $data, $bid_locale){
	
    $html = str_replace('[id]',$data->id,$html);
	$html = str_replace('[createdate]', pn_strip_input($data->createdate),$html);
	$html = str_replace('[curs1]', pn_strip_input($data->curs1),$html);
	$html = str_replace('[curs2]', pn_strip_input($data->curs2),$html);
	$html = str_replace('[valut1]', pn_strip_input(ctv_ml($data->valut1,$bid_locale)),$html);
	$html = str_replace('[valut2]', pn_strip_input(ctv_ml($data->valut2,$bid_locale)),$html);
	$html = str_replace('[vtype1]', pn_strip_input($data->vtype1),$html);
	$html = str_replace('[vtype2]', pn_strip_input($data->vtype2),$html);
	$html = str_replace('[account1]', pn_strip_input($data->account1),$html);
	$html = str_replace('[account2]', pn_strip_input($data->account2),$html);
    $html = str_replace('[first_name]', pn_strip_input($data->first_name),$html);
	$html = str_replace('[last_name]', pn_strip_input($data->last_name),$html);
	$html = str_replace('[second_name]', pn_strip_input($data->second_name),$html);
	$html = str_replace('[user_phone]', pn_strip_input($data->user_phone),$html);
	$html = str_replace('[user_skype]', pn_strip_input($data->user_skype),$html);
	$html = str_replace('[user_email]', pn_strip_input($data->user_email),$html);
	$html = str_replace('[user_passport]',pn_strip_input($data->user_passport),$html);
	$html = str_replace('[summ1]', pn_strip_input($data->summ1),$html);
	$html = str_replace('[summ1_dc]', pn_strip_input($data->summ1_dc),$html);
	$html = str_replace('[summ1c]', pn_strip_input($data->summ1c),$html);
	$html = str_replace('[summ2]', pn_strip_input($data->summ2),$html);
	$html = str_replace('[summ2_dc]', pn_strip_input($data->summ2_dc),$html);
	$html = str_replace('[summ2c]', pn_strip_input($data->summ2c),$html);
	$html = str_replace('[bidurl]',get_bids_url($data->hashed),$html);	
	
    return $html;
}

function goed_mail_to_changestatus_bids($obmen_id, $obmen, $name1='', $name2=''){
global $wpdb;	
	
	if(isset($obmen->id)){
	
		$mailtemp = get_option('mailtemp');
		
		/* админу письмо */
		if($name1){
			if(isset($mailtemp[$name1])){
				$data = $mailtemp[$name1];
				if($data['send'] == 1){
					$ot_mail = is_email($data['mail']);
					$ot_name = pn_strip_input($data['name']);			
					$subject = pn_strip_input(ctv_ml($data['title'],$obmen->bid_locale));
					$sitename = pn_strip_input(get_bloginfo('sitename'));			
					$html = pn_strip_text(ctv_ml($data['text'],$obmen->bid_locale));			
					if($data['tomail']){		
						$to_mail = $data['tomail'];								
						$subject = str_replace('[sitename]', $sitename ,$subject);
						$subject = get_replace_bidmail($subject, $obmen, $obmen->bid_locale);
						$subject = apply_filters('mail_bids_subject',$subject, $obmen);						
						$html = str_replace('[sitename]', $sitename ,$html);
						$html = get_replace_bidmail($html, $obmen, $obmen->bid_locale);
						$html = apply_filters('mail_bids_text',$html, $obmen);
						$html = apply_filters('comment_text',$html);									
																								
						pn_mail($to_mail, $subject, $html, $ot_name, $ot_mail);	 
					}
				}	
			}
		}
		
		/* юзеру письмо */
		$user_email = is_email($obmen->user_email);
		if($name2){
			if(isset($mailtemp[$name2])){
				$data = $mailtemp[$name2];
				if($data['send'] == 1){
					$ot_mail = is_email($data['mail']);
					$ot_name = pn_strip_input($data['name']);
					$sitename = pn_strip_input(get_bloginfo('sitename'));			
					$subject = pn_strip_input(ctv_ml($data['title'],$obmen->bid_locale));			
					$html = pn_strip_text(ctv_ml($data['text'],$obmen->bid_locale));			
					if(is_email($user_email)){		
						$to_mail = $user_email;		
						$subject = str_replace('[sitename]', $sitename ,$subject);
						$subject = get_replace_bidmail($subject, $obmen, $obmen->bid_locale);
						$subject = apply_filters('mail_bids_subject',$subject, $obmen);
						$html = str_replace('[sitename]', $sitename ,$html);
						$html = get_replace_bidmail($html, $obmen, $obmen->bid_locale);
						$html = apply_filters('mail_bids_text',$html, $obmen);
						$html = apply_filters('comment_text',$html);			
						
						pn_mail($user_email, $subject, $html, $ot_name, $ot_mail);	 
					}
				}	
			}	
		}
	}		
}

add_action('change_bidstatus_all','def_change_bidstatus_all',1,4);
function def_change_bidstatus_all($status, $obmen_id, $obmen, $place='site'){ 
global $wpdb, $premiumbox;
	
	$action1 = '';
	if($place == 'site' or $premiumbox->get_option('exchange','admin_mail') == 1){
		$action1 = $status.'_bids1';
	}
	$action2 = $status.'_bids2';
	goed_mail_to_changestatus_bids($obmen_id, $obmen, $action1, $action2);	
	
}