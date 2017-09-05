<?php
if( !defined( 'ABSPATH')){ exit(); }

function inex_indeposit_shortcode($atts, $content) { 
global $wpdb, $post, $investbox;

	$temp = '';

	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	$temp .= apply_filters('before_indeposit_page','');
	
	if($user_id){
		
		$temp .= '
		<div class="systemdiv">
			<div class="systemdiv_ins">
		';
		
		$depid = intval(is_param_get('depid'));
		if($depid){
			$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."inex_deposit WHERE id='$depid' AND user_id='$user_id' AND paystatus='0'");
			if(isset($data->id)){
				$data_id = $data->id;
			
				$before_toinvest_one = '
				<div class="onesystem">
					<div class="onesystem_ins">
						<div class="onesystemtitle">'. __('Invest','inex') .' '. pn_strip_input($data->gtitle .' '. $data->gvalut) .'</div>
				';
					
				$temp .= apply_filters('before_toinvest_one',$before_toinvest_one, $data);			
			
				$temp .= '
					<div class="onesystembody" style="display: block;">
						<div class="inv_table">
							<div class="inv_tr">
								<div class="inv_td labeltd">'. __('Your wallet','inex') .':</div>
								<div class="inv_td">'. pn_strip_input($data->user_schet) .'</div>
								<div class="inv_td helptd">'. __('Wallet we are to send money on','inex') .'</div>
							</div>
							<div class="inv_tr">
								<div class="inv_td labeltd">'. __('Amount','inex') .':</div>
								<div class="inv_td">'. is_my_money($data->insumm) .'</div>
								<div class="inv_td helptd">'. __('Amount of deposit','inex') .' ('. pn_strip_input($data->gvalut) .')</div>
							</div>
							<div class="inv_tr">
								<div class="inv_td labeltd">'. __('Period','inex') .':</div>
								<div class="inv_td">'. is_my_money($data->couday) .' '. __('days','inex') .'</div>
								<div class="inv_td helptd"></div>
							</div>
							<div class="inv_tr">
								<div class="inv_td labeltd">'. __('Percent','inex') .':</div>
								<div class="inv_td datatd"><span class="changepers">'. is_my_money($data->pers) .'</span>%</div>
								<div class="inv_td helptd">'. __('Percent for period','inex') .'</div>
							</div>
							<div class="inv_tr">
								<div class="inv_td labeltd">'. __('Profit','inex') .':</div>
								<div class="inv_td datatd"><span class="changedohod">'. is_my_money($data->plussumm) .'</span> '. pn_strip_input($data->gvalut) .'</div>
								<div class="inv_td helptd"></div>
							</div>
							<div class="inv_tr">
								<div class="inv_td labeltd"></div>
								<div class="inv_td datatd">';
								
									$temp .= apply_filters('the_pay_form_deposit','', $data); 

								$temp .= '
								</div>
								<div class="inv_td helptd"></div>
							</div>
						</div>
					</div>
				';
					
				$after_toinvest_one = '
					</div>
				</div>	
				';
				$temp .= apply_filters('after_toinvest_one',$after_toinvest_one, $data);					
					
			} else {
				$temp .= '<div class="inex_resultfalse">[ '. __('Account does not exist','inex') .' ]</div>';
			}
		} else {
			$temp .= '<div class="inex_resultfalse">[ '. __('Account does not exist','inex') .' ]</div>';
		}
		
		$temp .= '
			</div>
		</div>	
		';	

	} else {
		$temp .= '<div class="inex_resultfalse">[ '. __('Page is available after log in only','inex') .' ]</div>';
	}
	
	$temp .= apply_filters('after_indeposit_page','');

	return $temp;
}
add_shortcode('indeposit', 'inex_indeposit_shortcode');