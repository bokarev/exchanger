<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_quicktags_page','pn_adminpage_quicktags_page_reviews');
function pn_adminpage_quicktags_page_reviews(){
?>
edButtons[edButtons.length] = 
new edButton('premium_reviews', '<?php _e('Reviews','pn'); ?>','[reviews count=5]');

edButtons[edButtons.length] = 
new edButton('premium_reviews_form', '<?php _e('Reviews form','pn'); ?>','[reviews_form]');
<?php	
}

function get_reviews_form(){
global $wpdb, $premiumbox;

	$reviews_form = '';
	if($premiumbox->get_option('reviews','method') != 'not'){

		$items = get_reviews_form_filelds();
		$html = prepare_form_fileds($items, 'reviews_form_line', 'rf');	
	
		$array = array(
			'[form]' => '<form method="post" class="ajax_post_form" action="'. get_ajax_link('reviewsform') .'">',
			'[/form]' => '</form>',
			'[result]' => '<div class="resultgo"></div>',
			'[html]' => $html,
			'[submit]' => '<input type="submit" formtarget="_top" name="submit" class="rf_submit" value="'. __('Leave a review', 'pn') .'" />',
		);	
	
		$temp_form = '
		<div class="rf_div_wrap">
		[form]

			<div class="rf_div_title">
				<div class="rf_div_title_ins">
					'. __('Post review','pn') .'
				</div>
			</div>
		
			<div class="rf_div">
				<div class="rf_div_ins">
					
					[html]
					
					<div class="rf_line has_submit">
						[submit]
					</div>					
					
					[result]
					
				</div>
			</div>
		
		[/form]
		</div>
		';
	
		$temp_form = apply_filters('reviews_form_temp',$temp_form);
		$reviews_form = get_replace_arrays($array, $temp_form);
	}	
	
	return $reviews_form;
}
add_shortcode('reviews_form', 'get_reviews_form');

function reviews_page_shortcode($atts, $content) {
global $wpdb, $premiumbox;

	$temp = '';
    $temp .= apply_filters('before_reviews_page','');
			
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);
					
	$limit = intval($premiumbox->get_option('reviews','count')); if($limit < 1){ $limit=10; }
	
	$deduce = intval($premiumbox->get_option('reviews','deduce'));
	
	$where = '';
	if($deduce == 1){
		$locale = get_locale();
		$where = " AND review_locale='$locale'";
	}
	
	$count = $wpdb->query("SELECT id FROM ".$wpdb->prefix."reviews WHERE review_status = 'publish' $where"); 
	$pagenavi = get_pagenavi_calc($limit,get_query_var('paged'),$count);
	$reviews = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."reviews WHERE review_status = 'publish' $where ORDER BY review_date DESC LIMIT ". $pagenavi['offset'] .",".$pagenavi['limit']);					

	$reviews_list = apply_filters('before_reviews_page_content','<div class="many_reviews"><div class="many_reviews_ins">');
	
	if(count($reviews) > 0){
		$r=0;	
		$reviews_date_format = apply_filters('reviews_date_format', get_option('date_format').', '.get_option('time_format'));
		
		foreach($reviews as $item){ $r++;
			$site = esc_url($item->user_site);
			$site1 = $site2 = '';
			if($site){
				$site1 = '<a href="'. $site .'" rel="nofollow" target="_blank">';
				$site2 = '</a>';
			}
			
		    $review_html ='
			<div class="one_reviews" id="review-'. $item->id .'">
				<div class="one_reviews_ins">
					<div class="one_reviews_abs"></div>
				
					<div class="one_reviews_name">
						'. $site1 .'
						'. pn_strip_input($item->user_name) .'
						'. $site2 .'							
					</div>
					<div class="one_reviews_date">'. get_mytime($item->review_date, $reviews_date_format) .'</div>
						<div class="clear"></div>
						
					<div class="one_reviews_text">
						'.  apply_filters('comment_text',$item->review_text) .'
							<div class="clear"></div>
					</div>
				</div>
			</div>
			';
			
			$reviews_list .= apply_filters('reviews_one', $review_html, $item, $r, $reviews_date_format);
			
		}
	} else {
        $reviews_list .='<div class="no_reviews"><div class="no_reviews_ins">'. __('No reviews','pn') .'</div></div>';
    }
	
	$reviews_list .= apply_filters('after_reviews_page_content','</div></div>');
		
    $reviews_navi = get_pagenavi($pagenavi);
	
	$reviews_form = get_reviews_form();
	
		$array = array(
			'[form]' => $reviews_form,
			'[list]' => $reviews_list,
			'[navi]' => $reviews_navi,
		);
	
		$page_map = '
			[list]
			[navi]
			[form]
		';

		$page_map = apply_filters('reviews_page_map',$page_map);
		$temp .= get_replace_arrays($array, $page_map);	
	
    $after = apply_filters('after_reviews_page','');
    $temp .= $after;	
	
	return $temp;
}
add_shortcode('reviews_page', 'reviews_page_shortcode');

