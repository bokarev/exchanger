<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('myaction_request_investcron','def_myaction_request_investcron');
function def_myaction_request_investcron(){
global $wpdb;	
	
	/* удаляем не оплаченные депозиты, через 7 дней */
	$time = current_time('timestamp') - (7 * DAY_IN_SECONDS);
    $ldate = date('Y-m-d H:00:00', $time);
	$appls = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."inex_deposit WHERE createdate < '$ldate' AND paystatus='0'");
	foreach($appls as $item){
		$id = $item->id;	
	    $wpdb->query("DELETE FROM ".$wpdb->prefix."inex_deposit WHERE id = '$id'");
	}	
	/* end удаляем не оплаченные депозиты, через 7 дней */
	
	/* уведомление клиенту о окончании депозита */
	$date = current_time('mysql');
	$mailtemp = get_option('inex_mailtemp');
	if(isset($mailtemp['mail1u'])){
		$data = $mailtemp['mail1u'];
		if($data['send'] == 1){ 
	
			$aus = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."inex_deposit WHERE paystatus='1' AND mail1='0' AND enddate < '$date'");
			foreach($aus as $au){
				$auid = $au->id;
				$wpdb->update($wpdb->prefix.'inex_deposit', array('mail1'=> 1), array('id'=> $auid));
		
				$user_email = is_email($au->user_email);
				$site_email = is_email($data['mail']);
				if($user_email){
					
					$locale = pn_strip_input($au->locale);
					
					$site_name = pn_strip_input($data['name']);
					$sitename = pn_strip_input(get_bloginfo('sitename'));
					$subject = pn_strip_input(ctv_ml($data['title'], $locale));
					
					$html = pn_strip_text(ctv_ml($data['text'], $locale));
					$html = str_replace('[outsumm]', pn_strip_input($au->outsumm .' '. $au->gvalut) ,$html);
					$html = str_replace('[sitename]',$sitename,$html);
					$html = str_replace('[id]',$au->id,$html);
					$html = apply_filters('comment_text', $html);
	
					pn_mail($user_email, $subject, $html, $site_name, $site_email);
					
				}						
		
			}
		
		}
	}		
	
	_e('Done','inex');
}