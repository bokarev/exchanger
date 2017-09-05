<?php
add_action('widgets_init', 'checkstatus_register_widgets');
function checkstatus_register_widgets(){
	class pn_checkstatus_Widget extends WP_Widget {
		
		public function __construct($id_base = false, $widget_options = array(), $control_options = array()){
			parent::__construct('get_pn_checkstatus', __('Check order status','pn'), $widget_options = array(), $control_options = array());
		}
		
		public function widget($args, $instance){
			extract($args);

			global $wpdb, $premiumbox;	
			
			if(is_ml()){
				
				$lang = get_locale();
				$title = pn_strip_input(is_isset($instance,'title'.$lang));
				if(!$title){ $title = __('Check order status','pn'); }
				
			} else {
				
				$title = pn_strip_input(is_isset($instance,'title'));
				if(!$title){ $title = __('Check order status','pn'); }	
				
			}
			
			$items = get_checkstatus_form_filelds('widget');
			$html = prepare_form_fileds($items, 'widget_checkstatus_form_line', 'widget_checkstatus');	
	
			$array = array(
				'[form]' => '<form method="post" class="ajax_post_form" action="'. get_ajax_link('checkstatusform') .'">',
				'[/form]' => '</form>',
				'[result]' => '<div class="resultgo"></div>',
				'[html]' => $html,
				'[submit]' => '<input type="submit" formtarget="_top" name="submit" class="widget_checkstatus_submit" value="'. __('Check', 'pn') .'" />',
				'[title]' => $title,
			);	
	
			$temp_form = '
			<div class="checkstatus_widget">
				<div class="checkstatus_widget_ins">
				[form]
				
					<div class="checkstatus_widget_title">
						<div class="checkstatus_widget_title_ins">
							[title]
						</div>
					</div>
					
					[result]
			
					<div class="checkstatus_widget_body">
						<div class="checkstatus_widget_body_ins">
						
							[html]
							
							<div class="widget_checkstatus_line_subm">
								[submit]
							</div>							
	 
						</div>
					</div>			

				[/form]
				</div>
			</div>
			';
	
			$temp_form = apply_filters('widget_checkstatus_form_temp',$temp_form);
			echo get_replace_arrays($array, $temp_form);
		
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
		} 
	}

	register_widget('pn_checkstatus_Widget');
}