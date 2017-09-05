<?php
/*
title: [ru_RU:]Payeer[:ru_RU][en_US:]Payeer[:en_US]
description: [ru_RU:]мерчант Payeer[:ru_RU][en_US:]Payeer merchant[:en_US]
version: 1.2
*/

if(!class_exists('merchant_payeer')){
	class merchant_payeer extends Merchant_Premiumbox{

		function __construct($file, $title)
		{
			$map = array(
				'PAYEER_SEKRET_KEY', 'PAYEER_SHOP_ID', 
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
			
			if(isset($options['check_api'])){
				unset($options['check_api']);
			}
			if(isset($options['check_payapi'])){
				unset($options['check_payapi']);
			}			
			
			$text = '
			<strong>RETURN URL:</strong> <a href="'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'" target="_blank">'. get_merchant_link($this->name.'_status' . get_hash_result_url($this->name)) .'</a><br />
			<strong>SUCCESS URL:</strong> <a href="'. get_merchant_link($this->name.'_success') .'" target="_blank">'. get_merchant_link($this->name.'_success') .'</a><br />
			<strong>FAIL URL:</strong> <a href="'. get_merchant_link($this->name.'_fail') .'" target="_blank">'. get_merchant_link($this->name.'_fail') .'</a>				
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
				is_deffin($this->m_data,'PAYEER_SHOP_ID') 
				and is_deffin($this->m_data,'PAYEER_SEKRET_KEY') 
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
					
			$m_desc = base64_encode($text_pay);
			$m_amount = number_format($pay_sum, 2, '.', '');
			$arHash = array(
				is_deffin($this->m_data,'PAYEER_SHOP_ID'),
				$item->id,
				$m_amount,
				$vtype,
				$m_desc,
				is_deffin($this->m_data,'PAYEER_SEKRET_KEY')
			);
			$sign = strtoupper(hash('sha256', implode(":", $arHash)));
					
			$temp = '
			<form method="GET" action="//payeer.com/api/merchant/m.php" target="_blank">
				<input type="hidden" name="m_shop" value="'. is_deffin($this->m_data,'PAYEER_SHOP_ID') .'">
				<input type="hidden" name="m_orderid" value="'. $item->id .'">
				<input type="hidden" name="m_amount" value="'. $pay_sum .'">
				<input type="hidden" name="m_curr" value="'. $vtype .'">
				<input type="hidden" name="m_desc" value="'. $m_desc .'">
				<input type="hidden" name="m_sign" value="'. $sign .'">
				<input type="submit" value="'. __('Make a payment','pn') .'" />
			</form>												
			';				
		
			return $temp;		
		}

		function myaction_merchant_fail(){
	
			$id = get_payment_id('m_orderid');
			the_merchant_bid_delete($id);
	
		}

		function myaction_merchant_success(){
	
			$id = get_payment_id('m_orderid');
			the_merchant_bid_success($id);
	
		}

		function myaction_merchant_status(){
	
			do_action('merchant_logs', $this->name);
	
			if (isset($_POST["m_operation_id"]) && isset($_POST["m_sign"])){

				$m_key = is_deffin($this->m_data,'PAYEER_SEKRET_KEY');
				$arHash = array($_POST['m_operation_id'],
						$_POST['m_operation_ps'],
						$_POST['m_operation_date'],
						$_POST['m_operation_pay_date'],
						$_POST['m_shop'],
						$_POST['m_orderid'],
						$_POST['m_amount'],
						$_POST['m_curr'],
						$_POST['m_desc'],
						$_POST['m_status'],
						$m_key);
						
				$sign_hash = strtoupper(hash('sha256', implode(":", $arHash)));
				if ($_POST["m_sign"] == $sign_hash && $_POST['m_status'] == "success"){			

					$id = $_POST['m_orderid'];
					$data = get_data_merchant_for_id($id);
					$in_summ = $_POST['m_amount'];
					$in_summ = is_my_money($in_summ,2);
					
					$err = $data['err'];
					$status = $data['status'];
					$m_id = $data['m_id'];
					$pay_purse = is_pay_purse($_POST['client_account'], $data, $m_id);
					$vtype = $data['vtype'];
					
					$bid_sum = is_my_money($data['pay_sum'],2);
					$bid_sum = apply_filters('merchant_bid_sum', $bid_sum, $m_id);
					
					if($status == 'new'){ 
						if($err == 0){
							if($m_id and $m_id == $this->name){
								if($vtype == $_POST['m_curr']){
									if($in_summ >= $bid_sum){	
							
										$params = array(
											'pay_purse' => $pay_purse,
											'sum' => $in_summ,
											'naschet' => is_deffin($this->m_data,'PAYEER_SHOP_ID'),
											'trans_in' => $_POST['m_operation_id'],
										);
										the_merchant_bid_status('realpay', $id, 'user', 0, '', $params);							
										 
										echo $_POST['m_orderid']."|success";
										exit;
										
									} 
								} 
							} 
						} 
					} 
				
				}
				
				echo $_POST['m_orderid']."|error";
				exit;
			}				
		}
		
	}
}

new merchant_payeer(__FILE__, 'Payeer');