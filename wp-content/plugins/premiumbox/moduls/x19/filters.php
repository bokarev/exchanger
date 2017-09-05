<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('tab_naps_tab8','tab_naps_tab_x19',99,2);
function tab_naps_tab_x19($data, $data_id){
	?>
	<tr>
		<th><?php _e('X19','pn'); ?></th>
		<td colspan="2">
			<div class="premium_wrap_standart">
				<?php 
					$x19mod = intval(get_naps_meta($data_id, 'x19mod')); 
				?>									
				<select name="x19mod" autocomplete="off">
					<?php
					$array = array(
						'0' => __('No item','pn'),
						'1' => __('Cash at the office','pn') .' -> '. __('Webmoney','pn'),
						'2' => __('Bank account','pn') .' -> '. __('Webmoney','pn'),
						'3' => __('Bank card','pn') .' -> '. __('Webmoney','pn'),
						'4' => __('Money transfer system','pn') .' -> '. __('Webmoney','pn'),
						'5' => __('SMS','pn') .' -> '. __('Webmoney','pn'),
						'6' => __('Webmoney','pn') .' -> '. __('Cash at the office','pn'),
						'7' => __('Webmoney','pn') .' -> '. __('Bank account','pn'),
						'8' => __('Webmoney','pn') .' -> '. __('Bank card','pn'),
						'9' => __('Webmoney','pn') .' -> '. __('Money transfer system','pn'),
						'10' => __('PayPal','pn') .' -> '. __('Webmoney','pn'),
						'11' => __('Skrill (Moneybookers)','pn') .' -> '. __('Webmoney','pn'),
						'12' => __('QIWI','pn') .' -> '. __('Webmoney','pn'),
						'13' => __('Yandex money','pn') .' -> '. __('Webmoney','pn'),
						'14' => __('EasyPay','pn') .' -> '. __('Webmoney','pn'),
						'15' => __('Webmoney','pn') .' -> '. __('PayPal','pn'),
						'16' => __('Webmoney','pn') .' -> '. __('Skrill (Moneybookers)','pn'),
						'17' => __('Webmoney','pn') .' -> '. __('QIWI','pn'),
						'18' => __('Webmoney','pn') .' -> '. __('Yandex money','pn'),
						'19' => __('Webmoney','pn') .' -> '. __('EasyPay','pn'),
						'20' => __('Webmoney','pn') .' -> '. __('Webmoney','pn'),
						'21' => __('Webmoney','pn') .' -> '. __('Bitcoin','pn'),
					);
						
					foreach($array as $key => $arr){
					?>
						<option value="<?php echo $key; ?>" <?php selected($x19mod,$key); ?>><?php echo $arr; ?></option>
					<?php } ?>
				</select>
			</div>
		</td>
	</tr>	
	<?php
} 

add_action('pn_naps_edit_before','pn_naps_edit_x19'); 
add_action('pn_naps_add','pn_naps_edit_x19');
function pn_naps_edit_x19($data_id){
	$x19mod = intval(is_param_post('x19mod'));
	update_naps_meta($data_id, 'x19mod', $x19mod);
}

add_filter('form_bids_account1','x19_form_bids_account1',99,3);
function x19_form_bids_account1($show, $naps, $vd){
	if($show == 0){
		$x19mod = intval(is_isset($naps,'x19mod'));
		if($x19mod > 0){
			$arrwm = array(6,7,8,9,15,16,17,18,19,20,21);
			if(in_array($x19mod,$arrwm)){
				$show = 1;
			}
		}
	}
		return $show;
}

add_filter('form_bids_account2','x19_form_bids_account2',99,3);
function x19_form_bids_account2($show, $naps, $vd){
	if($show == 0){
		$x19mod = intval(is_isset($naps,'x19mod'));
		if($x19mod > 0){
			$arrwm = array(6,7,8,9,15,16,17,18,19,21);
			if(!in_array($x19mod,$arrwm)){
				$show = 1;
			}
		}
	}
		return $show;
}

