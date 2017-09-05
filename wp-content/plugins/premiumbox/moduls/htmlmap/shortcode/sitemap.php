<?php
if( !defined( 'ABSPATH')){ exit(); }

function sitemap_shortcode($atts, $content) {
global $wpdb, $post, $premiumbox;
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
    $temp = '';
    $temp .= apply_filters('before_sitemap_page','');
	
		if($premiumbox->get_option('htmlmap','pages') == 1){
	
			$temp .= '
			<div class="sitemap_block">
				<div class="sitemap_block_ins">';	
	
				$sitemap_block_title = '
				<div class="sitemap_title">
					<div class="sitemap_title_ins">
						<div class="sitemap_title_abs"></div>
						'. __('Pages','pn') .'
					</div>
				</div>
					<div class="clear"></div>
				';
				$temp .= apply_filters('sitemap_block_title', $sitemap_block_title, 'pages');
				
				$temp .= '
				<div class="sitemap_once">
					<div class="sitemap_once_ins">
					<ul class="sitemap_ul">
				';
				
					$exclude_pages = $premiumbox->get_option('htmlmap','exclude_page');
					if(!is_array($exclude_pages)){ $exclude_pages = array(); }
					$exclude = join(',',$exclude_pages);
					$args = array(
					'post_type' => 'page',
					'posts_per_page' => '-1',
					'exclude' => $exclude
					);			
					$mposts = get_posts($args);
			
					foreach($mposts as $mpos){ 
						$temp .= '<li><a href="'. get_permalink($mpos->ID) .'">'. pn_strip_input(ctv_ml($mpos->post_title)) .'</a></li>';
					}			
			
				$temp .= '
					</ul>
						<div class="clear"></div>
					</div>
				</div>
				
				</div>
			</div>	
			';	
		
		}
		 
		if($premiumbox->get_option('htmlmap','exchanges') == 1){
			
			$show_data = pn_exchanges_output('sm');
			
			if($show_data['text']){
				$temp .= '<div class="resultfalse">'. $show_data['text'] .'</div>';
			}
			
			if($show_data['mode'] == 1){
				
				$v = get_valuts_data();			
				
				$where = get_naps_where('sm'); 
				$naps = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where ORDER BY site_order1 ASC");
				
				$temp .= '
				<div class="sitemap_block">
					<div class="sitemap_block_ins">';	
			
						$sitemap_block_title = '
						<div class="sitemap_title">
							<div class="sitemap_title_ins">
								<div class="sitemap_title_abs"></div>
								'. __('Direction of Exchange','pn') .'
							</div>
						</div>
							<div class="clear"></div>
						';
						$temp .= apply_filters('sitemap_block_title', $sitemap_block_title, 'exchanges');
						 
						$temp .= '
						<div class="sitemap_once">
							<div class="sitemap_once_ins">
							<ul class="sitemap_ul_exchanges">
						';
						
						foreach($naps as $item){
							$output = apply_filters('get_naps_output', 1, $item, 'sm');
							if($output){
								$valsid1 = $item->valut_id1;
								$valsid2 = $item->valut_id2;
								
								if(isset($v[$valsid1]) and isset($v[$valsid2])){
									$vd1 = $v[$valsid1];
									$vd2 = $v[$valsid2];
									
									$title1 = get_valut_title($vd1);
									$title2 = get_valut_title($vd2);
									$link = get_exchange_link($item->naps_name);
									$line = '<li><a href="'. $link .'">'. $title1 .' &rarr; '. $title2 .'</a></li>';
									$temp .= apply_filters('sitemap_exchange_title', $line, $vd1, $vd2, $link, $item);
								}
							}
						}	
					
						$temp .= '
							</ul>
								<div class="clear"></div>
							</div>
						</div>
						
						</div>
					</div>	
					';	
				
			}
			
		}		
		
		if($premiumbox->get_option('htmlmap','news') == 1){
	
			$temp .= '
			<div class="sitemap_block">
				<div class="sitemap_block_ins">';	
	
				$sitemap_block_title = '
				<div class="sitemap_title">
					<div class="sitemap_title_ins">
						<div class="sitemap_title_abs"></div>
						'. __('News','pn') .'
					</div>
				</div>
					<div class="clear"></div>
				';
				$temp .= apply_filters('sitemap_block_title', $sitemap_block_title, 'news');
				
				$temp .= '
				<div class="sitemap_once">
					<div class="sitemap_once_ins">
					<ul class="sitemap_ul">
				';
				
					$args = array(
					'post_type' => 'post',
					'posts_per_page' => '-1',
					);			
					$mposts = get_posts($args);
			
					foreach($mposts as $mpos){ 
						$temp .= '<li><a href="'. get_permalink($mpos->ID) .'">'. pn_strip_input(ctv_ml($mpos->post_title)) .'</a></li>';
					}			
			
				$temp .= '
					</ul>
						<div class="clear"></div>
					</div>
				</div>
				
				</div>
			</div>	
			';	
		
		}					

    $after = apply_filters('after_sitemap_page','');
    $temp .= $after;			
	
	return $temp;
}
add_shortcode('sitemap', 'sitemap_shortcode');