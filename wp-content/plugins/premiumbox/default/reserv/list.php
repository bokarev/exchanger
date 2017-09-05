<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_reserv', 'pn_admin_title_pn_reserv');
function pn_admin_title_pn_reserv(){
	_e('Reserve adjustment','pn');
}

add_action('pn_adminpage_content_pn_reserv','def_pn_admin_content_pn_reserv');
function def_pn_admin_content_pn_reserv(){

	if(class_exists('trev_reserv_List_Table')){
		$Table = new trev_reserv_List_Table();
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
			'1' => __('expenditure','pn'),
			'2' => __('income','pn'),
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


add_action('premium_action_pn_reserv','def_premium_action_pn_reserv');
function def_premium_action_pn_reserv(){
global $wpdb, $user_ID;

	only_post();
	pn_only_caps(array('administrator','pn_reserv'));
	
	$reply = '';
	$action = get_admin_action();
					
		if(isset($_POST['id']) and is_array($_POST['id'])){				
				
			if($action == 'delete'){
						
				foreach($_POST['id'] as $id){
					$id = intval($id);
							
					$item = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."trans_reserv WHERE id='$id'");
					if(isset($item->id)){	
						do_action('pn_reserv_delete_before', $id, $item);
						$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."trans_reserv WHERE id = '$id'");
						if($result){
							do_action('pn_reserv_delete', $id, $item);
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

class trev_reserv_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'sum'){
			return get_summ_color($item->trans_summ);
		} elseif($column_name == 'create'){
			return get_mytime($item->trans_create,'d.m.Y H:i');	
		} elseif($column_name == 'edit'){
			return get_mytime($item->trans_edit,'d.m.Y H:i');		
		} elseif($column_name == 'valut'){
			return get_vtitle($item->valut_id);
		} elseif($column_name == 'creator'){
			
			$user_id = $item->user_creator;
			$us = '';
			if($user_id > 0){
				$ui = get_userdata($user_id);
				$us .='<a href="'. admin_url('user-edit.php?user_id='. $user_id) .'">';
				if(isset($ui->user_login)){
					$us .= is_user($ui->user_login); 
				}
				$us .='</a>';
			}
			
		    return $us;
			
		} elseif($column_name == 'editor'){
			
			$user_id = $item->user_editor;
			$us = '';
			if($user_id > 0){
				$ui = get_userdata($user_id);
		        $us .='<a href="'. admin_url('user-edit.php?user_id='. $user_id) .'">';
				if(isset($ui->user_login)){
					$us .= is_user($ui->user_login); 
				}
		        $us .='</a>';
			}
			
		    return $us;
			
		} 
		return apply_filters('reserv_manage_ap_col', '', $column_name,$item);
		
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
            'edit'      => '<a href="'. admin_url('admin.php?page=pn_add_reserv&item_id='. $item->id) .'">'. __('Edit','pn') .'</a>',
        );
 		$primary = apply_filters('reserv_manage_ap_primary', pn_strip_input($item->trans_title), $item);
		$actions = apply_filters('reserv_manage_ap_actions', $actions, $item);	       
        return sprintf('%1$s %2$s',
            $primary,
            $this->row_actions($actions)
        );
		
    }	
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
			'title'     => __('Comment','pn'),
			'valut' => __('Currency name','pn'),
			'sum' => __('Amount','pn'),
			'create' => __('Creation date','pn'),
			'creator' => __('Created by','pn'),
			'edit' => __('Edit date','pn'),
			'editor' => __('Edited by','pn'),
        );
		$columns = apply_filters('reserv_manage_ap_columns', $columns);
        return $columns;
    }	
	

    function get_bulk_actions() {
        $actions = array(
            'delete'    => __('Delete','pn'),
        );
        return $actions;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array( 
			'create'     => array('create',false),
			'edit'     => array('edit',false),
			'sum'     => array('sum',false),
        );
        return $sortable_columns;
    }	
	
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_reserv_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		$oby = is_param_get('orderby');
		if($oby == 'sum'){
		    $orderby = '(trans_summ -0.0)';
		} elseif($oby == 'create'){
			$orderby = 'trans_create';
		} elseif($oby == 'edit'){
			$orderby = 'trans_edit';			
		} else {
		    $orderby = 'id';
	    }
		$order = is_param_get('order');
		if($order != 'asc'){ $order = 'desc'; }			
		
		$where = '';
		
        $mod = intval(is_param_get('mod'));
        if($mod == 1){ 
            $where .= " AND trans_summ > 0"; 
		} elseif($mod == 2){
			$where .= " AND trans_summ <= 0";
		}		
		
        $valut_id = intval(is_param_get('valut_id'));
        if($valut_id > 0){ 
            $where .= " AND valut_id='$valut_id'"; 
		}		
		
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."trans_reserv WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."trans_reserv WHERE id > 0 $where ORDER BY $orderby $order LIMIT $offset , $per_page");  		

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
            <a href="<?php echo admin_url('admin.php?page=pn_add_reserv');?>" class="button"><?php _e('Add new','pn'); ?></a>
		</div>		
	<?php 
	}	
	
}

add_action('premium_screen_pn_reserv','my_myscreen_pn_reserv');
function my_myscreen_pn_reserv(){
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_reserv_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_reserv_List_Table')){
		new trev_reserv_List_Table;
	}
} 