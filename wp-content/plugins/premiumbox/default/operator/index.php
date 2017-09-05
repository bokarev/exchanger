<?php
if( !defined( 'ABSPATH')){ exit(); }

/* статусы оператора */
add_filter('status_operator','def_status_operator');
function def_status_operator($status_operator){
	
	$status_operator[0] = __('offline','pn');
	$status_operator[1] = __('online','pn');
	
	return $status_operator;
}

/* 
Подключаем к меню
*/
add_action('admin_menu', 'pn_adminpage_operator');
function pn_adminpage_operator(){
global $premiumbox;
	
	add_menu_page( __('Work status','pn'), __('Work status','pn'), 'administrator', "pn_operator", array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('operator'));
	add_submenu_page( "pn_operator", __('Settings','pn'), __('Settings','pn'), 'administrator', "pn_operator", array($premiumbox, 'admin_temp'));
	$hook = add_submenu_page( "pn_operator", __('Operator schedule','pn'), __('Operator schedule','pn'), 'administrator', "pn_operator_schedule", array($premiumbox, 'admin_temp'));
	add_action( "load-$hook", 'pn_trev_hook' );
	add_submenu_page( "pn_operator", __('Add schedule','pn'), __('Add schedule','pn'), 'administrator', "pn_operator_add_schedule", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_js_dashboard','statuswork_adminpage_js_dashboard');
function statuswork_adminpage_js_dashboard(){
global $premiumbox;	
	if(intval($premiumbox->get_option('operator_type')) == 0){
?>
    $('#statuswork').on('change', function(){ 
		var id = $(this).val();
		var dataString='id='+id;
		$('#statuswork').prop('disabled',true);
        $.ajax({
			type: "POST",
			url: "<?php pn_the_link_post('statuswork_change'); ?>",
			data: dataString,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{
				$('#statuswork').prop('disabled',false);				
			}
        });
	
        return false;
    });
<?php	
	}
}

add_action('premium_action_statuswork_change', 'pn_premium_action_statuswork_change');
function pn_premium_action_statuswork_change(){
global $premiumbox;		
	only_post();
	if(current_user_can('read') and intval($premiumbox->get_option('operator_type')) == 0){	
		$id = intval(is_param_post('id'));
		$premiumbox->update_option('operator','',$id);	
	}
} 

add_action('wp_dashboard_setup', 'statuswork_wp_dashboard_setup' );
function statuswork_wp_dashboard_setup() {
global $premiumbox;	
	if(intval($premiumbox->get_option('operator_type')) == 0){
		wp_add_dashboard_widget('statuswork_dashboard_widget', __('Work status','pn'), 'statuswork_dashboard_widget_function');
	}
}
function statuswork_dashboard_widget_function(){
global $premiumbox;		
	$status_operator = apply_filters('status_operator',array());
	$operator = $premiumbox->get_option('operator');
?>
<select id="statuswork" name="statuswork" autocomplete="off">
	<?php 
	if(is_array($status_operator)){
		foreach($status_operator as $key => $title){ 
		?>
			<option value="<?php echo $key; ?>" <?php selected($operator,$key); ?>><?php echo $title; ?></option>
		<?php 
		}
	}
	?>
</select>
<?php
} 

add_filter('globalajax_admin_data','operator_globalajax_data');
add_filter('globalajax_wp_data','operator_globalajax_data');
function operator_globalajax_data($log){
global $premiumbox;		
	if(intval($premiumbox->get_option('operator_type')) == 1){
		if(current_user_can('administrator') or current_user_can('pn_bids')){
			update_option('operator_time', current_time('timestamp'));
		}
	}
	
	return $log;
}

add_action('wp_footer','statuswork_wp_footer');
function statuswork_wp_footer(){
global $premiumbox;		
	if(intval($premiumbox->get_option('statuswork','show_button')) == 1){
		
		$time = current_time('timestamp');

		$operator = get_operator_status();
		$status = 'status_op'.$operator;

		$text = pn_strip_text(ctv_ml($premiumbox->get_option('statuswork','text'.$operator)));
		$link = pn_strip_text(ctv_ml($premiumbox->get_option('statuswork','link'.$operator)));

		$style = 'toleft';
		if($premiumbox->get_option('statuswork','location') == 1){
			$style = 'toright';
		}
		
		$date_format = get_option('date_format');
		$time_format = get_option('time_format');
		$date = date("{$date_format}, {$time_format}",$time);
		$date = apply_filters('statuswork_now_date',$date,$time);
?>
<?php if($link){ ?>
	<a href="<?php echo $link; ?>" class="statuswork_div <?php echo $status; ?> <?php echo $style; ?>">
<?php } else { ?>
	<div class="statuswork_div <?php echo $status; ?> <?php echo $style; ?>">
<?php } ?>
	<div class="statuswork_div_ins">
		<div class="statuswork">
			<div class="statuswork_ins">
				<div class="statuswork_title"><span><?php echo $text; ?></span></div>
				<div class="statuswork_date"><span><?php echo $date; ?></span></div>
			</div>	
		</div>
	</div>
<?php if($link){ ?>
	</a>
<?php } else { ?>
	</div>
<?php } ?>
<?php
	
	}
} 

global $premiumbox;	
$premiumbox->include_patch(__FILE__, 'settings');
$premiumbox->include_patch(__FILE__, 'list');
$premiumbox->include_patch(__FILE__, 'add');