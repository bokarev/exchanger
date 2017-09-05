<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Массовая корректировка резерва[:ru_RU][en_US:]Reserve adjustment (group)[:en_US]
description: [ru_RU:]Массовая корректировка резерва[:ru_RU][en_US:]Reserve adjustment (group)[:en_US]
version: 1.0
category: [ru_RU:]Валюты[:ru_RU][en_US:]Currency[:en_US]
cat: currency
*/

add_action('admin_menu', 'pn_adminpage_corrreserv');
function pn_adminpage_corrreserv(){
global $premiumbox;
	if(current_user_can('administrator')){
		add_submenu_page("pn_reserv", __('Reserve adjustment (group)','pn'), __('Reserve adjustment (group)','pn'), 'read', "pn_mass_reserv", array($premiumbox, 'admin_temp'));
	}
}

add_action('pn_adminpage_title_pn_mass_reserv', 'def_adminpage_title_pn_mass_reserv');
function def_adminpage_title_pn_mass_reserv(){
	_e('Reserve adjustment (group)','pn');
}

add_action('pn_adminpage_content_pn_mass_reserv','def_pn_admin_content_pn_mass_reserv');
function def_pn_admin_content_pn_mass_reserv(){
global $wpdb;
?>
<div class="premium_body">
    <form method="post" action="<?php pn_the_link_post(); ?>">
    <table class="premium_standart_table">		
        <tr>
		    <th><?php _e('Currency name','pn'); ?></th>
			<td>
				<div class="premium_wrap_standart">
					<?php
					$valuts = apply_filters('list_valuts_manage', array(), __('Check all/Uncheck all','pn'));
					foreach($valuts as $k => $v){ 
						$cl = '';
						$style = '';
						if($k == 0){ $cl = 'check_all'; $style = 'font-weight: 500;'; }
					?>
						<div style="<?php echo $style; ?>"><label><input type="checkbox" name="valut_ids[]" class="check_once <?php echo $cl; ?>" value="<?php echo $k; ?>" /> <?php echo $v; ?></label></div>
					<?php
					}
					?>
				</div>
			</td>			
		</tr>
        <tr>
		    <th><?php _e('Amount','pn'); ?></th>
			<td>
				<div class="premium_wrap_standart">
					<input type="text" name="trans_sum" value="" />
				</div>
			</td>			
		</tr>
        <tr>
		    <th><?php _e('Comment','pn'); ?></th>
			<td>
				<div class="premium_wrap_standart">
					<input type="text" name="trans_title" value="" />
				</div>
			</td>			
		</tr>		
        <tr>
		    <th></th>
			<td>
				<div class="premium_wrap_standart">
					<input type="submit" name="" class="button" value="<?php _e('Save','pn'); ?>" />
				</div>
			</td>
		</tr>		
    </table>
	</form>	
</div>
<script type="text/javascript">
jQuery(function($){
	$('.check_all').change(function(){
		if($(this).prop('checked')){
			$('.check_once').prop('checked',true);
		} else {
			$('.check_once').prop('checked',false);
		}
	});	
});
</script>
<?php	
}

add_action('premium_action_pn_mass_reserv','def_premium_action_pn_mass_reserv');
function def_premium_action_pn_mass_reserv(){
global $wpdb, $user_ID;	

	only_post();
	pn_only_caps(array('administrator','pn_reserv'));

	$trans_title = pn_strip_input(is_param_post('trans_title'));
	$trans_sum = is_my_money(is_param_post('trans_sum'));
	if($trans_sum != 0){
		$valut_ids = is_param_post('valut_ids');
		if(is_array($valut_ids) and count($valut_ids) > 0){
			foreach($valut_ids as $v_id){
				$v_id = intval($v_id);
				if($v_id){
					$array = array();
					$array['trans_title'] = $trans_title;
					$array['trans_summ'] = $trans_sum;
					$array['valut_id'] = 0;
					$array['vtype_id'] = 0;
					$array['vtype_title'] = '';
					$valut_data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE id='$v_id'");
					if(isset($valut_data->id)){
						$array['valut_id'] = $valut_data->id;
						$array['vtype_id'] = $valut_data->vtype_id;
						$array['vtype_title'] = is_site_value($valut_data->vtype_title);	
					}	
					$array['trans_create'] = current_time('mysql');
					$array['user_creator'] = intval($user_ID);
					$wpdb->insert($wpdb->prefix.'trans_reserv', $array);
					$data_id = $wpdb->insert_id;	
					update_valut_reserv($array['valut_id']);
					do_action('pn_reserv_add', $data_id, $array);
				}
			}
		}
	}

	$url = admin_url('admin.php?page=pn_reserv&reply=true');
	wp_redirect($url);
	exit;
}	