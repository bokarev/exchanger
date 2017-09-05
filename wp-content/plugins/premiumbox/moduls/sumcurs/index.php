<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Курс зависящий от суммы обмена[:ru_RU][en_US:]Exchange rate dependent on amount of exchange[:en_US]
description: [ru_RU:]Курс зависящий от суммы обмена[:ru_RU][en_US:]Exchange rate dependent on amount of exchange[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_sumcurs');
function bd_pn_moduls_active_sumcurs(){
global $wpdb;	
	
	$table_name= $wpdb->prefix ."naps_sumcurs";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`naps_id` bigint(20) NOT NULL default '0',
		`sum_val` varchar(50) NOT NULL default '0',
		`curs1` varchar(50) NOT NULL default '0',
		`curs2` varchar(50) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);
	
}
/* end BD */

add_action('pn_naps_delete', 'pn_naps_delete_sumcurs');
function pn_naps_delete_sumcurs($item_id){
global $wpdb;	
	$wpdb->query("DELETE FROM ".$wpdb->prefix."naps_sumcurs WHERE naps_id = '$item_id'");
}

add_action('pn_naps_copy', 'pn_naps_copy_sumcurs', 1, 2);
function pn_naps_copy_sumcurs($last_id, $new_id){
global $wpdb;

	$naps_meta = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps_sumcurs WHERE naps_id='$last_id'"); 
	foreach($naps_meta as $nap){
		$arr = array();
		$arr['naps_id'] = $data_id;
		$arr['sum_val'] = is_my_money($nap->sum_val);
		$arr['curs1'] = is_my_money($nap->curs1);
		$arr['curs2'] = is_my_money($nap->curs2);
		$wpdb->insert($wpdb->prefix.'naps_sumcurs', $arr);
	}
	
}

/* sum curs */
function get_napscurs_html($data_id){
global $wpdb;	
	
	$temp = '';
	
	$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."naps_sumcurs WHERE naps_id='$data_id' ORDER BY (sum_val -0.0) ASC"); 
	foreach($items as $item){

		$temp .= '
		<div class="napscurs_line js_napscurs_line" data-id="'. $item->id .'">
			<div class="napscurs_item">
				<div class="napscurs_title">
					'. __('Amount','pn') .'
				</div>
				<div class="napscurs_input">
					<input type="text" name="" style="width: 100px;" class="rate_sum0" value="'. is_my_money($item->sum_val) .'" />
				</div>
			</div>
			<div class="napscurs_item">
				<div class="napscurs_title">
					'. __('Send','pn') .'
				</div>
				<div class="napscurs_input">
					<input type="text" name="" style="width: 100px;" class="rate_sum1" value="'. is_my_money($item->curs1) .'" />
				</div>
			</div>
			<div class="napscurs_item">
				<div class="napscurs_title">
					'. __('Receive','pn') .'
				</div>
				<div class="napscurs_input">
					<input type="text" name="" style="width: 100px;" class="rate_sum2" value="'. is_my_money($item->curs2) .'" />
				</div>
			</div>		
			<div class="napscurs_add js_napscurs_add">'. __('Save','pn') .'</div>
			<div class="napscurs_del js_napscurs_del">'. __('Delete','pn') .'</div>
				<div class="premium_clear"></div>
		</div>';	
		
	}
	
	$temp .= '
	<div class="napscurs_line js_napscurs_line" data-id="0">
		<div class="napscurs_item">
			<div class="napscurs_title">
				'. __('Amount','pn') .'
			</div>
			<div class="napscurs_input">
				<input type="text" name="" style="width: 100px;" class="rate_sum0" value="" />
			</div>
		</div>
		<div class="napscurs_item">
			<div class="napscurs_title">
				'. __('Send','pn') .'
			</div>
			<div class="napscurs_input">
				<input type="text" name="" style="width: 100px;" class="rate_sum1" value="" />
			</div>
		</div>
		<div class="napscurs_item">
			<div class="napscurs_title">
				'. __('Receive','pn') .'
			</div>
			<div class="napscurs_input">
				<input type="text" name="" style="width: 100px;" class="rate_sum2" value="" />
			</div>
		</div>		
		<div class="napscurs_add js_napscurs_add">'. __('Add new','pn') .'</div>
			<div class="premium_clear"></div>
	</div>';
	
	return $temp;
}
/* end sum curs */

add_action('premium_action_sumcurs_del', 'pn_premium_action_sumcurs_del');
function pn_premium_action_sumcurs_del(){
global $wpdb;

	only_post();
	
	$log = array();
	$log['status'] = 'success';	
	
	if(current_user_can('administrator') or current_user_can('pn_naps')){
		
		$data_id = intval(is_param_post('data_id'));
		$item_id = intval(is_param_post('item_id'));		
		$wpdb->query("DELETE FROM ".$wpdb->prefix."naps_sumcurs WHERE id='$item_id' AND naps_id='$data_id'");

		$log['html'] = get_napscurs_html($data_id);
	}  		

	echo json_encode($log);	
	exit;
}

