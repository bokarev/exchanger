<?php
if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
	header('Allow: POST');
	header('HTTP/1.1 405 Method Not Allowed');
	header('Content-Type: text/plain');
	exit;
}

$email = '';
if(isset($_POST['email'])){
	if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL, FILTER_FLAG_SCHEME_REQUIRED)){
		$email = $_POST['email'];
	}
}

$lang = '';
if(isset($_POST['lang'])){ $lang = $_POST['lang']; }
if($lang != 'en'){ $lang = 'ru'; }

if(!$email){
	$log['otv'] = 'error';
	if($lang == 'ru'){
		$log['text'] = 'Ошибка! Вы не ввели e-mail!';
	} else {
		$log['text'] = 'Error! You have not entered an e-mail!';
	}
	echo json_encode($log);
	exit;
}

$gomail = mail($email, "Test mail", "Test content in mail");
if($gomail){
	$log['otv'] = 'success';
	if($lang == 'ru'){
		$log['text'] = 'Письмо успешно отправлено! Проверьте почтовый ящик!';
	} else {
		$log['text'] = 'A letter has been sent successfully! Check the mailbox!';
	}
	
} else {
	$log['otv'] = 'error';
	if($lang == 'ru'){
		$log['text'] = 'Ошибка! Сервер не смог отправить письмо!';
	} else {
		$log['text'] = 'Error! The server was unable to send the letter!';
	}
	
}

$log['exit'] = 'yes';
echo json_encode($log);

?>