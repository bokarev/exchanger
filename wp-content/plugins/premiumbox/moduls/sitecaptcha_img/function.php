<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('init','sci_init');
function sci_init(){
global $wpdb;
	
	$sess_hash = get_session_id();
	$cc = $wpdb->query("SELECT id FROM ".$wpdb->prefix."sitecaptcha_user WHERE sess_hash = '$sess_hash'");
	if($cc == 0){
		$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."sitecaptcha_images ORDER BY RAND()");
		if(isset($item->id)){
			$array = array();
			$array['sess_hash'] = $sess_hash;
			$array['createdate'] = current_time('mysql');
			$array['uslov'] = $item->uslov;
			$array['img1'] = $item->img1;
			$array['img2'] = $item->img2;
			$array['img3'] = $item->img3;
			$array['num1'] = wp_generate_password( 35 , false, false);
			$array['num2'] = wp_generate_password( 35 , false, false);
			$array['num3'] = wp_generate_password( 35 , false, false);
			if($item->variant == 1){
				$array['variant'] = $array['num1'];
			} elseif($item->variant == 2){
				$array['variant'] = $array['num2'];
			} elseif($item->variant == 3){
				$array['variant'] = $array['num3'];
			}
			$wpdb->insert($wpdb->prefix.'sitecaptcha_user', $array);
		}
	}
}
 
function sci_del_img(){	
global $wpdb;

	$time = current_time('timestamp') - (24*60*60);
	$date = date('Y-m-d H:i:s', $time);
	$wpdb->query("DELETE FROM ".$wpdb->prefix."sitecaptcha_user WHERE createdate < '$date'");	
	
}

add_filter('mycron_10min', 'mycron_10min_scidel');
function mycron_10min_scidel($filters){
	$filters['sci_del_img'] = __('Delete standard captcha','pn');
	return $filters;
}

add_action('ajax_post_form_jsresult','ajax_post_form_jsresult_captcha_sci');
function ajax_post_form_jsresult_captcha_sci(){
?>
	if(res['cimg1']){
		$('.sci_img1').attr('src',res['cimg1']);
		$('.sci_img1').attr('data-id',res['cnum1']);
	}
	if(res['cimg2']){
		$('.sci_img2').attr('src',res['cimg2']);
		$('.sci_img2').attr('data-id',res['cnum2']);
	}
	if(res['cimg3']){
		$('.sci_img3').attr('src',res['cimg3']);
		$('.sci_img3').attr('data-id',res['cnum3']);
	}	
	if(res['cuslov']){
		$('.captcha_sci_title').html(res['cuslov']);
	}	
	$('.captcha_sci_img').removeClass('active');
	$('.captcha_sci_hidden').val('0');
<?php	
} 

add_action('siteplace_js','siteplace_js_captcha_sci');
function siteplace_js_captcha_sci(){
?>
jQuery(function($){ 
	$(document).on('click', '.captcha_sci_reload', function(){
		var thet = $(this);
		thet.addClass('act');
		var dataString='have=reload';
		$.ajax({
		type: "POST",
		url: "<?php echo get_ajax_link('sci_reload'); ?>",
		dataType: 'json',
		data: dataString,
		error: function(res,res2,res3){
			<?php do_action('pn_js_error_response', 'ajax'); ?>
		},		
		success: function(res)
		{
			if(res['cimg1']){
				$('.sci_img1').attr('src',res['cimg1']);
				$('.sci_img1').attr('data-id',res['cnum1']);
			}
			if(res['cimg2']){
				$('.sci_img2').attr('src',res['cimg2']);
				$('.sci_img2').attr('data-id',res['cnum2']);
			}
			if(res['cimg3']){
				$('.sci_img3').attr('src',res['cimg3']);
				$('.sci_img3').attr('data-id',res['cnum3']);
			}	
			if(res['cuslov']){
				$('.captcha_sci_title').html(res['cuslov']);
			}		
			$('.captcha_sci_img').removeClass('active');
			$('.captcha_sci_hidden').val('0');
			thet.removeClass('act');
		}
		});
		
		return false;
	});
	$(document).on('click', '.captcha_sci_img', function(){
		var thet = $(this);
		$('.captcha_sci_img').removeClass('active');
		thet.addClass('active');
		var hashed = thet.find('img').attr('data-id');
		$('.captcha_sci_hidden').val(hashed);
		return false;
	});	
});	
<?php	
}

function sci_reload(){
global $wpdb;
	$data = array();
	$data['cimg1'] = '';
	$data['cimg2'] = '';
	$data['cimg3'] = '';
	$data['cuslov'] = '';
	
	$sess_hash = get_session_id();
	$wpdb->query("DELETE FROM ".$wpdb->prefix."sitecaptcha_user WHERE sess_hash = '$sess_hash'");
	
	$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."sitecaptcha_images ORDER BY RAND()");
	if(isset($item->id)){
		$array = array();
		$array['sess_hash'] = $sess_hash;
		$array['createdate'] = current_time('mysql');
		$array['uslov'] = $item->uslov;
		$array['img1'] = $item->img1;
		$array['img2'] = $item->img2;
		$array['img3'] = $item->img3;
		$array['num1'] = wp_generate_password( 35 , false, false);
		$array['num2'] = wp_generate_password( 35 , false, false);
		$array['num3'] = wp_generate_password( 35 , false, false);
		if($item->variant == 1){
			$array['variant'] = $array['num1'];
		} elseif($item->variant == 2){
			$array['variant'] = $array['num2'];
		} elseif($item->variant == 3){
			$array['variant'] = $array['num3'];
		}
		$wpdb->insert($wpdb->prefix.'sitecaptcha_user', $array);
		
		$data['cimg1'] = $array['img1'];
		$data['cimg2'] = $array['img2'];
		$data['cimg3'] = $array['img3'];
		$data['cnum1'] = $array['num1'];
		$data['cnum2'] = $array['num2'];
		$data['cnum3'] = $array['num3'];
		$data['cuslov'] = ctv_ml($array['uslov']);		
	}
	
	return $data;
}

