<?php
if( !defined( 'ABSPATH')){ exit(); }

function domacc_page_shortcode($atts, $content) {
global $wpdb;

	$temp = '';
	
    $temp .= apply_filters('before_domacc_page','');
			
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);		
			
	if($user_id){
			
		$vtypes = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."vtypes");	
		$temp .= '
		<div class="domacc_div_wrap">
			<div class="domacc_wrap_ins">
			
				<div class="domacc_div_title">
					<div class="domacc_div_title_ins">
						'. __('Internal account','pn') .'
					</div>
				</div>
		
				<div class="domacc_div">
					<div class="domacc_div_ins">
						';
						
						foreach($vtypes as $vtype){
							
							$temp .= '
							<div class="domacc_line">
								<div class="domacc_label">
									'. is_site_value($vtype->vtype_title) .':
								</div>
								<div class="domacc_val">
									'. get_user_domacc($user_id, $vtype->id) .'
								</div>
									<div class="clear"></div>
							</div>
							';
							
						}
						
						$temp .= '
					</div>
				</div>
		
			</div>
		</div>
		';		

	} else {
		$temp .= '<div class="resultfalse">'. __('Error! You must authorize','pn') .'</div>';
	}
	
    $after = apply_filters('after_domacc_page','');
    $temp .= $after;	
	
	return $temp;
}
add_shortcode('domacc_page', 'domacc_page_shortcode');