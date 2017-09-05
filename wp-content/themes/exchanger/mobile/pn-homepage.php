<?php 
if( !defined( 'ABSPATH')){ exit(); }

/*

Template Name: Home page template

*/

mobile_template('header');

global $premiumbox;

$ho_change = get_option('mho_change');
$array = array('wtitle','wtext','ititle','itext','blocreviews','lastobmen','hidecurr','partners');
$change = array();
foreach($array as $opt){
	$change[$opt] = ctv_ml(is_isset($ho_change,$opt));	
}
?>

<?php
if($change['wtext']){
?>
<div class="home_wtext_block">
	<div class="home_wtext_title"><?php echo pn_strip_input($change['wtitle']); ?></div>
	<div class="home_wtext_div">
		<div class="text">
			<?php echo apply_filters('the_content',$change['wtext']); ?>
				<div class="clear"></div>
		</div>
	</div>
</div>		
<?php } ?>

<div class="xchange_table_wrap">
	<?php if(function_exists('the_exchange_home_mobile')){ the_exchange_home_mobile(); }  ?>
</div>

<?php
if($change['itext']){
?>
<div class="home_text_block">
	<div class="home_text_title"><?php echo pn_strip_input($change['ititle']); ?></div>
	<div class="home_text_div">
		<div class="text">
			<?php echo apply_filters('the_content',$change['itext']); ?>
				<div class="clear"></div>
		</div>
	</div>
</div>	
<?php } ?>

<?php 
if($change['blocreviews'] == 1 and function_exists('list_reviews')){ 
	$review_url = $premiumbox->get_page('reviews');
	$data_posts = list_reviews(3);	
?>
<div class="home_gray_block">
	<div class="home_white_blick"></div>
	<div class="home_gray_block_ins">
		<div class="home_reviews_block">
			<div class="home_reviews_title"><?php _e('Reviews','pntheme'); ?></div>
			
			<div class="home_reviews_div">
			
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
						<div class="home_reviews_date"><?php echo $site1 . pn_strip_input($item->user_name) . $site2; ?>, <?php echo get_mytime($item->review_date , $reviews_date_format); ?></div>
							<div class="clear"></div>
						<div class="home_reviews_content"><?php echo wp_trim_words(pn_strip_input($item->review_text), 15); ?></div>
					</div>			
				
				<?php } ?>
			
				<div class="clear"></div>
			</div>
			
			<div class="home_reviews_more"><a href="<?php echo $review_url; ?>"><?php _e('All reviews','pntheme'); ?></a></div>
		</div>
	</div>
</div>
<?php } ?>

<?php
if($change['lastobmen'] == 1 and function_exists('get_last_bid')){ 
	$last_bid = get_last_bid('success');
	if(isset($last_bid['id'])){	
	?>
	<div class="home_lchange_div">
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
	</div>	
	<?php 
	} 
}
?>	
<?php 
if(function_exists('get_partners') and $change['partners'] == 1){
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

<?php  
if(function_exists('list_view_valuts')){
	$hidecurr = explode(',',is_isset($change,'hidecurr'));
	$valuts = list_view_valuts('', $hidecurr);
	if(count($valuts) > 0){
	?>
	<div class="home_reserv_block">
		<div class="home_reserv_title"><?php _e('Currency reserve','pntheme'); ?></div>
				
		<div class="home_reserv_many">
			<div class="home_reserv_many_ins">
				
				<?php $r=0; 
				foreach($valuts as $valut){ $r++; 
					$cl = '';
					if($r%2==0){ $cl = 'reserv_right'; }
				?> 
				<div class="one_home_reserv <?php echo $cl; ?>"> 
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
					<?php if($r%2==0){ ?><div class="clear"></div><?php } ?>
				<?php } ?>
				
				<div class="clear"></div>
			</div>	
		</div>
	</div>	
	<?php 
	} 
}
?>
		
<?php 
mobile_template('footer');