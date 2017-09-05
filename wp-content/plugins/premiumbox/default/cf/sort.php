<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** сортировка ************************************************/

add_action('pn_adminpage_title_pn_sort_cf', 'pn_admin_title_pn_sort_cf');
function pn_admin_title_pn_sort_cf(){
	_e('Sort custom fields','pn');
}

add_action('pn_adminpage_content_pn_sort_cf','def_pn_admin_content_pn_sort_cf');
function def_pn_admin_content_pn_sort_cf(){
global $wpdb;
	$datas = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."custom_fields ORDER BY cf_order ASC");
	$sort_list = array();
	foreach($datas as $item){
		$sort_list[0][] = array(
			'title' => pn_strip_input(ctv_ml($item->tech_name)),
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


add_action('premium_action_pn_sort_cf','def_premium_action_pn_sort_cf');
function def_premium_action_pn_sort_cf(){
global $wpdb;	
	only_post();
	if(current_user_can('administrator') or current_user_can('pn_cf')){
		$number = is_param_post('number');
		$y = 0;
		if(is_array($number)){	
			foreach($number as $theid) { $y++;
				$theid = intval($theid);
				$wpdb->query("UPDATE ".$wpdb->prefix."custom_fields SET cf_order='$y' WHERE id = '$theid'");
			}
		}
	}
}