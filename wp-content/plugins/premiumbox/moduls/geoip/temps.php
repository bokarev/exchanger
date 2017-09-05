<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_pn_geoip_temp', 'pn_admin_title_pn_geoip_temp');
function pn_admin_title_pn_geoip_temp(){
	_e('Templates','pn');
}

add_action('pn_adminpage_content_pn_geoip_temp','def_pn_admin_content_pn_geoip_temp');
function def_pn_admin_content_pn_geoip_temp(){

	if(class_exists('trev_geoip_temp_List_Table')){
		$Table = new trev_geoip_temp_List_Table();
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

add_action('premium_action_pn_geoip_temp','def_premium_action_pn_geoip_temp');
function def_premium_action_pn_geoip_temp(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_geoip'));	
	
	$reply = '';
	$action = get_admin_action();
	if(isset($_POST['id']) and is_array($_POST['id'])){

		if($action == 'active'){
					
			foreach($_POST['id'] as $id){
				$id = intval($id);
						
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."geoip_template WHERE id='$id'");
				if(isset($item->id)){
					do_action('pn_geoip_template_active_before', $id, $item);
					$result = $wpdb->update($wpdb->prefix.'geoip_template', array('default_temp' => 0), array('default_temp' => 1));
					if($result){
						$wpdb->update($wpdb->prefix.'geoip_template', array('default_temp' => 1), array('id' => $id));
						do_action('pn_geoip_template_active', $id, $item);
					}
				}
			}
					
			$reply = '&reply=true';
					
		}				
			
		if($action == 'delete'){
					
			foreach($_POST['id'] as $id){
				$id = intval($id);
				
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."geoip_template WHERE id='$id'");
				if(isset($item->id)){
					do_action('pn_geoip_template_delete_before', $id, $item);
					$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."geoip_template WHERE id = '$id'");
					if($result){
						$wpdb->update($wpdb->prefix.'geoip_country', array('temp_id' => 0), array('temp_id' => $id));
						do_action('pn_geoip_template_delete', $id, $item);
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

class trev_geoip_temp_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'status'){
		    if($item->default_temp == 1){ 
			    return '<span class="bgreen">'. __('Default template','pn') .'</span>';
			} else {
			    return __('To select','pn');
			} 
		}
		
    }	
	
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'], 
            $item->id                
        );
    }	

    function column_ctitle($item){
        $actions = array(
            'edit'      => '<a href="'. admin_url('admin.php?page=pn_geoip_addtemp&item_id='. $item->id) .'">'. __('Edit','pn') .'</a>',
        );
        return sprintf('%1$s %2$s',
            /*$1%s*/ pn_strip_input($item->temptitle),
            /*$2%s*/ $this->row_actions($actions)
        );
    }	
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',          
			'ctitle'    => __('Template name','pn'),  
			'status'    => __('Template type','pn'),
        );
		
        return $columns;
    }	
	

    function get_bulk_actions() {
        $actions = array(
			'active'    => __('Default template','pn'),
            'delete'    => __('Delete','pn'),
        );
        return $actions;
    }
    
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_geoip_temp_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		$where = '';
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."geoip_template WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."geoip_template WHERE id > 0 $where ORDER BY id DESC LIMIT $offset , $per_page");  		

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
            <a href="<?php echo admin_url('admin.php?page=pn_geoip_addtemp');?>" class="button"><?php _e('Add new','pn'); ?></a>
		</div>
		<?php
	}	  
	
}

add_action('premium_screen_pn_geoip_temp','my_myscreen_pn_geoip_temp');
function my_myscreen_pn_geoip_temp() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_geoip_temp_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_geoip_temp_List_Table')){
		new trev_geoip_temp_List_Table;
	}
} 