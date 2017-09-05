<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Финансовая статистика (от сумм обмена)[:ru_RU][en_US:]Financial statistics (on amount of exchange)[:en_US]
description: [ru_RU:]Финансовая статистика от сумм обмена[:ru_RU][en_US:]Financial statistics on amount of exchange[:en_US]
version: 1.0
category: [ru_RU:]Заявки[:ru_RU][en_US:]Orders[:en_US]
cat: req
*/

add_action('admin_menu', 'pn_adminpage_finstats');
function pn_adminpage_finstats(){
global $premiumbox;	
	
	if(current_user_can('administrator') or current_user_can('pn_finstats')){
		add_menu_page(__('Financial statistics','pn'), __('Financial statistics','pn'), 'read', "pn_finstats", array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('finstats'));
	}
	
}

add_filter('pn_caps','finstats_pn_caps');
function finstats_pn_caps($pn_caps){
	
	$pn_caps['pn_finstats'] = __('Use financial statistics','pn');
	
	return $pn_caps;
}

add_action('pn_adminpage_title_pn_finstats', 'pn_admin_title_pn_finstats');
function pn_admin_title_pn_finstats(){
	_e('Financial statistics','pn');
}

add_action('pn_adminpage_content_pn_finstats','def_pn_admin_content_pn_finstats');
function def_pn_admin_content_pn_finstats(){
global $wpdb;
?>
<form action="<?php pn_the_link_post('finstats_form'); ?>" class="finstats_form" method="post">
	<div class="finfiletrs">
		<div class="fin_list">
			<div class="fin_label"><?php _e('Start date','pn'); ?></div>
			<input type="search" name="startdate" class="pn_datepicker" value="" />
		</div>
		<div class="fin_list">
			<div class="fin_label"><?php _e('End date','pn'); ?></div>
			<input type="search" name="enddate" class="pn_datepicker" value="" />
		</div>		
			<div class="premium_clear"></div>
			
		<?php
		$valuts = apply_filters('list_valuts_manage', array(), __('No item','pn'));		
		?>
					
		<div class="fin_list">
			<div class="fin_label"><?php _e('Currency name','pn'); ?></div>

			<select name="valut_id" autocomplete="off">
				<?php foreach($valuts as $key => $valut){ ?>
					<option value="<?php echo $key; ?>"><?php echo $valut; ?></option>
				<?php } ?>
			</select>
		</div>

		<?php
		$vtype = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."vtypes ORDER BY vtype_title ASC");
		?>		
		<div class="fin_list">
			<div class="fin_label"><?php _e('Currency code','pn'); ?></div>

			<select name="vtype_id" autocomplete="off">
				<option value="0">--<?php _e('No item','pn'); ?>--</option>
				<?php foreach($vtype as $item){ ?>
					<option value="<?php echo $item->id; ?>"><?php echo is_site_value($item->vtype_title); ?></option>
				<?php } ?>
			</select>

		</div>		
			<div class="premium_clear"></div>
		
		<div class="fin_list">
			<div class="fin_label"><?php _e('Convert to','pn'); ?></div>

			<select name="convert" autocomplete="off">
				<option value="0">--<?php _e('not to convert','pn'); ?>--</option>
				<?php foreach($vtype as $item){ ?>
					<option value="<?php echo $item->id; ?>"><?php echo is_site_value($item->vtype_title); ?></option>
				<?php } ?>
			</select>
		</div>

		<div class="fin_list">
			<div class="fin_label"><?php _e('Individual Central Bank rate','pn'); ?></div>
			<input type="text" name="curs" value="" />
		</div>		
			<div class="premium_clear"></div>		
			
		<div class="fin_line"><label><input type="checkbox" name="share" value="1" /> <?php _e('multiplied by individual rate','pn'); ?></label></div>	
		<div class="fin_line"><label><input type="checkbox" name="ppay" value="1" /> <?php _e('consider affiliate payouts','pn'); ?></label></div>
		<div class="fin_line"><label><input type="checkbox" name="trans" value="1" /> <?php _e('consider corrections of reserve','pn'); ?></label></div>
			
		<input type="submit" name="submit" class="finstat_link" value="<?php _e('Display statistics','pn'); ?>" />
		<div class="finstat_ajax"></div>
			
			<div class="premium_clear"></div>
	</div>
