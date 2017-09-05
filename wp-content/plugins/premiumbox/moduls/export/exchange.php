<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_export_exchange', 'pn_admin_title_pn_export_exchange');
function pn_admin_title_pn_export_exchange($page){
	_e('Exchanges export','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_export_exchange','def_pn_admin_content_pn_export_exchange');
function def_pn_admin_content_pn_export_exchange(){
global $wpdb;
?>
<div class="premium_body">	
    <form method="post" target="_blank" action="<?php pn_the_link_post('export_exchange'); ?>">
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
			<div class="premium_wrap_standart check_div">
				<div style="font-weight: 500;"><label><input type="checkbox" class="check_all" name="" value="1" /> <?php _e('Check all/Uncheck all','pn'); ?></label></div>
				<?php
				$array = array(
					'id' => __('Identifier','pn'),
					'createdate' => __('Date','pn'),
					'editdate' => __('Edit date','pn'),
					'cgive' => __('Currency Send','pn'),
					'cget' => __('Currency Receive','pn'),
					'curs1' => __('Rate Send','pn'),
					'curs2' => __('Rate Receive','pn'),
					'summ1' => __('Amount To send','pn'),
					'dop_com1' => __('Add. fees amount Send','pn'),
					'summ1_dc' => __('Amount Send with add. fees','pn'),
					'com_ps1' => __('PS fees Send','pn'),
					'summ1c' => __('Amount Send with add. fees and PS fees','pn'),
					'summ1cr' => __('Amount Send for reserve','pn'),
					'summ2t' => __('Amount at the Exchange Rate','pn'),
					'summ2' => __('Amount (discount included)','pn'),
					'dop_com2' => __('Add. fees amount Receive','pn'),
					'summ2_dc' => __('Amount Receive with add. fees','pn'),
					'com_ps2' => __('PS fees Receive','pn'),
					'summ2c' => __('Amount Receive with add. fees and PS fees','pn'),
					'summ2cr' => __('Amount Receive for reserve','pn'),
					'exsum' => __('Amount in internal currency needed for exhange','pn'),
					'profit' => __('Profit','pn'),
					'account1' => __('Account To send','pn'),
					'account2' => __('Account To receive','pn'),
					'naschet' => __('Merchant account','pn'),
					'soschet' => __('Automatic payout account','pn'),
					'trans_in' => __('Merchant transaction ID','pn'),
					'trans_out' => __('Auto payout transaction ID','pn'),
					'last_name' => __('Last name','pn'),
					'first_name' => __('First name','pn'),
					'second_name' => __('Second name','pn'),
					'user_email' => __('E-mail','pn'),
					'user_phone' => __('Phone no.','pn'),
					'user_skype' => __('Skype','pn'),
					'user' => __('User','pn'),
					'user_sk' => __('User discount','pn'),
					'user_sksumm' => __('User discount amount','pn'),
					'user_ip' => __('User IP','pn'),
					'hash' => __('Hash','pn'),
					'link' => __('Link','pn'),
					'status' => __('Status','pn'),	
					'locale' => __('Language','pn'),
					'napsidenty' => __('Money transfer nubmer','pn'),
				);
				foreach($array as $key => $val){
				?>
					<div><label><input type="checkbox" name="data[]" class="check_once" value="<?php echo $key; ?>" /> <?php echo $val; ?></label></div>
				<?php } ?>
			</div>
			</td>
		</tr>
		<tr>
		    <th><?php _e('Bid status','pn'); ?></th>
			<td>
			<div class="premium_wrap_standart check_div">
				<div style="font-weight: 500;"><label><input type="checkbox" class="check_all" name="" value="1" /> <?php _e('Check all/Uncheck all','pn'); ?></label></div>
				<?php
				$bid_status_list = apply_filters('bid_status_list',array());
				foreach($bid_status_list as $key => $val){
				?>
					<div><label><input type="checkbox" name="bs[]" class="check_once" value="<?php echo $key; ?>" /> <?php echo $val; ?></label></div>
				<?php } ?>
			</div>
			</td>
		</tr>		
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

<script type="">
jQuery(function($){ 
	$('.check_all').on('change',function(){
		if($(this).prop('checked')){
			$(this).parents('.check_div').find('.check_once').prop('checked',true);
		} else {
			$(this).parents('.check_div').find('.check_once').prop('checked',false);
		}
	});		
});
</script>	
<?php
} 

/* обработка */
add_action('premium_action_export_exchange','def_premium_action_export_exchange');
function def_premium_action_export_exchange(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_export_exchange'));			
	
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
	
	$bs = is_param_post('bs');
	$in = array();
	if(is_array($bs)){
		foreach($bs as $b){
			$b = is_status_name($b);
			if($b){
				$in[] = "'". $b ."'";
			}
		}
	}
	if(count($in) > 0){
		$join_status = join(',',$in);
		$where .= " AND status IN($join_status)";
	}
	
	$my_dir = wp_upload_dir();
	$path = $my_dir['basedir'].'/';		
		
	$file = $path . 'bidsexport-'. date('Y-m-d-H-i') .'.csv';           
	$fs = @fopen($file, 'w');
	
	$items = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."bids WHERE status != 'auto' $where ORDER BY id DESC");
	
	$data = is_param_post('data');
		
	$content = '';
		
	$array = array(
		'id' => __('Identifier','pn'),
		'createdate' => __('Date','pn'),
		'editdate' => __('Edit date','pn'),
		'cgive' => __('Currency Send','pn'),
		'cget' => __('Currency Receive','pn'),
		'curs1' => __('Rate Send','pn'),
		'curs2' => __('Rate Receive','pn'),
		'summ1' => __('Amount To send','pn'),
		'dop_com1' => __('Add. fees amount Send','pn'),
		'summ1_dc' => __('Amount Send with add. fees','pn'),
		'com_ps1' => __('PS fees Send','pn'),
		'summ1c' => __('Amount Send with add. fees and PS fees','pn'),
		'summ1cr' => __('Amount Send for reserve','pn'),
		'summ2t' => __('Amount at the Exchange Rate','pn'),
		'summ2' => __('Amount after discount','pn'),
		'dop_com2' => __('Add. fees amount Receive','pn'),
		'summ2_dc' => __('Amount Receive with add. fees','pn'),
		'com_ps2' => __('PS fees Receive','pn'),
		'summ2c' => __('Amount Receive with add. fees and PS fees','pn'),
		'summ2cr' => __('Amount Receive for reserve','pn'),
		'exsum' => __('Amount in internal currency needed for exhange','pn'),
		'profit' => __('Profit','pn'),
		'account1' => __('Account To send','pn'),
		'account2' => __('Account To receive','pn'),
		'naschet' => __('Merchant account','pn'),
		'soschet' => __('Automatic payout account','pn'),
		'trans_in' => __('Merchant transaction ID','pn'),
		'trans_out' => __('Auto payout transaction ID','pn'),					
		'last_name' => __('Last name','pn'),
		'first_name' => __('First name','pn'),
		'second_name' => __('Second name','pn'),
		'user_email' => __('E-mail','pn'),
		'user_phone' => __('Phone no.','pn'),
		'user_skype' => __('Skype','pn'),
		'user' => __('User','pn'),
		'user_sk' => __('User discount','pn'),
		'user_sksumm' => __('User discount amount','pn'),
		'user_ip' => __('User IP','pn'),
		'hash' => __('Hash','pn'),
		'link' => __('Link','pn'),
		'status' => __('Status','pn'),	
		'locale' => __('Language','pn'),
		'napsidenty' => __('Money transfer nubmer','pn'),
	);
			
	if(is_array($data)){
			
		$en = array();
		$csv_title = '';
		$csv_key = '';
		foreach($array as $k => $v){
			if(in_array($k, $data)){
				$en[] = $k;
				$csv_title .= '"'. get_cptgn($v) .'";';
			} 
		}	
			
		$content .= $csv_title."\n";

		if(count($en) > 0){
			foreach($items as $item){
				$line = '';
					
				foreach($en as $key){
					$line .= '"';
						
					if($key == 'id'){
						$line .= $item->id;
					} elseif($key == 'createdate'){
						$line .= get_mytime($item->createdate,'d.m.Y H:i');
					} elseif($key == 'editdate'){
						$line .= get_mytime($item->editdate,'d.m.Y H:i');
					} elseif($key == 'cgive'){
						$line .= get_cptgn(ctv_ml($item->valut1) .' '. $item->vtype1);
					} elseif($key == 'cget'){
						$line .= get_cptgn(ctv_ml($item->valut2) .' '. $item->vtype2);
					} elseif($key == 'account1'){
						$line .= get_cptgn($item->account1);
					} elseif($key == 'account2'){
						$line .= get_cptgn($item->account2);
					} elseif($key == 'naschet'){
						$line .= get_cptgn($item->naschet);	
					} elseif($key == 'soschet'){
						$line .= get_cptgn($item->soschet);
					} elseif($key == 'trans_in'){
						$line .= get_cptgn($item->trans_in);
					} elseif($key == 'trans_out'){
						$line .= get_cptgn($item->trans_out);							
					} elseif($key == 'last_name'){
						$line .= get_cptgn($item->last_name);
					} elseif($key == 'first_name'){
						$line .= get_cptgn($item->first_name);
					} elseif($key == 'second_name'){
						$line .= get_cptgn($item->second_name);
					} elseif($key == 'user_email'){
						$line .= get_cptgn($item->user_email);							
					} elseif($key == 'user_phone'){
						$line .= get_cptgn($item->user_phone);
					} elseif($key == 'user_skype'){
						$line .= get_cptgn($item->user_skype);	
					} elseif($key == 'user'){
						$user = '';
						$user_id = $item->user_id;
						if($user_id){
							$ui = get_userdata($user_id);
							if(isset($ui->user_login)){
								$user = get_cptgn($ui->user_login);
							}
						} 			
						$line .= $user;
					} elseif($key == 'user_sk'){
						$line .= $item->user_sk;
					} elseif($key == 'user_sksumm'){
						$line .= $item->user_sksumm;
					} elseif($key == 'user_ip'){
						$line .= get_cptgn($item->user_ip);
					} elseif($key == 'hash'){
						$line .= $item->hashed;
					} elseif($key == 'link'){
						$line .= get_bids_url($item->hashed);
					} elseif($key == 'status'){
						$line .= get_cptgn(get_bid_status($item->status));
					} elseif($key == 'locale'){	
						$line .= get_lang_key($item->bid_locale);
					} else {
						$line .= rep_dot(is_isset($item,$key));
					}						

					$line .= '";';
				}
					
				$line .= "\n";
				$content .= $line;
			}
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
		unlink($file);
		exit;
	} else {
		pn_display_mess(__('Error! Unable to create file!','pn'));
	}	
}