<?php 
if( !defined( 'ABSPATH')){ exit(); } 
get_header(); ?>

<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
						
	<div class="text">
					
		<?php the_content(); ?>
						
			<div class="clear"></div>
	</div>
				
<?php endwhile; ?>								
<?php endif; ?>					

<?php get_footer();?>