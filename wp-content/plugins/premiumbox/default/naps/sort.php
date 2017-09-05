<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** сортировка ************************************************/

add_action('pn_adminpage_title_pn_sort_naps', 'pn_admin_title_pn_sort_naps');
function pn_admin_title_pn_sort_naps(){
	_e('Sort exchange directions','pn');
}

add_action('pn_adminpage_content_pn_sort_naps','def_pn_admin_content_pn_sort_naps');
function def_pn_admin_content_pn_sort_naps(){
global $wpdb;

	$items = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE autostatus='1' AND naps_status='1' ORDER BY site_order1 ASC");
	$sort_list = array();
	foreach($items as $item){
		$sort_list[0][] = array(
			'title' => $item->tech_name,
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
				$.post("<?php pn_the_link_ajax('pn_sort_naps'); ?>", order, function(theResponse){
					$('#premium_ajax').hide();
				}); 															 
			}	 				
		});

	});	
	</script>	
<?php
}


add_action('premium_action_pn_sort_naps','def_premium_action_pn_sort_naps');
function def_premium_action_pn_sort_naps(){
global $wpdb;	

	if(current_user_can('administrator') or current_user_can('pn_naps')){
		only_post();
			$number = is_param_post('number');
			$y = 0;
			if(is_array($number)){
				foreach($number as $theid) { $y++;
					$theid = intval($theid);
					$wpdb->query("UPDATE ".$wpdb->prefix."naps SET site_order1='$y' WHERE id = '$theid'");
				}
			}
	}
}