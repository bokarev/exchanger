<?php
if( !defined( 'ABSPATH')){ exit(); } 

/* шорткод */
function pexch_page_shortcode($atts, $content) {
global $wpdb, $premiumbox;
	
	$temp = '';
	
	$temp .= apply_filters('before_pexch_page','');
	
	$pages = $premiumbox->get_option('partners','pages');
	if(!is_array($pages)){ $pages = array(); }	
	if(in_array('pexch',$pages)){	
	
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
					'date' => __('Date','pn'),
					'user' => __('User','pn'),
					'bids_data' => __('Exchange rate','pn'),
					'partner_reward' => __('Reward','pn'),
				),
				'noitem' => '<tr><td colspan="[count]"><div class="no_items"><div class="no_items_ins">[title]</div></div></td></tr>',
			);
			$lists = apply_filters('lists_pexch', $lists);
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
		
			$limit = apply_filters('limit_list_pexch', 15);
			$count = $wpdb->query("SELECT ID FROM ".$wpdb->prefix."bids WHERE ref_id = '$user_id' AND pcalc='1' AND status='success'");
			$pagenavi = get_pagenavi_calc($limit,get_query_var('paged'),$count);
			
			$datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."bids WHERE ref_id = '$user_id' AND pcalc='1' AND status='success' ORDER BY createdate DESC LIMIT ". $pagenavi['offset'] .",".$pagenavi['limit']);		

			$date_format = get_option('date_format');
			$time_format = get_option('time_format');				
				
			$s=0;
			foreach ($datas as $item){ $s++;
				if($s%2==0){ $odd_even = 'even'; } else { $odd_even = 'odd'; }
						
				$one_line = '';
				
				if(is_array($lists['lists'])){
					foreach($lists['lists'] as $key => $title){
								
						$data_item = '';
						if($key == 'date'){
							$data_item = get_mytime($item->createdate, "{$date_format}, {$time_format}");
						}
						if($key == 'user'){
							$uid = $item->user_id;
							if($uid > 0){
								$ui = get_userdata($uid);
								if(isset($ui->user_login)){
									$data_item = is_user($ui->user_login); 
								}
							} else {
								$data_item = __('Guest','pn');
							}
						}
						if($key == 'bids_data'){
							$status = get_bid_status($item->status);
							$link = get_bids_url($item->hashed);
							$data_item = '<span class="uo_curs1"><span class="uosum">'. is_my_money($item->curs1) .'</span> '. is_site_value($item->vtype1) .'</span> <span class="uo_curs2"><span class="uosum">'. is_my_money($item->curs2) .'</span> '. is_site_value($item->vtype2) .'</span>';
						}
						if($key == 'partner_reward'){
							$data_item = is_out_sum(is_my_money($item->summp), 12, 'all') . ' ' . cur_type();
						}
						$data_item = apply_filters('body_list_plinks', $data_item, $item, $key, $title, $date_format, $time_format);
								
						if($data_item){
							$list = is_isset($lists, 'body_item');
							$list = str_replace('[key]',$key,$list);
							$list = str_replace('[title]',$title,$list);
							$list = str_replace('[content]',$data_item,$list);
							$one_line .= $list;
						}
								
					}
				}
						
				$body_list_line = is_isset($lists, 'body_line');
				$body_list_line = str_replace('[html]',$one_line,$body_list_line);
				$body_list_line = str_replace('[odd_even]',$odd_even,$body_list_line);
				$table_list .= $body_list_line;					

			}	

			if($count == 0){
				$list = is_isset($lists, 'noitem');
				$list = str_replace('[count]', $c,$list);
				$list = str_replace('[title]',__('No data','pn'),$list);
				$table_list .= $list;						
			}
			
			$table_list .= is_isset($lists, 'after_body');
			$table_list .= is_isset($lists, 'after');
			
			$temp_html = ' 
			<div class="userxch_table">
				<div class="userxch_table_ins">
					[table_list]
				</div>	
			</div>
			
			[pagenavi]
			';
			
			$array = array(
				'[table_list]' => $table_list,
				'[pagenavi]' => get_pagenavi($pagenavi),
			);
			
			$temp_html = apply_filters('div_list_pexch',$temp_html);
			$temp .= get_replace_arrays($array, $temp_html);			
		
		} else {
			$temp .= '<div class="resultfalse">'. __('Error! Page is available for authorized users only','pn') .'</div>';
		}
	
	} else {
		$temp .= '<div class="resultfalse">'. __('Error! Page is unavailable','pn') .'</div>';
	}	
	
	$after = apply_filters('after_pexch_page','');
	$temp .= $after;

	return $temp;
}
add_shortcode('pexch_page', 'pexch_page_shortcode');
/* end шорткод переходов */