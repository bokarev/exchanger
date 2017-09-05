<?php
/*
title: [ru_RU:]SMSC[:ru_RU][en_US:]SMSC[:en_US]
description: [ru_RU:]SMSC[:ru_RU][en_US:]SMSC[:en_US]
version: 1.0
*/

if(!class_exists('smsgate_smsc')){
	class smsgate_smsc extends SmsGate_Premiumbox{

		function __construct($file, $title)
		{
			
			$map = array(
				'SMSC_NAME', 'SMSC_PASS', 'SMSC_SENDER', 'SMSC_TEL',
			);
			parent::__construct($file, $map, $title);
			
			add_filter('smsgate_settingtext_'.$this->name, array($this, 'smsgate_settingtext'));
			add_action('pn_send_sms', array($this, 'send_sms'), 10, 2);
		}	

		function smsgate_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'SMSC_NAME') 
				and is_deffin($this->m_data,'SMSC_PASS') 
				and is_deffin($this->m_data,'SMSC_SENDER') 
				and is_deffin($this->m_data,'SMSC_TEL')
			){
				$text = '';
			}
			
			return $text;
		}
		
		function send_sms($text, $phone=''){
			if(is_enable_smsgate($this->name)){
				if(is_deffin($this->m_data,'SMSC_NAME') and is_deffin($this->m_data,'SMSC_PASS') and is_deffin($this->m_data,'SMSC_SENDER')){
					$text = trim($text);
					$text = iconv('UTF-8','CP1251',$text);
					
					$phone = trim($phone);
					if(!$phone){ $phone = is_deffin($this->m_data,'SMSC_TEL'); }
					$tels = explode(',', $phone);
					foreach($tels as $tel){
						$tel = trim($tel);
						if($tel){
						
							$c_options = array(
								CURLOPT_FOLLOWLOCATION => false,
								CURLOPT_POST => true,
								CURLOPT_HEADER => false,
								CURLOPT_CONNECTTIMEOUT => 15,
								CURLOPT_POSTFIELDS => array(
									'login'=> is_deffin($this->m_data,'SMSC_NAME'),
									'psw'=> is_deffin($this->m_data,'SMSC_PASS'),
									'sender'=> is_deffin($this->m_data,'SMSC_SENDER'),
									'phones'=> $tel,
									'mes'=>$text
								),   
							);
							$result = get_curl_parser('http://smsc.ru/sys/send.php', $c_options, 'smsgate', 'smsc'); 
			 
						}
					}
					
				}
			}
		}		
	}
}

new smsgate_smsc(__FILE__, 'SMSC');