<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_zreserv', 'pn_admin_title_pn_zreserv');
function pn_admin_title_pn_zreserv(){
	_e('Reserve requests','pn');
}

add_action('pn_adminpage_content_pn_zreserv','def_pn_admin_content_pn_zreserv');
function def_pn_admin_content_pn_zreserv(){
global $wpdb;

	if(class_exists('trev_zreserv_List_Table')){
		$Table = new trev_zreserv_List_Table();
		$Table->prepare_items();
		
		$search = array();
		
		$naps[0] = '--'. __('All directions','pn') .'--';
		$napobmens = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."naps WHERE autostatus='1' ORDER BY site_order1 ASC");	
		foreach($napobmens as $nap){
			$naps[$nap->id] = pn_strip_input($nap->tech_name);
		}
		$search[] = array(
			'view' => 'select',
			'title' => __('Direction of Exchange','pn'),
			'default' => intval(is_param_get('naps_id')),
			'options' => $naps,
			'name' => 'naps_id',
		);	
		
		pn_admin_searchbox($search, 'reply');
?>
	<form method="post" action="<?php pn_the_link_post(); ?>">
		<?php $Table->display() ?>
	</form>
<?php 
	} else {
		echo 'Class not found';
	}
} 


add_action('premium_action_pn_zreserv','def_premium_action_pn_zreserv');
function def_premium_action_pn_zreserv(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_zreserv'));
	
	$reply = '';
	$action = get_admin_action();
					
	if(isset($_POST['id']) and is_array($_POST['id'])){					
		if($action == 'delete'){		
			foreach($_POST['id'] as $id){
				$id = intval($id);		
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."reserve_requests WHERE id='$id'");
				if(isset($item->id)){							
					do_action('pn_zreserv_delete_before', $id, $item);
					$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."reserve_requests WHERE id = '$id'");
					if($result){
						do_action('pn_zreserv_delete', $id, $item);
					}
				}
			}		
			$reply = '&reply=true';		
		}		
	} 		
			
	$url = is_param_post('_wp_http_referer') . $reply;
	$paged = intval(is_param_post('paged'));
	if($paged > 1){ $url .= '&paged='.$paged; }		
	wp_redirect($url);
	exit;			
} 

class trev_zreserv_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'csumm'){
		    return pn_strip_input($item->amount);
		} elseif($column_name == 'ccom'){		
		    return pn_strip_input($item->comment);		
		} elseif($column_name == 'cdate'){		
		    return get_mytime($item->rdate, 'd.m.Y в H:i');		
		} elseif($column_name == 'cemail'){
		    return '<a href="mailto:'. is_email($item->user_email) .'">'. is_email($item->user_email) .'</a>';
		} elseif($column_name == 'cvals'){
		    return pn_strip_input($item->naps_title);
		}		 
		return apply_filters('zreserv_manage_ap_col', '', $column_name,$item);
		
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
			'cdate'     => __('Date','pn'),
			'cemail'    => __('E-mail','pn'),
			'cvals'  => __('Direction of Exchange','pn'),
            'csumm'  => __('Amount','pn'),
			'ccom'  => __('Comment','pn'),			
        );
		$columns = apply_filters('zreserv_manage_ap_columns', $columns);
        return $columns;
    }	
	
    function get_sortable_columns() {
        $sortable_columns = array( 
			'cdate'     => array('cdate',false),
			'csumm'     => array('csumm',false),
        );
        return $sortable_columns;
    }	

    function get_bulk_actions() {
        $actions = array(
            'delete'    => __('Delete','pn'),
        );
        return $actions;
    }
    
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_zreserv_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		$oby = is_param_get('orderby');
		if($oby == 'csumm'){
		    $orderby = '(amount -0.0)';
		} elseif($oby == 'cdate'){
		    $orderby = 'rdate';		
		} else {
		    $orderby = 'id';
		}
		$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc';
		if($order != 'asc'){ $order = 'desc'; }		
		
		$where = '';
		
        $naps_id = intval(is_param_get('naps_id'));
        if($naps_id > 0){ 
            $where .= " AND naps_id='$naps_id'"; 
		}		
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."reserve_requests WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."reserve_requests WHERE id > 0 $where ORDER BY $orderby $order LIMIT $offset , $per_page");  		

        $current_page = $this->get_pagenum();
        $this->items = $data;
		
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)  
        ));
    }	  
	
}

add_action('premium_screen_pn_zreserv','my_myscreen_pn_zreserv');
function my_myscreen_pn_zreserv() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_zreserv_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_zreserv_List_Table')){
		new trev_zreserv_List_Table;
	}
}