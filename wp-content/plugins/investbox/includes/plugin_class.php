<?php
if( !defined( 'ABSPATH')){ exit(); }

if(!class_exists('InvestBox')){
	class InvestBox extends Premium {

		function __construct($debug_mode=0)
		{
		
			$this->debug_constant_name = 'INEX_RICH_TEST';
			$this->plugin_name = 'Invest';
			$this->plugin_version = '3.2';
			$this->plugin_path = INEX_PLUGIN_NAME;
			$this->plugin_dir = INEX_PLUGIN_DIR;
			$this->plugin_url = INEX_PLUGIN_URL;
			$this->plugin_prefix = 'inex';
			$this->plugin_option_name = 'inex_change';
			$this->plugin_page_name = 'inex_pages';
			
			parent::__construct($debug_mode);
			
			add_filter('pn_caps',array($this, 'pn_caps'));
			add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
			add_action('admin_menu', array($this, 'admin_menu'),10000);
			add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'), 99);
			add_action('wp_before_admin_bar_render', array($this, 'check_icon'));
			
			add_filter('account_list_pages', array($this, 'account_list_pages'),100);
			
			add_action('after_pn_adminpage_title', array($this, 'after_pn_adminpage_title'), 10, 2);
			
			add_filter($this->plugin_prefix.'_tech_pages', array($this, 'list_tech_pages'));
		}	
		
		function pn_caps($pn_caps){
			$pn_caps['pn_investbox'] = __('Investments','inex');
			return $pn_caps;
		}		
		
		public function admin_menu(){ 
			
			if(current_user_can('administrator') or current_user_can('pn_investbox')){
				$hook = add_menu_page(__('Investments','inex'), __('Investments','inex'), 'read', 'inex_index', array($this, 'admin_temp'), $this->get_icon_link('invest'));  
				add_action( "load-$hook", 'pn_trev_hook' );
				
				add_submenu_page("inex_index",  __('Deposits','inex'),  __('Deposits','inex'), 'read', "inex_index", array($this, 'admin_temp'));
				
				add_submenu_page("inex_index", __('Add deposit','inex'), __('Add deposit','inex'), 'read', "inex_add_index", array($this, 'admin_temp'));
				
				$hook = add_submenu_page("inex_index", __('Payouts','inex'), __('Payouts','inex'), 'read', "inex_out", array($this, 'admin_temp'));
				add_action( "load-$hook", 'pn_trev_hook' );
				
				$hook = add_submenu_page("inex_index", __('Rates','inex'), __('Rates','inex'), 'read', "inex_tars", array($this, 'admin_temp'));
				add_action( "load-$hook", 'pn_trev_hook' );
				
				add_submenu_page("inex_index", __('Add tariff','inex'), __('Add tariff','inex'), 'read', "inex_add_tars", array($this, 'admin_temp'));
				
				add_submenu_page("inex_index", __('Payment systems','inex'), __('Payment systems','inex'), 'read', "inex_system", array($this, 'admin_temp'));
				add_submenu_page("inex_index", __('E-mail templates','inex'), __('E-mail templates','inex'), 'read', "inex_mailtemp", array($this, 'admin_temp'));
				add_submenu_page("inex_index", __('Settings','inex'), __('Settings','inex'), 'read', "inex_settings", array($this, 'admin_temp'));	
				add_submenu_page("inex_index",  __('Migration','inex'),  __('Migration','inex'), 'read', "inex_migrate", array($this, 'admin_temp'));
			}
			
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
			
			if (isset($_GET['page']) and preg_match('/^inex_/i',$_GET['page'])){
		
				wp_enqueue_style('investbox style', $this->plugin_url . "investbox.css", false, $plugin_vers);		
		
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
		
		public function wp_enqueue_scripts(){
			$plugin_url = $this->plugin_url;
			$plugin_vers = $this->plugin_version;
			if($this->is_debug_mode()){
				$plugin_vers = current_time('timestamp');
			}
			wp_enqueue_script('investbox site script', $plugin_url . 'js/inex.js', false, $plugin_vers);
			if($this->get_option('change', 'style') != 1){
				wp_enqueue_style('investbox site style', $plugin_url . 'sitestyle.css', false, $plugin_vers);
			}
		}
		
		public function list_tech_pages($pages){
			
			$pages[] = array(
				'post_name'      => 'toinvest',
				'post_title'     => '[ru_RU:]Инвестировать[:ru_RU][en_US:]Invest[:en_US]',
				'post_content'   => '[toinvest]',
				'post_template'   => 'pn-pluginpage.php',
			);
			$pages[] = array(  
				'post_name'      => 'indeposit',
				'post_title'     => '[ru_RU:]Оплатить депозит[:ru_RU][en_US:]Pay a deposit[:en_US]',
				'post_content'   => '[indeposit]',
				'post_template'   => 'pn-pluginpage.php',
			);		
			
			return $pages;
		}		
		
		public function check_icon(){
			global $wp_admin_bar, $wpdb;
			if(current_user_can('administrator') or current_user_can('pn_investbox')){
				$query = $wpdb->query("CHECK TABLE ".$wpdb->prefix ."inex_deposit");
				if($query == 1){
					$z = $wpdb->query("SELECT id FROM ".$wpdb->prefix."inex_deposit WHERE paystatus = '1' AND zakstatus='1' AND vipstatus = '0'");
					if($z > 0){
						$wp_admin_bar->add_menu( array(
							'id'     => 'new_deposit',
							'href' => admin_url('admin.php?page=inex_out&mod=1'),
							'title'  => '<div style="height: 32px; width: 22px; background: url('. $this->plugin_url .'images/pay.png) no-repeat center center"></div>',
							'meta' => array( 'title' => __('Amount of requests for deposit payout','inex').' ('. $z .')' )		
						));	
					}	
				}
			}			
		}
		
 		function after_pn_adminpage_title($page, $prefix){
			if($prefix == $this->plugin_prefix){
			?>
				<div class="premium_default_window">
					<?php _e('Cron link','inex'); ?><br />
					<a href="<?php echo get_site_url_or(); ?>/request-investcron.html" target="_blank"><?php echo get_site_url_or(); ?>/request-investcron.html</a>
				</div>	
			<?php			
			}
		}
		
		function is_system_name($name){
			$name = pn_string($name);
			if (preg_match("/^[a-zA-z0-9_]{1,250}$/", $name, $matches )) {
				return $name;
			} else {
				return false;
			}
		}	
		
		/* инвестировали в ПС */
		function get_in_system($gid){
			global $wpdb;
			
			$gid = $this->is_system_name($gid);
			$s = $wpdb->get_var("SELECT SUM(insumm) FROM ".$wpdb->prefix."inex_deposit WHERE gid='$gid' AND paystatus='1'"); 
			
			return is_my_money($s, 2);	
		}

		/* на данный момент на депозитах */
		function get_deposit_system($gid){
			global $wpdb;
			
			$gid = $this->is_system_name($gid);
			$s = $wpdb->get_var("SELECT SUM(insumm) FROM ".$wpdb->prefix."inex_deposit WHERE gid='$gid' AND paystatus='1' AND vipstatus='0'"); 
			
			return is_my_money($s, 2);
		}

		/* выплачено из ПС */
		function get_outs_system($gid){
			global $wpdb;
			
			$gid = $this->is_system_name($gid);
			$s = $wpdb->get_var("SELECT SUM(outsumm) FROM ".$wpdb->prefix."inex_deposit WHERE gid='$gid' AND paystatus='1' AND vipstatus='1'"); 
			
			return is_my_money($s, 2);
		}

		/* необходимо выплатить */
		function get_pays_system($gid){
			global $wpdb;
			
			$gid = $this->is_system_name($gid);
			$s = $wpdb->get_var("SELECT SUM(outsumm) FROM ".$wpdb->prefix."inex_deposit WHERE gid='$gid' AND paystatus='1' AND vipstatus='0'"); 
			
			return is_my_money($s, 2);
		}	

		function get_title_ps_by_id($id){
			$systems = apply_filters('invest_systems', array());
			if(isset($systems[$id]['title'])){
				return $systems[$id]['title'];
			} else {
				return '';
			}
		}

		function get_valut_ps_by_id($id){
			$systems = apply_filters('invest_systems', array());
			if(isset($systems[$id]['valut'])){
				return $systems[$id]['valut'];
			} else {
				return '';
			}	
		}

		function check_ps($id){
			$systems = apply_filters('invest_systems', array());
			$checks = array();
			foreach($systems as $k => $v){
				$checks[] = $k;
			}
			return in_array($id, $checks);
		}		
		
		function summ_pers($summ, $pers){
			if($summ > 0 and $pers > 0){
				return $summ + ($summ / 100 * $pers);
			} else {
				return 0;
			}
		}

		function alter_summ($summ, $pers){
			if($summ > 0 and $pers > 0){
				return round(100 * $summ / (100 - $pers),2);
			} else {
				return round($summ,2);
			}
		}		
		
		function account_list_pages($account_list_pages){
			
			$inex_pages = get_option('inex_pages');
			if(isset($inex_pages['toinvest'])){
				$account_list_pages['toinvest'] = array(
					'title' => get_the_title($inex_pages['toinvest']),
					'url' => get_permalink($inex_pages['toinvest']),
					'type' => 'link',
				);
			}
		
			return $account_list_pages;			
		}
		
	}    
}