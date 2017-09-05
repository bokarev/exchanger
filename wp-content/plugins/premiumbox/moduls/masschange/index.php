<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Свой курс ЦБ[:ru_RU][en_US:]Individual Central Bank rate[:en_US]
description: [ru_RU:]Свой курс ЦБ[:ru_RU][en_US:]Individual Central Bank rate[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_masschange');
function bd_pn_moduls_active_masschange(){
global $wpdb;	
	
/*
masschange - свой курс ЦБ
mnums1 - число 1
melem1 - 0-сумма, 1-процент
mnums2 - число 1
melem2 - 0-сумма, 1-процент
*/	
	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'masschange'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `masschange` bigint(20) NOT NULL default '0'");
    }		
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'mnums1'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `mnums1` varchar(50) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'melem1'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `melem1` int(2) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'mnums2'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `mnums2` varchar(50) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'melem2'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `melem2` int(2) NOT NULL default '0'");
    }	
	
/*
Свой курс ЦБ
title - название
curs1 - курс1
curs2 - курс 2
*/
 	$table_name= $wpdb->prefix ."masschange";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`title` tinytext NOT NULL,
		`curs1` varchar(50) NOT NULL default '0',
		`curs2` varchar(50) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);		
	
}
/* end BD */

add_action('pn_adminpage_content_pn_naps', 'masschange_pn_adminpage_content_pn_naps');
function masschange_pn_adminpage_content_pn_naps(){
?>	
<style>
.column-yourcb{ width: 300px!important; }
</style>
<script type="text/javascript">
jQuery(function($){
	$('.naps_masschange').change(function(){
		var id = $(this).attr('id').replace('naps_masschange_','');
		var vale = $(this).val();
		if(vale > 0){
			$('#the_naps_masschange_'+id).show();
			$('#naps_parser_'+id).val('0');
			$('#the_naps_parser_'+id).hide();			
		} else {
			$('#the_naps_masschange_'+id).hide();
		}
	});		
});
</script>
<?php
}

add_action('pn_naps_save', 'masschange_pn_naps_save');
function masschange_pn_naps_save(){
global $wpdb;	
	
	if(isset($_POST['masschange']) and is_array($_POST['masschange'])){
		foreach($_POST['masschange'] as $id => $masschange){
			$id = intval($id);
			$masschange = intval($masschange);
			$melem1 = intval($_POST['melem1'][$id]);
			$mnums1 = pn_parser_num($_POST['mnums1'][$id]);			
			$melem2 = intval($_POST['melem2'][$id]);
			$mnums2 = pn_parser_num($_POST['mnums2'][$id]);						
						
			$array = array();
			if($masschange > 0){
				$array['masschange'] = $masschange;
				$array['melem1'] = $melem1;
				$array['mnums1'] = $mnums1;			
				$array['melem2'] = $melem2;
				$array['mnums2'] = $mnums2;
			} else {
				$array['masschange'] = 0;
				$array['melem1'] = 0;
				$array['mnums1'] = 0;			
				$array['melem2'] = 0;
				$array['mnums2'] = 0;							
			}					
			$wpdb->update($wpdb->prefix.'naps', $array, array('id'=>$id));
						
			if($masschange > 0){
				update_naps_to_masschange($masschange);
			}			
		}
	}		
}

add_filter('naps_manage_ap_columns', 'masschange_naps_manage_ap_columns');
function masschange_naps_manage_ap_columns($columns){
	
	$new_columns = array();
	foreach($columns as $k => $v){
		
		$new_columns[$k] = $v;
		
		if($k == 'course2'){
			$new_columns['yourcb'] = __('Snap to individual Central Bank rate','pn');
		}
	}
	
	return $new_columns;
}

add_filter('naps_manage_ap_col', 'masschange_naps_manage_ap_col', 10, 3);
function masschange_naps_manage_ap_col($show, $column_name, $item){
global $wpdb;
	
	if($column_name == 'yourcb'){

		$masschanges = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."masschange ORDER BY title ASC");

		$html = '
		<div style="width: 300px;">
			<select name="masschange['. $item->id .']" autocomplete="off" id="naps_masschange_'. $item->id .'" class="naps_masschange" style="width: 300px; display: block; margin: 0 0 10px;"> 
		';
			$enable = 0;
				$html .= '<option value="0" '. selected($item->masschange,0,false) .'>-- '. __('No item','pn') .' --</option>';

				foreach($masschanges as $massch){
					if($item->masschange == $massch->id){
						$enable = 1;
					}
						
					$html .= '<option value="'. $massch->id .'" '. selected($item->masschange,$massch->id,false) .'>'. pn_strip_input($massch->title) .'</option>';
				}

		$style = 'style="display: none;"';	
		if($enable == 1){
			$style = '';
		}
				
		$html .= '
			</select>
			<div id="the_naps_masschange_'. $item->id .'" '. $style .'>
				<input type="text" name="mnums1['. $item->id .']" style="width: 60px; float: left; margin: 2px 5px 0 0;" value="'. pn_strip_input($item->mnums1) .'" />
				<select name="melem1['. $item->id .']" style="float: left;" autocomplete="off">	
					<option value="0" '. selected($item->melem1,0,false) .'>S</option>
					<option value="1" '. selected($item->melem1,1,false) .'>%</option>
				</select>
				<div style="float: left; margin: 5px 10px 0 10px;">=></div>
				<input type="text" name="mnums2['. $item->id .']" style="width: 60px; float: left; margin: 2px 5px 0 0;" value="'. pn_strip_input($item->mnums2) .'" />
				<select name="melem2['. $item->id .']" style="float: left;" autocomplete="off">	
					<option value="0" '. selected($item->melem2,0,false) .'>S</option>
					<option value="1" '. selected($item->melem2,1,false) .'>%</option>
				</select>				
					<div class="premium_clear"></div>
			</div>		
		</div>
		';
		$html .= '</div>';
		return $html;	
	
	}
	
	return $show;
}

