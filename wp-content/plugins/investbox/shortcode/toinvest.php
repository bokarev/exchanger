<?php
if( !defined( 'ABSPATH')){ exit(); }

function inex_toinvest_shortcode($atts, $content) { 
global $wpdb, $investbox;

	$temp = '';

	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	$temp .= apply_filters('before_toinvest_page','');
	
	if($user_id){
		
		$temp .= '
		<div class="systemdiv">
			<div class="systemdiv_ins">';
		
			$systems = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."inex_system ORDER BY title ASC, valut ASC");
			foreach($systems as $sys){ /* выводим системы */
				$gid = $sys->gid;
				if($investbox->check_ps($gid)){
				
					$tarifs = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."inex_tars WHERE gid='$gid' AND status='1' ORDER BY cdays ASC");
					if(count($tarifs) > 0){
					
						$item = $tarifs[0];
					
						$maxsum_st = 'style="display: none;"';
						if($item->maxsum > 0){
							$maxsum_st = '';
							$defsumm = is_my_money($item->maxsum);
						} else {
							$defsumm = 10000;
						}

						$minsum_st = 'style="display: none;"';
						if($item->minsum > 0){
							$minsum_st = '';
							if($defsumm < $item->minsum){
								$defsumm = is_my_money($item->minsum);
							}
						}				
					
						$pers = pn_strip_input($item->mpers);
						$dohod = is_my_money($defsumm / 100 * $pers,2);
					
						$before_toinvest_one = '
						<div class="onesystem">
							<div class="onesystem_ins">
								<div class="onesystemtitle">'. __('Invest','inex') .' '. pn_strip_input($sys->title .' '. $sys->valut) .'</div>
						';
					
						$temp .= apply_filters('before_toinvest_one',$before_toinvest_one, $sys);
					
						$temp .= '
						<form action="'. get_ajax_link('invest_createdeposit','get') .'" method="post">
							<div class="onesystembody">
								<div class="inv_table">
									<div class="inv_tr">
										<div class="inv_td labeltd">'. __('Your wallet','inex') .':</div>
										<div class="inv_td"><input type="text" name="account" class="inex_input depositkow" value="" /></div>
										<div class="inv_td helptd">'. __('Wallet we are to send money on','inex') .'</div>
									</div>							
									<div class="inv_tr">
										<div class="inv_td labeltd">'. __('Amount','inex') .':</div>
										<div class="inv_td"><input type="text" name="sum" class="inex_input depositchangesumm" value="'. $defsumm .'" /></div>
										<div class="inv_td helptd">'. __('Amount of deposit','inex') .'
											<span class="change_min_sum_line" '. $minsum_st .'><br /><strong>'. __('min.','inex') .'</strong> <span class="change_min_sum">'. is_my_money($item->minsum) .'</span> '. pn_strip_input($sys->valut).'</span>
											<span class="change_max_sum_line" '. $maxsum_st .'><br /><strong>'. __('max.','inex') .'</strong> <span class="change_max_sum">'. is_my_money($item->maxsum) .'</span> '. pn_strip_input($sys->valut).'</span>
										</div>
									</div>
									<div class="inv_tr">
										<div class="inv_td labeltd">'. __('Period','inex') .':</div>
										<div class="inv_td"><select name="tarid" class="inex_select depositchangetar" autocomplete="off">';
										
										foreach($tarifs as $tar){
											$temp .= '<option value="'. $tar->id .'">'. pn_strip_input($tar->cdays) .' '. __('days','inex') .'</option>';	
										}
										
										$temp .= '
										</select>
										</div>
										<div class="inv_td helptd">'. __('Terms and conditions may vary due to a period','inex') .'</div>
									</div>
									<div class="inv_tr">
										<div class="inv_td labeltd">'. __('Percent','inex') .':</div>
										<div class="inv_td datatd"><span class="changepers">'. $pers .'</span>%</div>
										<div class="inv_td helptd">'. __('Percent for period','inex') .'</div>
									</div>

									<div class="inv_tr">
										<div class="inv_td labeltd">'. __('Profit','inex') .':</div>
										<div class="inv_td datatd"><span class="changedohod">'. $dohod .'</span> '. pn_strip_input($sys->valut) .'</div>
										<div class="inv_td helptd"></div>
									</div>
									<div class="inv_tr">
										<div class="inv_td labeltd"></div>
										<div class="inv_td datatd"><input type="submit" name="submit" class="inex_submit goinvest" value="'. __('Invest','inex') .'" /></div>
										<div class="inv_td helptd"></div>
									</div>								
									';								
							
						$temp .='
								</div>
							</div>
						</form>
						';
						
						foreach($tarifs as $tar){
							$temp .= '
							<div class="tars_'. $tar->id .'" style="display: none;">
								<input type="hidden" name="" class="the_minsum" value="'. is_my_money($tar->minsum) .'" />
								<input type="hidden" name="" class="the_maxsum" value="'. is_my_money($tar->maxsum) .'" />
								<input type="hidden" name="" class="the_pers" value="'. is_my_money($tar->mpers) .'" />
								<input type="hidden" name="" class="the_days" value="'. is_my_money($tar->cdays) .'" />
							</div>
							';
						}
					
						$after_toinvest_one = '
							</div>
						</div>	
						';
					
						$temp .= apply_filters('after_toinvest_one',$after_toinvest_one, $sys);
					
					}
				}
			}	
		
		$temp .= '
			</div>
		</div>';
	
	
		$before_toinvest_form = '
		<div class="toinvest_title">
			<div class="toinvest_titlevn">
				'. __('My deposits','inex') .':
			</div>
		</div>
		<div class="toinvest_table">
			<div class="toinvest_table_ins">
				<table>
		            <tr>
			            <th class="pthall first">№</th>
						<th class="pthall">'. __('Date','inex') .'</th>
			            <th class="pthall">'. __('Amount','inex') .'</th>
						<th class="pthall">'. __('Status','inex') .'</th>
						<th class="pthall"></th>
						<th class="pthall"></th>
						<th class="pthall last"></th>
		            </tr>
		';
		$temp .= apply_filters('before_toinvest_form',$before_toinvest_form);
	
			$perpage = apply_filters('toinvest_form_limit',10);
			$count = $wpdb->query("SELECT id FROM ".$wpdb->prefix."inex_deposit WHERE user_id = '$user_id' AND paystatus = '1'");
			$pagenavi = get_pagenavi_calc($perpage,get_query_var('paged'),$count);
			
			if($count > 0){	
						 
				$datas = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."inex_deposit WHERE user_id = '$user_id' AND paystatus='1' ORDER BY id DESC LIMIT ".$pagenavi['offset'].", ".$pagenavi['limit']);
				
				$long = $investbox->get_option('change', 'long');
				
				$e=0;
                $time = current_time('timestamp');
				$date_format = get_option('date_format');
				$time_format = get_option('time_format');
				foreach ($datas as $item) { $e++;
					if($e%2==0){ $cl='even'; } else { $cl='odd'; }
							
					$link1 = $link2 = '';
					$status = '';
					
					if($item->paystatus == 1 and $item->vipstatus == 0 and strtotime($item->enddate) > $time){
						$status='<b>'. __('Active','inex') .'</b>';
					} elseif($item->paystatus == 1 and $item->vipstatus == 0 and strtotime($item->enddate) < $time){
						if($item->zakstatus == 1){
							$status='<b>'. __('Payment is requested','inex') .'</b>';
						} else {	
							$status='<b>'. __('Completed','inex') .'</b>';
							if($long == 'true'){
								$link1 = '<a href="'. get_ajax_link('invest_longdeposit','get') .'&id='. $item->id .'" class="inex_link" target="_blank">'. __('Prolong','inex') .'</a>';
							}
							$link2 = '<a href="'. get_ajax_link('invest_paydeposit','get') .'&id='. $item->id .'" class="inex_link" target="_blank">'. __('Withdraw','inex') .'</a>';
						}
					} elseif($item->paystatus == 1 and $item->vipstatus == 1){
						$status='<span class="bgreen">'. __('Closed','inex') .'</span>';
					}

                        $toinvest_one = '
                            <tr class="'. $cl .'">
                                <td>'. $item->id .'</td>
								<td>'. get_mytime($item->indate, "{$date_format}, {$time_format}") .'</td>
								<td>'. pn_strip_input($item->insumm .' '. $item->gvalut) .'</td>
								<td>'. $status .'</td>
								<td><a href="#'. $item->id .'" class="inex_link hasinfo">'. __('Info','inex') .'</a></td>
								<td>'. $link1 .'</td>
								<td>'. $link2 .'</td>
                            </tr>
							<tr class="hideinfo thedep_'. $item->id .'">
								<td colspan="7">
									<div class="lineinfodep">
										<span class="infodeplabel">'. __('Amount','inex') .':</span> '. pn_strip_input($item->insumm .' '. $item->gvalut) .'
									</div>
									<div class="lineinfodep">
										<span class="infodeplabel">'. __('PS','inex') .':</span> '. pn_strip_input($item->gtitle .' '. $item->gvalut) .'
									</div>
									<div class="lineinfodep">
										<span class="infodeplabel">'. __('Percent','inex') .':</span> '. is_my_money($item->pers) .'%
									</div>
									<div class="lineinfodep">
										<span class="infodeplabel">'. __('Profit','inex') .':</span> '. pn_strip_input($item->plussumm .' '. $item->gvalut) .'
									</div>
									<div class="lineinfodep">
										<span class="infodeplabel">'. __('Closing date','inex') .':</span> '. get_mytime($item->enddate, "{$date_format}, {$time_format}") .'
									</div>
									<div class="lineinfodep">
										<span class="infodeplabel">'. __('Wallet','inex') .':</span> '. pn_strip_input($item->user_schet) .'
									</div>
									<div class="lineinfodep">
										<span class="infodeplabel">'. __('Total amount','inex') .':</span> '. pn_strip_input($item->outsumm .' '. $item->gvalut) .'
									</div>									
								</td>	
							</tr>	
						';
						
						$temp .= apply_filters('toinvest_one',$toinvest_one,$item);	
	            }				
			}					

		$after_toinvest_form = '
				</table>
            </div>				
		</div>				
		';
		$temp .= apply_filters('after_toinvest_form',$after_toinvest_form);
		
		$temp .= get_pagenavi($pagenavi);			
	
	} else {
		$temp .= '<div class="inex_resultfalse">[ '. __('Page is available after log in only','inex') .' ]</div>';
	}
	
	$temp .= apply_filters('after_toinvest_page','');

	return $temp;
}
add_shortcode('toinvest', 'inex_toinvest_shortcode');