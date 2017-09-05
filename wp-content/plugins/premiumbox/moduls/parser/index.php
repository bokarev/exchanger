<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Парсер курсов валют[:ru_RU][en_US:]Exchange rates parser[:en_US]
description: [ru_RU:]Парсер курсов валют[:ru_RU][en_US:]Exchange rates parser[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_parser');
function bd_pn_moduls_active_parser(){
global $wpdb;	
	
/*
parser - id парсера
nums1 - число 1
elem1 - 0-сумма, 1-процент
nums2 - число 1
elem2 - 0-сумма, 1-процент
*/	
	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'parser'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `parser` bigint(20) NOT NULL default '0'");
    }		
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'nums1'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `nums1` varchar(50) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'elem1'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `elem1` int(2) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'nums2'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `nums2` varchar(50) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'elem2'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `elem2` int(2) NOT NULL default '0'");
    }	
}
/* end BD */

add_action('pn_adminpage_content_pn_naps', 'parser_pn_adminpage_content_pn_naps');
add_action('pn_adminpage_content_pn_bc_adjs', 'parser_pn_adminpage_content_pn_naps');
function parser_pn_adminpage_content_pn_naps(){
?>	
<style>
.column-parser{ width: 300px!important; }
</style>
<script type="text/javascript">
jQuery(function($){
	$('.naps_parser').change(function(){
		var id = $(this).attr('id').replace('naps_parser_','');
		var vale = $(this).val();
		if(vale > 0){
			$('#the_naps_parser_'+id).show();
			$('#the_naps_masschange_'+id).hide();
			$('#naps_masschange_'+id).val('0');
		} else {
			$('#the_naps_parser_'+id).hide();
		}
	});			
});
</script>
<?php
}

add_action('pn_naps_save', 'parser_pn_naps_save');
function parser_pn_naps_save(){
global $wpdb;	
	
	if(isset($_POST['parser']) and is_array($_POST['parser'])){ /* parser */		
		foreach($_POST['parser'] as $id => $parser_id){
						
			$id = intval($id);
			$parser = intval($parser_id);
			$elem1 = intval($_POST['elem1'][$id]);
			$nums1 = pn_parser_num($_POST['nums1'][$id]);			
			$elem2 = intval($_POST['elem2'][$id]);
			$nums2 = pn_parser_num($_POST['nums2'][$id]);
						
			$array = array();
			if($parser > 0){
				$array['parser'] = $parser;
				$array['elem1'] = $elem1;
				$array['nums1'] = $nums1;			
				$array['elem2'] = $elem2;
				$array['nums2'] = $nums2;
			} else {
				$array['parser'] = 0;
				$array['elem1'] = 0;
				$array['nums1'] = 0;			
				$array['elem2'] = 0;
				$array['nums2'] = 0;							
			}
								
			$wpdb->update($wpdb->prefix.'naps', $array, array('id'=>$id));
						
		}			
	}	

	update_naps_to_parser();
}

add_action('pn_bcadjs_save', 'parser_pn_bcadjs_save');
function parser_pn_bcadjs_save(){
global $wpdb;	
	if(isset($_POST['parser']) and is_array($_POST['parser'])){ /* parser */		
		foreach($_POST['parser'] as $id => $parser_id){
			
			$id = intval($id);
			$parser = intval($parser_id);
			$elem1 = intval($_POST['elem1'][$id]);
			$nums1 = pn_parser_num($_POST['nums1'][$id]);			
			$elem2 = intval($_POST['elem2'][$id]);
			$nums2 = pn_parser_num($_POST['nums2'][$id]);
						
			$array = array();
			if($parser > 0){
				$array['parser'] = $parser;
				$array['elem1'] = $elem1;
				$array['nums1'] = $nums1;			
				$array['elem2'] = $elem2;
				$array['nums2'] = $nums2;
			} else {
				$array['parser'] = 0;
				$array['elem1'] = 0;
				$array['nums1'] = 0;			
				$array['elem2'] = 0;
				$array['nums2'] = 0;							
			}					
			$wpdb->update($wpdb->prefix.'bcbroker_naps', $array, array('id'=>$id));	
			
		}			
	}	
}

