<?php
class pn_lastobmens_Widget extends WP_Widget {
	
 	public function __construct($id_base = false, $widget_options = array(), $control_options = array()){
		parent::__construct('get_pn_lastobmens', __('Recent exchanges','pn'), $widget_options = array(), $control_options = array());
	}
	
	public function widget($args, $instance){
		extract($args);

		global $wpdb, $live_lchange;	
		
		$live_lchange = 0;
		
		if(is_ml()){
			$lang = get_locale();
			$title = pn_strip_input(is_isset($instance,'title'.$lang));
			if(!$title){ $title = __('Recent exchange','pn'); }
		} else {
			$title = pn_strip_input(is_isset($instance,'title'));
			if(!$title){ $title = __('Recent exchange','pn'); }	
		}
		
		$count = intval(is_isset($instance,'count')); if($count < 1){ $count = 1; }
		$live = intval(is_isset($instance,'live'));
		
		$array_all = array(
			'[title]' => $title,
		);					
			
		$widget = '
		<input type="hidden" name="lc_count" class="lc_count" value="'. $count .'" />
		<div class="widget widget_lchange_div">
			<div class="widget_ins">
				<div class="widget_title">
					<div class="widget_titlevn">
						[title]
					</div>
				</div>';
				
				$cl = '';
				if($live){ 
					$live_lchange++;
					$cl = 'globalajax_ind';
				}
				
				$v = get_valuts_data();
				
				$widget .= '
				<div class="widget_lchange_html">
					<div class="widget_lchange_abs '. $cl .'"></div>
					<div class="widget_lchange_ajax">
				';
				
				$bids = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."bids WHERE status = 'success' ORDER BY editdate DESC LIMIT $count");
				if(count($bids) > 0){
					foreach($bids as $bid){
						$bid_id = $bid->id;
						$valut1 = $bid->valut1i;
						$valut2 = $bid->valut2i;
						if(isset($v[$valut1]) and isset($v[$valut2])){		
							$vd1 = $v[$valut1];
							$vd2 = $v[$valut2];

							$widget .= get_lchange_line($bid, $vd1, $vd2, 'widget');
						}
					}
				} else {
					$widget .= '<div class="widget_lchange_noitem">'. __('No orders','pn') .'</div>';
				}
				
				$widget .= '
					</div>
				</div>';
						
		$widget .= '				
			</div>
		</div>		
		';	

		$widget = apply_filters('lchange_widget_block', $widget);
		$widget = get_replace_arrays($array_all, $widget);				
			
		echo $widget;
	
		if($live_lchange > 0){
			add_filter('globalajax_wp_data_request', 'globalajax_wp_data_request_lastbids');
			add_filter('globalajax_wp_data_jsresult', 'globalajax_wp_data_jsresult_lastbids');	
		}		
	}


	public function form($instance){ 
	?>
		<?php if(is_ml()){ 
			$langs = get_langs_ml();
			foreach($langs as $key){
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title_'.$key); ?>"><?php _e('Title'); ?> (<?php echo get_title_forkey($key); ?>): </label><br />
			<input type="text" name="<?php echo $this->get_field_name('title'.$key); ?>" id="<?php $this->get_field_id('title'.$key); ?>" class="widefat" value="<?php echo is_isset($instance,'title'.$key); ?>">
		</p>		
			<?php } ?>
		
		<?php } else { ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>: </label><br />
			<input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php $this->get_field_id('title'); ?>" class="widefat" value="<?php echo is_isset($instance,'title'); ?>">
		</p>
		<?php } ?>
		<p>
			<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Count of exchanges', 'pn'); ?>: </label><br />
			<input type="text" name="<?php echo $this->get_field_name('count'); ?>" id="<?php $this->get_field_id('count'); ?>" class="widefat" value="<?php echo is_isset($instance,'count'); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('live'); ?>"><?php _e('Online update','pn'); ?>: </label><br />
			<select name="<?php echo $this->get_field_name('live'); ?>" autocomplete="off" id="<?php $this->get_field_id('live'); ?>">
				<option value="0"><?php _e('No','pn'); ?></option>
				<option value="1" <?php selected(is_isset($instance,'live'), 1); ?> ><?php _e('Yes','pn'); ?></option>
			</select>
		</p>		
	<?php
	} 
}
register_widget('pn_lastobmens_Widget');

function globalajax_wp_data_request_lastbids($params){
	
	$params['lchange'] = 1; 
	$params['lc_count'] = "' + $('.lc_count').val() + '";
	
	return $params;
}

add_filter('globalajax_wp_data', 'globalajax_wp_data_lastbids');
function globalajax_wp_data_lastbids($logs){
global $wpdb;

	$lchange = is_param_post('lchange');
	$lastbids = '';
	
	if($lchange == 1){
		$v = get_valuts_data();
		$count = intval(is_param_post('lc_count')); if($count < 1 or $count > 10){ $count = 1; }
		$bids = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."bids WHERE status = 'success' ORDER BY editdate DESC LIMIT $count");
		foreach($bids as $bid){
			$bid_id = $bid->id;
			$valut1 = $bid->valut1i;
			$valut2 = $bid->valut2i;
			if(isset($v[$valut1]) and isset($v[$valut2])){		
				$vd1 = $v[$valut1];
				$vd2 = $v[$valut2];
				$lastbids .= get_lchange_line($bid, $vd1, $vd2, 'widget');	
			}
		}					
	}
	
	$logs['lb_res'] = 1;
	$logs['lb_html'] = $lastbids;
	
	return $logs;	
}	

function globalajax_wp_data_jsresult_lastbids(){
?>
	if(res['lb_res']){
		$('.widget_lchange_ajax').html(res['lb_html']);
	}
<?php	
}