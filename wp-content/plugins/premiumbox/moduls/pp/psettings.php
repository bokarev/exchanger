<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_psettings', 'pn_admin_title_pn_psettings');
function pn_admin_title_pn_psettings(){
	_e('Settings','pn');
}

add_action('pn_adminpage_content_pn_psettings','def_pn_admin_content_pn_psettings');
function def_pn_admin_content_pn_psettings(){
global $premiumbox;
	
	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Settings','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	$options['wref'] = array(
		'view' => 'select',
		'title' => __('Referral lifetime','pn'),
		'options' => array('0'=>__('Eternally','pn'), '1'=>__('By cookies','pn')),
		'default' => $premiumbox->get_option('partners','wref'),
		'name' => 'wref',
	);	
	$options['clife'] = array(
		'view' => 'input',
		'title' => __('Cookies lifetime (days)','pn'),
		'default' => $premiumbox->get_option('partners','clife'),
		'name' => 'clife',
	);	
	$options['line1'] = array(
		'view' => 'line',
		'colspan' => 2,
	);		
	$options['minpay'] = array(
		'view' => 'input',
		'title' => __('Minimum payout','pn').' ('. cur_type() .')',
		'default' => $premiumbox->get_option('partners','minpay'),
		'name' => 'minpay',
	);
	$options['calc'] = array(
		'view' => 'select',
		'title' => __('Charge affiliate reward from','pn'),
		'options' => array('0'=>__('All users','pn'),'1'=>__('Registered users only','pn')),
		'default' => $premiumbox->get_option('partners','calc'),
		'name' => 'calc',
	);				
	$options['line2'] = array(
		'view' => 'line',
		'colspan' => 2,
	);
	$options['uskidka'] = array(
		'view' => 'select',
		'title' => __('Take users discount into account when calculating partner reward','pn'),
		'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('partners','uskidka'),
		'name' => 'uskidka',
	);				
	$options['line3'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['disable_pages'] = array(
		'view' => 'user_func',
		'func_data' => array(),
		'func' => 'pn_pp_reserv_option',
	);		
	$options['line4'] = array(
		'view' => 'line',
		'colspan' => 2,
	);
	$options['reserv'] = array(
		'view' => 'user_func',
		'func_data' => array(),
		'func' => 'pn_pp_disable_pages_option',
	);
	$options['line5'] = array(
		'view' => 'line',
		'colspan' => 2,
	);
	$options['text_banners'] = array(
		'view' => 'select',
		'title' => __('Show promo text materials','pn'),
		'options' => array('0'=>__('No','pn'),'1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('partners','text_banners'),
		'name' => 'text_banners',
	);
	$options['line6'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['payouttext'] = array(
		'view' => 'textareatags',
		'title' => __('Text above form of withdrawal of partner funds','pn'),
		'default' => $premiumbox->get_option('partners','payouttext'),
		'tags' => array('minpay'=>__('Minimum payout','pn'), 'currency'=>__('Currency type','pn')),
		'width' => '',
		'height' => '150px',
		'prefix1' => '[',
		'prefix2' => ']',
		'name' => 'payouttext',
		'work' => 'text',
		'ml' => 1,
	);	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_pp_adminform', $options, '');	
}

function pn_pp_disable_pages_option(){	
global $premiumbox;

	$pages = $premiumbox->get_option('partners','pages');
	if(!is_array($pages)){ $pages = array(); }				
	$list = array(
		'paccount' => __('Affiliate account','pn'),
		'promotional' => __('Promotional materials','pn'),
		'plinks' => __('Affiliate transitions','pn'),
		'pexch' => __('Affiliate exchanges','pn'),
		'preferals' => __('Affiliate referrals','pn'),
		'payouts' => __('Affiliate payouts','pn'),
		'partnersfaq' => __('Affiliate FAQ','pn'),
		'terms' => __('Affiliate terms and conditions','pn'),
	);
	?>					
	<tr>
		<th><label><?php _e('Show sections','pn'); ?></label></th>
		<td>
			<div class="premium_wrap_standart">		
				<?php foreach($list as $key => $title){ ?>
					<div><label><input type="checkbox" name="pages[]" <?php if(in_array($key, $pages)){ echo 'checked="checked"'; }?> value="<?php echo $key; ?>" /> <?php echo $title; ?></label></div>
				<?php } ?>			
			</div>
		</td>		
	</tr>						
	<?php				
}

function pn_pp_reserv_option(){
global $premiumbox;

 	$reserv = $premiumbox->get_option('partners','reserv');
	if(!is_array($reserv)){ $reserv = array(); }

	$list = array(
		'0' => __('pending order','pn'),
		'1' => __('paid order','pn'),
	);
	?>					
	<tr>
		<th><label><?php _e('Consider affiliate payments included in reserve','pn'); ?></label></th>
		<td>
			<div class="premium_wrap_standart">		
				<?php foreach($list as $key => $title){ ?>
					<div><label><input type="checkbox" name="reserv[<?php echo $key; ?>]" <?php if(in_array($key, $reserv)){ echo 'checked="checked"'; }?> value="<?php echo $key; ?>" /> <?php echo $title; ?></label></div>
				<?php } ?>			
			</div>
		</td>		
	</tr>
	<?php 	
}

add_action('premium_action_pn_psettings','def_premium_action_pn_psettings');
function def_premium_action_pn_psettings(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator','pn_pp'));
	
	$premiumbox->update_option('partners','wref',intval(is_param_post('wref')));
	$premiumbox->update_option('partners','payouttext', pn_strip_text(is_param_post_ml('payouttext')));
	$premiumbox->update_option('partners','text_banners',intval(is_param_post('text_banners')));
	$premiumbox->update_option('partners','calc',intval(is_param_post('calc')));
	$premiumbox->update_option('partners','uskidka',intval(is_param_post('uskidka')));
	$premiumbox->update_option('partners','reserv', is_param_post('reserv'));
	$premiumbox->update_option('partners','minpay',is_my_money(is_param_post('minpay'),2));		
	$premiumbox->update_option('partners','clife',intval(is_param_post('clife')));
	$premiumbox->update_option('partners','pages', is_param_post('pages'));
			
	do_action('pn_pp_adminform_post');
			
	$url = admin_url('admin.php?page=pn_psettings&reply=true');
	wp_redirect($url);
	exit;
}	