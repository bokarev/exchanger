<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_pn_usfield', 'pn_admin_title_pn_usfield');
function pn_admin_title_pn_usfield(){
	_e('Verification fields','pn');
}

add_action('pn_adminpage_content_pn_usfield','def_pn_admin_content_pn_usfield');
function def_pn_admin_content_pn_usfield(){

	if(class_exists('trev_usfield_List_Table')){
		$Table = new trev_usfield_List_Table();
		$Table->prepare_items();
		
		$options = array(
			'1' => __('active fields','pn'),
			'2' => __('inactive fields','pn'),
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


add_action('premium_action_pn_usfield','def_premium_action_pn_usfield');
function def_premium_action_pn_usfield(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_userverify'));

	$reply = '';
	$action = get_admin_action();
			
	if(isset($_POST['id']) and is_array($_POST['id'])){				
				
		if($action=='active'){
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."uv_field WHERE id='$id' AND status != '1'");
				if(isset($item->id)){
					do_action('pn_usfield_activate_before', $id, $item);
					$result = $wpdb->update($wpdb->prefix.'uv_field', array('status'=>'1'), array('id'=>$id));
					if($result){
						do_action('pn_usfield_activate', $id, $item);
					}
				}
			}
			$reply = '&reply=true';
		}	

		if($action=='notactive'){
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."uv_field WHERE id='$id' AND status != '0'");
				if(isset($item->id)){		
					do_action('pn_usfield_notactivate_before', $id, $item);
					$result = $wpdb->update($wpdb->prefix.'uv_field', array('status'=>'0'), array('id'=>$id));
					if($result){
						do_action('pn_usfield_notactivate', $id, $item);
					}
				}
			}
			$reply = '&reply=true';
		}	

		if($action=='delete'){
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."uv_field WHERE id='$id'");
				if(isset($item->id)){
					do_action('pn_usfield_delete_before', $id, $item);
					$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."uv_field WHERE id = '$id'");
					if($result){
						do_action('pn_usfield_delete', $id, $item); 
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

class trev_usfield_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'type'){
			$types = array('0'=> __('Text input field','pn'), '1'=> __('File','pn'));
			return is_isset($types, $item->fieldvid);
		} elseif($column_name == 'lang'){
			if(intval($item->locale) == 0){
				return __('All','pn');
			} else {
				return get_title_forkey($item->locale);
			}
		} elseif($column_name == 'required'){	
		    if($item->uv_req == 0){ 
			    return '<span class="bred">'. __('No','pn') .'</span>'; 
			} else { 
			    return '<span class="bgreen">'. __('Yes','pn') .'</span>'; 
			}			
		} elseif($column_name == 'status'){	
		    if($item->status == 0){ 
			    return '<span class="bred">'. __('inactive field','pn') .'</span>'; 
			} else { 
			    return '<span class="bgreen">'. __('active field','pn') .'</span>'; 
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

    function column_title($item){

        $actions = array(
            'edit'      => '<a href="'. admin_url('admin.php?page=pn_add_usfield&item_id='. $item->id) .'">'. __('Edit','pn') .'</a>',
        );
        
        return sprintf('%1$s %2$s',
            pn_strip_input(ctv_ml($item->title)),
            $this->row_actions($actions)
        );
		
    }	
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
			'title'     => __('Custom field name','pn'),
			'type'    => __('Verification field type','pn'),
			'required' => __('Required field','pn'),
			'lang' => __('Language','pn'),
			'status'    => __('Status','pn'),
        );
		
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
		
        $per_page = $this->get_items_per_page('trev_usfield_per_page', 20);
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
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."uv_field WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."uv_field WHERE id > 0 $where ORDER BY id DESC LIMIT $offset , $per_page");  		

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
            <a href="<?php echo admin_url('admin.php?page=pn_add_usfield');?>" class="button"><?php _e('Add new','pn'); ?></a>
		</div>		
	<?php 
	}	  
	
}

add_action('premium_screen_pn_usfield','my_myscreen_pn_usfield');
function my_myscreen_pn_usfield() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_usfield_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_usfield_List_Table')){
		new trev_usfield_List_Table;
	}
}