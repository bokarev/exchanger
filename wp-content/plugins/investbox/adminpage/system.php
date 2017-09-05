<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_inex_system', 'adminpage_title_inex_system');
function adminpage_title_inex_system($page){
	_e('Payment systems','inex');
} 

/* настройки */
add_action('pn_adminpage_content_inex_system','def_adminpage_content_inex_system');
function def_adminpage_content_inex_system(){
global $wpdb, $investbox;

	$systems = array();
	$systems = apply_filters('invest_systems', $systems);
?>
	<div class="premium_body">
		<table class="premium_standart_table">
			<tr>
				<th style="border-bottom: 1px solid #ccc;"><strong><?php _e('PS title','inex'); ?></strong></th>
				<td style="border-bottom: 1px solid #ccc;"><strong><?php _e('Invested','inex'); ?></strong></td>
				<td style="border-bottom: 1px solid #ccc;"><strong><?php _e('In terms of deposits','inex'); ?></strong></td>
				<td style="border-bottom: 1px solid #ccc;"><strong><?php _e('Paid','inex'); ?></strong></td>
				<td style="border-bottom: 1px solid #ccc;"><strong><?php _e('Need to be paid','inex'); ?></strong></td>
				<td style="border-bottom: 1px solid #ccc;"></td>
			</tr>
	
			<?php
 			if(is_array($systems)){
				$r=0;
				foreach($systems as $gid => $data){ $r++;
					$gid = $investbox->is_system_name($gid);
					$title = is_isset($data,'title') .' '. is_isset($data,'valut');
					?>

					<tr>
						<th><?php echo $title; ?></th>
						<td><?php echo $investbox->get_in_system($gid); ?></td>
						<td><?php echo $investbox->get_deposit_system($gid); ?></td>
						<td><?php echo $investbox->get_outs_system($gid); ?></td>
						<td><?php echo $investbox->get_pays_system($gid); ?></td>
						<td>
						    <?php
							$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."inex_system WHERE gid='$gid'");
							if($cc == 0){
							?>
								<a href="<?php pn_the_link_post(); ?>&action=enable&id=<?php echo $gid; ?>" class="button"><?php _e('Enable','inex'); ?></a>
							<?php } else { ?>
								<a href="<?php pn_the_link_post(); ?>&action=disable&id=<?php echo $gid; ?>" class="button" style="color: #ff0000;"><?php _e('Disable','inex'); ?></a>
							<?php } ?>
						</td>						
					</tr>					
					
					<?php 
				}
				if($r == 0){
					echo '<tr><td colspan="6"><span class="bred">'. __('Payment systems are disabled','inex') .'</span></td></tr>';
				} else { 
					?>
				    <tr>
						<td colspan="5" style="border-top: 1px solid #ccc;">
						<td style="border-top: 1px solid #ccc;">
							<a href="<?php pn_the_link_post(); ?>&action=enableall" class="button"><?php _e('Enable all','inex'); ?></a>
							<a href="<?php pn_the_link_post(); ?>&action=disableall" class="button" style="color: #ff0000;"><?php _e('Disable all','inex'); ?></a>
						</td>
					</tr>					
					<?php
				}	
			} else {
				echo '<tr><td colspan="6">'. __('Error','inex') .'</td></tr>';
			} 
			?>			
		</table>
	</div>
<?php  
}  

/* обработка */
add_action('premium_action_inex_system','def_premium_action_inex_system');
function def_premium_action_inex_system(){
global $wpdb, $investbox;	

	pn_only_caps(array('administrator', 'pn_investbox'));
		
		$action = is_param_get('action');
		$id = $investbox->is_system_name(is_param_get('id'));

		$systems = apply_filters('invest_systems', array());
		if($action == 'enableall'){
			foreach($systems as $gid => $data){
				$title = $data['title'];
				$valut = $data['valut'];
				$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."inex_system WHERE gid='$gid'");
				if($cc == 0){
					$arr = array();
					$arr['title'] = pn_strip_input($title);
					$arr['valut'] = pn_strip_input($valut);
					$arr['gid'] = $gid;
					$wpdb->insert($wpdb->prefix.'inex_system', $arr);
				}
			}
		} elseif($action == 'disableall'){
			foreach($systems as $gid => $data){
				$title = pn_strip_input($data['title']);
				$valut = pn_strip_input($data['valut']);
				$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."inex_system WHERE gid='$gid'");
				if($cc > 0){
					$wpdb->query("DELETE FROM ".$wpdb->prefix."inex_system WHERE gid='$gid'");
				}
			}	
		} elseif($action == 'enable'){
			foreach($systems as $gid => $data){
				if($gid == $id){
					$title = pn_strip_input($data['title']);
					$valut = pn_strip_input($data['valut']);
					$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."inex_system WHERE gid='$gid'");
					if($cc == 0){
						$arr = array();
						$arr['title'] = $title;
						$arr['valut'] = $valut;
						$arr['gid'] = $gid;
						$wpdb->insert($wpdb->prefix.'inex_system', $arr);
					}
				}
			}		
		} elseif($action == 'disable'){		
			foreach($systems as $gid => $data){
				if($gid == $id){
					$title = pn_strip_input($data['title']);
					$valut = pn_strip_input($data['valut']);
					$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."inex_system WHERE gid='$gid'");
					if($cc > 0){
						$wpdb->query("DELETE FROM ".$wpdb->prefix."inex_system WHERE gid='$gid'");
					}
				}
			}	
		}


	$url = admin_url('admin.php?page=inex_system&reply=true');
	wp_redirect($url);
	exit;		
}