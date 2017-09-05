<?php  
if( !defined( 'ABSPATH')){ exit(); } 

get_header();
?>
	<?php if(is_category() or is_tax() or is_tag()){ ?>
		<?php 
		$description = trim(term_description()); 
		if($description){
		?>
			<div class="term_description">
				<div class="text">
					<?php echo apply_filters('the_content',$description); ?>
					<div class="clear"></div>
				</div>	
			</div>
		<?php } ?>
	<?php } ?>	
	
	
	<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>	
	
		<div class="one_news" itemscope itemtype="http://schema.org/Article">
			
			<h2 class="one_news_title">
				<a href="<?php the_permalink() ?>" itemprop="url" rel="bookmark" title="<?php the_title_attribute(); ?>"><span itemprop="name"><?php the_title(); ?></span></a>
			</h2>
			
			<div class="one_news_date" itemprop="datePublished" content="<?php the_time('Y-m-d'); ?>">
				<?php the_time(get_option('date_format')); ?>
			</div>
				<div class="clear"></div>
			
			<div class="one_news_excerpt">
				<div class="text" itemprop="articleBody">				
					<?php the_excerpt(); ?>
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
				
				
				<a href="<?php the_permalink();?>" itemprop="url" class="more_news_link"><?php _e('Read more','pntheme'); ?></a>
				    <div class="clear"></div>
			</div>		
		</div>	
	
	<?php endwhile; ?>
		<?php the_pagenavi(); ?>
	<?php else : ?>
	
	<div class="noitem">
		<p><?php _e('Unfortunately this section is empty','pntheme'); ?></p>								
	</div>
	
	<?php endif; ?>
	
<?php 

get_footer();