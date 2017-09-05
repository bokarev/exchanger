<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Закладки для админ-бара[:ru_RU][en_US:]Bookmarks for admin-bar[:en_US]
description: [ru_RU:]Закладки для админ-бара[:ru_RU][en_US:]Bookmarks for admin-bar[:en_US]
version: 1.0
category: [ru_RU:]Навигация[:ru_RU][en_US:]Navigation[:en_US]
cat: nav
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_fav');
function bd_pn_moduls_active_fav(){
global $wpdb;	
	
	$table_name= $wpdb->prefix ."user_fav";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`user_id` bigint(20) NOT NULL default '0',
        `link` varchar(250) NOT NULL default '0',
		`title` varchar(250) NOT NULL default '0',
		`menu_order` bigint(20) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);
	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."user_fav LIKE 'menu_order'");
	if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."user_fav ADD `menu_order` bigint(20) NOT NULL default '0'");
	}	
}
/* end BD */
 
add_action('pn_bd_activated', 'bd_pn_moduls_migrate_fav');
function bd_pn_moduls_migrate_fav(){
global $wpdb;

	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."user_fav LIKE 'menu_order'");
	if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."user_fav ADD `menu_order` bigint(20) NOT NULL default '0'");
	}
}

add_action('wp_before_admin_bar_render', 'wp_before_admin_bar_render_fav', 9);
function wp_before_admin_bar_render_fav() {
global $wp_admin_bar, $wpdb, $premiumbox;
	
    if(current_user_can('read')){
		$ui = wp_get_current_user();
		$user_id = intval($ui->ID);	
	
		$wp_admin_bar->add_menu( array(
			'id'     => 'new_fav',
			'href' => '#',
			'title'  => '<div id="add_userfav" style="height: 32px; padding: 0 0px 0 30px; background: url('. $premiumbox->plugin_url .'images/fav.png) no-repeat 0 center">'. __('Add to bookmarks','pn') .'</div>',
			'meta' => array( 
				'title' => __('Add to bookmarks','pn') 
			)		
		));	
		
		$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."user_fav WHERE user_id='$user_id' ORDER BY menu_order ASC");
		foreach($items as $item){
			$wp_admin_bar->add_menu( array(
				'id'     => 'new_fav'.$item->id,
				'href' => esc_attr($item->link),
				'parent' => 'new_fav',
				'title'  => '<div style="height: 32px; padding: 0 40px 0 0px; position: relative;">'. pn_strip_input($item->title) .'<div class="remove_userfav" data-id="'. $item->id .'" style="height: 32px; width: 30px; position: absolute; top: 0; right: 0px; background: url('. $premiumbox->plugin_url .'images/remove.png) no-repeat center center" title="'. __('Delete','pn') .'"></div></div>',
				'meta' => array( 
					'title' => pn_strip_input($item->title) 
				)		
			));				
		}
		
	}
}

add_action('delete_user', 'delete_user_fav');
function delete_user_fav($user_id){
global $wpdb;
	
	$wpdb->query("DELETE FROM ". $wpdb->prefix ."user_fav WHERE user_id = '$user_id'");
}


add_action('admin_footer','fav_admin_footer');
add_action('wp_footer','fav_admin_footer');
function fav_admin_footer(){ 
	if(current_user_can('read')){
?>	
<script type="text/javascript">
jQuery(function($){

	$('#add_userfav').on('click',function(){

		var pageTitle = $('title').html();
		var new_url = encodeURIComponent(window.location.href);
		var param = 'link='+ new_url +'&title='+ encodeURIComponent(pageTitle);	
		$.ajax({
			type: "POST",
			url: "<?php pn_the_link_post('fav_add_link'); ?>",
			dataType: 'json',
			data: param,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{		
				window.location.href = '';
			}
		});	

		return false;
	});	
	
	$('.remove_userfav').on('click',function(){

		var param = 'id='+ $(this).attr('data-id');	
		$.ajax({
			type: "POST",
			url: "<?php pn_the_link_post('fav_remove_link');?>",
			dataType: 'json',
			data: param,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{		
				window.location.href = '';
			}
		});	

		return false;
	});		
	
});	
</script>
<?php	
	}
}

