<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_quicktags_page','pn_adminpage_quicktags_reserve');
function pn_adminpage_quicktags_reserve(){
?>
edButtons[edButtons.length] = 
new edButton('premium_reserv_form', '<?php _e('Reserve','pn'); ?>','[reserve ids="" notids="" line="2"]');
<?php	
}

function reserve_shortcode($atts, $content) {
global $wpdb, $premiumbox;

	$temp = '';				

	$ids = explode(',',is_isset($atts,'ids'));
	$notids = explode(',',is_isset($atts,'notids'));
	$line = intval(is_isset($atts,'line'));
	
	$valuts = list_view_valuts($ids, $notids);
	if(count($valuts) > 0){
	$temp .= '
	<div class="reserv_wrap">
		<div class="reserv_block">
			<div class="reserv_many">
				<div class="reserv_many_ins">';
				
					$r=0; 
					foreach($valuts as $valut){ $r++; 
						$temp .= '
						<div class="one_reserv"> 
							<div class="one_reserv_ico" style="background: url('. $valut['logo'] .') no-repeat center center;"></div>
							<div class="one_reserv_block">
								<div class="one_reserv_title">
									'. $valut['title'] .'
								</div>
								<div class="one_reserv_sum">
									'. is_out_sum($valut['reserv'], $valut['decimal'], 'reserv') .'
								</div>
							</div>
								<div class="clear"></div>
						</div>';
						if($line > 0 and $r%$line==0){ $temp .= '<div class="clear"></div>'; }
					} 
	$temp .= '			
						<div class="clear"></div>
				</div>	
			</div>	
		</div>
	</div>'; 
	} 	

	return $temp;
}
add_shortcode('reserve', 'reserve_shortcode');