add_filter('cf_auto_form_value','x19_cf_auto_form_value',1,4);
function x19_cf_auto_form_value($cauv,$value,$item,$naps){
global $wpdb;
	
	$cf_auto = $item->cf_auto;
	$x19mod = intval(is_isset($naps,'x19mod'));
	
	$error = 0;
	
	if($cf_auto == 'first_name' or $cf_auto == 'last_name'){ 
		if(!$value){
			$error = 1;					
		} 
	} 	
	
	if($x19mod == 1 or $x19mod == 6){
		if($cf_auto == 'user_passport'){ 
			if(!$value){
				$error = 1;					
			} 
		} 
	}

	if($error == 1){
		$cauv = array(
			'error' => 1,
			'error_text' => __('field is not filled in','pn')
		);
	}			
	
	return $cauv;
}

add_filter('error_bids','x19_error_bids',99,7);
function x19_error_bids($error_bids, $account1, $account2, $naps, $vd1, $vd2, $auto_data){
	
	$x19mod = intval(is_isset($naps,'x19mod'));
	
	if($error_bids['error'] == 0){
		if($x19mod > 0){
			
			$arrwm1 = array(6,7,8,9,15,16,17,18,19,20,21);
			if(in_array($x19mod,$arrwm1)){
				$wmkow = $account1;
				$wmkow2 = $account2;
				$wtype = 1;
			} else {
				$wmkow = $account2;
				$wmkow2 = $account1;
				$wtype = 2;
			}
				
			$pursetype = 'WM'.mb_strtoupper(mb_substr($wmkow,0,1));
			$result = x19_info_for_wm($wmkow);
			if(isset($result['wmid'])){
					
				$wmid = pn_maxf(pn_strip_input($result['wmid']),250);	
					
				if($x19mod == 20){
					$result = x19_info_for_wm($wmkow2);
					if(isset($result['wmid'])){
						$wmid2 = $result['wmid'];
						if($wmid != $wmid2){
							$error_bids['error'] = 1;
							$error_bids['error_text'][] = __('This wallet is of other WMID','pn');
							$error_bids['account1_error'] = 1;
							$error_bids['account2_error'] = 1;
						}
					} else {
						if($wtype==1){
							$error_bids['error'] = 1;
							$error_bids['account1_error'] = 1;
							$error_bids['account1_error_text'] = __('Invalid account Send','pn');
						} else {
							$error_bids['error'] = 1;
							$error_bids['account2_error'] = 1;
							$error_bids['account2_error_text'] = __('Invalid account Receive','pn');				
						}						
					}
				} else {					
					
					$amount = 100; 
					$fname = is_isset($auto_data,'last_name');
					$iname = is_isset($auto_data,'first_name');
					$obmen_pasport = is_isset($auto_data,'user_passport');
					$pnomer = '';
					$bank_name = '';
					$bank_account = '';
					$card_number = ''; 
					$emoney_name = '';
					$emoney_id = '';
					$phone = '';
					$crypto_name='';
					$crypto_address='';
					
					if($x19mod == 1){ /* Наличные в офисе -> WM */
						$type = 1;
						$direction = 2;
						$pnomer = $obmen_pasport;
					} elseif($x19mod == 2){ /* Банковский счет -> WM */ 
						$type = 3;
						$direction = 2;						
						$bank_name = ctv_ml($vd1->psys_title);
						$bank_account = $account1;					
					} elseif($x19mod == 3){ /* Банковская карта -> WM */ 
						$type = 4;
						$direction = 2;						
						$bank_name = ctv_ml($vd1->psys_title);
						$card_number = $account1;					
					} elseif($x19mod == 4){ /* Системы денежных переводов -> WM */
						$type = 2;
						$direction = 2;					
					} elseif($x19mod == 5){ /* SMS -> WM */
						$type = 6;
						$direction = 2;
						$phone = x19_phone($account1);	
					} elseif($x19mod == 6){ /* WM -> Наличные в офисе */
						$type = 1;
						$direction = 1;
						$pnomer = $obmen_pasport;
					} elseif($x19mod == 7){ /* WM -> Банковский счет */
						$type = 3;
						$direction = 1;
						$bank_name = ctv_ml($vd2->psys_title);
						$bank_account = $account2;					
					} elseif($x19mod == 8){ /* WM -> Банковская карта */
						$type = 4;
						$direction = 1;
						$bank_name = ctv_ml($vd2->psys_title);
						$card_number = $account2;					
					} elseif($x19mod == 9){ /* WM -> Системы денежных переводов */
						$type = 2;
						$direction = 1;					
					} elseif($x19mod == 10){ /* PayPal -> WM */
						$type = 5;
						$direction = 2; 
						$emoney_name = 'paypal.com';
						$emoney_id = $account1;					
					} elseif($x19mod == 11){ /* Skrill (Moneybookers) -> WM */
						$type = 5;
						$direction = 2; 
						$emoney_name = 'moneybookers.com';
						$emoney_id = $account1;					
					} elseif($x19mod == 12){ /* QIWI Кошелёк -> WM */
						$type = 5;
						$direction = 2; 
						$emoney_name = 'qiwi.ru';
						$emoney_id = x19_phone($account1);					
					} elseif($x19mod == 13){ /* Яндекс.Деньги -> WM */
						$type = 5;
						$direction = 2; 
						$emoney_name = 'money.yandex.ru';
						$emoney_id = $account1;				
					} elseif($x19mod == 14){ /* EasyPay -> WM */
						$type = 5;
						$direction = 2; 
						$emoney_name = 'easypay.by';
						$emoney_id = $account1;	
					} elseif($x19mod == 15){ /* WM -> PayPal */
						$type = 5;
						$direction = 1; 
						$emoney_name = 'paypal.com';
						$emoney_id = $account2;					
					} elseif($x19mod == 16){ /* WM -> Skrill (Moneybookers) */
						$type = 5;
						$direction = 1; 
						$emoney_name = 'moneybookers.com';
						$emoney_id = $account2;					
					} elseif($x19mod == 17){ /* WM -> QIWI Кошелёк */
						$type = 5;
						$direction = 1; 
						$emoney_name = 'qiwi.ru';
						$emoney_id = x19_phone($account2);					
					} elseif($x19mod == 18){ /* WM -> Яндекс.Деньги */
						$type = 5;
						$direction = 1; 
						$emoney_name = 'money.yandex.ru';
						$emoney_id = $account2;					
					} elseif($x19mod == 19){ /* WM -> EasyPay */
						$type = 5;
						$direction = 1; 
						$emoney_name = 'easypay.by';
						$emoney_id = $account2;
					} elseif($x19mod == 21){ /* WM -> Bitcoin */
						$type = 8;
						$direction = 1; 
						$crypto_name = 'bitcoin';
						$crypto_address = $schet2;						
					}
					
					try{
						$object = WMXI_X19();
						if(is_object($object)){
							$aResponse = $object->X19($type, $direction, $pursetype, $amount, $wmid, $pnomer, $fname, $iname, $bank_name, $bank_account, $card_number, $emoney_name, $emoney_id, $phone, $crypto_name, $crypto_address)->toArray();
							$retval = is_isset($aResponse,'retval');
						} else {
							$retval = 1000;
							$aResponse['retdesc'] = 'Ошибка интерфейса';
						}
					} catch(Exception $e){

						$retval = 1000;
						$aResponse['retdesc'] = 'Ошибка интерфейса';
					
					}
						
					if($retval == 0){
						/* 
							$error_bids['error'] = 1;
							$error_bids['error_text'] = $aResponse['retdesc']; 
						*/					
					} elseif($retval == 404){
						$error_bids['error'] = 1;
						$error_bids['error_text'][] = $aResponse['retdesc'];
					} else {
						$error_bids['error'] = 1;
						$error_bids['error_text'][] = $aResponse['retdesc'];
					}
				
				}
				
			} else {
				if($wtype==1){
					$error_bids['error'] = 1;
					$error_bids['account1_error'] = 1;
					$error_bids['account1_error_text'] = __('Invalid account Send','pn');
				} else {
					$error_bids['error'] = 1;
					$error_bids['account2_error'] = 1;
					$error_bids['account2_error_text'] = __('Invalid account Receive','pn');				
				}
			}
		}
	}
		
	return $error_bids;
}