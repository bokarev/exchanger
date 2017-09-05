<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_pn_reviews', 'pn_adminpage_title_pn_reviews');
function pn_adminpage_title_pn_reviews(){
	_e('Reviews','pn');
}

add_action('pn_adminpage_content_pn_reviews','def_pn_adminpage_content_pn_reviews');
function def_pn_adminpage_content_pn_reviews(){

	if(class_exists('trev_reviews_List_Table')){
		$Table = new trev_reviews_List_Table();
		$Table->prepare_items();
		
		pn_admin_searchbox(array(), 'reply');
		
		$options = array(
			'1' => __('published reviews','pn'),
			'2' => __('reviews moderating','pn'),
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


add_action('premium_action_pn_reviews','def_premium_action_pn_reviews');
function def_premium_action_pn_reviews(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_reviews'));
	
	$reply = '';
	$action = get_admin_action();
	if(isset($_POST['id']) and is_array($_POST['id'])){

		if($action == 'approve'){
					
			foreach($_POST['id'] as $id){
				$id = intval($id);
						
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."reviews WHERE id='$id' AND review_status != 'publish'");
				if(isset($item->id)){
					do_action('pn_reviews_approve_before', $id, $item);
					$result = $wpdb->query("UPDATE ".$wpdb->prefix."reviews SET review_status = 'publish' WHERE id = '$id'");
					if($result){
						do_action('pn_reviews_approve', $id, $item);
					}
				}
						
			}	
			$reply = '&reply=true';
					
		}

		if($action == 'unapprove'){
					
			foreach($_POST['id'] as $id){
				$id = intval($id);
						
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."reviews WHERE id='$id' AND review_status != 'moderation'");
				if(isset($item->id)){					
					do_action('pn_reviews_unapprove_before', $id, $item);	
					$result = $wpdb->query("UPDATE ".$wpdb->prefix."reviews SET review_status = 'moderation' WHERE id = '$id'");
					if($result){
						do_action('pn_reviews_unapprove', $id, $item);
					}
				}
			}	
			$reply = '&reply=true';
					
		}				
			
		if($action == 'delete'){
					
			foreach($_POST['id'] as $id){
				$id = intval($id);
						
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."reviews WHERE id='$id'");
				if(isset($item->id)){
					do_action('pn_reviews_delete_before', $id, $item);
					$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."reviews WHERE id = '$id'");
					if($result){
						do_action('pn_reviews_delete', $id, $item);
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

class trev_reviews_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'cuser'){
			
			$user_id = $item->user_id;
			$us = '';
			if($user_id > 0){
				$ui = get_userdata($user_id);
		        $us .='<a href="'. admin_url('user-edit.php?user_id='. $user_id) .'">';
				if(isset($ui->user_login)){
					$us .= is_user($ui->user_login); 
				}
		        $us .='</a>';
			} else {
				return pn_strip_input($item->user_name);
			}
			
		    return $us;	
			
		} elseif($column_name == 'cemail'){
			
		    return '<a href="mailto:'. is_email($item->user_email) .'">'. is_email($item->user_email) .'</a>';
			
		} elseif($column_name == 'csite'){
			
		    return '<a href="'. pn_strip_input($item->user_site) .'" target="_blank">'. pn_strip_input($item->user_site) .'</a>';
			
		} elseif($column_name == 'clang'){	
			
			return get_title_forkey($item->review_locale);
			
		} elseif($column_name == 'cstatus'){
			
		    if($item->review_status == 'moderation'){ 
			    return '<span class="bred">'. __('review is moderating','pn') .'</span>'; 
			} else { 
			    return '<span class="bgreen">'. __('published review','pn') .'</span>'; 
			}	
			
		}
		return apply_filters('reviews_manage_ap_col', '', $column_name,$item);
		
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
            'edit'      => '<a href="'. admin_url('admin.php?page=pn_add_reviews&item_id='. $item->id) .'">'. __('Edit','pn') .'</a>',
        );
		if($item->review_status == 'publish'){
		    $actions['view'] = '<a href="'. get_review_link($item->id, $item) .'" target="_blank">'. __('View','pn') .'</a>';
		}
		$primary = apply_filters('reviews_manage_ap_primary', get_mytime($item->review_date,'d.m.Y, H:i'), $item);
		$actions = apply_filters('reviews_manage_ap_actions', $actions, $item);		
        return sprintf('%1$s %2$s',
            $primary,
            $this->row_actions($actions)
        );
		
    }	
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',          
			'title'     => __('Publication date','pn'),
			'cuser'    => __('User','pn'),
			'cemail'    => __('User e-mail','pn'),
			'csite'  => __('Website','pn'),
			'clang'  => __('Language','pn'),
            'cstatus'  => __('Status','pn'),
        );
		$columns = apply_filters('reviews_manage_ap_columns', $columns);
        return $columns;
    }	
	
    function get_sortable_columns() {
        $sortable_columns = array( 
            'title'     => array('title',false),
        );
        return $sortable_columns;
    }	

	function single_row( $item ) {
		$class = '';
		if($item->review_status == 'publish'){
			$class = 'active';
		}
		echo '<tr class="pn_tr '. $class .'">';
			$this->single_row_columns( $item );
		echo '</tr>';
	}	
	
    function get_bulk_actions() {
        $actions = array(
			'approve'    => __('Approve','pn'),
			'unapprove'    => __('Decline','pn'),
            'delete'    => __('Delete','pn'),
        );
        return $actions;
    }
    
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_reviews_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		$oby = is_param_get('orderby');
		if($oby == 'title'){
		    $orderby = 'review_date';
		} else {
		    $orderby = 'id';
		}
		$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc';
		if($order != 'asc'){ $order = 'desc'; }		
		
		$where = '';

		$mod = intval(is_param_get('mod'));
		if($mod == 1){
			$where = " AND review_status = 'publish'";
		} elseif($mod == 2){
			$where = " AND review_status = 'moderation'";
		}
		
		$where = pn_admin_search_where($where);
		
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."reviews WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."reviews WHERE id > 0 $where ORDER BY $orderby $order LIMIT $offset , $per_page");  		

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
            <a href="<?php echo admin_url('admin.php?page=pn_add_reviews');?>" class="button"><?php _e('Add new','pn'); ?></a>
		</div>
		<?php
	}	  
	
}


add_action('premium_screen_pn_reviews','my_premium_screen_pn_reviews');
function my_premium_screen_pn_reviews() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_reviews_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_reviews_List_Table')){
		new trev_reviews_List_Table;
	}
}