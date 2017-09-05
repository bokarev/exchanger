<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_export_direction', 'pn_admin_title_pn_export_direction');
function pn_admin_title_pn_export_direction($page){
	_e('Exchange directions Export/Import','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_export_direction','def_pn_admin_content_pn_export_direction');
function def_pn_admin_content_pn_export_direction(){
global $wpdb;
?>
<?php
if(current_user_can('administrator') or current_user_can('pn_export_exchange_direcions')){
?>
<div class="premium_body">	
    <form method="post" target="_blank" action="<?php pn_the_link_post('export_direction'); ?>">
    <table class="premium_standart_table">
        <tr>
		    <th><?php _e('Select data','pn'); ?></th>
			<td>
			<div class="premium_wrap_standart">
				<div style="font-weight: 500;"><label><input type="checkbox" class="check_all" name="" value="1" /> <?php _e('Check all/Uncheck all','pn'); ?></label></div>
				<?php
				$array = array(
					'valut1' => __('Currency name Sending','pn'),
					'vtype1' => __('Currency code Send','pn'),
					'valut2' => __('Currency name Receiving','pn'),
					'vtype2' => __('Currency code Receive','pn'),
					'curs1' => __('Rate Send','pn'),
					'curs2' => __('Rate Receive','pn'),
					'minsumm1' => __('Min. amount Send','pn'),
					'maxsumm1' => __('Max. amount Send','pn'),
					'minsumm2' => __('Min. amount Receive','pn'),
					'maxsumm2' => __('Max. amount Receive','pn'),					
					'com_box_summ1' => __('Add. Sender fee','pn'),
					'com_box_pers1' => __('Add. Sender fee (%)','pn'),
					'com_box_min1' => __('Minimum fee from sender','pn'),
					'com_box_summ2' => __('Add. Recipient fee','pn'),
					'com_box_pers2' => __('Add. Recipient fee (%)','pn'),
					'com_box_min2' => __('Minimum fee from recipient','pn'),					
					'com_summ1' => __('Fee Send','pn'),
					'com_pers1' => __('Fee (%) Send','pn'),
					'com_summ2' => __('Fee Receive','pn'),
					'com_pers2' => __('Fee (%) Receive','pn'),
					'com_summ1_check' => __('Fee Send for verfified account','pn'),
					'com_pers1_check' => __('Fee (%) Send for verfified account','pn'),
					'com_summ2_check' => __('Fee Receive for verfified account','pn'),
					'com_pers2_check' => __('Fee (%) Receive for verfified account','pn'),					
					'pay_com1' => __('Exchange pays fee Send','pn'),
					'pay_com2' => __('Exchange pays fee Receive','pn'),
					'nscom1' => __('Non standard fee Send','pn'),
					'nscom2' => __('Non standard fee Receive','pn'),					
					'minsumm1com' => __('Min. amount of fee Send','pn'),
					'minsumm2com' => __('Min. amount of fee Receive','pn'),
					'maxsumm1com' => __('Max. amount of fee Send','pn'),
					'maxsumm2com' => __('Max. amount of fee Receive','pn'),
					'naps_status' => __('Activity','pn'),
					'maxnaps' => __('Max. amount for sending','pn'),
					'user_sk' => __('User discount','pn'),
					'max_user_sk' => __('Max. user discount','pn'),
					'partmax' => __('Max. affiliate program percentage','pn'),
					'p_enable' => __('Affiliate payments','pn'),
					'nums1' => __('Add S or % to rate Send','pn'),
					'elem1' => __('Add value to rate Send','pn'),
					'nums2' => __('Add S or % to rate Receive','pn'),
					'elem2' => __('Add value to rate Receive','pn'),
					'mnums1' => __('Individual rate S or % Send','pn'),
					'melem1' => __('Add value to individual rate Send','pn'),
					'mnums2' => __('Individual rate S or % Receive','pn'),
					'melem2' => __('Add value to individual rate Receive','pn'),
					'profit_summ1' => __('Profit amount Send','pn'),
					'profit_pers1' => __('Profit percent Send','pn'),
					'profit_summ2' => __('Profit amount Receive','pn'),
					'profit_pers2' => __('Profit percent Receive','pn'),					
				);
				foreach($array as $key => $val){
				?>
					<div><label><input type="checkbox" name="data[]" class="check_once" value="<?php echo $key; ?>" /> <?php echo $val; ?></label></div>
				<?php } ?>
			</div>
			</td>
		</tr>		
        <tr>
		    <th></th>
			<td>
			<div class="premium_wrap_standart">
			    <input type="submit" name="" class="button" value="<?php _e('Download','pn'); ?>" />
			</div>
			</td>
		</tr>		
    </table>
	</form>	
</div>
<?php } ?>

<?php
if(current_user_can('administrator') or current_user_can('pn_import_exchange_direcions')){
?>
<div class="premium_body">	
    <form method="post" target="_blank" action="<?php pn_the_link_post('import_direction'); ?>" enctype="multipart/form-data">
    <table class="premium_standart_table">
        <tr>
		    <th><?php _e('Import','pn'); ?></th>
			<td>
			<div class="premium_wrap_standart">
				<input type="file" name="importfile" value="" />
			</div>
			</td>
		</tr>		
        <tr>
		    <th></th>
			<td>
			<div class="premium_wrap_standart">
			    <input type="submit" name="" class="button" value="<?php _e('Upload','pn'); ?>" />
			</div>
			</td>
		</tr>		
    </table>
	</form>	
</div>
<?php } ?>

<script type="">
jQuery(function($){ 
	$('.check_all').on('change',function(){
		if($(this).prop('checked')){
			$('.check_once').prop('checked',true);
		} else {
			$('.check_once').prop('checked',false);
		}
	});
});
</script>	
<?php
} 

/* обработка */
add_action('premium_action_export_direction','def_premium_action_export_direction');
function def_premium_action_export_direction(){
global $wpdb;	

	pn_only_caps(array('administrator','pn_export_exchange_direcions'));

	$my_dir = wp_upload_dir();
	$path = $my_dir['basedir'].'/';		
		
	$file = $path.'directionexport-'. date('Y-m-d-H-i') .'.csv';           
	$fs=@fopen($file, 'w');
	
	$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."naps WHERE autostatus='1' ORDER BY id DESC");
	
	$data = is_param_post('data');
		
	$content = '';
		
	$array = array(
		'id' => __('Identifier','pn'),
		'valut1' => __('Currency name Sending','pn'),
		'vtype1' => __('Currency code Send','pn'),
		'valut2' => __('Currency name Receiving','pn'),
		'vtype2' => __('Currency code Receive','pn'),
		'curs1' => __('Rate Send','pn'),
		'curs2' => __('Rate Receive','pn'),
		'minsumm1' => __('Min. amount Send','pn'),
		'maxsumm1' => __('Max. amount Send','pn'),
		'minsumm2' => __('Min. amount Receive','pn'),
		'maxsumm2' => __('Max. amount Receive','pn'),					
		'com_box_summ1' => __('Add. Sender fee','pn'),
		'com_box_pers1' => __('Add. Sender fee (%)','pn'),
		'com_box_min1' => __('Minimum fee from sender','pn'),
		'com_box_summ2' => __('Add. Recipient fee','pn'),
		'com_box_pers2' => __('Add. Recipient fee (%)','pn'),
		'com_box_min2' => __('Minimum fee from recipient','pn'),					
		'com_summ1' => __('Fee Send','pn'),
		'com_pers1' => __('Fee (%) Send','pn'),
		'com_summ2' => __('Fee Receive','pn'),
		'com_pers2' => __('Fee (%) Receive','pn'),
		'com_summ1_check' => __('Fee Send for verfified account','pn'),
		'com_pers1_check' => __('Fee (%) Send for verfified account','pn'),
		'com_summ2_check' => __('Fee Receive for verfified account','pn'),
		'com_pers2_check' => __('Fee (%) Receive for verfified account','pn'),						
		'pay_com1' => __('Exchange pays fee Send','pn'),
		'pay_com2' => __('Exchange pays fee Receive','pn'),
		'nscom1' => __('Non standard fee Send','pn'),
		'nscom2' => __('Non standard fee Receive','pn'),
		'minsumm1com' => __('Min. amount of fee Send','pn'),
		'minsumm2com' => __('Min. amount of fee Receive','pn'),
		'maxsumm1com' => __('Max. amount of fee Send','pn'),
		'maxsumm2com' => __('Max. amount of fee Receive','pn'),
		'naps_status' => __('Activity','pn'),
		'maxnaps' => __('Max. amount for sending','pn'),
		'user_sk' => __('User discount','pn'),
		'max_user_sk' => __('Max. user discount','pn'),
		'partmax' => __('Max. affiliate program percentage','pn'),
		'p_enable' => __('Affiliate payments','pn'),
		'nums1' => __('Add S or % to rate Send','pn'),
		'elem1' => __('Add value to rate Send','pn'),
		'nums2' => __('Add S or % to rate Receive','pn'),
		'elem2' => __('Add value to rate Receive','pn'),
		'mnums1' => __('Individual rate S or % Send','pn'),
		'melem1' => __('Add value to individual rate Send','pn'),
		'mnums2' => __('Individual rate S or % Receive','pn'),
		'melem2' => __('Add value to individual rate Receive','pn'),
		'profit_summ1' => __('Profit amount Send','pn'),
		'profit_pers1' => __('Profit percent Send','pn'),
		'profit_summ2' => __('Profit amount Receive','pn'),
		'profit_pers2' => __('Profit percent Receive','pn'),
	);
		
	$psys_id = array();
	$vtype_id = array();
	$valutsn = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."valuts");
	foreach($valutsn as $valut){
		$psys_id[$valut->id] = $valut->psys_title;
		$vtype_id[$valut->id] = $valut->vtype_title;
	}
		
	if(is_array($data)){
			
		$en = array();
		$csv_title = '';
		$csv_key = '';
		foreach($array as $k => $v){
			if(in_array($k, $data) or $k == 'id'){
				$en[] = $k;
				$csv_title .= '"'.get_cptgn($v).'";';
				$csv_key .= '"'.get_cptgn($k).'";';
			} 
		}	
			
		$content .= $csv_title."\n";
		$content .= $csv_key."\n";

		$qw_arr = array('naps_status','pay_com1','pay_com2','nscom1','nscom2','user_sk');
		$sk_arr = array('elem1','elem2','melem1','melem2');
			
		if(count($en) > 0){

			foreach($items as $item){
				$line = '';
					
				$data_id = $item->id;
				foreach($en as $key){
					$line .= '"';
						
					if(in_array($key,$qw_arr)){
						$line .= get_cptgn(get_exvar(is_isset($item,$key),array(__('no','pn'),__('yes','pn'))));
					} elseif(in_array($key,$sk_arr)){
						$line .= get_cptgn(get_exvar(is_isset($item,$key),array('S','%')));						
					} elseif($key == 'valut1'){
						$line .= get_cptgn(ctv_ml(is_isset($psys_id,is_isset($item,'valut_id1'))));
					} elseif($key == 'valut2'){
						$line .= get_cptgn(ctv_ml(is_isset($psys_id,is_isset($item,'valut_id2'))));
					} elseif($key == 'vtype1'){
						$line .= get_cptgn(is_isset($vtype_id,is_isset($item,'valut_id1')));
					} elseif($key == 'vtype2'){
						$line .= get_cptgn(is_isset($vtype_id,is_isset($item,'valut_id2')));							
					} elseif($key == 'p_enable'){
						$p_enable = get_naps_meta($data_id, 'p_enable');
						if(!is_numeric($p_enable)){ $p_enable = 1; }
						$line .= get_cptgn(get_exvar($p_enable,array(__('no','pn'),__('yes','pn'))));
					} elseif($key == 'partmax'){	
						$line .= rep_dot(is_my_money(get_naps_meta($data_id, 'p_max')));
					} else {
						$line .= rep_dot(is_my_money(is_isset($item,$key)));
					}
						
					$line .= '";';
				}
					
				$line .= "\n";
				$content .= $line;
			}
			
		}
	}
		
	@fwrite($fs, $content);
	@fclose($fs);	
	
	if(is_file($file)) {
		if (ob_get_level()) {
			ob_end_clean();
		}
		header('Content-Type: text/html; charset=CP1251');
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		readfile($file);
		unlink($file);
		exit;
	} else {
		pn_display_mess(__('Error! Unable to create file!','pn'));
	}	
}