if(!has_filter('list_tabs_naps', 'parser_list_tabs_naps')){
	add_filter('list_tabs_naps', 'parser_list_tabs_naps');
	function parser_list_tabs_naps($list_tabs_naps){
		$new_list_tabs_naps = array();
		
		foreach($list_tabs_naps as $k => $v){
			$new_list_tabs_naps[$k] = $v;
			if($k == 'tab2'){
				$new_list_tabs_naps['tab3'] = __('Auto adjust rate','pn');
			}
		}
		
		return $new_list_tabs_naps;
	}
}

add_action('tab_naps_tab3', 'masschange_tab_naps_tab3', 2, 2);
function masschange_tab_naps_tab3($data, $data_id){
global $wpdb;
?>

	<tr>
		<td colspan="3">
			<div class="premium_h3"><?php _e('Individual Central Bank rate','pn'); ?></div>
			<div class="premium_h3submit">
				<input type="submit" name="" class="button" value="<?php _e('Save'); ?>" />
			</div>
		</td>
	</tr>							
							
	<?php
	$masschanges = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."masschange ORDER BY title ASC");
	?>
	<tr>
		<th><?php _e('Snap to individual Central Bank rate','pn'); ?></th>
		<td>
			<div class="premium_wrap_standart">
				<select name="masschange" id="the_masschange_select" autocomplete="off"> 
					<option value="0" <?php selected(is_isset($data, 'masschange'),0,true); ?>>-- <?php _e('is not installed','pn'); ?> --</option>
						<?php foreach($masschanges as $mass){ ?>
							<option value="<?php echo $mass->id; ?>" <?php selected(is_isset($data, 'masschange'),$mass->id,true); ?>><?php echo $mass->title; ?></option>
						<?php } ?>
				</select>
			</div>
		</td>
		<td></td>
	</tr>

	<tr>
		<th><?php _e('Add to rate','pn'); ?></th>
		<td>
			<div class="premium_wrap_standart">
				<input type="text" name="mnums1" style="width: 100px; float: left; margin: 2px 5px 0 0;" value="<?php echo pn_strip_input(is_isset($data, 'mnums1'));?>" />
				<select name="melem1" style="float: left;" autocomplete="off">	
					<option value="0" <?php selected(is_isset($data, 'melem1'),0);?>>S</option>
					<option value="1" <?php selected(is_isset($data, 'melem1'),1);?>>%</option>
				</select>
					<div class="premium_clear"></div>										
			</div>			
		</td>
		<td>
			<div class="premium_wrap_standart">
				<input type="text" name="mnums2" style="width: 100px; float: left; margin: 2px 5px 0 0;" value="<?php echo pn_strip_input(is_isset($data, 'mnums2'));?>" />
				<select name="melem2" style="float: left;" autocomplete="off">	
					<option value="0" <?php selected(is_isset($data, 'melem2'),0);?>>S</option>
					<option value="1" <?php selected(is_isset($data, 'melem2'),1);?>>%</option>
				</select>
					<div class="premium_clear"></div>											
			</div>	
		</td>
	</tr>
<script type="text/javascript">
jQuery(function(){
	$('#the_masschange_select').change(function(){
		$('#the_parser_select').val('0');
	});
});
</script>
<?php	
} 

add_filter('pn_naps_addform_post', 'masschange_pn_naps_addform_post');
function masschange_pn_naps_addform_post($array){
	$array['masschange'] = $masschange = intval(is_param_post('masschange'));
	if($masschange > 0){
		$array['melem1'] = intval(is_param_post('melem1'));
		$array['mnums1'] = pn_parser_num(is_param_post('mnums1'));			
		$array['melem2'] = intval(is_param_post('melem2'));
		$array['mnums2'] = pn_parser_num(is_param_post('mnums2'));				
	} else {
		$array['melem1'] = 0;
		$array['mnums1'] = 0;			
		$array['melem2'] = 0;
		$array['mnums2'] = 0;				
	}	
	return $array;
}

add_action('pn_naps_edit', 'masschange_pn_naps_edit',1,2);
add_action('pn_naps_add', 'masschange_pn_naps_edit',1,2);
function masschange_pn_naps_edit($data_id, $array){
	if($data_id){
		$masschange = intval(is_param_post('masschange'));
		if($masschange > 0 and function_exists('update_naps_to_masschange')){
			update_naps_to_masschange($masschange);
		}
	}	
}

add_action('admin_menu', 'pn_adminpage_masschange');
function pn_adminpage_masschange(){
global $premiumbox;		
	if(current_user_can('administrator') or current_user_can('pn_masschange')){
		$hook = add_menu_page( __('Individual Central Bank rate','pn'), __('Individual Central Bank rate','pn'), 'read', "pn_masschange", array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('cbr'));	
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_masschange", __('Add rate','pn'), __('Add rate','pn'), 'read', "pn_add_masschange", array($premiumbox, 'admin_temp'));
	}
}
 
add_filter('pn_caps','masschange_pn_caps');
function masschange_pn_caps($pn_caps){
	$pn_caps['pn_masschange'] = __('Use individual Central Bank rate','pn');
	return $pn_caps;
}

global $premiumbox;
$premiumbox->file_include($path.'/add');
$premiumbox->file_include($path.'/list');