<?php
if( !defined( 'ABSPATH')){ exit(); }

add_filter('merchant_header', 'def_merchant_header', 10,2);
function def_merchant_header($html, $item){
global $premiumbox;
	
	$item_title1 = pn_strip_input(ctv_ml($item->valut1)).' '.is_site_value($item->vtype1);
	$item_title2 = pn_strip_input(ctv_ml($item->valut2)).' '.is_site_value($item->vtype2);
	$title = sprintf(__('Exchange %1$s to %2$s','pn'),$item_title1,$item_title2);
	
	$logo = get_logotype();
	$textlogo = get_textlogo();
	
	$html .= '
	<!DOCTYPE html>
	<html '. get_language_attributes( 'html' ) .'>
	<head>

		<meta charset="'. get_bloginfo( 'charset' ) .'">
		<title>'. $title .'</title>
			
		<script type="text/javascript" src="'. get_premium_url() .'js/jquery.min.js"></script>
		<link rel="stylesheet" href="'. $premiumbox->plugin_url .'merchant_style.css" type="text/css" media="all" />
		';
		if($premiumbox->get_option('exchange','mhead_style') == 1){
			$html .= '<link rel="stylesheet" href="'. $premiumbox->plugin_url .'merchant_style_black.css" type="text/css" media="all" />';
		}
	$html .= '
	</head>
	<body>
	<div class="header">
		<div class="logo">
			<div class="logo_ins">
				<a href="'. get_site_url_ml() .'">';
								
					if($logo){
						$html .= '<img src="'. $logo .'" alt="" />';
					} elseif($textlogo){
						$html .= $textlogo; 
					} else { 
						$textlogo = str_replace(array('http://','https://','www.'),'',get_site_url_or());
						$html .= get_caps_name($textlogo);	
					} 
									
				$html .= '				
				</a>	
			</div>
		</div>
	</div>
	<div class="description">
		'. $title .'
	</div>
	<div class="back_div"><a href="'. get_bids_url($item->hashed) .'" id="back_link">'. __('Back to order page','pn') .'</a></div>
	<div class="content">
	';
	
	return $html;
}

add_filter('merchant_footer', 'def_merchant_footer', 10,2);
function def_merchant_footer($html, $item){
	
	$html .= "
	
	</div>
		<script type='text/javascript'>
			jQuery(function($){			
				$(document).on('keyup', function( e ){
					if(e.which == 27){
						var nurl = $('a#back_link').attr('href');
						window.location.href = nurl;
					}
				});								
			});
		</script>	
	</body>
	</html>
	";
	
	return $html;
}