<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Финансовая статистика (от прибыли) [:ru_RU][en_US:]Financial statistics (from profit)[:en_US]
description: [ru_RU:]Финансовая статистика от прибыли обмена[:ru_RU][en_US:]Financial statistics from exchange profits[:en_US]
version: 1.0
category: [ru_RU:]Заявки[:ru_RU][en_US:]Orders[:en_US]
cat: req
*/

add_action('admin_menu', 'pn_adminpage_finstats_bid');
function pn_adminpage_finstats_bid(){
global $premiumbox;	
	
	if(current_user_can('administrator') or current_user_can('pn_finstats')){
		add_menu_page(__('Financial statistics','pn'), __('Financial statistics','pn'), 'read', "pn_finstats_bid", array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('finstats'));
	}
	
}

add_filter('pn_caps','finstats_bid_pn_caps');
function finstats_bid_pn_caps($pn_caps){
	
	$pn_caps['pn_finstats'] = __('Use financial statistics','pn');
	
	return $pn_caps;
}

add_action('pn_adminpage_title_pn_finstats_bid', 'def_adminpage_title_pn_finstats_bid');
function def_adminpage_title_pn_finstats_bid(){
	_e('Financial statistics','pn');
}

add_action('pn_adminpage_content_pn_finstats_bid','def_adminpage_content_pn_finstats_bid');
function def_adminpage_content_pn_finstats_bid(){
global $wpdb;
?>
<form action="<?php pn_the_link_post('finstats_bid_form'); ?>" class="finstats_form" method="post">
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
		$vtypes = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."vtypes ORDER BY vtype_title ASC");
		?>		
		<div class="fin_list">
			<div class="fin_label"><?php _e('Convert to','pn'); ?></div>

			<select name="convert" autocomplete="off">
				<option value="0">--<?php _e('not to convert','pn'); ?>--</option>
				<?php foreach($vtypes as $item){ ?>
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

add_action('premium_action_finstats_bid_form', 'pn_premium_action_finstats_bid_form');
function pn_premium_action_finstats_bid_form(){
global $wpdb;

	only_post();
	$log = array();
	$log['status'] = 'success';
	$log['response'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = '';	
	
	if(current_user_can('administrator') or current_user_can('pn_finstats')){
		
		$where1 = $where2 = '';		
		
		$pr = $wpdb->prefix;
		
		$startdate = is_my_date(is_param_post('startdate'));
		if($startdate){
			$startdate = get_mydate($startdate,'Y-m-d 00:00');
			$where1 .= " AND createdate >= '$startdate'";
			$where2 .= " AND pay_date >= '$startdate'";
		}
		$enddate = is_my_date(is_param_post('enddate'));
		if($enddate){
			$enddate = get_mydate($enddate,'Y-m-d 00:00');
			$where1 .= " AND createdate <= '$enddate'";
			$where2 .= " AND pay_date <= '$enddate'";
		}	

		$vtype_convert = cur_type();

		$c_oper = 0;
		$profit = 0;		
		
		$c_oper = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."bids WHERE status IN('success') $where1");
		
		$profit_c = $wpdb->get_var("SELECT SUM(profit) FROM ".$wpdb->prefix."bids WHERE status IN('success') $where1");
		$profit = $profit + $profit_c;

		$ppay = intval(is_param_post('ppay'));
		if($ppay == 1){
			$query = $wpdb->query("CHECK TABLE ".$wpdb->prefix ."payoutuser");
			if($query == 1){
				$partn = $wpdb->get_var("SELECT SUM(pay_sum_or) FROM ".$wpdb->prefix."payoutuser WHERE status = '1' $where3");
				$profit = $profit - $partn;
			}
		}			
		
		$convert = intval(is_param_post('convert'));
		$curs = is_my_money(is_param_post('curs'));
		if($convert){
			$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."vtypes WHERE id='$convert'");
			if(isset($data->id)){
				$profit = convert_sum($profit, $vtype_convert, $data->vtype_title);
				$vtype_convert = $data->vtype_title;
			}
		} elseif($curs > 0){
			$share = intval(is_param_post('share'));
			$profit = convert_mycurs($profit, $curs, $share);			
			$vtype_convert = 'S';
		}
		
		$profit = get_summ_color($profit);		
		
		$table = '
		<div class="finresults">
			<div class="finline"><strong>'. __('Exchange operations in Total','pn') .'</strong>: '. $c_oper .'</div>
			<div class="finline"><strong>'. __('Profit','pn') .'</strong>: '. $profit .' '. $vtype_convert .'</div>
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