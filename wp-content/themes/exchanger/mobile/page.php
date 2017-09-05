<?php 
if( !defined( 'ABSPATH')){ exit(); } 
mobile_template('header');
?>

	<?php if (have_posts()) : ?>
	<?php while (have_posts()) : the_post(); ?>
		
		<h1 class="page_wrap_title"><?php the_title(); ?></h1>
		
		<div class="page_wrap">
			<div class="text">		
				<?php the_content(); ?>			
					<div class="clear"></div>
			</div>
		</div>	
			
	<?php endwhile; ?>								
	<?php endif; ?>					

<?php 
mobile_template('footer'); 
?>