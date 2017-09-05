<?php
/*
title: [ru_RU:]Webmoney[:ru_RU][en_US:]Webmoney[:en_US]
description: [ru_RU:]мерчант webmoney[:ru_RU][en_US:]webmoney merchant[:en_US]
version: 1.2
*/

if(!class_exists('merchant_webmoney')){
	class merchant_webmoney extends Merchant_Premiumbox{
		
		function __construct($file, $title)
		{
			$map = array(
				'WEBMONEY_WMZ_PURSE', 'WEBMONEY_WMZ_KEY', 
				'WEBMONEY_WMR_PURSE', 'WEBMONEY_WMR_KEY',
				'WEBMONEY_WME_PURSE', 'WEBMONEY_WME_KEY', 
				'WEBMONEY_WMU_PURSE', 'WEBMONEY_WMU_KEY',
				'WEBMONEY_WMB_PURSE', 'WEBMONEY_WMB_KEY', 
				'WEBMONEY_WMY_PURSE', 'WEBMONEY_WMY_KEY',
				'WEBMONEY_WMG_PURSE', 'WEBMONEY_WMG_KEY', 
				'WEBMONEY_WMX_PURSE', 'WEBMONEY_WMX_KEY',
				'WEBMONEY_WMK_PURSE', 'WEBMONEY_WMK_KEY',
			);
			parent::__construct($file, $map, $title);
			
			add_action('get_merchant_admin_options_'. $this->name, array($this, 'get_merchant_admin_options'), 10, 2);
			add_filter('merchants_settingtext_'.$this->name, array($this, 'merchants_settingtext'));
			add_filter('merchant_formstep_autocheck',array($this, 'merchant_formstep_autocheck'),1,2);
			add_filter('merchants_action_bid_'.$this->name, array($this,'merchants_action_bid'),99,4);
			add_action('myaction_merchant_'. $this->name .'_fail', array($this,'myaction_merchant_fail'));
			add_action('myaction_merchant_'. $this->name .'_success', array($this,'myaction_merchant_success'));
			add_action('myaction_merchant_'. $this->name .'_status', array($this,'myaction_merchant_status'));
		}

		function get_merchant_admin_options($options, $data){ 
			
			$text = '
			<strong>Result URL:</strong> <a href="'. get_merchant_link($this->name.'_status') .'" target="_blank">'. get_merchant_link($this->name.'_status') .'</a><br />
			<strong>Success URL:</strong> <a href="'. get_merchant_link($this->name.'_success') .'" target="_blank">'. get_merchant_link($this->name.'_success') .'</a><br />
			<strong>Fail URL:</strong> <a href="'. get_merchant_link($this->name.'_fail') .'" target="_blank">'. get_merchant_link($this->name.'_fail') .'</a>			
			';

			$options['text'] = array(
				'view' => 'textfield',
				'title' => '',
				'default' => $text,
			);

			if(isset($options['resulturl'])){
				unset($options['resulturl']);
			}
			if(isset($options['check_api'])){
				unset($options['check_api']);
			}
			if(isset($options['check_payapi'])){
				unset($options['check_payapi']);
			}			
			
			return $options;	
		}		
		
		function merchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'WEBMONEY_WMZ_PURSE') and is_deffin($this->m_data,'WEBMONEY_WMZ_KEY') 
				or is_deffin($this->m_data,'WEBMONEY_WMR_PURSE') and is_deffin($this->m_data,'WEBMONEY_WMR_KEY') 
				or is_deffin($this->m_data,'WEBMONEY_WME_PURSE') and is_deffin($this->m_data,'WEBMONEY_WME_KEY') 
				or is_deffin($this->m_data,'WEBMONEY_WMU_PURSE') and is_deffin($this->m_data,'WEBMONEY_WMU_KEY') 
				or is_deffin($this->m_data,'WEBMONEY_WMB_PURSE') and is_deffin($this->m_data,'WEBMONEY_WMB_KEY') 
				or is_deffin($this->m_data,'WEBMONEY_WMY_PURSE') and is_deffin($this->m_data,'WEBMONEY_WMY_KEY') 
				or is_deffin($this->m_data,'WEBMONEY_WMG_PURSE') and is_deffin($this->m_data,'WEBMONEY_WMG_KEY')
				or is_deffin($this->m_data,'WEBMONEY_WMX_PURSE') and is_deffin($this->m_data,'WEBMONEY_WMX_KEY') 
				or is_deffin($this->m_data,'WEBMONEY_WMK_PURSE') and is_deffin($this->m_data,'WEBMONEY_WMK_KEY')
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
			$vtype = str_replace(array('WMZ'),'USD',$vtype);
			$vtype = str_replace(array('RUR','WMR'),'RUB',$vtype);
			$vtype = str_replace(array('WME'),'EUR',$vtype);
			$vtype = str_replace(array('WMU'),'UAH',$vtype);
			$vtype = str_replace(array('WMB'),'BYR',$vtype);
			$vtype = str_replace(array('WMY'),'UZS',$vtype);
			$vtype = str_replace(array('WMG'),'GLD',$vtype);
			$vtype = str_replace(array('WMX'),'BTC',$vtype);
			$vtype = str_replace(array('WMK'),'KZT',$vtype);
					
				$LMI_PAYEE_PURSE = 0;
					
					if($vtype == 'USD'){
						$LMI_PAYEE_PURSE = is_deffin($this->m_data,'WEBMONEY_WMZ_PURSE');
					} elseif($vtype == 'RUB'){
						$LMI_PAYEE_PURSE = is_deffin($this->m_data,'WEBMONEY_WMR_PURSE');
					} elseif($vtype == 'EUR'){
						$LMI_PAYEE_PURSE = is_deffin($this->m_data,'WEBMONEY_WME_PURSE');
					} elseif($vtype == 'UAH'){
						$LMI_PAYEE_PURSE = is_deffin($this->m_data,'WEBMONEY_WMU_PURSE');
					} elseif($vtype == 'BYR'){
						$LMI_PAYEE_PURSE = is_deffin($this->m_data,'WEBMONEY_WMB_PURSE');
					} elseif($vtype == 'UZS'){
						$LMI_PAYEE_PURSE = is_deffin($this->m_data,'WEBMONEY_WMY_PURSE');	
					} elseif($vtype == 'GLD'){
						$LMI_PAYEE_PURSE = is_deffin($this->m_data,'WEBMONEY_WMG_PURSE');
					} elseif($vtype == 'BTC'){
						$LMI_PAYEE_PURSE = is_deffin($this->m_data,'WEBMONEY_WMX_PURSE');
					} elseif($vtype == 'KZT'){
						$LMI_PAYEE_PURSE = is_deffin($this->m_data,'WEBMONEY_WMK_PURSE');			
					}		


					$pay_sum = is_my_money($pay_sum,2);		
					$text_pay = get_text_pay($this->name, $item, $pay_sum);
						
					$temp = '
					<form name="MerchantPay" action="https://merchant.webmoney.ru/lmi/payment.asp" method="post" accept-charset="windows-1251">
						<input type="hidden" name="LMI_RESULT_URL" value="'. get_merchant_link($this->name.'_status') .'" />
						<input type="hidden" name="LMI_SUCCESS_URL" value="'. get_merchant_link($this->name.'_success') .'" />
						<input type="hidden" name="LMI_SUCCESS_METHOD" value="POST" />
						<input type="hidden" name="LMI_FAIL_URL" value="'. get_merchant_link($this->name.'_fail') .'" />
						<input type="hidden" name="LMI_FAIL_METHOD" value="POST" />			    
						<input name="LMI_PAYMENT_NO" type="hidden" value="'. $item->id .'" />
						<input name="LMI_PAYMENT_AMOUNT" type="hidden" value="'. $pay_sum .'" />
						<input name="LMI_PAYEE_PURSE" type="hidden" value="'. $LMI_PAYEE_PURSE .'" />
						<input name="LMI_PAYMENT_DESC" type="hidden" value="'. $text_pay .'" />
						<input name="sEmail" type="hidden" value="'. is_email($item->user_email) .'" />				

						<input type="submit" value="Pay" />
					</form>			
					';				
			
			return $temp;
		}

		function myaction_merchant_fail(){
	
			$id = get_payment_id('LMI_PAYMENT_NO');
			the_merchant_bid_delete($id);
	
		}

		function myaction_merchant_success(){
	
			$id = get_payment_id('LMI_PAYMENT_NO');
			the_merchant_bid_success($id);
	
		}

		function myaction_merchant_status(){
	
			do_action('merchant_logs', $this->name);
	
			$dPaymentAmount = trim(is_param_post('LMI_PAYMENT_AMOUNT'));
			$iPaymentID = trim(is_param_post('LMI_PAYMENT_NO'));
			$bPaymentMode = trim(is_param_post('LMI_MODE'));
			$iPayerWMID = trim(is_param_post('LMI_PAYER_WM'));
			$sPayerPurse = trim(is_param_post('LMI_PAYER_PURSE'));
			$sEmail = trim(is_param_post('sEmail'));

			if( $bPaymentMode != 0 ) {
				die( 'Payments are not permitted in test mode' );
			}

			if( isset( $_POST['LMI_PREREQUEST'] ) ){
				die( 'YES' );
			}

			$iSysInvsID = trim(is_param_post('LMI_SYS_INVS_NO'));
			$iSysTransID = trim(is_param_post('LMI_SYS_TRANS_NO'));
			$sSignature = trim(is_param_post('LMI_HASH'));
			$sSysTransDate = trim(is_param_post('LMI_SYS_TRANS_DATE'));

			if(!$sPayerPurse){
				die('Purse empty');
			}
	
			$constant = is_deffin($this->m_data,'WEBMONEY_WM'. substr( $sPayerPurse, 0, 1 ) .'_PURSE');
			$constant2 = is_deffin($this->m_data,'WEBMONEY_WM'. substr( $sPayerPurse, 0, 1 ) .'_KEY');
	
			if( $sSignature != strtoupper( hash( 'sha256', implode(  '', array( $constant, $dPaymentAmount, $iPaymentID, $bPaymentMode, $iSysInvsID, $iSysTransID, $sSysTransDate, $constant2, $sPayerPurse, $iPayerWMID ) ) ) ) ) {
				die( 'Invalid control signature' );
			}

			# $iPaymentID - номер заказа
			# $dPaymentAmount - сумма платежа
			# $iPayerWMID - WMID плательщика
			# $sPayerPurse - кошелек плательщика
			# $sEmail - E-mail адрес плательщика
			# $iSysInvsID - уникальный номер счета
			# $iSysTransID - уникальный номер транзакции
	
			$id = $iPaymentID;
			$data = get_data_merchant_for_id($id);
			
			$in_summ = $dPaymentAmount;	
			$in_summ = is_my_money($in_summ,2);
			$err = $data['err'];
			$status = $data['status'];
			$m_id = $data['m_id'];
			$pay_purse = is_pay_purse($sPayerPurse, $data, $m_id);
			
			$vtype = $data['vtype'];
			$vtype = str_replace(array('WMZ','USD'),'Z',$vtype);
			$vtype = str_replace(array('RUR','WMR','RUB'),'R',$vtype);
			$vtype = str_replace(array('WME','EUR'),'E',$vtype);
			$vtype = str_replace(array('WMU','UAH'),'U',$vtype);
			$vtype = str_replace(array('WMB','BYR'),'B',$vtype);
			$vtype = str_replace(array('WMY','UZS'),'Y',$vtype);
			$vtype = str_replace(array('WMG','GLD'),'G',$vtype);
			$vtype = str_replace(array('WMX','BTC'),'X',$vtype);
			$vtype = str_replace(array('WMK','KZT'),'K',$vtype);	
	
			$bid_sum = is_my_money($data['pay_sum'],2);
			$bid_sum = apply_filters('merchant_bid_sum', $bid_sum, $m_id);
	
			$fl = substr($sPayerPurse, 0, 1 );
	
			if($status == 'new'){ 
				if($err == 0){
					if($m_id and $m_id == $this->name){
						if($vtype == $fl){
							if($in_summ >= $bid_sum){		
					
								$params = array(
									'pay_purse' => $pay_purse,
									'sum' => $in_summ,
									'naschet' => $constant,
									'trans_in' => $iSysTransID,
								);
								the_merchant_bid_status('realpay', $id, 'user', 0, '', $params);					
											 
								die('Completed');
								
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

new merchant_webmoney(__FILE__, 'Webmoney');