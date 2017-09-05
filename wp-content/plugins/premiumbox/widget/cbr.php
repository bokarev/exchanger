<?php
class pn_cbr_Widget extends WP_Widget {
	
 	public function __construct($id_base = false, $widget_options = array(), $control_options = array()){
		parent::__construct('get_pn_cbr', __('Exchange rates','pn'), $widget_options = array(), $control_options = array());
	}
	
	public function widget($args, $instance){
		extract($args);

		global $wpdb;	
		
		if(is_ml()){
			
			$lang = get_locale();
			$title = pn_strip_input(is_isset($instance,'title'.$lang));
			if(!$title){ $title = __('Exchange rates','pn'); }
			
		} else {
			
			$title = pn_strip_input(is_isset($instance,'title'));
			if(!$title){ $title = __('Exchange rates','pn'); }	
			
		}
		

		$html = '';
		$r=0;
		
		$cbr = is_isset($instance,'cbr');
		if(!is_array($cbr)){ $cbr = array(); }
		
		$date = __('No','pn');
		$time_parser = get_option('time_parser');
		if($time_parser){
			$date_format = get_option('date_format');
			$time_format = get_option('time_format');
			$date = date("{$date_format}, {$time_format}",$time_parser);
		}		
		
		$curs_parser = get_option('curs_parser');
		if(!is_array($curs_parser)){ $curs_parser = array(); }
		$lcurs_parser = get_option('lcurs_parser');
		if(!is_array($lcurs_parser)){ $lcurs_parser = array(); }

		$parsers = get_list_parsers();
		foreach($parsers as $parser_id => $data){
			if(in_array($parser_id,$cbr)){ $r++;
						
				if($r%2 == 0){
					$oddeven = 'even';
				} else {
					$oddeven = 'odd';
				}			
				
				$p_title = explode('-',is_isset($data, 'para'));
				$p_title1 = pn_strip_input(is_isset($p_title, 0));
				$p_title2 = pn_strip_input(is_isset($p_title, 1));
				
				$p_birg = pn_strip_input(is_isset($data, 'birg'));
				
				$curs_data = is_isset($curs_parser,$parser_id);
				$curs1 = is_out_sum(is_my_money(is_isset($curs_data,'curs1')), 12 ,'course');
				$curs2 = is_out_sum(is_my_money(is_isset($curs_data,'curs2')), 12 ,'course');
				
				$lcurs_data = is_isset($lcurs_parser,$parser_id);
				$lcurs2 = is_out_sum(is_my_money(is_isset($lcurs_data,'curs2')), 12 , 'course');				
				
				$cl2 = '';
				if($lcurs2 > $curs2){
					$cl2 = 'down';
				} elseif($lcurs2 < $curs2){
					$cl2 = 'up';
				}
				
				$temp_html = '
					<div class="widget_cbr_line '. $oddeven .'">
					
						<div class="widget_cbr_left">
							<div class="widget_cbr_title">'. $p_title1 .'/'. $p_title2 .'</div>
							<div class="widget_cbr_birg">'. $p_birg .'</div>
						</div>
						<div class="widget_cbr_curs '. $cl2 .'">
							<div class="widget_cbr_onecurs">'. $curs1 .' '. $p_title1 .'</div>
							<div class="widget_cbr_onecurs">'. $curs2 .' '. $p_title2 .'</div>
						</div>

							<div class="clear"></div>
					</div>		
				';
				
				$html .= apply_filters('cbr_widget_one', $temp_html, $parser_id, $r, $p_title1, $p_title2, $p_birg, $curs1, $curs2, $lcurs2, $data);
			
			}
		}
		
		$widget = '
			<div class="widget_cbr_div">
				<div class="widget_cbr_div_ins">
					<div class="widget_cbr_div_title">
						<div class="widget_cbr_div_title_ins">
							'. $title .'
						</div>
					</div>
					
					'. $html .'
					
					<div class="cbr_update">
						'. __('Update time','pn') .': '. $date .'
					</div>
					
				</div>
			</div>
		';		
		
	    $widget = apply_filters('cbr_widget_block', $widget, $title, $html, $time_parser);
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
 		$parsers = get_list_parsers();
		$cbr = is_isset($instance,'cbr');
		if(!is_array($cbr)){ $cbr = array(); }
		?>
		<div style="border: 1px solid #dedede; padding: 10px; margin: 0 0 10px 0;">
			<div style="max-height: 200px; overflow-y: scroll;" class="cf_div">
				<div><label style="font-weight: 500;"><input class="check_all" type="checkbox" name="0" value="0"> <?php _e('Check all/Uncheck all','pn'); ?></label></div>
				<?php 
 				if(is_array($parsers)){
					foreach($parsers as $parser_key => $parser_data){ ?>
						<div><label><input type="checkbox" name="<?php echo $this->get_field_name('cbr'); ?>[]" <?php if(in_array($parser_key, $cbr)){ ?>checked="checked"<?php } ?> value="<?php echo $parser_key; ?>"> <?php echo $parser_data['title']; ?></label></div>
					<?php }
				} ?>
			</div>
		</div>
		<script type="text/javascript">
		jQuery(function($){
			$('.check_all').on('change', function(){
				var par = $(this).parents('.cf_div');
				if($(this).prop('checked')){
					par.find('input').prop('checked',true);
				} else {
					par.find('input').prop('checked',false);
				}
			});
		});
		</script>		
	<?php
	} 
}

register_widget('pn_cbr_Widget');