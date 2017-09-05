<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_pn_blacklist', 'pn_admin_title_pn_blacklist');
function pn_admin_title_pn_blacklist(){
	_e('Blacklist','pn');
}

add_action('pn_adminpage_content_pn_blacklist','def_pn_admin_content_pn_blacklist');
function def_pn_admin_content_pn_blacklist(){

	if(class_exists('trev_blacklist_List_Table')){
		$Table = new trev_blacklist_List_Table();
		$Table->prepare_items();
		
		$search = array();
		$search[] = array(
			'view' => 'input',
			'title' => '',
			'default' => pn_strip_input(is_param_get('item')),
			'name' => 'item',
		);
		$options = array(
			'0' => __('everywhere','pn'),
			'1' => __('account','pn'),
			'2' => __('e-mail','pn'),
			'3' => __('phone number','pn'),
			'4' => __('skype','pn'),
			'5' => __('ip','pn'),
		);
		$search[] = array(
			'view' => 'select',
			'title' => '',
			'options' => $options,
			'default' => intval(is_param_get('witem')),
			'name' => 'witem',
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


add_action('premium_action_pn_blacklist','def_premium_action_pn_blacklist');
function def_premium_action_pn_blacklist(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_blacklist'));

	$reply = '';
	$action = get_admin_action();
	if(isset($_POST['id']) and is_array($_POST['id'])){			
			
		if($action == 'delete'){	
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."blacklist WHERE id='$id'");
				if(isset($item->id)){
					do_action('pn_blacklist_delete_before', $id, $item);
					$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."blacklist WHERE id = '$id'");
					if($result){
						do_action('pn_blacklist_delete', $id, $item);
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

class trev_blacklist_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'ctype'){
			$arr = array('0'=>__('invoice','pn'),'1'=>__('e-mail','pn'),'2'=>__('phone number','pn'),'3'=>__('skype','pn'),'4'=>__('ip','pn'));
			return is_isset($arr,$item->meta_key);	
		} 
		
		return apply_filters('blacklist_manage_ap_col', '', $column_name,$item);
		
    }	
	
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'], 
            $item->id                
        );
    }	

    function column_cvalue($item){

        $actions = array(
            'edit'      => '<a href="'. admin_url('admin.php?page=pn_add_blacklist&item_id='. $item->id) .'">'. __('Edit','pn') .'</a>',
        );
  		$primary = apply_filters('blacklist_manage_ap_primary', pn_strip_input($item->meta_value), $item);
		$actions = apply_filters('blacklist_manage_ap_actions', $actions, $item);         
        return sprintf('%1$s %2$s',
            $primary,
            $this->row_actions($actions)
        );
		
    }	
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',          
			'cvalue'     => __('Value','pn'),
			'ctype'    => __('Type','pn'),
        );
  		$columns = apply_filters('blacklist_manage_ap_columns', $columns);
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
		
        $per_page = $this->get_items_per_page('trev_blacklist_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		$where = '';

		$item = pn_sfilter(pn_strip_input(is_param_get('item')));
        if($item){ 
            $where .= " AND meta_value LIKE '%$item%'";
		}		
		
		$witem = intval(trim(is_param_get('witem')));
        if($witem){ 
			$witem = intval($witem);
			if($witem > 0){
				$witem = $witem - 1;
				$where .= " AND meta_key = '$witem'";
			}
		}		
		
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."blacklist WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."blacklist WHERE id > 0 $where ORDER BY id DESC LIMIT $offset , $per_page");  		

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
            <a href="<?php echo admin_url('admin.php?page=pn_add_blacklist');?>" class="button"><?php _e('Add new','pn'); ?></a>
			<a href="<?php echo admin_url('admin.php?page=pn_add_blacklist_many');?>" class="button"><?php _e('Add list','pn'); ?></a>
		</div>
		<?php
	}	  
	
}

add_action('premium_screen_pn_blacklist','my_myscreen_pn_blacklist');
function my_myscreen_pn_blacklist() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_blacklist_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_blacklist_List_Table')){
		new trev_blacklist_List_Table;
	}
}