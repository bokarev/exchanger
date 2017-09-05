<?php
if( !defined( 'ABSPATH')){ exit(); }

/* 
Подключаем к меню
*/
add_action('admin_menu', 'admin_menu_admin');
function admin_menu_admin(){
global $premiumbox;	

	add_submenu_page("pn_config", __('Admin Panel','pn'), __('Admin Panel','pn'), 'administrator', "pn_admin", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_admin', 'pn_adminpage_title_pn_admin');
function pn_adminpage_title_pn_admin(){
	_e('Admin Panel','pn');
}

/* настройки */
add_action('pn_adminpage_content_pn_admin','def_pn_adminpage_content_pn_admin');
function def_pn_adminpage_content_pn_admin(){
global $premiumbox;	
	
	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Widgets on the main page','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
		$options['w0'] = array(
			'view' => 'select',
			'title' => __('Hide Welcome Panel','pn'),
			'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
			'default' => $premiumbox->get_option('admin','w0'),
			'name' => 'w0',
			'work' => 'int',
		);		
		$options['w1'] = array(
			'view' => 'select',
			'title' => __('Hide At a Glance','pn'),
			'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
			'default' => $premiumbox->get_option('admin','w1'),
			'name' => 'w1',
			'work' => 'int',
		);
		$options['w2'] = array(
			'view' => 'select',
			'title' => __('Hide Activity','pn'),
			'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
			'default' => $premiumbox->get_option('admin','w2'),
			'name' => 'w2',
			'work' => 'int',
		);
		$options['w3'] = array(
			'view' => 'select',
			'title' => __('Hide Quick Drafts','pn'),
			'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
			'default' => $premiumbox->get_option('admin','w3'),
			'name' => 'w3',
			'work' => 'int',
		);
		$options['w4'] = array(
			'view' => 'select',
			'title' => __('Hide WordPress News','pn'),
			'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
			'default' => $premiumbox->get_option('admin','w4'),
			'name' => 'w4',
			'work' => 'int',
		);
		$options['w5'] = array(
			'view' => 'select',
			'title' => __('Hide Recent Comments','pn'),
			'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
			'default' => $premiumbox->get_option('admin','w5'),
			'name' => 'w5',
			'work' => 'int',
		);
		$options['w6'] = array(
			'view' => 'select',
			'title' => __('Hide Incoming Refs','pn'),
			'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
			'default' => $premiumbox->get_option('admin','w6'),
			'name' => 'w6',
			'work' => 'int',
		);
		$options['w7'] = array(
			'view' => 'select',
			'title' => __('Hide Plugins','pn'),
			'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
			'default' => $premiumbox->get_option('admin','w7'),
			'name' => 'w7',
			'work' => 'int',
		);
		$options['w8'] = array(
			'view' => 'select',
			'title' => __('Hide Recent Drafts','pn'),
			'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
			'default' => $premiumbox->get_option('admin','w8'),
			'name' => 'w8',
			'work' => 'int',
		);
	$options['center_title'] = array(
		'view' => 'h3',
		'title' => __('Menu Sections','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
		$options['ws0'] = array(
			'view' => 'select',
			'title' => sprintf(__('Hide section "%s"','pn'), __('Posts','pn')),
			'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
			'default' => $premiumbox->get_option('admin','ws0'),
			'name' => 'ws0',
			'work' => 'int',
		);	
		$options['ws1'] = array(
			'view' => 'select',
			'title' => sprintf(__('Hide section "%s"','pn'), __('Comments','pn')),
			'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
			'default' => $premiumbox->get_option('admin','ws1'),
			'name' => 'ws1',
			'work' => 'int',
		);	
	$options['other_title'] = array(
		'view' => 'h3',
		'title' => __('Other','pn'),
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);
		$options['wm0'] = array(
			'view' => 'select',
			'title' => __('Disable e-mail notification when user changes password','pn'),
			'options' => array('0'=>__('No','pn'), '1'=>__('Yes','pn')),
			'default' => $premiumbox->get_option('admin','wm0'),
			'name' => 'wm0',
			'work' => 'int',
		);			
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pn'),
		'colspan' => 2,
	);		
			
	pn_admin_one_screen('', $options);	  
} 

/* обработка */
add_action('premium_action_pn_admin','def_premium_action_pn_admin');
function def_premium_action_pn_admin(){
global $wpdb, $premiumbox;	

	only_post();

	pn_only_caps(array('administrator'));
		
		$options = array();

		$options['w0'] = array(
			'view' => 'select',
			'name' => 'w0',
			'work' => 'int',
		);		
		$options['w1'] = array(
			'view' => 'select',
			'name' => 'w1',
			'work' => 'int',
		);
		$options['w2'] = array(
			'view' => 'select',
			'name' => 'w2',
			'work' => 'int',
		);
		$options['w3'] = array(
			'view' => 'select',
			'name' => 'w3',
			'work' => 'int',
		);
		$options['w4'] = array(
			'view' => 'select',
			'name' => 'w4',
			'work' => 'int',
		);
		$options['w5'] = array(
			'view' => 'select',
			'name' => 'w5',
			'work' => 'int',
		);
		$options['w6'] = array(
			'view' => 'select',
			'name' => 'w6',
			'work' => 'int',
		);
		$options['w7'] = array(
			'view' => 'select',
			'name' => 'w7',
			'work' => 'int',
		);
		$options['w8'] = array(
			'view' => 'select',
			'name' => 'w8',
			'work' => 'int',
		);
		$options['ws0'] = array(
			'view' => 'select',
			'name' => 'ws0',
			'work' => 'int',
		);	
		$options['ws1'] = array(
			'view' => 'select',
			'name' => 'ws1',
			'work' => 'int',
		);
		$options['wm0'] = array(
			'view' => 'select',
			'name' => 'wm0',
			'work' => 'int',
		);			
			
		$data = pn_strip_options('', $options);
		foreach($data as $key => $val){
			$premiumbox->update_option('admin', $key, $val);
		}		
				
	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
			
	wp_safe_redirect($back_url);
	exit;
} 

/* выводим время лицензии */
add_action('wp_dashboard_setup', 'license_wp_dashboard_setup' );
function license_wp_dashboard_setup() {
	wp_add_dashboard_widget('license_pn_dashboard_widget', __('License Info','pn'), 'dashboard_license_pn_in_admin_panel');
}

function dashboard_license_pn_in_admin_panel(){
global $wpdb;

	$text = __('No data available','pn');
	$end_time = get_license_time();
	if($end_time){
		$time = current_time('timestamp');
		$cou_days = ceil(($end_time - $time) / 24 / 60 / 60);
		$cou_days = intval($cou_days);
		if($cou_days == 0){
			$text = ' <span class="bred">'. __('License validity period expires today','pn') .'</span>';
		} elseif($cou_days <= 7){
			$text = ' <span class="bred">'.sprintf(__('Days till license expiration date: %s days','pn'), $cou_days).'</span>';
		} else {
			$text = ' '.sprintf(__('Days till license expiration date: %s days','pn'), $cou_days);
		}
	}
	echo $text;
}

/* скрываем ненужные вещи */
add_filter( 'admin_footer_text', 'def_admin_footer_text' );
function def_admin_footer_text(){
	$text = '&copy; '. get_copy_date('2015') .' <strong>Premium Exchanger</strong>.';
	$end_time = get_license_time();
	if($end_time){
		$time = current_time('timestamp');
		$cou_days = ceil(($end_time - $time) / 24 / 60 / 60);
		$cou_days = intval($cou_days);
		if($cou_days == 0){
			$text .= ' (<span class="bred">'. __('License validity period expires today','pn') .'</span>)';
		} elseif($cou_days <= 7){
			$text .= ' (<span class="bred">'.sprintf(__('Days till license expiration date: %s days','pn'), $cou_days).'</span>)';
		} else {
			$text .= ' ('.sprintf(__('Days till license expiration date: %s days','pn'), $cou_days).')';
		}
	}
	
	return $text;
}

global $premiumbox;
if($premiumbox->get_option('admin', 'w0') == 1){
	remove_action( 'welcome_panel', 'wp_welcome_panel' );
}

add_action('wp_dashboard_setup', 'pn_remove_dashboard_widgets' );
function pn_remove_dashboard_widgets() {
global $premiumbox;

	if($premiumbox->get_option('admin','w1') == 1){
		remove_meta_box('dashboard_right_now', 'dashboard', 'normal'); 
	}
	if($premiumbox->get_option('admin','w2') == 1){
		remove_meta_box('dashboard_activity', 'dashboard', 'normal'); 
	}	
	if($premiumbox->get_option('admin','w3') == 1){
		remove_meta_box('dashboard_quick_press', 'dashboard', 'side'); 
	}	
	if($premiumbox->get_option('admin','w4') == 1 or function_exists('is_ml') and is_ml()){
		remove_meta_box('dashboard_primary', 'dashboard', 'side'); 
	}	
	if($premiumbox->get_option('admin','w5') == 1){
		remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
	}
	if($premiumbox->get_option('admin','w6') == 1){
		remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
	}
	if($premiumbox->get_option('admin','w7') == 1){
		remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
	}	
	if($premiumbox->get_option('admin','w8') == 1){
		remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
	}
	
	remove_meta_box('dashboard_secondary', 'dashboard', 'side');
}

add_filter( 'gettext', 'pn_remove_howdy', 10, 3 );
function pn_remove_howdy( $translation, $text, $domain ) {
	if ( $text == 'Howdy, %1$s' ){
		return '%1$s';
	}
	
		return $translation;
}

add_action( 'widgets_init', 'pn_remove_default_widget' );
function pn_remove_default_widget() {
    unregister_widget('WP_Widget_RSS');
	unregister_widget('WP_Widget_Calendar');
	unregister_widget('WP_Widget_Tag_Cloud');		
	unregister_widget('WP_Nav_Menu_Widget');
    unregister_widget('WP_Widget_Recent_Posts');
	unregister_widget('WP_Widget_Pages');
	unregister_widget('WP_Widget_Archives');		
	unregister_widget('WP_Widget_Meta');	
	unregister_widget('WP_Widget_Search');
	if(defined('PN_COMMENT_STATUS') and constant('PN_COMMENT_STATUS') != 'true'){
		unregister_widget('WP_Widget_Recent_Comments');
	}	
	unregister_widget('WP_Widget_Categories');
	/* unregister_widget('WP_Widget_Text'); */		
}

add_action( 'admin_menu', 'pn_remove_meta_boxes' );
function pn_remove_meta_boxes() {
global $menu, $premiumbox, $submenu;
	
	if(function_exists('is_ml') and is_ml()){
		remove_meta_box('postexcerpt', 'post', 'normal');
	}
	remove_meta_box('trackbacksdiv', 'post', 'normal');
	remove_meta_box('postcustom', 'post', 'normal');
	remove_meta_box('trackbacksdiv', 'page', 'normal');
	remove_meta_box('postcustom', 'page', 'normal');
	if(defined('PN_COMMENT_STATUS') and constant('PN_COMMENT_STATUS') != 'true'){
		remove_meta_box('commentsdiv', 'post', 'normal');
		remove_meta_box('commentsdiv', 'page', 'normal');
	}	
	
	$restricted = array();
	if($premiumbox->get_option('admin','ws0') == 1){
		$restricted[] = __('Posts');
	}
	if($premiumbox->get_option('admin','ws1') == 1){
		$restricted[] = __('Comments');
	}	
	
	remove_submenu_page('themes.php', 'customize.php');

	if(isset($submenu['themes.php'])){
        foreach($submenu[ 'themes.php' ] as $index => $menu_item){
            if(in_array(__('Customize'), $menu_item)) {
                unset($submenu['themes.php'][$index]);
            }
        }
    }
	
	end($menu);
	while(prev($menu)){
		$value = explode(' ',$menu[key($menu)][0]);
		if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){
			unset($menu[key($menu)]);
		}
	}	
}

remove_action( 'wp_head', 'wp_generator' );

foreach ( array( 'rss2_head', 'commentsrss2_head', 'rss_head', 'rdf_header', 'atom_head', 'comments_atom_head', 'opml_head', 'app_head' ) as $action ) {
	remove_action( $action, 'the_generator' );
}

// Отключаем сам REST API
add_filter('rest_enabled', '__return_false');
 
// Отключаем фильтры REST API
remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
remove_action( 'wp_head', 'rest_output_link_wp_head', 10, 0 );
remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );
remove_action( 'auth_cookie_malformed', 'rest_cookie_collect_status' );
remove_action( 'auth_cookie_expired', 'rest_cookie_collect_status' );
remove_action( 'auth_cookie_bad_username', 'rest_cookie_collect_status' );
remove_action( 'auth_cookie_bad_hash', 'rest_cookie_collect_status' );
remove_action( 'auth_cookie_valid', 'rest_cookie_collect_status' );
remove_filter( 'rest_authentication_errors', 'rest_cookie_check_errors', 100 );
 
// Отключаем события REST API
remove_action( 'init', 'rest_api_init' );
remove_action( 'rest_api_init', 'rest_api_default_filters', 10, 1 );
remove_action( 'parse_request', 'rest_api_loaded' );
 
// Отключаем Embeds связанные с REST API
remove_action( 'rest_api_init', 'wp_oembed_register_route');
remove_filter( 'rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10, 4 );
 
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

remove_action( 'wp_head', 'rsd_link');
remove_action( 'wp_head', 'wlwmanifest_link');

add_action('wp_before_admin_bar_render', 'pn_admin_bar_links');
function pn_admin_bar_links() {
global $wp_admin_bar, $premiumbox;

    $wp_admin_bar->remove_menu('wp-logo'); 
	$wp_admin_bar->remove_menu('new-media');
	$wp_admin_bar->remove_menu('new-link');
	$wp_admin_bar->remove_menu('themes');
	$wp_admin_bar->remove_menu('search');
	$wp_admin_bar->remove_menu('customize');
	if($premiumbox->get_option('admin','ws0') == 1){
		$wp_admin_bar->remove_menu('new-post');
	}
	if($premiumbox->get_option('admin','ws1') == 1){
		$wp_admin_bar->remove_menu('comments');
	}	
}

add_filter('the_content', 'do_shortcode', 10);		
add_filter('comment_text', 'do_shortcode', 10);

add_action( 'parse_query', 'pn_search_turn_off' );
function pn_search_turn_off( $q, $e = true ) {
	if ( is_search() ) {
		$q->is_search = false;
		$q->query_vars['s'] = false;
		$q->query['s'] = false;	
		if ( $e == true ){
			$q->is_404 = true;
		}
	}
}

add_filter( 'get_search_form', 'def_get_search_form');
function def_get_search_form(){
	return null;
}

if ( !function_exists('wp_new_user_notification') ){
	function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {	
		$action = is_param_post('action');
		$pass = is_param_post('pass1');
		$send = is_param_post('send_user_notification');
		if($action == 'createuser' and $send == 1){
			$user = get_userdata( $user_id );
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$message = sprintf(__('Username: %s'), $user->user_login) . "<br />";
			$message .= sprintf(__('Password: %s'), $pass) . "<br />";
			wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);
		}
	}
}

add_filter('send_password_change_email', 'def_send_password_change_email', 1, 3);
function def_send_password_change_email($send, $user, $userdata){
global $premiumbox;	
	if($premiumbox->get_option('admin','wm0') == 1){
		if(isset($userdata['ID']) and !user_can( $userdata['ID'], 'administrator' )){
			return false;
		} 
	}
			
	return $send;	
}

/* login form */
add_filter('login_headerurl', 'def_login_headerurl' );
function def_login_headerurl($login_header_url){
	
	$login_header_url = 'https://premiumexchanger.com/';
	
	return $login_header_url;
}

add_filter('login_headertitle', 'def_login_headertitle' );
function def_login_headertitle($login_header_title){
	
	$login_header_title = 'PremiumExchanger';
	
	return $login_header_title;
}

add_action('login_head','def_login_head');
function def_login_head(){
global $premiumbox;		
?>
<style>
.login h1 a {
height: 108px;
width: 108px;
background: url(<?php echo $premiumbox->plugin_url; ?>/images/admin-logo.png) no-repeat center center;	
}
</style>
<?php
} 
/* end login form */