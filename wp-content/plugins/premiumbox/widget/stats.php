<?php
class pn_stats_Widget extends WP_Widget {
	
 	public function __construct($id_base = false, $widget_options = array(), $control_options = array()){
		parent::__construct('get_pn_stats', __('Statistics','pn'), $widget_options = array(), $control_options = array());
	}
	
	public function widget($args, $instance){
		extract($args);

		global $wpdb;	
		
		if(is_ml()){
			$lang = get_locale();
			$title = pn_strip_input(is_isset($instance,'title'.$lang));
			if(!$title){ $title = __('Statistics','pn'); }
		} else {
			$title = pn_strip_input(is_isset($instance,'title'));
			if(!$title){ $title = __('Statistics','pn'); }	
		}
		
		$stats = is_isset($instance,'stats'); 
		if(!is_array($stats)){ $stats = array(); }
		
		$widget = '
		<div class="widget widget_stats_div">
			<div class="widget_ins">
				<div class="widget_title">
					<div class="widget_titlevn">
						'. $title .'
					</div>
				</div>
				';	
				
				$time = current_time('timestamp');
				$date = date('Y-m-d 00:00:00', $time);
				
				$stats = is_isset($instance,'stats'); 
				if(!is_array($stats)){ $stats = array(); }
				
				if(in_array(1, $stats)){
					$widget .= '<div class="widget_stats_line"><span>'. __('Total users','pn') .':</span> '. is_out_sum(get_user_for_site(), 12, 'all') .'</div>';
				}				
				if(in_array(2, $stats)){
					$widget .= '<div class="widget_stats_line"><span>'. __('Registered users today','pn') .':</span> '. is_out_sum(get_user_for_site($date), 12, 'all') .'</div>';
				}
				if(in_array(3, $stats)){
					$widget .= '<div class="widget_stats_line"><span>'. __('Number of exchanges today','pn') .':</span> '. is_out_sum(get_count_exchanges($date), 12, 'all') .'</div>';
				}
				if(in_array(4, $stats)){
					$widget .= '<div class="widget_stats_line"><span>'. __('Amount of exchanges today','pn') .':</span> '. is_out_sum(get_sum_exchanges($date, 'USD'), 12, 'all') .' USD</div>';
				}
				if(in_array(5, $stats)){
					$widget .= '<div class="widget_stats_line"><span>'. __('Paid out to partners','pn') .':</span> '. is_out_sum(get_partner_payouts('', 'USD'), 12, 'all') .' USD</div>';
				}
				if(in_array(6, $stats)){
					$widget .= '<div class="widget_stats_line"><span>'. __('Total amount of reserves','pn') .':</span> '. is_out_sum(get_general_reserv('USD'), 12, 'reserv') .' USD</div>';
				}				
				
				$widget .= '
			</div>
		</div>
		';			
		echo $widget;
		
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
		
		<?php
		$stats = is_isset($instance,'stats'); 
		if(!is_array($stats)){ $stats = array(); }
		?>
		<div style="border: 1px solid #dedede; padding: 10px; margin: 0 0 10px 0;">
			<div><label><input type="checkbox" name="<?php echo $this->get_field_name('stats'); ?>[]" <?php if(in_array(1, $stats)){ ?>checked="checked"<?php } ?> value="1"> <?php _e('Total users','pn'); ?></label></div>
			<div><label><input type="checkbox" name="<?php echo $this->get_field_name('stats'); ?>[]" <?php if(in_array(2, $stats)){ ?>checked="checked"<?php } ?> value="2"> <?php _e('Registered users today','pn'); ?></label></div>
			<div><label><input type="checkbox" name="<?php echo $this->get_field_name('stats'); ?>[]" <?php if(in_array(3, $stats)){ ?>checked="checked"<?php } ?> value="3"> <?php _e('Number of exchanges today','pn'); ?></label></div>
			<div><label><input type="checkbox" name="<?php echo $this->get_field_name('stats'); ?>[]" <?php if(in_array(4, $stats)){ ?>checked="checked"<?php } ?> value="4"> <?php _e('Amount of exchanges today','pn'); ?></label></div>
			<div><label><input type="checkbox" name="<?php echo $this->get_field_name('stats'); ?>[]" <?php if(in_array(5, $stats)){ ?>checked="checked"<?php } ?> value="5"> <?php _e('Paid out to partners','pn'); ?></label></div>
			<div><label><input type="checkbox" name="<?php echo $this->get_field_name('stats'); ?>[]" <?php if(in_array(6, $stats)){ ?>checked="checked"<?php } ?> value="6"> <?php _e('Total amount of reserves','pn'); ?></label></div>
		</div>		
	<?php
	} 
}

register_widget('pn_stats_Widget');