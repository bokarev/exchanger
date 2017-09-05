<?php  
if( !defined( 'ABSPATH')){ exit(); } 
mobile_template('header');
?>
	
<h1 class="page_wrap_title"><?php _e('Error 404','pntheme'); ?></h1>	
	
<div class="page_wrap">	
	<div class="text">
			
		<h3><?php _e('What does it mean?','pntheme'); ?></h3>

		<ul>
			<li><?php _e('Page has been renamed','pntheme'); ?></li>
			<li><?php _e('Page has been deleted','pntheme'); ?></li>
			<li><?php _e('Pages never existed','pntheme'); ?></li>						
		</ul>
						
		<p><?php printf(__('Please go to <a href="%s">main page</a>.','pntheme'), get_site_url_ml()); ?>.</p>
						
		<div class="clear"></div>
	</div>
</div>

<?php 
mobile_template('footer');
?>