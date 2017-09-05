<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Горячие клавиши[:ru_RU][en_US:]Hotkeys[:en_US]
description: [ru_RU:]Горячие клавиши для смены статуса заявки[:ru_RU][en_US:]Hotkeys for changing request status[:en_US]
version: 1.0
category: [ru_RU:]Заявки[:ru_RU][en_US:]Orders[:en_US]
cat: req
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_action('admin_menu', 'pn_adminpage_hotkey', 100);
function pn_adminpage_hotkey(){
global $premiumbox;		
	if(current_user_can('administrator') or current_user_can('pn_bids')){
		add_submenu_page("pn_bids", __('Hotkeys','pn'), __('Hotkeys','pn'), 'read', "pn_hotkey_bids", array($premiumbox, 'admin_temp'));
	}
	
}

add_action('pn_adminpage_title_pn_hotkey_bids', 'pn_admin_title_pn_hotkey_bids');
function pn_admin_title_pn_hotkey_bids(){
	_e('Settings','pn');
}

add_action('pn_adminpage_content_pn_hotkey_bids','def_pn_admin_content_pn_hotkey_bids');
function def_pn_admin_content_pn_hotkey_bids(){
global $wpdb;

	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);
	
	$uchk = get_user_meta($user_id, 'user_change_hotkey', true);
	if(!is_array($uchk)){ $uchk = array(); }
	
	
	$statused = get_statusbids_for_admin();
	
	$arr = array(
		'0' => '---',
		'81' => 'Shift + q',
		'87' => 'Shift + w', 
		'69' => 'Shift + e',
		'82' => 'Shift + r',
		'84' => 'Shift + t',
		'89' => 'Shift + y',
		'85' => 'Shift + u',
		'73' => 'Shift + i',
		'79' => 'Shift + o',
		'80' => 'Shift + p',
		'65' => 'Shift + a',
		'83' => 'Shift + s',
		'68' => 'Shift + d',
		'70' => 'Shift + f',
		'71' => 'Shift + g',
		'72' => 'Shift + h',
		'74' => 'Shift + j',
		'75' => 'Shift + k',
		'76' => 'Shift + l',
		'90' => 'Shift + z',
		'88' => 'Shift + x',
		'67' => 'Shift + c',
		'86' => 'Shift + v',
		'66' => 'Shift + b',
		'78' => 'Shift + n',
		'77' => 'Shift + m',		
	);
	
	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	
	foreach($statused as $key => $data){
		$title = $data['title'];
		if($key != 'my'){
						
			$def = 0;
			if(isset($uchk[$key]['h_key'])){
				$def = intval($uchk[$key]['h_key']);
			}
			$options[] = array(
				'view' => 'select',
				'title' => __('Action','pn').' "'.$title.'"',
				'options' => $arr,
				'default' => $def,
				'name' => $key,
			);	
		}
	}	
	
	$options['help'] = array(
		'view' => 'help',
		'title' => __('More info','pn'),
		'default' => __('Set hotkeys to change status of selected orders','pn'),
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('', $options);				
}

add_action('premium_action_pn_hotkey_bids','def_premium_action_pn_hotkey_bids');
function def_premium_action_pn_hotkey_bids(){
global $wpdb;

	only_post();
	pn_only_caps(array('administrator','pn_bids'));
	
	$statused = get_statusbids_for_admin();
	
	$hotkeys = array(
		'0' => '---',
		'81' => 'Shift + q',
		'87' => 'Shift + w', 
		'69' => 'Shift + e',
		'82' => 'Shift + r',
		'84' => 'Shift + t',
		'89' => 'Shift + y',
		'85' => 'Shift + u',
		'73' => 'Shift + i',
		'79' => 'Shift + o',
		'80' => 'Shift + p',
		'65' => 'Shift + a',
		'83' => 'Shift + s',
		'68' => 'Shift + d',
		'70' => 'Shift + f',
		'71' => 'Shift + g',
		'72' => 'Shift + h',
		'74' => 'Shift + j',
		'75' => 'Shift + k',
		'76' => 'Shift + l',
		'90' => 'Shift + z',
		'88' => 'Shift + x',
		'67' => 'Shift + c',
		'86' => 'Shift + v',
		'66' => 'Shift + b',
		'78' => 'Shift + n',
		'77' => 'Shift + m',		
	);	
			
	$true_key = array();
	$uchk = array();
	foreach($statused as $key => $data){
		$title = $data['title'];
		if($key != 'my'){
			$v_key = intval(is_param_post($key));
			$h_title = is_isset($hotkeys,$v_key);
			if(!in_array($v_key, $true_key) and $v_key){
				$true_key[] = $v_key;
				$uchk[$key] = array(
					'action' => $key,
					'action_title' => $title,
					'h_key' => $v_key,
					'h_title' => $h_title,						
				);
			}
		}	
	}
			
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);			
	update_user_meta( $user_id, 'user_change_hotkey', $uchk) or add_user_meta($user_id, 'user_change_hotkey', $uchk, true);

	$url = admin_url('admin.php?page=pn_hotkey_bids&reply=true');
	wp_redirect($url);
	exit;
}	

add_action('pn_adminpage_content_pn_bids','change_bids_filter_after_hotkey', 11);
function change_bids_filter_after_hotkey(){
	
 	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);
	$user_hotkeys = get_user_meta($user_id, 'user_change_hotkey', true);
	if(!is_array($user_hotkeys)){ $user_hotkeys = array(); }
	
	if(count($user_hotkeys) > 0){
?>
<div class="hotkey_div">
	<div class="hotkey_div_title"><?php _e('Hotkeys','pn'); ?></div>

	<div class="hotkey_table">
		<table>
			<tr>
				<th><?php _e('Action','pn'); ?></th>
				<th><?php _e('Keyboard shortcut','pn'); ?></th>
			</tr>
			<?php foreach($user_hotkeys as $uh_data){
				$hdn_kn = explode('+',is_isset($uh_data,'h_title'));
				?>
				<tr class="hotkey_tr hotkey_<?php echo is_isset($uh_data,'h_key'); ?>">
					<td><?php echo is_isset($uh_data,'action_title'); ?></td>
					<td><span class="hdl_kn"><?php echo trim(is_isset($hdn_kn,'0')); ?></span> + <span class="hdl_kn"><?php echo trim(is_isset($hdn_kn,'1')); ?></span></td>
				</tr>		
				<?php
			} ?>
		</table>
	</div>
</div>
	<?php } 
} 

add_action('change_bids_filter_js', 'change_bids_filter_js_hotkey');
function change_bids_filter_js_hotkey(){
 	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);
	$user_hotkeys = get_user_meta($user_id, 'user_change_hotkey', true);
	if(!is_array($user_hotkeys)){ $user_hotkeys = array(); }
	if(count($user_hotkeys) > 0){	
?>	
	$(document).on('keydown', function( e ){
		if(!$("textarea").is(":focus")){	
			<?php 
			foreach($user_hotkeys as $uh_data){
				$h_action = trim(is_isset($uh_data,'action'));
				$h_key = intval(is_isset($uh_data,'h_key'));
			?>
				if(e.shiftKey && e.which == <?php echo $h_key; ?>){
					
					$('.hotkey_'+<?php echo $h_key; ?>).addClass('active');
					$('.sel_action').val('<?php echo $h_action; ?>');
					go_ajax_action();
					
					return false;
				}			
			<?php
			}
			?>	
		}
	});	
<?php
	}
}