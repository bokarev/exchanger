<?php 
if( !defined( 'ABSPATH')){ exit(); }

/*

Template Name: Home page template

*/

get_header(); 

global $premiumbox;

$ho_change = get_option('ho_change');
$array = array('wtitle','wtext','ititle','itext','blocknews','blocreviews','catnews','lastobmen','hidecurr');
$change = array();
foreach($array as $opt){
	$change[$opt] = ctv_ml(is_isset($ho_change,$opt));	
}
?>
<div class="homepage_wrap">

<?php
if($change['wtext']){
?>
<div class="home_wtext_wrap">
	<div class="home_wtext_ins">
		<div class="home_wtext_block">
			<div class="home_wtext_title"><?php echo pn_strip_input($change['wtitle']); ?></div>
			<div class="home_wtext_div">
				<div class="text">
					<?php echo apply_filters('the_content',$change['wtext']); ?>
					<div class="clear"></div>
				</div>
			</div>
		</div>	
	</div>
</div>	
<?php } 
?>

<div class="xchange_table_wrap">
	<?php if(function_exists('the_exchange_home')){ the_exchange_home(); }  ?>
</div>

<?php if(function_exists('the_exchange_widget')){ the_exchange_widget(); } ?>

<?php
if($change['itext']){
?>
<div class="home_text_wrap">
	<!--<div class="home_gray_blick"></div>-->
	<div class="home_text_ins">
		<div class="home_text_block">
			<div class="home_text_title"><?php echo pn_strip_input($change['ititle']); ?></div>
			<div class="home_text_div">
				<div class="text">
					<?php echo apply_filters('the_content',$change['itext']); ?>
					<div class="clear"></div>
				</div>
			</div>
		</div>	
	</div>
</div>	
<?php } 
?>

<?php 
if($change['blocknews'] == 1){  

$sof = get_option('show_on_front'); 
if($sof == 'page'){
	$blog_url = get_permalink(get_option('page_for_posts'));
} else {
	$blog_url = get_site_url_ml();
}

$catnews = intval($change['catnews']);
$args = array(
	'post_type' => 'post',
	'posts_per_page' => 3
);	
if($catnews){
	$args['cat'] = $catnews;
}

$data_posts = get_posts($args);
?>
<div class="home_news_wrap">
	<div class="home_white_blick"></div>
	
	<div class="home_news_ins">
		<div class="home_news_block">
			<div class="home_news_title"><?php _e('News','pntheme'); ?></div>
			
			<div class="home_news_div">
			
				<?php 
				$date_format = get_option('date_format');
				foreach($data_posts as $item){ ?>
				
					<div class="home_news_one">
						<div class="home_news_date"><?php echo get_the_time( $date_format, $item->ID); ?></div>
							<div class="clear"></div>
						<div class="home_news_content"><a href="<?php echo get_permalink($item->ID); ?>"><?php echo pn_strip_input(ctv_ml($item->post_title)); ?></a></div>
					</div>			
				
				<?php } ?>
			
				<div class="clear"></div>
			</div>
			
			<div class="home_news_more"><a href="<?php echo $blog_url; ?>"><?php _e('All news','pntheme'); ?></a></div>
		</div>
	</div>
</div>
<?php } ?>

<?php  
if(function_exists('list_view_valuts')){
	$hidecurr = explode(',',is_isset($change,'hidecurr'));
	$valuts = list_view_valuts('', $hidecurr);
	if(count($valuts) > 0){
	?>
	<div class="home_reserv_wrap">
		<div class="home_gray_blick"></div>
		
		<div class="home_reserv_block_ins">

			<div class="home_reserv_block">
				<div class="home_reserv_title"><?php _e('Currency reserve','pntheme'); ?></div>
				
				<div class="home_reserv_many">
					<div class="home_reserv_many_ins">
				
						<?php $r=0; 
						foreach($valuts as $valut){ $r++; ?> 
						<div class="one_home_reserv"> 
							<div class="one_home_reserv_ico" style="background: url(<?php echo $valut['logo']; ?>) no-repeat center center;"></div>
							<div class="one_home_reserv_block">
								<div class="one_home_reserv_title">
									<?php echo $valut['title']; ?>
								</div>
								<div class="one_home_reserv_sum">
									<?php echo is_out_sum($valut['reserv'], $valut['decimal'], 'reserv'); ?>
								</div>
							</div>
								<div class="clear"></div>
						</div>
							<?php if($r%4==0){ ?><div class="clear"></div><?php } ?>
						<?php } ?>
				
						<div class="clear"></div>
					</div>	
				</div>
			</div>	
		
		</div>
	</div>
	<?php 
	} 
} 
?>

