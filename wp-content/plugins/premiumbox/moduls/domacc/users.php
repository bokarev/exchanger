<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action( 'show_user_profile', 'pn_edit_user_domacc');
add_action( 'edit_user_profile', 'pn_edit_user_domacc');
function pn_edit_user_domacc($user){
global $wpdb;	
	$user_id = $user->ID;
	$vtypes = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."vtypes");	
	?>	
	<h3><?php _e('Internal account','pn') ?></h3>
	<table class="form-table">
		<?php foreach($vtypes as $vtype){ ?>
			<tr>
				<th style="padding: 5px;">
					<?php echo is_site_value($vtype->vtype_title); ?>
				</th>
				<td style="padding: 5px;">
					<?php echo get_user_domacc($user_id, $vtype->id); ?>
				</td>
			</tr>
		<?php } ?>	
    </table>		
	<?php
}