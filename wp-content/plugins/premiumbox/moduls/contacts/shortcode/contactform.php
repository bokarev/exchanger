<?php
if( !defined( 'ABSPATH')){ exit(); }
 
add_action('pn_adminpage_quicktags_page','pn_adminpage_quicktags_page_contact');
function pn_adminpage_quicktags_page_contact(){
?>
edButtons[edButtons.length] = 
new edButton('premium_contact', '<?php _e('Contact form','pn'); ?>','[contact_form]');
<?php	
}

add_filter('admin_mailtemp','contactform_admin_mailtemp');
function contactform_admin_mailtemp($places_admin){
	
	$places_admin['contactform'] = __('Contact form','pn');
	
	return $places_admin;
}

add_filter('user_mailtemp','contactform_user_mailtemp');
function contactform_user_mailtemp($places_admin){
	
	$places_admin['contactform_auto'] = __('Auto-responder (contact form)','pn');
	
	return $places_admin;
}

add_filter('mailtemp_tags_contactform','def_mailtemp_tags_contactform');
add_filter('mailtemp_tags_contactform_auto','def_mailtemp_tags_contactform');
function def_mailtemp_tags_contactform($tags){
	
	$tags['name'] = __('Your name','pn');
	$tags['idz'] = __('Exchange ID','pn');
	$tags['text'] = __('Text','pn');
	$tags['email'] = __('Your e-mail','pn');
	$tags['link'] = __('Reply link','pn');
	
	return $tags;
}

/* shortcode */
function pn_contact_form_shortcode($atts) {
	
	$temp = '';
	
 	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	
	$items = get_contact_form_filelds();
	$html = prepare_form_fileds($items, 'contact_form_line', 'cf');
	
	$array = array(
		'[form]' => '<form method="post" class="ajax_post_form" action="'. get_ajax_link('contactform') .'">',
		'[/form]' => '</form>',
		'[result]' => '<div class="resultgo"></div>',
		'[html]' => $html,
		'[submit]' => '<input type="submit" formtarget="_top" name="submit" class="cf_submit" value="'. __('Send a message', 'pn') .'" />',
	);	
	
	$temp = '
	<div class="cf_div_wrap">
	[form]

		<div class="cf_div_title">
			<div class="cf_div_title_ins">
				'. __('Contact form','pn') .'
			</div>
		</div>
	
		<div class="cf_div">
			<div class="cf_div_ins">
				
				[html]
				
				<div class="cf_line has_submit">
					[submit]
				</div>
				
				[result]
				
			</div>
		</div>
	
	[/form]
	</div>
	';
	
	$temp = apply_filters('contact_form_temp',$temp);
	$temp = get_replace_arrays($array, $temp);
	
	return $temp;
}
add_shortcode('contact_form', 'pn_contact_form_shortcode');
/* end shortcode */

/* обработка формы */
add_action('myaction_site_contactform', 'def_myaction_ajax_contactform');
function def_myaction_ajax_contactform(){
global $or_site_url, $premiumbox;	
	
	only_post();
	
	$log = array();
	$log['response'] = '';
	$log['status'] = '';
	$log['status_code'] = 0;
	$log['status_text'] = '';
	
	$premiumbox->up_mode();
	
	$log = apply_filters('before_ajax_form_field', $log, 'contactform');
	$log = apply_filters('before_ajax_contactform', $log);
	
	$name = pn_maxf_mb(pn_strip_input(is_param_post('name')), 150);
	$email = is_email(is_param_post('email'));
	$idz = pn_maxf_mb(pn_strip_input(is_param_post('idz')), 300);
	$text = pn_maxf_mb(pn_strip_input(is_param_post('text')), 2000);
	
	if(strlen($name) > 3){
		if($email){
			if(strlen($text) > 3){
		
				$mailtemp = get_option('mailtemp');
				if(isset($mailtemp['contactform'])){
					$data = $mailtemp['contactform'];
					if($data['send'] == 1){
						$ot_mail = str_replace('[user]',$email,$data['mail']);
						$ot_mail = is_email($ot_mail);
						$ot_name = pn_strip_input($data['name']);
						$ot_name = str_replace('[user]',$name,$ot_name);
						
						$subject = pn_strip_input(ctv_ml($data['title']));
						
						$html = pn_strip_text(ctv_ml($data['text']));
						
						if($data['tomail']){
						
							$to_mail = $data['tomail'];
							$sitename = pn_strip_input(get_bloginfo('sitename'));
							
							$sarray = array(
								'[sitename]' => $sitename,
								'[name]' => $name,
								'[idz]' => $idz,
								'[email]' => $email,
							);							
							$subject = get_replace_arrays($sarray, $subject);
							$subject = apply_filters('mail_contactform_subject',$subject);
							
							$link = '<a href="mailto:'. $email .'?subject='. $subject .'">'. __('Reply','pn') .'</a>';
							
							$sarray = array(
								'[sitename]' => $sitename,
								'[name]' => $name,
								'[idz]' => $idz,
								'[email]' => $email,
								'[text]' => $text,
								'[link]' => $link,
							);							
							$html = get_replace_arrays($sarray, $html);
							$html = apply_filters('mail_contactform_text',$html);
							$html = apply_filters('comment_text',$html);
							
							pn_mail($to_mail, $subject, $html, $ot_name, $ot_mail);	
						
						}
					}
				}
				if(isset($mailtemp['contactform_auto'])){
					$data = $mailtemp['contactform_auto'];
					if($data['send'] == 1){
						$ot_mail = str_replace('[user]',$email,$data['mail']);
						$ot_mail = is_email($ot_mail);
						$ot_name = pn_strip_input($data['name']);
						$ot_name = str_replace('[user]',$name,$ot_name);
						
						$subject = pn_strip_input(ctv_ml($data['title']));
						
						$html = pn_strip_text(ctv_ml($data['text']));
						
						if(is_email($email)){
						
							$sitename = pn_strip_input(get_bloginfo('sitename'));
							
							$sarray = array(
								'[sitename]' => $sitename,
								'[name]' => $name,
								'[idz]' => $idz,
								'[email]' => $email,
							);							
							$subject = get_replace_arrays($sarray, $subject);
							$subject = apply_filters('mail_contactform_subject',$subject);
							
							$link = '<a href="mailto:'. $email .'?subject='. $subject .'">'. __('Reply','pn') .'</a>';
							
							$sarray = array(
								'[sitename]' => $sitename,
								'[name]' => $name,
								'[idz]' => $idz,
								'[email]' => $email,
								'[text]' => $text,
								'[link]' => $link,
							);							
							$html = get_replace_arrays($sarray, $html);
							$html = apply_filters('mail_contactform_text',$html);
							$html = apply_filters('comment_text',$html);
							
							pn_mail($email, $subject, $html, $ot_name, $ot_mail);	
						
						}
					}
				}				

				$log['status'] = 'success_clear';
				$log['status_text'] = apply_filters('ajax_contactform_success_message',__('Your message has been successfully sent','pn'));				
		
			} else {
				$log['status'] = 'error';
				$log['status_code'] = 1;
				$log['status_text'] = __('Error! You must enter a message','pn');				
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
	
	echo json_encode($log);
	exit;
}
/* end обработка формы */