add_action('premium_action_sumcurs_add', 'pn_premium_action_sumcurs_add');
function pn_premium_action_sumcurs_add(){
global $wpdb;

	only_post();
	
	$log = array();
	$log['status'] = 'success';	
	
	if(current_user_can('administrator') or current_user_can('pn_naps')){
		
		$data_id = intval(is_param_post('data_id'));
		$item_id = intval(is_param_post('item_id'));
		$sum1 = is_my_money(is_param_post('sum1'));
		$sum2 = is_my_money(is_param_post('sum2'));
		$sum3 = is_my_money(is_param_post('sum3'));
		if($data_id > 0){
			$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."naps WHERE id='$data_id'");
			if(isset($data->id)){
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."naps_sumcurs WHERE id='$item_id' AND naps_id='$data_id'");
				
				$array = array();
				$array['naps_id'] = $data_id;
				$array['sum_val'] = $sum1;
				$array['curs1'] = $sum2;
				$array['curs2'] = $sum3;
				
				if(isset($item->id)){
					$wpdb->update($wpdb->prefix.'naps_sumcurs', $array, array('id'=>$item->id));
				} else {
					$wpdb->insert($wpdb->prefix.'naps_sumcurs', $array);
				}
				
			}
		}	
			$log['html'] = get_napscurs_html($data_id);
	}  		
	
	echo json_encode($log);
	exit;
}

add_action('tab_naps_tab2', 'tab_naps_tab2_sumcurs');
function tab_naps_tab2_sumcurs($data){	
	if(isset($data->id)){ 
		$data_id = $data->id;
	?>
		<tr>
			<th><?php _e('Rate is depends on exchange amount','pn'); ?></th>
			<td colspan="2">
				<div id="napscurs_html" data-id="<?php echo $data_id; ?>">
					<?php echo get_napscurs_html($data_id); ?>
				</div>
			</td>
		</tr>
		<tr>
			<td></td>
			<td colspan="2">
				<?php pn_help(__('More info','pn'), __('Set a the lower amount of exchange in field "Amount". Then set a currency rate for Giving and Receiving. If the user wants to send you the specified amount then the rate will be the same you previously set.','pn')); ?>
			</td>
		</tr>

<script type="text/javascript">
$(function(){
	
	$(document).on('click', '.js_napscurs_add', function(){ 
		var data_id = parseInt($('#napscurs_html').attr('data-id'));
		var par = $(this).parents('.js_napscurs_line');
		var item_id = parseInt(par.attr('data-id'));
		var sum1 = parseFloat(par.find('.rate_sum0').val());
		var sum2 = parseFloat(par.find('.rate_sum1').val());
		var sum3 = parseFloat(par.find('.rate_sum2').val());
		
		$('#napscurs_html').find('input').attr('disabled',true);
		$('#napscurs_html').find('.js_napscurs_add, .js_napscurs_del').addClass('active');
		
		var param = 'data_id='+data_id+'&item_id='+item_id+'&sum1='+sum1+'&sum2='+sum2+'&sum3='+sum3;	
		$.ajax({
			type: "POST",
			url: "<?php pn_the_link_post('sumcurs_add');?>",
			dataType: 'json',
			data: param,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{		
				if(res['html']){
					$('#napscurs_html').html(res['html']);
				} 
			}
		});		
		
		return false;
	});

	$(document).on('click', '.js_napscurs_del', function(){
		var data_id = parseInt($('#napscurs_html').attr('data-id'));
		var par = $(this).parents('.js_napscurs_line');
		var item_id = parseInt(par.attr('data-id'));
		
		$('#napscurs_html').find('input').attr('disabled',true);
		$('#napscurs_html').find('.js_napscurs_add, .js_napscurs_del').addClass('active');
		
		var param = 'data_id='+data_id+'&item_id='+item_id;	
		$.ajax({
			type: "POST",
			url: "<?php pn_the_link_post('sumcurs_del');?>",
			dataType: 'json',
			data: param,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{		
				if(res['html']){
					$('#napscurs_html').html(res['html']);
				} 
			}
		});		
		
		return false;
	});		

});
</script>		
	<?php }
	
}	

/* filter */
add_filter('set_calc_data', 'set_calc_data_sumcurs', 0, 9);
function set_calc_data_sumcurs($cdata, $vd1, $vd2, $naps, $user_id, $post_sum, $check1, $check2, $dej){
global $wpdb;
	
	$naps_id = $naps->id;
	$post_sum = is_my_money($post_sum);
	if($dej == 1){
		$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."naps_sumcurs WHERE naps_id='$naps_id'");
		if($cc > 0){
			$cdata['dis1c'] = 1;
			$cdata['dis2'] = 1;	
			$cdata['dis2c'] = 1;
		}
		$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."naps_sumcurs WHERE naps_id='$naps_id' AND ('$post_sum' -0.0) >= sum_val ORDER BY (sum_val -0.0) DESC");
		if(isset($data->id)){
			$cdata['curs1'] = $data->curs1;
			$cdata['curs2'] = $data->curs2;
		}		
	}
	
	return $cdata;
}	