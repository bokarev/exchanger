<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('widgets_init', 'userverify_register_widgets');
function userverify_register_widgets(){
	class userverify_Widget extends WP_Widget {
			
			public function __construct($id_base = false, $widget_options = array(), $control_options = array()){
				parent::__construct('get_userverify', __('Verification','pn'), $widget_options = array(), $control_options = array());
			}
			
			public function widget($args, $instance){
				extract($args);

				global $premiumbox;
				
				if(is_ml()){
					
					$lang = get_locale();
					
					$title = pn_strip_input(is_isset($instance,'title'.$lang));
					if(!$title){ $title = __('Verification','pn'); }
					
					$text1 = pn_strip_input(is_isset($instance,'text1'.$lang));
					if(!$text1){ $text1 = __('Verification confirmed','pn'); }				
					
					$text2 = pn_strip_input(is_isset($instance,'text2'.$lang));
					if(!$text2){ $text2 = __('You have to pass verification procedure','pn'); }

					$text3 = pn_strip_input(is_isset($instance,'text3'.$lang));
					if(!$text3){ $text3 = __('Go to verification','pn'); }				
					
				} else {
					
					$title = pn_strip_input(is_isset($instance,'title'));
					if(!$title){ $title = __('Verification','pn'); }

					$text1 = pn_strip_input(is_isset($instance,'text1'));
					if(!$text1){ $text1 = __('Verification confirmed','pn'); }				
					
					$text2 = pn_strip_input(is_isset($instance,'text2'));
					if(!$text2){ $text2 = __('You have to pass verification procedure','pn'); }

					$text3 = pn_strip_input(is_isset($instance,'text3'));
					if(!$text3){ $text3 = __('Go to verification','pn'); }				
					
				}			
				
				$ui = wp_get_current_user();
				$user_id = intval($ui->ID);
				
				if($user_id){
					$link = $premiumbox->get_page('userverify');
					if(isset($ui->user_verify)){
						$user_verify = $ui->user_verify;
						
						$temp = '
						<div class="userverify_widget">
							<div class="userverify_widget_ins">
								<div class="userverify_widget_title">
									<div class="userverify_widget_title_ins">
										'. $title .'
									</div>	
								</div>
								<div class="userverify_widget_body">
						';
						
						if($user_verify == 1){
							$temp .= '<div class="account_verify true">'. $text1 .'</div>';
						} else {
							$temp .= '<div class="account_verify">'. $text2 .'</div>';
						}
						
						if($user_verify == 0 and $premiumbox->get_option('usve','status') == 1){
							$temp .= '<div class="needverifylink"><a href="'. $link .'">'. $text3 .'</a></div>';	
						}
						
						$temp .= '
								</div>
							</div>
						</div>';
						
						echo apply_filters('userverify_widget_block',$temp, $title, $text1, $text2, $text3, $link, $user_verify, $ui);
					}
				} 

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
					<label for="<?php echo $this->get_field_id('text1'.$key); ?>"><?php _e('Text for verified users','pn'); ?> (<?php echo get_title_forkey($key); ?>): </label><br />
					<input type="text" name="<?php echo $this->get_field_name('text1'.$key); ?>" id="<?php $this->get_field_id('text1'.$key); ?>" class="widefat" value="<?php echo is_isset($instance,'text1'.$key); ?>">
				</p>		
					<?php } ?>
				
				<?php } else { ?>
				<p>
					<label for="<?php echo $this->get_field_id('text1'); ?>"><?php _e('Text for verified users','pn'); ?>: </label><br />
					<input type="text" name="<?php echo $this->get_field_name('text1'); ?>" id="<?php $this->get_field_id('text1'); ?>" class="widefat" value="<?php echo is_isset($instance,'text1'); ?>">
				</p>
				<?php } ?>			

				<?php if(is_ml()){ 
					$langs = get_langs_ml();
					foreach($langs as $key){
				?>
				<p>
					<label for="<?php echo $this->get_field_id('text2'.$key); ?>"><?php _e('Text for unverified users','pn'); ?> (<?php echo get_title_forkey($key); ?>): </label><br />
					<input type="text" name="<?php echo $this->get_field_name('text2'.$key); ?>" id="<?php $this->get_field_id('text2'.$key); ?>" class="widefat" value="<?php echo is_isset($instance,'text2'.$key); ?>">
				</p>		
					<?php } ?>
				
				<?php } else { ?>
				<p>
					<label for="<?php echo $this->get_field_id('text2'); ?>"><?php _e('Text for unverified users','pn'); ?>: </label><br />
					<input type="text" name="<?php echo $this->get_field_name('text2'); ?>" id="<?php $this->get_field_id('text2'); ?>" class="widefat" value="<?php echo is_isset($instance,'text2'); ?>">
				</p>
				<?php } ?>

				<?php if(is_ml()){ 
					$langs = get_langs_ml();
					foreach($langs as $key){
				?>
				<p>
					<label for="<?php echo $this->get_field_id('text3'.$key); ?>"><?php _e('Text content in link needed for verification','pn'); ?> (<?php echo get_title_forkey($key); ?>): </label><br />
					<input type="text" name="<?php echo $this->get_field_name('text3'.$key); ?>" id="<?php $this->get_field_id('text3'.$key); ?>" class="widefat" value="<?php echo is_isset($instance,'text3'.$key); ?>">
				</p>		
					<?php } ?>
				
				<?php } else { ?>
				<p>
					<label for="<?php echo $this->get_field_id('text3'); ?>"><?php _e('Text content in link needed for verification','pn'); ?>: </label><br />
					<input type="text" name="<?php echo $this->get_field_name('text3'); ?>" id="<?php $this->get_field_id('text3'); ?>" class="widefat" value="<?php echo is_isset($instance,'text3'); ?>">
				</p>
				<?php } ?>			
				
			<?php 
			}
			
	}
	register_widget('userverify_Widget');
}	