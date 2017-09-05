<?php
if( !defined( 'ABSPATH')){ exit(); }

/* 
Подключаем к меню
*/
add_action('pn_adminpage_title_pn_seo', 'pn_adminpage_title_pn_seo');
function pn_adminpage_title_pn_seo($page){
	_e('SEO','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_seo','def_pn_adminpage_content_pn_seo');
function def_pn_adminpage_content_pn_seo(){
global $premiumbox;	
	
	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Homepage','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);	
	$options['home_title'] = array(
		'view' => 'inputbig',
		'title' => __('Page title','pn'),
		'default' => $premiumbox->get_option('seo','home_title'),
		'name' => 'home_title',
		'ml' => 1,
	);	
	$options['home_key'] = array( 
		'view' => 'textarea',
		'title' => __('Page keywords','pn'),
		'default' => $premiumbox->get_option('seo','home_key'),
		'name' => 'home_key',
		'width' => '',
		'height' => '50px',
		'ml' => 1,
	);
	$options['home_descr'] = array( 
		'view' => 'textarea',
		'title' => __('Page description','pn'),
		'default' => $premiumbox->get_option('seo','home_descr'),
		'name' => 'home_descr',
		'width' => '',
		'height' => '100px',
		'ml' => 1,
	);
	$options['line1'] = array(
		'view' => 'line',
		'colspan' => 2,
	);		
	$options['ogp_home_title'] = array(
		'view' => 'inputbig',
		'title' => __('OGP title','pn'),
		'default' => $premiumbox->get_option('seo','ogp_home_title'),
		'name' => 'ogp_home_title',
		'ml' => 1,
	);	
	$options['ogp_home_descr'] = array( 
		'view' => 'textarea',
		'title' => __('OGP description','pn'),
		'default' => $premiumbox->get_option('seo','ogp_home_descr'),
		'name' => 'ogp_home_descr',
		'width' => '',
		'height' => '100px',
		'ml' => 1,
	);	
	$options['ogp_home_img'] = array(
		'view' => 'uploader',
		'title' => __('OGP image', 'pn'),
		'default' => $premiumbox->get_option('seo','ogp_home_img'),
		'name' => 'ogp_home_img',
		'work' => 'input',
	);	
	$options['news_h3'] = array(
		'view' => 'h3',
		'title' => __('News','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	$options['news_title'] = array(
		'view' => 'inputbig',
		'title' => __('Page title','pn'),
		'default' => $premiumbox->get_option('seo','news_title'),
		'name' => 'news_title',
		'ml' => 1,
	);
	$options['news_key'] = array( 
		'view' => 'textarea',
		'title' => __('Page keywords','pn'),
		'default' => $premiumbox->get_option('seo','news_key'),
		'name' => 'news_key',
		'width' => '',
		'height' => '50px',
		'ml' => 1,
	);
	$options['news_descr'] = array( 
		'view' => 'textarea',
		'title' => __('Page description','pn'),
		'default' => $premiumbox->get_option('seo','news_descr'),
		'name' => 'news_descr',
		'width' => '',
		'height' => '100px',
		'ml' => 1,
	);	
	$options['line2'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['ogp_news_title'] = array(
		'view' => 'inputbig',
		'title' => __('OGP title','pn'),
		'default' => $premiumbox->get_option('seo','ogp_news_title'),
		'name' => 'ogp_news_title',
		'ml' => 1,
	);	
	$options['ogp_news_descr'] = array( 
		'view' => 'textarea',
		'title' => __('OGP description','pn'),
		'default' => $premiumbox->get_option('seo','ogp_news_descr'),
		'name' => 'ogp_news_descr',
		'width' => '',
		'height' => '100px',
		'ml' => 1,
	);	
	$options['ogp_news_img'] = array(
		'view' => 'uploader',
		'title' => __('OGP image', 'pn'),
		'default' => $premiumbox->get_option('seo','ogp_news_img'),
		'name' => 'ogp_news_img',
		'work' => 'input',
	);
	$options['news_title_h3'] = array(
		'view' => 'h3',
		'title' => __('News title template','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	$options['news_temp'] = array(
		'view' => 'textareatags',
		'title' => __('Template','pn'),
		'default' => $premiumbox->get_option('seo','news_temp'),
		'tags' => array('title'=>__('Title','pn')),
		'width' => '',
		'height' => '50px',
		'prefix1' => '[',
		'prefix2' => ']',
		'name' => 'news_temp',
		'ml' => 1,
	);
	$options['pages_title_h3'] = array(
		'view' => 'h3',
		'title' => __('Pages title template','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);				
	$options['page_temp'] = array(
		'view' => 'textareatags',
		'title' => __('Template','pn'),
		'default' => $premiumbox->get_option('seo','page_temp'),
		'tags' => array('title'=>__('Title','pn')),
		'width' => '',
		'height' => '50px',
		'prefix1' => '[',
		'prefix2' => ']',
		'name' => 'page_temp',
		'ml' => 1,
	);				
	$options['exchange_title_h3'] = array(
		'view' => 'h3',
		'title' => __('Exchange page title template (title)','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);				
	$options['exch_temp'] = array(
		'view' => 'textareatags',
		'title' => __('Template','pn'),
		'default' => $premiumbox->get_option('seo','exch_temp'),
		'tags' => array('title1'=>__('Currency title 1','pn'), 'title2'=>__('Currency title 2','pn')),
		'width' => '',
		'height' => '50px',
		'prefix1' => '[',
		'prefix2' => ']',
		'name' => 'exch_temp',
		'ml' => 1,
	);				
	$options['exchange_h3'] = array(
		'view' => 'h3',
		'title' => __('Exchange page title template (H1)','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);				
	$options['exch_temp2'] = array(
		'view' => 'textareatags',
		'title' => __('Template','pn'),
		'default' => $premiumbox->get_option('seo','exch_temp2'),
		'tags' => array('title1'=>__('Currency title 1','pn'), 'title2'=>__('Currency title 2','pn')),
		'width' => '',
		'height' => '50px',
		'prefix1' => '[',
		'prefix2' => ']',
		'name' => 'exch_temp2',
		'ml' => 1,
	);				
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('seo_changeform', $options);	
} 

/* обработка */
add_action('premium_action_pn_seo','def_premium_action_pn_seo');
function def_premium_action_pn_seo(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator', 'pn_seo'));
	
	$options = array(
		'home_title','home_key','home_descr','news_title','news_key','news_descr','news_temp','page_temp','exch_temp','exch_temp2',
		'ogp_home_title','ogp_home_descr','ogp_news_title','ogp_news_descr','ogp_home_img','ogp_news_img'
	);	
					
	foreach($options as $key){
		$val = pn_strip_input(is_param_post_ml($key));
		$premiumbox->update_option('seo',$key, $val);
	}				

	do_action('seo_changeform_post');
	
	$url = admin_url('admin.php?page=pn_seo&reply=true');
	wp_redirect($url);
	exit;
} 