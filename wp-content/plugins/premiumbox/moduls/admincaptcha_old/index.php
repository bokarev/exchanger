<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Captcha для Админ панели (простая)[:ru_RU][en_US:]Captcha for admin panel (light)[:en_US]
description: [ru_RU:]Captcha для Админ панели с математическим действием: сложение[:ru_RU][en_US:]Captcha for Admin Panel with mathematical operation used: addition[:en_US]
version: 1.0
category: [ru_RU:]Безопасность[:ru_RU][en_US:]Security[:en_US]
cat: secur
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_admincaptcha');
function bd_pn_moduls_active_admincaptcha(){
global $wpdb;	
	
	/* каптча админа */	
	$table_name = $wpdb->prefix ."admin_captcha";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`createdate` datetime NOT NULL,
		`sess_hash` varchar(150) NOT NULL,
		`num1` varchar(10) NOT NULL default '0',
		`num2` varchar(10) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	

}
/* end BD */

function ac_generate_image($word) {
global $premiumbox;

	$word = pn_strip_input($word);

	$fonts = array(
		$premiumbox->plugin_dir . 'moduls/admincaptcha_old/fonts/GenAI102.TTF',
		$premiumbox->plugin_dir . 'moduls/admincaptcha_old/fonts/GenAR102.TTF',
		$premiumbox->plugin_dir . 'moduls/admincaptcha_old/fonts/GenI102.TTF',
		$premiumbox->plugin_dir . 'moduls/admincaptcha_old/fonts/GenR102.TTF' 
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

function ac_del_img(){	
global $wpdb;

	$time = current_time('timestamp') - (1*24*60*60);
	$date = date('Y-m-d H:i:s', $time);
	$wpdb->query("DELETE FROM ".$wpdb->prefix."admin_captcha WHERE createdate < '$date'");	
	
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

add_filter('mycron_10min', 'acdel_mycron_10min');
function acdel_mycron_10min($filters){
	$filters['ac_del_img'] = __('Deleting admin captcha','pn');
	
	return $filters;
}

add_action('login_enqueue_scripts', 'ac_login_enqueue_scripts');
function ac_login_enqueue_scripts(){
global $premiumbox;	
	
	wp_deregister_script('jquery');
    wp_register_script('jquery', $premiumbox->plugin_url . 'premium/js/jquery.min.js', false, '3.2.1');
    wp_enqueue_script('jquery');
}

function ac_reload(){
global $wpdb;
	
	$sess_hash = get_session_id();
	$wpdb->query("DELETE FROM ".$wpdb->prefix."admin_captcha WHERE sess_hash = '$sess_hash'");
	
	$array = array();
	$array['sess_hash'] = $sess_hash;
	$array['createdate'] = current_time('mysql');
	$array['num1'] = mt_rand(1,9);
	$array['num2'] = mt_rand(0,9);
	$wpdb->insert($wpdb->prefix.'admin_captcha', $array);
	$insert_id = $wpdb->insert_id;
	
	return $insert_id;	
}

add_action('premium_action_ac_reload', 'the_premium_action_ac_reload');
function the_premium_action_ac_reload(){

	only_post();
	
	$log = array();
	$log['status'] = 'success';
	$log['status_text'] = '';
	$log['status_code'] = 0;

	global $wpdb;
	$id = ac_reload();
	$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."admin_captcha WHERE id='$id'");
	if(isset($data->id)){
		$num1 = intval($data->num1);
		$num2 = intval($data->num2);
	} else {
		$num1 = 0;
		$num2 = 0;		
	}
	$img1 = ac_generate_image($num1);
	$img2 = ac_generate_image($num2);

	$log['ncapt1'] = $img1;
	$log['ncapt2'] = $img2;

	echo json_encode($log);	
	exit;
}

add_action('login_footer','ac_login_footer');
add_action('newadminpanel_form_footer', 'ac_login_footer');
function ac_login_footer(){
	?>
<script type="text/javascript">	
jQuery(function($) {	 

	$(document).on('click', '.rlc_reload', function(){

		var dataString='have=reload';
		$.ajax({
		type: "POST",
		url: "<?php pn_the_link_post('ac_reload'); ?>",
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

add_action('login_form', 'ac_login_form' );
add_action('newadminpanel_form', 'ac_login_form');
function ac_login_form(){ 
global $wpdb;

	$temp = '';

	$id = ac_reload();
	$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."admin_captcha WHERE id='$id'");
	if(isset($data->id)){
		$num1 = intval($data->num1);
		$num2 = intval($data->num2);
	} else {
		$num1 = 0;
		$num2 = 0;		
	}
	
	$img1 = ac_generate_image($num1);
	$img2 = ac_generate_image($num2);
	
	$temp = '
		<div class="rlc_div">
			<div class="rlc_divimg">
				<img src="'. $img1 .'" class="captcha1" alt="" />
			</div>
			<div class="rlc_divznak">
				+
			</div>	
			<div class="rlc_divimg">
				<img src="'. $img2 .'" class="captcha2" alt="" />
			</div>
			<div class="rlc_divznak">
				=
			</div>

			<input type="text" class="rlc_divpole" name="number" maxlength="3" autocomplete="off" value="" />
			
				<div class="clear"></div>
		</div>
		<div class="rlc_div_change">
			<a href="#" class="rlc_reload">'. __('replace task','pn') .'</a>
		</div>
	';
	
	echo $temp;
}

add_filter('authenticate', 'ac_login_check', 99, 1 );
function ac_login_check($user){
global $wpdb;

	if(isset($_POST['log']) and isset($_POST['pwd'])){
		$number = '';
		if(isset($_POST['number']) and $_POST['number']){
			$number = trim($_POST['number']);
			$sess_hash = get_session_id();
			$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."admin_captcha WHERE sess_hash='$sess_hash'");
			if(isset($data->id)){	
				$num1 = $data->num1;
				$num2 = $data->num2;
				$sum = $num1+$num2;
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

add_filter('newadminpanel_ajax_form', 'ac_newadminpanel_ajax_form');
function ac_newadminpanel_ajax_form($log){
global $wpdb;

	$number = trim(is_param_post('number'));
	if($number){
		$sess_hash = get_session_id();
		$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."admin_captcha WHERE sess_hash='$sess_hash'");
		if(isset($data->id)){		
			$num1 = $data->num1;
			$num2 = $data->num2;
			$sum = $num1+$num2;
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
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('<strong>Error:</strong> You have not entered test number.','pn');	
		echo json_encode($log);
		exit;
	}
	
	return $log;
}