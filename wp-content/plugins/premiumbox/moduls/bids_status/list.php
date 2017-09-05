<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_pn_bidstatus', 'pn_admin_title_pn_bidstatus');
function pn_admin_title_pn_bidstatus(){
	_e('Orders status','pn');
}

add_action('pn_adminpage_content_pn_bidstatus','def_pn_admin_content_pn_bidstatus');
function def_pn_admin_content_pn_bidstatus(){

	if(class_exists('trev_bidstatus_List_Table')){
		$Table = new trev_bidstatus_List_Table();
		$Table->prepare_items();
		
		pn_admin_searchbox(array(), 'reply');
?>
	<form method="post" action="<?php pn_the_link_post(); ?>">
		<?php $Table->display() ?>
	</form>
<?php 
	} else {
		echo 'Class not found';
	}
}


add_action('premium_action_pn_bidstatus','def_premium_action_pn_bidstatus');
function def_premium_action_pn_bidstatus(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_bidstatus'));
	
	$reply = '';
	$action = get_admin_action();
				
	if(isset($_POST['id']) and is_array($_POST['id'])){											
		if($action == 'delete'){			
			foreach($_POST['id'] as $id){
				$id = intval($id);	
				$cc = $wpdb->query("SELECT * FROM ". $wpdb->prefix ."bids WHERE status='my{$id}'");
				if($cc == 0){
					$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."bidstatus WHERE id='$id'");
					if(isset($item->id)){
						do_action('pn_bidstatus_delete_before', $id, $item);
						$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."bidstatus WHERE id = '$id'");
						if($result){
							do_action('pn_bidstatus_delete', $id, $item);
						}
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

class trev_bidstatus_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        global $wpdb;
		
		if($column_name == 'cap'){
			$status_id = $item->id;
			$cc = $wpdb->query("SELECT * FROM ". $wpdb->prefix ."bids WHERE status='my{$status_id}'");
			return $cc;
		} 
		return apply_filters('bidstatus_manage_ap_col', '', $column_name,$item);
		
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
            'edit'      => '<a href="'. admin_url('admin.php?page=pn_add_bidstatus&item_id='. $item->id) .'">'. __('Edit','pn') .'</a>',
        );
  		$primary = apply_filters('bidstatus_manage_ap_primary', pn_strip_input(ctv_ml($item->title)), $item);
		$actions = apply_filters('bidstatus_manage_ap_actions', $actions, $item);         
        return sprintf('%1$s %2$s',
            $primary,
            $this->row_actions($actions)
        );
		
    }	
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
			'title'     => __('Displayed name','pn'),
			'cap'     => __('Amount of orders','pn'),
        );
		$columns = apply_filters('bidstatus_manage_ap_columns', $columns);
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
		
        $per_page = $this->get_items_per_page('trev_bidstatus_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;

		$where = pn_admin_search_where('');
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."bidstatus WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."bidstatus WHERE id > 0 $where ORDER BY status_order ASC LIMIT $offset , $per_page");  		

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
            <a href="<?php echo admin_url('admin.php?page=pn_add_bidstatus');?>" class="button"><?php _e('Add new','pn'); ?></a>
		</div>		
	<?php 
	}	  
	
}


add_action('premium_screen_pn_bidstatus','my_myscreen_pn_bidstatus');
function my_myscreen_pn_bidstatus() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_bidstatus_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_bidstatus_List_Table')){
		new trev_bidstatus_List_Table;
	}
}