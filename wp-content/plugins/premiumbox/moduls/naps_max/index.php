<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Лимит резерва валюты по направлению обмена[:ru_RU][en_US:]Exchange direction currency limit[:en_US]
description: [ru_RU:]Лимит резерва валюты по направлению обмена[:ru_RU][en_US:]Exchange direction currency limit[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);
 
/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_naps_max');
function bd_pn_moduls_active_naps_max(){
global $wpdb;	
	
/*
naps_lang - языки
maxexip - макс кол-во обменов с одного ip в сутки
*/	
	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'maxnaps'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `maxnaps` varchar(50) NOT NULL default '0'");
    }
	
}
/* end BD */

add_action('tab_naps_tab8', 'naps_max_tab_naps_tab8', 1, 2);
function naps_max_tab_naps_tab8($data, $data_id){
	?>
	<tr>
		<th><?php _e('Reserve limit for exhange direction','pn'); ?></th>
		<td colspan="2">
			<div class="premium_wrap_standart">
				<input type="text" name="maxnaps" style="width: 200px;" value="<?php echo is_my_money(is_isset($data, 'maxnaps')); ?>" />
			</div>			
		</td>
	</tr>	
	<?php 		
}


add_filter('pn_naps_addform_post', 'naps_max_pn_naps_addform_post');
function naps_max_pn_naps_addform_post($array){

	$array['maxnaps'] = is_my_money(is_param_post('maxnaps'));
	
	return $array;
}

add_filter('get_max_sum_to_naps_get', 'naps_max_get_max_sum_to_naps_get', 10, 3);
function naps_max_get_max_sum_to_naps_get($max, $naps, $vd){
	
	if($naps->maxnaps > 0){
		$summ_naps_all = get_summ_naps_all($naps->id, 'out'); /* сумма обменов по данному направлению */
		$maxnaps = $naps->maxnaps - $summ_naps_all;
		if($maxnaps < 0){ $maxnaps = 0; }
		
		if(is_numeric($max)){
			if($max > $maxnaps){
				$max = $maxnaps;
			}
		} else {
			$max = $maxnaps;
		}
	}				
	
	return $max;
}	