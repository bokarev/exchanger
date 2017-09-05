<?php
if( !defined( 'ABSPATH')){ exit(); }

class investbox_menu_widget extends WP_Widget {
			
	public function __construct($id_base = false, $widget_options = array(), $control_options = array()){
		parent::__construct('get_investbox_menu_widget', __('Investments Section','inex'), $widget_options = array(), $control_options = array());
	}
			
	public function widget($args, $instance){
		extract($args);

		global $investbox;
				
		if(is_ml()){
					
			$lang = get_locale();
					
			$title = pn_strip_input(is_isset($instance,'title'.$lang));
			if(!$title){ $title = __('Investments Section','inex'); }
					
			$text = pn_strip_input(is_isset($instance,'text'.$lang));
			if(!$text){ $text = __('Invest','inex'); }								
					
		} else {
			
			$title = pn_strip_input(is_isset($instance,'title'));
			if(!$title){ $title = __('Investments Section','inex'); }
			
			$text = pn_strip_input(is_isset($instance,'text'));
			if(!$text){ $text = __('Invest','inex'); }
			
		}			
				

		$temp = '
		<div class="invest_widget">
			<div class="invest_widget_ins">
				<div class="invest_widget_title">
					<div class="invest_widget_title_ins">
						'. $title .'
					</div>	
				</div>
				
				<div class="invest_widget_ul">
					<ul>
						<li><a href="'. $investbox->get_page('toinvest') .'">'. $text .'</a></li>
					</ul>
				</div>
			</div>
		</div>';
		echo $temp;
						
	}

	public function form($instance){ 
?>
			
				<?php if(is_ml()){ 
					$langs = get_langs_ml();
					foreach($langs as $key){
				?>
				<p>
					<label for="<?php echo $this->get_field_id('title'.$key); ?>"><?php _e('Title'); ?> (<?php echo get_title_forkey($key); ?>): </label><br />
					<input type="text" name="<?php echo $this->get_field_name('title'.$key); ?>" id="<?php $this->get_field_id('title'.$key); ?>" class="widefat" value="<?php echo is_isset($instance,'title'.$key); ?>">
				</p>		
					<?php } ?>
				
				<?php } else { ?>
				<p>
					<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>: </label><br />
					<input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php $this->get_field_id('title'); ?>" class="widefat" value="<?php echo is_isset($instance,'title'); ?>">
				</p>
				<?php } ?>
				
				<?php if(is_ml()){ 
					$langs = get_langs_ml();
					foreach($langs as $key){
				?>
				<p>
					<label for="<?php echo $this->get_field_id('text'.$key); ?>"><?php _e('Text link','inex'); ?> (<?php echo get_title_forkey($key); ?>): </label><br />
					<input type="text" name="<?php echo $this->get_field_name('text'.$key); ?>" id="<?php $this->get_field_id('text'.$key); ?>" class="widefat" value="<?php echo is_isset($instance,'text'.$key); ?>">
				</p>		
					<?php } ?>
				
				<?php } else { ?>
				<p>
					<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text link','inex'); ?>: </label><br />
					<input type="text" name="<?php echo $this->get_field_name('text'); ?>" id="<?php $this->get_field_id('text'); ?>" class="widefat" value="<?php echo is_isset($instance,'text'); ?>">
				</p>
				<?php } ?>						
				
			<?php 
	}	
}
register_widget('investbox_menu_widget');