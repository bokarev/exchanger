<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_inex_index', 'adminpage_title_inex_index');
function adminpage_title_inex_index(){
	_e('Deposits','inex');
}  

add_action('pn_adminpage_content_inex_index','def_adminpage_content_inex_index');
function def_adminpage_content_inex_index(){

	if(class_exists('inex_index_List_Table')){
		$Table = new inex_index_List_Table();
		$Table->prepare_items();
		
		global $wpdb;
		
		$valuts = array('0'=>'--' . __('All currency types','inex').'--');
		$ps = array('0'=>'--' .__('All PS','inex').'--');
			
		$deposits = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."inex_deposit ORDER BY gtitle ASC, gvalut ASC");
		foreach($deposits as $dep){
			$val = pn_strip_input($dep->gtitle .' '. $dep->gvalut);
			if(!in_array($val, $ps)){
				$ps[$dep->gid] = $val;
			}
			$type = pn_strip_input($dep->gvalut);
			if(!in_array($type, $valuts)){
				$valuts[$type] = $type;
			}				
		}		
		
		$search = array();
		$search[] = array(
			'view' => 'input',
			'title' => __('User','inex'),
			'default' => pn_strip_input(is_param_get('ulogin')),
			'name' => 'ulogin',
		);
		$search[] = array(
			'view' => 'input',
			'title' => __('Account','inex'),
			'default' => pn_strip_input(is_param_get('schet')),
			'name' => 'schet',
		);
		$search[] = array(
			'view' => 'input',
			'title' => __('Deposit ID','inex'),
			'default' => pn_strip_input(is_param_get('num')),
			'name' => 'num',
		);	
		$search[] = array(
			'view' => 'line',
		);		
		$search[] = array(
			'view' => 'select',
			'options' => $valuts,
			'title' => __('Currency type','inex'),
			'default' => pn_strip_input(is_param_get('thevaluts')),
			'name' => 'thevaluts',
		);
		$search[] = array(
			'view' => 'select',
			'options' => $ps,
			'title' => __('Payment system','inex'),
			'default' => intval(is_param_get('theps')),
			'name' => 'theps',
		);		
		pn_admin_searchbox($search, 'reply');

		$options = array(
			'1' => __('Is not paid','inex'),
			'2' => __('Active','inex'),
			'3' => __('Completed','inex'),
			'4' => __('Close','inex'), 
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
 
add_action('premium_action_inex_index','def_myaction_post_inex_index');
function def_myaction_post_inex_index(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_investbox'));
		
	$reply = '';
	$action = get_admin_action();

	if(isset($_POST['id']) and is_array($_POST['id'])){				
				
		$date = current_time('mysql');	
		if($action=='delete'){
			foreach($_POST['id'] as $id){			
				$id = intval($id);
				$wpdb->query("DELETE FROM ".$wpdb->prefix."inex_deposit WHERE id = '$id'");						
			}
		}
					
	}	
			
	$url = is_param_post('_wp_http_referer') . $reply;
	$paged = intval(is_param_post('paged'));
	if($paged > 1){ $url .= '&paged='.$paged; }	
	wp_redirect($url);
	exit;			
} 


class inex_index_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		
		if($column_name == 'cnum'){
			return $item->id;	
		} elseif($column_name == 'csyst'){
            return pn_strip_input($item->gtitle.' '.$item->gvalut);
		} elseif($column_name == 'cuser'){
			return '<a href="'. admin_url('user-edit.php?user_id='. $item->user_id) .'">'. pn_strip_input($item->user_login) .'</a>';
		} elseif($column_name == 'createdate'){
			return get_mytime($item->createdate,'d.m.Y H:i');		
		} elseif($column_name == 'datestart'){
			return get_mytime($item->indate,'d.m.Y H:i');
		} elseif($column_name == 'dateend'){
			return get_mytime($item->enddate,'d.m.Y H:i');			
		} elseif($column_name == 'cdays'){
			return intval($item->couday);
		} elseif($column_name == 'cpers'){
			return is_my_money($item->pers).'%';
		} elseif($column_name == 'startsumm'){
			return is_my_money($item->insumm);
		} elseif($column_name == 'dohsumm'){
			return is_my_money($item->plussumm);
		} elseif($column_name == 'endsumm'){
			return is_my_money($item->outsumm);			
		} elseif($column_name == 'schet'){
			return pn_strip_input($item->user_schet);		
		} elseif($column_name == 'cstatus'){
			
			$time = current_time('timestamp');
			
			if($item->paystatus == 0){
				$status='<span class="bred">'. __('Is not paid','inex') .'</span>';
			} elseif($item->paystatus == 2){
				$status='<b>'. __('Listed as paid','inex') .'</b>';
			} elseif($item->paystatus == 1 and $item->vipstatus == 0 and strtotime($item->enddate) > $time){
				$status='<b>'. __('Active','inex') .'</b>';
			} elseif($item->paystatus == 1 and $item->vipstatus == 0 and strtotime($item->enddate) < $time){
				$status='<b>'. __('Completed','inex') .'</b>';
			} elseif($item->paystatus == 1 and $item->vipstatus == 1){
				$status='<span class="bgreen">'. __('Close','inex') .'</span>';
			}
 	
			return $status;
		} 		
		
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
			'cnum'    => '№',
			'cuser' => __('User','inex'),
			'createdate' => __('Creation date','inex'), 
			'datestart'    => __('Start date','inex'), 
			'startsumm'     => __('Deposit','inex'),
			'csyst'    => __('PS','inex'),
			'cpers'     => __('Percent','inex'),
			'dohsumm'     => __('Profit','inex'),
			'dateend'    => __('End date','inex'), 
			'cdays'    => __('Number of days','inex'),
			'endsumm'     => __('Amount to be paid','inex'),
			'schet'     => __('Account','inex'),
			'cstatus'  => __('Status','inex'),					
        );
        return $columns;
    }	

    function column_cnum($item){

        $actions = array(
            'edit'      => '<a href="'. admin_url('admin.php?page=inex_add_index&item_id='. $item->id) .'">'. __('Edit','inex') .'</a>',
        );
        
        return sprintf('%1$s %2$s',
            /*$1%s*/ $item->id,
            /*$2%s*/ $this->row_actions($actions)
        );
    }	
	 
    function get_bulk_actions() {
        $actions = array(
            'delete'    => __('Delete','inex'),		
        );
        return $actions;
    }
	
    function get_sortable_columns() {
        $sortable_columns = array( 
            'createdate'     => array('createdate',false),
			'datestart'     => array('datestart',false),
			'dateend'     => array('dateend',false),
			'startsumm'     => array('startsumm',false),
			'cdays'     => array('cdays',false),
        );
        return $sortable_columns;
    }	
    
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('inex_index_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		
		$oby = is_param_get('orderby');
		if($oby == 'createdate'){
		    $orderby = 'createdate';
		} elseif($oby == 'datestart'){
			$orderby = 'indate';
		} elseif($oby == 'dateend'){
			$orderby = 'enddate';		
		} elseif($oby == 'cdays'){
			$orderby = 'couday';		
		} elseif($oby == 'startsumm'){
			$orderby = "(insumm -0.0)";		
		} else {
		    $orderby = 'id';
		}
		$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc';		
		if($order != 'desc'){ $order = 'asc'; }
		
		$where = '';	

		$date = current_time('mysql');
        $mod = intval(is_param_get('mod'));
        if($mod==1){ //не оплачен
            $where .= " AND paystatus = '0'";
		} elseif($mod==2) { //активен
			$where .= " AND vipstatus = '0' AND paystatus='1' AND enddate > '$date'";
		} elseif($mod==3) { //завершен
			$where .= " AND vipstatus = '0' AND paystatus='1' AND enddate < '$date'";
		} elseif($mod==4) { //закрыт
			$where .= " AND vipstatus = '1' AND paystatus='1' AND enddate < '$date'";
		} elseif($mod==5) { //закрыт
			$where .= " AND paystatus = '2'";		
		}  		

		$thevaluts = pn_sfilter(pn_strip_input(is_param_get('thevaluts')));
		if($thevaluts){
			$where .= " AND gvalut = '$thevaluts'";
		}
		$theps = intval(is_param_get('theps'));
		if($theps){
			$where .= " AND gid = '$theps'";
		}
		$ulogin = pn_sfilter(pn_strip_input(is_param_get('ulogin')));
		if($ulogin){
		    $where .= " AND user_login LIKE '%$ulogin%'";
		}
		$schet = pn_sfilter(pn_strip_input(is_param_get('schet')));
		if($schet){
		    $where .= " AND user_schet LIKE '%$schet%'";
		}	
		$num = pn_sfilter(pn_strip_input(is_param_get('num')));
		if($num){
		    $where .= " AND id='$num'";
		}				

		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."inex_deposit WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."inex_deposit WHERE id > 0 $where ORDER BY $orderby $order LIMIT $offset , $per_page");  		

        $current_page = $this->get_pagenum();
        $this->items = $data;
		
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)  
        ) );
    }

	function extra_tablenav( $which ) {
		if ( 'top' == $which ) {
		?>
			<div class="alignleft actions">
				<a href="<?php echo admin_url('admin.php?page=inex_add_index');?>" class="button"><?php _e('Add new','inex'); ?></a>
			</div>			
		<?php 
		}
	}	
	
} 

add_action('premium_screen_inex_index','my_myscreen_inex_index');
function my_myscreen_inex_index() {
    $args = array(
        'label' => __('Display','inex'),
        'default' => 20,
        'option' => 'inex_index_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('inex_index_List_Table')){
		new inex_index_List_Table;
	}
}  