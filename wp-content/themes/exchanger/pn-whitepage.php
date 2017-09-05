<?php 
if( !defined( 'ABSPATH')){ exit(); }

/*

Template Name: White template

*/

get_header(); 
?>

<div class="whitepage">
<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
						
	<div class="text">
					
		<?php the_content(); ?>
						
			<div class="clear"></div>
	</div>
				
<?php endwhile; ?>								
<?php endif; ?>	
</div>
		
<?php get_footer();?>