<?php
/* защита от прямого обращения */
if( !defined( 'ABSPATH')){ exit(); }

/*
Подключаем фильтр к письмам, что бы все письма отображались как HTML
*/
if(!function_exists('premium_wp_mail_content_type')){
	add_filter('wp_mail_content_type', 'premium_wp_mail_content_type');
	function premium_wp_mail_content_type(){
		return "text/html";
	}
}

if(!function_exists('premium_html_wp_mail')){
	add_filter('wp_mail', 'premium_html_wp_mail');
	function premium_html_wp_mail($data){
		$data['message'] = ' 
		<html> 
			<head> 
				<title>'. $data['subject'] .'</title> 
			</head> 
			<body>
				'. $data['message'] .'
			</body> 
		</html>';
		return $data;
	}	
}

if(!function_exists('premium_default_wp_mail')){
	add_filter('wp_mail', 'premium_default_wp_mail', 100);
	function premium_default_wp_mail($data){
		$headers = trim(is_isset($data, 'headers'));
		if(!$headers){
			$data['headers'] = "From: WordPress <wordpress@mail.ru>\r\n";
		}
		return $data;
	}
}

if(!function_exists('pn_mail')){
	function pn_mail($recipient_mail, $subject, $html, $sender_name='', $sender_mail=''){
		$headers = '';
		if($sender_name and $sender_mail){
			$headers = "From: $sender_name <". $sender_mail .">\r\n";
		}		
		$recipient_mails = explode(',', $recipient_mail);
		foreach($recipient_mails as $mail){
			$mail = trim($mail);
			if(is_email($mail)){
				wp_mail($mail, $subject, $html, $headers);
			}
		}
	}
}