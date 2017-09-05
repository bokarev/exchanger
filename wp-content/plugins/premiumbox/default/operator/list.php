<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_pn_operator_schedule', 'def_adminpage_title_pn_operator_schedule');
function def_adminpage_title_pn_operator_schedule(){
	_e('Schedules','pn');
}

add_action('pn_adminpage_content_pn_operator_schedule','def_pn_adminpage_content_pn_operator_schedule');
function def_pn_adminpage_content_pn_operator_schedule(){

	if(class_exists('trev_operator_schedule_List_Table')){
		$Table = new trev_operator_schedule_List_Table();
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


add_action('premium_action_pn_operator_schedule','def_premium_action_pn_operator_schedule');
function def_premium_action_pn_operator_schedule(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator'));
	
		$reply = '';
		$action = get_admin_action();
		if(isset($_POST['id']) and is_array($_POST['id'])){					
			if($action == 'delete'){
						
				foreach($_POST['id'] as $id){
					$id = intval($id);
							
					$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."operator_schedules WHERE id='$id'");
					if(isset($item->id)){
						do_action('pn_schedule_delete_before', $id, $item);
						$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."operator_schedules WHERE id = '$id'");
						if($result){
							do_action('pn_schedule_delete', $id, $item);
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

class trev_operator_schedule_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'ctime'){
			return $item->h1 .':'. $item->m1 .'-'. $item->h2 .':'. $item->m2;
		} elseif($column_name == 'cdays'){
			$days = array(
				'd1' => __('monday','pn'),
				'd2' => __('tuesday','pn'),
				'd3' => __('wednesday','pn'),
				'd4' => __('thursday','pn'),
				'd5' => __('friday','pn'),
				'd6' => '<span class="bred">'. __('saturday','pn') .'</span>',
				'd7' => '<span class="bred">'. __('sunday','pn') .'</span>',
			);
			$ndays = array();
			foreach($days as $k => $v){
				if(is_isset($item, $k) == 1){
					$ndays[] = $v;
				}
			}
		
			echo join(', ',$ndays);
		} 
		return apply_filters('schedule_manage_ap_col', '', $column_name,$item);
		
    }	
	
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'], 
            $item->id                
        );
    }	

    function column_title($item){
	global $premiumbox;
        $actions = array(
            'edit'      => '<a href="'. admin_url('admin.php?page=pn_operator_add_schedule&item_id='. $item->id) .'">'. __('Edit','pn') .'</a>',
        );
 		$primary = apply_filters('schedule_manage_ap_primary', pn_strip_text(ctv_ml($premiumbox->get_option('statuswork','text'. $item->status))), $item);
		$actions = apply_filters('schedule_manage_ap_actions', $actions, $item);       
        return sprintf('%1$s %2$s',
            $primary,
            $this->row_actions($actions)
        );
		
    }	
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
			'title'     => __('Status','pn'),
			'ctime'     => __('Work time','pn'),
			'cdays'     => __('Work days','pn'),
        );
		$columns = apply_filters('schedule_manage_ap_columns', $columns);
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
		
        $per_page = $this->get_items_per_page('trev_operator_schedule_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		
		$where = pn_admin_search_where('');
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."operator_schedules WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."operator_schedules WHERE id > 0 $where ORDER BY save_order ASC LIMIT $offset , $per_page");  		

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
            <a href="<?php echo admin_url('admin.php?page=pn_operator_add_schedule');?>" class="button"><?php _e('Add new','pn'); ?></a>
		</div>
		<?php
	}	  
	
}


add_action('premium_screen_pn_operator_schedule','my_premium_screen_pn_operator_schedule');
function my_premium_screen_pn_operator_schedule() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_operator_schedule_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_operator_schedule_List_Table')){
		new trev_operator_schedule_List_Table;
	}
}