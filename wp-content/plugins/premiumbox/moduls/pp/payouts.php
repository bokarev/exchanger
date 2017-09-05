<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/
add_action('pn_adminpage_title_pn_payouts', 'pn_admin_title_pn_payouts');
function pn_admin_title_pn_payouts(){
	_e('Payouts','pn');
} 

add_action('pn_adminpage_content_pn_payouts','def_pn_admin_content_pn_payouts');
function def_pn_admin_content_pn_payouts(){

	if(class_exists('trev_payouts_List_Table')){
		$Table = new trev_payouts_List_Table();
		$Table->prepare_items();
		
		$search = array();
		$search[] = array(
			'view' => 'input',
			'title' => __('User','pn'),
			'default' => pn_strip_input(is_param_get('suser')),
			'name' => 'suser',
		);	
		pn_admin_searchbox($search, 'reply');
		
		$options = array(
			'1' => __('waiting','pn'),
			'2' => __('paid','pn'),
			'3' => __('cancelled','pn'),
			'4' => __('cancelled by user','pn'),
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

add_action('premium_action_pn_payouts','def_premium_action_pn_payouts');
function def_premium_action_pn_payouts(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_pp'));

	$reply = '';
	$action = get_admin_action();
			
	if(isset($_POST['xml'])){
				

				
	} else {
				
		if(isset($_POST['id']) and is_array($_POST['id'])){				
				
			if($action=='wait'){
				foreach($_POST['id'] as $id){
					$id = intval($id);
							
					$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."payoutuser WHERE id='$id' AND status != '0'");
					if(isset($item->id)){
						do_action('pn_payoutuser_wait_before', $id, $item);
						$result = $wpdb->query("UPDATE ".$wpdb->prefix."payoutuser SET status = '0' WHERE id = '$id'");
						if($result){
							do_action('pn_payoutuser_wait_after', $id, $item);
						}
					}
				}
			}
			if($action=='success'){
				foreach($_POST['id'] as $id){
					$id = intval($id);
							
					$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."payoutuser WHERE id='$id' AND status != '1'");
					if(isset($item->id)){
						do_action('pn_payoutuser_success_before', $id, $item);
						$result = $wpdb->query("UPDATE ".$wpdb->prefix."payoutuser SET status = '1' WHERE id = '$id'");
						if($result){
							do_action('pn_payoutuser_success_after', $id, $item);
						}
					}
				}	
			}
			if($action=='not'){
				foreach($_POST['id'] as $id){
					$id = intval($id);
							
					$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."payoutuser WHERE id='$id' AND status != '2'");
					if(isset($item->id)){
						do_action('pn_payoutuser_not_before', $id, $item);
						$result = $wpdb->query("UPDATE ".$wpdb->prefix."payoutuser SET status = '2' WHERE id = '$id'");
						if($result){
							do_action('pn_payoutuser_not_after', $id, $item);
						}
					}
				}
			}

			if($action=='delete'){
				foreach($_POST['id'] as $id){
					$id = intval($id);
					$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."payoutuser WHERE id='$id'");
					if(isset($item->id)){
						do_action('pn_payoutuser_delete_before', $id, $item);
						$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."payoutuser WHERE id = '$id'");
						if($result){
							do_action('pn_payoutuser_delete_after', $id, $item);
						}
					}
				}
			}				
					
		} 
				
	}
			
	$url = is_param_post('_wp_http_referer') . $reply;
	$paged = intval(is_param_post('paged'));
	if($paged > 1){ $url .= '&paged='.$paged; }	
	wp_redirect($url);
	exit;		
} 
 
class trev_payouts_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'cuser'){
			
		    $user_id = $item->user_id;
		    $us = '<a href="'. admin_url('user-edit.php?user_id='. $user_id) .'">'. is_user($item->user_login) . '</a>';
			