add_filter('naps_manage_ap_columns', 'parser_naps_manage_ap_columns');
function parser_naps_manage_ap_columns($columns){
	
	$new_columns = array();
	foreach($columns as $k => $v){
		
		$new_columns[$k] = $v;
		
		if($k == 'course2'){
			$new_columns['parser'] = __('Auto adjust rate','pn');
		}
	}
	
	return $new_columns;
}

add_filter('bcadjs_manage_ap_columns', 'parser_bcadjs_manage_ap_columns');
function parser_bcadjs_manage_ap_columns($columns){
	
	$new_columns = array();
	foreach($columns as $k => $v){
		
		$new_columns[$k] = $v;
		
		if($k == 'standart'){
			$new_columns['parser'] = __('Standard rate auto adjust','pn');
		}
	}
	
	return $new_columns;
}

add_filter('naps_manage_ap_col', 'parser_naps_manage_ap_col', 10, 3);
add_filter('bcadjs_manage_ap_col', 'parser_naps_manage_ap_col', 10, 3);
function parser_naps_manage_ap_col($show, $column_name, $item){
	if($column_name == 'parser'){
		$en_parsers = get_list_parsers('work','[para] [[birg]]');			
			
		$html = '
		<div style="width: 300px;">
		';
			
		$html .= '
		<select name="parser['. $item->id .']" autocomplete="off" id="naps_parser_'. $item->id .'" class="naps_parser" style="width: 300px; display: block; margin: 0 0 10px;"> 
		';
			$enable = 0;
				$html .= '<option value="0" '. selected($item->parser,0,false) .'>-- '. __('No item','pn') .' --</option>';
			if(is_array($en_parsers)){
				foreach($en_parsers as $parser_key => $parser_data){
					if($item->parser == $parser_key){
						$enable = 1;
					}
						
					$html .= '<option value="'. $parser_key .'" '. selected($item->parser,$parser_key,false) .'>'. $parser_data['title'] .'</option>';
				}
			}
				
		$style = 'style="display: none;"';	
		if($enable == 1){
			$style = '';
		}
				
		$html .= '
		</select>
			
		<div id="the_naps_parser_'. $item->id .'" '. $style .'>
			<input type="text" name="nums1['. $item->id .']" style="width: 60px; float: left; margin: 2px 5px 0 0;" value="'. pn_strip_input($item->nums1) .'" />
			<select name="elem1['. $item->id .']" style="float: left;" autocomplete="off">	
				<option value="0" '. selected($item->elem1,0,false) .'>S</option>
				<option value="1" '. selected($item->elem1,1,false) .'>%</option>
			</select>
			<div style="float: left; margin: 5px 10px 0 10px;">=></div>
			<input type="text" name="nums2['. $item->id .']" style="width: 60px; float: left; margin: 2px 5px 0 0;" value="'. pn_strip_input($item->nums2) .'" />
			<select name="elem2['. $item->id .']" style="float: left;" autocomplete="off">	
				<option value="0" '. selected($item->elem2,0,false) .'>S</option>
				<option value="1" '. selected($item->elem2,1,false) .'>%</option>
			</select>				
				<div class="premium_clear"></div>
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

add_action('tab_naps_tab3', 'parser_tab_naps_tab3', 1, 2);
function parser_tab_naps_tab3($data, $data_id){
?>

	<?php
		$parsers = array();
		$parsers[0] = '-- '. __('No item','pn') .' --';
		$en_parsers = get_list_parsers('work','[para] [[birg]]');
		if(is_array($en_parsers)){
			foreach($en_parsers as $key => $val){
				$parsers[$key] = $val['title'];
			}
		}
	?>
		<tr>
			<th><?php _e('Auto adjust rate','pn'); ?></th>
			<td>
				<div class="premium_wrap_standart">
					<select name="parser" id="the_parser_select" autocomplete="off"> 
						<?php foreach($parsers as $parser_key => $parser_title){ ?>
							<option value="<?php echo $parser_key; ?>" <?php selected(is_isset($data, 'parser'),$parser_key,true); ?>><?php echo $parser_title; ?></option>
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
					<input type="text" name="nums1" style="width: 100px; float: left; margin: 2px 5px 0 0;" value="<?php echo pn_strip_input(is_isset($data, 'nums1'));?>" />
					<select name="elem1" style="float: left;" autocomplete="off">	
						<option value="0" <?php selected(is_isset($data, 'elem1'),0);?>>S</option>
						<option value="1" <?php selected(is_isset($data, 'elem1'),1);?>>%</option>
					</select>
						<div class="premium_clear"></div>										
				</div>			
			</td>
			<td>
				<div class="premium_wrap_standart">
					<input type="text" name="nums2" style="width: 100px; float: left; margin: 2px 5px 0 0;" value="<?php echo pn_strip_input(is_isset($data, 'nums2'));?>" />
					<select name="elem2" style="float: left;" autocomplete="off">	
						<option value="0" <?php selected(is_isset($data, 'elem2'),0);?>>S</option>
						<option value="1" <?php selected(is_isset($data, 'elem2'),1);?>>%</option>
					</select>
						<div class="premium_clear"></div>											
				</div>	
			</td>
		</tr>
<script type="text/javascript">
jQuery(function(){
	$('#the_parser_select').change(function(){
		$('#the_masschange_select').val('0');
	});
});
</script>	
<?php	
}

add_filter('pn_naps_addform_post', 'parser_pn_naps_addform_post');
function parser_pn_naps_addform_post($array){

	$array['parser'] = $parser = intval(is_param_post('parser'));
	if($parser > 0){
		$array['elem1'] = intval(is_param_post('elem1'));
		$array['nums1'] = pn_parser_num(is_param_post('nums1'));			
		$array['elem2'] = intval(is_param_post('elem2'));
		$array['nums2'] = pn_parser_num(is_param_post('nums2'));
	} else {
		$array['elem1'] = 0;
		$array['nums1'] = 0;			
		$array['elem2'] = 0;
		$array['nums2'] = 0;				
	}	
	
	return $array;
}

add_action('pn_naps_edit', 'parser_pn_naps_edit',1,2);
add_action('pn_naps_add', 'parser_pn_naps_edit',1,2);
function parser_pn_naps_edit($data_id, $array){
	if($data_id){
		$parser = intval(is_param_post('parser'));
		if($parser > 0 and function_exists('update_naps_to_parser')){
			update_naps_to_parser();
		}
	}	
}

add_action('load_parser_courses','naps_load_parser_courses');
function naps_load_parser_courses(){
	update_naps_to_parser();
}

add_filter('bcparser_def_course', 'parser_bcparser_def_course', 10, 3);
function parser_bcparser_def_course($darr, $item, $options){
	$curs_parser = get_option('curs_parser');
	if(!is_array($curs_parser)){ $curs_parser = array(); }
	$parser = intval($item->parser);
	$nums1 = pn_strip_input($item->nums1);
	$nums2 = pn_strip_input($item->nums2);
	$elem1 = is_my_money($item->elem1);
	$elem2 = is_my_money($item->elem2);
	if($parser > 0){
		$curs_data = is_isset($curs_parser,$parser);
		$curs1 = is_my_money(is_isset($curs_data,'curs1'));
		$curs2 = is_my_money(is_isset($curs_data,'curs2'));	
		$ncurs1 = plus_persent_curs($curs1, $elem1, $nums1);
		$ncurs2 = plus_persent_curs($curs2, $elem2, $nums2);				
		if($ncurs1 > 0){
			$darr = array(
				'course1' => $ncurs1,
				'course2' => $ncurs2,
			);				
		}
	}				
	
	return $darr;
}