<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_pp', 'pn_adminpage_title_pn_pp');
function pn_adminpage_title_pn_pp(){
	_e('Statistics','pn');
}

add_action('pn_adminpage_content_pn_pp','def_pn_adminpage_content_pn_pp');
function def_pn_adminpage_content_pn_pp(){
global $wpdb;	
	
	$time = current_time('timestamp');
	$start = date('Y-m-d 00:00:00',$time);
	 
	$plinks = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."plinks WHERE pdate >= '$start'");
	$plinks_all_real = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."plinks");
	$plinks_all_arch = $wpdb->get_var("SELECT SUM(meta_value) FROM ".$wpdb->prefix."archive_data WHERE meta_key='plinks'");
	$plinks_all = $plinks_all_real + $plinks_all_arch;
	
	$pusers = $wpdb->query("SELECT ID FROM ". $wpdb->prefix ."users WHERE ref_id > 0 AND user_registered >= '$start'");
	$pusers_all = $wpdb->query("SELECT ID FROM ". $wpdb->prefix ."users WHERE ref_id > 0");
	
	$bids = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."bids WHERE pcalc='1' AND createdate >= '$start'");
	$bids_all_real = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."bids WHERE pcalc='1'");
	$bids_all_arch = $wpdb->get_var("SELECT SUM(meta_value) FROM ".$wpdb->prefix."archive_data WHERE meta_key='pbids'");
	$bids_all = $bids_all_real + $bids_all_arch;
?>	

<div class="premium_single">
				
	<div class="premium_single_line">
		<strong><?php _e('Affiliate transitions done today','pn'); ?>:</strong> <?php echo $plinks; ?>
	</div>
	<div class="premium_single_line">
		<strong><?php _e('Affiliate transitions','pn'); ?>:</strong> <?php echo $plinks_all; ?>
	</div>	
	
	<div class="premium_single_line">
		<strong><?php _e('Affiliate registrations done today','pn'); ?>:</strong> <?php echo $pusers; ?>
	</div>
	<div class="premium_single_line">
		<strong><?php _e('Affiliate registrations','pn'); ?>:</strong> <?php echo $pusers_all; ?>
	</div>

	<div class="premium_single_line">
		<strong><?php _e('Partnership exchanges done today','pn'); ?>:</strong> <?php echo $bids; ?>
	</div>
	<div class="premium_single_line">
		<strong><?php _e('Partnership exchanges','pn'); ?>:</strong> <?php echo $bids_all; ?>
	</div>	
				
</div>

<?php
}