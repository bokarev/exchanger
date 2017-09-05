<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_pn_naps', 'pn_adminpage_title_pn_naps');
function pn_adminpage_title_pn_naps(){
	_e('Direction of Exchange','pn');
}

add_action('pn_adminpage_content_pn_naps','def_pn_admin_content_pn_naps');
function def_pn_admin_content_pn_naps(){

	if(class_exists('trev_naps_List_Table')){
		$Table = new trev_naps_List_Table();
		$Table->prepare_items();
?>

<style>
.column-cid{ width: 50px!important; }
.column-merchant, .column-paymerchant{ width: 150px!important; }
</style>

<script type="text/javascript">
jQuery(function($){
 	$(document).on('change', '.m_in', function(){  
		var id = $(this).attr('name');
		var wid = $(this).val();
		var thet = $(this);
		thet.attr('disabled',true);
		
		$('#premium_ajax').show();
		var dataString='id=' + id + '&wid=' + wid;
		
        $.ajax({
			type: "POST",
			url: "<?php pn_the_link_post('merchant_naps_save'); ?>",
			data: dataString,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{
				$('#premium_ajax').hide();	
				thet.attr('disabled',false);
			}
        });
	
        return false;
	});
	
	$(document).on('change', '.m_out', function(){
		var id = $(this).attr('name');
		var wid = $(this).val();
		var thet = $(this);
		thet.attr('disabled',true);
		
		$('#premium_ajax').show();
		var dataString='id=' + id + '&wid=' + wid;
		
        $.ajax({
			type: "POST",
			url: "<?php pn_the_link_post('paymerchant_naps_save'); ?>",
			data: dataString,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{
				$('#premium_ajax').hide();	
				thet.attr('disabled',false);
			}
        });
	
        return false;
	});	 	
});
</script>
	
	<?php
	pn_admin_searchbox(array(), 'reply');
	
	$options = array(
		'1' => __('active direction','pn'),
		'2' => __('inactive direction','pn'),
	);
	pn_admin_submenu('mod', $options, 'reply');		
	?>
	<form method="post" action="<?php pn_the_link_post(); ?>">
		<?php $Table->display() ?>
	</form>
<?php 
	} else {
		echo 'Class not found';
	}
}

add_action('premium_action_merchant_naps_save', 'pn_premium_action_merchant_naps_save');
function pn_premium_action_merchant_naps_save(){
global $wpdb;

	only_post();
	if(current_user_can('administrator') or current_user_can('pn_naps')){
		$data_id = intval(is_param_post('id'));
		if($data_id){
			$wid = is_extension_name(is_param_post('wid'));
			$array = array();
			$array['m_in'] = $wid;
			$wpdb->update($wpdb->prefix.'naps', $array, array('id'=>$data_id));
			$wpdb->query("UPDATE ".$wpdb->prefix."bids SET m_in = '$wid' WHERE naps_id = '$data_id'");
		}	
	}  		
		
}

add_action('premium_action_paymerchant_naps_save', 'pn_premium_action_paymerchant_naps_save');
function pn_premium_action_paymerchant_naps_save(){
global $wpdb;

	only_post();
	if(current_user_can('administrator') or current_user_can('pn_naps')){
		$data_id = intval(is_param_post('id'));
		if($data_id){
			$wid = is_extension_name(is_param_post('wid'));
			$array = array();
			$array['m_out'] = $wid;
			$wpdb->update($wpdb->prefix.'naps', $array, array('id'=>$data_id));
			$wpdb->query("UPDATE ".$wpdb->prefix."bids SET m_out = '$wid' WHERE naps_id = '$data_id'");
		}	
	}  		
		
}

