<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_pn_cf', 'pn_admin_title_pn_cf');
function pn_admin_title_pn_cf(){
	_e('Custom fields','pn');
}

add_action('pn_adminpage_content_pn_cf','def_pn_admin_content_pn_cf');
function def_pn_admin_content_pn_cf(){
	if(class_exists('trev_cf_List_Table')){
		$Table = new trev_cf_List_Table();
		$Table->prepare_items();
		
		pn_admin_searchbox(array(), 'reply');
		$options = array(
			'1' => __('active field','pn'),
			'2' => __('inactive field','pn'),
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


add_action('premium_action_pn_cf','def_premium_action_pn_cf');
function def_premium_action_pn_cf(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_cf'));
	
	
	$reply = '';
	$action = get_admin_action();
				
	if(isset($_POST['id']) and is_array($_POST['id'])){				
				
		if($action == 'active'){			
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."custom_fields WHERE id='$id' AND status != '1'");
				if(isset($item->id)){
					do_action('pn_cf_activate', $id, $item);
					$result = $wpdb->query("UPDATE ".$wpdb->prefix."custom_fields SET status = '1' WHERE id = '$id'");
					if($result){
						do_action('pn_cf_activate', $id, $item);
					}
				}	
			}
						
			$reply = '&reply=true';			
		}

		if($action == 'notactive'){		
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."custom_fields WHERE id='$id' AND status != '0'");
				if(isset($item->id)){
					do_action('pn_cf_notactivate_before', $id, $item);
					$result = $wpdb->query("UPDATE ".$wpdb->prefix."custom_fields SET status = '0' WHERE id = '$id'");
					if($result){
						do_action('pn_cf_notactivate', $id, $item);
					}
				}
			}
						
			$reply = '&reply=true';	
		}					
				
		if($action == 'delete'){		
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."custom_fields WHERE id='$id'");
				if(isset($item->id)){	
					do_action('pn_cf_delete_before', $id, $item);
					$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."custom_fields WHERE id = '$id'");
					if($result){
						do_action('pn_cf_delete', $id, $item);
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

class trev_cf_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'minzn'){
			return intval($item->minzn);
		} elseif($column_name == 'maxzn'){
			return intval($item->maxzn);
		} elseif($column_name == 'autofield'){
			$cf_auto = apply_filters('cf_auto_filed', array());
			return is_isset($cf_auto, $item->cf_auto);	
		} elseif($column_name == 'uniqueid'){
			return pn_strip_input($item->uniqueid);			
		} elseif($column_name == 'site_title'){
			return pn_strip_input(ctv_ml($item->cf_name));
		} elseif($column_name == 'status'){	
		    if($item->status == 0){ 
			    return '<span class="bred">'. __('inactive field','pn') .'</span>'; 
			} else { 
			    return '<span class="bgreen">'. __('active field','pn') .'</span>'; 
			}			
		} 
		return apply_filters('cf_manage_ap_col', '', $column_name,$item);
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
            'edit'      => '<a href="'. admin_url('admin.php?page=pn_add_cf&item_id='. $item->id) .'">'. __('Edit','pn') .'</a>',
        );
 		$primary = apply_filters('cf_manage_ap_primary', pn_strip_input(ctv_ml($item->tech_name)), $item);
		$actions = apply_filters('cf_manage_ap_actions', $actions, $item);        
        return sprintf('%1$s %2$s',
            $primary,
            $this->row_actions($actions)
        );
		
    }	
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
			'title'     => __('Custom field name (technical)','pn'),
			'site_title'     => __('Custom field name','pn'),
			'minzn' => __('Min. number of symbols','pn'),
			'maxzn' => __('Max. number of symbols','pn'),
			'autofield' => __('Automatic completion','pn'),
			'uniqueid' => __('Unique ID','pn'),
			'status'    => __('Status','pn'),
        );
		$columns = apply_filters('cf_manage_ap_columns', $columns);
        return $columns;
    }	
	
	function single_row( $item ) {
		$class = '';
		if($item->status == 1){
			$class = 'active';
		}
		echo '<tr class="pn_tr '. $class .'">';
			$this->single_row_columns( $item );
		echo '</tr>';
	}
	
    function get_bulk_actions() {
        $actions = array(
			'active'    => __('Activate','pn'),
			'notactive'    => __('Deactivate','pn'),
            'delete'    => __('Delete','pn'),
        );
        return $actions;
    }
    
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_cf_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		$where = '';
		
        $mod = intval(is_param_get('mod'));
        if($mod == 1){ 
            $where .= " AND status='1'"; 
		} elseif($mod == 2){
			$where .= " AND status='0'";
		}			
		
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."custom_fields WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."custom_fields WHERE id > 0 $where ORDER BY cf_order ASC LIMIT $offset , $per_page");  		

        $current_page = $this->get_pagenum();
        $this->items = $data;
		
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)  
        ));
    }
	
	function extra_tablenav( $which ){		  	
    ?>
		<div class="alignleft actions">
            <a href="<?php echo admin_url('admin.php?page=pn_add_cf');?>" class="button"><?php _e('Add new','pn'); ?></a>
		</div>		
	<?php 
	}	  
	
}


add_action('premium_screen_pn_cf','my_myscreen_pn_cf');
function my_myscreen_pn_cf() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_cf_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_cf_List_Table')){
		new trev_cf_List_Table;
	}
}