<?php if( !defined( 'ABSPATH')){ exit(); } 

global $or_template_directory;
$or_template_directory = pn_strip_input($or_template_directory);
$f_change = get_option('f_change');
$array = array('ctext','timetable','phone','vk','fb','gp','tw');
$change = array();
foreach($array as $opt){
	$change[$opt] = ctv_ml(is_isset($f_change,$opt));	
}
?>

		<?php if(!is_front_page()){ ?>	
			</div>
			<div class="sidebar">
				<?php get_sidebar(); ?>
			</div>
				<div class="clear"></div>
		</div>	
		<?php } ?>
	</div>
	
	<!-- footer --> 
	<div class="footer_wrap">
		<div class="footer">
		
			<div class="footer_left">
			
				<div class="copyright">
					<?php if($change['ctext']){ ?>
						<?php echo apply_filters('comment_text', $change['ctext']); ?>
					<?php } else { ?>
						&copy; <?php echo get_copy_date('2015'); ?> PremiumExchanger.com — <?php _e('electronic currency exchange service.','pntheme'); ?>
					<?php } ?>
				</div>
				
				<div class="footer_menu">
					<?php
					wp_nav_menu(array(
					'sort_column' => 'menu_order',
					'container' => 'div',
					'container_class' => 'menu',
					'menu_class' => 'fmenu',
					'menu_id' => '',
					'depth' => '1',
					'fallback_cb' => 'no_menu',
					'theme_location' => 'the_bottom_menu'
					)); 
					?>
						<div class="clear"></div>
				</div>				
			
			</div>
			<div class="footer_center">
				<?php 
				$self_link = lang_self_link();
				$self_link = urlencode($self_link);
				
				$arr = array('vk','fb','gp','tw');
				foreach($arr as $ar){
					if($change[$ar]){
						$link = $change[$ar];
						$link_class = '';
						if(strstr($link,'[soc_link]')){
							$link_class = 'social_link';
							
							if($ar == 'vk'){
								$link = 'http://vk.com/share.php?url='.$self_link;
							} elseif($ar == 'fb'){
								$link = 'https://www.facebook.com/sharer/sharer.php?u='.$self_link;
							} elseif($ar == 'gp'){
								$link = 'https://plus.google.com/share?url='.$self_link;
							} elseif($ar == 'tw'){
								$link = 'http://twitter.com/share?url='.$self_link;
							}
						}
						$link = esc_url($link);
					?>
						<a href="<?php echo $link; ?>" class="<?php echo $link_class; ?>" target="_blank" rel="nofollow"><img src="<?php echo $or_template_directory; ?>/images/<?php echo $ar; ?>-ico.png" alt="" /></a>
					<?php 
					} 
				}
				?>	
					<div class="clear"></div>
			</div>
			<div class="footer_right">
			
				<?php if($change['phone']){ ?>
				<div class="footer_phone">
					<span><?php echo pn_strip_input($change['phone']); ?></span>
				</div>
				<?php } ?>
				
				<?php if($change['timetable']){ ?>
				<div class="footer_timetable">
					<?php echo apply_filters('comment_text',$change['timetable']); ?>
				</div>
				<?php } ?>				
			
			</div>			
				<div class="clear"></div>
		</div>
	</div>
	<!-- end footer -->

</div>

<div id="topped"></div>

<?php do_action('pn_footer_theme'); ?>
<?php wp_footer(); ?>

</body>
</html>