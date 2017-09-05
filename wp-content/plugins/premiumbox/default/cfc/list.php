<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_pn_cfc', 'pn_admin_title_pn_cfc');
function pn_admin_title_pn_cfc(){
	_e('Custom fields','pn');
}

add_action('pn_adminpage_content_pn_cfc','def_pn_admin_content_pn_cfc');
function def_pn_admin_content_pn_cfc(){
	if(class_exists('trev_cfc_List_Table')){
		$Table = new trev_cfc_List_Table();
		$Table->prepare_items();
		
		$search = array();
		
		$valuts = apply_filters('list_valuts_manage', array(), __('All currency','pn'));
		$search[] = array(
			'view' => 'select',
			'title' => __('Currency','pn'),
			'default' => intval(is_param_get('valut_id')),
			'options' => $valuts,
			'name' => 'valut_id',
		);		
		pn_admin_searchbox($search, 'reply');
		
		$options = array(
			'1' => __('active custom fields','pn'),
			'2' => __('not active custom fields','pn'),
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

add_action('premium_action_pn_cfc','def_premium_action_pn_cfc');
function def_premium_action_pn_cfc(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_cfc'));	

	$reply = '';
	$action = get_admin_action();
					
		if(isset($_POST['id']) and is_array($_POST['id'])){				
				
			if($action=='active'){
				foreach($_POST['id'] as $id){
					$id = intval($id);
					$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."custom_fields_valut WHERE id='$id' AND status != '1'");
					if(isset($item->id)){
						do_action('pn_cfc_activate_before', $id);
						$result = $wpdb->update($wpdb->prefix.'custom_fields_valut', array('status'=>'1'), array('id'=>$id));
						if($result){
							do_action('pn_cfc_activate', $id);
						}
					}
				}
				$reply = '&reply=true';
			}	

			if($action=='notactive'){
				foreach($_POST['id'] as $id){
					$id = intval($id);
					$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."custom_fields_valut WHERE id='$id' AND status != '0'");
					if(isset($item->id)){		
						do_action('pn_cfc_notactivate_before', $id, $item);
						$result = $wpdb->update($wpdb->prefix.'custom_fields_valut', array('status'=>'0'), array('id'=>$id));
						if($result){
							do_action('pn_cfc_notactivate', $id, $item);
						}
					}
				}
				$reply = '&reply=true';
			}	

			if($action=='delete'){
				foreach($_POST['id'] as $id){
					$id = intval($id);
					$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."custom_fields_valut WHERE id='$id'");
					if(isset($item->id)){
						do_action('pn_cfc_delete_before', $id, $item);
						$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."custom_fields_valut WHERE id = '$id'");
						if($result){
							do_action('pn_cfc_delete', $id, $item);
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

class trev_cfc_List_Table extends WP_List_Table {

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
		} elseif($column_name == 'valuts'){
		    return get_vtitle($item->valut_id);
		} elseif($column_name == 'site_title'){
			return pn_strip_input(ctv_ml($item->cf_name));
		} elseif($column_name == 'uniqueid'){
			return pn_strip_input($item->uniqueid);			
		} elseif($column_name == 'status'){	
		    if($item->status == 0){ 
			    return '<span class="bred">'. __('inactive field','pn') .'</span>'; 
			} else { 
			    return '<span class="bgreen">'. __('active field','pn') .'</span>'; 
			}			
		} 
		return apply_filters('cfc_manage_ap_col', '', $column_name,$item);
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
            'edit'      => '<a href="'. admin_url('admin.php?page=pn_add_cfc&item_id='. $item->id) .'">'. __('Edit','pn') .'</a>',
        );
  		$primary = apply_filters('cfc_manage_ap_primary', pn_strip_input(ctv_ml($item->tech_name)), $item);
		$actions = apply_filters('cfc_manage_ap_actions', $actions, $item);        
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
			'valuts'    => __('Currency name','pn'),
			'minzn' => __('Min. number of symbols','pn'),
			'maxzn' => __('Max. number of symbols','pn'),
			'uniqueid' => __('Unique ID','pn'),
			'status'    => __('Status','pn'),
        );
		$columns = apply_filters('cfc_manage_ap_columns', $columns);
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
		
        $per_page = $this->get_items_per_page('trev_cfc_per_page', 20);
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

        $valut_id = intval(is_param_get('valut_id'));
        if($valut_id > 0){ 
            $where .= " AND valut_id='$valut_id'"; 
		}		
		
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."custom_fields_valut WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."custom_fields_valut WHERE id > 0 $where ORDER BY cf_order ASC LIMIT $offset , $per_page");  		

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
            <a href="<?php echo admin_url('admin.php?page=pn_add_cfc');?>" class="button"><?php _e('Add new','pn'); ?></a>
		</div>		
	<?php 
	}	  
	
}

add_action('premium_screen_pn_cfc','my_myscreen_pn_cfc');
function my_myscreen_pn_cfc() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_cfc_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_cfc_List_Table')){
		new trev_cfc_List_Table;
	}
}