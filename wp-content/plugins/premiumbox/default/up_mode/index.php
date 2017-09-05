<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('admin_footer','pn_up_mode_footer');
function pn_up_mode_footer(){ 
global $premiumbox;
	if($premiumbox->get_option('up_mode') == 1){
?>
	<div class="up_mode_div">
		<div class="up_mode_title"><?php printf(__('Update mode on %s','pn'), 'Premium Exchanger'); ?></div>
		<?php if(current_user_can('administrator')){ ?>
		<div class="up_mode_form">
			<form method="post" action="<?php pn_the_link_post('change_up_mode_' . $premiumbox->plugin_name); ?>">
				<input type="hidden" name="_wp_http_referer" value="<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" />
				<input type="hidden" name="up_mode" value="0" />
				<input type="submit" class="button" name="" value="<?php _e('Disable update mode','pn'); ?>" />
			</form>
		</div>
		<?php } ?>
	</div>
<?php	
	}
}

global $premiumbox;
add_action('premium_action_change_up_mode_' . $premiumbox->plugin_name ,'def_premiumbox_action_change_up_mode');
function def_premiumbox_action_change_up_mode(){
global $wpdb, $premiumbox, $or_site_url;	

	only_post();
	
	pn_only_caps(array('administrator'));
		
	$premiumbox->update_option('up_mode', '', 0);	
		
	$back_url = $or_site_url . urldecode(is_param_post('_wp_http_referer'));		
	wp_safe_redirect($back_url);
	exit;					
} 	