function reviews_shortcode($atts, $content) {
global $wpdb, $premiumbox;
$temp = '';				

	$deduce = intval($premiumbox->get_option('reviews','deduce'));
	
	$where = '';
	if($deduce == 1){
			
		$locale = get_locale();
		$where = " AND review_locale='$locale'";
			
	}
	
	$limit = intval(is_isset($atts,'count')); if($limit < 1){ $limit=10; }
	$reviews = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."reviews WHERE review_status = 'publish' $where ORDER BY review_date DESC LIMIT ". $limit);					

	$temp .= apply_filters('before_reviews_page_content','<div class="many_reviews"><div class="many_reviews_ins">');
	
	if(count($reviews) > 0){
		$r=0;	
		$reviews_date_format = apply_filters('reviews_date_format', get_option('date_format').', '.get_option('time_format'));
		foreach($reviews as $item){ $r++;
			$site = esc_url($item->user_site);
			$site1 = $site2 = '';
			if($site){
				$site1 = '<a href="'. $site .'" rel="nofollow" target="_blank">';
				$site2 = '</a>';
			}
			
		    $review_html ='
			<div class="one_reviews" id="review-'. $item->id .'">
				<div class="one_reviews_ins">
					<div class="one_reviews_abs"></div>
				
					<div class="one_reviews_name">
						'. $site1 .'
						'. pn_strip_input($item->user_name) .'
						'. $site2 .'							
					</div>
					<div class="one_reviews_date">'. get_mytime($item->review_date, $reviews_date_format) .'</div>
						<div class="clear"></div>
						
					<div class="one_reviews_text">
						'.  apply_filters('comment_text',$item->review_text) .'
							<div class="clear"></div>
					</div>
				
				</div>
			</div>
			';
			
			$temp .= apply_filters('reviews_one', $review_html, $item, $r, $reviews_date_format);
			
		}
	} else {
        $temp .='<div class="no_reviews"><div class="no_reviews_ins">'. __('No reviews','pn') .'</div></div>';
    }
	
	$temp .= apply_filters('after_reviews_page_content','</div></div>');

	return $temp;
}
add_shortcode('reviews', 'reviews_shortcode');

