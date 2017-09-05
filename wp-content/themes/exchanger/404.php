<?php  
if( !defined( 'ABSPATH')){ exit(); } 
get_header(); 
?>
	
<div class="text">
		
	<h3><?php _e('What does it mean?','pntheme'); ?></h3>

	<ul>
		<li><?php _e('Page has been renamed','pntheme'); ?></li>
		<li><?php _e('Page has been deleted','pntheme'); ?></li>
		<li><?php _e('Pages never existed','pntheme'); ?></li>						
	</ul>
					
	<p><?php printf(__('Please go to <a href="%s">main page</a>','pntheme'), get_site_url_ml()); ?>.</p>
		            
	<div class="clear"></div>
</div>

<?php get_footer();?>