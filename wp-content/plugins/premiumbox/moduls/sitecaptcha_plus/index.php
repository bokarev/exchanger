<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Captcha для сайта (расширенная)[:ru_RU][en_US:]Captcha for website (extended)[:en_US]
description: [ru_RU:]Captcha для сайта с математическими действиями: умножение, сложение, вычитание[:ru_RU][en_US:]Captcha for website with mathematical operations: multiplication, addition, subtraction[:en_US]
version: 1.0
category: [ru_RU:]Безопасность[:ru_RU][en_US:]Security[:en_US]
cat: secur
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_sitecaptcha_plus');
function bd_pn_moduls_active_sitecaptcha_plus(){
global $wpdb;	
		
	$table_name = $wpdb->prefix ."standart_captcha_plus";
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

add_filter('captcha_settings', 'captcha_settings_sitecaptcha_plus');
function captcha_settings_sitecaptcha_plus($ind){
	return 1;
}
				
/* функции каптчи */
function scp_generate_image($word) {
global $premiumbox;

	$word = pn_strip_input($word);

	$fonts = array(
		$premiumbox->plugin_dir . 'moduls/sitecaptcha_plus/fonts/GenAI102.TTF',
		$premiumbox->plugin_dir . 'moduls/sitecaptcha_plus/fonts/GenAR102.TTF',
		$premiumbox->plugin_dir . 'moduls/sitecaptcha_plus/fonts/GenI102.TTF',
		$premiumbox->plugin_dir . 'moduls/sitecaptcha_plus/fonts/GenR102.TTF' 
	);
	$fonts = apply_filters('pn_sc_fonts', $fonts);

	$wp_upload_dir = wp_upload_dir();
	$path = $wp_upload_dir['basedir'];
	$dir = trailingslashit( $path . '/captcha/' );	
	if(!realpath($dir)){
		@mkdir($dir, 0777);
	}
	
	$filename = '';
	$prefix = time() . mt_rand(1000,1000000);

	if ( $im = imagecreatetruecolor( 50, 50 ) ) {

		$bgcolor = apply_filters('pn_sc_bgcolor', array('255','255','255'));
		$bg = imagecolorallocate( $im, $bgcolor[0], $bgcolor[1], $bgcolor[2]);
		
		$color = apply_filters('pn_sc_color', array('49','118','232'));
		$fg = imagecolorallocate( $im, $color[0], $color[1], $color[2] );
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

add_action('init','scp_init');
function scp_init(){
global $wpdb;
	
	$sess_hash = get_session_id();
	$cc = $wpdb->query("SELECT id FROM ".$wpdb->prefix."standart_captcha_plus WHERE sess_hash = '$sess_hash'");
	if($cc == 0){
	
		$array = array();
		$array['sess_hash'] = $sess_hash;
		$array['createdate'] = current_time('mysql');
		$array['num1'] = mt_rand(1,9);
		$array['num2'] = mt_rand(0,9);
		$array['symbol'] = mt_rand(0,2);
		$wpdb->insert($wpdb->prefix.'standart_captcha_plus', $array);

	}
}

function scp_del_img(){	
global $wpdb;

	$time = current_time('timestamp') - (24*60*60);
	$date = date('Y-m-d H:i:s', $time);
	$wpdb->query("DELETE FROM ".$wpdb->prefix."standart_captcha_plus WHERE createdate < '$date'");	
	
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

add_filter('mycron_10min', 'mycron_10min_scpdel');
function mycron_10min_scpdel($filters){
	
	$filters['scp_del_img'] = __('Delete standard captcha','pn');
	
	return $filters;
}

add_action('ajax_post_form_jsresult','ajax_post_form_jsresult_captcha_plus');
function ajax_post_form_jsresult_captcha_plus(){
?>
	if(res['ncapt1']){
		$('.captcha1').attr('src',res['ncapt1']);
	}
	if(res['ncapt2']){
		$('.captcha2').attr('src',res['ncapt2']);
	}
	if(res['nsym']){
		$('.captcha_sym').html(res['nsym']);
	}	
<?php	
} 

add_action('siteplace_js','siteplace_js_captcha_plus');
function siteplace_js_captcha_plus(){
?>
jQuery(function($){ 
	$(document).on('click', '.captcha_reload', function(){
		
		var thet = $(this);
		thet.addClass('act');
		
		var dataString='have=reload';
		$.ajax({
		type: "POST",
		url: "<?php echo get_ajax_link('scp_reload'); ?>",
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
			
			thet.removeClass('act');
		}
		});
		
		return false;
	});
});	
<?php	
}

function scp_reload(){
global $wpdb, $premiumbox;
	$data = array();

	$sess_hash = get_session_id();
	$wpdb->query("DELETE FROM ".$wpdb->prefix."standart_captcha_plus WHERE sess_hash = '$sess_hash'");
	
	$array = array();
	$array['sess_hash'] = $sess_hash;
	$array['createdate'] = current_time('mysql');
	$array['num1'] = mt_rand(1,9);
	$array['num2'] = mt_rand(0,9);
	$array['symbol'] = $symbol = mt_rand(0,2);
	$wpdb->insert($wpdb->prefix.'standart_captcha_plus', $array);

	$symbols = array('+','-','x');
	
	$data['img1'] = scp_generate_image($array['num1']);
	$data['img2'] = scp_generate_image($array['num2']);	
	$data['nsym'] = is_isset($symbols, $symbol);
	
	return $data;
}

add_action('myaction_site_scp_reload', 'the_myaction_site_scp_reload');
function the_myaction_site_scp_reload(){
global $premiumbox;	
	only_post();
	
	$log = array();
	$log['status'] = 'success';
	$log['status_text'] = '';
	$log['status_code'] = 0;
	
	$premiumbox->up_mode();
	
	$data = scp_reload();

	$log['ncapt1'] = $data['img1'];
	$log['ncapt2'] = $data['img2'];
	$log['nsym'] = $data['nsym'];

	echo json_encode($log);
	exit;
}

function get_captcha_plus_temp($img1,$img2, $symbol){
	
	$temp = '
		<div class="captcha_div">
			<div class="captcha_title">
				'. __('Enter your reply','pn') .'
			</div>
			<div class="captcha_body">
				<div class="captcha_divimg">
					<img src="'. $img1 .'" class="captcha1" alt="" />
				</div>
				<div class="captcha_divznak">
					<span class="captcha_sym">'. $symbol .'</span>
				</div>	
				<div class="captcha_divimg">
					<img src="'. $img2 .'" class="captcha2" alt="" />
				</div>
				<div class="captcha_divznak">
					=
				</div>

				<input type="text" class="captcha_divpole" name="number" maxlength="4" autocomplete="off" value="" />
			
					<div class="clear"></div>
			</div>
			<div class="captcha_div_change">
				<a href="#" class="captcha_reload">'. __('replace task','pn') .'</a>
			</div>
		</div>	
	';
	
	$temp = apply_filters('get_captcha_temp', $temp, $img1, $img2, $symbol);
	
	return $temp;
}
/* end функции каптчи */

add_filter('get_form_filelds','get_form_filelds_sitecaptcha_plus', 10, 2);
function get_form_filelds_sitecaptcha_plus($items, $name){
global $premiumbox;	
	if($premiumbox->get_option('captcha',$name) == 1){
		$items['captcha_plus'] = array(
			'type' => 'captcha_plus',
		);
	}
	return $items;
}

add_filter('before_ajax_form_field','before_ajax_form_field_sitecaptcha_plus', 99, 2);
function before_ajax_form_field_sitecaptcha_plus($logs, $name){
global $premiumbox, $wpdb;	

	if($premiumbox->get_option('captcha',$name) == 1){
		
		$number = trim(is_param_post('number'));	
		$sess_hash = get_session_id();
		$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."standart_captcha_plus WHERE sess_hash='$sess_hash'");
		$data_new = scp_reload();
		$logs['ncapt1'] = $data_new['img1'];
		$logs['ncapt2'] = $data_new['img2'];
		$logs['nsym'] = $data_new['nsym'];		
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
					
				$logs['status']	= 'error';
				$logs['status_code'] = '-3'; 
				$logs['status_text'] = __('Error! You have entered an incorrect number','pn');
				echo json_encode($logs);
				exit;
				
			}
					
		} else {
			$logs['status']	= 'error';
			$logs['status_code'] = '-3';
			$logs['status_text'] = __('Error! You have entered an incorrect number','pn');
			echo json_encode($logs);
			exit;
		}	
		
	}
	
	return $logs;
}

