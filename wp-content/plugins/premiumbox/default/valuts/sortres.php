<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** сортировка ************************************************/

add_action('pn_adminpage_title_pn_sort_valuts_reserve', 'pn_adminpage_title_pn_sort_valuts_reserve');
function pn_adminpage_title_pn_sort_valuts_reserve(){
	_e('Sort reserve','pn');
}

add_action('pn_adminpage_content_pn_sort_valuts_reserve','def_pn_admin_content_pn_sort_valuts_reserve');
function def_pn_admin_content_pn_sort_valuts_reserve(){
global $wpdb;

	$datas = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."valuts ORDER BY reserv_order ASC");
	$sort_list = array();
	foreach($datas as $item){
		$sort_list[0][] = array(
			'title' => get_valut_title($item),
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

add_action('premium_action_pn_sort_valuts_reserve','def_premium_action_pn_sort_valuts_reserve');
function def_premium_action_pn_sort_valuts_reserve(){
global $wpdb;
	if(current_user_can('administrator')){
		$number = is_param_post('number');
		$y = 0;
		if(is_array($number)){	
			foreach($number as $theid) { $y++;
				$theid = intval($theid);
				$wpdb->query("UPDATE ".$wpdb->prefix."valuts SET reserv_order='$y' WHERE id = '$theid'");	
			}	
		}
	}
}