<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_live_bids', 'pn_admin_title_pn_live_bids');
function pn_admin_title_pn_live_bids(){
	_e('LIVE orders','pn');
} 

add_action('pn_adminpage_content_pn_live_bids','def_pn_admin_content_pn_live_bids');
function def_pn_admin_content_pn_live_bids(){
global $wpdb;
 
	$ui = wp_get_current_user();
	$user_id = intval($ui->ID);
	
	$ulc = get_user_meta($user_id, 'user_live_change', true);
	if(!is_array($ulc)){ $ulc = array(); }
	
	$autoupdate = intval(is_isset($ulc, 'autoupdate'));
	$timeupdate = intval(is_isset($ulc, 'timeupdate'));
	$rington = intval(is_isset($ulc, 'rington'));
	$hidetransit = intval(is_isset($ulc, 'hidetransit'));
	$status = (string)is_isset($ulc, 'status');
	$statused = explode(',',$status);
	
	$sounds = get_sounds_live();
?>
<div class="head_bids_div">

	<div class="hb_button live_change" data-name="live_change">
		<?php _e('Live','pn'); ?>
	</div>	
	<div class="hb_timer" id="the_timer">
		0
	</div>
		<div class="premium_clear"></div>
		
</div>

<div class="head_bids_window">

	<!-- live changes -->
	<form action="<?php pn_the_link_post('bids_live_change'); ?>" class="bids_live_form" method="post">
	<div class="change_div" id="live_change">
		
		<div class="change_div_title"><?php _e('Settings','pn'); ?></div>
		
		<div class="change_div_line">
			<div class="change_div_label"><?php _e('Page auto-update','pn'); ?></div>
			<div class="change_div_input">
				<select name="autoupdate" id="the_autoupdate" autocomplete="off">
					<option value="0" <?php selected(0,$autoupdate); ?>><?php _e('disable','pn'); ?></option>
					<option value="1" <?php selected(1,$autoupdate); ?>><?php _e('enable','pn'); ?></option>
				</select>
			</div>
		</div>	
		
		<div class="change_div_line">
			<div class="change_div_label"><?php _e('Auto-update period','pn'); ?></div>
			<div class="change_div_input">
				<?php
				$timeupdate_list = array();
				$timeupdate_list['10'] = '10 '. __('seconds','pn');
				$timeupdate_list['30'] = '30 '. __('seconds','pn');
				$timeupdate_list['60'] = '1 '. __('minute','pn');
				$timeupdate_list['180'] = '3 '. __('minutes','pn');
				$timeupdate_list['300'] = '5 '. __('minutes','pn');
				$timeupdate_list['600'] = '10 '. __('minutes','pn');
				$timeupdate_list = apply_filters('timeupdate_list',$timeupdate_list);
				?>
				<select name="timeupdate" id="the_timeupdate" autocomplete="off">
					<?php if(is_array($timeupdate_list)){
						foreach($timeupdate_list as $t => $timer_title){
							$t = intval($t);
					?>
						<option value="<?php echo $t; ?>" <?php selected($t,$timeupdate); ?>><?php echo $timer_title; ?></option>
					<?php }} ?>
				</select>
			</div>
		</div>		
		
		<div class="change_div_line">
			<div class="change_div_label"><?php _e('Ringtone alert','pn'); ?></div>
			<div class="change_div_input">
				<select name="rington" id="the_rington" autocomplete="off">
					<option value="0" <?php selected(0,$rington); ?>>--<?php _e('No ringtone','pn'); ?>--</option>
					<?php foreach($sounds as $sou){ 
						$sound_title = pn_strip_input(is_isset($sou,'title'));
						$sound_id = pn_strip_input(is_isset($sou,'id'));
					?>
						<option value="<?php echo $sound_id; ?>" <?php selected($sound_id,$rington); ?>><?php echo $sound_title; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>		
		
		<div class="change_div_line">
			<div class="change_div_input">
				<label><input type="checkbox" name="hidetransit" id="the_hidetransit" <?php checked(1,$hidetransit); ?> value="1" /> <?php _e('hide orders since transition','pn'); ?></label>
			</div>
		</div>		
		
		<div class="change_div_line">
			<div class="change_div_label"><?php _e('Status','pn'); ?></div>
			<div class="change_div_input">
				
				<?php
				$bid_status_list = apply_filters('bid_status_list',array());
				if(is_array($bid_status_list)){
					foreach($bid_status_list as $key => $status){
					?>
					<div><label><input type="checkbox" name="status[]" class="checkbox_status" <?php if(in_array($key, $statused)){ ?>checked="checked"<?php } ?> value="<?php echo $key; ?>" /> <?php echo $status; ?></label></div>
					<?php }
				}
				?>
				
			</div>
		</div>			
		
		<div class="change_close_wrap">
			<a href="#" class="filter_link f1 change_link_close"><?php _e('Hide','pn'); ?></a>
				<div class="premium_clear"></div>
		</div>		
		
	</div>
	</form>
	<!-- end live changes -->	
	
</div>
	<div class="premium_clear"></div>

<?php
$ldata = $wpdb->get_row("SELECT id FROM ". $wpdb->prefix ."bids WHERE status NOT IN('auto') ORDER BY id DESC LIMIT 1");
$last_id = 0;
if(isset($ldata->id)){
	$last_id = $ldata->id;
}
?>	
	
<input type="hidden" name="" id="last_id" value="<?php echo $last_id; ?>" />
	
<div id="bids_live">
	<div class="nobids" id="nobids"><?php _e('No orders','pn'); ?></div>
</div>			
	
<?php 
foreach($sounds as $sou){ 
	$mp3 = is_isset($sou,'mp3');
	$ogg = is_isset($sou,'ogg');
	$sound_id = is_isset($sou,'id');
?>
<audio class="audio_list" id="sound_id_<?php echo $sound_id; ?>">
    <source src="<?php echo $mp3; ?>"></source>
    <source src="<?php echo $ogg; ?>"></source>
</audio>
<?php } ?>	
				
<script type="text/javascript">
jQuery(function(){ 
	
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
		
		$('.hb_button').removeClass('open');
		$('.change_div').stop(true,true).slideUp(300);
		
		return false;
	});		
	
function rington_go(){
	var vale = $('#the_rington').val();
	if(vale !== '0'){
		var audio = $("#sound_id_"+vale)[0];
		audio.play();
	}	
}

function is_autoupdate(){
	var autoupdate = parseInt($('#the_autoupdate').val());
	if(autoupdate == 1){
		return true;
	} else {
		return false;
	}
}	

function delete_bid(id){
	$('#bid_id'+id).stop(true,true).fadeOut(500, function(){
		$(this).remove();
			
		var ccl = $('.js_bids').length;
		if(ccl == 0){
			$('#nobids').show();
		} 
			
	});	
} 

var nowdata = 0;	
var workin = 1;
function get_new_bids(){

	if(workin == 1){

		workin = 0;
		$('.live_change').addClass('active');
		
		var bid_status = '';
		$('.checkbox_status:checked').each(function(){
			bid_status = bid_status + $(this).val() +',';
		});
		
		var old_id = '';
		$('.js_bids').each(function(){
			old_id = old_id + $(this).attr('id').replace('bid_id','') +',';
		});		
		
		var last_id = parseInt($('#last_id').val());
		
		var param = 'last_id=' + $('#last_id').val() + '&bid_status='+bid_status + '&old_id='+old_id;
		$.ajax({
			type: "POST",
			url: "<?php pn_the_link_post('bids_live_html');?>",
			dataType: 'json',
			data: param,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},
			success: function(res)
			{		
				$('.live_change').removeClass('active');		
				if(res['status'] == 'error'){
					<?php do_action('pn_js_alert_response'); ?>
				} else if(res['status'] == 'success') {
					
					var now_id = parseInt(res['last_id']);
					if(now_id > last_id){
						rington_go();
						$('#last_id').val(now_id);
						
						$('#nobids').hide();
						
						var bids = res['bids'];
						//for (var key in bids)
						for (var i = 0; i < bids.length; i++) {
							var data = bids[i];
							var temp_bid = '<div class="one_bids_live js_bids" id="bid_id'+ data['id'] +'">' +
								'<div class="obl_id">' +
									'<?php _e("ID Order","pn"); ?>: <strong>'+ data['id'] +'</strong>'+
								'</div>'+
								'<div class="obl_why">'+
									'<strong>'+ data['sum_give'] +' '+ data['cur_give'] +' = '+ data['sum_get'] +' '+ data['cur_get'] +'</strong>'+
								'</div>'+
								
								'<div class="obl_status">'+
									'<span class="stname st_'+ data['status'] +'">'+ data['status_name'] +'</span>'+
									'<div class="premium_clear"></div>'+
								'</div>'+
								'<a href="'+ data['link'] +'" target="_blank" class="obl_view"><?php _e("View","pn"); ?></a>'+
								'<div class="obl_close js_close"><?php _e("Hide","pn"); ?></div>'+
									'<div class="premium_clear"></div>'+								
							'</div>';
							
							var ccl = $('.js_bids').length;
							if(ccl > 0){
								$('.js_bids:first').before(temp_bid);
							} else {
								$('#nobids').after(temp_bid);
							}
						}
					}

					var hide_id = res['hide_id'];
					for (var i = 0; i < hide_id.length; i++) {
						var del_id = hide_id[i];
						delete_bid(del_id);
					}
					
				}
				
				nowdata = 0;
				workin = 1;
						
			}
		});	
	
	}
	
} 

	$('.bids_live_form').ajaxForm({
	    dataType:  'json',
        beforeSubmit: function(a,f,o) {
		    $('.live_change').addClass('active');
        },
		error: function(res, res2, res3) {
			<?php do_action('pn_js_error_response', 'form'); ?>
		},		
        success: function(res) {
			$('.live_change').removeClass('active');
			
			if(res['status'] == 'error'){
				<?php do_action('pn_js_alert_response'); ?>
			} else if(res['status'] == 'success'){		
				if(is_autoupdate()){
					get_new_bids();
				}
			}
        }
    });

	$(document).on('change', '#the_timeupdate, #the_autoupdate, #the_rington, #the_hidetransit, .checkbox_status', function(){
		$('.bids_live_form').submit();
	});	
	
	$(document).on('change', '#the_autoupdate', function(){
		if($(this).val() == '0'){
			nowdata = 0;
			$('#the_timer').hide();
		}
	});	
	
	$(document).on('change', '#the_rington', function(){
		rington_go();
	});

var time_display = 0;	
function check_timer_bid(){
	if(is_autoupdate()){
			
		var time_go = parseInt($('#the_timeupdate').val());
		time_display = time_go - nowdata;
		nowdata = parseInt(nowdata) + 1;
		
		if(nowdata >= time_go){
			get_new_bids();
		}
		
		if(time_display > 0){
			$('#the_timer').show().html(time_display);
		}
	}
}	
	setInterval(check_timer_bid,1000);	
	
	$(document).on('click', '.js_close', function(){
		
		var del_id = $(this).parents('.js_bids').attr('id').replace('bid_id','');
		delete_bid(del_id);
		
		return false;
	});

	$(document).on('click', '.obl_view', function(){
		
		if($('#the_hidetransit').prop('checked')){
		
			var del_id = $(this).parents('.js_bids').attr('id').replace('bid_id','');
			delete_bid(del_id);
		
		}
		
	});	
	
});
</script>		
<?php
}