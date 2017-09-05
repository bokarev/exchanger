<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** сортировка ************************************************/

add_action('pn_adminpage_title_pn_sort_table2', 'pn_admin_title_pn_sort_table2');
function pn_admin_title_pn_sort_table2(){
	printf(__('Sort exchange direction for exchange table %s','pn'),'2');
}

add_action('pn_adminpage_content_pn_sort_table2','def_pn_admin_content_pn_sort_table2');
function def_pn_admin_content_pn_sort_table2(){
global $wpdb;
	$place = is_param_get('place');
	
	$selects = array();
	$selects[] = array(
		'link' => admin_url("admin.php?page=pn_sort_table2"),
		'title' => '--' . __('Left column','pn') . '--',
		'background' => '',
		'default' => '',
	);		
	$selects[] = array(
		'link' => admin_url("admin.php?page=pn_sort_table2&place=right"),
		'title' => '--' . __('Right column','pn') . '--',
		'background' => '',
		'default' => 'right',
	);			
	pn_admin_select_box($place, $selects, __('Setting up','pn'));	
	
	$sort_list = array();

	if($place == 'right'){
		$datas = $wpdb->get_results("SELECT DISTINCT(psys_id2) FROM ".$wpdb->prefix."naps WHERE autostatus='1' AND naps_status='1' ORDER BY to2_2 ASC");
		foreach($datas as $val){
			$sort_list[0][] = array(
				'title' => get_pstitle($val->psys_id2),
				'id' => $val->psys_id2,
				'number' => $val->psys_id2,
			);			
		}
		$sort_link = pn_link_ajax('sort_table2_right');
	} else {
		$datas = $wpdb->get_results("SELECT DISTINCT(psys_id1) FROM ".$wpdb->prefix."naps WHERE autostatus='1' AND naps_status='1' ORDER BY to2_1 ASC");
		foreach($datas as $val){
			$sort_list[0][] = array(
				'title' => get_pstitle($val->psys_id1),
				'id' => $val->psys_id1,
				'number' => $val->psys_id1,
			);			
		}
		$sort_link = pn_link_ajax('sort_table2_left');
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
				$.post("<?php echo $sort_link; ?>", order, function(theResponse){
					$('#premium_ajax').hide();
				}); 															 
			}	 				
		});

	});	
	</script>	
<?php 

}


add_action('premium_action_sort_table2_left','def_premium_action_sort_table2_left');
function def_premium_action_sort_table2_left(){
global $wpdb;	
	if(current_user_can('administrator') or current_user_can('pn_naps')){
		
		only_post();
	
			$number = is_param_post('number');
			$y = 0;
			if(is_array($number)){
				
				foreach($number as $theid) { $y++;
				
					$theid = intval($theid);
					$wpdb->query("UPDATE ".$wpdb->prefix."naps SET to2_1='$y' WHERE psys_id1 = '$theid'");
					
				}
				
			}
	}
}

add_action('premium_action_sort_table2_right','def_premium_action_sort_table2_right');
function def_premium_action_sort_table2_right(){
global $wpdb;	
	if(current_user_can('administrator') or current_user_can('pn_naps')){
		
		only_post();
	
			$number = is_param_post('number');
			$y = 0;
			if(is_array($number)){
				
				foreach($number as $theid) { $y++;
				
					$theid = intval($theid);
					$wpdb->query("UPDATE ".$wpdb->prefix."naps SET to2_2='$y' WHERE psys_id2 = '$theid'");
					
				}
				
			}
	}
}