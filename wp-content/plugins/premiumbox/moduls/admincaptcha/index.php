<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Captcha для Админ панели (расширенная)[:ru_RU][en_US:]Captcha for Admin Panel (extended)[:en_US]
description: [ru_RU:]Captcha для Админ панели с математическими действиями: умножение, сложение, вычитание[:ru_RU][en_US:]Captcha for Admin Panel with mathematical operations used: multiplication, addition, subtraction[:en_US]
version: 1.0
category: [ru_RU:]Безопасность[:ru_RU][en_US:]Security[:en_US]
cat: secur
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_admincaptcha_plus');
function bd_pn_moduls_active_admincaptcha_plus(){
global $wpdb;	
	
	/* каптча админа */	
	$table_name = $wpdb->prefix ."admin_captcha_plus";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`createdate` datetime NOT NULL,
		`sess_hash` varchar(150) NOT NULL,
		`num1` varchar(10) NOT NULL default '0',
		`num2` varchar(10) NOT NULL default '0',
		`symbol` int(2) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	
	
}
/* end BD */

function acp_generate_image($word) {
global $premiumbox;

	$word = pn_strip_input($word);

	$fonts = array(
		$premiumbox->plugin_dir . 'moduls/admincaptcha/fonts/GenAI102.TTF',
		$premiumbox->plugin_dir . 'moduls/admincaptcha/fonts/GenAR102.TTF',
		$premiumbox->plugin_dir . 'moduls/admincaptcha/fonts/GenI102.TTF',
		$premiumbox->plugin_dir . 'moduls/admincaptcha/fonts/GenR102.TTF' 
	);

	$wp_upload_dir = wp_upload_dir();
	$path = $wp_upload_dir['basedir'];
	$dir = trailingslashit( $path . '/captcha/' );	
	if(!realpath($dir)){
		@mkdir($dir, 0777);
	}
	
	$filename = '';
	$prefix = time() . mt_rand(1000,1000000);

	if ( $im = imagecreatetruecolor( 50, 50 ) ) {

		$bg = imagecolorallocate( $im, 255, 255, 255 );
		$fg = imagecolorallocate( $im, 49, 118, 232 );
		imagefill( $im, 0, 0, $bg );

		$font = $fonts[array_rand( $fonts )];
		imagettftext($im, 30, 0, mt_rand(0,30), mt_rand(30,40) , $fg, $font, $word );

		$filename = sanitize_file_name( $prefix . '.png' );
		$link = $dir . $filename;
		imagepng( $im, $link );

		imagedestroy( $im );
				
	}

	return $wp_upload_dir['baseurl'] . '/captcha/'. $filename;
}

function acp_del_img(){	
global $wpdb;

	$time = current_time('timestamp') - (1*24*60*60);
	$date = date('Y-m-d H:i:s', $time);
	$wpdb->query("DELETE FROM ".$wpdb->prefix."admin_captcha_plus WHERE createdate < '$date'");	
	
	$wp_upload_dir = wp_upload_dir();
	$path = $wp_upload_dir['basedir'];
    $dir = trailingslashit( $path . '/captcha/' );
    if(is_array(glob("$dir*"))){
        foreach (glob("$dir*") as $filename) {
	        if(is_file($filename)){
			    @unlink($filename);
			}
        }
    }
	
}

add_filter('mycron_10min', 'acpdel_mycron_10min');
function acpdel_mycron_10min($filters){
	$filters['acp_del_img'] = __('Deleting admin captcha','pn');
	
	return $filters;
}

add_action('login_enqueue_scripts', 'acp_login_enqueue_scripts');
function acp_login_enqueue_scripts(){
global $premiumbox;
	
	wp_deregister_script('jquery');
    wp_register_script('jquery', $premiumbox->plugin_url .'premium/js/jquery.min.js', false, '3.2.1');
    wp_enqueue_script('jquery');
	
}

function acp_reload(){
global $wpdb;
	
	$sess_hash = get_session_id();
	$wpdb->query("DELETE FROM ".$wpdb->prefix."admin_captcha_plus WHERE sess_hash = '$sess_hash'");
	
	$array = array();
	$array['sess_hash'] = $sess_hash;
	$array['createdate'] = current_time('mysql');
	$array['num1'] = mt_rand(1,9);
	$array['num2'] = mt_rand(0,9);
	$array['symbol'] = mt_rand(0,2);
	$wpdb->insert($wpdb->prefix.'admin_captcha_plus', $array);
	$insert_id = $wpdb->insert_id;
	
	return $insert_id;	
}

add_action('premium_action_acp_reload', 'the_premium_action_acp_reload');
function the_premium_action_acp_reload(){

	only_post();
	
	$log = array();
	$log['status'] = 'success';
	$log['status_text'] = '';
	$log['status_code'] = 0;

	global $wpdb;
	$id = acp_reload();
	$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."admin_captcha_plus WHERE id='$id'");
	if(isset($data->id)){
		$num1 = intval($data->num1);
		$num2 = intval($data->num2);
		$symbol = intval($data->symbol);
	} else {
		$num1 = 0;
		$num2 = 0;
		$symbol = 0;
	}
	$img1 = acp_generate_image($num1);
	$img2 = acp_generate_image($num2);
	$symbols = array('+','-','x');

	$log['ncapt1'] = $img1;
	$log['ncapt2'] = $img2;
	$log['nsym'] = is_isset($symbols, $symbol);

	echo json_encode($log);	
	exit;
}

