<?php 
if( !defined( 'ABSPATH')){ exit(); } 
mobile_template('header');
?>

<div itemscope itemtype="http://schema.org/Article">

<h1 class="page_wrap_title"><?php the_title(); ?></h1>

<div class="many_news">
<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); 
$pof = get_option('page_on_front');
$sof = get_option('show_on_front');	
$home_url = get_site_url_ml();
if($sof == 'page'){
	$blog_url = get_permalink(get_option('page_for_posts'));
} else {
    $blog_url = $home_url;
}
?>				
		<div class="single_news">
		
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
			
				<div class="metabox_cats">
					<span><?php _e('Category:','pntheme'); ?></span> <?php the_terms( $post->ID, 'category','<span itemprop="articleSection">',', ','</span>'); ?>
				</div>
				<?php the_tags( '<div class="metabox_cats"><span>'. __('Tags:','pntheme') .'</span> ', ', ', '</div>' ); ?>
				
			</div>

			<a href="<?php echo $blog_url;?>" class="more_news_link"><?php _e('Back to news','pntheme'); ?></a>
				<div class="clear"></div>			
		</div>		
<?php endwhile; ?>								
<?php endif; ?>	

</div>
</div>				

<?php 
mobile_template('footer'); 
?>