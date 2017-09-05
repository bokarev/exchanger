<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('myaction_site_invest_createdeposit','def_invest_createdeposit');
function def_invest_createdeposit(){
global $wpdb, $investbox;
	only_post();
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	

	if($user_id){

		$tarid = intval(is_param_post('tarid'));
		
		$toinvest = $investbox->get_page('toinvest');
		$indeposit = $investbox->get_page('indeposit');
		
		if($tarid){
			$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."inex_tars WHERE id='$tarid'");
			if(isset($data->id)){
				
				$minsum = is_my_money($data->minsum);
				$maxsum = is_my_money($data->maxsum);
				$mpers = pn_strip_input($data->mpers);
				$cdays = pn_strip_input($data->cdays);
				
				$account = pn_maxf(pn_strip_input(is_param_post('account')),250);
				$sum = is_my_money(is_param_post('sum'),2);
				if($account){
					if($sum >= $minsum){
						if($sum <= $maxsum or $maxsum == 0){
					
							$array = array();
							$array['couday'] = $cdays;
							$array['pers'] = $mpers;
							$array['insumm'] = $sum;
							$array['user_id'] = $user_id;
							$array['user_login'] = pn_strip_input($ui->user_login);
							$array['user_email'] = is_email($ui->user_email);
							$array['user_schet'] = $account;

							$array['paystatus'] = 0;
							$array['zakstatus'] = 0;
							$array['vipstatus'] = 0;

							$array['gid'] = $gid = $investbox->is_system_name($data->gid);
							$gdata = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."inex_system WHERE gid='$gid'");
							if(isset($gdata->title)){
								$array['gtitle'] = pn_strip_input($gdata->title);
								$array['gvalut'] = pn_strip_input($gdata->valut);
							}
							$plussumm = $sum / 100 * $mpers;
							$outsumm = $sum + $plussumm;
							$array['plussumm'] = $plussumm;
							$array['outsumm'] = $outsumm;

							$array['createdate'] = current_time('mysql');
							$array['locale'] = get_locale();
							
							$wpdb->insert($wpdb->prefix.'inex_deposit', $array);
							$data_id = $wpdb->insert_id;
							
							wp_redirect($indeposit.'?depid='.$data_id);
							exit;					
			
						} else {
							wp_redirect($toinvest);
							exit;					
						}		
					} else {
						wp_redirect($toinvest);
						exit;					
					}
				} else {
					wp_redirect($toinvest);
					exit;				
				}
			} else {
				wp_redirect($toinvest);
				exit;			
			}
		} else {
			pn_display_mess(__('Form error','inex')); 
		}
	} else { 
		pn_display_mess(__('You are not logged in','inex')); 
	}	
}

add_action('myaction_site_invest_paydeposit','def_invest_paydeposit');
function def_invest_paydeposit(){
global $wpdb, $investbox;
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	

	if($user_id){
		$id = intval(is_param_get('id'));
		
		$toinvest = $investbox->get_page('toinvest');
		$indeposit = $investbox->get_page('indeposit');
		if($id){
			$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."inex_deposit WHERE id='$id' AND user_id='$user_id'");
			if(isset($item->id)){
				$time = current_time('timestamp');
				if($item->paystatus == 1){
					if($item->vipstatus == 0){
						if(strtotime($item->enddate) < $time){
							if($item->zakstatus == 0){
								$array = array();
								$array['zakstatus'] = 1;
								$wpdb->update($wpdb->prefix.'inex_deposit', $array, array('id'=>$item->id));
								
								$mailtemp = get_option('inex_mailtemp');
								if(isset($mailtemp['mail2'])){
									$data = $mailtemp['mail2'];
									if($data['send'] == 1){
										
										$to_email = is_email($data['tomail']);
										$site_email = is_email($data['mail']);
										if($to_email){
											$site_name = pn_strip_input($data['name']);
											$subject = pn_strip_input(ctv_ml($data['title']));
											$sitename = pn_strip_input(get_bloginfo('sitename'));
											
											$html = pn_strip_text(ctv_ml($data['text']));
											$html = str_replace('[outsumm]', pn_strip_input($item->insumm .' '. $item->gvalut) ,$html);
											$html = str_replace('[sitename]',$sitename,$html);
											$html = str_replace('[system]', pn_strip_input($item->gtitle .' '. $item->gvalut) ,$html);
											$html = str_replace('[id]',$item->id,$html);
											$html = apply_filters('comment_text', $html);
						
											pn_mail($to_email, $subject, $html, $site_name, $site_email);
										}									
																	
									}
								}
								
								pn_display_mess(__('Deposit successfully sent for further payment','inex'), __('Deposit successfully sent for payment','inex'), 'true');

							} else {
								pn_display_mess(__('Deposit has already ordered for payout','inex'));
							}						
						} else {
							pn_display_mess(__('Deposit is still active','inex')); 
						}			
					} else { 
						pn_display_mess(__('Deposit has already paid','inex')); 
					}
				} else {
					wp_redirect($indeposit.'?depid='. $item->id);
					exit;						
				}
			} else {
				wp_redirect($toinvest);
				exit;
			}
		} else {
			pn_display_mess(__('Form error','inex')); 
		}
	} else { 
		pn_display_mess(__('You are not logged in','inex')); 
	}	
}

