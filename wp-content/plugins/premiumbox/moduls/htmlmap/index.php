<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]HTML карта сайта[:ru_RU][en_US:]HTML sitemap[:en_US]
description: [ru_RU:]HTML карта сайта[:ru_RU][en_US:]HTML sitemap[:en_US]
version: 1.0
category: [ru_RU:]Настройки[:ru_RU][en_US:]Settings[:en_US]
cat: sett
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_filter('pn_tech_pages', 'list_tech_pages_htmlmap');
function list_tech_pages_htmlmap($pages){
 
	$pages[] = array(
		'post_name'      => 'sitemap',
		'post_title'     => '[ru_RU:]Карта сайта[:ru_RU][en_US:]Sitemap[:en_US]',
		'post_content'   => '[sitemap]',
		'post_template'   => 'pn-pluginpage.php',
	);		
	
	return $pages;
}
/* end BD */

add_filter('pn_exchange_cat_filters','pn_exchange_cat_filters_htmlmap');
function pn_exchange_cat_filters_htmlmap($cats){
	$cats['sm'] = __('Sitemap HTML','pn');
	return $cats;
}

add_action('admin_menu', 'pn_adminpage_htmlmap');
function pn_adminpage_htmlmap(){
global $premiumbox;
	
	add_submenu_page("pn_config", __('HTML sitemap settings','pn'), __('HTML sitemap settings','pn'), 'administrator', "pn_htmlmap", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_htmlmap', 'pn_adminpage_title_pn_htmlmap');
function pn_adminpage_title_pn_htmlmap($page){
	_e('HTML sitemap settings','pn');
} 

add_action('pn_adminpage_content_pn_htmlmap','def_pn_adminpage_content_pn_htmlmap');
function def_pn_adminpage_content_pn_htmlmap(){
global $wpdb, $premiumbox;

	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('HTML sitemap settings','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	$options['news'] = array(
		'view' => 'select',
		'title' => __('Show news','pn'),
		'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('htmlmap','news'),
		'name' => 'news',
	);	
	$options['line1'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	$options['exchanges'] = array(
		'view' => 'select',
		'title' => __('Show exchange directions','pn'),
		'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('htmlmap','exchanges'),
		'name' => 'exchanges',
	);	
	$options['line2'] = array(
		'view' => 'line',
		'colspan' => 2,
	);							
	$options['pages'] = array(
		'view' => 'select',
		'title' => __('Show pages','pn'),
		'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('htmlmap','pages'),
		'name' => 'pages',
	);						
	$options['exclude_page'] = array(
		'view' => 'user_func',
		'func_data' => array(),
		'func' => 'pn_htmlmap_option1',
	);		
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_htmlmap_option', $options);	
} 

function pn_htmlmap_option1($data){
global $premiumbox;	
	$args = array(
		'post_type' => 'page',
		'posts_per_page' => '-1'
	);
	$pages = get_posts($args);
	
	$exclude_pages = $premiumbox->get_option('htmlmap','exclude_page');
	if(!is_array($exclude_pages)){ $exclude_pages = array(); }

	?>
	<tr>
		<th><?php _e('Exclude pages','pn'); ?></th>
		<td>
			<div class="premium_wrap_standart">
				<?php foreach($pages as $item){ ?>
					<div><label><input type="checkbox" name="exclude_page[]" <?php if(in_array($item->ID, $exclude_pages)){ ?>checked="checked"<?php } ?> value="<?php echo $item->ID; ?>" /> <a href="<?php echo get_permalink($item->ID); ?>" target="_blank"><?php echo ctv_ml($item->post_title); ?></a></label></div>
				<?php } ?>
			</div>
		</td>		
	</tr>					
	<?php	
}

add_action('premium_action_pn_htmlmap','def_premium_action_pn_htmlmap');
function def_premium_action_pn_htmlmap(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator'));
	
	$new_exclude_page = array();
	$exclude_page = is_param_post('exclude_page');
	if(is_array($exclude_page)){
		foreach($exclude_page as $val){
			$new_exclude_page[] = intval($val);
		}
	}
	$premiumbox->update_option('htmlmap','exclude_page',$new_exclude_page);

	$options = array('exchanges','pages','news');					
	foreach($options as $key){
		$premiumbox->update_option('htmlmap',$key, intval(is_param_post($key)));
	}				

	do_action('pn_htmlmap_option_post');
	
	$url = admin_url('admin.php?page=pn_htmlmap&reply=true');
	wp_redirect($url);
	exit;
} 

global $premiumbox;
$premiumbox->auto_include($path.'/shortcode');