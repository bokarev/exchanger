<?php
if( !defined( 'ABSPATH')){ exit(); }

/* добавляем JS */
add_action('siteplace_js','siteplace_js_tarifs');
function siteplace_js_tarifs(){	
?>	
/* tarifs */
jQuery(function($){ 

	$('.javahref').on('click', function(){
	    var the_link = $(this).attr('name');
	    window.location = the_link;
	});

});		
/* end tarifs */
<?php	
} 
/* end добавляем JS */

function tarifs_shortcode($atts, $content) {
global $wpdb, $post;
        
	$temp = '';
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);
		
	$temp .= apply_filters('before_tarifs_page','');		
		
	$show_data = pn_exchanges_output('tar'); 
	if($show_data['text']){
		$temp .= '<div class="resultfalse"><div class="resultclose"></div>'. $show_data['text'] .'</div>';
	}			
	
	if($show_data['mode'] == 1){
		
		$v = get_valuts_data();

		$where = get_naps_where('tar');
		$napobmens = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where ORDER BY site_order1 ASC");
		$naps = $naps2 = array();
		foreach($napobmens as $napob){
			$output = apply_filters('get_naps_output', 1, $napob, 'tar');
			if($output){
				$naps[$napob->valut_id1] = $napob;
				$naps2[$napob->valut_id1][] = $napob;
			}
		}		
		
			$temp .='
			<div class="tarif_div">
				<div class="tarif_div_ins">
			';	
		
			foreach($naps as $data){
				$valut_id = $data->valut_id1;
				if(isset($v[$valut_id])){	
					$vd = $v[$valut_id];
					
					$tarif_title = get_valut_title($vd);
					$tarif_logo = get_valut_logo($vd);
					
					$temp .= '
					<div class="tarif_block">
						<div class="tarif_block_ins">';
					
						$one_tarifs_title = '
						<div class="tarif_title">
							<div class="tarif_title_ins">
								<div class="tarif_title_abs"></div>
								'. $tarif_title .'
							</div>
								<div class="clear"></div>
						</div>
							<div class="clear"></div>';
						$temp .= apply_filters('one_tarifs_title', $one_tarifs_title, $tarif_title, $tarif_logo, $vd);

						$before_one_tarifs_block = '
						<table class="tarif_table">
							<tr>
								<th colspan="2" class="tarif_table_out">'. __('You send','pn') .'</th>
								<th class="tarif_table_arr"></th>
								<th colspan="2" class="tarif_table_in">'. __('You receive','pn') .'</th>
								<th class="tarif_table_reserv">'. __('Reserve','pn') .'</th>
							</tr>							
						';
						$temp .= apply_filters('before_one_tarifs_block',$before_one_tarifs_block, $tarif_title, $vd);

						if(is_array($naps2[$valut_id])){
							$tarifs = $naps2[$valut_id];
							foreach($tarifs as $tar){
								
								$valsid1 = $tar->valut_id1;
								$valsid2 = $tar->valut_id2;
								
								if(isset($v[$valsid1]) and isset($v[$valsid2])){
								
									$vd1 = $v[$valsid1];
									$vd2 = $v[$valsid2];
								
									$curs1 = is_out_sum(get_course1($tar->curs1, $vd1->lead_num, $vd1->valut_decimal, 'tarifs'), $vd1->valut_decimal, 'course');
									$curs2 = is_out_sum(get_course2($vd1->lead_num, $tar->curs1, $tar->curs2, $vd2->valut_decimal, 'tarifs'), $vd2->valut_decimal, 'course');
								
									$reserv = is_out_sum(get_naps_reserv($vd2->valut_reserv , $vd2->valut_decimal, $tar), $vd2->valut_decimal, 'reserv');
								
									$one_tarifs_line = '
									<tr class="javahref" name="'. get_exchange_link($tar->naps_name) .'">
										<td class="tarif_curs_out"><div class="tarif_curs_out_ins">'. $curs1 .'&nbsp;'. is_site_value($vd1->vtype_title) .'</div></td>
										<td class="tarif_curs_title_out">
											<div class="tarif_curs_title_out_ins">
												'. get_valut_title($vd1) .'
											</div>
										</td>
										<td class="tarif_curs_arr">
											<div class="tarif_curs_arr_ins"></div>
										</td>
										<td class="tarif_curs_in"><div class="tarif_curs_in_ins">'. $curs2 .'&nbsp;'. is_site_value($vd2->vtype_title) .'</div></td>
										<td class="tarif_curs_title_in">
											<div class="tarif_curs_title_in_ins">
												'. get_valut_title($vd2) .'
											</div>
										</td>	
										<td class="tarif_curs_reserv">
											<div class="tarif_curs_reserv_ins">'. $reserv .'</div>
										</td>
									</tr>
									';
						    
									$temp .= apply_filters('one_tarifs_line',$one_tarifs_line, $tar, $curs1, $curs2, $reserv, $vd1, $vd2);
							
								}
							}
						}

						$after_one_tarifs_block = '
						</table>';
						$temp .= apply_filters('after_one_tarifs_block',$after_one_tarifs_block, $tarif_title, $vd);
					
					$temp .= '
						</div>
					</div>
					';					
					
				}
			}		
	
		$temp .='
			</div>
		</div>';	
		 
	} 
	
	$temp .= apply_filters('after_tarifs_page','');
	
	return $temp;
}
add_shortcode('tarifs', 'tarifs_shortcode');