add_action('login_footer','acp_login_footer');
add_action('newadminpanel_form_footer', 'acp_login_footer');
function acp_login_footer(){
	?>
<script type="text/javascript">	
jQuery(function($) {	 

	$(document).on('click', '.rlc_reload', function(){
		var dataString='have=reload';
		$.ajax({
		type: "POST",
		url: "<?php pn_the_link_post('acp_reload'); ?>",
		dataType: 'json',
		data: dataString,
		error: function(res,res2,res3){
			<?php do_action('pn_js_error_response', 'ajax'); ?>
		},		
		success: function(res)
		{
			if(res['ncapt1']){
				$('.captcha1').attr('src',res['ncapt1']);
			}
			if(res['ncapt2']){
				$('.captcha2').attr('src',res['ncapt2']);
			}	
			if(res['nsym']){
				$('.captcha_sym').html(res['nsym']);
			}
		}
		});
		
		return false;
	});
		
});	
</script>
<style>
.rlc_div{
margin: 0 0 10px 0;
}
	.rlc_divimg{
	float: left;
	width: 50px;
	height: 50px;
	border: 1px solid #ddd;
	}
	.rlc_divznak{
	float: left;
	width: 30px;
	height: 50px;
	font: 30px/50px Arial;
	text-align: center;
	}
	input.rlc_divpole{
	float: left;
	width: 80px!important;
	height: 50px!important;
	font: 30px/50px Arial!important;
	margin: 0!important;
	text-align: center;
	}
	.rlc_div_change{
	padding: 0 0 10px 0;
	}
	
.clear{ clear: both; }	
</style>
	<?php
}

add_action('login_form', 'acp_login_form' );
add_action('newadminpanel_form', 'acp_login_form');
function acp_login_form(){ 
global $wpdb;

	$temp = '';

	$id = acp_reload();
	$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."admin_captcha_plus WHERE id='$id'");
	if(isset($data->id)){
		$num1 = intval($data->num1);
		$num2 = intval($data->num2);
		$symbol = intval($data->symbol);
	} else {
		$num1 = 0;
		$num2 = 0;		
		$symbol = 0;
	}
	
	$symbols = array('+','-','x');
	
	$img1 = acp_generate_image($num1);
	$img2 = acp_generate_image($num2);
	
	$temp = '
		<div class="rlc_div">
			<div class="rlc_divimg">
				<img src="'. $img1 .'" class="captcha1" alt="" />
			</div>
			<div class="rlc_divznak">
				<span class="captcha_sym">'. is_isset($symbols, $symbol) .'</span>
			</div>	
			<div class="rlc_divimg">
				<img src="'. $img2 .'" class="captcha2" alt="" />
			</div>
			<div class="rlc_divznak">
				=
			</div>

			<input type="text" class="rlc_divpole" name="number" maxlength="4" autocomplete="off" value="" />
			
				<div class="clear"></div>
		</div>
		<div class="rlc_div_change">
			<a href="#" class="rlc_reload">'. __('replace task','pn') .'</a>
		</div>
	';
	
	echo $temp;
}

add_filter('authenticate', 'acp_login_check', 99, 1 );
function acp_login_check($user){
global $wpdb;

	if(isset($_POST['log']) and isset($_POST['pwd'])){

		$number = '';
		if(isset($_POST['number'])){
			$number = trim($_POST['number']);
		
			$sess_hash = get_session_id();
			$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."admin_captcha_plus WHERE sess_hash='$sess_hash'");
			if(isset($data->id)){
					
				$num1 = $data->num1;
				$num2 = $data->num2;
				$symbol = intval($data->symbol);
				if($symbol == 0){
					$sum = $num1+$num2;
				} elseif($symbol == 1){
					$sum = $num1-$num2;
				} else {
					$sum = $num1*$num2;
				}
				if($number == $sum){
					
					return $user;
					
				} else {
					$error = new WP_Error();
					$error->add( 'pn_error', __('<strong>Error:</strong> You have not entered test number.','pn') );
					wp_clear_auth_cookie();
					return $error;			
				}
				
			} else {
				$error = new WP_Error();
				$error->add( 'pn_error', __('<strong>Error:</strong> You have not entered test number.','pn') );
				wp_clear_auth_cookie();
				return $error;				
			}
		} else {
			$error = new WP_Error();
			$error->add( 'pn_error', __('<strong>Error:</strong> You have not entered test number.','pn') );
			wp_clear_auth_cookie();
			return $error;
		}
	
	} else {
		return $user;
	}
}	

add_filter('newadminpanel_ajax_form', 'acp_newadminpanel_ajax_form');
function acp_newadminpanel_ajax_form($log){
global $wpdb;

	$number = trim(is_param_post('number'));
	$sess_hash = get_session_id();
	$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."admin_captcha_plus WHERE sess_hash='$sess_hash'");
	if(isset($data->id)){		
		$num1 = $data->num1;
		$num2 = $data->num2;
		$symbol = intval($data->symbol);
		if($symbol == 0){
			$sum = $num1+$num2;
		} elseif($symbol == 1){
			$sum = $num1-$num2;
		} else {
			$sum = $num1*$num2;
		}
		if($number != $sum){
			$log['status'] = 'error';
			$log['status_code'] = 1;
			$log['status_text'] = __('<strong>Error:</strong> You have not entered test number.','pn');	
			echo json_encode($log);
			exit;			
		}	
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('<strong>Error:</strong> You have not entered test number.','pn');	
		echo json_encode($log);
		exit;				
	}
	
	return $log;
}