</form>

<div id="finres"></div>

<script type="text/javascript">
jQuery(function($){
	
	$('.finstats_form').ajaxForm({
	    dataType:  'json',
        beforeSubmit: function(a,f,o) {
			
			$('.finstat_link').attr('disabled',true);
		    $('.finstat_ajax').show();
			
        },
		error: function(res, res2, res3){
			<?php do_action('pn_js_error_response'); ?>
		},		
        success: function(res) {
			
			$('.finstat_link').attr('disabled',false);
		    $('.finstat_ajax').hide();
			
			if(res['status'] == 'error'){
				<?php do_action('pn_js_alert_response'); ?>
			} else if(res['status'] == 'success') {
				$('#finres').html(res['table']);
			}
        }
    });
	
});
</script>	
	
<?php
}

add_action('premium_action_finstats_form', 'pn_premium_action_finstats_form');
function pn_premium_action_finstats_form(){
global $wpdb;

	only_post();
	$log = array();
	$log['status'] = 'success';
	$log['response'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = '';	
	
	if(current_user_can('administrator') or current_user_can('pn_finstats')){
		
		$where1 = $where2 = $where3 = $where4 = '';		
		
		$pr = $wpdb->prefix;
		
		$startdate = is_my_date(is_param_post('startdate'));
		if($startdate){
			$startdate = get_mydate($startdate,'Y-m-d 00:00');
			$where1 .= " AND createdate >= '$startdate'";
			$where2 .= " AND createdate >= '$startdate'";
			$where3 .= " AND pay_date >= '$startdate'";
			$where4 .= " AND trans_create >= '$startdate'";
		}
		$enddate = is_my_date(is_param_post('enddate'));
		if($enddate){
			$enddate = get_mydate($enddate,'Y-m-d 00:00');
			$where1 .= " AND createdate <= '$enddate'";
			$where2 .= " AND createdate <= '$enddate'";
			$where3 .= " AND pay_date <= '$enddate'";
			$where4 .= " AND trans_create <= '$enddate'";
		}	

		$vtype_convert = '';
		
		$valut_id = intval(is_param_post('valut_id'));
		if($valut_id){
			$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE id='$valut_id'");
			if(isset($data->id)){
				$where1 .= " AND valut1i = '$valut_id'";
				$where2 .= " AND valut2i = '$valut_id'";
				$where3 .= " AND valut_id = '$valut_id'";
				$where4 .= " AND valut_id = '$valut_id'";	
				$vtype_convert = $data->vtype_title;
			}
		}
		
		$vtype_id = intval(is_param_post('vtype_id'));
		if($vtype_id){
			$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."vtypes WHERE id='$vtype_id'");
			if(isset($data->id)){			
				$where1 .= " AND vtype1i = '$vtype_id'";
				$where2 .= " AND vtype2i = '$vtype_id'";
				$where3 .= " AND vtype_id = '$vtype_id'";
				$where4 .= " AND vtype_id = '$vtype_id'";
				$vtype_convert = $data->vtype_title;
			}
		}	
		
		if(!$vtype_convert){
			$log['table'] = '<div class="finresults">'. __('Currency code is not chosen','pn') .'</div>';
			echo json_encode($log);	
			exit;
		}

		$c_oper = 0;
		$bou = 0;
		$ac_bou = 0;
		$sol = 0;
		$ac_sol = 0;
		$profit = 0;		
		$ac_profit = 0;
		
		$c_oper = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."bids WHERE status IN('payed','realpay','verify','coldsuccess','success') $where1 OR status IN('success') $where2");
		
		$bou_c = $wpdb->get_var("SELECT SUM(summ1_dc) FROM ".$wpdb->prefix."bids WHERE status IN('payed','realpay','verify','coldsuccess','success') $where1");
		$bou = $bou + $bou_c;
		
		$ac_bou_c = $wpdb->get_var("SELECT SUM(summ1cr) FROM ".$wpdb->prefix."bids WHERE status IN('payed','realpay','verify','coldsuccess','success') $where1");
		$ac_bou = $ac_bou + $ac_bou_c;		
		
		$sol_c = $wpdb->get_var("SELECT SUM(summ2c) FROM ".$wpdb->prefix."bids WHERE status = 'success' $where2");
		$sol = $sol + $sol_c;
		
		$ac_sol_c = $wpdb->get_var("SELECT SUM(summ2cr) FROM ".$wpdb->prefix."bids WHERE status = 'success' $where2");
		$ac_sol = $ac_sol + $ac_sol_c;

		$ppay = intval(is_param_post('ppay'));
		if($ppay == 1){
			$query = $wpdb->query("CHECK TABLE ".$wpdb->prefix ."payoutuser");
			if($query == 1){
				$partn = $wpdb->get_var("SELECT SUM(pay_sum) FROM ".$wpdb->prefix."payoutuser WHERE status = '1' $where3");
				$bou = $bou - $partn;
				$ac_bou = $ac_bou - $partn;
			}
		}
		
		$trans = intval(is_param_post('trans'));
		if($trans == 1){
			$tr = $wpdb->get_var("SELECT SUM(trans_summ) FROM ".$wpdb->prefix."trans_reserv WHERE id > 0 $where4");
			$bou = $bou + $tr;
			$ac_bou = $ac_bou + $tr;
		}			
		
		$convert = intval(is_param_post('convert'));
		$curs = is_my_money(is_param_post('curs'));
		if($convert){
			$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."vtypes WHERE id='$convert'");
			if(isset($data->id)){
				$bou = convert_sum($bou, $vtype_convert, $data->vtype_title);
				$sol = convert_sum($sol, $vtype_convert, $data->vtype_title);
				$ac_bou = convert_sum($ac_bou, $vtype_convert, $data->vtype_title);
				$ac_sol = convert_sum($ac_sol, $vtype_convert, $data->vtype_title);
				$vtype_convert = $data->vtype_title;
			}
		} elseif($curs > 0){
			$share = intval(is_param_post('share'));
			$bou = convert_mycurs($bou, $curs, $share);
			$sol = convert_mycurs($sol, $curs, $share);
			$ac_bou = convert_mycurs($ac_bou, $curs, $share);
			$ac_sol = convert_mycurs($ac_sol, $curs, $share);			
			$vtype_convert = 'S';
		}
		
		$profit = $bou - $sol;
		$ac_profit = $ac_bou - $ac_sol;		
		
		$profit = get_summ_color($profit);		
		$ac_profit = get_summ_color($ac_profit);
		
		$table = '
		<div class="finresults">
			<div class="finline"><strong>'. __('Exchange operations in Total','pn') .'</strong>: '. $c_oper .'</div>
			<div class="finline"><strong>'. __('Bought','pn') .'</strong>: '. $bou .' '. $vtype_convert .'</div>
			<div class="finline"><strong>'. __('Actually bought','pn') .'</strong>: '. $ac_bou .' '. $vtype_convert .'</div>
			<div class="finline"><strong>'. __('Sold','pn') .'</strong>: '. $sol .' '. $vtype_convert .'</div>
			<div class="finline"><strong>'. __('Actually sold','pn') .'</strong>: '. $ac_sol .' '. $vtype_convert .'</div>
			<div class="finline"><strong>'. __('Profit','pn') .'</strong>: '. $profit .' '. $vtype_convert .'</div>
			<div class="finline"><strong>'. __('Actuall profit','pn') .'</strong>: '. $ac_profit .' '. $vtype_convert .'</div>
		</div>		
		';
		
		$log['table'] = $table;
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('You do not have permission','pn');
	}	
	
	echo json_encode($log);	
	exit;
}