<?php
class pn_lk_Widget extends WP_Widget {
	
 	public function __construct($id_base = false, $widget_options = array(), $control_options = array()){
		parent::__construct('get_pn_lk', __('Personal account','pn'), $widget_options = array(), $control_options = array());
	}
	
	public function widget($args, $instance){
		extract($args);

		global $wpdb, $or_site_url;	
		
		$ui = wp_get_current_user();
		$user_id = intval($ui->ID);
		
		if($user_id){
		
			if(is_ml()){
				
				$lang = get_locale();
				
				$title_widget = pn_strip_input(is_isset($instance,'title'.$lang));
				if(!$title_widget){ $title_widget = __('Personal account','pn'); }
				
			} else {
				
				$title_widget = pn_strip_input(is_isset($instance,'title'));
				if(!$title_widget){ $title_widget = __('Personal account','pn'); }	
				
			}
		
			$list_page = pn_list_user_menu();
	
			$array = array(
				'[list_page]' => $list_page,
				'[exit]' => get_ajax_link('logout', 'get'),
				'[title]' => $title_widget,
				'[user_discount]' => get_user_discount($user_id),
			);	
	
			$temp_form = '
			<div class="user_widget">
				<div class="user_widget_ins">
				
					<div class="user_widget_title">
						<div class="user_widget_title_ins">
							[title]
						</div>
					</div>
					
					<div class="uswidin">
						<div class="uswidinleft">'. __('Your discount','pn').'</div>  
						<div class="uswidinright">[user_discount]%</div>
							<div class="clear"></div>
					</div>					
					
					<div class="user_widget_body">
						<div class="user_widget_body_ins">
						
							[list_page]
								
							<div class="user_widget_exit">
								<a href="[exit]" class="exit_link">'. __('Exit','pn') .'</a>
							</div>							
	 
						</div>
					</div>			

				</div>
			</div>
			';
	
			$temp_form = apply_filters('widget_user_form_temp',$temp_form);
			echo get_replace_arrays($array, $temp_form);			
	
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
	
	<?php

	}  
}

register_widget('pn_lk_Widget');