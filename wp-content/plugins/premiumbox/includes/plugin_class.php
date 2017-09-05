<?php
if( !defined( 'ABSPATH')){ exit(); }

if(!class_exists('PremiumBox')){
	class PremiumBox extends Premium {

		function __construct($debug_mode=0)
		{
		
			$this->debug_constant_name = 'PN_RICH_TEST';
			$this->plugin_version = '1.2';
			$this->plugin_name = 'Premium Exchanger';
			$this->plugin_path = PN_PLUGIN_NAME;
			$this->plugin_dir = PN_PLUGIN_DIR;
			$this->plugin_url = PN_PLUGIN_URL;
			$this->plugin_prefix = 'pn';
			$this->plugin_option_name = 'change';
			$this->plugin_page_name = 'the_pages';
			
			parent::__construct($debug_mode);
			
			/* Мультизаголовок для русскоязычных пользователей */
			add_filter('all_plugins', array($this, 'plugin_all_plugins'));
			
			/* Действия при активации: устанавливаем настройки, добавляем таблицы, добавляем страницы */
			add_action('activate_'. $this->plugin_path, array($this, 'premiumbox_plugin_activate'));
			
			/* увеличиваем время жизни сессии */
			add_filter( 'auth_cookie_expiration', array($this, 'premiumbox_auth_cookie_expiration'), 10, 3 );
			
			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
			add_action('admin_menu', array($this, 'admin_menu'),0);
			
			add_action('wp_enqueue_scripts', array($this, 'pn_themeinit'),0);
			
			add_action('myaction_site_logout', array($this, 'myaction_site_logout')); 
			add_filter('logout_url', array($this, 'premiumbox_logout_url'));
			
			add_filter('query_vars', array($this, 'query_vars'));
			add_filter('generate_rewrite_rules', array($this, 'generate_rewrite_rules'));
			
			add_action('init', array($this,'deprecated_pages'));
			
			add_filter($this->plugin_prefix.'_tech_pages', array($this, 'list_tech_pages'));
			
			if($this->is_debug_mode()){
				add_action('wp_footer', array($this,'mobile_test_wp_footer'));
			}
			
		}	
		
		public function plugin_all_plugins($plugins){
			global $locale;	
				
			$plugin_path = $this->plugin_path;	
				
			if($locale == 'ru_RU'){ /* русское описание */
				$name = 'Premium Exchanger';
				$description = 'Профессиональный обменный пункт';
					
				$plugins[$plugin_path]['Name'] = $name;
				$plugins[$plugin_path]['Description'] = $description;	
			} 	
				
			return $plugins;
		}	

		public function premiumbox_plugin_activate(){
			/* после переактивации, ставим режим апдейта */
			$this->update_option('up_mode','', 1);
		}
		
		public function list_tech_pages($pages){
			 
			$pages[] = array(
				'post_name'      => 'home',
				'post_title'     => '[ru_RU:]Главная[:ru_RU][en_US:]Home[:en_US]',
				'post_content'   => '',
				'post_template'   => 'pn-homepage.php',
			);
			$pages[] = array(
				'post_name'      => 'news',
				'post_title'     => '[ru_RU:]Новости[:ru_RU][en_US:]News[:en_US]',
				'post_content'   => '',
				'post_template'   => '',
			);	
			$pages[] = array( 
				'post_name'      => 'tos',
				'post_title'     => '[ru_RU:]Правила сайта[:ru_RU][en_US:]Rules[:en_US]',
				'post_content'   => '',
				'post_template'   => '',
			);	
			$pages[] = array( 
				'post_name'      => 'notice',
				'post_title'     => '[ru_RU:]Предупреждение[:ru_RU][en_US:]Warning messages[:en_US]',
				'post_content'   => '',
				'post_template'   => '',
			);				
			$pages[] = array(
				'post_name'      => 'login',
				'post_title'     => '[ru_RU:]Авторизация[:ru_RU][en_US:]Authorization[:en_US]',
				'post_content'   => '[login_page]',
				'post_template'   => 'pn-pluginpage.php',
			);
			$pages[] = array(
				'post_name'      => 'register',
				'post_title'     => '[ru_RU:]Регистрация[:ru_RU][en_US:]Register[:en_US]',
				'post_content'   => '[register_page]',
				'post_template'   => 'pn-pluginpage.php',
			);
			$pages[] = array(
				'post_name'      => 'lostpass',
				'post_title'     => '[ru_RU:]Восстановление пароля[:ru_RU][en_US:]Password recovery[:en_US]',
				'post_content'   => '[lostpass_page]',
				'post_template'   => 'pn-pluginpage.php',
			);
			$pages[] = array(
				'post_name'      => 'account',
				'post_title'     => '[ru_RU:]Личный кабинет[:ru_RU][en_US:]Personal account[:en_US]',
				'post_content'   => '[account_page]',
				'post_template'   => 'pn-pluginpage.php',
			);	
			$pages[] = array(
				'post_name'      => 'security',
				'post_title'     => '[ru_RU:]Настройки безопасности[:ru_RU][en_US:]Security settings[:en_US]',
				'post_content'   => '[security_page]',
				'post_template'   => 'pn-pluginpage.php',
			);								
			$pages[] = array(
				'post_name'      => 'exchange',
				'post_title'     => '[ru_RU:]Обмен[:ru_RU][en_US:]Exchange[:en_US]',
				'post_content'   => '[exchange]',
				'post_template'   => 'pn-pluginpage.php',
			);	
			$pages[] = array(
				'post_name'      => 'hst',
				'post_title'     => '[ru_RU:]Обмен - шаги[:ru_RU][en_US:]Exchange - steps[:en_US]',
				'post_content'   => '[exchangestep]',
				'post_template'   => 'pn-pluginpage.php',
			);		
			
			return $pages;
		}		
		
		public function premiumbox_auth_cookie_expiration( $expiration, $user_id, $remember ){
			if(defined('PN_USERSESS_DAY')){
				$session_day = intval(PN_USERSESS_DAY);
				if($session_day <= 0){ $session_day = 3; }
				$expiration = PN_USERSESS_DAY * DAY_IN_SECONDS;
			}
			return $expiration;
		} 	

		public function up_mode($method=''){
			if(!$method){ $method = trim(is_param_get('meth')); }
			if($method != 'post'){ $method = 'get'; }
			if($this->get_option('up_mode') == 1){
				if($method == 'get'){
					pn_display_mess(__('Maintenance','pn')); 
				} else {
					$log = array();
					$log['status'] = 'error';
					$log['status_code'] = '-1'; 
					$log['status_text'] = __('Maintenance','pn');
					echo json_encode($log);
					exit;	
				}
			}
		}
		
		public function admin_menu(){ 
			
			add_submenu_page("index.php", __('Migration','pn'), __('Migration','pn'), 'administrator', "pn_migrate", array($this, 'admin_temp'));
			add_menu_page(__('Exchange office settings','pn'), __('Exchange office settings','pn'), 'administrator', 'pn_config', array($this, 'admin_temp'), $this->get_icon_link('settings'));
			add_submenu_page("pn_config", __('General settings','pn'), __('General settings','pn'), 'administrator', "pn_config", array($this, 'admin_temp'));
			add_menu_page(__('Theme settings','pn'), __('Theme settings','pn'), 'administrator', 'pn_themeconfig', array($this, 'admin_temp'), $this->get_icon_link('theme'));
			add_menu_page(__('Merchants','pn'), __('Merchants','pn'), 'administrator', 'pn_merchants', array($this, 'admin_temp'), $this->get_icon_link('merchants'));
			$hook = add_menu_page(__('Modules','pn'), __('Modules','pn'), 'administrator', 'pn_moduls', array($this, 'admin_temp'), $this->get_icon_link('moduls'));
			add_action( "load-$hook", 'pn_trev_hook' );
			
		}

		public function deprecated_pages(){
			
			$plugin = basename($this->plugin_path,'.php');
			$wp_content = ltrim(str_replace(ABSPATH,'',WP_CONTENT_DIR),'/');
			
			add_rewrite_rule('rtest.html$', $wp_content . '/plugins/'. $plugin .'/sitepage/test.php', 'top');

			/* old */
			add_rewrite_rule('blackping.html$', $wp_content . '/plugins/'.$plugin.'/sitepage/blackping.php', 'top');
			add_rewrite_rule('curscron.html$', $wp_content . '/plugins/'. $plugin .'/sitepage/curscron.php', 'top');
			add_rewrite_rule('sitemap.xml$', $wp_content . '/plugins/'.$plugin.'/sitepage/sitemap.php', 'top');
			add_rewrite_rule('exportxml.xml$', $wp_content .'/plugins/'.$plugin.'/sitepage/exportxml.php', 'top');
			add_rewrite_rule('exporttxt.txt$', $wp_content .'/plugins/'.$plugin.'/sitepage/exporttxt.php', 'top');
			
		}
		
		public function admin_enqueue_scripts(){
			$pn_version = get_premium_version();
			$plugin_vers = $this->plugin_version;
			if($this->is_debug_mode()){
				$pn_version = $plugin_vers = current_time('timestamp');
			}			
			$plugin_url = get_premium_url();
			
			$screen_id = pn_get_current_screen();
			if($screen_id != 'nav-menus'){
				wp_deregister_script('jquery');
				wp_register_script('jquery', $plugin_url . 'js/jquery.min.js', false, '3.2.1');
				wp_enqueue_script('jquery');				
				
				wp_enqueue_script('jquery-ui', $plugin_url . 'js/jquery-ui/script.min.js', false, '1.11.4');
				wp_enqueue_script("jform", $plugin_url . "js/jquery.form.js", false, "3.51");

				wp_enqueue_script("jquery-cookie", $plugin_url ."js/jcook.js", false, "2.1.4");
				wp_enqueue_script("jquery-clipboard", $plugin_url ."js/clipboard.min.js", false, "1.5.15");
			}
			
			if (isset($_GET['page']) and preg_match('/^pn_/i',$_GET['page'])){
		
				wp_enqueue_style('premiumbox style', $this->plugin_url . "premiumbox.css", false, $plugin_vers);
		
				wp_enqueue_script("jquery-prbar", $plugin_url ."js/jquery-prbar.js", false, $pn_version);
		
				$locale = get_locale();
				if($locale == 'ru_RU'){
					wp_enqueue_script("jquery-datepicker", $plugin_url . "js/jquery-ui/jquery.ui.datepicker-ru.js", false, $pn_version);
					wp_enqueue_script("jquery-timepicker", $plugin_url . "js/jquery-ui/jquery-ui-timepicker-addon.js", false, $pn_version);
				} else {
					wp_enqueue_script("jquery-timepicker", $plugin_url . "js/jquery-ui/jquery-ui-timepicker-addon-en.js", false, $pn_version);
				}
			
				wp_enqueue_script('premium config', $plugin_url . 'js/config.js', false, $pn_version);
		
				wp_enqueue_media();
				wp_register_script( 'tgm-nmp-media', $plugin_url . 'js/media.js' , array( 'jquery' ), $pn_version, true );
				wp_localize_script( 'tgm-nmp-media', 'tgm_nmp_media',
					array(
						'title'     => __('Choose or upload file', $this->plugin_prefix), 
						'button'    => __('Insert file into the field', $this->plugin_prefix),
						'library'   => 'image', 
					)
				);
				wp_enqueue_script( 'tgm-nmp-media' ); 
		
			}				
		}
		
		public function pn_themeinit(){
			global $or_template_directory;
			
			$plugin_url = get_premium_url();
			$pn_version = $this->plugin_version;
			if($this->is_debug_mode()){
				$pn_version = current_time('timestamp');
			}

			if(!function_exists('is_mobile') or !is_mobile()){
				wp_enqueue_style('theme style', $or_template_directory . "/style.css", false, $pn_version);
			}
			
			wp_deregister_script('jquery');
			wp_register_script('jquery', $plugin_url . 'js/jquery.min.js', false, '3.2.1');
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui', $plugin_url . 'js/jquery-ui/script.min.js', false, '1.11.4');
			wp_enqueue_script('jquery forms', $plugin_url . "js/jquery.form.js", false, "3.51");
			wp_enqueue_script("jquery-cookie", $plugin_url ."js/jcook.js", false, "2.1.4");
			if(!function_exists('is_mobile') or !is_mobile()){
				wp_enqueue_script('jquery site js', $or_template_directory.'/js/site.js', false, $pn_version);
			}
		}		
		
		public function premiumbox_logout_url($link){
			return get_ajax_link('logout', 'get');
		}
		
		public function myaction_site_logout(){
		global $or_site_url;	

			wp_logout();
			$url = trim(is_param_post('url'));
			if(!$url){
				if(function_exists('get_site_url_ml')){
					$url = get_site_url_ml();
				} else {
					$url = $or_site_url;
				}
			}
			
			wp_redirect($url);
			exit();				
		}
		
		public function query_vars( $query_vars ){
			$query_vars[] = 'pnhash';
			$query_vars[] = 'hashed';

			return $query_vars;
		}

		public function general_tech_pages(){
			$g_pages = array(
				'exchange' => 'exchange',
				'hst' => 'hst',
			);
			
			return apply_filters('general_tech_pages', $g_pages);			
		}		
		
		public function generate_rewrite_rules($wp_rewrite) {
			
			$g_pages = $this->general_tech_pages();
			
			$rewrite_rules = array (
				$g_pages['exchange'] .'_([\-_A-Za-z0-9]+)$' => 'index.php?pagename=exchange&pnhash=$matches[1]',
				$g_pages['hst'] .'_([A-Za-z0-9]{35})$' => 'index.php?pagename=hst&hashed=$matches[1]',
			);
			$wp_rewrite->rules = array_merge($rewrite_rules, $wp_rewrite->rules);
		
		}		
		
 		public function mobile_test_wp_footer(){
			if(function_exists('mobile_vers_link') and !is_mobile()){
			?>
			<div style="padding: 15px 0; text-align: center;"><a href="<?php echo mobile_vers_link(); ?>">Mobile version only</a></div>
			<?php
			}
		} 
		
	}    
}