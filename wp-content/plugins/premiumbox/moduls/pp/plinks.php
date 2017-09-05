<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/
add_action('pn_adminpage_title_pn_plinks', 'def_adminpage_title_pn_plinks');
function def_adminpage_title_pn_plinks(){
	_e('Transitions','pn');
}

add_action('pn_adminpage_content_pn_plinks','def_pn_admin_content_pn_plinks');
function def_pn_admin_content_pn_plinks(){

	if(class_exists('trev_plinks_List_Table')){
		$Table = new trev_plinks_List_Table();
		$Table->prepare_items();
		
		$search = array();
		$search[] = array(
			'view' => 'input',
			'title' => __('User','pn'),
			'default' => pn_strip_input(is_param_get('suser')),
			'name' => 'suser',
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


add_action('premium_action_pn_plinks','def_premium_action_pn_plinks');
function def_premium_action_pn_plinks(){
global $wpdb;

	only_post();
	pn_only_caps(array('administrator','pn_pp'));	
	
	$reply = '';
	$action = get_admin_action();
	if(isset($_POST['id']) and is_array($_POST['id'])){
		if($action == 'delete'){
					
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."plinks WHERE id='$id'");
				if(isset($item->id)){
					do_action('pn_plinks_delete_before', $id, $item);
					$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."plinks WHERE id = '$id'");
					if($result){
						do_action('pn_plinks_delete', $id, $item);
					}
				}
			}
					
			$reply = '&reply=true';		
		}
	} 
			
	$url = admin_url('admin.php?page=pn_plinks'. $reply);
	$paged = intval(is_param_post('paged'));
	if($paged > 1){ $url .= '&paged='.$paged; }	
	wp_redirect($url);
	exit;			
} 


class trev_plinks_List_Table extends WP_List_Table {

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
		    $us = '<a href="'. admin_url('user-edit.php?user_id='. $user_id) .'">' . is_user($item->user_login) . '</a>';
		    return $us;	
		} elseif($column_name == 'cdate'){
		    return pn_strip_input($item->pdate);
		} elseif($column_name == 'cbrowser'){
		    return get_browser_name($item->pbrowser);
		} elseif($column_name == 'cip'){	
			return pn_strip_input($item->pip);
		} elseif($column_name == 'cref'){
		    return pn_strip_input($item->prefer);	
		}
		return apply_filters('plinks_manage_ap_col', '', $column_name,$item);
		
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
			'cuser'    => __('User','pn'),
			'cbrowser'    => __('Browser','pn'),
			'cip'  => __('IP','pn'),
			'cref'  => __('Referral website','pn'),
        );
		$columns = apply_filters('plinks_manage_ap_columns', $columns);
        return $columns;
    }	
	

    function get_bulk_actions() {
        $actions = array(
            'delete'    => __('Delete','pn'),
        );
        return $actions;
    }
    
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_plinks_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;

		$where = '';
		$suser = pn_sfilter(pn_strip_input(is_param_get('suser')));
		if($suser){
			$where = " AND user_login LIKE '%$suser%'";
		}		
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."plinks WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."plinks WHERE id > 0 $where ORDER BY id DESC LIMIT $offset , $per_page");  		

        $current_page = $this->get_pagenum();
        $this->items = $data;
		
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)  
        ));
    }	  
	
}


add_action('premium_screen_pn_plinks','my_myscreen_pn_plinks');
function my_myscreen_pn_plinks() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_plinks_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_plinks_List_Table')){
		new trev_plinks_List_Table;
	}
} 