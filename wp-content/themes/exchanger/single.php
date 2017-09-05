<?php 
if( !defined( 'ABSPATH')){ exit(); } 
get_header(); 
?>

<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
						
		<div class="one_news" itemscope itemtype="http://schema.org/Article">
		
			<meta itemprop="name" content="<?php the_title_attribute(); ?>">
			
			<div class="one_news_date" itemprop="datePublished" content="<?php the_time('Y-m-d'); ?>">
				<?php the_time(get_option('date_format')); ?>
			</div>
				<div class="clear"></div>
			
			<div class="one_news_content">
				<div class="text" itemprop="articleBody">				
					<?php the_content(); ?>
						<div class="clear"></div>
				</div>
			</div>
			
			<div class="metabox_div">
			
				<div class="metabox_left">
					<div class="metabox_cats">
						<span><?php _e('Category:','pntheme'); ?></span> <?php the_terms( $post->ID, 'category','<span itemprop="articleSection">',', ','</span>'); ?>
					</div>
					<?php the_tags( '<div class="metabox_cats"><span>'. __('Tags:','pntheme') .'</span> ', ', ', '</div>' ); ?>
				</div>
				
				    <div class="clear"></div>
			</div>		
		</div>
				
<?php endwhile; ?>								
<?php endif; ?>					

<?php get_footer();?>