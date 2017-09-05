<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Редактор заявки на обмен[:ru_RU][en_US:]Order editor[:en_US]
description: [ru_RU:]Редактор заявки на обмен[:ru_RU][en_US:]Order editor[:en_US]
version: 0.1
category: [ru_RU:]Заявки[:ru_RU][en_US:]Orders[:en_US]
cat: req
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path); 

add_filter('pn_caps','editbids_pn_caps');
function editbids_pn_caps($pn_caps){
	$pn_caps['pn_bids_edit'] = __('Edit order','pn');
	return $pn_caps;
}

add_filter('onebid_actions','onebid_actions_dop_editbids', 1000,3);
function onebid_actions_dop_editbids($onebid_actions, $item, $data_fs){
	if(current_user_can('administrator') or current_user_can('pn_bids_edit')){
		$onebid_actions['edit_bids'] = array(
			'type' => 'link',
			'title' => __('Edit','pn'),
			'label' => __('Edit','pn'),
			'link' => pn_link_post('edit_one_bid') .'&bid_id=[id]',
			'link_target' => '_blank',
			'link_class' => 'editting',
		);	
	}

	return $onebid_actions;
}

add_filter('list_param_edit_bids', 'def_list_param_edit_bids', 10, 2);
function def_list_param_edit_bids($lists, $item){
	
	$lists['curs1'] = array(
		'title' => __('Rate Send','pn'),
		'name' => 'curs1',
		'view' => 'input',
		'default' => is_my_money(is_isset($item,'curs1')),
		'work' => 'sum',
	);
	$lists['curs2'] = array(
		'title' => __('Rate Receive','pn'),
		'name' => 'curs2',
		'view' => 'input',
		'default' => is_my_money(is_isset($item,'curs2')),
		'work' => 'sum',
	);	
	$lists['exsum'] = array(
		'title' => __('Amount in internal currency','pn'),
		'name' => 'exsum',
		'view' => 'input',
		'default' => is_my_money(is_isset($item,'exsum')),
		'work' => 'sum',
	);	
	$lists['profit'] = array(
		'title' => __('Profit','pn'),
		'name' => 'profit',
		'view' => 'input',
		'default' => is_my_money(is_isset($item,'profit')),
		'work' => 'sum',
	);	
	$lists['user_sk'] = array(
		'title' => __('User discount (%)','pn'),
		'name' => 'user_sk',
		'view' => 'input',
		'default' => is_my_money(is_isset($item,'user_sk')),
		'work' => 'sum',
	);
	$lists['user_sksumm'] = array(
		'title' => __('User discount (amount)','pn'),
		'name' => 'user_sksumm',
		'view' => 'input',
		'default' => is_my_money(is_isset($item,'user_sksumm')),
		'work' => 'sum',
	);	
	$lists['naschet'] = array(
		'title' => __('Merchant account','pn'),
		'name' => 'naschet',
		'view' => 'input',
		'default' => pn_strip_input(is_isset($item,'naschet')),
		'work' => 'input',
	);	
	$lists['soschet'] = array(
		'title' => __('Automatic payout account','pn'),
		'name' => 'soschet',
		'view' => 'input',
		'default' => pn_strip_input(is_isset($item,'soschet')),
		'work' => 'input',
	);
	$lists['trans_in'] = array(
		'title' => __('Merchant transaction ID','pn'),
		'name' => 'trans_in',
		'view' => 'input',
		'default' => pn_strip_input(is_isset($item,'trans_in')),
		'work' => 'input',
	);
	$lists['trans_out'] = array(
		'title' => __('Auto payout transaction ID','pn'),
		'name' => 'trans_out',
		'view' => 'input',
		'default' => pn_strip_input(is_isset($item,'trans_out')),
		'work' => 'input',
	);	
	$lists['account1'] = array(
		'title' => __('From account','pn'),
		'name' => 'account1',
		'view' => 'input',
		'default' => pn_strip_input(is_isset($item,'account1')),
		'work' => 'input',
	);
	$lists['account2'] = array(
		'title' => __('Into account','pn'),
		'name' => 'account2',
		'view' => 'input',
		'default' => pn_strip_input(is_isset($item,'account2')),
		'work' => 'input',
	);
	$lists['summ1_dc'] = array(
		'title' => __('Amount (with add. fees)','pn'),
		'name' => 'summ1_dc',
		'view' => 'input',
		'default' => pn_strip_input(is_isset($item,'summ1_dc')),
		'work' => 'sum',
	);
	$lists['summ1c'] = array(
		'title' => __('Amount (with add. fees and PS fees)','pn'),
		'name' => 'summ1c',
		'view' => 'input',
		'default' => pn_strip_input(is_isset($item,'summ1c')),
		'work' => 'sum',
	);	
	$lists['summ2_dc'] = array(
		'title' => __('Amount (with add. fees)','pn'),
		'name' => 'summ2_dc',
		'view' => 'input',
		'default' => pn_strip_input(is_isset($item,'summ2_dc')),
		'work' => 'sum',
	);	
	$lists['summ2c'] = array(
		'title' => __('Amount (with add. fees and PS fees)','pn'),
		'name' => 'summ2c',
		'view' => 'input',
		'default' => pn_strip_input(is_isset($item,'summ2c')),
		'work' => 'sum',
	);		
	
	return $lists;
}

