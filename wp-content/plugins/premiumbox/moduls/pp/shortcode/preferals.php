<?php
if( !defined( 'ABSPATH')){ exit(); } 

/* шорткод рефералов */
function preferals_page_shortcode($atts, $content) {
global $wpdb, $premiumbox;
	
	$temp = '';
	
	$temp .= apply_filters('before_preferals_page','');
	
	$pages = $premiumbox->get_option('partners','pages');
	if(!is_array($pages)){ $pages = array(); }	
	if(in_array('preferals',$pages)){
	
		$ui = wp_get_current_user();
		$user_id = intval($ui->ID);	
		
		if($user_id){

			$lists = array(
				'before' => '<table>',
				'after' => '</table>',
				'before_head' => '<thead><tr>',
				'after_head' => '</tr></thead>',
				'head_line' => '<th class="th_[key]">[title]</th>',
				'before_body' => '<tbody>',
				'after_body' => '</tbody>',
				'body_line' => '<tr>[html]</tr>',
				'body_item' => '<td class="td_[key] [odd_even]">[content]</td>',
				'lists' => array(
					'referal_name' => __('User','pn'),
					'registered' => __('Registration date','pn'),
				),
				'noitem' => '<tr><td colspan="[count]"><div class="no_items"><div class="no_items_ins">[title]</div></div></td></tr>',
			);
			$lists = apply_filters('lists_preferals', $lists);
			$lists = (array)$lists;		
		
			$head_list = '';
			$c = 0;
			if(is_array($lists['lists'])){
				foreach($lists['lists'] as $key => $title){
					$c++;
					$list = is_isset($lists, 'head_line');
					$list = str_replace('[key]',$key,$list);
					$list = str_replace('[title]',$title,$list);
					$head_list .= $list;
				}
			}		
		
			$table_list = is_isset($lists, 'before');
			$table_list .= is_isset($lists, 'before_head');
			$table_list .= $head_list;
			$table_list .= is_isset($lists, 'after_head');
			$table_list .= is_isset($lists, 'before_body');

			$limit = apply_filters('limit_list_preferals', 15);
			$count = $wpdb->query("SELECT ID FROM ".$wpdb->prefix."users WHERE ref_id = '$user_id'");
			$pagenavi = get_pagenavi_calc($limit,get_query_var('paged'),$count);
			
			$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."users WHERE ref_id = '$user_id' ORDER BY user_registered DESC LIMIT ". $pagenavi['offset'] .",".$pagenavi['limit']);		
		
			if(count($datas) > 0){
				
				$date_format = get_option('date_format');
				$time_format = get_option('time_format');
				
				$s=0;
				foreach($datas as $item){ $s++;
					
					if($s%2==0){ $odd_even = 'even'; } else { $odd_even = 'odd'; }
					
					$one_line = '';
					if(is_array($lists['lists'])){
						foreach($lists['lists'] as $key => $title){
						
							$data_item = '';
							if($key == 'referal_name'){
								$data_item = is_user($item->user_login);
							}
							if($key == 'registered'){
								$data_item = get_mytime($item->user_registered, "{$date_format}, {$time_format}");
							}			
							$data_item = apply_filters('body_list_preferals', $data_item, $item, $key, $title, $date_format, $time_format);	
							$list = is_isset($lists, 'body_item');
							$list = str_replace('[key]',$key,$list);
							$list = str_replace('[title]',$title,$list);
							$list = str_replace('[content]',$data_item,$list);
							$one_line .= $list;
						
						}
					}
					
					$body_list_line = is_isset($lists, 'body_line');
					$body_list_line = str_replace('[html]',$one_line,$body_list_line);
					$body_list_line = str_replace('[odd_even]',$odd_even,$body_list_line);
					$table_list .= $body_list_line;
				
				}
				
			} else {
				
				$list = is_isset($lists, 'noitem');
				$list = str_replace('[count]', $c,$list);
				$list = str_replace('[title]',__('No data','pn'),$list);
				$table_list .= $list;				
				
			}
			
			$table_list .= is_isset($lists, 'after_body');
			$table_list .= is_isset($lists, 'after');			
			
			$temp_html = '
			<div class="preferals_table"> 
				<div class="preferals_table_ins">
					[table_list]
				</div>	
			</div>
			
			[pagenavi]
			';
			
			$array = array(
				'[table_list]' => $table_list,
				'[pagenavi]' => get_pagenavi($pagenavi),
			);
			
			$temp_html = apply_filters('div_list_preferals',$temp_html);
			$temp .= get_replace_arrays($array, $temp_html);
		
		} else {
			$temp .= '<div class="resultfalse">'. __('Error! Page is available for authorized users only','pn') .'</div>';
		}
	
	} else {
		$temp .= '<div class="resultfalse">'. __('Error! Page is unavailable','pn') .'</div>';
	}
	
	$after = apply_filters('after_preferals_page','');
	$temp .= $after;

	return $temp;
}
add_shortcode('preferals_page', 'preferals_page_shortcode');
/* end шорткод рефералов */