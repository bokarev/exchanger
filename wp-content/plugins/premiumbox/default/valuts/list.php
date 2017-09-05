<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_pn_valuts', 'pn_admin_title_pn_valuts');
function pn_admin_title_pn_valuts(){
	_e('Currency','pn');
}

add_action('pn_adminpage_content_pn_valuts','def_pn_admin_content_pn_valuts');
function def_pn_admin_content_pn_valuts(){

	if(class_exists('trev_valuts_List_Table')){
		$Table = new trev_valuts_List_Table();
		$Table->prepare_items();
		
		$search = array();
		
		$vtypes = apply_filters('list_vtypes_manage', array(), __('All codes','pn'));
		$search[] = array(
			'view' => 'select',
			'title' => __('Code','pn'),
			'default' => intval(is_param_get('vtype_id')),
			'options' => $vtypes,
			'name' => 'vtype_id',
		);	
		$psys = apply_filters('list_psys_manage', array(), __('All payment systems','pn'));	
		$search[] = array(
			'view' => 'select',
			'title' => __('Payment system','pn'),
			'default' => intval(is_param_get('psys_id')),
			'options' => $psys,
			'name' => 'psys_id',
		);		
		pn_admin_searchbox($search, 'reply');			
		
		$options = array(
			'1' => __('active currency','pn'),
			'2' => __('inactive currency','pn'),
		);
		pn_admin_submenu('mod', $options, 'reply'); 	
?>
	<form method="post" action="<?php pn_the_link_post(); ?>">
		<?php $Table->display() ?>
	</form>
	
	<script type="text/javascript">
	jQuery(function($){
		$(document).on('click', '.js_button_small', function(){
			var id = $(this).attr('data-id');
			var thet = $(this);
			thet.addClass('active');
			
			$('#premium_ajax').show();
			var dataString='id=' + id;
			
			$.ajax({
				type: "POST",
				url: "<?php pn_the_link_post('pn_currency_updatereserv'); ?>",
				dataType: 'json',
				data: dataString,
				error: function(res, res2, res3){
					<?php do_action('pn_js_error_response', 'ajax'); ?>
				},			
				success: function(res)
				{
					$('#premium_ajax').hide();	
					thet.removeClass('active');
					
					if(res['status'] == 'success'){
						$('.js_reserve_'+id).html(res['reserv']);
					}
					
				}
			});
		
			return false;
		});		
	});
	</script>	
<?php 
	} else {
		echo 'Class not found';
	}
}

add_action('premium_action_pn_currency_updatereserv', 'pn_premium_action_pn_currency_updatereserv');
function pn_premium_action_pn_currency_updatereserv(){
global $wpdb;

	only_post();
	$log = array();
	$log['status'] = 'error';
	$log['status_code'] = 1;
	$log['status_text'] = '';
	
	if(current_user_can('administrator') or current_user_can('pn_valuts')){
		$data_id = intval(is_param_post('id'));
		if($data_id){
			
			if(function_exists('update_valut_reserv')){ 
				update_valut_reserv($data_id);
			}
			$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."valuts WHERE id='$data_id'");
			if(isset($item->id)){
				$log['status'] = 'success';
				$log['reserv'] = get_summ_color(is_my_money($item->valut_reserv, $item->valut_decimal));
			}
			
		}	
	}  		
		
	echo json_encode($log);
	exit;	
}


