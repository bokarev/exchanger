<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Настройка вывода направлений обмена в XML/TXT файле[:ru_RU][en_US:]Show Exchange direction settings in XML/TXT file[:en_US]
description: [ru_RU:]Настройка вывода направлений обмена в XML/TXT файле[:ru_RU][en_US:]Show Exchange direction settings in XML/TXT file[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_napsfiles');
add_action('pn_bd_activated', 'bd_pn_moduls_active_napsfiles');
function bd_pn_moduls_active_napsfiles(){
global $wpdb;	
	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'show_file'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `show_file` int(1) NOT NULL default '1'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'xml_city'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `xml_city` varchar(150) NOT NULL");
    }	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'xml_manual'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `xml_manual` int(1) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'xml_juridical'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `xml_juridical` int(1) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'xml_show1'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `xml_show1` varchar(50) NOT NULL");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'xml_show2'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `xml_show2` varchar(50) NOT NULL");
    }
	
}
/* end BD */

add_filter('pn_exchange_cat_filters','pn_exchange_cat_filters_txtxml');
function pn_exchange_cat_filters_txtxml($cats){
	
	$cats['files'] = __('Files containing rates needed for monitoring','pn');
	
	return $cats;
}

add_action('pn_adminpage_content_pn_naps','txtxml_pn_admin_content_pn_naps', 0);
function txtxml_pn_admin_content_pn_naps(){
	
	$text = __('Links to files containing rates','pn').': <a href="'. get_site_url_or() .'/request-exporttxt.txt" target="_blank">TXT</a> | <a href="'. get_site_url_or() .'/request-exportxml.xml" target="_blank">XML</a>';
	pn_admin_substrate($text);
	
}

