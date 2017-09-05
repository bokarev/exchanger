<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_inex_out', 'adminpage_title_inex_out');
function adminpage_title_inex_out(){
	_e('Payouts','inex');
} 

add_action('pn_adminpage_content_inex_out','def_adminpage_content_inex_out');
function def_adminpage_content_inex_out(){

	if(class_exists('inex_out_List_Table')){
		$Table = new inex_out_List_Table();
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
			'2' => __('Paid','inex'),
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

add_action('premium_action_inex_out','def_myaction_post_inex_out');
function def_myaction_post_inex_out(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator', 'pn_investbox'));
		
	$reply = '';
	$action = get_admin_action();


	if(isset($_POST['id']) and is_array($_POST['id'])){				
				
		$date = current_time('mysql');
				
		if($action=='true'){
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."inex_deposit WHERE id='$id'");
				if(isset($item->id)){						
					$wpdb->update($wpdb->prefix.'inex_deposit', array('vipstatus'=>'1','outdate'=>$date), array('id'=>$id));
				}			
			}
		}
					
		if($action=='false'){			
			foreach($_POST['id'] as $id){			
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."inex_deposit WHERE id='$id'");
				if(isset($item->id)){
					$wpdb->update($wpdb->prefix.'inex_deposit', array('vipstatus'=>'0','outdate'=>$date), array('id'=>$id));
				}			
			}
		}	

		if($action=='delete'){
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."inex_deposit WHERE id='$id'");
				if(isset($item->id)){
					$wpdb->update($wpdb->prefix.'inex_deposit', array('zakstatus'=>'0','outdate'=>$date), array('id'=>$id));		
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

class inex_out_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'csyst'){
            return pn_strip_input($item->gtitle.' '.$item->gvalut);
		} elseif($column_name == 'cuser'){
			return '<a href="'. admin_url('user-edit.php?user_id='. $item->user_id) .'">'. pn_strip_input($item->user_login) .'</a>';
		} elseif($column_name == 'datestart'){
			return get_mytime($item->indate,'d.m.Y H:i');
		} elseif($column_name == 'dateend'){
			return get_mytime($item->enddate,'d.m.Y H:i');			
		} elseif($column_name == 'cdays'){
			return pn_strip_input($item->couday);
		} elseif($column_name == 'cpers'){
			return pn_strip_input($item->pers);
		} elseif($column_name == 'startsumm'){
			return pn_strip_input($item->insumm);
		} elseif($column_name == 'dohsumm'){
			return pn_strip_input($item->plussumm);
		} elseif($column_name == 'endsumm'){
			return pn_strip_input($item->outsumm);			
		} elseif($column_name == 'schet'){
			return pn_strip_input($item->user_schet);		
		} elseif($column_name == 'cstatus'){
	        $st = pn_strip_input($item->vipstatus);
		    if($st == 1){
				$status='<span class="bgreen">'. __('Paid','inex') .'</span>';
		    } elseif($st == 0){
				$status='<span class="bred">'. __('Is not paid','inex') .'</span>';
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
			'csyst'    => __('PS','inex'),  
			'cuser' => __('User','inex'), 
			'datestart'    => __('Start date','inex'), 
			'dateend'    => __('End date','inex'), 
			'cdays'    => __('Number of days','inex'),
			'cpers'     => __('Percent','inex'),
			'startsumm'     => __('Deposit','inex'),
			'dohsumm'     => __('Profit','inex'),
			'endsumm'     => __('Amount to be paid','inex'),
			'schet'     => __('Account','inex'),
			'cstatus'  => __('Status','inex'),			
        );
        return $columns;
    }		
	 
    function get_bulk_actions() {
        $actions = array(
		    'true'    => __('Change status to "Paid"','inex'),
		    'false'    => __('Change status to "Not paid"','inex'),
            'delete'    => __('Delete','inex'),		
        );
        return $actions;
    }
    
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('inex_out_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		
		$where = '';
		
		$where = '';		
        $mod = intval(is_param_get('mod'));
        if($mod==1){ 
            $where .= " AND vipstatus = '0'";
		} elseif($mod==2) {
			$where .= " AND vipstatus = '1'";	
		}  		

		$thevaluts = pn_sfilter(pn_strip_input(is_param_get('thevaluts')));
		if($thevaluts){
			$where .= " AND gvalut = '$thevaluts'";
		}
		
		$theps = intval(is_param_get('theps'));
		if($theps){
			$where .= " AND gid = '$theps'";
		}				

		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."inex_deposit WHERE paystatus='1' AND zakstatus='1' $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."inex_deposit WHERE paystatus='1' AND zakstatus='1' $where ORDER BY id DESC  LIMIT $offset , $per_page");  		

        $current_page = $this->get_pagenum();
        $this->items = $data;
		
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)  
        ) );
    }	
	
}

add_action('premium_screen_inex_out','my_myscreen_inex_out');
function my_myscreen_inex_out(){
    $args = array(
        'label' => __('Display','inex'),
        'default' => 20,
        'option' => 'inex_out_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('inex_out_List_Table')){
		new inex_out_List_Table;
	}
}  