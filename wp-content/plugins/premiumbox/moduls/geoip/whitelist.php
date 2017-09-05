<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_pn_geoip_whitelist', 'pn_admin_title_pn_geoip_whitelist');
function pn_admin_title_pn_geoip_whitelist(){
	_e('White list','pn');
}

add_action('pn_adminpage_content_pn_geoip_whitelist','def_pn_admin_content_pn_geoip_whitelist');
function def_pn_admin_content_pn_geoip_whitelist(){

	if(class_exists('trev_geoip_whitelist_List_Table')){
		$Table = new trev_geoip_whitelist_List_Table();
		$Table->prepare_items();
		
		$search = array();
		$search[] = array(
			'view' => 'input',
			'title' => __('IP','pn'),
			'default' => pn_strip_input(is_param_get('item')),
			'name' => 'item',
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


add_action('premium_action_pn_geoip_whitelist','def_premium_action_pn_geoip_whitelist');
function def_premium_action_pn_geoip_whitelist(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_geoip'));

	$reply = '';
	$action = get_admin_action();
	if(isset($_POST['id']) and is_array($_POST['id'])){			
			
		if($action == 'delete'){
					
			foreach($_POST['id'] as $id){
				$id = intval($id);
						
				$wpdb->query("DELETE FROM ".$wpdb->prefix."geoip_whiteip WHERE id = '$id'");
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

class trev_geoip_whitelist_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'theip'){
		    return pn_strip_input($item->theip);			
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
			'theip'     => __('IP','pn'),
        );
		
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
		
        $per_page = $this->get_items_per_page('trev_geoip_whitelist_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		$where = '';

		$item = pn_sfilter(pn_strip_input(is_param_get('item')));
        if($item){ 
            $where .= " AND theip LIKE '%$item%'";
		}				
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."geoip_whiteip WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."geoip_whiteip WHERE id > 0 $where ORDER BY id DESC LIMIT $offset , $per_page");  		

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
            <a href="<?php echo admin_url('admin.php?page=pn_geoip_addwhitelist');?>" class="button"><?php _e('Add new','pn'); ?></a>
		</div>
		<?php
	}	  
	
} 

add_action('premium_screen_pn_geoip_whitelist','my_myscreen_pn_geoip_whitelist');
function my_myscreen_pn_geoip_whitelist() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_geoip_whitelist_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_geoip_whitelist_List_Table')){
		new trev_geoip_whitelist_List_Table;
	}
}