<?php
/*
title: [ru_RU:]Paypal[:ru_RU][en_US:]Paypal[:en_US]
description: [ru_RU:]мерчант Paypal[:ru_RU][en_US:]Paypal merchant[:en_US]
version: 1.2
*/

if(!class_exists('merchant_paypal')){
	class merchant_paypal extends Merchant_Premiumbox{

		function __construct($file, $title)
		{
			$map = array(
				'PAYPAL_BUSINESS_ACCOUNT', 
			);
			parent::__construct($file, $map, $title);
			
			add_filter('merchants_settingtext_'.$this->name, array($this, 'merchants_settingtext'));
			add_action('get_merchant_admin_options_'. $this->name, array($this, 'get_merchant_admin_options'), 10, 2);
			add_filter('merchant_formstep_autocheck',array($this, 'merchant_formstep_autocheck'),1,2);
			add_filter('merchants_action_bid_'.$this->name, array($this,'merchants_action_bid'),99,4);
			add_action('myaction_merchant_'. $this->name .'_fail', array($this,'myaction_merchant_fail'));
			add_action('myaction_merchant_'. $this->name .'_success', array($this,'myaction_merchant_success'));
			add_action('myaction_merchant_'. $this->name .'_status', array($this,'myaction_merchant_status'));
		}	
		
		function get_merchant_admin_options($options, $data){ 
			
			if(isset($options['resulturl'])){
				unset($options['resulturl']);
			}
			if(isset($options['check_api'])){
				unset($options['check_api']);
			}
			if(isset($options['check_payapi'])){
				unset($options['check_payapi']);
			}			
			
			$text = '
			<strong>Status URL:</strong> <a href="'. get_merchant_link($this->name.'_status') .'" target="_blank">'. get_merchant_link($this->name.'_status') .'</a><br />
			<strong>Success URL:</strong> <a href="'. get_merchant_link($this->name.'_success') .'" target="_blank">'. get_merchant_link($this->name.'_success') .'</a><br />
			<strong>Fail URL:</strong> <a href="'. get_merchant_link($this->name.'_fail') .'" target="_blank">'. get_merchant_link($this->name.'_fail') .'</a>			
			';

			$options['text'] = array(
				'view' => 'textfield',
				'title' => '',
				'default' => $text,
			);									
			
			return $options;	
		}		
		
		function merchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'PAYPAL_BUSINESS_ACCOUNT')  
			){
				$text = '';
			}
			
			return $text;
		}	

		function merchant_formstep_autocheck($autocheck, $m_id){
			
			if($m_id and $m_id == $this->name){
				$autocheck = 1;
			}
			
			return $autocheck;
		}		

		function merchants_action_bid($temp, $pay_sum, $item, $naps){

			$vtype = pn_strip_input($item->vtype1);
					
			$pay_sum = is_my_money($pay_sum,2);				
			$text_pay = get_text_pay($this->name, $item, $pay_sum);
					
			$temp = '
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
					<input type="hidden" name="cmd" value="_xclick" />
					<input type="hidden" name="notify_url" value="'. get_merchant_link($this->name.'_status') .'" />
					<input type="hidden" name="currency_code" value="'. $vtype .'" />
					<input type="hidden" name="business" value="'. is_deffin($this->m_data,'PAYPAL_BUSINESS_ACCOUNT') .'" />
					<input type="hidden" name="return" value="'. get_merchant_link($this->name.'_success') .'" />
					<input type="hidden" name="rm" value="0" />
					<input type="hidden" name="cancel_return" value="'. get_merchant_link($this->name.'_fail') .'" />
					<input type="hidden" name="charset" value="UTF-8" />
					<input type="hidden" name="item_number" value="'. $item->id .'" />
					<input type="hidden" name="item_name" value="'. $text_pay .'" />
					<input type="hidden" name="amount" value="'. $pay_sum .'" />
					<input type="submit" value="'. __('Make a payment','pn') .'" />
				</form>													
			';				
		
			return $temp;				
		}

		function myaction_merchant_fail(){
	
			$id = get_payment_id('item_number');
			the_merchant_bid_delete($id);
	
		}

		function myaction_merchant_success(){
	
			$id = get_payment_id('item_number');
			the_merchant_bid_success($id);
	
		}

		function myaction_merchant_status(){
	
			do_action('merchant_logs', $this->name);

			if(isset($_POST["ipn_track_id"], $_POST["item_number"], $_POST["mc_gross"]) and is_numeric($_POST["mc_gross"]) and $_POST["mc_gross"] > 0){

				$aResponse = array();

				foreach($_POST as $sKey => $sValue){
					if(get_magic_quotes_gpc()){
						$sKey = stripslashes($sKey);
						$sValue = stripslashes($sValue);
					}

					$aResponse[] = $sKey . "=" . $sValue;
					$aResponseUrl[] = $sKey . "=" . urlencode($sValue);
				}

				$c_options = array(
					CURLOPT_HEADER => 0,
					CURLOPT_POST => 1,
					CURLOPT_POSTFIELDS => "cmd=_notify-validate&" . implode("&", $aResponseUrl),
					CURLOPT_SSL_VERIFYHOST => 1,
				);
				$result = get_curl_parser("https://www.paypal.com/cgi-bin/webscr", $c_options, 'merchant', 'paypal');
				$sResponse = $result['output'];

				if($sResponse == "VERIFIED"){
					
					$currency = $_POST["mc_currency"];
					$payer_purse = $_POST["payer_email"];
					$id = $_POST["item_number"];
					$data = get_data_merchant_for_id($id);
					$in_summ = $_POST["mc_gross"];
					$in_summ = is_my_money($in_summ,2);
					
					$err = $data['err'];
					$status = $data['status'];
					$m_id = $data['m_id'];
					$pay_purse = is_pay_purse($payer_purse, $data, $m_id);
					$vtype = $data['vtype'];	
					$bid_sum = is_my_money($data['pay_sum'],2);
					$bid_sum = apply_filters('merchant_bid_sum', $bid_sum, $m_id);
					if($status == 'new'){ 
						if($err == 0){
							if($m_id and $m_id == $this->name){
								if($vtype == $currency){
									if($in_summ >= $bid_sum){		
							
										$params = array(
											'pay_purse' => $pay_purse,
											'sum' => $in_summ,
											'naschet' => is_deffin($this->m_data,'PAYPAL_BUSINESS_ACCOUNT'),
											'trans_in' => $_POST["ipn_track_id"],
										);
										the_merchant_bid_status('realpay', $id, 'user', 0, '', $params);							
													 
										die( 'Completed' );
										
									} else {
										die('The payment amount is less than the provisions');
									}
								} else {
									die('Wrong type of currency');
								}
							} else {
								die('At the direction of off merchant');
							}
						} else {
							die( 'The application does not exist or the wrong ID' );
						}
					} else {
						die( 'In the application the wrong status' );
					}		
					
				}
			}	
	
		}
		
	}
}

new merchant_paypal(__FILE__, 'Paypal');