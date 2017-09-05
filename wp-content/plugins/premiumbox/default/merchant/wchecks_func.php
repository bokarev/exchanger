<?php
if( !defined( 'ABSPATH')){ exit(); }

add_filter('list_wchecks', 'def_list_wchecks', 100);
function def_list_wchecks($list){
	asort($list);
	return $list;
}

/* проверка активности смс-гейта */
function is_enable_wchecks($id){
	$wchecks = get_option('wchecks');
	if(!is_array($wchecks)){ $wchecks = array(); }
	
	return intval(is_isset($wchecks,$id));
}

if(!class_exists('Wchecks_Premiumbox')){
	class Wchecks_Premiumbox{
		public $name = "";
		public $m_data = "";
		public $title = "";	
		
		function __construct($file, $map, $title)
		{
			$path = get_extension_file($file);
			$name = get_extension_name($path);
			$numeric = get_extension_num($name);
			
			$data = set_extension_data($path . '/dostup/index', $map);
			
			global $premiumbox;
			$premiumbox->file_include($path . '/class');			
			
			$this->name = trim($name);
			$this->m_data = $data;
			$this->title = $title . ' ' . $numeric;
			
			add_filter('list_wchecks', array($this, 'list_wchecks'));
			add_action('wchecks_admin', array($this, 'wchecks_admin'));
			add_action('premium_action_'. $name .'_test', array($this, 'premium_action_test'));
			add_filter('check_purse1_text', array($this, 'check_purse_text'), 10, 2);
			add_filter('check_purse2_text', array($this, 'check_purse_text'), 10, 2);		
		}
		
		public function list_wchecks($list){
			$list[] = array(
				'id' => $this->name,
				'title' => $this->title,
			);
			return $list;
		}
		
		public function wchecks_admin(){
			$options = array();	
			$options['top_title'] = array(
				'view' => 'h3',
				'title' => __('Test','pn'),
				'submit' => __('Test','pn'),
				'colspan' => 2,
			);	
			$options['purse'] = array(
				'view' => 'inputbig',
				'title' => __('Wallet','pn'),
				'default' => '',
				'name' => 'purse',
			);		
			$options['bottom_title'] = array(
				'view' => 'h3',
				'title' => '',
				'submit' => __('Test','pn'),
				'colspan' => 2,
			);
			pn_admin_one_screen('', $options, '', pn_link_post($this->name.'_test'));											
		}		
		
		public function premium_action_test(){
			pn_display_mess(__('Not tested','pn'));
		}
		
		public function check_purse_text($text, $check_id){
			if($check_id and $check_id == $this->name){
				$text = __('Your account is not verified','pn');
			}
			return $text;
		}		
	}
}