		    return $us;	
			
		} elseif($column_name == 'cdate'){
			
		    return pn_strip_input($item->pay_date);
			
		} elseif($column_name == 'cnum'){
			
		    return $item->id;
			
		} elseif($column_name == 'csum'){	
			
			return is_my_money($item->pay_sum) .' '. is_site_value($item->vtype_title);
		
		} elseif($column_name == 'csum_or'){	
			
			return is_my_money($item->pay_sum_or) .' '. cur_type();
		
		} elseif($column_name == 'cpurse'){
			
		    return pn_strip_input($item->pay_account);	
			
		} elseif($column_name == 'csys'){

			return pn_strip_input(ctv_ml($item->psys_title));
		
		} elseif($column_name == 'status'){
			$status = intval($item->status);
            if($status == 0){
                $st = '<span>'. __('Request in progress','pn') .'</span>';
            } elseif($status == 1){
                $st = '<span class="bgreen">'. __('Request completed','pn') .'</span>';
            } elseif($status == 2){
                $st = '<span class="bred">'. __('Request rejected','pn') .'</span>';
            } elseif($status == 3){
				$st = '<span class="bred">'. __('Request is cancelled by user','pn') .'</span>';
			}		
			return $st;
		}
		return apply_filters('payouts_manage_ap_col', '', $column_name,$item);
		
    }	
	
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'], 
            $item->id                
        );
    }	
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',  
			'cnum'     => __('ID','pn'),
			'cdate'     => __('Date','pn'),
			'cuser'    => __('User','pn'),
			'csum'    => __('Amount including PS fee','pn'),
			'csum_or'    => __('Amount excluding PS fee','pn').' '.cur_type(),
			'cpurse'  => __('Account','pn'),
			'csys'  => __('PS','pn'),
			'status'  => __('Status','pn'),
        );
		$columns = apply_filters('payouts_manage_ap_columns', $columns);
        return $columns;
    }	
	
	function single_row( $item ) {
		$class = '';
		if($item->status == 1){
			$class = 'active';
		}
		echo '<tr class="pn_tr '. $class .'">';
			$this->single_row_columns( $item );
		echo '</tr>';
	}	

    function get_bulk_actions() {
        $actions = array(
			'wait'    => __('Change status to In progress','pn'),
			'success'    => __('Change status to Paid','pn'),
			'not'    => __('Change status to Not paid','pn'),		
            'delete'    => __('Delete','pn'),
        );
        return $actions;
    }
	
    function get_sortable_columns() {
        $sortable_columns = array( 
			'cnum'     => array('cnum',false),
			'cuser'     => array('cuser',false),
            'cdate'     => array('cdate',false),
			'csum'     => array('csum',false),
			'csum_or'     => array('csum_or',false),
			'csys'     => array('csys',false),
        );
        return $sortable_columns;
    }	
    
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_payouts_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;

		$oby = is_param_get('orderby');
		if($oby == 'cdate'){
		    $orderby = 'pay_date';
		} elseif($oby == 'cnum'){
			$orderby = 'id';
		} elseif($oby == 'cuser'){
			$orderby = 'user_login';
		} elseif($oby == 'csum'){
		    $orderby = '(pay_sum -0.0)';
		} elseif($oby == 'csum_or'){
		    $orderby = '(pay_sum_or -0.0)';			
		} elseif($oby == 'csys'){		
		    $orderby = 'psys_title';
		} else {
		    $orderby = 'id';
		}
		$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc';
		if($order != 'asc'){ $order = 'desc'; }
		
		$where = '';
		$suser = pn_sfilter(pn_strip_input(is_param_get('suser')));
		if($suser){
			$where = " AND user_login LIKE '%$suser%'";
		}
		
        $mod = intval(is_param_get('mod'));
        if($mod==1){ 
            $where .= " AND status = '0'";
		} elseif($mod==2) {
			$where .= " AND status = '1'";
		} elseif($mod==3) {
			$where .= " AND status = '2'";
		} elseif($mod==4) {
			$where .= " AND status = '3'";			
		} 		
		
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."payoutuser WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."payoutuser WHERE id > 0 $where ORDER BY $orderby $order LIMIT $offset , $per_page");  		

        $current_page = $this->get_pagenum();
        $this->items = $data;
		
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)  
        ));
    }		
	
} 

add_action('premium_screen_pn_payouts','my_myscreen_pn_payouts');
function my_myscreen_pn_payouts() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_payouts_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_payouts_List_Table')){
		new trev_payouts_List_Table;
	}
}  