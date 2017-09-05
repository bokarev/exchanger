<?php
if( !defined( 'ABSPATH')){ exit(); }

global $premiumbox;

add_action('template_redirect','globalajax_template_redirect');
function globalajax_template_redirect(){
global $wpdb;
	add_mycookie('globalajax_time', current_time('timestamp'));
}

if($premiumbox->get_option('ga','ga_admin') == 1){
	add_action('admin_footer','globalajax_admin_footer');
	add_action('premium_action_globalajax_admin_check', 'pn_premium_action_globalajax_admin_check');
}
function globalajax_admin_footer(){
global $premiumbox;	
	$timer = intval($premiumbox->get_option('ga','admin_time'));
	if($timer < 1){ $timer = 10; }
	$globalajax_admin_timer = $timer * 1000;
	
	$link = urlencode($_SERVER['REQUEST_URI']);
	$page = is_param_get('page');
	
	$params = array();
	$params['link'] = $link;
	$params['page'] = $page;
	$params = apply_filters('globalajax_admin_data_request', $params , $link, $page);
	$http_params_arr = array();	
	foreach($params as $k_param => $v_param){
		$http_params_arr[] = $k_param . '='. $v_param;
	}
	$http_params = join('&', $http_params_arr);
?>	
<script type="text/javascript">
jQuery(function($){

var auto_load = 1;
function globalajax_timer(){
	if(auto_load == 1){
		auto_load = 0;
		
		var param = '<?php echo $http_params; ?>';
		$('.globalajax_ind').addClass('active');
		$.ajax({
			type: "POST",
			url: "<?php pn_the_link_ajax('globalajax_admin_check');?>",
			dataType: 'json',
			data: param,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{		
				if(res['status'] == 'success'){
					auto_load = 1;
					<?php do_action('globalajax_admin_data_jsresult', $link, $page); ?>
				} 
				$('.globalajax_ind').removeClass('active');
			}
		});	

	}
}	
	setInterval(globalajax_timer, <?php echo $globalajax_admin_timer; ?>);
	globalajax_timer();
});	
</script>
<?php	
}

function pn_premium_action_globalajax_admin_check(){
	only_post();
	
	$log = array();
	$log['status'] = 'success';
	$log['status_code'] = 0;
	$log['status_text'] = '';
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	$log = apply_filters('globalajax_admin_data', $log, urldecode(is_param_post('link')), is_param_post('page'));
	
	echo json_encode($log);
	exit;
} 

if($premiumbox->get_option('ga','ga_site') == 1){
	add_action('wp_footer','globalajax_wp_footer');
	add_action('myaction_site_globalajax_wp_check', 'def_myaction_site_globalajax_wp_check');
}
function globalajax_wp_footer(){
global $premiumbox;		
	$timer = intval($premiumbox->get_option('ga','site_time'));
	if($timer < 1){ $timer = 10; }
	$globalajax_wp_timer = $timer * 1000;	
	
	$link = urlencode($_SERVER['REQUEST_URI']);
	
	$params = array();
	$params['link'] = $link;
	$params = apply_filters('globalajax_wp_data_request', $params , $link);
	$http_params_arr = array();	
	foreach($params as $k_param => $v_param){
		$http_params_arr[] = $k_param . '='. $v_param;
	}
	$http_params = join('&', $http_params_arr);
?>	
<script type="text/javascript">
jQuery(function($){

var auto_load = 1;
function globalajax_timer(){

	if(auto_load == 1){
		auto_load = 0;
		
		var param = '<?php echo $http_params; ?>';
		$('.globalajax_ind').addClass('active');
		$.ajax({
			type: "POST",
			url: "<?php echo get_ajax_link('globalajax_wp_check');?>",
			dataType: 'json',
			data: param,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{		
				if(res['status'] == 'success'){
					auto_load = 1;						
					<?php do_action('globalajax_wp_data_jsresult',$link); ?>
				}	
				$('.globalajax_ind').removeClass('active');
			}
		});
	}

}	
	setInterval(globalajax_timer, <?php echo $globalajax_wp_timer; ?>);
	globalajax_timer();
});	
</script>
<?php	
}

function def_myaction_site_globalajax_wp_check(){
global $premiumbox;
	
	only_post();
	
	$log = array();
	$log['status'] = 'success';
	$log['status_code'] = 0;
	$log['status_text'] = '';
	 
	$premiumbox->up_mode();	
	
	$log = apply_filters('globalajax_wp_data', $log, urldecode(is_param_post('link')));
	
	echo json_encode($log);	
	exit;
} 