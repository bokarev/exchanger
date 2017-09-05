<?php
if( !defined( 'ABSPATH')){ exit(); }

remove_action('wp_head','start_post_rel_link',10,0);
remove_action('wp_head','index_rel_link');
remove_action('wp_head','adjacent_posts_rel_link_wp_head', 10, 0 );
remove_action('wp_head','wp_shortlink_wp_head', 10, 0 );
remove_action('wp_head','feed_links_extra', 3);
remove_action('wp_head','feed_links', 2);

remove_action('wp_head','print_emoji_detection_script',7);
remove_action('wp_print_styles','print_emoji_styles',10);

if (function_exists('register_nav_menu')) {
	register_nav_menu('the_top_menu', __('Top menu for Guests','pntheme'));
	register_nav_menu('the_top_menu_user', __('Top menu for Users','pntheme'));
	register_nav_menu('the_bottom_menu', __('Bottom menu','pntheme'));
}
function no_menu(){}

function no_menu_standart(){
	wp_nav_menu(array(
		'sort_column' => 'menu_order',
		'container' => 'div',
		'container_class' => 'menu',
		'menu_class' => 'tmenu js_menu',
		'menu_id' => '',
		'depth' => '3',
		'fallback_cb' => 'no_menu',
		'theme_location' => 'the_top_menu'
	));	
}

/* кол-во знаком в короткой новости */
function new_excerpt_length($length) {
	return 45;
}
add_filter('excerpt_length', 'new_excerpt_length');

/* новое окончание короткой новости */
function new_excerpt_more($more) {
	return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');

register_sidebar(array(
    'name'=> __('Sidebar'),
	'id' => 'unique-sidebar-id',
	'before_title' => '<div class="widget_title"><div class="widget_titlevn">',
	'after_title' => '</div></div>',
	'before_widget' => '<div class="widget"><div class="widget_ins">',
	'after_widget' => '<div class="clear"></div></div></div>',
));

add_action('wp_enqueue_scripts', 'my_themeinit', 0);
function my_themeinit(){
global $or_template_directory, $premiumbox;

	$vers = $premiumbox->plugin_version;
	if($premiumbox->is_debug_mode()){
		$vers = current_time('timestamp');
	}
	if(!function_exists('is_mobile') or function_exists('is_mobile') and !is_mobile()){	
		wp_deregister_style('open-sans');
		wp_enqueue_style('open-sans', is_ssl_url("http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700&subset=latin,cyrillic-ext,cyrillic"), false, "1.0");
		wp_enqueue_script("jquery select", $or_template_directory . "/js/jquery-select.js", false, $vers);
	}

}

global $or_template_directory;
add_theme_support('custom-background', array(
	'default-color'      => '#e4eae7',
	'default-image' => $or_template_directory . '/images/bg.png',
	'default-repeat' => '',
));

/* lang list */
function the_lang_list($wrap_class=''){
	$list = '';
	
	if(is_ml()){
		
		$list .= '<div class="'. $wrap_class .'">';
		
		$lang = get_locale();
		$langs = get_langs_ml();
		
		$list .= '
		<div class="langlist_div">
			<div class="langlist_title"><span>'. get_lang_key($lang) .'</span></div>
			<div class="langlist_ul">
		';
			foreach($langs as $lan){
				$cl = '';
				if($lan == $lang){ $cl = '';}
				$list .= '
				<a href="'. lang_self_link($lan) .'" class="langlist_li '. $cl .'">
					<div class="langlist_liimg">
						<img src="'. get_lang_icon($lan) .'" alt="" />
					</div>
					'. get_title_forkey($lan) .'
				</a>';	
			}
		$list .= '
			</div>
		</div>';
		

		$list .= '</div>';	
	}
	
	echo $list;
}
/* end lang list */