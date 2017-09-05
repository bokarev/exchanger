<?php 
if( !defined( 'ABSPATH')){ exit(); }

if(!function_exists('set_premium_page')){
	add_action('init', 'set_premium_page', 10);
	function set_premium_page(){
		
		$data = premium_rewrite_data();
		$super_base = $data['super_base'];
		
		$matches = '';
		
		if($super_base == 'premium_post.html'){
				
			header('Content-Type: text/html; charset=utf-8');

			$method = trim(is_param_get('meth'));
			if(!pn_verify_nonce(is_param_get('yid'), 'pnbx')) { 
				if($method == 'get'){
					pn_display_mess(__('NOT CSRF!','NOT CSRF!','premium')); 
				} else {
					$log = array();
					$log['status'] = 'error';
					$log['status_code'] = 1; 
					$log['status_text'] = __('NOT CSRF!','premium');
					echo json_encode($log);
					exit;		
				}	
			}

			$myaction = pn_maxf(pn_strip_input(is_param_get('myaction')), 250);
			if(has_filter('premium_action_'.$myaction)){
				do_action('premium_action_'.$myaction);
			}
			
			exit;
				
		} elseif($super_base == 'premium_quicktags.js'){
				
			if(current_user_can('read')){
				header('Content-Type: application/x-javascript; charset=utf-8');
				
				$place = pn_maxf(pn_strip_input(is_param_get('place')),500);
				if(has_filter('pn_adminpage_quicktags_' . $place) or has_filter('pn_adminpage_quicktags')){
					do_action('pn_adminpage_quicktags_' . $place);
					do_action('pn_adminpage_quicktags');
				}			
				exit;
			}

		} elseif($super_base == 'premiumjs.js'){	
			
			header('Content-Type: application/x-javascript; charset=utf-8');
	
			set_premium_default_js();
	
			do_action('siteplace_js');
			
			exit;
			
		} elseif(preg_match("/^request-([a-zA-Z0-9\_]+).(txt|html|xml|js|php)?$/", $super_base, $matches )){	
			
			header('Content-Type: text/html; charset=utf-8');
						
			$myaction = pn_maxf(pn_strip_input(is_isset($matches,1)), 250);
			if(has_filter('myaction_request_'.$myaction)){
				do_action('myaction_request_'.$myaction);
			}
			
			exit;
			
		} elseif($super_base == 'api.html'){	
			
			do_action('pn_plugin_api');
			
			exit;			
			
		} elseif(preg_match("/^ajax-([a-zA-Z0-9\_]+).html?$/", $super_base, $matches )){	
				
			header('Content-Type: text/html; charset=utf-8');	
				
			/* Проверка на CSRF */
			$method = trim(is_param_get('meth'));

			if ( !pn_verify_nonce( is_param_get('yid'), 'pnbx_site' ) ) {
				if($method == 'get'){
					pn_display_mess(__('Action have not completed because you had changed your browser','premium'));
				} else {
					$log = array();
					$log['status'] = 'error';
					$log['status_code'] = '1'; 
					$log['status_text']= __('Action have not completed because you had changed your browser','premium');
					echo json_encode($log);
					exit;
				}
			}

			$myaction = pn_maxf(pn_strip_input(is_isset($matches,1)), 250);
			if(has_filter('myaction_site_'.$myaction)){
				do_action('myaction_site_'.$myaction);
				exit;
			}

			if($method == 'get'){
				pn_display_mess(__('Not action','premium'));
			} else {
				$log = array();
				$log['status'] = 'error';
				$log['status_code'] = '-2'; 
				$log['status_text']= __('Not action','premium');
				echo json_encode($log);
				exit;
			}	
			
			exit;	
				
		}
			
	}
	
	add_action('init', 'set_premium_page_merchant', 1);
	function set_premium_page_merchant(){
		
		$data = premium_rewrite_data();
		$super_base = $data['super_base'];
		
		$matches = '';
		
		if(preg_match("/^merchant-([a-zA-Z0-9\_]+).html?$/", $super_base, $matches )){	
			
			header('Content-Type: text/html; charset=utf-8');
						
			$myaction = pn_maxf(pn_strip_input(is_isset($matches,1)), 250);
			if(has_filter('myaction_merchant_'.$myaction)){
				do_action('myaction_merchant_'.$myaction);
			}
			
			exit;			
		} 
			
	}	
}

if(!function_exists('set_premium_default_js')){
	function set_premium_default_js(){ ?>	
jQuery(function($){
 	$('.ajax_post_form').ajaxForm({
		dataType:  'json',
		beforeSubmit: function(a,f,o) {
			f.addClass('thisactive');
			$('.thisactive input[type=submit], .thisactive input[type=button]').attr('disabled',true);
			$('.thisactive').find('.ajax_submit_ind').show();
		},
		error: function(res, res2, res3) {
			<?php do_action('pn_js_error_response', 'form'); ?>
		},
		success: function(res) {
					
			if(res['status'] == 'error'){
				if(res['status_text']){
					$('.thisactive .resultgo').html('<div class="resultfalse"><div class="resultclose"></div>'+res['status_text']+'</div>');
				}
			}
			if(res['status'] == 'error_clear'){
				if(res['status_text']){
					$('.thisactive .resultgo').html('<div class="resultfalse"><div class="resultclose"></div>'+res['status_text']+'</div>');
				}
				$('.thisactive input[type=text]:not(.notclear), .thisactive input[type=password]:not(.notclear), .thisactive textarea:not(.notclear)').val('');
			}			
			if(res['status'] == 'success'){
				if(res['status_text']){
					$('.thisactive .resultgo').html('<div class="resulttrue"><div class="resultclose"></div>'+res['status_text']+'</div>');
				}
			}
			if(res['status'] == 'success_clear'){
				if(res['status_text']){
					$('.thisactive .resultgo').html('<div class="resulttrue"><div class="resultclose"></div>'+res['status_text']+'</div>');
				}
				$('.thisactive input[type=text]:not(.notclear), .thisactive input[type=password]:not(.notclear), .thisactive textarea:not(.notclear)').val('');
			}			
					
			if(res['url']){
				window.location.href = res['url']; 
			}
						
			<?php do_action('ajax_post_form_jsresult'); ?>
					
			$('.thisactive input[type=submit], .thisactive input[type=button]').attr('disabled',false);
			$('.thisactive').find('.ajax_submit_ind').hide();
			$('.thisactive').removeClass('thisactive');
						
		}
	});	
});		
<?php
	}
}

if(!function_exists('view_premium_merchant_locale')){
	/* правка к локализации, после оплаты */
	add_filter('locale','view_premium_merchant_locale',100);
	function view_premium_merchant_locale($locale){
		$data = premium_rewrite_data();
		$super_base = $data['super_base'];		
		if(preg_match("/^merchant-([a-zA-Z0-9\_]+).html?$/", $super_base, $matches )){
			$new_locale = get_mycookie('merch_locale');
			if($new_locale){
				return $new_locale;
			}
		}
		return $locale;
	}
}

if(!function_exists('set_premium_merchant_locale')){
	/* правка к локализации, после оплаты */
	add_action('template_redirect','set_premium_merchant_locale', 3);
	function set_premium_merchant_locale(){
		add_mycookie('merch_locale', get_locale());
	}
}