add_action('premium_action_pn_naps','def_premium_action_pn_naps');
function def_premium_action_pn_naps(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_naps'));

	$reply = '';
	$action = get_admin_action();
			
	if(isset($_POST['filter'])){
			
		$ref = is_param_post('_wp_http_referer');
		$url = pn_admin_filter_data($ref, 'reply, val1, val2');			
			
		$val1 = intval(is_param_post('val1'));
		if($val1){
			$url .= '&val1='.$val1;
		}
				
		$val2 = intval(is_param_post('val2'));
		if($val2){
			$url .= '&val2='.$val2;
		}				
				
		wp_redirect($url);
		exit;
				
	} elseif(isset($_POST['back_filter'])){	
				
		$ref = is_param_post('_wp_http_referer');
		$url = pn_admin_filter_data($ref, 'reply, val1, val2');		
			
		$val1 = intval(is_param_post('val1'));
		if($val1){
			$url .= '&val2='.$val1;
		}
				
		$val2 = intval(is_param_post('val2'));
		if($val2){
			$url .= '&val1='.$val2;
		}				
				
		wp_redirect($url);
		exit;				
			
	} elseif(isset($_POST['save'])){
				
		if(isset($_POST['curs1']) and is_array($_POST['curs1']) and isset($_POST['curs2']) and is_array($_POST['curs2'])){
					
			$now_date = current_time('mysql');	
			foreach($_POST['curs1'] as $id => $curs1){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."naps WHERE id='$id'");
				if(isset($item->id)){
					$curs1 = is_my_money($curs1);
					$curs2 = is_my_money($_POST['curs2'][$id]);
							
					if($curs1 != $item->curs1 or $curs2 != $item->curs2){
						$arr = array();
						$arr['editdate'] = $now_date;
						$arr['curs1'] = $curs1;
						$arr['curs2'] = $curs2;
						$wpdb->update($wpdb->prefix.'naps', $arr, array('id'=>$id));
								
						do_action('naps_change_course', $id, $item, $curs1, $curs2, 'editnaps'); 
					}
				}	
			}
		}	 		
				
		do_action('pn_naps_save');
		$reply = '&reply=true';

	} else {	
		if(isset($_POST['id']) and is_array($_POST['id'])){				
				
			if($action == 'active'){
						
				foreach($_POST['id'] as $id){
					$id = intval($id);
					$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."naps WHERE id='$id' AND naps_status != '1'");
					if(isset($item->id)){
						do_action('pn_naps_active_before', $id, $item);
						$result = $wpdb->query("UPDATE ".$wpdb->prefix."naps SET naps_status = '1' WHERE id = '$id'");
						if($result){
							do_action('pn_naps_active', $id, $item);
						}
					}
				}
						
				$reply = '&reply=true';	
			}

			if($action == 'notactive'){
						
				foreach($_POST['id'] as $id){
					$id = intval($id);
					$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."naps WHERE id='$id' AND naps_status != '0'");
					if(isset($item->id)){							
						do_action('pn_naps_notactive_before', $id, $item);
						$result = $wpdb->query("UPDATE ".$wpdb->prefix."naps SET naps_status = '0' WHERE id = '$id'");
						if($result){
							do_action('pn_naps_notactive', $id, $item);
						}
					}
				}
						
				$reply = '&reply=true';	
			}					
				
			if($action == 'delete'){
						
				foreach($_POST['id'] as $id){
					$id = intval($id);
					$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."naps WHERE id='$id'");
					if(isset($item->id)){
						do_action('pn_naps_delete_before', $id, $item);
						$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."naps WHERE id = '$id'");
						if($result){
							do_action('pn_naps_delete', $id, $item);
						}
					}
				}
						
				$reply = '&reply=true';	
			}
					
		} 	
	}
			
	$url = is_param_post('_wp_http_referer') . $reply;
	$paged = intval(is_param_post('paged'));
	if($paged > 1){ $url .= '&paged='.$paged; }	
	wp_redirect($url);
	exit;			
} 

