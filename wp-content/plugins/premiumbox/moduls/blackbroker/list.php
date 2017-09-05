<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_pn_blackbroker', 'def_adminpage_title_pn_blackbroker');
function def_adminpage_title_pn_blackbroker(){
	_e('Auto Broker','pn');
} 

add_action('pn_adminpage_content_pn_blackbroker','def_adminpage_content_pn_blackbroker');
function def_adminpage_content_pn_blackbroker(){

	if(class_exists('trev_blackbroker_List_Table')){
		$Table = new trev_blackbroker_List_Table();
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


add_action('premium_action_pn_blackbroker','def_premium_action_pn_blackbroker');
function def_premium_action_pn_blackbroker(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_naps'));

	$reply = '';
	$action = get_admin_action();
			
	if(isset($_POST['save'])){
				
		if(isset($_POST['url']) and is_array($_POST['url'])){
			foreach($_POST['url'] as $id => $url){
				
				$id = intval($id);
				$url = pn_strip_input($url);
						
				$array = array();	
				$array['url'] = $url;
				$wpdb->update($wpdb->prefix."blackbrokers", $array, array('id'=>$id));	
				
			}						
		}
				
		do_action('pn_blackbrokers_save');
		$reply = '&reply=true';

	} else {
				
		if(isset($_POST['id']) and is_array($_POST['id'])){				
				
			if($action == 'delete'){
						
				foreach($_POST['id'] as $id){
					$id = intval($id);
							
					$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."blackbrokers WHERE id='$id'");
					if(isset($item->id)){
						do_action('pn_blackbrokers_delete_before', $id, $item);
						$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."blackbrokers WHERE id = '$id'");
						if($result){
							do_action('pn_blackbrokers_delete', $id, $item);
						}
					}		
				}
						
				$reply = '&reply=true';
			}
					
		} 
				
	}
			
	$url = is_param_post('_wp_http_referer') . $reply;
	$paged = intval(is_param_post('paged'));
	if($paged > 1){ $url .= '&paged='.$paged; }		
	wp_redirect($url);
	exit;			
}  

class trev_blackbroker_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
		if($column_name == 'url'){		
		    return '<input type="text" style="width: 300px;" name="url['. $item->id .']" value="'. pn_strip_input($item->url) .'" />';				
		} 
		return apply_filters('blackbrokers_manage_ap_col', '', $column_name, $item);
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
            'edit'      => '<a href="'. admin_url('admin.php?page=pn_add_blackbroker&item_id='. $item->id) .'">'. __('Edit','pn') .'</a>',
        );
  		$primary = apply_filters('blackbrokers_manage_ap_primary', pn_strip_input($item->title), $item);
		$actions = apply_filters('blackbrokers_manage_ap_actions', $actions, $item);       
        return sprintf('%1$s %2$s',
            $primary,
            $this->row_actions($actions)
        );
		
    }	
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
			'title'     => __('Website name','pn'),
			'url' => __('XML URL','pn'),
        );
		$columns = apply_filters('blackbrokers_manage_ap_columns', $columns);
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
		
        $per_page = $this->get_items_per_page('trev_masschange_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		
		$where = '';
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."blackbrokers WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."blackbrokers WHERE id > 0 $where ORDER BY id DESC LIMIT $offset , $per_page");  		

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
			<input type="submit" name="save" class="button" value="<?php _e('Save','pn'); ?>">
            <a href="<?php echo admin_url('admin.php?page=pn_add_blackbroker');?>" class="button"><?php _e('Add new','pn'); ?></a>
		</div>		
	<?php 
	}	  
	
}

add_action('premium_screen_pn_blackbroker','my_myscreen_pn_blackbroker');
function my_myscreen_pn_blackbroker(){
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_blackbroker_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_blackbroker_List_Table')){
		new trev_blackbroker_List_Table;
	}
}