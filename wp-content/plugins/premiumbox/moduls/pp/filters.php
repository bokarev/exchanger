<?php
if( !defined( 'ABSPATH')){ exit(); }

/* добавляем страницы в меню */
add_filter('account_list_pages','account_list_pages_pp',99);
function account_list_pages_pp($account_list_pages){
global $premiumbox;
	
	$pages = $premiumbox->get_option('partners','pages');
	if(!is_array($pages)){ $pages = array(); }
	
	foreach($pages as $page){
		$account_list_pages[$page] = array('type' => 'page');
	}
	
	return $account_list_pages;
}
/* end добавляем страницы в меню */

add_filter('banner_pages', 'def_banner_pages');
function def_banner_pages($banner_pages){
global $premiumbox;
	
	$text_banners = intval($premiumbox->get_option('partners','text_banners'));
	if(!$text_banners){
		if(isset($banner_pages['text'])){
			unset($banner_pages['text']);
		}
	}
	
	return $banner_pages;
}

add_filter('pp_banners','def_pp_banners');
function def_pp_banners($banners){
	
	$banners = array(
		'text'=> __('Text materials','pn'),
		'banner1'=> sprintf(__('Banners %s','pn'),'(468 x 60)'),
		'banner2'=> sprintf(__('Banners %s','pn'),'(200 x 200)'),
		'banner3'=> sprintf(__('Banners %s','pn'),'(120 x 600)'),
		'banner4'=> sprintf(__('Banners %s','pn'),'(100 x 100)'),
		'banner5'=> sprintf(__('Banners %s','pn'),'(88 x 31)'),
		'banner6'=> sprintf(__('Banners %s','pn'),'(336 x 280)'),
		'banner7'=> sprintf(__('Banners %s','pn'),'(250 x 250)'),
		'banner8'=> sprintf(__('Banners %s','pn'),'(240 x 400)'),
		'banner9'=> sprintf(__('Banners %s','pn'),'(234 x 60)'),
		'banner10'=> sprintf(__('Banners %s','pn'),'(120 x 90)'),
		'banner11'=> sprintf(__('Banners %s','pn'),'(120 x 60)'),
		'banner12'=> sprintf(__('Banners %s','pn'),'(120 x 240)'),
		'banner13'=> sprintf(__('Banners %s','pn'),'(125 x 125)'),
		'banner14'=> sprintf(__('Banners %s','pn'),'(300 x 600)'),
		'banner15'=> sprintf(__('Banners %s','pn'),'(300 x 250)'),
		'banner16'=> sprintf(__('Banners %s','pn'),'(80 x 150)'),
		'banner17'=> sprintf(__('Banners %s','pn'),'(728 x 90)'),
		'banner18'=> sprintf(__('Banners %s','pn'),'(160 x 600)'),
		'banner19'=> sprintf(__('Banners %s','pn'),'(80 x 15)'),
	);	
	
	return $banners;
}

add_action('wp_before_admin_bar_render', 'wp_before_admin_bar_render_payouts');
function wp_before_admin_bar_render_payouts() {
global $wp_admin_bar, $wpdb, $premiumbox;
	
    if(current_user_can('administrator') or current_user_can('pn_pp')){
		$z = $wpdb->query("SELECT id FROM ".$wpdb->prefix."payoutuser WHERE status = '0'");
		if($z > 0){
			$wp_admin_bar->add_menu( array(
				'id'     => 'new_payoutuser',
				'href' => admin_url('admin.php?page=pn_payouts&mod=1'),
				'title'  => '<div style="height: 32px; width: 22px; background: url('. $premiumbox->plugin_url .'moduls/pp/images/newpayout.png) no-repeat center center"></div>',
				'meta' => array( 'title' => sprintf(__('Requests for payouts (%s)','pn'), $z) ) 		
			));	
		}
	}
}

