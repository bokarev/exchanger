<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_bids', 'def_admin_title_pn_bids');
function def_admin_title_pn_bids(){
	_e('Orders','pn');
}

add_action('pn_adminpage_content_pn_bids','def_pn_admin_content_pn_bids');
function def_pn_admin_content_pn_bids(){
global $wpdb, $user_ID, $premiumbox;
?>
	
<div class="head_bids_div">
	<div class="hb_button filter_change" data-name="changer_filter">
		<?php _e('Filters','pn'); ?>
	</div>		
	<div class="hb_limit">
		<?php _e('Shown orders','pn'); ?>
		<?php
		$limit = list_bid_limit();	
		?>		
		<input type="text" name="limit" id="bid_limit_count" value="<?php echo $limit; ?>" />
	</div>
	<div class="hb_limit">
		<a href="#" class="button" id="bid_limit"><?php _e('Apply','pn'); ?></a>
			<div class="premium_clear"></div>
	</div>	
		<div class="premium_clear"></div>
</div>


<div class="head_bids_window">
	<!-- filter -->	
	<form action="<?php pn_the_link_post('bids_filter_change'); ?>" class="bids_filter_form" method="post">
	<div class="change_div filters_this" id="changer_filter">	
		<?php
		$cats = array(
			'status' => __('Status of orders','pn'),
			'sum' => __('Amount and date','pn'),
			'currency' => __('Currency and codes','pn'),
			'user' => __('User filters','pn'),
			'other' => __('Other filters','pn'),
		);
		$lists = apply_filters('change_bids_filter_list', array());
		$lists = (array)$lists;
		
		foreach($cats as $cat_name => $cat_title){
			if(isset($lists[$cat_name]) and is_array($lists[$cat_name])){
			?>
			<div class="change_div_one">
				<div class="change_div_title"><?php echo $cat_title; ?></div>
			
				<?php 
				foreach($lists[$cat_name] as $data){ 
					$view = is_isset($data,'view');
					$name = trim(is_isset($data,'name'));
					$title = is_isset($data,'title');
					$get = is_param_get($name);
					$options = is_isset($data,'options');
					$work = trim(is_isset($data,'work'));
					if($work == 'input'){
						$def = pn_maxf_mb(pn_strip_input($get), 1000);
					} elseif($work == 'int'){
						$def = intval($get);
					} elseif($work == 'sum'){
						$def = is_my_money($get);					
					} elseif($work == 'options'){
						$en_options = array();
						if(is_array($options)){
							foreach($options as $k => $v){
								$en_options[] = $k;
							}
						}
						if(is_array($get)){
							$def = array();
							foreach($get as $va){
								if(in_array($va, $en_options)){
									$def[] = $va;
								}
							}
						} else {
							$def = '';
							if(in_array($get, $en_options)){
								$def = $get;
							}
						}
					}	
					if($view == 'select' and is_array($options)){
					?>
						<div class="change_div_list">
							<div class="change_div_label"><?php echo $title; ?></div>
							<div class="change_div_input">
								<select name="<?php echo $name; ?>" autocomplete="off">
									<?php foreach($options as $k => $v){ ?>
										<option value="<?php echo $k; ?>" <?php selected($k,$def); ?>><?php echo $v; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>				
					<?php 
					} elseif($view == 'multi' and is_array($options)){
						if(!is_array($def)){ $def=array(); }
					?>
						<div class="change_div_list multi" id="muarr_<?php echo $name; ?>">
							<div class="change_div_label"><?php echo $title; ?></div>
							<div class="change_div_input">
								<?php foreach($options as $k => $t){ ?>
									<div><label><input type="checkbox" class="muarr_<?php echo $k; ?>" <?php if(in_array($k, $def)){ ?>checked="checked"<?php } ?> name="<?php echo $name; ?>[]" value="<?php echo $k; ?>" /> <?php echo $t; ?></label></div>
								<?php } ?>	
							</div>
						</div>
					<?php
					} elseif($view == 'input'){
					?>
						<div class="change_div_list">
							<div class="change_div_label"><?php echo $title; ?></div>
							<div class="change_div_input">
								<input type="search" name="<?php echo $name; ?>" value="<?php echo $def; ?>" />
							</div>
						</div>
					<?php
					} elseif($view == 'date'){
					?>
						<div class="change_div_list">
							<div class="change_div_label"><?php echo $title; ?></div>
							<div class="change_div_input">
								<input type="search" name="<?php echo $name; ?>" class="pn_datepicker" value="<?php echo $def; ?>" />
							</div>
						</div>
					<?php					
					} elseif($view == 'clear'){
					?>
						<div class="premium_clear"></div>
					<?php
					}
				} ?>
			
					<div class="premium_clear"></div>
			</div>			
			<?php
			}
		}
		?>
		
		<div class="change_close_wrap">
			<a href="#" class="filter_link f1 change_link_close"><?php _e('Hide','pn'); ?></a>
			<a href="#" id="go_filter" class="filter_link f2"><?php _e('Apply filters','pn'); ?></a>
			<a href="#" id="clear_filter" class="filter_link f3"><?php _e('Clear filters','pn'); ?></a>
			<a href="#" id="save_filter" class="filter_link f4"><?php _e('Save filters','pn'); ?></a>
			<a href="#" id="restore_filter" class="filter_link f5"><?php _e('Restore filters','pn'); ?></a>
				<div class="premium_clear"></div>			
		</div>
	</div>
	</form>
	<!-- end filter -->
</div>
	<div class="premium_clear"></div>	
	
<input type="hidden" name="visible_ids" id="visible_ids" value="" />	
	
<form action="<?php pn_the_link_post('bids_action_html'); ?>" class="bids_action_form" method="post">	
	<input type="hidden" name="_wp_referrer" id="url_hide" style="width: 100%;" value="<?php echo esc_html(get_site_url_or() . $_SERVER['REQUEST_URI']); ?>" />
	<input type="hidden" name="_wp_param" id="url_param" style="width: 100%;" value="<?php echo esc_html($_SERVER['REQUEST_URI']); ?>" />
	<div id="bids_html"><?php echo get_bids_html($_SERVER['REQUEST_URI']); ?></div>	
</form>	
	
<script type="text/javascript">
jQuery(function($){  	

	$(document).on('click', '.hb_button', function(){
		var block = $(this).attr('data-name');
		
		if($(this).hasClass('open')){
			$(this).removeClass('open');
			$('#'+block).stop(true,true).slideUp(300);
		} else {
			$('.hb_button').removeClass('open');
			$('.change_div').stop(true,true).slideUp(300);
			$(this).addClass('open');
			$('#'+block).stop(true,true).slideDown(300);
		}
		
		return false;
	});
	
	$(document).on('click', '.change_link_close', function(){
		
		$('.change_div').stop(true,true).slideUp(300);
		$('.hb_button').removeClass('open');
		
		return false;
	});

		
function assembly_bids_id(){
	var ids = '';
	$('.one_bids').each(function(){
		var now_id = $(this).attr('id').replace('bidid_','');
		ids = ids + now_id + ',';
	});
	$('#visible_ids').val(ids);
}
assembly_bids_id();

function load_html_bids(p){

 	$('.filter_change, .filter_link').addClass('active');
	$('.apply_loader').show();
	
	var nurl = '<?php echo admin_url('admin.php?page=pn_bids'); ?>' + '&idspage=' + p;
	var param = 'idspage=' + p;	
	$('.filters_this input[type=text], .filters_this input[type=search], .filters_this select, .filters_this input[type=checkbox]:checked').each(function(){
		var vale = $(this).val();
		if(vale && vale !== '0'){
			nurl = nurl + '&' + encodeURIComponent($(this).attr('name')) + '=' + encodeURIComponent(vale);
			param = param + '&' + encodeURIComponent($(this).attr('name')) + '=' + encodeURIComponent(vale);
		}
	});	
			
	window.history.replaceState(null, null, nurl);
	
	$('#url_hide').val(nurl);
	$('#url_param').val(param);

	$.ajax({
		type: "POST",
		url: "<?php pn_the_link_post('bids_filter_html');?>",
		dataType: 'json',
		data: param,
		error: function(res, res2, res3){
			<?php do_action('pn_js_error_response', 'ajax'); ?>
		},		
		success: function(res)
		{		
			$('.filter_change, .filter_link').removeClass('active');
			$('.apply_loader').hide();
			if(res['status'] == 'error'){
				<?php do_action('pn_js_alert_response'); ?>
			} else if(res['status'] == 'success'){
				$('#bids_html').html(res['html']);
				assembly_bids_id();
			}		
		}
	}); 

} 

 	$(document).on('click', '#bid_limit', function(){
		$('.filter_change').addClass('active');
		var thet = $(this);
		var param = 'count=' + $('#bid_limit_count').val();
		
		$.ajax({
			type: "POST",
			url: "<?php pn_the_link_post('bids_filter_count');?>",
			dataType: 'json',
			data: param,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{		
				$('.filter_change').removeClass('active');
						
				if(res['status'] == 'error'){
					<?php do_action('pn_js_alert_response'); ?>
				} else if(res['status'] == 'success') {
					load_html_bids(1);					
				}
						
			}
		});	
	
		return false;
	});

	$('.bids_filter_form').ajaxForm({
	    dataType:  'json',
        beforeSubmit: function(a,f,o) {
		    $('.filter_change, .filter_link').addClass('active');
        },
		error: function(res, res2, res3){
			<?php do_action('pn_js_error_response', 'form'); ?>
		},			
        success: function(res) {
			$('.filter_change, .filter_link').removeClass('active');
			if(res['status'] == 'error'){
				<?php do_action('pn_js_alert_response'); ?>
			} 
        }
    });
	
	$(document).on('click', '#save_filter', function(){
		$('.bids_filter_form').submit();
		load_html_bids(1);
		
		return false;
	});

	$(document).on('click', '#restore_filter', function(){
		$('.filter_change, .filter_link').addClass('active');
		
		var dataString='id=1';
		$.ajax({
			type: "POST",
			url: "<?php pn_the_link_post('bids_filter_restore');?>",
			dataType: 'json',
			data: dataString,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{
					
				$('.filter_change, .filter_link').removeClass('active');	
				if(res['status'] == 'error'){
					<?php do_action('pn_js_alert_response'); ?>
				} else if(res['status'] == 'success'){		

					$('.filters_this input[type=checkbox]').prop('checked',false);			
					$.each(res['values'], function(index, value){
						if($.isArray(value)){
							var par = $('#muarr_'+index);
							$.each(value, function(index, value){
								par.find('.muarr_'+value).prop('checked', true);
							});
						} else {
							$('.filters_this input[type=text], .filters_this input[type=search], .filters_this select').each(function(){
								var nk = $(this).attr('name');
								if(nk == index){
									$(this).val(decodeURIComponent(value));
								}
							});								
						}
					});	

					load_html_bids(1);
				}	
			}
		});		
		
		return false;
	});	

	$(document).on('click', '#clear_filter', function(){
						
		$('.filters_this input[type=text], .filters_this input[type=search]').val('');
					
		$('.filters_this select').each(function(){
			$(this).find('option:first').prop('selected',true);
		});
		
		$('.filters_this input[type=checkbox]').prop('checked',false);
		
		$('.change_div').stop(true,true).slideUp(300);
		$('.hb_button').removeClass('open');		
		
		load_html_bids(1);
		
		return false;
	});

	$(document).on('click', '#go_filter', function(){	
		
		$('.change_div').stop(true,true).slideUp(300);
		$('.hb_button').removeClass('open');		
		load_html_bids(1);
		
		return false;
	});	
	
	$(document).on('click', '.bids_pagenavi a', function(){	
		var p = $(this).attr('data-page');
		load_html_bids(p);
		
		return false;
	});	
	
function go_ajax_action(){
	
	var vale = $('.sel_action').val();
	if(vale == '0'){
		alert('<?php _e('Error! Action is not selected!','pn'); ?>');
		$('.hotkey_tr').removeClass('active');		
	} else if(vale == 'realdelete'){
		if (confirm("<?php _e('Are you sure you want to delete these orders?','pn'); ?>")) {
			$('.bids_action_form').attr('action','<?php pn_the_link_post('bids_action_ajax'); ?>');
			$('.bids_action_form').submit();
		} else {
			$('.hotkey_tr').removeClass('active');
		}
	} else {			
		$('.bids_action_form').attr('action','<?php pn_the_link_post('bids_action_ajax'); ?>');
		$('.bids_action_form').submit();			
	}	
			
}

<?php do_action('change_bids_filter_js'); ?>

 	$('.bids_action_form').ajaxForm({
		dataType:  'json',
		beforeSubmit: function(a,f,o) {
			$('.filter_change, .filter_link').addClass('active');
			$('.apply_loader').show();
		},
		error: function(res, res2, res3) {
			<?php do_action('pn_js_error_response', 'form'); ?>
		},			
		success: function(res) { 
			$('.filter_change, .filter_link').removeClass('active');
			$('.apply_loader').hide();
			
			if(res['status'] == 'error'){
				<?php do_action('pn_js_alert_response'); ?>
			} else if(res['status'] == 'success'){
				$('#bids_html').html(res['html']);
				assembly_bids_id();
			}
			$('.hotkey_tr').removeClass('active');
		}
	});
	
	$(document).on('click', '.js_bids_action', function(){	
		go_ajax_action();
		return false;
	}); 	
	
  	$(document).on('change', '.check_all', function(){
		if($(this).prop('checked')){
			$('.check_all, .check_one').prop('checked',true);
			$('.check_one').parents('.one_bids').addClass('checked');
		} else {
			$('.check_all, .check_one').prop('checked',false);
			$('.check_one').parents('.one_bids').removeClass('checked');
		}
		
		return false;
	});
	$(document).on('change', '.check_one', function(){
		if($(this).prop('checked')){
			$(this).parents('.one_bids').addClass('checked');
		} else {
			$('.check_all').prop('checked',false);
			$(this).parents('.one_bids').removeClass('checked');
		}
		return false;
	});
	$(document).on('click', '.js_info', function(){
		$(this).parents('.one_bids').find('.js_info_block').stop(true,true).slideToggle(300);
		return false;
	});		
	$(document).on('change', '.sel_action', function(){
		var vale = $(this).val();
		$('.sel_action').val(vale);
		return false;
	});	 	 
	

<?php 
$nocopydata = intval($premiumbox->get_option('nocopydata'));
if(!$nocopydata){ ?>
var clipboard = new Clipboard('.clpb_item');
<?php } ?>	
	
});
</script>
	
<?php
}