add_action('premium_action_fav_add_link', 'pn_premium_action_fav_add_link');
function pn_premium_action_fav_add_link(){
global $wpdb;	
	
	only_post();
	
	$log = array();
	$log['response'] = '';
	$log['status'] = '';
	$log['status_code'] = 0;
	$log['status_text'] = '';
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	if(current_user_can('read')){
	
		$link = pn_strip_input(urldecode(is_param_post('link')));
		$title = pn_strip_input(urldecode(is_param_post('title')));
		$title = explode('‹', $title);
		$title = pn_maxf_mb($title[0], 240);
		
		$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."user_fav WHERE user_id='$user_id' AND link='$link'");
		if($cc == 0){
			$array = array();
			$array['user_id'] = $user_id;
			$array['link'] = $link;
			$array['title'] = $title;
			$wpdb->insert($wpdb->prefix ."user_fav", $array);		
		}
	}
	
	echo json_encode($log);
	exit;
}

add_action('premium_action_fav_remove_link', 'pn_premium_action_fav_remove_link');
function pn_premium_action_fav_remove_link(){
global $wpdb;	
	
	only_post();
	
	$log = array();
	$log['response'] = '';
	$log['status'] = '';
	$log['status_code'] = 0;
	$log['status_text'] = '';
	
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);	
	if(current_user_can('read')){
		$id = intval(is_param_post('id'));
		$wpdb->query("DELETE FROM ". $wpdb->prefix ."user_fav WHERE user_id = '$user_id' AND id='$id'");
	}
	
	echo json_encode($log);	
	exit;
}

add_action('admin_menu', 'admin_menu_fav');
function admin_menu_fav(){
global $premiumbox;	
	add_submenu_page("pn_moduls", __('Sort bookmarks','pn'), __('Sort bookmarks','pn'), 'read', "pn_sort_fav", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_sort_fav', 'def_adminpage_title_pn_sort_fav');
function def_adminpage_title_pn_sort_fav($page){
	_e('Sort bookmarks','pn');
}

add_action('pn_adminpage_content_pn_sort_fav','def_pn_adminpage_content_pn_sort_fav');
function def_pn_adminpage_content_pn_sort_fav(){
global $wpdb;

	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);

	$datas = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."user_fav WHERE user_id = '$user_id' ORDER BY menu_order ASC");	
	$sort_list = array();
	foreach($datas as $item){
		$sort_list[0][] = array(
			'title' => pn_strip_input($item->title),
			'id' => $item->id,
			'number' => $item->id,
		);		
	}
	
	pn_sort_one_screen($sort_list);	
?>
<script type="text/javascript">
$(document).ready(function(){ 									   
	$(".thesort ul").sortable({ 
		opacity: 0.6, 
		cursor: 'move',
		revert: true,
		update: function() {
			$('#premium_ajax').show();
			
			var order = $(this).sortable("serialize"); 
			$.post("<?php pn_the_link_ajax(); ?>", order, function(theResponse){
				$('#premium_ajax').hide();
			}); 															 
		}	 				
	});
});	
</script>	
<?php 
} 

add_action('premium_action_pn_sort_fav','def_premium_action_pn_sort_fav');
function def_premium_action_pn_sort_fav(){
global $wpdb;	

	if(current_user_can('read')){
		$number = is_param_post('number');
		$y = 0;
		if(is_array($number)){	
			foreach($number as $theid) { $y++;
				$theid = intval($theid);
				$wpdb->query("UPDATE ".$wpdb->prefix."user_fav SET menu_order='$y' WHERE id = '$theid'");	
			}	
		}
	}
} 