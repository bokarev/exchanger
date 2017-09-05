<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Скидки пользователей[:ru_RU][en_US:]User discounts[:en_US]
description: [ru_RU:]Скидки пользователей[:ru_RU][en_US:]User discounts[:en_US]
version: 1.0
category: [ru_RU:]Пользователи[:ru_RU][en_US:]Users[:en_US]
cat: user
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_discounts');
function bd_pn_moduls_active_discounts(){
global $wpdb;
	
/* 	
user_discounts
*/	
	$table_name= $wpdb->prefix ."user_discounts";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
        `sumec` varchar(50) NOT NULL default '0',
		`discount` varchar(50) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);
	
}
/* end BD */

add_action('pn_adminpage_quicktags_page','pn_adminpage_quicktags_page_discount');
function pn_adminpage_quicktags_page_discount(){
?>
edButtons[edButtons.length] = 
new edButton('premium_table_discount', '<?php _e('Discount table','pn'); ?>','[table_discount]');
<?php	
}

add_action('admin_menu', 'pn_adminpage_discount');
function pn_adminpage_discount(){
global $premiumbox;
	
	if(current_user_can('administrator') or current_user_can('pn_discount')){
		
		$hook = add_menu_page(__('User discounts','pn'), __('User discounts','pn'), 'read', "pn_discount", array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('discount'));	
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_discount", __('Add discount','pn'), __('Add discount','pn'), 'read', "pn_add_discount", array($premiumbox, 'admin_temp'));
	
	}
	
}

add_filter('pn_caps','discount_pn_caps');
function discount_pn_caps($pn_caps){
	
	$pn_caps['pn_discount'] = __('User discounts settings','pn');
	
	return $pn_caps;
}

function shortcode_table_discount($atts,$content=""){ 
global $wpdb;

    $datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."user_discounts ORDER BY (sumec -0.0) ASC");
	$temp = '
	<div class="discont_div">
		<div class="discont_div_ins">
			<table>
				<tr>
					<th>'. __('Amount','pn') .'</th>
					<th>'. __('Discount (%)','pn') .'</th>
				</tr>';
				
				foreach($datas as $item){
			
					$temp .= '
					<tr>
						<td> > '. is_out_sum(is_my_money($item->sumec), 12, 'all') .'</td>
						<td>'. is_out_sum(is_my_money($item->discount), 12, 'all') .'%</td>
					</tr>
					';
					
				}
				
				$temp .= '
			</table>
		</div>	
	</div>
	';
	return $temp;
}
add_shortcode('table_discount', 'shortcode_table_discount');

add_filter('user_discount', 'user_discount_discounts', 9, 2);
function user_discount_discounts($discount, $user_id){
global $wpdb;
	
	$discount = is_my_money($discount);
	if($discount == 0){
		$sm = is_my_money(get_user_sum_exchanges($user_id));
		$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."user_discounts WHERE ('$sm' -0.0) >= sumec ORDER BY (sumec -0.0) DESC");
		if(isset($data->discount)){
			$discount = is_my_money($data->discount);
		}	
	}
	
	return $discount;
}

global $premiumbox;
$premiumbox->file_include($path.'/add');
$premiumbox->file_include($path.'/list');