<?php 
if($change['blocreviews'] == 1 and function_exists('list_reviews')){ 
	$review_url = $premiumbox->get_page('reviews');
	$data_posts = list_reviews(3);	
?>
<div class="home_reviews_wrap">
	<div class="home_reviews_ins">
		<div class="home_reviews_block">
			<div class="home_reviews_title"><?php _e('Reviews','pntheme'); ?></div>
			
			<div class="home_reviews_div">
				<div class="home_reviews_div_ins">
			
				<?php 
				$reviews_date_format = apply_filters('reviews_date_format', get_option('date_format').', '.get_option('time_format'));
				
				foreach($data_posts as $item){ 
				
					$site = esc_url($item->user_site);
					$site1 = $site2 = '';
					if($site){
						$site1 = '<a href="'. $site .'" rel="nofollow" target="_blank">';
						$site2 = '</a>';
					}				
				?>
				
					<div class="home_reviews_one">
						<div class="home_reviews_abs"></div>
						<div class="home_reviews_date"><?php echo $site1 . pn_strip_input($item->user_name) . $site2; ?>, <?php echo get_mytime($item->review_date , $reviews_date_format); ?></div>
							<div class="clear"></div>
						<div class="home_reviews_content"><?php echo wp_trim_words(pn_strip_input($item->review_text), 15); ?></div>
					</div>			
				
				<?php } ?>
			
					<div class="clear"></div>
				</div>
			</div>
			
			<div class="home_reviews_more"><a href="<?php echo $review_url; ?>"><?php _e('All reviews','pntheme'); ?></a></div>
		</div>
	</div>
</div>
<?php } 
?>

<div class="home_partner_wrap">
	<div class="home_gray_blick"></div>
	<div class="home_partner_wrap_ins">
		<div class="home_partner_block">
			
			<div class="home_lchange_div">
				<?php
				if($change['lastobmen'] == 1 and function_exists('get_last_bid')){ 
					$last_bid = get_last_bid('success');
					if(isset($last_bid['id'])){	
					?>
				
					<div class="home_lchange_title"><?php _e('Last exchange','pntheme'); ?></div>
					<div class="home_lchange_date"><?php echo $last_bid['createdate']; ?></div>
					<div class="home_lchange_body">
					
						<div class="home_lchange_why">
							<div class="home_lchange_ico" style="background: url(<?php echo $last_bid['logo_give']; ?>) no-repeat center center;"></div>
							<div class="home_lchange_txt">
								<div class="home_lchange_sum"><?php echo is_out_sum($last_bid['sum_give'], $last_bid['decimal_give'], 'all'); ?></div>
								<div class="home_lchange_name"><?php echo $last_bid['vtype_give']; ?></div>
							</div>
								<div class="clear"></div>
						</div>
					
						<div class="home_lchange_arr"></div>
					
						<div class="home_lchange_why">
							<div class="home_lchange_ico" style="background: url(<?php echo $last_bid['logo_get']; ?>) no-repeat center center;"></div>
							<div class="home_lchange_txt">
								<div class="home_lchange_sum"><?php echo is_out_sum($last_bid['sum_get'], $last_bid['decimal_get'], 'all'); ?></div>
								<div class="home_lchange_name"><?php echo $last_bid['vtype_get']; ?></div>
							</div>
						</div>				
							<div class="clear"></div>
					</div>
					<?php 
					} 
				}
				?>	
			</div>
			
			<?php 
 			if(function_exists('get_partners')){
				$partners = get_partners(); 
				if(is_array($partners) and count($partners) > 0){			
			?>			
			<div class="home_partner_div">
				<div class="home_partner_title"><?php _e('Partners','pntheme'); ?></div>
				<?php  
				foreach($partners as $item){ 
					$link = esc_url($item->link);
					?>
						<div class="home_partner_one">
							<?php if($link){ ?><a href="<?php echo $link; ?>" target="_blank"><?php } ?>
								<img src="<?php echo is_ssl_url(pn_strip_input($item->img)); ?>" alt="" />
							<?php if($link){ ?></a><?php } ?>
						</div>
					<?php  
				}
				?>
					<div class="clear"></div>
			</div>
			<?php
				}
			} 
			?>
				<div class="clear"></div>
		</div>	
	</div>
</div>

</div>
		
<?php get_footer(); ?>