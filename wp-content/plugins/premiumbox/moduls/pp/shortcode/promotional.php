<?php
if( !defined( 'ABSPATH')){ exit(); } 

/* добавляем JS */
add_action('siteplace_js','siteplace_js_promotional');
function siteplace_js_promotional(){
?>	
/* partners */
jQuery(function($){ 
    $(".promo_menu li a").on('click',function () {
        if(!$(this).hasClass('act')){
		    $(".pbcontainer, .promo_menu li").removeClass('act');
		    $(".pbcontainer").filter(this.hash).addClass('act');
		    $(this).parents('li').addClass('act');
        }
        return false;
    });
	
    $(".bannerboxlink a").on('click',function() {
        var text = $(this).text();
		var st = $(this).attr('show-title');
		var ht = $(this).attr('hide-title');
		
        if(text == st){
            $(this).html(ht);
        } else {
            $(this).html(st);
        }
        $(this).parents(".bannerboxone").find(".bannerboxtextarea").toggle();
	    $(this).toggleClass('act');

        return false;
    });
});	
/* end partners */	
<?php	
}	
/* end добавляем JS */

/* шорткод баннеров */
function promotional_page_shortcode($atts, $content) {
global $wpdb, $premiumbox;
	
	$temp = '';
	
	$temp .= apply_filters('before_promotional_page','');
	
	$pages = $premiumbox->get_option('partners','pages');
	if(!is_array($pages)){ $pages = array(); }	
	if(in_array('promotional',$pages)){	
	
		$ui = wp_get_current_user();
		$user_id = intval($ui->ID);	
		
		if($user_id){

			$banner_pages = array('banners' => __('Banners','pn'), 'text' => __('Text materials','pn'));
			$banner_pages = apply_filters('banner_pages',$banner_pages);
			
			$promo = pn_strip_input(is_param_get('promo'));
			if(!$promo){ $promo = key($banner_pages); }
			
			$topmenu = '		
			<ul>';
				if(is_array($banner_pages)){
					foreach($banner_pages as $key => $title){
						$cl = '';
						if($key == $promo){
							$cl = 'current';
						}
									
						$topmenu .= '
						<li class="'. $cl .'"><a href="?promo='. $key .'">'. $title .'</a></li>
						';
					}
				}
				$topmenu .= '
					<div class="clear"></div>
			</ul>';
			
			$url = get_site_url_ml() .'/';
			$banners = get_option('banners');
			
			$nbanners = apply_filters('pp_banners',array());
			$nbanners = (array)$nbanners;
		
			$html = '';
			$second_menu = '';
		
			if($promo == 'text'){ /* текст */
				
				foreach($nbanners as $key => $title){
					if(isset($banners[$key]) and is_array($banners[$key])){
						$text = $banners[$key];
					
						$html .= '
						<div class="promotext_warning">
							'. __('Note: here are a few examples of the service description, which are already published on dozens of websites. We strongly recommend you to rewrite these texts in your own words.','pn') .'
						</div>';
					
						foreach($text as $txt){
							$txt = str_replace('[url]',$url,$txt);
							$txt = str_replace('[partner_link]',$url.'?'. stand_refid() .'='.$user_id,$txt);
							$txt = trim(stripslashes($txt));
							if($txt){
								$text_temp = '
								<div class="one_promotxt">
									'. $txt .'
								</div>
								<div class="one_promotxt_code">
									<textarea class="partner_textarea" onclick="this.select()">'. $txt .'</textarea>
								</div>		
								';
								$html .= apply_filters('promotional_textbanner', $text_temp, $txt);
							}
						}

					}
					break;
				}
				
			} else { /* banner */
								
					$second_menu = '
					<div class="promo_menu">
						<ul>';
							$r=0;
							if(isset($nbanners['text'])){ unset($nbanners['text']); }
							foreach($nbanners as $myb => $value){ 
								if(isset($banners[$myb]) and is_array($banners[$myb])){ $r++;
									$cl = '';
										if($r==1){ $cl = 'act'; }
										
										$value = str_replace(array(__('Banners','pn'),'(',')'),'',$value);
										
										$second_menu .= '
											<li class="'. $cl .'"><a href="#ftab'. $r .'">'. $value .'</a></li>
										';
								}
								if($r%9==0){ $second_menu .= '<div class="clear"></div>'; }
							}
							$second_menu .= '
						</ul>
					</div>';

					$html .= '<div class="bannerbox">';
					
					$r=0;
					
					foreach($nbanners as $myb => $value){ 
						if(isset($banners[$myb]) and is_array($banners[$myb])){  $r++;
						
							if($r == 1){ $clay='act'; } else { $clay=''; }
							$html .= '<div id="ftab'. $r .'" class="pbcontainer '. $clay .'">';
							
							foreach($banners[$myb] as $txt){
								$txt = str_replace('[url]',$url,$txt);
								$txt = str_replace('[partner_link]',$url.'?'. stand_refid() .'='.$user_id,$txt);
								$txt = trim(stripslashes($txt));
								if($txt){    
								
									$text_temp  = '
									<div class="prevbanner">'.$txt.'</div>
									<div class="bannerboxone">
										<div class="bannerboxlink">
											<a href="#" show-title="'. __('Show code','pn') .'" hide-title="'. __('Hide code','pn') .'">'. __('Show code','pn') .'</a>
										</div>
										<div class="bannerboxtextarea">
											<textarea class="partner_textarea" onclick="this.select()">'.$txt.'</textarea>
										</div>
									</div>		
									';
									$html .= apply_filters('promotional_banner', $text_temp, $txt);
									
								}
							}		
							$html .= '</div>';
						}
					}
					
					$html .= '</div>';	
				
			}	

			$array = array(
				'[topmenu]' => $topmenu,
				'[html]' => $html,
				'[second_menu]' => $second_menu,
			);	
		
			$temp_form = '
				<div class="promopage">
					<div class="promopage_ins">
						<div class="promo_topmenu">
							[topmenu]
						</div>
							<div class="clear"></div>
					
						[second_menu]	
					
						[html]
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
	
	$temp .= apply_filters('after_promotional_page','');

	return $temp;
}
add_shortcode('promotional_page', 'promotional_page_shortcode');
/* шорткод end баннеров */