/* обработка формы сайта */
add_action('myaction_site_reviewsform', 'def_myaction_site_reviewsform');
function def_myaction_site_reviewsform(){
global $wpdb, $premiumbox;	
	
	only_post();
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);
	
	$log = array();
	$log['response'] = '';
	$log['status_text'] = '';
	$log['status'] = 'error';
	$log['status_code'] = 0; 	
	
	$premiumbox->up_mode();
	
	$log = apply_filters('before_ajax_form_field', $log, 'reviewsform');
	$log = apply_filters('before_ajax_reviewsform', $log);
	
	$array = array();
	$array['user_id'] = $user_id;
	$array['review_date'] = current_time('mysql');
	$review_hash = wp_generate_password(25, false, false);
	$array['review_hash'] = md5($review_hash);
	$array['review_locale'] = get_locale();
	
	$array['user_name'] = $name = pn_maxf_mb(pn_strip_input(is_param_post('name')),150);
	$array['user_email'] = $email = is_email(is_param_post('email'));
	if($premiumbox->get_option('reviews','website') == 1){
		$array['user_site'] = $website = pn_maxf_mb(esc_url(pn_strip_input(is_param_post('website'))),500);
	}	
	$array['review_text'] = $text = pn_maxf_mb(pn_strip_input(is_param_post('text')),1000);
	
	$method = $premiumbox->get_option('reviews','method');
	if($method != 'not'){
		if(mb_strlen($name) > 3){
			if($email){
				if(mb_strlen($text) > 3){
		
					if($method == 'moderation'){ /* если с модерацией */
				
						$review_status = 'moderation';
						$ajax_reviewsform_success_message = __('Your review has been successfully added and is waiting for moderation','pn');					
					
					} elseif($method == 'verify'){ /* если с верификацией */
				
						$review_status = 'moderation';
						$ajax_reviewsform_success_message = __('Your review has been successfully added. We sent you an email for confirmation','pn');					
					
						$mailtemp = get_option('mailtemp');
						if(isset($mailtemp['confirmreview'])){
							$data = $mailtemp['confirmreview'];
							if($data['send'] == 1){
								$ot_mail = is_email($data['mail']);
								$ot_name = pn_strip_input($data['name']);
								$sitename = pn_strip_input(get_bloginfo('sitename'));
								$subject = pn_strip_input(ctv_ml($data['title']));
						
								$html = pn_strip_text(ctv_ml($data['text']));
						
								if($array['user_email']){
						
									$to_mail = $array['user_email'];
						
									$link = get_ajax_link('confirmreview','get').'&act='. $review_hash;
						
									$sarray = array(
										'[sitename]' => $sitename,
										'[link]' => $link,
									);							
									$subject = get_replace_arrays($sarray, $subject);									
									$subject = apply_filters('mail_reviewsform_subject',$subject);
							
									$html = get_replace_arrays($sarray, $html);
									$html = apply_filters('mail_reviewsform_text',$html);
									$html = apply_filters('comment_text',$html);
										
									pn_mail($to_mail, $subject, $html, $ot_name, $ot_mail);	 

								}
							}
						}										
						
					} else { /* если просто добавить */
				
						$review_status = 'publish';
						$ajax_reviewsform_success_message = __('Your review has been successfully added','pn');

					}	

					$array['review_status'] = $review_status;
					$array = apply_filters('before_insert_reviewsform', $array);					
					$wpdb->insert($wpdb->prefix.'reviews', $array);
					$rewiew_id = $wpdb->insert_id;
					$array['id'] = $rewiew_id;
					$review_object = (object)$array;
					
					$log['status'] = 'success_clear';
					$log['status_text'] = apply_filters('ajax_reviewsform_success_message',$ajax_reviewsform_success_message,$method);
		
					if($method == 'moderation'){
						mailto_add_reviews($review_object, 'moderation');
					} elseif($method == 'notmoderation'){
						mailto_add_reviews($review_object, 'publish');
					}					
		
				} else {
					$log['status'] = 'error';
					$log['status_code'] = 1;
					$log['status_text'] = __('Error! You must enter a text','pn');				
				}
			} else {
				$log['status'] = 'error';
				$log['status_code'] = 1;
				$log['status_text'] = __('Error! You must enter your e-mail','pn');			
			}
		} else {
			$log['status'] = 'error';
			$log['status_code'] = 1;
			$log['status_text'] = __('Error! You must enter your name','pn');
		}
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1;
		$log['status_text'] = __('Error! Reviews are disabled','pn');		
	}
	
	$log['exit'] = 'yes';
	echo json_encode($log);
	exit;
}
/* end обработка формы сайта */

/* верификация */
add_action('myaction_site_confirmreview', 'def_myaction_ajax_confirmreview');
function def_myaction_ajax_confirmreview(){
global $wpdb, $premiumbox;	
	
	$premiumbox->up_mode();
	
	$hash = is_reviews_hash(is_param_get('act'));
	if($hash){
		$hash_md = md5($hash);
		$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."reviews WHERE review_hash='$hash_md'");
		if(isset($data->id)){
			$id = $data->id;
			$wpdb->query("UPDATE ".$wpdb->prefix."reviews SET review_status='publish', review_hash='' WHERE id = '$id'");
			
			$link = get_review_link($id, $data);
			mailto_add_reviews($data, 'publish');

			wp_redirect($link);
			exit;
	
		} else {
			pn_display_mess(__('Error!','pn'), __('Error!','pn'), 'error');	
		}
	} else {
		pn_display_mess(__('Error!','pn'), __('Error!','pn'), 'error');
	}
}