add_action('premium_action_edit_one_bid','def_edit_one_bid');
function def_edit_one_bid(){
global $wpdb, $premiumbox;
	if(current_user_can('administrator') or current_user_can('pn_bids_edit')){

		reset_adminpass();
	
		$bid_id = intval(is_param_get('bid_id'));
		if($bid_id > 0){
			$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bids WHERE id='$bid_id'");
			if(isset($item->id)){
				$title = sprintf(__('Edit order ID %s','pn'), $bid_id);
?>
<!DOCTYPE html>
<html <?php echo get_language_attributes('html'); ?>>
<head>
	<meta charset="<?php echo get_bloginfo( 'charset' ); ?>">
	<title><?php echo $title; ?></title>
			
	<script type="text/javascript" src="<?php echo get_premium_url(); ?>js/jquery.min.js"></script>
	<link rel="stylesheet" href="<?php echo $premiumbox->plugin_url; ?>bid_style.css" type="text/css" media="all" />
</head>
<body>
<div id="container">	
	<div class="header">
		<div class="header_ins">
			<?php echo $title; ?>
		</div>
	</div>
<?php 
	$reply = is_param_get('reply');
	if($reply == 'true'){
?>
	<div class="resulttrue"><?php _e('Action completed successfully','pn'); ?></div>
<?php
	}	
?>
	<div class="content">
		<div class="content_ins">
			<form method="post" action="<?php pn_the_link_post('edit_one_bid_post'); ?>">
				<input type="hidden" name="bid_id" value="<?php echo $bid_id; ?>" />
				<input type="hidden" name="_wp_referrer" value="<?php echo urlencode(get_site_url_or() . $_SERVER['REQUEST_URI']); ?>" />
				<table>
					<?php
					$lists = apply_filters('list_param_edit_bids', array(), $item); 
					foreach($lists as $list){
					?>
					<tr>
						<th><?php echo is_isset($list, 'title'); ?>:</th>
						<td><input type="text" name="<?php echo is_isset($list, 'name'); ?>" value="<?php echo is_isset($list, 'default'); ?>" /></td>
					</tr>				
					<?php } ?>
					<?php 
					if(m_defined('PN_SECRET_KEY')){ 
						$pass = get_adminpass(m_defined('PN_SECRET_KEY'));
						$now_pass = get_mycookie('adminpass');
						if($pass != $now_pass){
					?>
					<tr>
						<th>Password:</th>
						<td><input type="password" name="pass" value="" /></td>
					</tr>
					<?php
						}
					} 
					?>
					<tr>
						<th></th>
						<td><input type="submit" name="" value="<?php _e('Edit','pn'); ?>" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>	
</body>
</html>	
<?php
			} else {
				pn_display_mess(__('Error! Order do not exist','pn'));
			}
		} else {
			pn_display_mess(__('Error! Order do not exist','pn'));
		}
	} else {
		pn_display_mess(__('Error! insufficient privileges!','pn'));
	}
}

add_action('premium_action_edit_one_bid_post','def_edit_one_bid_post');
function def_edit_one_bid_post(){
global $wpdb, $premiumbox;

	only_post();

	$secret_key = m_defined('PN_SECRET_KEY');
	if($secret_key){ /* если есть пароль */ 
		$pass = get_adminpass($secret_key);
		$now_pass = get_mycookie('adminpass');
		if($pass != $now_pass){	/* если пароль из кук несовпадает */
			$user_pass = is_param_post('pass');
			if($secret_key != $user_pass){ 
				pn_display_mess(__('Error! You have entered an incorrect security password','pn'));	
			} else {
				set_adminpass($user_pass);
			}			
		}
	}
	
	if(current_user_can('administrator') or current_user_can('pn_bids_edit')){
		$bid_id = intval(is_param_post('bid_id'));
		if($bid_id > 0){
			$item = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."bids WHERE id='$bid_id'");
			if(isset($item->id)){
				
				$arr = array();
				$tables = array();
				$lists = apply_filters('list_param_edit_bids', array(), $item); 
				foreach($lists as $list){
					$name = trim(is_isset($list,'name'));
					if($name){
						$work = trim(is_isset($list,'work'));
						$value = is_param_post($name);
						if($work == 'input'){
							$value = pn_strip_input($value);
						} elseif($work == 'sum'){
							$value = is_my_money($value);
						}
						$arr[$name] = $value;
						$tables[] = $name;
					}
				}	
				if(count($arr) > 0){
					$wpdb->update($wpdb->prefix ."bids", $arr, array('id'=>$item->id));
				}
				
				bid_hashdata($item->id, '', $tables);
				
				reset_adminpass();
				
				$url = urldecode(is_param_post('_wp_referrer')).'&reply=true';
				wp_redirect($url);
				exit;
			} else {
				pn_display_mess(__('Error! insufficient privileges!','pn'));
			}
		} else {
			pn_display_mess(__('Error! insufficient privileges!','pn'));
		}			
	} else {
		pn_display_mess(__('Error! insufficient privileges!','pn'));
	}
}