add_action('premium_action_pn_valuts','def_premium_action_pn_valuts');
function def_premium_action_pn_valuts(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_valuts'));

	$reply = '';
	$action = get_admin_action();
			
	if(isset($_POST['save'])){
				
		if(isset($_POST['valut_decimal']) and is_array($_POST['valut_decimal'])){
			foreach($_POST['valut_decimal'] as $id => $valut_decimal){
				$id = intval($id);
				$valut_decimal = intval($valut_decimal);
				if($valut_decimal < 0){ $valut_decimal = 2; }
							
				$wpdb->query("UPDATE ".$wpdb->prefix."valuts SET valut_decimal = '$valut_decimal' WHERE id = '$id'");
			}
		}				

		if(isset($_POST['lead_num']) and is_array($_POST['lead_num'])){
			foreach($_POST['lead_num'] as $id => $lead_num){
				$id = intval($id);
				$lead_num = intval($lead_num);
				if($lead_num <= 0){ $lead_num = 0; }
							
				$wpdb->query("UPDATE ".$wpdb->prefix."valuts SET lead_num = '$lead_num' WHERE id = '$id'");
			}
		}				
				
				
		do_action('pn_valuts_save');
		$reply = '&reply=true';

	} else {
				
		if(isset($_POST['id']) and is_array($_POST['id'])){				
				
			if($action == 'active'){		
				foreach($_POST['id'] as $id){
					$id = intval($id);
					$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."valuts WHERE id='$id' AND valut_status != '1'");
					if(isset($item->id)){
						do_action('pn_valuts_active_before', $id, $item);
						$result = $wpdb->query("UPDATE ".$wpdb->prefix."valuts SET valut_status = '1' WHERE id = '$id'");
						if($result){
							do_action('pn_valuts_active', $id, $item);
						}
					}
				}
						
				$reply = '&reply=true';		
			}

			if($action == 'notactive'){		
				foreach($_POST['id'] as $id){
					$id = intval($id);
					$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."valuts WHERE id='$id' AND valut_status != '0'");
					if(isset($item->id)){
						do_action('pn_valuts_notactive_before', $id, $item);
						$result = $wpdb->query("UPDATE ".$wpdb->prefix."valuts SET valut_status = '0' WHERE id = '$id'");
						if($result){
							do_action('pn_valuts_notactive', $id, $item);
						}
					}
				}
						
				$reply = '&reply=true';
			}					
				
			if($action == 'delete'){		
				foreach($_POST['id'] as $id){
					$id = intval($id);
					$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."valuts WHERE id='$id'");
					if(isset($item->id)){
						do_action('pn_valuts_delete_before', $id, $item);
						$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."valuts WHERE id = '$id'");
						if($result){
							do_action('pn_valuts_delete', $id, $item);
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

class trev_valuts_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'cid'){
			return $item->id;
		} elseif($column_name == 'code'){
			return is_site_value($item->vtype_title);		
		} elseif($column_name == 'xml_value'){
			return is_xml_value($item->xml_value);		
		} elseif($column_name == 'reserve'){
			$html = '
			<div class="js_reserve_'. $item->id .'">'.get_summ_color(is_my_money($item->valut_reserv, $item->valut_decimal)).'</div>
			<a href="#" data-id="'. $item->id .'" class="js_button_small">'. __('Update','pn') .'</a><div class="premium_clear"></div>
			';	
			return $html;
		} elseif($column_name == 'received'){
			return is_my_money(get_valut_in($item->id), $item->valut_decimal);
		} elseif($column_name == 'issued'){
			return is_my_money(get_valut_out($item->id), $item->valut_decimal);
		} elseif($column_name == 'lead_num'){		
		    return '<input type="text" style="width: 50px;" name="lead_num['. $item->id .']" value="'. intval($item->lead_num) .'" />';		
		} elseif($column_name == 'decimal'){		
		    return '<input type="text" style="width: 50px;" name="valut_decimal['. $item->id .']" value="'. intval($item->valut_decimal) .'" />';				
		} elseif($column_name == 'status'){	
		    if($item->valut_status == 0){ 
			    return '<span class="bred">'. __('inactive currency','pn') .'</span>'; 
			} else { 
			    return '<span class="bgreen">'. __('active currency','pn') .'</span>'; 
			}			
		} 
		return apply_filters('valuts_manage_ap_col', '', $column_name,$item);
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
            'edit'      => '<a href="'. admin_url('admin.php?page=pn_add_valuts&item_id='. $item->id) .'">'. __('Edit','pn') .'</a>',
        );
 		$primary = apply_filters('valuts_manage_ap_primary', pn_strip_input(ctv_ml($item->psys_title)), $item);
		$actions = apply_filters('valuts_manage_ap_actions', $actions, $item);	       
        return sprintf('%1$s %2$s',
            $primary,
            $this->row_actions($actions)
        );
		
    }	
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
			'cid'     => __('ID','pn'),
			'title'     => __('Currency name','pn'),
			'code' => __('Currency code','pn'),
			'reserve' => __('Reserve','pn'),
			'received' => __('Received','pn').' &larr;',
			'issued' => __('Sent','pn').' &rarr;',
			'lead_num' => __('Convert to','pn'),
			'decimal' => __('Amount of Decimal places','pn'),
			'xml_value' => __('XML name','pn'),
			'status'    => __('Status','pn'),
        );
		$columns = apply_filters('valuts_manage_ap_columns', $columns);
        return $columns;
    }	
	
	function single_row( $item ) {
		$class = '';
		if($item->valut_status == 1){
			$class = 'active';
		}
		echo '<tr class="pn_tr '. $class .'">';
			$this->single_row_columns( $item );
		echo '</tr>';
	}
	
    function get_bulk_actions() {
        $actions = array(
			'active'    => __('Activate','pn'),
			'notactive'    => __('Deactivate','pn'),
            'delete'    => __('Delete','pn'),
        );
        return $actions;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array( 
			'cid'     => array('cid',false),
            'title'     => array('title',false),
			'code'     => array('code',false),
        );
        return $sortable_columns;
    }	
	
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_valuts_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		$oby = is_param_get('orderby');
		if($oby == 'title'){
		    $orderby = 'psys_title';
		} elseif($oby == 'code'){	
			$orderby = 'vtype_title';
		} elseif($oby == 'cid'){	
			$orderby = 'id';			
		} else {
		    $orderby = 'site_order';
		}
		$order = is_param_get('order');		
		if($order != 'desc'){ $order = 'asc'; }		
		
		$where = '';
		
        $mod = intval(is_param_get('mod'));
        if($mod == 1){ 
            $where .= " AND valut_status='1'"; 
		} elseif($mod == 2){
			$where .= " AND valut_status='0'";
		}		
		
        $vtype_id = intval(is_param_get('vtype_id'));
        if($vtype_id > 0){ 
            $where .= " AND vtype_id='$vtype_id'"; 
		}
		
        $psys_id = intval(is_param_get('psys_id'));
        if($psys_id > 0){ 
            $where .= " AND psys_id='$psys_id'"; 
		}		
		
		$where = pn_admin_search_where($where);
		
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."valuts WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."valuts WHERE id > 0 $where ORDER BY $orderby $order LIMIT $offset , $per_page");  		

        $current_page = $this->get_pagenum();
        $this->items = $data;
		
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)  
        ));
    }
	
	function extra_tablenav( $which ) {
	?>
		<div class="alignleft actions">
			<input type="submit" name="save" class="button" value="<?php _e('Save','pn'); ?>">
            <a href="<?php echo admin_url('admin.php?page=pn_add_valuts');?>" class="button"><?php _e('Add new','pn'); ?></a>
		</div>		
	<?php 
	}	  
	
}

add_action('premium_screen_pn_valuts','my_myscreen_pn_valuts');
function my_myscreen_pn_valuts() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_valuts_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_valuts_List_Table')){
		new trev_valuts_List_Table;
	}
}