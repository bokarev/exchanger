<?php 
if( !defined( 'ABSPATH')){ exit(); }

mobile_template('header'); 
?>

	<?php 
	if (have_posts()) : ?>
    <?php while (have_posts()) : the_post();  ?>		

		<?php the_content(); ?>				
		
	<?php endwhile; ?>
    <?php endif; ?>			
	
	<div class="clear"></div>	
		
<?php 
mobile_template('footer'); 