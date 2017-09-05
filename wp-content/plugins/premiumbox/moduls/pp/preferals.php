<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/
add_action('pn_adminpage_title_pn_preferals', 'pn_admin_title_pn_preferals');
function pn_admin_title_pn_preferals(){
	_e('Referrals','pn');
}

add_action('pn_adminpage_content_pn_preferals','def_pn_admin_content_pn_preferals');
function def_pn_admin_content_pn_preferals(){

	if(class_exists('trev_preferals_List_Table')){
		$Table = new trev_preferals_List_Table();
		$Table->prepare_items();
		
		$search = array();
		$search[] = array(
			'view' => 'input',
			'title' => __('User','pn'),
			'default' => pn_strip_input(is_param_get('user')),
			'name' => 'user',
		);		
		$search[] = array(
			'view' => 'input',
			'title' => __('Referral','pn'),
			'default' => pn_strip_input(is_param_get('suser')),
			'name' => 'suser',
		);	
		pn_admin_searchbox($search, 'reply');		
?>
	<form method="post" action="<?php pn_the_link_post(); ?>">
		<?php $Table->display() ?>
	</form>
<?php 
	} else {
		echo 'Class not found';
	}
}
 
add_action('premium_action_pn_preferals','def_premium_action_pn_preferals');
function def_premium_action_pn_preferals(){
global $wpdb;
	
	only_post();
	
	$url = is_param_post('_wp_http_referer');
	$paged = intval(is_param_post('paged'));
	if($paged > 1){ $url .= '&paged='.$paged; }	
	wp_redirect($url);
	exit;			
}  
 
class trev_preferals_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'cuser'){
			
			$user_id = $item->ID;
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
			
		} elseif($column_name == 'cref'){
	
			$user_id = $item->ref_id;
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
		return apply_filters('preferals_manage_ap_col', '', $column_name,$item);
		
    }	
	
    function get_columns(){
        $columns = array(          
			'cuser'    => __('User','pn'),
			'cref'    => __('Referral','pn'),
        );
		$columns = apply_filters('preferals_manage_ap_columns', $columns);
        return $columns;
    }	
	

    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_preferals_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;

		$where = '';
		$suser = is_user(is_param_get('suser'));
		if($suser){
			$suser_id = username_exists($suser);
			$where .= " AND ref_id='$suser_id'";
		}
		$user = is_user(is_param_get('user'));
		if($user){
			$user_id = username_exists($user);
			$where .= " AND ID='$user_id'";
		}		
		
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT ID FROM ". $wpdb->prefix ."users WHERE ref_id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."users WHERE ref_id > 0 $where ORDER BY ID DESC LIMIT $offset , $per_page");  		

        $current_page = $this->get_pagenum();
        $this->items = $data;
		
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)  
        ));
    }	  
	
}


add_action('premium_screen_pn_preferals','my_myscreen_pn_preferals');
function my_myscreen_pn_preferals() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_preferals_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_preferals_List_Table')){
		new trev_preferals_List_Table;
	}
} 