<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Экспорт контактов[:ru_RU][en_US:]Contacts export[:en_US]
description: [ru_RU:]Экспорт контактов[:ru_RU][en_US:]Contacts export[:en_US]
version: 1.0
category: [ru_RU:]Пользователи[:ru_RU][en_US:]Users[:en_US]
cat: user
*/

add_action('admin_menu', 'admin_menu_cexp');
function admin_menu_cexp(){
global $premiumbox;		
	add_menu_page(__('Export contacts','pn'), __('Export contacts','pn'), 'administrator', 'pn_cexp', array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('export'));  	
}

add_action('pn_adminpage_title_pn_cexp', 'def_adminpage_title_pn_cexp');
function def_adminpage_title_pn_cexp($page){
	_e('Export contacts','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_cexp','def_adminpage_content_pn_cexp');
function def_adminpage_content_pn_cexp(){
global $wpdb;
?>
<div class="premium_body">
    <form method="post" target="_blank" action="<?php pn_the_link_post('cexp'); ?>">
    <table class="premium_standart_table">
        <tr>
		    <th><?php _e('Start date','pn'); ?></th>
			<td>
			<div class="premium_wrap_standart">
			    <input type="text" name="date1" class="pn_datepicker" value="" />
			</div>
			</td>
		</tr>
        <tr>
		    <th><?php _e('End date','pn'); ?></th>
			<td>
			<div class="premium_wrap_standart">
			    <input type="text" name="date2" class="pn_datepicker" value="" />
			</div>
			</td>
		</tr>		
        <tr>
		    <th><?php _e('Select data','pn'); ?></th>
			<td>
			<div class="premium_wrap_standart">
				<div><label><input type="checkbox" name="data[]" value="name" /> <?php _e('First name','pn'); ?></label></div>
			    <div><label><input type="checkbox" name="data[]" value="email" /> <?php _e('E-mail','pn'); ?></label></div>
				<div><label><input type="checkbox" name="data[]" value="phone" /> <?php _e('Phone no.','pn'); ?></label></div>
				<div><label><input type="checkbox" name="data[]" value="skype" /> <?php _e('Skype','pn'); ?></label></div>
			</div>
			</td>
		</tr>
        <tr>
		    <th><?php _e('Unique key','pn'); ?></th>
			<td>
			<div class="premium_wrap_standart">
				<div><label><input type="checkbox" name="key[]" value="name" /> <?php _e('First name','pn'); ?></label></div>
			    <div><label><input type="checkbox" name="key[]" value="email" /> <?php _e('E-mail','pn'); ?></label></div>
				<div><label><input type="checkbox" name="key[]" value="phone" /> <?php _e('Phone no.','pn'); ?></label></div>
				<div><label><input type="checkbox" name="key[]" value="skype" /> <?php _e('Skype','pn'); ?></label></div>
			</div>
			</td>
		</tr>
		<?php
		$query = $wpdb->query("CHECK TABLE ".$wpdb->prefix . "archive_bids");
		if($query == 1){
		?>
        <tr>
		    <th><?php _e('Archived orders','pn'); ?></th>
			<td>
			<div class="premium_wrap_standart">
				<div><label><input type="checkbox" name="archive" value="1" /> <?php _e('Include archived orders in list','pn'); ?></label></div>
			</div>
			</td>
		</tr>
		<?php } ?>
        <tr>
		    <th></th>
			<td>
			<div class="premium_wrap_standart">
			    <input type="submit" name="" class="button" value="<?php _e('Download','pn'); ?>" />
			</div>
			</td>
		</tr>		
    </table>
	</form>	
	
</div>
<?php
} 

/* обработка */
add_action('premium_action_cexp','def_premium_action_cexp');
function def_premium_action_cexp(){
global $wpdb;	

	pn_only_caps(array('administrator'));

	$where = '';
	$datestart = is_my_date(is_param_post('date1'));
	if($datestart){
		$dstart = get_mytime($datestart, 'Y-m-d H:i:s');
		$where .= " AND createdate >= '$dstart'";
	}
		
	$dateend = is_my_date(is_param_post('date2'));
	if($dateend){
		$dend = get_mytime($dateend, 'Y-m-d H:i:s');
		$where .= " AND createdate <= '$dend'";
	}	
		
	$my_dir = wp_upload_dir();
	$path = $my_dir['basedir'].'/';		
		
	$file = $path.'contactexport-'. date('Y-m-d-H-i') .'.csv';           
	$fs=@fopen($file, 'w');
	
	$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."bids WHERE status != 'auto' $where");
	
	$query = $wpdb->query("CHECK TABLE ".$wpdb->prefix . "archive_bids");
	if($query == 1){
		$archive = intval(is_param_post('archive'));
		if($archive){
			$aitems = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."archive_bids");
			$nitems = array_merge($items, $aitems);
		} else {
			$nitems = $items;
		}
	} else {
		$nitems = $items;
	}
	
	$data = is_param_post('data');
	$key = is_param_post('key');
	if(!is_array($key)){ $key = array(); }
		
	$keys1 = $keys2 = $keys3 = $keys4 = array();
	$k1 = $k2 = $k3 = $k4 = 0;
	if(in_array('name', $key)){
		$k1 = 1;
	}
	if(in_array('email', $key)){
		$k2 = 1;
	}			
	if(in_array('phone', $key)){
		$k3 = 1;
	}
	if(in_array('skype', $key)){
		$k4 = 1;
	}
		
	$content = '';
		
	if(is_array($data)){
			
		$v_name = 0;
		$v_email = 0;
		$v_phone = 0;
		$v_skype = 0;
			
		if(in_array('name', $data)){
			$content .= get_cptgn(__('First name','pn')).';';
			$v_name = 1;
		}
		if(in_array('email', $data)){
			$content .= get_cptgn(__('E-mail','pn')).';';
			$v_email = 1;
		}
		if(in_array('phone', $data)){
			$content .= get_cptgn(__('Phone no.','pn')).';';
			$v_phone = 1;
		}		
		if(in_array('skype', $data)){
			$content .= get_cptgn(__('Skype','pn')).';'."\n";
			$v_skype = 1;
		}
			
		foreach($nitems as $item){
			$email = get_cptgn(str_replace(';','',$item->user_email));
			$tel = get_cptgn(str_replace(';','',$item->user_phone));
			$fio = str_replace(';','',$item->last_name .' '. $item->first_name .' '. $item->second_name);
			$fio = get_cptgn($fio);
			$skype = get_cptgn(str_replace(';','',$item->user_skype));
				
			$line = '';
				
			if(!in_array($fio, $keys1) and !in_array($email, $keys2) and !in_array($tel, $keys3) and !in_array($skype, $keys4)){
					
				if($v_name == 1){
					$line .= $fio.';';
					if($k1 == 1){
						$keys1[] = $fio;
					}
				}
				if($v_email == 1){
					$line .= $email.';';
					if($k2 == 1){
						$keys2[] = $email;
					}						
				}
				if($v_phone == 1){
					$line .= $tel.';';
					if($k3 == 1){
						$keys3[] = $tel;
					}						
				}
				if($v_skype == 1){
					$line .= $skype.';';
					if($k4 == 1){
						$keys4[] = $skype;
					}						
				}				
				$line .= "\n";
				
			}
				
			$content .= $line;
		}
			
	}
		
	@fwrite($fs, $content);
	@fclose($fs);	
	
	if(is_file($file)) {
		if (ob_get_level()) {
			ob_end_clean();
		}
		header('Content-Type: text/html; charset=CP1251');
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		readfile($file);
		@unlink($file);
		exit;
	} else {
		pn_display_mess(__('Error! Unable to create file!','pn'));
	}	
}