add_action('admin_menu', 'pn_adminpage_txtxml');
function pn_adminpage_txtxml(){
global $premiumbox;	

	add_submenu_page("pn_config", __('TXT and XML export settings','pn'), __('TXT and XML export settings','pn'), 'administrator', "pn_txtxml", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_txtxml', 'def_adminpage_title_pn_txtxml');
function def_adminpage_title_pn_txtxml($page){
	_e('TXT and XML export settings','pn');
} 

add_action('pn_adminpage_content_pn_txtxml','pn_adminpage_content_pn_txtxml');
function pn_adminpage_content_pn_txtxml(){
global $wpdb, $premiumbox;

	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('TXT and XML export settings','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	$options['txt'] = array(
		'view' => 'select',
		'title' => __('TXT file','pn'),
		'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('txtxml','txt'),
		'name' => 'txt',
	);	
	$options['numtxt'] = array(
		'view' => 'input',
		'title' => __('Number of characters in TXT file', 'pn'),
		'default' => $premiumbox->get_option('txtxml','numtxt'),
		'name' => 'numtxt',
	);		
	$options['line1'] = array(
		'view' => 'line',
		'colspan' => 2,
	);			
	$options['xml'] = array(
		'view' => 'select',
		'title' => __('XML file','pn'),
		'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('txtxml','xml'),
		'name' => 'xml',
	);	
	$options['numxml'] = array(
		'view' => 'input',
		'title' => __('Number of characters in XML file', 'pn'),
		'default' => $premiumbox->get_option('txtxml','numxml'),
		'name' => 'numxml',
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_txtxml_option', $options);
} 

add_action('premium_action_pn_txtxml','premium_action_pn_txtxml');
function premium_action_pn_txtxml(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator'));
	
	$options = array('txt','xml');			
	foreach($options as $key){
		$val = pn_strip_input(is_param_post($key));
		$premiumbox->update_option('txtxml',$key, $val);
	}
				
	$options = array('numtxt','numxml');		
	foreach($options as $key){
		$value = intval(is_param_post($key));
		if($value < 0){ $value = 6; }
		$premiumbox->update_option('txtxml',$key, $value);
	}				

	do_action('pn_txtxml_option_post');
	
	$url = admin_url('admin.php?page=pn_txtxml&reply=true');
	wp_redirect($url);
	exit;
}

add_filter('account_list_pages','pn_account_list_pages');
function pn_account_list_pages($account_list_pages){	
global $wpdb, $premiumbox;	
	
	$lang_data = '';
	if(is_ml()){
		$lang = get_locale();
		$lang_key = get_lang_key($lang);
		$lang_data = '?lang='.$lang_key;
	}
	
	$show_files = apply_filters('show_txtxml_files', 1);
	if($show_files == 1){
		if($premiumbox->get_option('txtxml','xml') == 1){
			$account_list_pages['exportxml'] = array(
				'title' => __('XML file containing rates','pn'),
				'url' => get_site_url_or() .'/request-exportxml.xml'.$lang_data,
				'type' => 'target_link',
			);
		}	
		if($premiumbox->get_option('txtxml','txt') == 1){
			$account_list_pages['exporttxt'] = array(
				'title' => __('TXT file containing rates','pn'),
				'url' => get_site_url_or() .'/request-exporttxt.txt'.$lang_data,
				'type' => 'target_link',
			);
		}
	}
	
	return $account_list_pages;
}

add_action('myaction_request_exporttxt','def_myaction_request_exporttxt');
function def_myaction_request_exporttxt(){
global $wpdb, $premiumbox;	
	
	header("Content-type: text/txt; charset=utf-8");
	
	if($premiumbox->get_option('up_mode') == 1){
		_e('Maintenance','pn');
		exit;	
	}

	$show_files = apply_filters('show_txtxml_files', 1, 'txt');
	if($show_files == 1){
		if($premiumbox->get_option('txtxml','txt') == 1){
			
			$show_data = pn_exchanges_output('files');
			if($show_data['mode'] == 1){
				
				$num = intval($premiumbox->get_option('txtxml','numtxt'));

				$v = get_valuts_data();

				$where = get_naps_where("files");
				$naps = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where ORDER BY site_order1 ASC");
				foreach($naps as $ob){ 
					$output = apply_filters('get_naps_output', 1, $ob, 'txtxml');
					if($output == 1){
						$valid1 = $ob->valut_id1;
						$valid2 = $ob->valut_id2;
						
						if(isset($v[$valid1]) and isset($v[$valid2])){
							$vd1 = $v[$valid1];
							$vd2 = $v[$valid2];

							echo is_xml_value($vd1->xml_value) .';'. is_xml_value($vd2->xml_value) .';'. is_my_money($ob->curs1,$num).';'.is_my_money($ob->curs2,$num).';'. get_naps_reserv($vd2->valut_reserv , $num, $ob) .";\n";
						}
					}
				} 
			
			}
			
			exit;
		} 
	} 

	_e('File containing rates disabled','pn');	
}

add_action('myaction_request_exportxml','def_myaction_request_exportxml');
function def_myaction_request_exportxml(){
global $wpdb, $premiumbox;
	header("Content-Type: text/xml; charset=utf-8");
	
	if($premiumbox->get_option('up_mode') != 1){
	?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
	<?php
	$show_files = apply_filters('show_txtxml_files', 1, 'xml');
	if($show_files == 1){
		if($premiumbox->get_option('txtxml','xml') == 1){
			
			$show_data = pn_exchanges_output('files');
			if($show_data['mode'] == 1){
			
				$num = intval($premiumbox->get_option('txtxml','numxml'));

				$v = get_valuts_data();

				$where = get_naps_where("files");
				$naps = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where ORDER BY site_order1 ASC");
	?>
	<rates>
	<?php		
			foreach($naps as $ob){	
				$output = apply_filters('get_naps_output', 1, $ob, 'txtxml');	
				if($output == 1){
					$valid1 = $ob->valut_id1;
					$valid2 = $ob->valut_id2;
					
					if(isset($v[$valid1]) and isset($v[$valid2])){
						$vd1 = $v[$valid1];
						$vd2 = $v[$valid2];
						$m_out = is_extension_name(is_isset($ob,'m_out'));
						
						$lines = array();
						$lines['from'] = is_xml_value($vd1->xml_value);
						$lines['to'] = is_xml_value($vd2->xml_value);
						$lines['in'] = is_my_money($ob->curs1,$num);
						$lines['out'] = is_my_money($ob->curs2,$num);
						$lines['amount'] = get_naps_reserv($vd2->valut_reserv , $num, $ob);
						
						$min1 = is_my_money($ob->com_box_min1,$num);
						$min2 = is_my_money($ob->com_box_min2,$num);
						if($min1 > 0){
							$minfee = $min1;
							$vtype = pn_strip_input($vd1->vtype_title);
						} else {
							$minfee = $min2;
							$vtype = pn_strip_input($vd2->vtype_title);					
						}
						if($minfee > 0){
							$lines['minfee'] = $minfee .' '. $vtype;
						}						
						
						$fromfee = array();
						if($ob->com_box_summ1){ 
							$fromfee[] = is_my_money($ob->com_box_summ1,$num) . ' '. pn_strip_input($vd1->vtype_title);
						}
						if($ob->com_box_pers1){
							$fromfee[] = is_my_money($ob->com_box_pers1,$num).' %';
						}
						if(count($fromfee) > 0){
							$lines['fromfee'] = join(', ',$fromfee);
						}	

						$tofee = array();
						if($ob->com_box_summ2){ 
							$tofee[] = is_my_money($ob->com_box_summ2,$num) . ' '. pn_strip_input($vd2->vtype_title);
						}
						if($ob->com_box_pers2){
							$tofee[] = is_my_money($ob->com_box_pers2,$num).' %';
						}
						if(count($tofee) > 0){
							$lines['tofee'] = join(', ',$tofee);
						}
						
						$lines['minamount'] = is_my_money($ob->minsumm1,$num).' '.pn_strip_input($vd1->vtype_title);
						 
						$params = array();
						$xml_manual = intval(is_isset($ob,'xml_manual'));
						if($xml_manual == 0){
							if(!$m_out or function_exists('is_enable_paymerchant') and !is_enable_paymerchant($m_out)){
								$params[] = 'manual';
							}
						} elseif($xml_manual == 2){
							$params[] = 'manual';
						} 
				
						$xml_juridical = intval(is_isset($ob,'xml_juridical'));
						if($xml_juridical){
							$params[] = 'juridical';
						}
						if(count($params) > 0){
							$lines['param'] = join(', ',$params);
						} 						
						$lines = apply_filters('file_xml_lines', $lines, $ob, $vd1, $vd2);

						$cities = pn_strip_input(is_isset($ob,'xml_city'));
						$num_item = 1;
						$cities_arr = array();
						if($cities){
							$cities_arr = explode(',',$cities);
							$num_item = count($cities_arr);
						}
						$r=0;
						while($r++<$num_item){
							$s = $r-1;
							$city = trim(is_isset($cities_arr,$s));
							if($city){
								$lines['city'] = $city;
							}
		?>
			<item>
				<?php foreach($lines as $line_key => $line_value){ ?>
				<<?php echo $line_key; ?>><?php echo $line_value; ?></<?php echo $line_key; ?>>	
				<?php } ?>
			</item>
		<?php		
						}
					}
				}
			}
	?>
	</rates>
	<?php	
			exit;
			}
		} 
	} 	
	}	
	?>
	<error><?php _e('File containing rates disabled','pn'); ?></error>	
	<?php
} 

add_action('list_tabs_naps', 'napsfiles_list_tabs_naps'); 
function napsfiles_list_tabs_naps($list_tabs_naps){
	$list_tabs_naps['tab12'] = __('TXT and XML export settings','pn');
	return $list_tabs_naps;
}

add_action('tab_naps_tab12', 'napsfiles_tab_naps_tab12', 1, 2);
function napsfiles_tab_naps_tab12($data, $data_id){
?>
	<tr>
		<th><?php _e('Show in file','pn'); ?></th>
		<td colspan="2">
			<div class="premium_wrap_standart">
				<select name="show_file" autocomplete="off">
					<?php 
					$show_file = is_isset($data, 'show_file'); 
					if(!is_numeric($show_file)){ $show_file = 1; }
					?>						
					<option value="1" <?php selected($show_file,1); ?>><?php _e('Yes','pn');?></option>
					<option value="0" <?php selected($show_file,0); ?>><?php _e('No','pn');?></option>
					<option value="2" <?php selected($show_file,2); ?>><?php _e('According to shedule','pn');?></option>						
				</select>
			</div>
		</td>
	</tr>
	<tr>
		<th><?php _e('Show exchange direction on shedule','pn'); ?></th>
		<td>
			<?php
				$xml_show = explode(':',is_isset($data, 'xml_show1'));
				$h1 = is_isset($xml_show, 0);
				$m1 = is_isset($xml_show, 1);
			?>
			<div class="premium_wrap_standart">
				<select name="xml_show_h1" style="width: 50px;" autocomplete="off">	
					<?php
					$r=-1;
					while($r++<23){
					?>
						<option value="<?php echo $r; ?>" <?php selected($h1,$r);?>><?php echo zeroise($r,2); ?></option>
					<?php } ?>
				</select>
					:
				<select name="xml_show_m1" style="width: 50px;" autocomplete="off">	
					<?php
					$r=-1;
					while($r++<59){
					?>
						<option value="<?php echo $r; ?>" <?php selected($m1,$r);?>><?php echo zeroise($r,2); ?></option>
					<?php } ?>
				</select>										
			</div>
		</td>
		<td>
			<?php
				$xml_show = explode(':',is_isset($data, 'xml_show2'));
				$h2 = is_isset($xml_show, 0);
				$m2 = is_isset($xml_show, 1);
			?>		
			<div class="premium_wrap_standart">
				<select name="xml_show_h2" style="width: 50px;" autocomplete="off">	
					<?php
					$r=-1;
					while($r++<23){
					?>
						<option value="<?php echo $r; ?>" <?php selected($h2,$r);?>><?php echo zeroise($r,2); ?></option>
					<?php } ?>
				</select>	
					:
				<select name="xml_show_m2" style="width: 50px;" autocomplete="off">	
					<?php
					$r=-1;
					while($r++<59){
					?>
						<option value="<?php echo $r; ?>" <?php selected($m2,$r);?>><?php echo zeroise($r,2); ?></option>
					<?php } ?>
				</select>				
			</div>
		</td>		
	</tr>	
	<tr>
		<th><?php _e('City where exchanges with cash is available','pn'); ?></th>
		<td colspan="2">
			<div class="premium_wrap_standart">
				<input type="text" name="xml_city" style="width: 200px;" value="<?php echo pn_strip_input(is_isset($data, 'xml_city')); ?>" />
			</div>
		</td>
	</tr>	
	<tr>
		<th><?php _e('Other options','pn'); ?></th>
		<td>
			<div class="premium_wrap_standart">
				<select name="xml_manual" autocomplete="off">
					<?php 
						$xml_manual = is_isset($data, 'xml_manual'); 
					?>						
					<option value="0" <?php selected($xml_manual,0); ?>><?php _e('Default exchange mode','pn');?></option>
					<option value="1" <?php selected($xml_manual,1); ?>><?php _e('Auto exchange mode (forced)','pn');?></option>
					<option value="2" <?php selected($xml_manual,2); ?>><?php _e('Manual exchange mode (forced)','pn');?></option>
				</select>
			</div>
		</td>
		<td>
			<div class="premium_wrap_standart">
				<select name="xml_juridical" autocomplete="off">
					<?php 
						$xml_juridical = is_isset($data, 'xml_juridical'); 
					?>						
					<option value="0" <?php selected($xml_juridical,0); ?>><?php _e('Individual transfer','pn');?></option>
					<option value="1" <?php selected($xml_juridical,1); ?>><?php _e('Legal entity transfer','pn');?></option>
				</select>
			</div>
		</td>		
	</tr>	
<?php	
} 

add_filter('pn_naps_addform_post', 'napsfiles_pn_naps_addform_post');
function napsfiles_pn_naps_addform_post($array){
	$array['show_file'] = intval(is_param_post('show_file'));
	$array['xml_show1'] = intval(is_param_post('xml_show_h1')) .':'. intval(is_param_post('xml_show_m1'));
	$array['xml_show2'] = intval(is_param_post('xml_show_h2')) .':'. intval(is_param_post('xml_show_m2'));
	$array['xml_city'] = pn_strip_input(is_param_post('xml_city'));
	$array['xml_manual'] = intval(is_param_post('xml_manual'));
	$array['xml_juridical'] = intval(is_param_post('xml_juridical'));
	return $array;
}

add_filter('get_naps_where', 'napsfiles_get_naps_where', 10, 2);
function napsfiles_get_naps_where($where, $place){
	if($place == 'files'){
		$where .= "AND show_file IN('1','2') ";	
	}
	return $where;
} 

function get_napsfiles_show($ind, $item){
	if($item->show_file == 2){
		$ind = 0;
		$now_time = current_time('timestamp');
		$today = date('d.m.Y',$now_time);
		$yestarday = date('d.m.Y', ($now_time - (24*60*60)));
		$tomorrow = date('d.m.Y', ($now_time + (24*60*60)));
		$xml_show = explode(':',is_isset($item, 'xml_show1'));
		$h1 = zeroise(intval(is_isset($xml_show, 0)),2);
		$m1 = zeroise(intval(is_isset($xml_show, 1)),2);
		$xml_show = explode(':',is_isset($item, 'xml_show2'));
		$h2 = zeroise(intval(is_isset($xml_show, 0)),2);
		$m2 = zeroise(intval(is_isset($xml_show, 1)),2);	
		if($h1 > $h2 or $h1 == $h2 and $m1 > $m2){ /* если график работы в течении двух дней */	
			$time1 = strtotime($yestarday .' '. $h1.':'.$m1);
			$time2 = strtotime($today .' '. $h2.':'.$m2);
			$time3 = strtotime($today .' '. $h1.':'.$m1);
			$time4 = strtotime($tomorrow .' '. $h2.':'.$m2);
			if($now_time >= $time1 and $now_time < $time2 or $now_time >= $time3 and $now_time < $time4){
				$ind = 1;
			}
		} elseif($h1 == $h2 and $m1 == $m2){
			$ind = 1;
		}  else { /* если график работы в течении дня */
			$time1 =  strtotime($today.' '. $h1.':'.$m1);
			$time2 =  strtotime($today.' '. $h2.':'.$m2);
			if($now_time >= $time1 and $now_time < $time2){
				$ind = 1;
			}	
		}	
	}	
	return $ind;
}

add_filter('get_naps_output', 'napsfiles_get_naps_output', 10, 3);
function napsfiles_get_naps_output($ind, $item, $place){
	if($ind == 1 and $place == 'txtxml'){
		return get_napsfiles_show($ind, $item);
	}
	return $ind;
}	