add_filter('pn_valuts_addform','pn_valuts_addform_pp', 10, 2);
function pn_valuts_addform_pp($options, $data){
		
	$options['line_pvivod'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['pvivod'] = array(
		'view' => 'select',
		'title' => __('Allow affiliate money withdrawal','pn'),
		'options' => array('1'=>__('Yes','pn'),'0'=>__('No','pn')),
		'default' => is_isset($data, 'pvivod'),
		'name' => 'pvivod',
	);
	$options['payout_com'] = array(
		'view' => 'input',
		'title' => __('Fee of payment system for payout of funds to partner','pn'),
		'default' => is_isset($data, 'payout_com'),
		'name' => 'payout_com',
	);	
	if(isset($options['bottom_title'])){
		unset($options['bottom_title']);
	}	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	
	return $options;
}

add_filter('pn_valuts_addform_post', 'pn_valuts_addform_post_pp');
function pn_valuts_addform_post_pp($array){
	
	$array['pvivod'] = intval(is_param_post('pvivod'));
	$array['payout_com'] = is_my_money(is_param_post('payout_com'));
	
	return $array;
}

add_filter('update_valut_reserv', 'update_valut_reserv_pp', 10, 2);
function update_valut_reserv_pp($money, $valut_id){
global $wpdb, $premiumbox;	
	
	$reserv = $premiumbox->get_option('partners','reserv');
	if(!is_array($reserv)){ $reserv = array(); }

	$status = array();
	foreach($reserv as $st){
		$st = pn_strip_input($st);
		$status[] = "'". $st ."'";
	}
	if(count($status) > 0){
		$st = join(',',$status);
		$sum = $wpdb->get_var("SELECT SUM(pay_sum) FROM ".$wpdb->prefix."payoutuser WHERE valut_id='$valut_id' AND status IN($st)");
		$money = is_my_money($money - $sum);
	} 	
	
	return $money;
}

/* api */
add_action('myaction_request_affiliate', 'myaction_request_affiliate_pp');
function myaction_request_affiliate_pp(){
global $premiumbox;
	
	$ref = 'register+cookie';
	if(intval($premiumbox->get_option('partners','wref')) == 1){
		$ref = 'cookie';
	}

	$log = array(
		'status' => 'enable',
		'ref' => $ref,
	);
	
	echo json_encode($log);
	exit;	
}
/* end api */

add_action('change_bidstatus_all', 'change_bidstatus_all_pp',1,3);
function change_bidstatus_all_pp($status, $item_id, $item){
global $wpdb, $premiumbox;
	$not = array('realdelete','autodelete','auto','archived');
	if(!in_array($status, $not)){
		if($status == 'success'){
			$calc = intval($premiumbox->get_option('partners','calc'));
			if($calc == 0 or $calc == 1 and $item->user_id > 0){
				$ref_id = $item->ref_id;
				$psum = is_my_money($item->summp);
				if($ref_id and $psum > 0){
					$rd = get_userdata($ref_id);
					$ctype = cur_type();
					
					if(isset($rd->user_email)){
						$ref_email = is_email($rd->user_email);
						$wpdb->update($wpdb->prefix.'bids', array('pcalc'=> 1), array('id'=>$item_id));
				
						$mailtemp = get_option('mailtemp');
						if(isset($mailtemp['partprofit'])){
							$data = $mailtemp['partprofit'];
							if($data['send'] == 1){
								$ot_mail = is_email($data['mail']);
								$ot_name = pn_strip_input($data['name']);
								
								$subject = pn_strip_input(ctv_ml($data['title']));
								$html = pn_strip_text(ctv_ml($data['text']));
								
								if($ref_email){
								
									$sitename = pn_strip_input(get_bloginfo('sitename'));
								
									$subject = str_replace('[sitename]', $sitename ,$subject);
									$subject = str_replace('[sum]', $psum ,$subject);
									$subject = str_replace('[ctype]', $ctype ,$subject);
									$subject = apply_filters('mail_partprofit_subject',$subject);
									
									$html = str_replace('[sitename]', $sitename ,$html);
									$html = str_replace('[sum]', $psum ,$html);
									$html = str_replace('[ctype]', $ctype ,$html);
									$html = apply_filters('mail_partprofit_text',$html);
									$html = apply_filters('comment_text',$html);
										
									pn_mail($ref_email, $subject, $html, $ot_name, $ot_mail);	
									
								}
							}
						}
					}
				}
			}
		} else {
			$wpdb->update($wpdb->prefix.'bids', array('pcalc'=> 0), array('id'=>$item_id));
		}
	}
}

add_filter('list_tabs_naps','list_tabs_naps_pp');
function list_tabs_naps_pp($list_tabs_naps){
	$list_tabs_naps['tab100'] = __('Affiliate program','pn');
	
	return $list_tabs_naps;
}

add_action('tab_naps_tab100','tab_naps_tab_pp',99,2);
function tab_naps_tab_pp($data, $data_id){
?>	
	<tr>
		<th><?php _e('Affiliate payments','pn'); ?></th>
		<td>
			<div class="premium_wrap_standart">
				<?php 
					$p_enable = get_naps_meta($data_id, 'p_enable');
					if(!is_numeric($p_enable)){ $p_enable = 1; } 
				?>									
				<select name="p_enable" autocomplete="off"> 
					<option value="1" <?php selected($p_enable,1); ?>><?php _e('pay','pn'); ?></option>
					<option value="0" <?php selected($p_enable,0); ?>><?php _e('not to pay','pn'); ?></option>
				</select>
			</div>
		</td>
		<td>			
		</td>
	</tr>
	<tr>
		<th><?php _e('Fixed amount of payment for benefit of partner','pn'); ?></th>
		<td>
			<div class="premium_wrap_standart">
				<input type="text" name="p_ind_sum" style="width: 100px;" value="<?php echo is_my_money(get_naps_meta($data_id, 'p_ind_sum')); ?>" /><?php echo cur_type(); ?>
			</div>
		</td>
		<td>			
		</td>
	</tr>	
	<tr>
		<th><?php _e('Min. amount of payment for benefit of partner','pn'); ?></th>
		<td>
			<div class="premium_wrap_standart">
				<input type="text" name="p_min_sum" style="width: 100px;" value="<?php echo is_my_money(get_naps_meta($data_id, 'p_min_sum')); ?>" /><?php echo cur_type(); ?>
			</div>
		</td>
		<td>			
		</td>
	</tr>	
	<tr>
		<th><?php _e('Max. amount of payment for benefit of partner','pn'); ?></th>
		<td>
			<div class="premium_wrap_standart">
				<input type="text" name="p_max_sum" style="width: 100px;" value="<?php echo is_my_money(get_naps_meta($data_id, 'p_max_sum')); ?>" /><?php echo cur_type(); ?>
			</div>
		</td>
		<td>			
		</td>
	</tr>	
	<tr>
		<th><?php _e('Individual percent given by an affiliate program','pn'); ?></th>
		<td>
			<div class="premium_wrap_standart">
				<input type="text" name="p_pers" style="width: 100px;" value="<?php echo is_my_money(get_naps_meta($data_id, 'p_pers')); ?>" />%
			</div>
		</td>
		<td>			
		</td>
	</tr>	
	<tr>
		<th><?php _e('Maximum percent given by an affiliate program','pn'); ?></th>
		<td>
			<div class="premium_wrap_standart">
				<input type="text" name="p_max" style="width: 100px;" value="<?php echo is_my_money(get_naps_meta($data_id, 'p_max')); ?>" />%
			</div>
		</td>
		<td>			
		</td>
	</tr>	
<?php
} 
 
add_action('pn_naps_edit_before','pn_naps_edit_pp');
add_action('pn_naps_add','pn_naps_edit_pp');
function pn_naps_edit_pp($data_id){
	
	$p_enable = intval(is_param_post('p_enable'));
	update_naps_meta($data_id, 'p_enable', $p_enable);
	
	$p_pers = is_my_money(is_param_post('p_pers'));
	update_naps_meta($data_id, 'p_pers', $p_pers);	
	
	$p_max = is_my_money(is_param_post('p_max'));
	update_naps_meta($data_id, 'p_max', $p_max);

	$p_ind_sum = is_my_money(is_param_post('p_ind_sum'));
	update_naps_meta($data_id, 'p_ind_sum', $p_ind_sum);

	$p_min_sum = is_my_money(is_param_post('p_min_sum'));
	update_naps_meta($data_id, 'p_min_sum', $p_min_sum);
	
	$p_max_sum = is_my_money(is_param_post('p_max_sum'));
	update_naps_meta($data_id, 'p_max_sum', $p_max_sum);
	
} 

add_filter('array_data_create_bids', 'pp_array_data_create_bids', 10, 4);
function pp_array_data_create_bids($array, $naps, $vd1, $vd2){
global $wpdb, $premiumbox;

	$ref_id = 0;
	$partpr = 0;
	$summp = 0;
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	$p_enable = is_isset($naps,'p_enable');
	if($p_enable == 1){
			
		$ref_id = 0;
		if(intval($premiumbox->get_option('partners','wref')) == 0 and $user_id){
			if(isset($ui->ref_id)){
				$ref_id = $ui->ref_id;
			}	
		}
			
		if(!$ref_id){
			$ref_id = intval(get_mycookie('ref_id')); 
		}			
			
		$profit = is_my_money($array['profit']);	
		$user_discount = is_my_money($array['user_sk']);
		if($ref_id and $ref_id != $user_id){
			$ref_cou = $wpdb->query("SELECT ID FROM ". $wpdb->prefix ."users WHERE ID='$ref_id'");
			if($ref_cou > 0){
				$p_ind_sum = is_my_money(is_isset($naps,'p_ind_sum'));
				if($p_ind_sum > 0){
					$summp = $p_ind_sum;
				} elseif($profit > 0) {
					$p_pers = is_my_money(is_isset($naps,'p_pers'),2);
					if($p_pers > 0){
						$partpr = $p_pers;
					} else {
						$partpr = get_user_pers_refobmen($ref_id);
						$p_max = is_my_money(is_isset($naps,'p_max'));
						if($p_max > 0 and $partpr > $p_max){ $partpr = $p_max; }
					}	
					if($partpr > 0){
						$summp = $profit / 100 * $partpr;
						$summp = is_my_money($summp,2);
					}						
				}				
				if($premiumbox->get_option('partners','uskidka') == 1 and $user_discount > 0 and $summp > 0){
					$one_pers = $summp / 100;
					$summp = $summp - ($one_pers * $user_discount);
				}
				$p_min_sum = is_my_money(is_isset($naps,'p_min_sum'));
				if($summp < $p_min_sum){ $summp = $p_min_sum; }
				$p_max_sum = is_my_money(is_isset($naps,'p_max_sum'));
				if($p_max_sum > 0 and $summp > $p_max_sum){ $summp = $p_max_sum; }
			} else {
				$ref_id = 0;
			}
		}  
		
	}	
	
	$array['ref_id'] = $ref_id;
	$array['summp'] = $summp;
	$array['partpr'] = $partpr;	
	
	return $array;
}