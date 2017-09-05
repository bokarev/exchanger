<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_pn_masschange', 'pn_admin_title_pn_masschange');
function pn_admin_title_pn_masschange(){
	_e('Individual Central Bank rate','pn');
} 

add_action('pn_adminpage_content_pn_masschange','def_pn_admin_content_pn_masschange');
function def_pn_admin_content_pn_masschange(){

	if(class_exists('trev_masschange_List_Table')){
		$Table = new trev_masschange_List_Table();
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


add_action('premium_action_pn_masschange','def_premium_action_pn_masschange');
function def_premium_action_pn_masschange(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_masschange'));

	$reply = '';
	$action = get_admin_action();
			
	if(isset($_POST['save'])){
				
		if(isset($_POST['curs1']) and is_array($_POST['curs1']) and isset($_POST['curs2']) and is_array($_POST['curs2'])){
				
			foreach($_POST['curs1'] as $id => $curs1){
						
				$id = intval($id);
				$curs1 = is_my_money($curs1);
				$curs2 = is_my_money($_POST['curs2'][$id]);	
						
				$array = array();	
				$array['curs1'] = $curs1;
				$array['curs2'] = $curs2;
				$wpdb->update($wpdb->prefix."masschange", $array, array('id'=>$id));
						
				update_naps_to_masschange($id);
						
			}					
				
		}
				
		do_action('pn_masschange_save');
		$reply = '&reply=true';

	} else {
				
		if(isset($_POST['id']) and is_array($_POST['id'])){				
				
			if($action == 'delete'){
						
				foreach($_POST['id'] as $id){
					$id = intval($id);
							
					$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."masschange WHERE id='$id'");
					if(isset($item->id)){
						do_action('pn_masschange_delete_before', $id, $item);
						$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."masschange WHERE id = '$id'");
						if($result){
							$wpdb->update($wpdb->prefix."naps", array('masschange'=>'0'), array('masschange'=>$id));
							do_action('pn_masschange_delete', $id, $item);
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

class trev_masschange_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'course1'){		
		    return '<input type="text" style="width: 100px;" name="curs1['. $item->id .']" value="'. is_my_money($item->curs1) .'" />';	
		} elseif($column_name == 'course2'){	
		    return '<input type="text" style="width: 100px;" name="curs2['. $item->id .']" value="'. is_my_money($item->curs2) .'" />';			
		} 
		
		return apply_filters('masschange_manage_ap_col', '', $column_name, $item);
		
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
            'edit'      => '<a href="'. admin_url('admin.php?page=pn_add_masschange&item_id='. $item->id) .'">'. __('Edit','pn') .'</a>',
        );
        
        return sprintf('%1$s %2$s',
            pn_strip_input($item->title),
            $this->row_actions($actions)
        );
		
    }	
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
			'title'     => __('Rate name','pn'),
			'course1' => __('Exchange rate 1','pn'),
			'course2' => __('Exchange rate 2','pn'),
        );
		
		$columns = apply_filters('masschange_manage_ap_columns', $columns);
		
        return $columns;
    }	
	
    function get_sortable_columns() {
        $sortable_columns = array( 
			'title'     => array('title',false),
			'course1'     => array('course1',false),
			'course2'     => array('course2',false),
        );
        return $sortable_columns;
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

		$oby = is_param_get('orderby');
		if($oby == 'title'){
		    $orderby = 'title';
		} elseif($oby == 'course1'){
			$orderby = '(curs1 -0.0)';
		} elseif($oby == 'course2'){	
			$orderby = '(curs2 -0.0)';
		} else {
		    $orderby = 'id';
		}
		$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc';
		if($order != 'asc'){ $order = 'desc'; }
		
		
		$where = '';
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."masschange WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."masschange WHERE id > 0 $where ORDER BY $orderby $order LIMIT $offset , $per_page");  		

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
            <a href="<?php echo admin_url('admin.php?page=pn_add_masschange');?>" class="button"><?php _e('Add new','pn'); ?></a>
		</div>		
	<?php 
	}	  
	
}

add_action('premium_screen_pn_masschange','my_myscreen_pn_masschange');
function my_myscreen_pn_masschange() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_masschange_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_masschange_List_Table')){
		new trev_masschange_List_Table;
	}
}