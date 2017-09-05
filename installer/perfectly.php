<?php
header("Content-Type: text/html; charset=UTF-8");
define( 'WP_INSTALLING', false );

$lang = '';
if(isset($_GET['lang'])){
	$lang = $_GET['lang'];
}

if($lang == 'ru'){
	$title = 'Установка завершена';
} else {
	$title = 'Installation complete';
}
?>
<!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8">
	<title><?php echo $title; ?></title>
	<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,300italic,300,400italic,500,500italic&subset=latin,cyrillic,cyrillic-ext' rel='stylesheet' type='text/css'>
	<link rel='stylesheet' href='style.css' type='text/css' media='all' />
	<script src="js/jquery-1.8.3.min.js" type="text/javascript"></script>
	<script src="js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
	<script src="js/jquery.form.js" type="text/javascript"></script>
	<script src="js/jcook.js" type="text/javascript"></script>
	<script src="js/config.js" type="text/javascript"></script>
	
</head>
<body>
<div id="container">

	<div class="header">
		<?php echo $title; ?>
	</div>
	
	<div class="content">
		
		<div class="perfectly"></div>
		<div class="perfectly_text">
			<?php
			if($lang == 'ru'){
			?>
				<p>Установка и настройка завершена!</p>
				<p>Удалите installer</p>
			<?php } else { ?>
				<p>Installation and setup is complete!</p>
				<p>Remove the installer</p>
			<?php } ?>
		</div>
	</div>
</div>
</body>
</html>