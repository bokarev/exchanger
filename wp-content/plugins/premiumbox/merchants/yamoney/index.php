<?php
/*
title: [ru_RU:]Yandex money[:ru_RU][en_US:]Yandex money[:en_US]
description: [ru_RU:]мерчант Yandex money[:ru_RU][en_US:]Yandex money merchant[:en_US]
version: 1.2
*/

if(!class_exists('merchant_yamoney')){
	class merchant_yamoney extends Merchant_Premiumbox {

		function __construct($file, $title)
		{
			$map = array(
				'YANDEX_MONEY_ACCOUNT', 'YANDEX_MONEY_APP_ID', 'YANDEX_MONEY_APP_KEY','YANDEX_MONEY_SECRET_KEY'
			);
			parent::__construct($file, $map, $title);
			
			add_action('get_merchant_admin_options', array($this, 'get_merchant_admin_options'), 10, 2);
			add_action('before_merchant_admin', array($this,'before_merchant_admin'));
			add_filter('merchants_settingtext_'. $this->name, array($this, 'merchants_settingtext'));
			add_filter('merchant_formstep_autocheck',array($this, 'merchant_formstep_autocheck'),1,2);
			add_filter('merchant_settings_save',array($this, 'merchant_settings_save'),1,3);
			add_action('myaction_merchant_'. $this->name .'_verify', array($this,'myaction_merchant_verify'));
			add_filter('merchants_action_bid', array($this,'merchants_action_bid'),99,5);
			add_action('myaction_merchant_'. $this->name .'_cron' . get_hash_result_url($this->name), array($this,'myaction_merchant_cron'));
			add_action('myaction_merchant_'. $this->name .'_status' . get_hash_result_url($this->name), array($this,'myaction_merchant_status'));
		}

		function list_merchants($list_merchants){

			$list_merchants[] = array(
				'id' => $this->name,
				'title' => $this->title,
			);
			$list_merchants[] = array(
				'id' => $this->name.'_card',
				'title' => $this->title.' (card)',
			);			
			
			return $list_merchants;
		}

		function before_merchant_admin($m_id){
			if($m_id and $m_id == $this->name or $m_id and $m_id == $this->name.'_card'){
			
				echo '<div class="premium_reply theerror">'. sprintf(__('You have to pass <a href="%s" target="_blank">application authorization</a> in order to proceed.','pn'), get_merchant_link($this->name.'_verify')) .'</div>';
					
			}
		}		
		
		function get_merchant_admin_options($options, $m_id){ 
			if($m_id and $m_id == $this->name or $m_id and $m_id == $this->name.'_card'){
				
				if(isset($options['check_api'])){
					unset($options['check_api']);
				}
				if(isset($options['check_payapi'])){
					unset($options['check_payapi']);
				}				
				
				$text = '
				<strong>'. __('Enter address to create new application','pn') .':</strong> <a href="https://sp-money.yandex.ru/myservices/new.xml" target="_blank">https://sp-money.yandex.ru/myservices/new.xml</a>.<br />
				<strong>Redirect URI:</strong> <a href="'. get_merchant_link($this->name.'_verify') .'" target="_blank">'. get_merchant_link($this->name.'_verify') .'</a><br />
				<strong>Cron:</strong> <a href="'. get_merchant_link($this->name.'_cron' . get_hash_result_url($this->name)) .'" target="_blank">'. get_merchant_link($this->name.'_cron' . get_hash_result_url($this->name)) .'</a><br />					
				<strong>HTTP-notification URL:</strong> <a href="'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'" target="_blank">'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'</a>
				';

				$options['text'] = array(
					'view' => 'textfield',
					'title' => '',
					'default' => $text,
				);									
			
			}
			
			return $options;	
		}	
		
		function merchant_settings_save($merchants, $m_id, $wid){
			
			if($m_id and $m_id == $this->name){
				$merchants[$m_id.'_card'] = $wid;
			}
			
			return $merchants;
		}
		
		function merchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'YANDEX_MONEY_ACCOUNT') 
			){
				$text = '';
			}
			
			return $text;
		}
		
		function get_merchant_id($now, $m_id, $item){
			
			if($m_id and $m_id == $this->name or $m_id and $m_id == $this->name.'_card'){
				if(is_enable_merchant($this->name)){
					return $m_id;
				} 
			}			
			
			return $now;
		}	

		function merchant_formstep_autocheck($autocheck, $m_id){
			
			if($m_id and $m_id == $this->name or $m_id and $m_id == $this->name.'_card'){
				$autocheck = 1;
			}
			
			return $autocheck;
		}		

		function myaction_merchant_verify(){
			
			if(current_user_can('administrator') or current_user_can('pn_merchants')){
				
				if( isset( $_GET['code'] ) ) {
						
					$oClass = new YaMoney(is_deffin($this->m_data,'YANDEX_MONEY_APP_ID'), is_deffin($this->m_data,'YANDEX_MONEY_APP_KEY'), $this->name);
					$token = $oClass->auth();
					if($token){
							
						$res = $oClass->accountInfo($token);
						if( !isset( $res['account'] ) or $res['account'] != is_deffin($this->m_data,'YANDEX_MONEY_ACCOUNT') ){
								
							pn_display_mess(sprintf(__('Authorization can me made from account %s','pn'), is_deffin($this->m_data,'YANDEX_MONEY_ACCOUNT')));
								
						} else {
								
							$oClass->update_token($token);
							wp_redirect(admin_url('admin.php?page=pn_data_merchants&m_id='. $this->name .'&reply=true'));
							exit;
								
						}
							
					} else {
							
						pn_display_mess(__('Retry','pn'));
							
					}
						
				} else {
						
					$oClass = new YaMoney(is_deffin($this->m_data,'YANDEX_MONEY_APP_ID'), is_deffin($this->m_data,'YANDEX_MONEY_APP_KEY'), $this->name);
					$res = $oClass->accountInfo();

					if( !isset( $res['account'] ) or $res['account'] != is_deffin($this->m_data,'YANDEX_MONEY_ACCOUNT') ){
							
						header( 'Location: https://sp-money.yandex.ru/oauth/authorize?client_id='. is_deffin($this->m_data,'YANDEX_MONEY_APP_ID') .'&response_type=code&redirect_uri='. urlencode( get_merchant_link($this->name.'_verify') ) .'&scope=account-info operation-history operation-details payment-p2p ');
						exit();
							
					} else {
						pn_display_mess(__('Payment system is configured','pn'), __('Payment system is configured','pn'),'true');
					}
						
				}
				
			} else {
				pn_display_mess(__('Error! insufficient privileges!','pn'));	
			}
		}		
		
		function merchants_action_bid($temp, $m_id, $pay_sum, $item, $naps){

			if($m_id and $m_id == $this->name or $m_id and $m_id == $this->name.'_card'){
					
				$vtype = pn_strip_input($item->vtype1);	
				$vtype = str_replace('RUR','RUB',$vtype);
							
				$pay_sum = is_my_money($pay_sum,2); 							
						
				$text_pay = get_text_pay($m_id, $item, $pay_sum);
				$text_pay2 = __('ID Order','pn').' '. $item->id;
						
				$temp = '
				<form name="pay" action="https://money.yandex.ru/quickpay/confirm.xml" method="post" target="_blank">
					<input name="receiver" type="hidden" value="'. is_deffin($this->m_data,'YANDEX_MONEY_ACCOUNT') .'">
					';
							
					if($m_id == $this->name.'_card'){
						$temp .= '<input name="paymentType" type="hidden" value="AC" />';
					}
						
					//<input name="formcomment" type="hidden" value="'. $text_pay .'" />
					//<input name="short-dest" type="hidden" value="'. $text_pay .'" />
						
					$temp .= '
					<input name="targets" type="hidden" value="'. $text_pay .'" />					
					<input name="writable-targets" type="hidden" value="false" />
					<input name="quickpay-form" type="hidden" value="shop" />               
					<input name="sum" type="hidden" value="'. $pay_sum .'" />					
					<input name="comment" type="hidden" value="'. $text_pay2 .'" />
					<input name="label" type="hidden" value="'. $item->id .'" />
							
					<input type="submit" value="'. __('Make a payment','pn') .'" />
				</form>									
				';				
						
			}
			
			return $temp;
		}

		function myaction_merchant_status(){
		
			do_action('merchant_logs', $this->name);
		
			if(isset($_POST['notification_type'],$_POST['operation_id'],$_POST['amount'],$_POST['currency'],$_POST['datetime'],$_POST['sender'],$_POST['codepro'],$_POST['label'])){
				$secret = is_deffin($this->m_data,'YANDEX_MONEY_SECRET_KEY');
				$s = $_POST['notification_type'].'&'.$_POST['operation_id'].'&'.$_POST['amount'].'&'.$_POST['currency'].'&'.$_POST['datetime'].'&'.$_POST['sender'].'&'.$_POST['codepro'].'&'.$secret.'&'.$_POST['label'];
				if(hash('sha1',$s) == $_POST['sha1_hash']){
					
					$id = intval($_POST['label']);
					$data = get_data_merchant_for_id($id);
					$in_summ = $_POST['amount'];
					
					$err = $data['err'];
					$status = $data['status'];
					$m_id = $data['m_id'];
					$vtype = $data['vtype'];
					$vtype = str_replace('RUR','RUB',$vtype);
						
					$sender = $_POST['sender'];
						
					$bid_sum = is_my_money($data['pay_sum'],2);	
					$bid_sum = apply_filters('merchant_bid_sum', $bid_sum, $m_id);
					
					if($status == 'new' and $err == 0){
						if($vtype == 'RUB'){
							if($m_id and $m_id == $this->name){ 	
								if($in_summ >= $bid_sum){			
									if($_POST['notification_type'] != 'p2p-incoming'){
										$sender .= ' card';
									}
									$pay_purse = is_pay_purse($sender, $data, $m_id);
							 
									$params = array(
										'pay_purse' => $pay_purse,
										'sum' => $in_summ,
										'naschet' => is_deffin($this->m_data,'YANDEX_MONEY_ACCOUNT'),
										'trans_in' => $_POST['operation_id'],
									);
									the_merchant_bid_status('realpay', $id, 'user', 0, '', $params);							 
								} 	
							} elseif($m_id and $m_id == $this->name.'_card') {		
								if($in_summ >= $bid_sum){			
									if($_POST['notification_type'] == 'p2p-incoming'){
										$sender .= ' purse';
									}	
									$pay_purse = is_pay_purse($sender, $data, $m_id);
										
									$params = array(
										'pay_purse' => $pay_purse,
										'sum' => $in_summ,
										'naschet' => is_deffin($this->m_data,'YANDEX_MONEY_ACCOUNT'),
										'trans_in' => $_POST['operation_id'],
									);
									the_merchant_bid_status('realpay', $id, 'user', 0, '', $params);										
								}	
							}
						}
					}						
					
				}
			}
			
		}
		
		function myaction_merchant_cron(){
			
			try{	

				$oClass = new YaMoney(is_deffin($this->m_data,'YANDEX_MONEY_APP_ID'), is_deffin($this->m_data,'YANDEX_MONEY_APP_KEY'), $this->name);
				$res = $oClass->operationHistory( 'deposition', null, null, null, null, 30, true );
				foreach( isset( $res['operations'] ) ? $res['operations'] : array() as $aOperation ) {
					# Фильтрация по нашим платежам :
					if( $aOperation['status'] == 'success' and $aOperation['direction'] == 'in' and isset( $aOperation['label'] ) ){
						$iSender = is_isset($aOperation,'sender'); 
						
						$trans_id = is_isset($aOperation,'operation_id'); 
						
						$pattern_id = '';
						if(isset($aOperation['pattern_id'])){
							$pattern_id = $aOperation['pattern_id']; //p2p
						}
						$sOrder = $aOperation['label']; //id заявки
						$dAmount = $aOperation['amount'] - 0;	//сумма
					
						$id = intval($sOrder);
						$data = get_data_merchant_for_id($id);
						$in_summ = $dAmount;
					
						$err = $data['err'];
						$status = $data['status'];
						$m_id = $data['m_id'];
						$vtype = $data['vtype'];
						$vtype = str_replace('RUR','RUB',$vtype);
						
						$bid_sum = is_my_money($data['pay_sum'],2);	
						$bid_sum = apply_filters('merchant_bid_sum', $bid_sum, $m_id);
						if($status == 'new' and $err == 0){
							if($vtype == 'RUB'){
								if($m_id and $m_id == $this->name){ 	
									if($in_summ >= $bid_sum){			
										if($pattern_id != 'p2p'){
											$iSender = $iSender . ' card';
										}
										$pay_purse = is_pay_purse($iSender, $data, $m_id);
							
										$params = array(
											'pay_purse' => $pay_purse,
											'sum' => $in_summ,
											'naschet' => is_deffin($this->m_data,'YANDEX_MONEY_ACCOUNT'),
											'trans_in' => $trans_id,
										);
										the_merchant_bid_status('realpay', $id, 'user', 0, '', $params);							
									} 	
								} elseif($m_id and $m_id == $this->name.'_card') {		
									if($in_summ >= $bid_sum){			
										if($pattern_id == 'p2p'){
											$iSender = $iSender . ' purse';
										}	
										$pay_purse = is_pay_purse($iSender, $data, $m_id);
										
										$params = array(
											'pay_purse' => $pay_purse,
											'sum' => $in_summ,
											'naschet' => is_deffin($this->m_data,'YANDEX_MONEY_ACCOUNT'),
											'trans_in' => $trans_id,
										);
										the_merchant_bid_status('realpay', $id, 'user', 0, '', $params);										
									}	
								}
							}
						}	
					
					}
				}
						
			}
			catch (Exception $e)
			{
							
			}			
	
		}
		
	}
}

new merchant_yamoney(__FILE__, 'Yandex money');