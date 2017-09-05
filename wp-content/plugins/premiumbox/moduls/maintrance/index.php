<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Режим тех. обслуживания[:ru_RU][en_US:]Maintenance mode[:en_US]
description: [ru_RU:]Режим тех. обслуживания[:ru_RU][en_US:]Maintenance mode[:en_US]
version: 1.0
category: [ru_RU:]Настройки[:ru_RU][en_US:]Settings[:en_US]
cat: sett
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_maintrance');
function bd_pn_moduls_active_maintrance(){
global $wpdb;
	
	$table_name = $wpdb->prefix ."maintrance";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`the_title` tinytext NOT NULL,
		`pages_law` longtext NOT NULL,
		`operator_status` varchar(150) NOT NULL default '-1',
		`show_text` longtext NOT NULL,
		`for_whom` int(1) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);
	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."maintrance LIKE 'for_whom'");
	if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."maintrance ADD `for_whom` int(1) NOT NULL default '0'");
	}
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."maintrance LIKE 'pages_law'");
	if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."maintrance ADD `pages_law` longtext NOT NULL");
	}	
}

add_action('pn_bd_activated', 'bd_pn_moduls_migrate_maintrance');
function bd_pn_moduls_migrate_maintrance(){
global $wpdb;
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."maintrance LIKE 'for_whom'");
	if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."maintrance ADD `for_whom` int(1) NOT NULL default '0'");
	}
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."maintrance LIKE 'pages_law'");
	if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."maintrance ADD `pages_law` longtext NOT NULL");
	}		
}
/* end BD */

add_filter('pn_caps','maintrance_pn_caps');
function maintrance_pn_caps($pn_caps){
	$pn_caps['pn_maintrance'] = __('Use maintenance','pn');
	return $pn_caps;
}

/* 
Подключаем к меню
*/
add_action('admin_menu', 'pn_adminpage_maintrance');
function pn_adminpage_maintrance(){
global $premiumbox;
	if(current_user_can('administrator') or current_user_can('pn_maintrance')){
		add_menu_page( __('Maintenance mode','pn'), __('Maintenance mode','pn'), 'read', "pn_maintrance", array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('operator'));
		add_submenu_page( "pn_maintrance", __('Settings','pn'), __('Settings','pn'), 'read', "pn_maintrance", array($premiumbox, 'admin_temp'));
		$hook = add_submenu_page( "pn_maintrance", __('Maintenance mode','pn'), __('Maintenance mode','pn'), 'read', "pn_maintrance_list", array($premiumbox, 'admin_temp'));
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page( "pn_maintrance", __('Add mode','pn'), __('Add mode','pn'), 'read', "pn_maintrance_add", array($premiumbox, 'admin_temp'));
	}
}

add_action('pn_adminpage_js_dashboard','maintrance_adminpage_js_dashboard');
function maintrance_adminpage_js_dashboard(){
global $premiumbox;	
	if(intval($premiumbox->get_option('tech','maintrance')) == 0){
?>
    $('#maintrance').on('change',function(){ 
		var id = $(this).val();
		var dataString='id='+id;
		$('#maintrance').prop('disabled',true);
        $.ajax({
			type: "POST",
			url: "<?php pn_the_link_post('maintrance_change'); ?>",
			data: dataString,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{
				$('#maintrance').prop('disabled',false);				
			}
        });
        return false;
    });
<?php	
	}
}

add_action('premium_action_maintrance_change', 'pn_premium_action_maintrance_change');
function pn_premium_action_maintrance_change(){
global $premiumbox;		
	only_post();
	if(current_user_can('administrator') or current_user_can('pn_maintrance')){	
		$id = intval(is_param_post('id'));
		$premiumbox->update_option('tech','manualy',$id);		
	}	
} 

add_action('wp_dashboard_setup', 'maintrance_wp_dashboard_setup' );
function maintrance_wp_dashboard_setup() {
global $premiumbox;		
	if(current_user_can('administrator') or current_user_can('pn_maintrance')){
		if(intval($premiumbox->get_option('tech','maintrance')) == 0){
			wp_add_dashboard_widget('maintrance_dashboard_widget', __('Maintenance mode','pn'), 'maintrance_dashboard_widget_function');
		}
	}
}
function maintrance_dashboard_widget_function(){
global $wpdb, $premiumbox;
 	$status = intval($premiumbox->get_option('tech','manualy'));
	$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."maintrance ORDER BY id DESC");
?>
<select id="maintrance" name="maintrance" autocomplete="off">
	<option value="0" <?php selected($status,0); ?>><?php _e('No','pn'); ?></option>
	<?php foreach($items as $item){ ?>
		<option value="<?php echo $item->id; ?>" <?php selected($status, $item->id); ?>><?php echo pn_strip_input(ctv_ml($item->the_title)); ?></option>
	<?php } ?>
</select>
<?php
}

add_action('init','get_maintrance', 0);
function get_maintrance(){
global $wpdb, $pn_maintrance, $premiumbox;

	$pn_maintrance = '';
	if(intval($premiumbox->get_option('tech','maintrance')) == 0){
		$id_mode = intval($premiumbox->get_option('tech','manualy'));
		if($id_mode > 0){
			$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."maintrance WHERE id='$id_mode'");
		}
	} else {	
		$operator = intval(get_operator_status());
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."maintrance WHERE operator_status='$operator'");
	}
		if(isset($data->id)){
			$pn_maintrance = $data;
		}	
}

add_filter('pn_exchanges_output', 'maintrance_exchanges_output', 0, 2);
function maintrance_exchanges_output($show_data, $place){
global $pn_maintrance;

	if(isset($pn_maintrance->id) and isset($show_data['mode']) and $show_data['mode'] == 1){
		$text = pn_strip_input(ctv_ml($pn_maintrance->show_text));
		$now = 0;
		
		if($pn_maintrance->for_whom == 0 or !current_user_can('read')){
			$pages_law = @unserialize($pn_maintrance->pages_law);
			$now = intval(is_isset($pages_law, $place));
		}
		
		if($now > 0){
			$show_data['text'] = $text;
		}
		if($now == 2){
			$show_data['mode'] = 0;
		}
	}
	
	return $show_data;
}

add_filter('before_ajax_bidsform', 'maintrance_before_ajax_bidsform');
add_filter('before_ajax_createbids', 'maintrance_before_ajax_bidsform');
function maintrance_before_ajax_bidsform($log){
global $pn_maintrance;	
	
	if(isset($pn_maintrance->id)){
		if($pn_maintrance->for_whom == 0 or !current_user_can('read')){
			$text = pn_strip_input(ctv_ml($pn_maintrance->show_text));
			$log['status'] = 'error';
			$log['status_code'] = 1; 
			$log['status_text'] = $text;
			echo json_encode($log);
			exit;	
		}
	}
	
	return $log; 
}

global $premiumbox;
$premiumbox->file_include($path.'/settings');
$premiumbox->file_include($path.'/list');
$premiumbox->file_include($path.'/add');