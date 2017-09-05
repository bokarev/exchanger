<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_geoip_import', 'pn_admin_title_pn_geoip_import');
function pn_admin_title_pn_geoip_import(){
	_e('Import countries','pn');
}

add_action('pn_adminpage_content_pn_geoip_import','def_pn_admin_content_pn_geoip_import');
function def_pn_admin_content_pn_geoip_import(){
global $wpdb;
	$country = get_geoip_country();
?>	
	<div class="geoip_table">
		<div class="premium_default_window">
			<input type="text" class="premium_input big_input" name="sword" placeholder="<?php _e('Search by name','pn'); ?>" id="search_country" value="" />
		</div>	
		<div class="geotable_fix">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<th><?php _e('Country','pn'); ?></th>
				<th style="width: 20px;"></th>
			</tr>	
			<?php if(is_array($country)){ ?>
				<?php foreach($country as $key => $title){
					$key = is_country_attr($key);		
					$cc = $wpdb->query("SELECT * FROM ". $wpdb->prefix ."geoip_country WHERE attr='$key'");
					$cl='';
					$link_title = '';
					if($cc == 0){
						$cl = 'geoip_active_upload';
						$link_title = __('Download country data','pn');
					} else {
						$cl = 'geoip_deactive_upload';
						$link_title = __('Delete country data','pn');
					}
				?>
					<tr>
						<td style="text-align: left;"><strong><?php echo ctv_ml($title); ?></strong></td>
						<td><a href="#<?php echo $key; ?>" class="geoip_active_button <?php echo $cl; ?>" data-count-url="<?php pn_the_link_post('dcountry_step_count'); ?>&attr=<?php echo $key; ?>" data-title="<?php _e('Download country data','pn'); ?>" title="<?php echo $link_title; ?>"></a></td>
					</tr>			
				<?php 
				} ?>
			<?php } ?>	
			<tr>
				<th><?php _e('Country','pn'); ?></th>
				<th></th>
			</tr>					
		</table>
		</div>
	</div>
	
	<div class="premium_shadow"></div>
	<div class="prbar_wrap">
		<div class="prbar_wrap_ins">
			<div class="prbar_close"></div>
			<div class="prbar_title"></div>
			<div class="prbar_content">
				<div class="prbar_num">
					<?php printf(__('Found: %1s %2s %3s requests','pn'), '<input type="text" name="" class="prbar_num_count" value="','0','" />'); ?>
				</div>
				<div class="prbar_control">
					<div class="prbar_input">
						<?php _e('Perform','pn'); ?>: <input type="text" name="" class="prbar_count" value="100" />
					</div>
					<div class="prbar_submit"><?php _e('Run','pn'); ?></div>
						<div class="premium_clear"></div>
				</div>
				<div class="prbar_ind"><div class="prbar_ind_abs"></div><div class="prbar_ind_text">0%</div></div>
				<div class="prbar_log_wrap">
					<div class="prbar_log"></div>			
				</div>
			</div>
		</div>
	</div>	
	
<script type="text/javascript">	
$(function(){
	
	var th = $('.geotable_fix table').height();
	$('.geotable_fix').css({'height':th});
	
	function search_country(){
		
		var txt = $.trim($('#search_country').val()).toLowerCase();
		
		$('.geoip_table table tr').show();
		
		$('.geoip_table table tr').each(function(){
			var td_first = $(this).find('td:first');
			
			if(td_first.length > 0){

				if(td_first.html().toLowerCase().indexOf(txt) + 1) {
					
				} else {
					$(this).hide();
				}
				
			}
		});
		
	}
	
	$('#search_country').bind('change', function(){
		search_country();
		$('#search_country').unbind('keyup');
	});	
	$('#search_country').bind('keyup', function(){
		search_country();
		$('#search_country').unbind('change');
	});		
	
 	$(document).on('click', '.geoip_deactive_upload', function(){
		if(!$(this).hasClass('act')){
		
			var id = $(this).attr('href').replace('#','');
			var thet = $(this);
			$('#premium_ajax').show();
			$('.geoip_active_button').addClass('act');
			
			var dataString='id=' + id;
			$.ajax({
				type: "POST",
				url: "<?php pn_the_link_post('geoip_del_country'); ?>",
				data: dataString,
				error: function(res, res2, res3){
					<?php do_action('pn_js_error_response', 'ajax'); ?>
				},				
				success: function(res)
				{
					
					if(res == 1){
						thet.removeClass('geoip_deactive_upload').addClass('geoip_active_upload');
						thet.attr('title','<?php _e('Download country data','pn'); ?>');
					}	
					$('.geoip_active_button').removeClass('act');
					$('#premium_ajax').hide();
				}
			});
		
		}
	
        return false;
	});	
	
	$(document).PrBar({
		trigger: '.geoip_active_upload:not(.act)',
		start_title: '<?php _e('determining the number of requests','pn'); ?>...',
		end_title: '<?php _e('number of requests defined','pn'); ?>',
		line_text: '<?php _e('processing %now% of %max% steps','pn'); ?>',
		line_success: '<?php _e('step %now% is successful','pn'); ?>',
		end_progress: '<?php _e('action is completed','pn'); ?>',
		success: function(res){
			res.removeClass('geoip_active_upload').addClass('geoip_deactive_upload');
			res.attr('title','<?php _e('Delete country data','pn'); ?>');
		}
	});	
	
});
</script>	
<?php
}

