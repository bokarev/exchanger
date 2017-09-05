<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_pn_merchantlogs', 'pn_admin_title_pn_merchantlogs');
function pn_admin_title_pn_merchantlogs(){
	_e('Merchant log','pn');
} 

add_action('pn_adminpage_content_pn_merchantlogs','def_pn_admin_content_pn_merchantlogs');
function def_pn_admin_content_pn_merchantlogs(){

	if(class_exists('trev_merchantlogs_List_Table')){
		$Table = new trev_merchantlogs_List_Table();
		$Table->prepare_items();
		
		global $wpdb;
		$options = array();
		$options[0] = '--'. __('All','pn') .'--';
		$items = $wpdb->get_results("SELECT DISTINCT(merchant) FROM ". $wpdb->prefix ."merchant_logs");  		
		foreach($items as $item){
			$options[$item->merchant] = is_extension_name($item->merchant);
		}
		
		$search = array();
		$search[] = array(
			'view' => 'select',
			'title' => __('Merchant','pn'),
			'default' => is_extension_name(is_param_get('merchant')),
			'options' => $options,
			'name' => 'merchant',
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

add_action('premium_action_pn_merchantlogs','def_premium_action_pn_merchantlogs');
function def_premium_action_pn_merchantlogs(){
global $wpdb;
	
	only_post();
	
	$url = is_param_post('_wp_http_referer');
	$paged = intval(is_param_post('paged'));
	if($paged > 1){ $url .= '&paged='.$paged; }	
	wp_redirect($url);
	exit;			
} 

class trev_merchantlogs_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'title'){
		    return get_mytime($item->createdate, 'd.m.Y H:i:s');
		} elseif($column_name == 'data'){
			return pn_strip_input($item->mdata);
		} elseif($column_name == 'merchant'){
			return is_extension_name($item->merchant);
		}
		
    }	
	
    function get_columns(){
        $columns = array(         
			'title'     => __('Date','pn'),
			'data'    => __('Data','pn'),
			'merchant'    => __('Merchant','pn'),
        );
		
        return $columns;
    }

    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_merchantlogs_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		
		$where = '';

        $merchant = is_extension_name(is_param_get('merchant'));
        if($merchant){ 
            $where .= " AND merchant = '$merchant'";
		}  		
		
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."merchant_logs WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."merchant_logs WHERE id > 0 $where ORDER BY id DESC LIMIT $offset , $per_page");  		

        $current_page = $this->get_pagenum();
        $this->items = $data;
		
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)  
        ));
    }	  
	
}


add_action('premium_screen_pn_merchantlogs','my_myscreen_pn_merchantlogs');
function my_myscreen_pn_merchantlogs() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_merchantlogs_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_merchantlogs_List_Table')){
		new trev_merchantlogs_List_Table;
	}
}