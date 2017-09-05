<?php 
if( !defined( 'ABSPATH')){ exit(); } 

/* шорткод партнерского кабинета */
function paccount_page_shortcode($atts, $content) {
global $wpdb, $premiumbox;
	
	$temp = '';
	
	$url = get_site_url_ml() .'/';
	$temp .= apply_filters('before_paccount_page','');
	
	$pages = $premiumbox->get_option('partners','pages');
	if(!is_array($pages)){ $pages = array(); }	
	if(in_array('paccount',$pages)){	
	
		$ui = wp_get_current_user();
		$user_id = intval($ui->ID);	
		
		if($user_id){

			$date_reg = get_mytime($ui->user_registered, get_option('date_format'));
			$plinks = get_partner_plinks($user_id);
			$referals = $wpdb->query("SELECT ID FROM ".$wpdb->prefix."users WHERE ref_id = '$user_id'");
			$count_obmen = get_user_count_refobmen($user_id);
			$obmen_sum = get_user_sum_refobmen($user_id);
			if($plinks > 0 and $count_obmen > 0){
				$cti = is_my_money(($count_obmen/$plinks)*100,2);
			} elseif($count_obmen > 0){
				$cti = $count_obmen*100;
			} else {
				$cti = 0;
			}	
			
			$balans = get_partner_money($user_id);
			$minpay = is_my_money($premiumbox->get_option('partners','minpay'));
			$balans2 = get_partner_money_now($user_id);
			$dbalans = 0;
			if($balans2 >= $minpay){
				$dbalans = $balans2;
			} 		 
			$z_all = get_partner_earn_all($user_id); 
			$pay1 = $wpdb->get_var("SELECT SUM(pay_sum_or) FROM ".$wpdb->prefix."payoutuser WHERE user_id='$user_id' AND status='1'");
			$pay2 = $wpdb->get_var("SELECT SUM(pay_sum_or) FROM ".$wpdb->prefix."payoutuser WHERE user_id='$user_id' AND status='0'");
			$pay1 = is_my_money($pay1);
			$pay2 = is_my_money($pay2);

			$stand_refid = stand_refid();
			$cur_type = cur_type();
		
			$topstat = '
			<table>
				<tr>
					<th>'. __('Identification number','pn') .'</th>
					<td>'. $user_id .'</td>
				</tr>
				<tr>
					<th>'. __('Registration date','pn') .'</th>
					<td>'.	$date_reg	.'</td>
				</tr>
				<tr>
					<th>'. __('E-mail','pn') .'</th>
					<td>'. is_email($ui->user_email) .'</td>
				</tr>
				<tr>
					<th>'. __('Your aff. percentage','pn') .'</th>
					<td>'. is_out_sum(get_user_pers_refobmen($user_id), 12, 'all') .'%</td>
				</tr>		
			</table>
			';
		
			$stat = '
			<table>
				<tr>
					<th>'. __('Visitors','pn') .'</th>
					<td>'. $plinks .'</td>
				</tr>
				<tr>
					<th>'. __('Count amount of referrals','pn') .'</th>
					<td>'. $referals .'</td>
				</tr>
				<tr>
					<th>'. __('Exchanges','pn') .'</th>
					<td>'. $count_obmen .'</td>
				</tr>
				<tr>
					<th>'. __('Amount of exchanges','pn') .'</th>
					<td>'. is_out_sum($obmen_sum, 12, 'all') .' '. $cur_type .'</td>
				</tr>						
				<tr>
					<th>'. __('CTR','pn') .'</th>
					<td>'. $cti .' %</td>
				</tr>
				<tr>
					<th>'. __('All time earned','pn') .'</th>
					<td>'. is_out_sum($z_all, 12, 'all') .' '. $cur_type .'</td>
				</tr>
				<tr>
					<th>'. __('Waiting payments','pn') .'</th>
					<td>'. is_out_sum($pay2, 12, 'all') .' '. $cur_type .'</td>
				</tr>
				<tr>
					<th>'. __('Paid in total','pn') .'</th>
					<td>'. is_out_sum($pay1, 12, 'all') .' '. $cur_type .'</td>
				</tr>
				<tr>
					<th>'. __('Current balance','pn') .'</th>
					<td>'. is_out_sum($balans2, 12, 'all') .' '. $cur_type .'</td>
				</tr>
				<tr>
					<th>'. __('Available for payout','pn') .'</th>
					<td>'. is_out_sum($dbalans, 12, 'all') .' '. $cur_type .'</td>
				</tr>		
			</table>
			';
			
			$promo = '
				<h3>'. __('Promotional materials','pn') .'</h3>
				<p>'. __('Ad text with a link that you place anywhere (on your website, in blogs, forums, FAQs, social networks, bookmarking services) will transit users to this website, and you will receive a guaranteed rewards for your referrals.','pn') .'</p>
				<p>'. __('Below are the basic options of promotional materials with your affiliate link included. You can use any text links or use ours. All you need is to copy the selected code, place it on your website and start making profit.','pn') .'</p>				
				<h4>'. __('Affiliate link','pn') .':</h4>			
				<p><textarea class="ptextareaus" onclick="this.select()">'. $url.'?'. $stand_refid .'='. $user_id .'</textarea></p>			
				<h4>'. __('Affiliate link in the HTML-code (for posting on websites and blogs)','pn') .':</h4>
				<p><textarea class="ptextareaus" onclick="this.select()"><a target="_blank" href="'.$url.'?'. $stand_refid .'='. $user_id .'">'. __('Currency exchange','pn') .'</a></textarea></p>			
				<h4>'. __('Hidden affiliate link in the HTML-code (for posting on websites and blogs)','pn') .':</h4>
				<p><textarea class="ptextareaus" onclick="this.select()"><a target="_blank" href="'.$url.'" onclick="this.href='.$url.'?'. $stand_refid .'='. $user_id .'">'. __('Currency exchange','pn') .'</a></textarea></p>				
				<h4>'. __('BBCode affiliate link (for posting on forums)','pn') .':</h4>	
				<p><textarea class="ptextareaus" onclick="this.select()">[url="'.$url.'?'. $stand_refid .'='. $user_id .'"]'. __('Currency exchange','pn') .'[/url]</textarea></p>   
			';	

			$array = array(
				'[topstat]' => $topstat,
				'[stat]' => $stat,
				'[promo]' => $promo,
			);	
		
			$temp_form = '
				<div class="stattablediv">
					<div class="stattablediv_ins">
						[topstat]
					</div>
				</div>
				<div class="statuserdiv">
					<div class="statuserdiv_ins">
						<div class="statuserdiv_title">
							<div class="statuserdiv_title_ins">
								'. __('Statistics','pn') .'
							</div>
						</div>	
					
						[stat]
					</div>
				</div>
				
				<div class="promouserdiv">
					<div class="promouserdiv_ins">
					[promo]
					</div>
				</div>
			';
		
			$temp_form = apply_filters('paccount_form_temp',$temp_form);
			$temp .= get_replace_arrays($array, $temp_form);
		
		} else {
			$temp .= '<div class="resultfalse">'. __('Error! Page is available for authorized users only','pn') .'</div>';
		}
	
	} else {
		$temp .= '<div class="resultfalse">'. __('Error! Page is unavailable','pn') .'</div>';
	}	
	
	$after = apply_filters('after_paccount_page','');
	$temp .= $after;

	return $temp;
}
add_shortcode('paccount_page', 'paccount_page_shortcode');
/* end партнерского кабинета */