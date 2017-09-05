<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_pn_userwallets', 'pn_adminpage_title_pn_userwallets');
function pn_adminpage_title_pn_userwallets(){
	_e('User accounts','pn');
}

add_action('pn_adminpage_content_pn_userwallets','def_pn_adminpage_content_pn_userwallets');
function def_pn_adminpage_content_pn_userwallets(){

	if(class_exists('trev_userwallets_List_Table')){
		$Table = new trev_userwallets_List_Table();
		$Table->prepare_items();
		
		$search = array();
		$search[] = array(
			'view' => 'input',
			'title' => __('User','pn'),
			'default' => pn_strip_input(is_param_get('user')),
			'name' => 'user',
		);
		$search[] = array(
			'view' => 'input',
			'title' => __('Account number','pn'),
			'default' => pn_strip_input(is_param_get('accountnum')),
			'name' => 'accountnum',
		);		
		$valuts = apply_filters('list_valuts_manage', array(), __('All currency','pn'));
		$search[] = array(
			'view' => 'select',
			'options' => $valuts,
			'title' => __('Currency','pn'),
			'default' => intval(is_param_get('valut_id')),
			'name' => 'valut_id',
		);			
		pn_admin_searchbox($search, 'reply');		
		
		$options = array(
			'1' => __('verified account','pn'),
			'2' => __('unverified account','pn'),
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

add_action('premium_action_pn_userwallets','def_premium_action_pn_userwallets');
function def_premium_action_pn_userwallets(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_userwallets'));
	
	$reply = '';
	$action = get_admin_action();	 			
	if(isset($_POST['id']) and is_array($_POST['id'])){

		if($action == 'verify'){		
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."user_accounts WHERE id='$id' AND verify != '1'");
				if(isset($item->id)){
					do_action('pn_userwallets_verify_before', $id, $item);
					$result = $wpdb->query("UPDATE ".$wpdb->prefix."user_accounts SET verify = '1' WHERE id = '$id'");
					if($result){
						do_action('pn_userwallets_verify', $id, $item);
					}
				}
			}
						
			$reply = '&reply=true';		
		}
		if($action == 'unverify'){		
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."user_accounts WHERE id='$id' AND verify != '0'");
				if(isset($item->id)){
					do_action('pn_userwallets_unverify_before', $id, $item);
					$result = $wpdb->query("UPDATE ".$wpdb->prefix."user_accounts SET verify = '0' WHERE id = '$id'");
					if($result){
						do_action('pn_userwallets_unverify', $id, $item);
					}
				}
			}
						
			$reply = '&reply=true';		
		}		
		if($action == 'delete'){
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."user_accounts WHERE id='$id'");
				if(isset($item->id)){
					do_action('pn_userwallets_delete_before', $id, $item);
					$result = $wpdb->query("DELETE FROM ". $wpdb->prefix ."user_accounts WHERE id = '$id'");
					if($result){
						do_action('pn_userwallets_delete', $id, $item);
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

class trev_userwallets_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'user'){
			
			$user_id = $item->user_id;
		    $us = '<a href="'. admin_url('user-edit.php?user_id='. $user_id) .'">' . is_user($item->user_login) . '</a>'; 
			
		    return $us;	
		
		} elseif($column_name == 'cps'){ 	
			
			return get_vtitle($item->valut_id);		
		
		} elseif($column_name == 'status'){

			if($item->verify == 1){
				$status ='<span class="bgreen">'. __('Verified','pn') .'</span>';
			} else {
				$status ='<span class="bred">'. __('Unverified','pn') .'</span>';
			} 	
			return $status;
		} 
		return apply_filters('userwallets_manage_ap_col', '', $column_name,$item);
		
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
            'edit'      => '<a href="'. admin_url('admin.php?page=pn_add_userwallets&item_id='. $item->id) .'">'. __('Edit','pn') .'</a>',
        );
   		$primary = apply_filters('userwallets_manage_ap_primary', pn_strip_input($item->accountnum), $item);
		$actions = apply_filters('userwallets_manage_ap_actions', $actions, $item);       
        return sprintf('%1$s %2$s',
            $primary,
            $this->row_actions($actions)
        );
		
    }	
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',          
			'title'     => __('Account number','pn'),
			'user'    => __('User','pn'),
			'cps' => __('PS','pn'),
			'status'    => __('Status','pn'),
        );
		$columns = apply_filters('userwallets_manage_ap_columns', $columns);
        return $columns;
    }	
	
	function single_row( $item ) {
		$class = '';
		if($item->verify == 1){
			$class = 'active';
		}
		echo '<tr class="pn_tr '. $class .'">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}
	
    function get_bulk_actions() {
        $actions = array(
			'verify'    => __('Verified','pn'),
			'unverify'    => __('Unverified','pn'),
            'delete'    => __('Delete','pn'),
        );
        return $actions;
    }
    
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_userwallets_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		$where = '';

		$user = pn_sfilter(pn_strip_input(is_param_get('user')));
        if($user){ 
            $where .= " AND user_login LIKE '%$user%'";
		}

		$accountnum = pn_sfilter(pn_strip_input(is_param_get('accountnum')));
        if($accountnum){ 
            $where .= " AND accountnum LIKE '%$accountnum%'";
		}

		$valut_id = intval(is_param_get('valut_id'));
        if($valut_id){ 
            $where .= " AND valut_id = '$valut_id'";
		}		

		$mod = pn_strip_input(is_param_get('mod'));
		if($mod == 1){
			$where .= " AND verify = '1'";
		} elseif($mod == 2){
			$where .= " AND verify = '0'";
		} 
		
		$where = pn_admin_search_where($where);
		
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."user_accounts WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."user_accounts WHERE id > 0 $where ORDER BY id DESC LIMIT $offset , $per_page");  		

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
            <a href="<?php echo admin_url('admin.php?page=pn_add_userwallets');?>" class="button"><?php _e('Add new','pn'); ?></a>
		</div>
		<?php
	}	  
	
}

add_action('premium_screen_pn_userwallets','my_premium_screen_pn_userwallets');
function my_premium_screen_pn_userwallets() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_userwallets_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_userwallets_List_Table')){
		new trev_userwallets_List_Table;
	}
}