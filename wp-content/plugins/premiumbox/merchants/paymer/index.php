<?php
/*
title: [ru_RU:]Paymer[:ru_RU][en_US:]Paymer[:en_US]
description: [ru_RU:]мерчант Paymer[:ru_RU][en_US:]Paymer merchant[:en_US]
version: 1.2
*/

if(!class_exists('merchant_paymer')){
	class merchant_paymer extends Merchant_Premiumbox{

		function __construct($file, $title)
		{
			$map = array(
				'PAYMER_MERCHANT_ID', 'PAYMER_SECRET_KEY',
				'PAYMER_LOGIN','PAYMER_PASSWORD','PAYMER_WMZ_PURSE','PAYMER_WMR_PURSE','PAYMER_WME_PURSE',
				'PAYMER_WMU_PURSE','PAYMER_WMB_PURSE','PAYMER_WMY_PURSE','PAYMER_WMG_PURSE',
				'PAYMER_WMX_PURSE','PAYMER_WMK_PURSE',				
			);
			parent::__construct($file, $map, $title);
			
			add_action('get_merchant_admin_options_'. $this->name, array($this, 'get_merchant_admin_options'), 10, 2);
			add_filter('merchants_settingtext_'.$this->name, array($this, 'merchants_settingtext'));
			add_filter('merchant_formstep_autocheck',array($this, 'merchant_formstep_autocheck'),1,2);
			add_filter('merchants_action_bid_'.$this->name, array($this,'merchants_action_bid'),99,4);
			add_action('myaction_merchant_'. $this->name .'_fail', array($this,'myaction_merchant_fail'));
			add_action('myaction_merchant_'. $this->name .'_success', array($this,'myaction_merchant_success'));
			add_action('myaction_merchant_'. $this->name .'_status' . get_hash_result_url($this->name), array($this,'myaction_merchant_status'));
		}

		function get_merchant_admin_options($options, $data){ 
			
			if(isset($options['bottom_title'])){
				unset($options['bottom_title']);
			}			
			
			$options['redeem'] = array(
				'view' => 'select',
				'title' => __('Automatic redemption','pn'),
				'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
				'default' => is_isset($data, 'redeem'),
				'name' => 'redeem',
				'work' => 'int',
			);			
			
			$text = '
			<strong>RETURN URL:</strong> <a href="'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'" target="_blank">'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'</a><br />
			<strong>SUCCESS URL:</strong> <a href="'. get_merchant_link($this->name.'_success') .'" target="_blank">'. get_merchant_link($this->name.'_success') .'</a><br />
			<strong>FAIL URL:</strong> <a href="'. get_merchant_link($this->name.'_fail') .'" target="_blank">'. get_merchant_link($this->name.'_fail') .'</a>			
			';
			if(isset($options['check'])){
				unset($options['check']);
			}
			if(isset($options['check_api'])){
				unset($options['check_api']);
			}
			if(isset($options['check_payapi'])){
				unset($options['check_payapi']);
			}
			if(isset($options['show_error'])){
				unset($options['show_error']);
			}			
			$options[] = array(
				'view' => 'textfield',
				'title' => '',
				'default' => $text,
			);	
			$options['bottom_title'] = array(
				'view' => 'h3',
				'title' => '',
				'submit' => __('Save','pn'),
				'colspan' => 2,
			);				
			
			return $options;	
		}				
		
		function merchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'PAYMER_MERCHANT_ID') 
				and is_deffin($this->m_data,'PAYMER_SECRET_KEY') 
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
			$vtype = str_replace('USD','WMZ',$vtype);
			$vtype = str_replace(array('RUR','RUB'),'WMR',$vtype);
			$vtype = str_replace('EUR','WME',$vtype);
			$vtype = str_replace('UAH','WMU',$vtype);
			
			$pay_sum = is_my_money($pay_sum,2);					
			$text_pay = get_text_pay($this->name, $item, $pay_sum);
					
			$temp = '
			<form action="https://www.paymer.com/merchant/pay/merchant.aspx?lang=ru-RU" method="post" target="_blank">
				<input name="PM_PAYMERCH_ID" type="hidden" value="'. is_deffin($this->m_data,'PAYMER_MERCHANT_ID') .'" />
				<input name="PM_PAYMENT_NO" type="hidden" value="'. $item->id .'" />
				<input name="PM_PAYMENT_AMOUNT" type="hidden" value="'. $pay_sum .'" />
				<input name="PM_PAYMENT_ATYPE" type="hidden" value="'. $vtype .'" />
				<input name="PM_PAYMENT_DESC" type="hidden" value="'. $text_pay .'"  />
				<input type="submit" value="'. __('Make a payment','pn') .'" />
			</form>													
			';				
		
			return $temp;				
		}

		function myaction_merchant_fail(){
	
			$id = get_payment_id('PM_PAYMENT_NO');
			the_merchant_bid_delete($id);
	
		}

		function myaction_merchant_success(){
	
			$id = get_payment_id('PM_PAYMENT_NO');
			the_merchant_bid_success($id);
	
		}

		function redeem_request($vtype, $trans_id){
			
			$reply = 0;
			
			$post_data = array();
			$post_data['PMS_LOGIN'] = is_deffin($this->m_data,'PAYMER_LOGIN');
			$post_data['PMS_PASSWORD'] = is_deffin($this->m_data,'PAYMER_PASSWORD');
			$post_data['PMS_TRANS_NO'] = $trans_id;
			$post_data['PMS_PURSE'] = is_deffin($this->m_data,'PAYMER_'. $vtype .'_PURSE');
			
			$c_options = array(
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $post_data,
			);
			$result = get_curl_parser('https://www.paymer.com/merchant/pay/redeem.aspx', $c_options, 'merchant', 'paymer');
			
			$err  = $result['err'];
			$out = $result['output'];
			if(!$err){
				if(strstr($out,'<pms.response>')){
					$object = @simplexml_load_string($out);
					if(is_object($object) and isset($object->error)){
						$error = intval($object->error);
						if($error < 1){
							$reply = 1;
						}
					}
				}
			} 		
				return $reply;
		}
		
		function myaction_merchant_status(){
	
			do_action('merchant_logs', $this->name);
	
			# Получение внешних данных :
			$iOrderID = isset( $_POST['PM_PAYMENT_NO'] ) ? $_POST['PM_PAYMENT_NO'] - 0 : 0;
			$iMerchantID = isset( $_POST['PM_PAYMERCH_ID'] ) ? $_POST['PM_PAYMERCH_ID'] - 0 : 0;
			$sCurrency = isset( $_POST['PM_PAYMENT_ATYPE'] ) ? $_POST['PM_PAYMENT_ATYPE'] : null;
			$dAmount = isset( $_POST['PM_PAYMENT_AMOUNT'] ) ? $_POST['PM_PAYMENT_AMOUNT'] : 0;
			$iTestMode = isset( $_POST['PM_PAYTEST_MODE'] ) ? $_POST['PM_PAYTEST_MODE'] - 0 : 0;
			$iTransNo = isset( $_POST['PM_PAYSYS_TRANS_NO'] ) ? $_POST['PM_PAYSYS_TRANS_NO'] - 0 : 0;
			$sTransDate = isset( $_POST['PM_PAYSYS_TRANS_DATE'] ) ? $_POST['PM_PAYSYS_TRANS_DATE'] : null;
			$sSignature = isset( $_POST['PM_PAYHASH'] ) ? $_POST['PM_PAYHASH'] : null;

			# Проверка № мерчанта :
			if( $iMerchantID != is_deffin($this->m_data,'PAYMER_MERCHANT_ID') ){
				die( 'bad merchant id' );
			}
			# Проверка режима оплаты :
			if( $iTestMode != 0 ){
				die( 'bad test mode' );
			}
			# Проверка контрольной подписи :
			if( $sSignature != strtoupper( md5( $iMerchantID.$dAmount.$sCurrency.$iOrderID.$iTestMode.$iTransNo.$sTransDate. is_deffin($this->m_data,'PAYMER_SECRET_KEY') ) ) ){
				die( 'bad signature' );
			}
			# ДЕЙСТВИЕ ВЫПОЛНЯЕМОЕ ПРИ ПОЛУЧЕНИИ ИНФОРМАЦИИ ОБ ОПЛАТЕ
			# $iOrderID - № заказа
			# $dAmount - сумма
			# $sCurrency - валюта
			# $iTransNo - уникальный № транзакции


			$id = $iOrderID;
			$data = get_data_merchant_for_id($id);
			$in_summ = $dAmount;
			$in_summ = is_my_money($in_summ,2);
			$err = $data['err'];
			$status = $data['status'];
			$m_id = $data['m_id'];
			$pay_purse = is_pay_purse('', $data, $m_id);
			$vtype = $data['vtype'];
			$vtype = str_replace('USD','WMZ',$vtype);
			$vtype = str_replace(array('RUR','RUB'),'WMR',$vtype);
			$vtype = str_replace('EUR','WME',$vtype);
			$vtype = str_replace('UAH','WMU',$vtype);		
			
			$naschet = is_deffin($this->m_data,'PAYMER_'. $vtype .'_PURSE');
			
			$bid_sum = is_my_money($data['pay_sum'],2);
			$bid_sum = apply_filters('merchant_bid_sum', $bid_sum, $m_id);
			
			$en_status = array('new','techpay','coldpay');
			if(in_array($status, $en_status)){  
				if($err == 0){
					if($m_id and $m_id == $this->name){
						if($vtype == $sCurrency){
							if($in_summ >= $bid_sum){		

								$params = array(
									'pay_purse' => $pay_purse,
									'sum' => $in_summ,
									'naschet' => $naschet,
									'trans_in' => $iTransNo,
								);
								the_merchant_bid_status('coldpay', $id, 'user', 0, '', $params);							
								 
								$data = get_merch_data($this->name);
								$redeem = intval(is_isset($data, 'redeem'));
								if($redeem == 1){
									$redeem_res = $this->redeem_request($vtype, $iTransNo);
									if($redeem_res == 1){
										$params = array(
											'pay_purse' => $pay_purse,
											'sum' => $in_summ,
											'naschet' => $naschet,
											'trans_in' => $iTransNo,
										);
										the_merchant_bid_status('realpay', $id, 'user', 0, '', $params);										
									}
								}
											
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

new merchant_paymer(__FILE__, 'Paymer');