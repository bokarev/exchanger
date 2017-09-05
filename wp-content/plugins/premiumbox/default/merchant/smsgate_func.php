<?php
if( !defined( 'ABSPATH')){ exit(); }

add_filter('list_smsgate', 'def_list_smsgate', 100);
function def_list_smsgate($list){
	asort($list);
	return $list;
}

/* проверка активности смс-гейта */
function is_enable_smsgate($id){
	$smsgate = get_option('smsgate');
	if(!is_array($smsgate)){ $smsgate = array(); }
	
	return intval(is_isset($smsgate,$id));
}

add_action('change_bidstatus_payed', 'smsgate_change_bidstatus_payed',99,3);
function smsgate_change_bidstatus_payed($obmen_id, $obmen, $place){ 
	if($place == 'site'){
		$data = get_option('smsgatedata');
		if(is_isset($data, 'manual') == 1){
					
			$text = pn_strip_input(ctv_ml(is_isset($data, 'text1'), $obmen->bid_locale));
			$text = str_replace('[id]',$obmen_id,$text);
			if(!$text){ $text = sprintf(__('Payment ID %s is received','pn'), $obmen_id ); }
					
			do_action('pn_send_sms', $text);		
		}
	}
}

add_action('change_bidstatus_realpay', 'smsgate_change_bidstatus_realpay',99,3);
add_action('change_bidstatus_verify', 'smsgate_change_bidstatus_realpay',99,3);
function smsgate_change_bidstatus_realpay($obmen_id, $obmen, $place){
	if($place == 'site'){
		$data = get_option('smsgatedata');
		if(is_isset($data, 'merch') == 1){
					
			$text = pn_strip_input(ctv_ml(is_isset($data, 'text1'), $obmen->bid_locale));
			$text = str_replace('[id]',$obmen_id,$text);
			if(!$text){ $text = sprintf(__('Payment ID %s is received','pn'), $obmen_id ); }
					
			do_action('pn_send_sms', $text);
		}
	}	
}

add_action('set_autopayouts', 'smsgate_set_autopayouts',99,2);
function smsgate_set_autopayouts($item, $place){
	if($place == 'site'){
		$data = get_option('smsgatedata');
		if(is_isset($data, 'autopay') == 1){
					
			$text = pn_strip_input(ctv_ml(is_isset($data, 'text2'),$item->bid_locale));
			$text = str_replace('[id]',$item->id,$text);
			if(!$text){ $text = sprintf(__('Automatic payout ID %s','pn'), $item->id ); }
					
			do_action('pn_send_sms', $text);
		}
	}			
}

if(!class_exists('SmsGate_Premiumbox')){
	class SmsGate_Premiumbox {
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
			$this->title = $title.' '.$numeric;
		
			add_filter('list_smsgate', array($this, 'list_smsgate'));
		
		}
		
		public function list_smsgate($list){

			$list[] = array(
				'id' => $this->name,
				'title' => $this->title
			);
			
			return $list;
		}		
		
	}
}	