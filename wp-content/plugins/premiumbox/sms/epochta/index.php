<?php
/*
title: [ru_RU:]E-pochta[:ru_RU][en_US:]E-pochta[:en_US]
description: [ru_RU:]E-pochta[:ru_RU][en_US:]E-pochta[:en_US]
version: 1.0
*/

if(!class_exists('smsgate_epochta')){
	class smsgate_epochta extends SmsGate_Premiumbox{
		
		function __construct($file, $title)
		{
			
			$map = array(
				'EPOCHTA_NAME', 'EPOCHTA_PASS', 'EPOCHTA_SENDER', 'EPOCHTA_TEL',
			);
			parent::__construct($file, $map, $title);
			
			add_filter('smsgate_settingtext_'.$this->name, array($this, 'smsgate_settingtext'));
			add_action('pn_send_sms', array($this, 'send_sms'), 10, 2);
		}	

		function smsgate_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'EPOCHTA_NAME') 
				and is_deffin($this->m_data,'EPOCHTA_PASS') 
				and is_deffin($this->m_data,'EPOCHTA_SENDER') 
				and is_deffin($this->m_data,'EPOCHTA_TEL')
			){
				$text = '';
			}
			
			return $text;
		}
		
		function send_sms($text, $phone=''){
			if(is_enable_smsgate($this->name)){
				if(is_deffin($this->m_data,'EPOCHTA_NAME') and is_deffin($this->m_data,'EPOCHTA_PASS') and is_deffin($this->m_data,'EPOCHTA_SENDER')){
					$text = trim($text);
					$phone = trim($phone);
					if(!$phone){ $phone = is_deffin($this->m_data,'EPOCHTA_TEL'); }
					$tels = explode(',', $phone);
						foreach($tels as $tel){
							$tel = trim($tel);
							if($tel){
								
$src = '<?xml version="1.0" encoding="UTF-8"?>    
<SMS> 
<operations>  
<operation>SEND</operation> 
</operations> 
<authentification>    
<username>'. is_deffin($this->m_data,'EPOCHTA_NAME') .'</username>   
<password>'. is_deffin($this->m_data,'EPOCHTA_PASS') .'</password>   
</authentification>   
<message> 
<sender>'. is_deffin($this->m_data,'EPOCHTA_SENDER') .'</sender>    
<text>'. $text .'</text>   
</message>    
<numbers> 
<number>'. $tel .'</number>   
</numbers>    
</SMS>'; 								
								
								$c_options = array(
									CURLOPT_POST => true,  
									CURLOPT_HEADER => false,
									CURLOPT_CONNECTTIMEOUT => 15,
									CURLOPT_POSTFIELDS => array('XML'=>$src), 
								);			
								$result = get_curl_parser('http://atompark.com/members/sms/xml.php', $c_options, 'smsgate', 'epochta');  
 
							}
						}
				}	
			}
		}		
	}
}

new smsgate_epochta(__FILE__, 'E-pochta');