<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Настройка языков для направлений обмена[:ru_RU][en_US:]Exchange directions Language settings[:en_US]
description: [ru_RU:]Настройка языков для направлений обмена[:ru_RU][en_US:]Exchange directions Language settings[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_napslangs');
function bd_pn_moduls_active_napslangs(){
global $wpdb;	
	
	/*
	naps_lang - языки
	*/	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'naps_lang'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `naps_lang` longtext NOT NULL");
    } else {
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps CHANGE `naps_lang` `naps_lang` longtext NOT NULL");
	}
	
}
/* end BD */

add_action('tab_naps_tab8', 'napslangs_tab_naps_tab8', 1, 2);
function napslangs_tab_naps_tab8($data, $data_id){

	if(is_ml()){ 
		$langs = get_langs_ml();
		?>
		<tr>
			<th><?php _e('Language','pn'); ?></th>
			<td colspan="2">
				<div class="premium_wrap_standart">				
					<div class="cf_div">
						<div style="font-weight: 500;"><label><input type="checkbox" class="check_all" name="" value="1" /> <?php _e('Check all/Uncheck all','pn'); ?></label></div>
						<?php
						$string = pn_strip_input(is_isset($data,'naps_lang'));
						$def = array();
						if(preg_match_all('/\[d](.*?)\[\/d]/s',$string, $match, PREG_PATTERN_ORDER)){
							$def = $match[1];
						}
						$r=0;
						foreach($langs as $lang){ $r++;
						?>	
							<div><label><input type="checkbox" name="naps_lang[]" <?php if(in_array($lang,$def) or $r == 1 and count($def) == 0){ ?>checked="checked"<?php } ?> value="<?php echo $lang; ?>" /> <?php echo get_title_forkey($lang);?></label></div>	
						<?php
						}	
						?>
					</div>				
				</div>
			</td>
		</tr>	
	<?php }		
}


add_filter('pn_naps_addform_post', 'napslangs_pn_naps_addform_post');
function napslangs_pn_naps_addform_post($array){

	$naps_lang = is_param_post('naps_lang');
	$langs = '';
	if(is_array($naps_lang)){
		foreach($naps_lang as $lang){
			$lang = pn_strip_input($lang);
			if($lang){
				$langs .= '[d]'. $lang .'[/d]';
			}
		}
	}
	$array['naps_lang'] = $langs;
	
	return $array;
}

add_action('pn_exchange_filters', 'napslangs_pn_exchange_filters');
function napslangs_pn_exchange_filters($lists){
	
	$lists[] = array(
		'title' => __('Filter by user language','pn'),
		'name' => 'napslangs',
	);
	
	return $lists;
}

add_filter('get_naps_where', 'napslangs_get_naps_where', 10, 2);
function napslangs_get_naps_where($where, $place){
global $premiumbox;	
	$locale = get_locale();
	if($locale){
		$ind = $premiumbox->get_option('exf_'. $place .'_napslangs');
		if($ind == 1){
			$where .= "AND naps_lang LIKE '%[d]{$locale}[/d]%' ";
		}
	}
	return $where;
}

add_filter('error_bids', 'error_bids_napslangs', 99 ,6);
function error_bids_napslangs($error_bids, $account1, $account2, $naps, $vd1, $vd2){

	$user_locale = get_locale();
	$string = pn_strip_input(is_isset($naps,'naps_lang'));
	$naps_lang = array();
	if(preg_match_all('/\[d](.*?)\[\/d]/s',$string, $match, PREG_PATTERN_ORDER)){
		$naps_lang = $match[1];
	}	
	if(!in_array($user_locale,$naps_lang)){
		$error_bids['error'] = 1;
		$error_bids['error_text'][] = __('Error! Exchange direction is prohibited for your language','pn');			
	}	
	
	return $error_bids;
}