add_filter('form_field_line','form_field_line_sitecaptcha_plus', 10, 3);
function form_field_line_sitecaptcha_plus($line, $filter, $data){
global $wpdb;	
	
	$type = trim(is_isset($data, 'type'));
	if($type == 'captcha_plus'){
	
		$sess_hash = get_session_id();
		$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."standart_captcha_plus WHERE sess_hash='$sess_hash'");
		if(isset($data->id)){
			$num1 = $data->num1;
			$num2 = $data->num2;
			$symbol = intval($data->symbol);
		} else {
			$num1 = 1;
			$num2 = 2;
			$symbol = 0;
		}
		
		$symbols = array('+','-','x');
		
		$img1 = scp_generate_image($num1);
		$img2 = scp_generate_image($num2);

		$line = get_captcha_plus_temp($img1,$img2,is_isset($symbols, $symbol));	
	
	}
	
	return $line;
}


add_filter('exchange_step1', 'exchange_form_captcha_plus');
function exchange_form_captcha_plus($line){
global $wpdb, $premiumbox;

	if($premiumbox->get_option('captcha','exchangeform') == 1){
		$sess_hash = get_session_id();
		$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."standart_captcha_plus WHERE sess_hash='$sess_hash'");
		if(isset($data->id)){
			$num1 = $data->num1;
			$num2 = $data->num2;
			$symbol = intval($data->symbol);
		} else {
			$num1 = 1;
			$num2 = 2;
			$symbol = 0;
		}
		
		$symbols = array('+','-','x');	
			
		$img1 = scp_generate_image($num1);
		$img2 = scp_generate_image($num2);

		$line .= get_captcha_plus_temp($img1,$img2, is_isset($symbols, $symbol));
	}
	
	return $line;	
}