add_action('myaction_site_invest_longdeposit','def_invest_longdeposit');
function def_invest_longdeposit(){
global $wpdb, $investbox;

	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);

	if($user_id){

		$id = intval(is_param_get('id'));
		
		$toinvest = $investbox->get_page('toinvest');
		$indeposit = $investbox->get_page('indeposit');
		$long = $investbox->get_option('change', 'long');
		
		if($id and $long == 'true'){
			$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."inex_deposit WHERE id='$id' AND user_id='$user_id'");
			if(isset($item->id)){
				$old_deposit_id = $item->id;
				$time = current_time('timestamp');
				if($item->paystatus == 1){
					if($item->vipstatus == 0){
						if(strtotime($item->enddate) < $time){
							if($item->zakstatus == 0){
								
								$date = current_time('mysql');
								
								$array = array();
								$array['vipstatus'] = 1;
								$array['outdate'] = $date;
								$wpdb->update($wpdb->prefix.'inex_deposit', $array, array('id'=>$item->id));
								
								$mpers = pn_strip_input($item->pers);
								$cdays = pn_strip_input($item->couday);
								$kowel = pn_strip_input($item->user_schet);
								$sum = pn_strip_input($item->outsumm);
								
								$array = array();	
								$array['couday'] = $cdays;
								$array['pers'] = $mpers;
								$array['insumm'] = $sum;
								
								$array['user_id'] = $user_id;
								$array['user_login'] = pn_strip_input($ui->user_login);
								$array['user_email'] = is_email($ui->user_email);
								$array['user_schet'] = $kowel;

								$array['paystatus'] = 1;
								$array['zakstatus'] = 0;
								$array['vipstatus'] = 0;

								$array['gid'] = $item->gid;
								$array['gtitle'] = pn_strip_input($item->gtitle);
								$array['gvalut'] = pn_strip_input($item->gvalut);

								$plussumm = $sum / 100 * $mpers;
								$outsumm = $sum + $plussumm;
								$array['plussumm'] = $plussumm;
								$array['outsumm'] = $outsumm;

								$array['createdate'] = current_time('mysql');
								$array['indate'] = $date;
								$endtime = $time + ($cdays * 24 * 60 * 60);
								$enddate = date('Y-m-d H:i:s', $endtime);
								$array['enddate'] = $enddate;
								$array['locale'] = get_locale();
						
								$wpdb->insert($wpdb->prefix.'inex_deposit', $array);
								$new_deposit_id = $wpdb->insert_id;
								
								$mailtemp = get_option('inex_mailtemp');
								if(isset($mailtemp['mail3'])){
									$data = $mailtemp['mail3'];
									if($data['send'] == 1){
										
										$to_email = $data['tomail'];
										$site_email = $data['mail'];
										if($to_email){
											$site_name = pn_strip_input($data['name']);
											$subject = pn_strip_input(ctv_ml($data['title']));
											$sitename = pn_strip_input(get_bloginfo('sitename'));
											
											$html = pn_strip_text(ctv_ml($data['text']));
											$html = str_replace('[sitename]',$sitename,$html);
											$html = str_replace('[newid]', $new_deposit_id ,$html);
											$html = str_replace('[id]', $old_deposit_id ,$html);
											$html = apply_filters('comment_text', $html);
						
											pn_mail($to_email, $subject, $html, $site_name, $site_email);
										}									
																	
									}
								}							
								
								wp_redirect($toinvest);
								exit;

							} else {
								pn_display_mess(__('Deposit has already ordered for payout','inex')); 
							}						
						} else {
							pn_display_mess(__('Deposit is still active','inex')); 
						}			
					} else {
						pn_display_mess(__('Deposit already paid','inex')); 
					}
				} else {
					wp_redirect($indeposit.'?depid='. $item->id);
					exit;						
				}
			} else {
				wp_redirect($toinvest);
				exit;
			}
		} else {
			pn_display_mess(__('Form error','inex')); 
		}
	} else { 
		pn_display_mess(__('You are not logged in','inex')); 
	}
}