<?php
if( !defined( 'ABSPATH')){ exit(); }

/* 
Подключаем к меню
*/
add_action('pn_adminpage_title_pn_xmlmap', 'pn_adminpage_title_pn_xmlmap');
function pn_adminpage_title_pn_xmlmap($page){
	_e('XML sitemap settings','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_xmlmap','def_pn_adminpage_content_pn_xmlmap');
function def_pn_adminpage_content_pn_xmlmap(){
global $wpdb, $premiumbox;


	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('XML sitemap settings','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	
	$options['news'] = array(
		'view' => 'select',
		'title' => __('Show news','pn'),
		'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
		'default' => $premiumbox->get_option('xmlmap','news'),
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
		'default' => $premiumbox->get_option('xmlmap','exchanges'),
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
		'default' => $premiumbox->get_option('xmlmap','pages'),
		'name' => 'pages',
	);		
					
	$options['exclude_page'] = array(
		'view' => 'user_func',
		'func_data' => array(),
		'func' => 'pn_xmlmap_option1',
	);		
	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
	pn_admin_one_screen('pn_xmlmap_option', $options);
} 

function pn_xmlmap_option1(){
global $premiumbox;	
	$args = array(
		'post_type' => 'page',
		'posts_per_page' => '-1'
	);
	$pages = get_posts($args);
	
	$exclude_pages = $premiumbox->get_option('xmlmap','exclude_page');
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

/* обработка */
add_action('premium_action_pn_xmlmap','def_premium_action_pn_xmlmap');
function def_premium_action_pn_xmlmap(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator', 'pn_seo'));

	$new_exclude_page = array();
	$exclude_page = is_param_post('exclude_page');
	if(is_array($exclude_page)){
		foreach($exclude_page as $val){
			$new_exclude_page[] = intval($val);
		}
	}

	$premiumbox->update_option('xmlmap','exclude_page',$new_exclude_page);				
				
	$options = array('exchanges','pages','news');	
					
	foreach($options as $key){
		$val = intval(is_param_post($key));
		$premiumbox->update_option('xmlmap',$key , $val);
	}				

	do_action('xmlmap_changeform_post');
	
	$url = admin_url('admin.php?page=pn_xmlmap&reply=true');
	wp_redirect($url);
	exit;
} 