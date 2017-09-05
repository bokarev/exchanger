<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** сортировка ************************************************/

add_action('pn_adminpage_title_pn_sort_table1', 'pn_admin_title_pn_sort_table1');
function pn_admin_title_pn_sort_table1(){
	printf(__('Sort exchange direction for exchange table %s','pn'),'1');
}

add_action('pn_adminpage_content_pn_sort_table1','def_pn_admin_content_pn_sort_table1');
function def_pn_admin_content_pn_sort_table1(){
global $wpdb;

	$places = $places_t = array();
	$place = is_param_get('place');
	$datas = $wpdb->get_results("SELECT DISTINCT(valut_id1) FROM ".$wpdb->prefix."naps WHERE autostatus='1' AND naps_status='1' ORDER BY to1 ASC");
	foreach($datas as $val){
		$places[$val->valut_id1] = get_vtitle($val->valut_id1);
		$places_t[] = $val->valut_id1;
	}
	$selects = array();
	$selects[] = array(
		'link' => admin_url("admin.php?page=pn_sort_table1"),
		'title' => '--' . __('Left column','pn') . '--',
		'background' => '',
		'default' => '',
	);		
	if(is_array($places)){ 
		foreach($places as $key => $val){ 
			$selects[] = array(
				'link' => admin_url("admin.php?page=pn_sort_table1&place=".$key),
				'title' => $val,
				'background' => '',
				'default' => $key,
			);		
		}
	}		
	pn_admin_select_box($place, $selects, __('Setting up','pn'));

if(in_array($place, $places_t)){
	$place = intval($place);
	$items = $wpdb->get_results("SELECT *, ".$wpdb->prefix."naps_order.id AS item_id FROM ".$wpdb->prefix."naps LEFT OUTER JOIN ".$wpdb->prefix."naps_order ON(".$wpdb->prefix."naps.id = ".$wpdb->prefix."naps_order.naps_id) WHERE ".$wpdb->prefix."naps.autostatus='1' AND ".$wpdb->prefix."naps.naps_status='1' AND ".$wpdb->prefix."naps.valut_id1='$place' AND ".$wpdb->prefix."naps_order.v_id='$place'  ORDER BY ".$wpdb->prefix."naps_order.order1 ASC");	

	$sort_list = array();
	foreach($items as $item){
		$sort_list[0][] = array(
			'title' => get_vtitle($item->valut_id2),
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
				$.post("<?php pn_the_link_ajax('pn_sort_table1_sort'); ?>", order, function(theResponse){
					$('#premium_ajax').hide();
				}); 															 
			}	 				
		});
	});	
	</script>	
<?php
} else {
	$sort_list = array();
	foreach($datas as $item){
		$sort_list[0][] = array(
			'title' => get_vtitle($item->valut_id1),
			'id' => $item->valut_id1,
			'number' => $item->valut_id1,
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
				$.post("<?php pn_the_link_ajax('pn_sort_table1_left'); ?>", order, function(theResponse){
					$('#premium_ajax').hide();
				}); 															 
			}	 				
		});
	});	
	</script>	
<?php
} 	
}

add_action('premium_action_pn_sort_table1_left','def_premium_action_pn_sort_table1_left');
function def_premium_action_pn_sort_table1_left(){
global $wpdb;	
	if(current_user_can('administrator') or current_user_can('pn_naps')){
		only_post();
	
			$number = is_param_post('number');
			$y = 0;
			if(is_array($number)){
				foreach($number as $theid) { $y++;
					$theid = intval($theid);
					$wpdb->query("UPDATE ".$wpdb->prefix."naps SET to1='$y' WHERE valut_id1 = '$theid'");
				}
			}
	}
}

add_action('premium_action_pn_sort_table1_sort','def_premium_action_pn_sort_table1_sort');
function def_premium_action_pn_sort_table1_sort(){
global $wpdb;	
	if(current_user_can('administrator') or current_user_can('pn_naps')){
		only_post();
	
		$number = is_param_post('number');
		$y = 0;
		if(is_array($number)){	
			foreach($number as $theid) { $y++;
				$theid = intval($theid);
				$wpdb->query("UPDATE ".$wpdb->prefix."naps_order SET order1='$y' WHERE id = '$theid'");	
			}	
		}
	}
}