add_action('premium_action_dcountry_step_count', 'pn_premium_action_dcountry_step_count');
function pn_premium_action_dcountry_step_count(){
global $wpdb, $premiumbox;	

	only_post();

	$log = array();
	$log['status'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = '';
	$log['count'] = 0;
	$log['link'] = '';
	
	$attr = is_country_attr(is_param_get('attr'));
	if($attr){
		if(current_user_can('administrator') or current_user_can('pn_geoip')){
			$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."geoip_country WHERE attr='$attr'");
			if($cc == 0){
				$file = $premiumbox->plugin_dir . 'moduls/geoip/data/'. $attr .'.txt';
				if(is_file($file)){
					$file_open = @file_get_contents($file);
					if($file_open){
						
						$count = 0;
						$lines = explode("\n",$file_open);
						$count = count($lines);	
						$log['status'] = 'success';
						$log['count'] = $count;
						$log['link'] = pn_link_post('dcountry_step_request').'&attr='.$attr;
						$log['status_text'] = __('Ok!','pn');
							
					} else {
						$log['status'] = 'error';
						$log['status_code'] = 1; 
						$log['status_text'] = __('Error! Country file is not open!','pn');
					}
				} else {
					$log['status'] = 'error';
					$log['status_code'] = 1; 
					$log['status_text'] = __('Error! Country file is empty!','pn');
				}
			} else {
				$log['status'] = 'error';
				$log['status_code'] = 1; 
				$log['status_text'] = __('Error! Country has uploaded!','pn');				
			}
		} else {
			$log['status'] = 'error';
			$log['status_code'] = 1; 
			$log['status_text'] = __('Error! insufficient privileges!','pn');
		}
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1; 
		$log['status_text'] = __('Error! No country!','pn');		
	}
	
	echo json_encode($log);
	exit;	
}

add_action('premium_action_dcountry_step_request', 'pn_premium_action_dcountry_step_request');
function pn_premium_action_dcountry_step_request(){
global $wpdb, $premiumbox;	

	only_post();

	$log = array();
	$log['status'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = '';
	$log['count'] = 0;
	$log['link'] = '';
	
	$attr = is_country_attr(is_param_get('attr'));
	$idspage = intval(is_param_post('idspage'));
	$limit = intval(is_param_post('limit')); if($limit < 1){ $limit = 1; }
	$offset = ($idspage - 1) * $limit;	
	if($attr){
		if(current_user_can('administrator') or current_user_can('pn_geoip')){
			$file = $premiumbox->plugin_dir . 'moduls/geoip/data/'. $attr .'.txt';
			if(is_file($file)){
				$file_open = @file_get_contents($file);
				if($file_open){
						
					$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."geoip_country WHERE attr='$attr'");
					if($cc == 0){	
						$wpdb->query("DELETE FROM ".$wpdb->prefix."geoip_iplist WHERE country_attr='$attr'");
						
						$country = get_geoip_country();
						
						$arr = array();
						if(isset($country[$attr])){
							$arr['title'] = pn_strip_input($country[$attr]);
						} 
						$arr['attr'] = $attr;
						$arr['status'] = 1;
 							
						if($attr == 'RU' or $attr == 'UA' or $attr == 'US'){
							$arr['status'] = 1;
						} 

						$wpdb->insert($wpdb->prefix.'geoip_country', $arr);
					}						
						
					$lines = explode("\n",$file_open);
					if(count($lines) > 0){			 
						$datas = array_slice($lines, $offset, $limit);
						foreach($datas as $line){ 
													
							$line = trim($line);
							if($line){
													
								$liar = explode(";",$line);
	
								$arr = array();
								$arr['before_cip'] = pn_strip_input(is_isset($liar,0));
								$arr['after_cip'] = pn_strip_input(is_isset($liar,1));
								$arr['before_ip'] = pn_strip_input(is_isset($liar,2));
								$arr['after_ip'] = pn_strip_input(is_isset($liar,3));
								$arr['country_attr'] = $attr;
								$arr['place_id'] = pn_strip_input(is_isset($liar,4));		
								$wpdb->insert($wpdb->prefix.'geoip_iplist', $arr);				
															
							}
													
						}									    													
					}							

					$log['status'] = 'success';
					$log['status_text'] = __('Ok!','pn');					
						
				} else {
					$log['status'] = 'error';
					$log['status_code'] = 1; 
					$log['status_text'] = __('Error! Country file is not open!','pn');
				}
			} else {
				$log['status'] = 'error';
				$log['status_code'] = 1; 
				$log['status_text'] = __('Error! Country file is empty!','pn');
			}
		} else {
			$log['status'] = 'error';
			$log['status_code'] = 1; 
			$log['status_text'] = __('Error! insufficient privileges!','pn');
		}
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1; 
		$log['status_text'] = __('Error! No country!','pn');		
	}
	
	echo json_encode($log);
	exit;	
}

add_action('premium_action_geoip_del_country', 'pn_premium_action_geoip_del_country');
function pn_premium_action_geoip_del_country(){
global $wpdb;
	only_post();
	$id = is_country_attr(is_param_post('id'));
	if($id){
		if(current_user_can('administrator') or current_user_can('pn_geoip')){
			$wpdb->query("DELETE FROM ".$wpdb->prefix."geoip_iplist WHERE country_attr='$id'");
			$wpdb->query("DELETE FROM ".$wpdb->prefix."geoip_country WHERE attr='$id'");
			echo 1;
		} 
	}  		
}