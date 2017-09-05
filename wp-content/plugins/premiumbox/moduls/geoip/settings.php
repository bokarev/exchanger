<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_geoip', 'pn_admin_title_pn_geoip');
function pn_admin_title_pn_geoip(){
	_e('Settings','pn');
}

add_action('pn_adminpage_content_pn_geoip','def_pn_admin_content_pn_geoip');
function def_pn_admin_content_pn_geoip(){
global $wpdb;
	$country = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."geoip_country ORDER BY title ASC");
	$wablons = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."geoip_template WHERE default_temp = '0'");
?>	
	<div class="geoip_table">
		<div class="premium_default_window">
			<input type="text" class="regular-text" name="sword" placeholder="<?php _e('Search by name','pn'); ?>" id="search_country" value="" />
		</div>	
		
		<div class="indicator_label_div">
			<div class="indicator_label allow">-<?php _e('allowed country','pn'); ?></div>
			<div class="indicator_label proh">-<?php _e('prohibited country','pn'); ?></div>
		</div>
		
		<table cellpadding="0" cellspacing="0">
				
			<tr>
				<th style="text-align: left;"><?php _e('Country','pn'); ?></th>
				<th style="width: 20px;"><?php _e('Status','pn'); ?></th>
				<th style="width: 100px;"><?php _e('Template','pn'); ?></th>
			</tr>	
	
			<?php if(is_array($country)){ ?>
				<?php foreach($country as $item){ 
				
				$temp_id = $item->temp_id;
				
				$indicator = '<a href="#" class="indicator allow"></a>';
				if($item->status == 0){
					$indicator = '<a href="#" class="indicator proh"></a>';
				}
				?>
					<tr id="country_<?php echo $item->id; ?>">
						<td style="text-align: left;"><strong><?php echo pn_strip_input(ctv_ml($item->title)); ?></strong></td>
						<td>
							<?php echo $indicator; ?>
						</td>
						<td>
							<select name="" class="template_select" autocomplete="off">
								<option value="0">-- <?php _e('Default','pn'); ?> --</option>
								<?php foreach($wablons as $wablon){ ?>
									<option value="<?php echo $wablon->id; ?>" <?php selected($wablon->id,$temp_id); ?>><?php echo pn_strip_input($wablon->temptitle); ?></option>
								<?php } ?>
							</select>						
						</td>
					</tr>			
				<?php 
				} ?>
			<?php } ?>	
	
			<tr>
				<th style="text-align: left;"><?php _e('Country','pn'); ?></th>
				<th><?php _e('Status','pn'); ?></th>
				<th><?php _e('Template','pn'); ?></th>
			</tr>				
				
		</table>
	</div>
<script type="text/javascript">	
$(function(){
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
	
	$('.template_select').bind('change', function(){
		var id = $(this).parents('tr').attr('id').replace('country_','');
		var wid = $(this).val();
		var thet = $(this);
		
		thet.prop('disabled', true);
		$('#premium_ajax').show();
		
		var dataString='id=' + id + '&wid=' + wid;
        $.ajax({
			type: "POST",
			url: "<?php echo pn_the_link_post('geoip_change_template'); ?>",
			data: dataString,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{
				$('#premium_ajax').hide();
				thet.prop('disabled', false);
			}
        });
	
        return false;
	});	
	
	$(document).on('click', '.indicator', function(){
		if(!$(this).hasClass('act')){
		
			var id = $(this).parents('tr').attr('id').replace('country_','');
			if($(this).hasClass('allow')){
				var wid = 0;
			} else {
				var wid = 1;
			}
			
			var thet = $(this);
			$('#premium_ajax').show();
			thet.addClass('act');
			
			var dataString='id=' + id + '&wid=' + wid;
			$.ajax({
				type: "POST",
				url: "<?php echo pn_the_link_post('geoip_change_status'); ?>",
				data: dataString,
				error: function(res, res2, res3){
					<?php do_action('pn_js_error_response', 'ajax'); ?>
				},				
				success: function(res)
				{
					if(wid == 0){
						thet.removeClass('act').removeClass('allow').addClass('proh');
					} else {
						thet.removeClass('act').addClass('allow').removeClass('proh');
					}
					$('#premium_ajax').hide();				
				}
			});
		
		}
	
        return false;
	});	
	
});
</script>	
<?php 
}

add_action('premium_action_geoip_change_status', 'pn_premium_action_geoip_change_status');
function pn_premium_action_geoip_change_status(){
global $wpdb;

	only_post();
	
	if(current_user_can('administrator') or current_user_can('pn_geoip')){
		$id = intval(is_param_post('id'));
		$wid = intval(is_param_post('wid'));
		$wpdb->update($wpdb->prefix.'geoip_country', array('status' => $wid), array('id' => $id));	
	}  			
}

add_action('premium_action_geoip_change_template', 'pn_premium_action_geoip_change_template');
function pn_premium_action_geoip_change_template(){
global $wpdb;

	only_post();
	
	if(current_user_can('administrator') or current_user_can('pn_geoip')){
		$id = intval(is_param_post('id'));
		$wid = intval(is_param_post('wid'));
		$wpdb->update($wpdb->prefix.'geoip_country', array('temp_id' => $wid), array('id' => $id));	
	}  		
}