<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** сортировка ************************************************/

add_action('pn_adminpage_title_pn_sort_bidstatus', 'pn_admin_title_pn_sort_bidstatus');
function pn_admin_title_pn_sort_bidstatus(){
	_e('Sort','pn');
}

add_action('pn_adminpage_content_pn_sort_bidstatus','def_pn_admin_content_pn_sort_bidstatus');
function def_pn_admin_content_pn_sort_bidstatus(){
global $wpdb;

	$datas = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."bidstatus ORDER BY status_order ASC");
	$sort_list = array();
	foreach($datas as $item){
		$sort_list[0][] = array(
			'title' => pn_strip_input(ctv_ml($item->title)),
			'id' => $item->id,
			'number' => $item->id,
		);		
	}
	pn_sort_one_screen($sort_list);	
?>
<script type="text/javascript">
$(document).ready(function(){ 								   
	$(".thesort ul").sortable({ 
		opacity: 0.6, 
		cursor: 'move',
		revert: true,
		update: function() {
			$('#premium_ajax').show();
			
			var order = $(this).sortable("serialize"); 
			$.post("<?php pn_the_link_ajax(); ?>", order, function(theResponse){
				$('#premium_ajax').hide();
			}); 															 
		}	 				
	});
});	
</script>	
<?php 
}


add_action('premium_action_pn_sort_bidstatus','def_premium_action_pn_sort_bidstatus');
function def_premium_action_pn_sort_bidstatus(){
global $wpdb;	
	if(current_user_can('administrator') or current_user_can('pn_bidstatus')){
		only_post();
			$number = is_param_post('number');
			$y = 0;
			if(is_array($number)){
				foreach($number as $theid) { $y++;
					$theid = intval($theid);
					$wpdb->query("UPDATE ".$wpdb->prefix."bidstatus SET status_order='$y' WHERE id = '$theid'");
				}
			}
	}
}