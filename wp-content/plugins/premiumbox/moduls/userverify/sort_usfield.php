<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** сортировка ************************************************/

add_action('pn_adminpage_title_pn_sort_usfield', 'pn_admin_title_pn_sort_usfield');
function pn_admin_title_pn_sort_usfield(){
	_e('Sort verification fields','pn');
}

add_action('pn_adminpage_content_pn_sort_usfield','def_pn_admin_content_pn_sort_usfield');
function def_pn_admin_content_pn_sort_usfield(){
global $wpdb;	

	$datas = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."uv_field WHERE status='1' AND fieldvid = '0' ORDER BY uv_order ASC");
	$sort_list = array();
	foreach($datas as $item){
		$sort_list[0][] = array(
			'title' => pn_strip_input(ctv_ml($item->title)),
			'id' => $item->id,
			'number' => $item->id,
		);		
	}
	
	pn_sort_one_screen($sort_list);	

	
	$datas = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."uv_field WHERE status='1' AND fieldvid = '1' ORDER BY uv_order ASC");
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


add_action('premium_action_pn_sort_usfield','def_premium_action_pn_sort_usfield');
function def_premium_action_pn_sort_usfield(){
global $wpdb;
	
	if(current_user_can('administrator') or current_user_can('pn_userverify')){
		only_post();
		$number = is_param_post('number');
		$y = 0;
		if(is_array($number)){	
			foreach($number as $theid) { $y++;
				$theid = intval($theid);
				$wpdb->query("UPDATE ".$wpdb->prefix."uv_field SET uv_order='$y' WHERE id = '$theid'");	
			}	
		}
	}
}