<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_inex_tars', 'adminpage_title_inex_tars');
function adminpage_title_inex_tars(){
	_e('Rates','inex');
}

add_action('pn_adminpage_content_inex_tars','def_adminpage_content_inex_tars');
function def_adminpage_content_inex_tars(){

	if(class_exists('inex_tars_List_Table')){
		$Table = new inex_tars_List_Table();
		$Table->prepare_items();
		
		$search = array();
		$search[] = array(
			'view' => 'input',
			'title' => __('Payment system','inex'),
			'default' => pn_strip_input(is_param_get('sword')),
			'name' => 'sword',
		);		
		pn_admin_searchbox($search, 'reply');		
		
		$options = array(
			'1' => __('Active tariff','inex'),
			'2' => __('Not active tariff','inex'),
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


add_action('premium_action_inex_tars','def_myaction_post_inex_tars');
function def_myaction_post_inex_tars(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator', 'pn_investbox'));
		
	$reply = '';
	$action = get_admin_action();

	if(isset($_POST['id']) and is_array($_POST['id'])){				
				
		if($action=='true'){
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."inex_tars WHERE id='$id' AND status != '1'");
				if(isset($item->id)){		
					$wpdb->update($wpdb->prefix.'inex_tars', array('status'=>'1'), array('id'=>$id));
				}			
			}
		}
					
		if($action=='false'){
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."inex_tars WHERE id='$id' AND status != '0'");
				if(isset($item->id)){						
					$wpdb->update($wpdb->prefix.'inex_tars', array('status'=>'0'), array('id'=>$id));
				}			
			}
		}	

		if($action=='delete'){
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."inex_tars WHERE id='$id'");
				if(isset($item->id)){						
					$wpdb->query("DELETE FROM ".$wpdb->prefix."inex_tars WHERE id = '$id'");			
				}			
			}
		}
					
	} 
			
	$url = is_param_post('_wp_http_referer') . $reply;
	$paged = intval(is_param_post('paged'));
	if($paged > 1){ $url .= '&paged='.$paged; }	
	wp_redirect($url);
	exit;			
}  

class inex_tars_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'csyst'){
			
            return pn_strip_input($item->gtitle.' '.$item->gvalut);
			
		} elseif($column_name == 'cmin'){
			
			return is_my_money($item->minsum);
			
		} elseif($column_name == 'cmax'){
			
			if($item->maxsum > 0){
				return is_my_money($item->maxsum);
			} else {
				return __('No','inex');
			}
			
		// } elseif($column_name == 'cmaxtar'){
			
			// if($item->maxsumtar > 0){
				// return is_my_money($item->maxsumtar);
			// } else {
				// return __('No','inex');
			// }			
			
		} elseif($column_name == 'cpers'){
			
			return is_my_money($item->mpers) .'%';
			
		} elseif($column_name == 'cdays'){
			
			return is_my_money($item->cdays);	
			
		} elseif($column_name == 'cstatus'){
			
	        $st = intval($item->status);
		    if($st == 1){
				
				$status='<span class="bgreen">'. __('Active','inex') .'</span>';
				
		    } elseif($st == 0){
				
				$status='<b>'. __('Inactive','inex') .'</b>';
				
		    } 	
			return $status;
		} 
    }	
	
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'], 
            /*$2%s*/ $item->id                
        );
    }		
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'ctitle'    => __('Rate name','inex'),
			'csyst'    => __('PS','inex'),           
			'cmin'     => __('Minimum amount','inex'),
			'cmax'     => __('Maximum amount','inex'),
			'cpers'     => __('Percent','inex'),
			'cdays'    => __('Number of days','inex'),
			//'cmaxtar' => __('Maximum amount in tarif','inex'),
			'cstatus'  => __('Status','inex'),
        );
        return $columns;
    }	
	
    function column_ctitle($item){

        $actions = array(
            'edit'      => '<a href="'. admin_url('admin.php?page=inex_add_tars&item_id='. $item->id) .'">'. __('Edit','inex') .'</a>',
        );
        
        return sprintf('%1$s %2$s',
            /*$1%s*/ pn_strip_input(ctv_ml($item->title)),
            /*$2%s*/ $this->row_actions($actions)
        );
    }	
	 
    function get_bulk_actions() {
        $actions = array(
		    'true'    => __('Make active','inex'),
		    'false'    => __('Make inactive','inex'),
            'delete'    => __('Delete','inex'),
        );
        return $actions;
    }
    
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('inex_tars_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		
		$where = '';
		
        $mod = intval(is_param_get('mod'));
        if($mod==1){ 
            $where .= " AND status = '1'";
		} elseif($mod==2) {
			$where .= " AND status = '0'";	
		}  		
		
		$sword = pn_sfilter(pn_strip_input(is_param_get('sword')));
		if($sword){
		    $where .= " AND gtitle LIKE '%$sword%'";
		}		

		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."inex_tars WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."inex_tars WHERE id > 0 $where ORDER BY gtitle ASC, cdays DESC  LIMIT $offset , $per_page");  		

        $current_page = $this->get_pagenum();
        $this->items = $data;
		
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)  
        ) );
    }

	function extra_tablenav( $which ) {
    ?>
		<div class="alignleft actions">
            <a href="<?php echo admin_url('admin.php?page=inex_add_tars');?>" class="button"><?php _e('Add new','inex'); ?></a>
		</div>
		<?php
	}	
	
}


add_action('premium_screen_inex_tars','my_myscreen_inex_tars');
function my_myscreen_inex_tars() {
    $args = array(
        'label' => __('Display','inex'),
        'default' => 20,
        'option' => 'inex_tars_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('inex_tars_List_Table')){
		new inex_tars_List_Table;
	}
} 