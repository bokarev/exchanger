<?php
header("Content-Type: text/html; charset=UTF-8");
define( 'WP_INSTALLING', false );
define('INSTALLER_PATH',dirname(__FILE__) . '/');
define('DIR_PATH', str_replace('installer/','',INSTALLER_PATH));

$php_version = '5.6'; //тут нужно указать версию php
$ioncube_version = '5.0'; //тут нужно указать версию куба
$old_prefix = 'pr_'; //тут пишем старый префикс БД
$old_url = 'http://premiumexchanger.ru'; //старый url

$plugin_vid = array('exchangebox','lendbox','premiumbox');
$plugin_installer = 'premiumbox'; //тут название дистрибутива

include_once(INSTALLER_PATH.'includes/function.php');

$max_step = 9;
if(in_array($plugin_installer, $plugin_vid)){
	$max_step = 10;
}

$step = get_step();
$lang = get_lang();

$error = array();

$db_name_v = $db_user_v = $db_pass_v = '';
$c_url_v = $c_email_v = $c_login_v = $c_pass_v = '';
$db_host_v = 'localhost';

/* обработка */
if(isset($_POST['submit'])){
	if($step == 6){
		
		$db_name_v = str_replace("'","",is_param_post_i('db_name'));
		$db_user_v = str_replace("'","",is_param_post_i('db_user'));	
		$db_pass_v = str_replace("'","",is_param_post_i('db_pass'));	
		$db_host_v = str_replace("'","",is_param_post_i('db_host'));
		if(!$db_host_v){ $db_host_v = 'localhost'; }
		
		$dbh = @mysqli_init();
		if($connect = @mysqli_real_connect($dbh, $db_host_v,$db_user_v,$db_pass_v)) {
			if (@mysqli_select_db($dbh, $db_name_v)) {
				$file = file_get_contents(DIR_PATH .'wp-config.php');
				if($file){
					
					$file = preg_replace("/(define\('DB_NAME', ')(.*?)('\);)/u", '${1}'.str_replace('\\','\\\\',$db_name_v).'${3}', $file);
					$file = preg_replace("/(define\('DB_USER', ')(.*?)('\);)/u", '${1}'.str_replace('\\','\\\\',$db_user_v).'${3}', $file);
					$file = preg_replace("/(define\('DB_PASSWORD', ')(.*?)('\);)/u", '${1}'.str_replace('\\','\\\\',$db_pass_v).'${3}', $file);
					$file = preg_replace("/(define\('DB_HOST', ')(.*?)('\);)/u", '${1}'.str_replace('\\','\\\\',$db_host_v).'${3}', $file);				

					if (file_put_contents(DIR_PATH .'wp-config.php',$file)){
						
						header('Location: index.php?step=7&lang='.$lang);
						exit;
						
					} else {
						if($lang == 'ru'){
							$error[] = 'Не удалось перезаписать файл wp-config.php. Установите временно права 777 на файл wp-config.php, который находится в корне вашего сайта.';
						} else {
							$error[] = 'Unable to overwrite the file wp-config.php. Install temporarily the right to file 777 wp-config.php, which is at the root of your site.';
						}						
					}
					
				} else {
					if($lang == 'ru'){
						$error[] = 'Не удалось открыть файл wp-config.php. Установите временно права 777 на файл wp-config.php, который находится в корне вашего сайта.';
					} else {
						$error[] = 'Unable to open the file wp-config.php. Install temporarily the right to file 777 wp-config.php, which is at the root of your site.';
					}				
				}
			} else {
				if($lang == 'ru'){
					$error[] = 'Неверные данные';
				} else {
					$error[] = 'Wrong data';
				}				
			}
		} else {	
			if($lang == 'ru'){
				$error[] = 'Неверные данные';
			} else {
				$error[] = 'Wrong data';
			}
		}
	}
	if($step == 7){
		$c_url_v = rtrim(strip_tags(htmlspecialchars(is_param_post_i('c_url'))),'/');
		if(!$c_url_v){
			if($lang == 'ru'){
				$error[] = 'указаны не все данные';
			} else {
				$error[] = 'It does not include all details';
			}			
		}		
		
		if(count($error) == 0){
			$max_size = wp_max_upload_size_i();
			$countfile = count($_FILES['dump_db']['name']);
			if($countfile > 0){
				$ext = strtolower(strrchr($_FILES['dump_db']['name'],"."));
				$tempFile = $_FILES['dump_db']['tmp_name'];
				$fileupform = array('.sql');
				if(in_array($ext, $fileupform)){
					if($_FILES["dump_db"]["size"] > 0 and $_FILES["dump_db"]["size"] < $max_size){
						
						$dir = DIR_PATH .'/wp-content/uploads/';
						if(!is_dir($dir)){ 
							@mkdir($dir , 0777);
						}			
						if(is_dir($dir)){
							$uploadfile = $dir . 'bd.sql';
							if (move_uploaded_file($tempFile, $uploadfile)){
								
								$prefix = strtolower(wp_generate_password_i(4,false,false)).'_';

								$file = file_get_contents(DIR_PATH .'wp-config.php');
								if($file){
									
									$file = preg_replace("/(table_prefix  = ')(.*?)(';)/u", '${1}'. $prefix .'${3}', $file);			
									if (file_put_contents(DIR_PATH .'wp-config.php',$file)){
							
										$DB_USER = $DB_PASSWORD = $DB_NAME = $DB_HOST = '';
							
										$file = file_get_contents(DIR_PATH .'wp-config.php');
										if(preg_match_all("/(define\('(.*?)', ')(.*?)('\);)/s",$file, $match, PREG_PATTERN_ORDER)){
											foreach($match[2] as $k => $v){
												$v = trim($v);
												if($v == 'DB_NAME'){
													$DB_NAME = $match[3][$k];
												} elseif($v == 'DB_USER'){
													$DB_USER = $match[3][$k];
												} elseif($v == 'DB_PASSWORD'){
													$DB_PASSWORD = $match[3][$k];
												} elseif($v == 'DB_HOST'){
													$DB_HOST = $match[3][$k];
												}
											}
											
											$dbh = @mysqli_init();
											
											if($connect = @mysqli_real_connect($dbh,$DB_HOST,$DB_USER,$DB_PASSWORD)) {
												if (@mysqli_select_db($dbh, $DB_NAME)) {
													
													$del_old = is_param_post_i('del_old');
													
													if($del_old == 1){
														
														$result = mysqli_query($dbh, "SHOW TABLES");
														while ( $row = mysqli_fetch_array($result)){
															if(isset($row[0])){
																
																$old_db = trim((string)$row[0]);
																$res = mysqli_query($dbh, "DROP TABLE $old_db");
																
															}
														}													
													}
													
													$file_sql = file($uploadfile);        
													foreach ($file_sql as $n => $l){
														if (substr($l,0,2)=='--'){ 
															unset($file_sql[$n]);
														}
													}
													$file_sql = explode(";\n",implode("\n",$file_sql));
													unset($file_sql[count($file_sql)-1]);
													foreach ($file_sql as $q) {
														$q = trim($q);
														if($q){
															
															$q = str_replace($old_prefix, $prefix, $q);
															$res = mysqli_query($dbh, $q);
															
														}
													}		
													
													$res = mysqli_query($dbh, "UPDATE ". $prefix ."options SET option_value = '{$c_url_v}' WHERE option_name = 'home' OR option_name = 'siteurl';");
													$res = mysqli_query($dbh, "UPDATE ". $prefix ."posts SET guid = replace(guid, '{$old_url}','{$c_url_v}');");
													$res = mysqli_query($dbh, "UPDATE ". $prefix ."posts SET post_content = replace(post_content, '{$old_url}', '{$c_url_v}');");
													
													header('Location: index.php?step=8&lang='.$lang);
													exit;
													
												} else {
													if($lang == 'ru'){
														$error[] = 'Неверные данные БД';
													} else {
														$error[] = 'Wrong data DB';
													}				
												}
											} else {	
												if($lang == 'ru'){
													$error[] = 'Неверные данные БД';
												} else {
													$error[] = 'Wrong data DB';
												}
											}
										
										} else {
											if($lang == 'ru'){
												$error[] = 'Не удалось открыть файл wp-config.php. Установите временно права 777 на файл wp-config.php, который находится в корне вашего сайта.';
											} else {
												$error[] = 'Unable to open the file wp-config.php. Install temporarily the right to file 777 wp-config.php, which is at the root of your site.';
											}				
										}						
							
									} else {
										if($lang == 'ru'){
											$error[] = 'Не удалось перезаписать файл wp-config.php. Установите временно права 777 на файл wp-config.php, который находится в корне вашего сайта.';
										} else {
											$error[] = 'Unable to overwrite the file wp-config.php. Install temporarily the right to file 777 wp-config.php, which is at the root of your site.';
										}						
									}	
									
								} else {
									if($lang == 'ru'){
										$error[] = 'Не удалось открыть файл wp-config.php. Установите временно права 777 на файл wp-config.php, который находится в корне вашего сайта.';
									} else {
										$error[] = 'Unable to open the file wp-config.php. Install temporarily the right to file 777 wp-config.php, which is at the root of your site.';
									}				
								}
						
							} else {
								if($lang == 'ru'){
									$error[] = 'Не удалось загрузить файл базы данных. Установите временно права 777 на директорию /wp-content/uploads/.';
								} else {
									$error[] = 'Could not load file database. Install the provisional rules 777 directory /wp-content/uploads/.';
								}							
							}
						} else {
							if($lang == 'ru'){
								$error[] = 'Необходимо создать папку wp-content/uploads/ и установить на нее права 777.';
							} else {
								$error[] = 'You must create the folder wp-content / uploads / and install it right 777.';
							}					
						}
					} else {
						if($lang == 'ru'){
							$error[] = 'неверный размер файла';
						} else {
							$error[] = 'Invalid file size';
						}					
					}
				} else {
					if($lang == 'ru'){
						$error[] = 'неверный формат файла';
					} else {
						$error[] = 'Incorrect file format';
					}				
				}
			} else {	
				if($lang == 'ru'){
					$error[] = 'вы не выбрали файл';
				} else {
					$error[] = 'you choose the file';
				}		
			}
		}
	}
	if($step == 8){
		
		require_once DIR_PATH .'wp-config.php';
		$c_email_v = is_email(esc_html(is_param_post_i('c_email')));	
		$c_login_v = is_user(is_param_post_i('c_login'));	
		$c_pass_v = is_password(is_param_post_i('c_pass'));	

		if(!$c_email_v or !$c_login_v or !$c_pass_v){
			if($lang == 'ru'){
				$error[] = 'указаны не все данные';
			} else {
				$error[] = 'It does not include all details';
			}			
		}
		
		if(count($error) == 0){
		
			global $wpdb;
			$admin_new_password_hash = wp_hash_password($c_pass_v);
			$wpdb->query("UPDATE ". $wpdb->prefix ."options SET option_value = '{$c_email_v}' WHERE option_name = 'admin_email';");

			$users = get_users('role=administrator&orderby=ID&order=ASC');
			foreach($users as $user){
				$user_id = $user->ID;
				
				$wpdb->query("UPDATE ". $wpdb->prefix ."users SET user_email  = '{$c_email_v}', user_login  = '{$c_login_v}', user_nicename  = '{$c_login_v}', user_pass  = '{$admin_new_password_hash}' WHERE ID='{$user_id}';");

				break;
			}
			
			if(in_array($plugin_installer, $plugin_vid)){
				header('Location: index.php?step=9&lang='.$lang);
				exit;
			} else {
				header('Location: perfectly.php?lang='.$lang);
				exit;			
			}
		
		}
		
	}
	if($step == 9){
		require_once DIR_PATH .'wp-config.php';
		$lan = array();
		$lan['admin_lang'] = $admin_lang = esc_html(is_param_post_i('admin_lang'));
		$lan['site_lang'] = $site_lang = esc_html(is_param_post_i('site_lang'));
		$lan['multilingual'] = $multilingual = intval(is_param_post_i('multilingual'));
		$lan['multisite_lang'] = array('ru_RU','en_US');
		update_option('pn_lang',$lan);
		
			header('Location: perfectly.php?lang='.$lang);
			exit;		
	}
}
$error_text = '';
if(count($error) > 0){
	$error_text = '<div class="error">'. join('<br />',$error) .'</div>';
}
/* end обработка */	
?>
<!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8">
	<title><?php installer_title(); ?></title>
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
		<?php installer_title(); ?>
	</div>
	
	<?php echo $error_text; ?>
	
	<div class="content">
		<?php if($step == 1){ ?>
		
			<a href="?step=2&lang=ru" class="lang_link">Русский язык</a>
			<a href="?step=2&lang=en" class="lang_link">English</a>
		
		<?php } elseif($step == 2){ ?>
		
			<table>
				<?php
				$check_arr = array(
					'1' => array(
						'title_ru' => 'Версия PHP',
						'title_en' => 'PHP version',
					),
					'2' => array(
						'title_ru' => 'Версия ionCube',
						'title_en' => 'ionCube version',
					),
					'3' => array(
						'title_ru' => 'MySql',
						'title_en' => 'MySql',
					),					
				);
				?>
				<?php foreach($check_arr as $key => $val){ 
					if($lang == 'ru'){ 
						$title = $val['title_ru'];
					} else {
						$title = $val['title_en'];
					}
				?>
				<tr>
					<th><?php echo $title; ?></th>
					<td>
					<?php 
					if($key == 1){ 
						$required_php_version = floatval($php_version);
						$php_version = substr(phpversion(),0,3);
						if($required_php_version){ 
							if ($php_version >= $required_php_version ) {
								echo $php_version;
							} else { 
								if($lang == 'ru'){ 
									echo 'Для работы, скрипту необходима версия <strong>'. $required_php_version .'</strong>, у вас установлена <span class="bred">'. $php_version .'</span>';
								} else {
									echo 'To work, you must have version <strong>'. $required_php_version .'</strong> of the script, you have the <span class="bred">'. $php_version .'</span>';
								}
							}	
						} else {
							echo $php_version;
						}						
					} elseif($key == 2){ 
						if ( extension_loaded('ionCube Loader') and function_exists('ioncube_loader_version') ) {
							$ioncube_loader_version = ioncube_loader_version();
							if($ioncube_version){
								if ($ioncube_loader_version >= $ioncube_version) {
									echo $ioncube_loader_version;
								} else {
									if($lang == 'ru'){ 
										echo 'Для работы, скрипту необходима версия <strong>'. $ioncube_version .'</strong>, у вас установлена <span class="bred">'. $ioncube_loader_version .'</span>';
									} else {
										echo 'To work, you must have version <strong>'. $ioncube_version .'</strong> of the script, you have the <span class="bred">'. $ioncube_loader_version .'</span>';
									}
								}
							} else {
								echo $ioncube_loader_version;
							}
						} else {
							if($lang == 'ru'){ 
								echo '<span class="bred">ionCube отсутствует. Установите или активируйте в настройках хостинга</span>';
							} else {
								echo '<span class="bred">ionCube missing. Install or activate the settings hosting.</span>';
							}
						}
					} elseif($key == 3){ 
						if ( !extension_loaded( 'mysqli' ) ) {
							if($lang == 'ru'){ 
								echo '<span class="bred">MYSQLI отсутствует</span>';
							} else {
								echo '<span class="bred">MYSQLI missing</span>';
							}
						} else {
							if($lang == 'ru'){ 
								echo 'OK';
							}
						}
					} ?>
					</td>
				</tr>
				<?php } ?>
							
			</table>
		
		<?php } elseif($step == 3){ ?>
		
			<table>
				<?php
				$check_arr = array(
					'1' => array(
						'title_ru' => 'iconv',
						'title_en' => 'iconv',
					),
					'2' => array(
						'title_ru' => 'Библиотека MB',
						'title_en' => 'MB library',
					),
					'3' => array(
						'title_ru' => 'CURL',
						'title_en' => 'CURL',
					),
					'4' => array(
						'title_ru' => 'Библиотека GD',
						'title_en' => 'GD library',
					),	
					'5' => array(
						'title_ru' => 'Дата и время',
						'title_en' => 'Date and time',
					),
					'6' => array(
						'title_ru' => 'Object',
						'title_en' => 'Object',
					),
					'7' => array(
						'title_ru' => 'strlen',
						'title_en' => 'strlen',
					),
					'8' => array(
						'title_ru' => 'sprintf',
						'title_en' => 'sprintf',
					),
					'9' => array(
						'title_ru' => 'ip2long',
						'title_en' => 'ip2long',
					),
					'10' => array(
						'title_ru' => 'serialize',
						'title_en' => 'serialize',
					),
					'11' => array(
						'title_ru' => 'unserialize',
						'title_en' => 'unserialize',
					),
					'12' => array(
						'title_ru' => 'crypt',
						'title_en' => 'crypt',
					),
					'14' => array(
						'title_ru' => 'parse_str',
						'title_en' => 'parse_str',
					),
					'15' => array(
						'title_ru' => 'json_encode',
						'title_en' => 'json_encode',
					),	
					'16' => array(
						'title_ru' => 'strtr',
						'title_en' => 'strtr',
					),
					'17' => array(
						'title_ru' => 'strstr',
						'title_en' => 'strstr',
					),
					'18' => array(
						'title_ru' => 'round',
						'title_en' => 'round',
					),
					'19' => array(
						'title_ru' => 'parse_url',
						'title_en' => 'parse_url',
					),
					'20' => array(
						'title_ru' => 'simplexml_load_string',
						'title_en' => 'simplexml_load_string',
					),
					'21' => array(
						'title_ru' => 'array_search',
						'title_en' => 'array_search',
					),
					'22' => array(
						'title_ru' => 'библиотека gmp',
						'title_en' => 'gmp library',
					),
					'23' => array(
						'title_ru' => 'библиотека mcrypt',
						'title_en' => 'mcrypt library',
					),					
				);
				?>
				<?php foreach($check_arr as $key => $val){ 
					if($lang == 'ru'){ 
						$title = $val['title_ru'];
					} else {
						$title = $val['title_en'];
					}
				?>
				<tr>
					<th><?php echo $title; ?></th>
					<td>
						<?php if($key == 1){ ?>
							<?php if (function_exists('iconv')){ ?>
								OK
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">Функция iconv отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">iconv missing. Install or activate the settings hosting.</span>';
								}								
								?>
							<?php } ?>
						<?php } elseif($key == 2){ ?>
							<?php if (function_exists('mb_strtoupper') and function_exists('mb_substr') and function_exists('mb_strtolower') and function_exists('mb_strlen')){ ?>
								OK 
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">missing. Install or activate the settings hosting.</span>';
								}								
								?>							
							<?php } ?>
						<?php } elseif($key == 3){ ?>
							<?php if (extension_loaded('curl')){ ?>
								OK 
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">missing. Install or activate the settings hosting.</span>';
								}								
								?>							
							<?php } ?>						
						<?php } elseif($key == 4){ ?>
							<?php if (function_exists('imagecreatetruecolor') and function_exists('imagecolorallocate') and function_exists('imagefill') and function_exists('imagettftext')){ ?>
								OK 
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">missing. Install or activate the settings hosting.</span>';
								}								
								?>							
							<?php } ?>						
						<?php } elseif($key == 5){ ?>
							<?php echo date('d.m.Y H:i'); ?>
						<?php } elseif($key == 6){ ?>
							<?php
							$arr = array('test'=>'1');
							$arr = (object)$arr;
							if(isset($arr->test)){
							?>
							OK
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">сервер неверно обрабатывает объекты</span>';
								} else {
									echo '<span class="bred">the server does not properly handle objects.</span>';
								}								
								?>							
							<?php } ?>
						<?php } elseif($key == 7){ ?>
							<?php
							$word = 'test';
							if (function_exists('strlen') and strlen($word) == 4){ 
							?>
							OK
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">работает не верно</span>';
								} else {
									echo '<span class="bred">It does not work properly</span>';
								}								
								?>							
							<?php } ?>
						<?php } elseif($key == 8){ ?>
							<?php
							if (function_exists('sprintf')){ 
							?>
							OK
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">missing. Install or activate the settings hosting.</span>';
								}								
								?>							
							<?php } ?>						
						<?php } elseif($key == 9){ ?>
							<?php
							if (function_exists('ip2long')){ 
							?>
							OK
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">missing. Install or activate the settings hosting.</span>';
								}								
								?>							
							<?php } ?>						
						<?php } elseif($key == 10){ ?>
							<?php
							if (function_exists('serialize')){ 
							?>
							OK
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">missing. Install or activate the settings hosting.</span>';
								}								
								?>							
							<?php } ?>						
						<?php } elseif($key == 11){ ?>
							<?php
							if (function_exists('unserialize')){ 
							?>
							OK
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">missing. Install or activate the settings hosting.</span>';
								}								
								?>							
							<?php } ?>						
						<?php } elseif($key == 12){ ?>
							<?php
							if (function_exists('crypt')){ 
							?>
							OK
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">missing. Install or activate the settings hosting.</span>';
								}								
								?>							
							<?php } ?>												
						<?php } elseif($key == 14){ ?>
							<?php
							if (function_exists('parse_str')){
								$val = 'test=1&test2=2';
								parse_str($val, $val_arr);
								if(isset($val_arr['test']) and isset($val_arr['test2'])){
							?>
								OK
								<?php
								} else { ?>
									<?php
									if($lang == 'ru'){ 
										echo '<span class="bred">работает неверно</span>';
									} else {
										echo '<span class="bred">It does not work properly</span>';
									}								
									?>								
								<?php } ?>
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">missing. Install or activate the settings hosting.</span>';
								}								
								?>							
							<?php } ?>						
						<?php } elseif($key == 15){ ?>
							<?php
							if (function_exists('json_encode')){ 
							?>
							OK
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">missing. Install or activate the settings hosting.</span>';
								}								
								?>							
							<?php } ?>						
						<?php } elseif($key == 16){ ?>
							<?php
							if (function_exists('strtr')){ 
							?>
							OK
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">missing. Install or activate the settings hosting.</span>';
								}								
								?>							
							<?php } ?>						
						<?php } elseif($key == 17){ ?>
							<?php
							if (function_exists('strstr')){ 
							?>
							OK
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">missing. Install or activate the settings hosting.</span>';
								}								
								?>							
							<?php } ?>						
						<?php } elseif($key == 18){ ?>
							<?php
							if (function_exists('round')){ 
							?>
							OK
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">missing. Install or activate the settings hosting.</span>';
								}								
								?>							
							<?php } ?>						
						<?php } elseif($key == 19){ ?>
							<?php
							if (function_exists('parse_url')){ 
							?>
							OK
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">missing. Install or activate the settings hosting.</span>';
								}								
								?>							
							<?php } ?>						
						<?php } elseif($key == 20){ ?>
							<?php
							if (function_exists('simplexml_load_string')){ 
							?>
							OK
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">missing. Install or activate the settings hosting.</span>';
								}								
								?>							
							<?php } ?>						
						<?php } elseif($key == 21){ ?>
							<?php
							if (function_exists('array_search')){ 
							?>
							OK
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">missing. Install or activate the settings hosting.</span>';
								}								
								?>							
							<?php } ?>
						<?php } elseif($key == 22){ ?>
							<?php
							if (extension_loaded('gmp')){ 
							?>
							OK
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">missing. Install or activate the settings hosting.</span>';
								}								
								?>							
							<?php } ?>
						<?php } elseif($key == 23){ ?>
							<?php
							if (extension_loaded('mcrypt')){ 
							?>
							OK
							<?php } else { ?>
								<?php
								if($lang == 'ru'){ 
									echo '<span class="bred">отсутствует. Установите или активируйте в настройках хостинга</span>';
								} else {
									echo '<span class="bred">missing. Install or activate the settings hosting.</span>';
								}								
								?>							
							<?php } ?>							
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
							
			</table>		
		
		<?php } elseif($step == 4){ ?>
			<?php
			if($lang == 'ru'){ 
				$button = 'Отправить письмо';
			} else {
				$button = 'Send message';
			}
			
			if (function_exists('mail')){
			?>
			<form method="post" class="ajax_post_form" action="ajax/mess.php?lang=<?php echo $lang; ?>">
			<table>
				<tr>
					<th>E-mail</th>
					<td>
						<input type="text" name="email" value="" />
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input type="submit" name="s" value="<?php echo $button; ?>" />
					</td>
				</tr>							
			</table>
			</form>
			<?php } else { 
				if($lang == 'ru'){ 
					echo '<span class="bred">Функция <strong>mail</strong> отсутствует. Установите или активируйте в настройках хостинга</span>';
				} else {
					echo '<span class="bred">Function <strong>mail</strong> missing. Install or activate the settings hosting.</span>';
				}			
			} ?>
		<?php } elseif($step == 5){ ?>	
			
			<table>
				<?php
				$check_arr = array(
					'1' => array(
						'title_ru' => 'Файл wp-config.php',
						'title_en' => 'File wp-config.php',
					),
					'2' => array(
						'title_ru' => 'Папка /wp-content/uploads/',
						'title_en' => 'Directory /wp-content/uploads/',
					),
					'3' => array(
						'title_ru' => 'Файл .htaccess',
						'title_en' => 'File .htaccess',
					),					
				);
				?>
				<?php foreach($check_arr as $key => $val){ 
					if($lang == 'ru'){ 
						$title = $val['title_ru'];
					} else {
						$title = $val['title_en'];
					}
				?>
				<tr>
					<th><?php echo $title; ?></th>
					<td>
						<?php if($key == 1){ ?>
							<?php 
							$file = DIR_PATH . 'wp-config.php';
							if(is_file($file)){ ?>
								<?php if(is_writable($file)){ ?>
									OK
								<?php } else { ?>
									<?php if($lang == 'ru'){ ?>
										<span class="bred">Файл недоступен для записи. Временно установите на него права 777.</span>
									<?php } else { ?>
										<span class="bred">The file is not writable. Temporarily install it right 777.</span>
									<?php } ?>								
								<?php } ?>
							<?php } else { ?>
								<?php if($lang == 'ru'){ ?>
									<span class="bred">Файл отсутствует. Перезагрузите дистрибутив.</span>
								<?php } else { ?>
									<span class="bred">The file is missing. Restart distribution.</span>
								<?php } ?>
							<?php } ?>
						<?php } elseif($key == 2){ ?>
							<?php 
							$file = DIR_PATH . 'wp-content/uploads/';
							if(is_dir($file)){ ?>
								<?php if(is_writable($file)){ ?>
									OK
								<?php } else { ?>
									<?php if($lang == 'ru'){ ?>
										<span class="bred">Директория недоступна для записи. Установите на неё права 777.</span>
									<?php } else { ?>
										<span class="bred">The directory can not be written. Install it right 777.</span>
									<?php } ?>								
								<?php }  ?>
							<?php } else { ?>
								<?php if($lang == 'ru'){ ?>
									<span class="bred">Директория отсутствует. Перезагрузите дистрибутив.</span>
								<?php } else { ?>
									<span class="bred">The directory is missing. Restart distribution.</span>
								<?php } ?>
							<?php } ?>						
						<?php } elseif($key == 3){ ?>
							<?php 
							$file = DIR_PATH . '.htaccess';
							if(is_file($file)){ ?>
								<?php if(is_writable($file)){ ?>
									OK
								<?php } else { ?>
									<?php if($lang == 'ru'){ ?>
										<span class="bred">Файл недоступен для записи. Временно установите на него права 777.</span>
									<?php } else { ?>
										<span class="bred">The file is not writable. Temporarily install it right 777.</span>
									<?php } ?>								
								<?php } ?>
							<?php } else { ?>
								<?php if($lang == 'ru'){ ?>
									<span class="bred">Файл отсутствует. Перезагрузите дистрибутив.</span>
								<?php } else { ?>
									<span class="bred">The file is missing. Restart distribution.</span>
								<?php } ?>
							<?php } ?>						
						<?php } ?>
					</td>
				</tr>
				<?php } ?>
							
			</table>			
			
		<?php } elseif($step == 6){ ?>
			<?php
			if($lang == 'ru'){ 
				$button = 'Обновить конфиг';
				$text = 'Если доступы к базе данных уже введены вручную, пропустите этот шаг';
				$db_name = 'Имя базы данных';
				$db_name_descr = 'Имя базы данных, в которую вы хотите установить продукт.';
				$db_user = 'Имя пользователя';
				$db_user_descr = 'Имя пользователя MySQL.';
				$db_pass = 'Пароль';
				$db_pass_descr = 'Пароль пользователя MySQL.';	
				$db_host = 'Сервер базы данных';
				$db_host_descr = 'Если localhost не работает, нужно узнать правильный адрес в службе поддержки хостинг-провайдера.';				
			} else {
				$button = 'Update configuration';
				$text = 'If access to the database already entered manually, skip this step';
				$db_name = 'The database name';
				$db_name_descr = 'Name of the database in which you want to install the product.';
				$db_user = 'Username';
				$db_user_descr = 'Username MySQL.';
				$db_pass = 'Password';
				$db_pass_descr = 'The user password MySQL.';	
				$db_host = 'The database server';
				$db_host_descr = 'If localhost does not work, you need to know the correct address in a support service hosting provider.';				
			}
			?>
			<form method="post" action="?step=<?php echo $step; ?>&lang=<?php echo $lang; ?>">
			<table>
				<tr>
					<th><?php echo $db_name; ?></th>
					<td>
						<input type="text" name="db_name" value="<?php echo $db_name_v; ?>" />
						<p><?php echo $db_name_descr; ?></p>
					</td>
				</tr>
				<tr>
					<th><?php echo $db_user; ?></th>
					<td>
						<input type="text" name="db_user" value="<?php echo $db_user_v; ?>" />
						<p><?php echo $db_user_descr; ?></p>
					</td>
				</tr>
				<tr>
					<th><?php echo $db_pass; ?></th>
					<td>
						<input type="text" name="db_pass" value="<?php echo $db_pass_v; ?>" />
						<p><?php echo $db_pass_descr; ?></p>
					</td>
				</tr>
				<tr>
					<th><?php echo $db_host; ?></th>
					<td>
						<input type="text" name="db_host" value="<?php echo $db_host_v; ?>" />
						<p><?php echo $db_host_descr; ?></p>
					</td>
				</tr>				
				
				<tr>
					<th></th>
					<td>
						<input type="submit" name="submit" value="<?php echo $button; ?>" />
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<?php echo $text; ?>
					</td>
				</tr>				
			</table>
			</form>
			
		<?php } elseif($step == 7){ ?>

			<?php
			$max_size = wp_max_upload_size_i();
			$max_mb = 0;
			if($max_size > 0){
				$max_mb = $max_size / 1024 / 1024;
			}
			
			if($lang == 'ru'){ 
				$button = 'Импортировать базу данных';
				$dump_db = 'Дамп базы данных';
				$dump_db_descr = 'Выберете дамп базы данных с вашего компьютера для импорта. (max. '. $max_mb .' MB)';
				$drop_txt = 'Удалить старые таблицы';
				$c_url = 'URL сайта';
				$c_url_descr = 'Укажите адрес вашего домена с http// или https://';				
			} else {
				$button = 'Import database';
				$dump_db = 'Dump database';
				$dump_db_descr = 'Select a database dump from your computer to import. (max. '. $max_mb .' MB)';
				$drop_txt = 'Delete old tables';
				$c_url = 'URL';
				$c_url_descr = 'Enter the address of your domain http // or https: //';				
			}
			?>		
			<form method="post" enctype="multipart/form-data" action="?step=<?php echo $step; ?>&lang=<?php echo $lang; ?>">
			<table>
				<tr>
					<th><?php echo $dump_db; ?></th>
					<td>
						<input type="file" name="dump_db" value="" />
						<p><?php echo $dump_db_descr; ?></p>
					</td>
				</tr>
				<tr>
					<th><?php echo $c_url; ?></th>
					<td>
						<input type="text" name="c_url" required placeholder="http://site.ru" value="<?php echo $c_url_v; ?>" />
						<p><?php echo $c_url_descr; ?></p>
					</td>
				</tr>				
				<tr>
					<th></th>
					<td>
						<label><input type="checkbox" name="del_old" value="1" /> <?php echo $drop_txt; ?></label>
					</td>
				</tr>				
				<tr>
					<th></th>
					<td>
						<input type="submit" name="submit" value="<?php echo $button; ?>" />
					</td>
				</tr>				
			</table>
			</form>		
		
		<?php } elseif($step == 8){ ?>
		
			<?php
			if($lang == 'ru'){ 
				$button = 'Установить';
				$c_email = 'Ваш e-mail';
				$c_email_descr = 'Укажите ваш электронный адрес аналогично примеру. В настройках указанного электронного адреса обязательно включите смс-авторизацию при входе в аккаунт!';
				$c_login = 'Логин администратора';
				$c_login_descr = 'Укажите логин администратора для входа в систему управления.';
				$c_pass = 'Пароль администратора';
				$c_pass_descr = 'Укажите пароль администратора для входа в систему.';				
			} else {
				$button = 'Establish';
				$c_email = 'Your e-mail';
				$c_email_descr = 'Enter your email address, it will receive all correspondence from the site. Similarly example.';
				$c_login = 'Administrator login';
				$c_login_descr = 'Enter the administrator login to access the management system.';
				$c_pass = 'Administrator password';
				$c_pass_descr = 'Enter the administrator password to login.';				
			}
			?>		
			<form method="post" action="?step=<?php echo $step; ?>&lang=<?php echo $lang; ?>">
			<table>
				<tr>
					<th><?php echo $c_email; ?></th>
					<td>
						<input type="text" name="c_email" required placeholder="mail@site.ru" value="<?php echo $c_email_v; ?>" />
						<p><?php echo $c_email_descr; ?></p>
					</td>
				</tr>
				<tr>
					<th><?php echo $c_login; ?></th>
					<td>
						<input type="text" name="c_login" required placeholder="notadmin" value="<?php echo $c_login_v; ?>" />
						<p><?php echo $c_login_descr; ?></p>
					</td>
				</tr>
				<tr>
					<th><?php echo $c_pass; ?></th>
					<td>
						<input type="text" name="c_pass" id="adm_new_password" required value="<?php echo $c_pass_v; ?>" /> <a href="#" id="adm_generate_pass" class="button">Сгенерировать</a>
						<p><?php echo $c_pass_descr; ?></p>
					</td>
				</tr>				
				<tr>
					<th></th>
					<td>
						<input type="submit" name="submit" id="osn_config" value="<?php echo $button; ?>" />
					</td>
				</tr>				
			</table>
			</form>		
		<?php } elseif($step == 9){ ?>
			<?php if($plugin_installer == 'exchangebox' or $plugin_installer == 'lendbox'){ ?>
				
				<?php
				if($lang == 'ru'){
					$site_lang = 'Язык сайта';
					$admin_lang = 'Язык админки';
					$multi_lang = 'Мультиязычность';
					$button = 'Сохранить';
				} else {
					$site_lang = 'Site language';
					$admin_lang = 'Admin-panel language';
					$multi_lang = 'Multilingual';	
					$button = 'Save';
				}
				?>
				
				<form method="post" action="?step=<?php echo $step; ?>&lang=<?php echo $lang; ?>">
				<table>
					<tr>
						<th><?php echo $site_lang; ?></th>
						<td>
							<select name="site_lang">
								<option value="ru_RU" <?php if($lang == 'ru'){ ?>selected="selected"<?php } ?>>Русский</option>
								<option value="en_US" <?php if($lang == 'en'){ ?>selected="selected"<?php } ?>>English</option>
							</select>
						</td>
					</tr>
					<tr>
						<th><?php echo $admin_lang; ?></th>
						<td>
							<select name="admin_lang">
								<option value="ru_RU" <?php if($lang == 'ru'){ ?>selected="selected"<?php } ?>>Русский</option>
								<option value="en_US" <?php if($lang == 'en'){ ?>selected="selected"<?php } ?>>English</option>
							</select>
						</td>
					</tr>				
					<tr>
						<th></th>
						<td>
							<input type="submit" name="submit" value="<?php echo $button; ?>" />
						</td>
					</tr>				
				</table>
				</form>			
			<?php } elseif($plugin_installer == 'premiumbox'){ ?>
			
				<?php
				if($lang == 'ru'){
					$site_lang = 'Язык сайта';
					$admin_lang = 'Язык админки';
					$multi_lang = 'Мультиязычность';
					$button = 'Сохранить';
				} else {
					$site_lang = 'Site language';
					$admin_lang = 'Admin-panel language';
					$multi_lang = 'Multilingual';	
					$button = 'Save';
				}
				?>
				
				<form method="post" action="?step=<?php echo $step; ?>&lang=<?php echo $lang; ?>">
				<table>
					<tr>
						<th><?php echo $site_lang; ?></th>
						<td>
							<select name="site_lang">
								<option value="ru_RU" <?php if($lang == 'ru'){ ?>selected="selected"<?php } ?>>Русский</option>
								<option value="en_US" <?php if($lang == 'en'){ ?>selected="selected"<?php } ?>>English</option>
							</select>
						</td>
					</tr>
					<tr>
						<th><?php echo $admin_lang; ?></th>
						<td>
							<select name="admin_lang">
								<option value="ru_RU" <?php if($lang == 'ru'){ ?>selected="selected"<?php } ?>>Русский</option>
								<option value="en_US" <?php if($lang == 'en'){ ?>selected="selected"<?php } ?>>English</option>
							</select>
						</td>
					</tr>
					<tr>
						<th><?php echo $multi_lang; ?></th>
						<td>
							<select name="multilingual">
								<option value="1"><?php if($lang == 'ru'){ ?>Да<?php } else { ?>Yes<?php } ?></option>
								<option value="0"><?php if($lang == 'ru'){ ?>Нет<?php } else { ?>No<?php } ?></option>
							</select>
						</td>
					</tr>				
					<tr>
						<th></th>
						<td>
							<input type="submit" name="submit" value="<?php echo $button; ?>" />
						</td>
					</tr>				
				</table>
				</form>			
			
			<?php } ?>
		<?php } ?>
		
		<div class="step_navi">
			<?php 
			$prev = $step - 1; 
			$next = $step + 1;
			
			if($prev > 0){
				?>
				<a href="?step=<?php echo $prev; ?>&lang=<?php echo $lang; ?>" class="prev">
					<?php if($lang == 'ru'){ ?>Назад<?php } else { ?>Previous<?php } ?>
				</a>
				<?php
			} 
			
			if($next < $max_step and $next > 2){
				?>
				<a href="?step=<?php echo $next; ?>&lang=<?php echo $lang; ?>" class="next">
					<?php if($lang == 'ru'){ ?>Вперед<?php } else { ?>Next<?php } ?>
				</a>				
				<?php				
			}
			?>
				<div class="clear"></div>
		</div>
		
	</div>
</div>
</body>
</html>