/* обработка */
add_action('premium_action_import_direction','def_premium_action_import_direction');
function def_premium_action_import_direction(){
global $wpdb, $premiumbox;	

	pn_only_caps(array('administrator','pn_import_exchange_direcions'));
	
	$premit_ext = array(".csv");
	if(isset($_FILES['importfile']['name'])){
		$ext = strtolower(strrchr($_FILES['importfile']['name'],"."));
		if(in_array($ext,$premit_ext)){
				
			$max_upload_size = wp_max_upload_size();
			if ( ! $max_upload_size ) {
				$max_upload_size = 0;
			}	
			$max_mb = ($max_upload_size / 1024 / 1024);
				
	        if($_FILES["importfile"]["size"] > 0 and $_FILES["importfile"]["size"] < $max_upload_size){
				$tempFile = $_FILES['importfile']['tmp_name'];
				$filename = delsimbol(time() . $_FILES['importfile']['name']);

				$my_dir = wp_upload_dir();
				$path = $my_dir['basedir'].'/';
				$path2 = $my_dir['basedir'].'/import/';
				if(!is_dir($path)){ 
					@mkdir($path , 0777);
				}
				if(!is_dir($path2)){ 
					@mkdir($path2 , 0777);
				}
					
				$targetFile =  str_replace('//','/',$path2) . $filename;
					
				if(move_uploaded_file($tempFile,$targetFile)){
						
					$error = 0;
						
					$array = array(
						'id' => __('Identifier','pn'),
						'valut1' => __('Currency name Sending','pn'),
						'vtype1' => __('Currency code Send','pn'),
						'valut2' => __('Currency name Receiving','pn'),
						'vtype2' => __('Currency code Receive','pn'),
						'curs1' => __('Rate Send','pn'),
						'curs2' => __('Rate Receive','pn'),
						'minsumm1' => __('Min. amount Send','pn'),
						'maxsumm1' => __('Max. amount Send','pn'),
						'minsumm2' => __('Min. amount Receive','pn'),
						'maxsumm2' => __('Max. amount Receive','pn'),					
						'com_box_summ1' => __('Add. Sender fee','pn'),
						'com_box_pers1' => __('Add. Sender fee (%)','pn'),
						'com_box_min1' => __('Minimum fee from sender','pn'),
						'com_box_summ2' => __('Add. Recipient fee','pn'),
						'com_box_pers2' => __('Add. Recipient fee (%)','pn'),
						'com_box_min2' => __('Minimum fee from recipient','pn'),					
						'com_summ1' => __('Fee Send','pn'),
						'com_pers1' => __('Fee (%) Send','pn'),
						'com_summ2' => __('Fee Receive','pn'),
						'com_pers2' => __('Fee (%) Receive','pn'),
						'com_summ1_check' => __('Fee Send for verfified account','pn'),
						'com_pers1_check' => __('Fee (%) Send for verfified account','pn'),
						'com_summ2_check' => __('Fee Receive for verfified account','pn'),
						'com_pers2_check' => __('Fee (%) Receive for verfified account','pn'),								
						'pay_com1' => __('Exchange pays fee Send','pn'),
						'pay_com2' => __('Exchange pays fee Receive','pn'),
						'nscom1' => __('Non standard fee Send','pn'),
						'nscom2' => __('Non standard fee Receive','pn'),
						'minsumm1com' => __('Min. amount of fee Send','pn'),
						'minsumm2com' => __('Min. amount of fee Receive','pn'),
						'maxsumm1com' => __('Max. amount of fee Send','pn'),
						'maxsumm2com' => __('Max. amount of fee Receive','pn'),
						'naps_status' => __('Activity','pn'),
						'maxnaps' => __('Max. amount for sending','pn'),
						'user_sk' => __('User discount','pn'),
						'max_user_sk' => __('Max. user discount','pn'),
						'partmax' => __('Max. affiliate program percentage','pn'),
						'p_enable' => __('Affiliate payments','pn'),
						'nums1' => __('Add S or % to rate Send','pn'),
						'elem1' => __('Add value to rate Send','pn'),
						'nums2' => __('Add S or % to rate Receive','pn'),
						'elem2' => __('Add value to rate Receive','pn'),
						'mnums1' => __('Individual rate S or % Send','pn'),
						'melem1' => __('Add value to individual rate Send','pn'),
						'mnums2' => __('Individual rate S or % Receive','pn'),
						'melem2' => __('Add value to individual rate Receive','pn'),
						'profit_summ1' => __('Profit amount Send','pn'),
						'profit_pers1' => __('Profit percent Send','pn'),
						'profit_summ2' => __('Profit amount Receive','pn'),
						'profit_pers2' => __('Profit percent Receive','pn'),
					);	
					
					$allow_key = array();
					$nochecked_key = array(
						'partmax','p_enable','valut1','vtype1','valut2','vtype2'
					);
					foreach($array as $k => $v){
						if(in_array($k, $nochecked_key)){
							$allow_key[] = $k;
						} else {
							$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE '{$k}'");
							if($query == 1){
								$allow_key[] = $k;
							}
						}
					}
						
					$result = file_get_contents($targetFile);
					$lines = explode("\n",$result);
					if(count($lines) > 2){
							
						$file_map = array();
						$csv_keys = explode(';',is_isset($lines,1));
						foreach($csv_keys as $csv_k => $csv_v){
							$file_map[$csv_k] = rez_exp($csv_v);
						}
							
						$r = -1;
							
						$int_arr = array('id');
						$input_arr = array('valut1','vtype1','valut2','vtype2');
						$qw_arr = array('pay_com1','pay_com2','nscom1','nscom2','naps_status','user_sk','p_enable');
						$uni_arr = array('elem1','elem2','melem1','melem2');
							
						foreach($lines as $line){ $r++;
							if($r > 1){
									
								$line = get_tgncp(trim($line));
								if($line){
									$bd_array = array();
										
									$items = explode(';',$line);
									foreach($items as $item_key => $item){
										$item = rez_exp($item);
											
										$db_key = $file_map[$item_key];
										if(in_array($db_key, $allow_key)){
												
											if(in_array($db_key, $int_arr)){
												$bd_array[$db_key] = intval($item);
											} elseif(in_array($db_key, $input_arr)){
												$bd_array[$db_key] = pn_maxf_mb(pn_strip_input($item),250);													
											} elseif(in_array($db_key, $qw_arr)){
												$bd_array[$db_key] = intval(get_exvar(mb_strtolower($item), array(__('no','pn')=>'0',__('yes','pn')=>'1')));												
											} elseif(in_array($db_key, $uni_arr)){
												$bd_array[$db_key] = intval(get_exvar(mb_strtolower($item), array('S'=>'0','%'=>'1')));						
											} else {
												$bd_array[$db_key] = is_my_money($item);
											}
												
										}
									}	
										
									if(count($bd_array) > 0){
										
										$data_id = intval(is_isset($bd_array,'id'));
										if(isset($bd_array['id'])){
											unset($bd_array['id']);											
										}		

										$xml_value1 = $xml_value2 = '';
											
										$locale = get_locale();

										$tech_name = is_isset($bd_array,'valut1').' '.is_isset($bd_array,'vtype1').' &rarr; '.is_isset($bd_array,'valut2').' '.is_isset($bd_array,'vtype2');
										
										if(isset($bd_array['valut1']) and isset($bd_array['vtype1']) and $bd_array['valut1'] and $bd_array['vtype1']){
												
												$valut1 = $bd_array['valut1'];
												if(is_ml()){
													$valut1_ml = '['. $locale .':]'. $valut1 .'[:'. $locale .']';
												} else {
													$valut1_ml = $valut1;
												}
												
												$psys_data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."psys WHERE psys_title LIKE '%". $valut1_ml ."%' OR psys_title = '$valut1'");
												if(isset($psys_data->id)){
													$bd_array['psys_id1'] = $psys_data->id;
												} else {	
													$up_arr = array(
														'psys_title' => $bd_array['valut1'],
													);
													$wpdb->insert($wpdb->prefix.'psys', $up_arr);
													$bd_array['psys_id1'] = $wpdb->insert_id;
												}												
												
												if(isset($bd_array['vtype1'])){
													$now = $bd_array['vtype1'];
													$vtype_data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."vtypes WHERE vtype_title = '$now'");
													if(isset($vtype_data->id)){
														$bd_array['vtype_id1'] = $vtype_data->id;
													} else {	
														$up_arr = array(
															'vtype_title' => $bd_array['vtype1'],
															'vncurs' => '1',
														);
														$wpdb->insert($wpdb->prefix.'vtypes', $up_arr);
														$bd_array['vtype_id1'] = $wpdb->insert_id;
													}
												}												
												
												if(isset($bd_array['psys_id1']) and isset($bd_array['vtype_id1'])){
													if($bd_array['psys_id1'] and $bd_array['vtype_id1']){
													
														$vals = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE psys_id='{$bd_array['psys_id1']}' AND vtype_id='{$bd_array['vtype_id1']}'");
														if(isset($vals->id)){
														
															$bd_array['valut_id1'] = $vals->id;
															$xml_value1 = $vals->xml_value;
														
														} else {
													
															$uniq = delsimbol(replace_cyr($bd_array['valut1']),2);
															$uniq = unique_xml_value($uniq, 0);
															
															$up_arr = array(
																'psys_title' => $bd_array['valut1'],
																'psys_id' => $bd_array['psys_id1'],
																'vtype_title' => $bd_array['vtype1'],
																'vtype_id' => $bd_array['vtype_id1'],															
																'xml_value' => $uniq,
															);
															$wpdb->insert($wpdb->prefix.'valuts', $up_arr);													
															$bd_array['valut_id1'] = $wpdb->insert_id;
															$xml_value1 = $uniq;
													
														}													
													
													}
												}
																								
										}
										
										if(isset($bd_array['valut1'])){
											unset($bd_array['valut1']);
										}
										if(isset($bd_array['vtype1'])){
											unset($bd_array['vtype1']);											
										}
										if(isset($bd_array['vtype_id1'])){
											unset($bd_array['vtype_id1']);											
										}											
											
										if(isset($bd_array['valut2']) and isset($bd_array['vtype2']) and $bd_array['valut2'] and $bd_array['vtype2']){
												
											$valut2 = $bd_array['valut2'];
											if(is_ml()){
												$valut2_ml = '['. $locale .':]'. $valut2 .'[:'. $locale .']';
											} else {
												$valut2_ml = $valut2;
											}
												
											$psys_data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."psys WHERE psys_title LIKE '%". $valut2_ml ."%' OR psys_title = '$valut2'");
											if(isset($psys_data->id)){
												$bd_array['psys_id2'] = $psys_data->id;
											} else {	
												$up_arr = array(
													'psys_title' => $bd_array['valut2'],
												);
												$wpdb->insert($wpdb->prefix.'psys', $up_arr);
												$bd_array['psys_id2'] = $wpdb->insert_id;
											}												
												
											if(isset($bd_array['vtype2'])){
												$now = $bd_array['vtype2'];
												$vtype_data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."vtypes WHERE vtype_title = '$now'");
												if(isset($vtype_data->id)){
													$bd_array['vtype_id2'] = $vtype_data->id;
												} else {	
													$up_arr = array(
														'vtype_title' => $bd_array['vtype2'],
														'vncurs' => '1',
													);
													$wpdb->insert($wpdb->prefix.'vtypes', $up_arr);
													$bd_array['vtype_id2'] = $wpdb->insert_id;
												}
											}												
												
												if(isset($bd_array['psys_id2']) and isset($bd_array['vtype_id2'])){
													if($bd_array['psys_id2'] and $bd_array['vtype_id2']){
													
														$vals = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts WHERE psys_id='{$bd_array['psys_id2']}' AND vtype_id='{$bd_array['vtype_id2']}'");
														if(isset($vals->id)){
														
															$bd_array['valut_id2'] = $vals->id;
															$xml_value2 = $vals->xml_value;
														
														} else {
													
															$uniq = delsimbol(replace_cyr($bd_array['valut2']),2);
															$uniq = unique_xml_value($uniq, 0);
															
															$up_arr = array(
																'psys_title' => $bd_array['valut2'],
																'psys_id' => $bd_array['psys_id2'],
																'vtype_title' => $bd_array['vtype2'],
																'vtype_id' => $bd_array['vtype_id2'],															
																'xml_value' => $uniq,
															);
															$wpdb->insert($wpdb->prefix.'valuts', $up_arr);													
															$bd_array['valut_id2'] = $wpdb->insert_id;
															$xml_value2 = $uniq;
													
														}
													
													}
												}
																								
										}
											
										if(isset($bd_array['valut2'])){
											unset($bd_array['valut2']);
										}
										if(isset($bd_array['vtype2'])){
											unset($bd_array['vtype2']);											
										}
										if(isset($bd_array['vtype_id2'])){
											unset($bd_array['vtype_id2']);											
										}
										
										$install = 1;
											
										$partmax = 0;
										if(isset($bd_array['partmax'])){
											$partmax = $bd_array['partmax'];
											unset($bd_array['partmax']);
										}	
										$p_enable = 0;
										if(isset($bd_array['p_enable'])){
											$p_enable = $bd_array['p_enable'];
											unset($bd_array['p_enable']);
										}											
											
										if($data_id){
											$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."naps WHERE id='$data_id'");
											if($cc > 0){
												$install = 0;
													
												$wpdb->update($wpdb->prefix.'naps', $bd_array, array('id'=>$data_id));
											} 											
										} 
													
										if($install == 1){
											if(isset($bd_array['psys_id1']) and isset($bd_array['psys_id2']) and isset($bd_array['valut_id1']) and isset($bd_array['valut_id2'])){
												if($bd_array['psys_id1'] and $bd_array['psys_id2'] and $bd_array['valut_id1'] and $bd_array['valut_id2']){
														
													if($xml_value1 and $xml_value2){
														$naps_premalink_temp = apply_filters('naps_premalink_temp','[xmlv1]_to_[xmlv2]');
														$naps_premalink_temp = str_replace('[xmlv1]',$xml_value1,$naps_premalink_temp);
														$naps_premalink_temp = str_replace('[xmlv2]',$xml_value2,$naps_premalink_temp);
														$naps_name = is_naps_premalink($naps_premalink_temp);
														$bd_array['naps_name'] = unique_naps_name($naps_name, 0);
													} 
													$bd_array['tech_name'] = $tech_name;	
													$wpdb->insert($wpdb->prefix.'naps', $bd_array);
													$data_id = $wpdb->insert_id;
														
													if($data_id){
														$list_naps_temp = apply_filters('list_naps_temp',array());
														if(is_array($list_naps_temp)){
															foreach($list_naps_temp as $key => $title){
																$text = $premiumbox->get_option('naps_temp',$key);
																update_naps_txtmeta($data_id, $key, $text);
															}
														}
													}
												}
											}
										}
											
										if($data_id){
											update_naps_meta($data_id, 'p_enable', $p_enable);
											update_naps_meta($data_id, 'p_max', $partmax);
										}
									}
								}
							}
						}							
					} 
						
					if($error == 0){
						if(is_file($targetFile)){
							@unlink($targetFile);
						}
							
						$url = admin_url('admin.php?page=pn_export_direction&reply=true');
						wp_redirect($url);
						exit;	
					}
						
                } else {
					pn_display_mess(__('Error! Error loading file','usve'));
				}
            } else {
				pn_display_mess(__('Error! Incorrect file size!','pn'));
			}					
        } else {
			pn_display_mess(__('Error! Incorrect file format!','pn'));
		}
	} else {
		pn_display_mess(__('Error! File is not selected!','pn'));
	}		
}