class trev_naps_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
		
		if($column_name == 'course1'){		
		    return '<input type="text" style="width: 100%; max-width: 80px;" name="curs1['. $item->id .']" value="'. is_my_money($item->curs1) .'" />';
		} elseif($column_name == 'course2'){		
		    return '<input type="text" style="width: 100%; max-width: 80px;" name="curs2['. $item->id .']" value="'. is_my_money($item->curs2) .'" />';			
		} elseif($column_name == 'merchant'){	

			$list_merchants = apply_filters('list_merchants',array());
			$m_in = is_extension_name(is_isset($item, 'm_in')); 
			
			$html ='
			<select name="'. $item->id .'" class="m_in" style="width: 150px;" autocomplete="off"> 
				<option value="0" '. selected($m_in,0, false) .'>--'. __('No item','pn') .'--</option>';
											
					foreach($list_merchants as $merch_data){ 
						$merch_id = is_extension_name(is_isset($merch_data,'id'));
						$merch_title = is_isset($merch_data,'title');
						$merch_en = intval(is_enable_merchant($merch_id));
						$enable_title = __('inactive merchant','pn'); if($merch_en == 1){ $enable_title = __('active merchant','pn'); }
						
						$html .='<option value="'. $merch_id .'" '. selected($m_in,$merch_id, false) .'>'. $merch_title .' ['. $enable_title .']</option>';
					} 
					
			$html .='
			</select>			
			';
			
			return $html;

		} elseif($column_name == 'paymerchant'){	

			$list_merchants = apply_filters('list_paymerchants',array());
			$m_out = is_extension_name(is_isset($item, 'm_out')); 
			
			$html ='
			<select name="'. $item->id .'" class="m_out" style="width: 150px;" autocomplete="off"> 
				<option value="0" '. selected($m_out,0, false) .'>--'. __('No item','pn') .'--</option>';
											
					foreach($list_merchants as $merch_data){ 
						$merch_id = is_extension_name(is_isset($merch_data,'id'));
						$merch_title = is_isset($merch_data,'title');
						$merch_en = intval(is_enable_paymerchant($merch_id));
						$enable_title = __('inactive automatic payout','pn');
						if($merch_en == 1){ $enable_title = __('active automatic payout','pn'); }
						
						$html .='<option value="'. $merch_id .'" '. selected($m_out,$merch_id, false) .'>'. $merch_title .' ['. $enable_title .']</option>';
					} 
					
			$html .='
			</select>			
			';
			
			return $html;			
			
		} elseif($column_name == 'copy'){	
			return '<a href="'. pn_link_post('copy_direction_exchange') .'&item_id='. $item->id .'" class="button">'. __('Copy','pn') .'</a>';
		} elseif($column_name == 'status'){
		    if($item->naps_status == 0){ 
			    return '<span class="bred">'. __('inactive direction','pn') .'</span>'; 
			} else { 
			    return '<span class="bgreen">'. __('active direction','pn') .'</span>'; 
			}	
		} elseif($column_name == 'cid'){
			return '<strong>'. $item->id .'</strong>';	
		} 
		return apply_filters('naps_manage_ap_col', '', $column_name,$item);
		
    }	
	
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'], 
            $item->id                
        );
    }	

    function column_title($item){

        $actions = array(
            'edit'      => '<a href="'. admin_url('admin.php?page=pn_add_naps&item_id='. $item->id) .'">'. __('Edit','pn') .'</a>',
        );
		
		if($item->naps_status == 1){
			$actions['view'] = '<a href="'. get_exchange_link($item->naps_name) .'" target="_blank">'. __('View','pn') .'</a>';
		}
   		$primary = apply_filters('naps_manage_ap_primary', pn_strip_input($item->tech_name), $item);
		$actions = apply_filters('naps_manage_ap_actions', $actions, $item);       
        return sprintf('%1$s %2$s',
            $primary,
            $this->row_actions($actions)
        );
		
    }	
	
	function single_row( $item ) {
		$class = '';
		if($item->naps_status == 1){
			$class = 'active';
		}
		echo '<tr class="pn_tr '. $class .'">';
			$this->single_row_columns( $item );
		echo '</tr>';
	}	
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
			'cid'     => __('ID','pn'),
			'title'     => __('Direction','pn'),
			'course1' => __('Exchange rate 1','pn'),
			'course2' => __('Exchange rate 2','pn'),
			'merchant' => __('Merchant','pn'),
			'paymerchant' => __('Automatic payouts','pn'),
			'copy'    => __('Copy exchange direction','pn'),
			'status'    => __('Status','pn'),
        );
		$columns = apply_filters('naps_manage_ap_columns', $columns);
        return $columns;
    }	
	
    function get_sortable_columns() {
        $sortable_columns = array( 
			'course1'     => array('course1',false),
			'course2'     => array('course2',false),
			'cid' => array('cid',false),
        );
        return $sortable_columns;
    }
	
    function get_bulk_actions() {
        $actions = array(
			'active'    => __('Activate','pn'),
			'notactive'    => __('Deactivate','pn'),
            'delete'    => __('Delete','pn'),
        );
        return $actions;
    }
    
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_naps_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		$oby = is_param_get('orderby');
		if($oby == 'course1'){
			$orderby = '(curs1 -0.0)';
		} elseif($oby == 'course2'){
			$orderby = '(curs2 -0.0)';
		} else {
		    $orderby = 'id';
		}
		$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc';
		if($order != 'asc'){ $order = 'desc'; }		
		
		$where = '';
		
        $mod = intval(is_param_get('mod'));
        if($mod == 1){ 
            $where .= " AND naps_status='1'"; 
		} elseif($mod == 2){
			$where .= " AND naps_status='0'";
		}		
		
        $val1 = intval(is_param_get('val1'));
        if($val1 > 0){ 
            $where .= " AND valut_id1='$val1'"; 
		}
        $val2 = intval(is_param_get('val2'));
        if($val2 > 0){ 
            $where .= " AND valut_id2='$val2'"; 
		}		
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."naps WHERE autostatus = '1' $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."naps WHERE autostatus = '1' $where ORDER BY $orderby $order LIMIT $offset , $per_page");  		

        $current_page = $this->get_pagenum();
        $this->items = $data;
		
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)  
        ));
    }
	
	function extra_tablenav( $which ) {
 		global $wpdb;
		
		$valuts = apply_filters('list_valuts_manage', array(), __('All currency','pn'));	
    ?>
		<div class="alignleft actions">
<?php   
		if ( 'top' == $which ) {
			$val1 = intval(is_param_get('val1'));
			$val2 = intval(is_param_get('val2'));
?>
			<select name="val1" autocomplete="off">
            <?php
				foreach($valuts as $key => $title){
					echo "\t<option value='" . $key . "' " . selected($key, $val1, false ) . ">". $title ."</option>\n";
			    }
			?>
			</select>
			
			<input type="submit" name="back_filter" class="back_filter" value="">
			
			<select name="val2" autocomplete="off">
            <?php
				foreach($valuts as $key => $title){
					echo "\t<option value='" . $key . "' " . selected($key, $val2, false ) . ">". $title ."</option>\n";
			    }
			?>
			</select>			
			<input type="submit" name="filter" class="button" value="<?php _e('Filter','pn'); ?>">
<?php
		}
    ?>
	    </div>	
	
		<div class="alignleft actions">
			<input type="submit" name="save" class="button" value="<?php _e('Save','pn'); ?>">
            <a href="<?php echo admin_url('admin.php?page=pn_add_naps');?>" class="button"><?php _e('Add new','pn'); ?></a>
		</div>		
	<?php  
	}	  
	
}


add_action('premium_screen_pn_naps','my_myscreen_pn_naps');
function my_myscreen_pn_naps(){
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_naps_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_naps_List_Table')){
		new trev_naps_List_Table;
	}
} 