add_action('myaction_site_sci_reload', 'the_myaction_site_sci_reload');
function the_myaction_site_sci_reload(){
global $premiumbox;
	
	only_post();
	
	$log = array();
	$log['status'] = 'success';
	$log['status_text'] = '';
	$log['status_code'] = 0;
	
	$premiumbox->up_mode();
	
	$data = sci_reload();
	
	$log['cimg1'] = $data['cimg1'];
	$log['cimg2'] = $data['cimg2'];
	$log['cimg3'] = $data['cimg3'];
	$log['cuslov'] = $data['cuslov'];
	$log['cnum1'] = $data['cnum1'];
	$log['cnum2'] = $data['cnum2'];
	$log['cnum3'] = $data['cnum3'];
	
	echo json_encode($log);
	exit;
}

function get_captcha_sci_temp(){
global $wpdb;	
	$sess_hash = get_session_id();
	$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."sitecaptcha_user WHERE sess_hash='$sess_hash'");
	if(isset($data->id)){
		$temp = '
			<div class="captcha_sci_div">
				<div class="captcha_sci_title">
					'. pn_strip_input(ctv_ml($data->uslov)) .'
				</div>
				<div class="captcha_sci_body">
					<input type="hidden" class="captcha_sci_hidden" name="captcha_sci" value="" />
					
					<div class="captcha_sci_img captcha_sci_img1">
						<img src="'. pn_strip_input($data->img1) .'" class="sci_img1" data-id="'. pn_strip_input($data->num1) .'" alt="" />
					</div>
					<div class="captcha_sci_img captcha_sci_img2">
						<img src="'. pn_strip_input($data->img2) .'" class="sci_img2" data-id="'. pn_strip_input($data->num2) .'" alt="" />
					</div>
					<div class="captcha_sci_img captcha_sci_img3">
						<img src="'. pn_strip_input($data->img3) .'" class="sci_img3" data-id="'. pn_strip_input($data->num3) .'" alt="" />
					</div>					
						<div class="clear"></div>
				</div>
				<div class="captcha_sci_div_change">
					<a href="#" class="captcha_sci_reload">'. __('replace task','pn') .'</a>
				</div>
			</div>	
		';
		
		$temp = apply_filters('get_captcha_sci_temp', $temp, $data);
		return $temp;
	} else {
		return __('Captcha not found','pn');
	}
}
/* end функции каптчи */

/* подключение */
add_filter('get_form_filelds','get_form_filelds_captcha_sci', 10, 2);
function get_form_filelds_captcha_sci($items, $name){
global $premiumbox;	
	if($premiumbox->get_option('captcha',$name) == 1){
		$items['captcha_sci'] = array(
			'type' => 'captcha_sci',
		);
	}
	return $items;
}

add_filter('before_ajax_form_field','before_ajax_form_field_captcha_sci', 99, 2);
function before_ajax_form_field_captcha_sci($logs, $name){
global $premiumbox, $wpdb;	

	if($premiumbox->get_option('captcha',$name) == 1){
		
		$captcha_sci = trim(is_param_post('captcha_sci'));		
		$sess_hash = get_session_id();
				
		$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."sitecaptcha_user WHERE sess_hash='$sess_hash'");
		$data_new = sci_reload();
		$logs['cimg1'] = $data_new['cimg1'];
		$logs['cimg2'] = $data_new['cimg2'];
		$logs['cimg3'] = $data_new['cimg3'];
		$logs['cnum1'] = $data_new['cnum1'];
		$logs['cnum2'] = $data_new['cnum2'];
		$logs['cnum3'] = $data_new['cnum3'];			
		$logs['cuslov'] = $data_new['cuslov'];		
		if(isset($data->id)){		
			$variant = $data->variant;
			if($captcha_sci != $variant){
				$logs['status']	= 'error';
				$logs['status_code'] = '-3'; 
				$logs['status_text'] = __('Error! Wrong image chosen','pn');
				echo json_encode($logs);
				exit;
			} 
		} else {
			$logs['status']	= 'error';
			$logs['status_code'] = '-3';
			$logs['status_text'] = __('Error! Image is not selected','pn');
			echo json_encode($logs);
			exit;
		}				
		
	}
	
	return $logs;
}

add_filter('form_field_line','form_field_line_captcha_sci', 10, 3);
function form_field_line_captcha_sci($line, $filter, $data){
global $wpdb;	
	
	$type = trim(is_isset($data, 'type'));
	if($type == 'captcha_sci'){
		$line = get_captcha_sci_temp();	
	}
	return $line;	
}

add_filter('exchange_step1', 'exchange_form_captcha_sci');
function exchange_form_captcha_sci($line){
global $wpdb, $premiumbox;

	if($premiumbox->get_option('captcha','exchangeform') == 1){
		$line .= get_captcha_sci_temp();
	}
	
	return $line;	
}	
/* end подключение */