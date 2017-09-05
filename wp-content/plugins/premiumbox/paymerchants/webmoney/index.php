<?php
/*
title: [ru_RU:]Webmoney[:ru_RU][en_US:]Webmoney[:en_US]
description: [ru_RU:]авто выплаты Webmoney[:ru_RU][en_US:]Webmoney automatic payouts[:en_US]
version: 1.2
*/

if(!class_exists('paymerchant_webmoney')){
	class paymerchant_webmoney extends AutoPayut_Premiumbox{

		function __construct($file, $title)
		{
			$map = array(
				'AP_WEBMONEY_BUTTON', 'AP_WEBMONEY_WMID', 'AP_WEBMONEY_KEYPATH', 
				'AP_WEBMONEY_KEYPASS', 'AP_WEBMONEY_WMZ_PURSE', 'AP_WEBMONEY_WMR_PURSE',
				'AP_WEBMONEY_WME_PURSE', 'AP_WEBMONEY_WMU_PURSE', 'AP_WEBMONEY_WMB_PURSE',
				'AP_WEBMONEY_WMY_PURSE', 'AP_WEBMONEY_WMG_PURSE', 'AP_WEBMONEY_WMX_PURSE', 'AP_WEBMONEY_WMK_PURSE',
			);
			parent::__construct($file, $map, $title, 'AP_WEBMONEY_BUTTON');
			
			add_action('get_paymerchant_admin_options_'.$this->name, array($this, 'get_paymerchant_admin_options'), 10, 2);			
			add_filter('paymerchants_settingtext_'.$this->name, array($this, 'paymerchants_settingtext'));
			add_filter('reserv_place_list',array($this,'reserv_place_list'));
			add_filter('update_valut_autoreserv', array($this,'update_valut_autoreserv'), 10, 3);
			add_filter('update_naps_reserv', array($this,'update_naps_reserv'), 10, 4);
			add_action('paymerchant_action_bid_'.$this->name, array($this,'paymerchant_action_bid'),99,3);
		}	
		
		function get_paymerchant_admin_options($options, $data){
			
			if(isset($options['bottom_title'])){
				unset($options['bottom_title']);
			}			

			$html_request = '';
			$num_request = intval(get_option('old_webmoney_id'));
			$new_request = intval(is_isset($data, 'num_request'));
			if($num_request > 0 and $new_request < 1){
				$html_request = ' ('. $num_request . ')';
			}
			
			if(isset($options['checkpay'])){
				unset($options['checkpay']);
			}			
			
			$options[] = array(
				'view' => 'input',
				'title' => __('Current payment ID','pn') . $html_request,
				'default' => is_isset($data, 'num_request'),
				'name' => 'num_request',
				'work' => 'int',
			);

			$options['bottom_title'] = array(
				'view' => 'h3',
				'title' => '',
				'submit' => __('Save','pn'),
				'colspan' => 2,
			);								
			
			return $options;
		}			
		
		function paymerchants_settingtext(){
			$text = '| <span class="bred">'. __('Config file is not set up','pn') .'</span>';
			if(
				is_deffin($this->m_data,'AP_WEBMONEY_WMID') 
				and is_deffin($this->m_data,'AP_WEBMONEY_KEYPASS')  
			){
				$text = '';
			}
			
			return $text;
		}

		function reserv_place_list($list){
			
			$purses = array(
				$this->name.'_1' => is_deffin($this->m_data,'AP_WEBMONEY_WMZ_PURSE'),
				$this->name.'_2' => is_deffin($this->m_data,'AP_WEBMONEY_WMR_PURSE'),
				$this->name.'_3' => is_deffin($this->m_data,'AP_WEBMONEY_WME_PURSE'),
				$this->name.'_4' => is_deffin($this->m_data,'AP_WEBMONEY_WMU_PURSE'),
				$this->name.'_5' => is_deffin($this->m_data,'AP_WEBMONEY_WMB_PURSE'),
				$this->name.'_6' => is_deffin($this->m_data,'AP_WEBMONEY_WMY_PURSE'),
				$this->name.'_7' => is_deffin($this->m_data,'AP_WEBMONEY_WMG_PURSE'),
				$this->name.'_8' => is_deffin($this->m_data,'AP_WEBMONEY_WMX_PURSE'),
				$this->name.'_9' => is_deffin($this->m_data,'AP_WEBMONEY_WMK_PURSE'),
			);
			
			foreach($purses as $k => $v){
				$v = trim($v);
				if($v){
					$list[$k] = 'Webmoney '. $v;
				}
			}
			
			return $list;						
		}

		function update_valut_autoreserv($ind, $key, $valut_id){
			if($ind == 0){
				if(strstr($key, $this->name.'_')){				
					$purses = array(
						$this->name.'_1' => is_deffin($this->m_data,'AP_WEBMONEY_WMZ_PURSE'),
						$this->name.'_2' => is_deffin($this->m_data,'AP_WEBMONEY_WMR_PURSE'),
						$this->name.'_3' => is_deffin($this->m_data,'AP_WEBMONEY_WME_PURSE'),
						$this->name.'_4' => is_deffin($this->m_data,'AP_WEBMONEY_WMU_PURSE'),
						$this->name.'_5' => is_deffin($this->m_data,'AP_WEBMONEY_WMB_PURSE'),
						$this->name.'_6' => is_deffin($this->m_data,'AP_WEBMONEY_WMY_PURSE'),
						$this->name.'_7' => is_deffin($this->m_data,'AP_WEBMONEY_WMG_PURSE'),
						$this->name.'_8' => is_deffin($this->m_data,'AP_WEBMONEY_WMX_PURSE'),
						$this->name.'_9' => is_deffin($this->m_data,'AP_WEBMONEY_WMK_PURSE'),
					);					
					$purse = trim(is_isset($purses, $key));
					if($purse){						
						try{					
							$oWMXI = new WMXI( PN_PLUGIN_DIR .'paymerchants/'. $this->name .'/classed/wmxi.crt', 'UTF-8' );
							$oWMXI->Classic( is_deffin($this->m_data,'AP_WEBMONEY_WMID'), array( 'pass' => is_deffin($this->m_data,'AP_WEBMONEY_KEYPASS'), 'file' => is_deffin($this->m_data,'AP_WEBMONEY_KEYPATH') ) );
						
							$aResponse = $oWMXI->X9( is_deffin($this->m_data,'AP_WEBMONEY_WMID') )->toObject();
							$server_reply = is_isset($aResponse, 'retval');
							if($server_reply == '0'){
								
								if(isset($aResponse->purses->purse)){
									$wmid_purses = $aResponse->purses->purse;
								
									$rezerv = '-1';
								
									foreach($wmid_purses as $wp){
										if( $wp->pursename == $purse ){
											$rezerv = (string)$wp->amount;
											break;
										}
									}						
								
									if($rezerv != '-1'){
										pm_update_vr($valut_id, $rezerv);
									}
								
								}

							} 						
						}
						catch (Exception $e)
						{							
						} 										
						return 1;
					}				
				}
			}
			
			return $ind;						
		}	
		
		function update_naps_reserv($ind, $key, $naps_id, $naps){
			
			$ind = intval($ind);
			if(!$ind){
				if(strstr($key, $this->name.'_')){
					$purses = array(
						$this->name.'_1' => is_deffin($this->m_data,'AP_WEBMONEY_WMZ_PURSE'),
						$this->name.'_2' => is_deffin($this->m_data,'AP_WEBMONEY_WMR_PURSE'),
						$this->name.'_3' => is_deffin($this->m_data,'AP_WEBMONEY_WME_PURSE'),
						$this->name.'_4' => is_deffin($this->m_data,'AP_WEBMONEY_WMU_PURSE'),
						$this->name.'_5' => is_deffin($this->m_data,'AP_WEBMONEY_WMB_PURSE'),
						$this->name.'_6' => is_deffin($this->m_data,'AP_WEBMONEY_WMY_PURSE'),
						$this->name.'_7' => is_deffin($this->m_data,'AP_WEBMONEY_WMG_PURSE'),
						$this->name.'_8' => is_deffin($this->m_data,'AP_WEBMONEY_WMX_PURSE'),
						$this->name.'_9' => is_deffin($this->m_data,'AP_WEBMONEY_WMK_PURSE'),
					);
					$purse = trim(is_isset($purses, $key));
					if($purse){
						
						try{
								
							$oWMXI = new WMXI( PN_PLUGIN_DIR .'paymerchants/'. $this->name .'/classed/wmxi.crt', 'UTF-8' );
							$oWMXI->Classic( is_deffin($this->m_data,'AP_WEBMONEY_WMID'), array( 'pass' => is_deffin($this->m_data,'AP_WEBMONEY_KEYPASS'), 'file' => is_deffin($this->m_data,'AP_WEBMONEY_KEYPATH') ) );
						
							$aResponse = $oWMXI->X9( is_deffin($this->m_data,'AP_WEBMONEY_WMID') )->toObject();
							$server_reply = is_isset($aResponse, 'retval');
							if($server_reply == '0'){
								
								if(isset($aResponse->purses->purse)){
									$wmid_purses = $aResponse->purses->purse;
								
									$rezerv = '-1';
								
									foreach($wmid_purses as $wp){
										if( $wp->pursename == $purse ){
											$rezerv = (string)$wp->amount;
											break;
										}
									}						
								
									if($rezerv != '-1'){
										pm_update_nr($naps_id, $rezerv);
									}
								
								}

							} 						
						
						}
						catch (Exception $e)
						{
							
						} 				
						
						return 1;
					}
				}
			}
			
			return $ind;
		}		

		function paymerchant_action_bid($item, $place, $naps_data){
			global $wpdb;
			
			$item_id = is_isset($item,'id');
			if($item_id){

				$paymerch_data = get_paymerch_data($this->name);
				
				$au_filter = array(
					'error' => array(),
					'pay_error' => 0,
					'enable' => 1,
				);
				$au_filter = apply_filters('autopayment_filter', $au_filter, $this->name, $item, $place, $naps_data, $paymerch_data);				
			
				$error = (array)$au_filter['error'];
				$pay_error = intval($au_filter['pay_error']);
				$trans_id = 0;			
			
				if($au_filter['enable'] == 1){
					
					$vtype = mb_strtoupper($item->vtype2);
					$vtype = str_replace(array('WMZ','USD'),'Z',$vtype);
					$vtype = str_replace(array('RUR','WMR','RUB'),'R',$vtype);
					$vtype = str_replace(array('WME','EUR'),'E',$vtype);
					$vtype = str_replace(array('WMU','UAH'),'U',$vtype);
					$vtype = str_replace(array('WMB','BYR'),'B',$vtype);
					$vtype = str_replace(array('WMY','UZS'),'Y',$vtype);
					$vtype = str_replace(array('WMG','GLD'),'G',$vtype);
					$vtype = str_replace(array('WMX','BTC'),'X',$vtype);
					$vtype = str_replace(array('WMK','KZT'),'K',$vtype);
					
					$enable = array('Z','R','E','U','B','Y','G','X','K');
					if(!in_array($vtype, $enable)){
						$error[] = __('Wrong currency code','pn'); 
					}						
						
					$account = $item->account2;
					$account = mb_strtoupper($account);
					if(!is_wm_purse($account, $vtype)){
						$error[] = __('Client wallet type does not match with currency code','pn');
					}		
					
					$site_purse = '';
					if($vtype == 'Z'){
						$site_purse = is_deffin($this->m_data,'AP_WEBMONEY_WMZ_PURSE');
					} elseif($vtype == 'R'){
						$site_purse = is_deffin($this->m_data,'AP_WEBMONEY_WMR_PURSE');
					} elseif($vtype == 'E'){
						$site_purse = is_deffin($this->m_data,'AP_WEBMONEY_WME_PURSE');
					} elseif($vtype == 'U'){
						$site_purse = is_deffin($this->m_data,'AP_WEBMONEY_WMU_PURSE');
					} elseif($vtype == 'B'){
						$site_purse = is_deffin($this->m_data,'AP_WEBMONEY_WMB_PURSE');
					} elseif($vtype == 'Y'){
						$site_purse = is_deffin($this->m_data,'AP_WEBMONEY_WMY_PURSE');
					} elseif($vtype == 'G'){
						$site_purse = is_deffin($this->m_data,'AP_WEBMONEY_WMG_PURSE');
					} elseif($vtype == 'X'){
						$site_purse = is_deffin($this->m_data,'AP_WEBMONEY_WMX_PURSE');
					} elseif($vtype == 'K'){
						$site_purse = is_deffin($this->m_data,'AP_WEBMONEY_WMK_PURSE');
					} 
					
					$site_purse = mb_strtoupper($site_purse);
					if(!is_wm_purse($site_purse, $vtype)){
						$error[] = __('Your account set on website does not match with currency code','pn');
					}	

					$sum = is_my_money(is_paymerch_sum($this->name, $item, $paymerch_data), 2);			
					
					if(count($error) == 0){

						$result = update_bids_meta($item->id, 'ap_status', 1);
						update_bids_meta($item->id, 'ap_status_date', current_time('timestamp'));				
						if($result){					
					
							$notice = get_text_paymerch($this->name, $item);
							if(!$notice){ $notice = sprintf(__('ID order %s','pn'), $item->id); }
							$notice = trim(pn_maxf($notice,245));
							
							if(is_file(PN_PLUGIN_DIR .'paymerchants/'. $this->name .'/classed/wmxi.crt') and is_deffin($this->m_data,'AP_WEBMONEY_KEYPASS') and is_deffin($this->m_data,'AP_WEBMONEY_KEYPATH')){
							
								$num_request = intval(is_isset($paymerch_data, 'num_request'));
								$num_request = $num_request + 1;
								$merch_data = get_option('paymerch_data');
								if(!is_array($merch_data)){ $merch_data = array(); }
								$merch_data[$this->name]['num_request'] = $num_request;
								update_option('paymerch_data', $merch_data);						
							
								try{
							
									$oWMXI = new WMXI( PN_PLUGIN_DIR .'paymerchants/'. $this->name .'/classed/wmxi.crt', 'UTF-8' );
									$oWMXI->Classic( is_deffin($this->m_data,'AP_WEBMONEY_WMID'), array( 'pass' => is_deffin($this->m_data,'AP_WEBMONEY_KEYPASS'), 'file' => is_deffin($this->m_data,'AP_WEBMONEY_KEYPATH') ) );
								
									$aResponse = $oWMXI->X2($num_request, $site_purse, $account, $sum , 0, '', $notice, 0, 0)->toObject();
									$server_reply = is_isset($aResponse, 'retval');
								
									if($server_reply != '0'){
										$error[] = is_isset($aResponse, 'retdesc').' Code:'.$server_reply;
										$pay_error = 1;
									} 
								
								}
								catch (Exception $e)
								{
									$error[] = $e;
									$pay_error = 1;
								} 
							
							} else {
								$error[] = 'Error interfaice';
								$pay_error = 1;
							}
						
						} else {
							$error[] = 'Database error';
						}					
									
					}
					
					if(count($error) > 0){
						
						if($pay_error == 1){
							update_bids_meta($item->id, 'ap_status', 0);
							update_bids_meta($item->id, 'ap_status_date', current_time('timestamp'));
						}					
						
						$error_text = join('<br />',$error);
						
						do_action('paymerchant_error', $this->name, $error, $item_id, $place);
						
						if($place == 'admin'){
							pn_display_mess(__('Error!','pn') . $error_text);
						} else {
							send_paymerchant_error($item_id, $error_text);
						}
						
					} else {
						
						$params = array(
							'soschet' => $site_purse,
							'trans_out' => $trans_id,
						);
						the_merchant_bid_status('success', $item_id, 'user', 1, $place, $params);						
						
						if($place == 'admin'){
							pn_display_mess(__('Automatic payout is done','pn'),__('Automatic payout is done','pn'),'true');
						}  
						
					}
				
				}
			}
		}				
		
	}
}

global $premiumbox;
$path = get_extension_file(__FILE__);
$premiumbox->file_include($path . '/classed/wmxicore.class');	
$premiumbox->file_include($path . '/classed/wmxi.class');
$premiumbox->file_include($path . '/classed/wmxilogin.class');
$premiumbox->file_include($path . '/classed/wmxiresult.class');
$premiumbox->file_include($path . '/classed/wmxilogger.class');
$premiumbox->file_include($path . '/classed/wminterfaces.class');
$premiumbox->file_include($path . '/classed/wmsigner.class');

new paymerchant_webmoney(__FILE__, 'Webmoney');