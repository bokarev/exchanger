<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/
add_action('pn_adminpage_title_pn_pexch', 'pn_admin_title_pn_pexch');
function pn_admin_title_pn_pexch(){
	_e('Partnership exchanges','pn');
}

add_action('pn_adminpage_content_pn_pexch','def_pn_admin_content_pn_pexch');
function def_pn_admin_content_pn_pexch(){

	if(class_exists('trev_pexch_List_Table')){
		$Table = new trev_pexch_List_Table();
		$Table->prepare_items();
		
		$search = array();
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
 
add_action('premium_action_pn_pexch','def_premium_action_pn_pexch');
function def_premium_action_pn_pexch(){
global $wpdb;
	
	only_post();
	
	$reply = '';
	if(isset($_POST['save'])){
		if(current_user_can('administrator') or current_user_can('pn_pp_bids')){		
			if(isset($_POST['summp']) and is_array($_POST['summp'])){
				foreach($_POST['summp'] as $id => $summp){
					$id = intval($id);
					$summp = is_my_money($summp);
					$wpdb->query("UPDATE ".$wpdb->prefix."bids SET summp = '$summp' WHERE id = '$id'");
				}
			}									
		}		
		do_action('pn_pexch_save');
		$reply = '&reply=true';
	}	
	
	$url = is_param_post('_wp_http_referer'). $reply;
	$paged = intval(is_param_post('paged'));
	if($paged > 1){ $url .= '&paged='.$paged; }	
	wp_redirect($url);
	exit;			
}  
 
class trev_pexch_List_Table extends WP_List_Table {

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
				$us = __('Guest','pn');
			}
			
		    return $us;
			
		} elseif($column_name == 'cbid'){
			return '<a href="'. admin_url('admin.php?page=pn_bids&bidid='. $item->id) .'" target="_blank">' . $item->id . '</a>';			
		} elseif($column_name == 'cdate'){
			return get_mytime($item->createdate,'d.m.Y, H:i');
		} elseif($column_name == 'cdata'){
			return is_my_money($item->summ1_dc) .'</span> '. pn_strip_input(ctv_ml($item->valut1)) .' '. is_site_value($item->vtype1) .'<br />'. is_my_money($item->summ2c) .'</span> '. pn_strip_input(ctv_ml($item->valut2)) .' '. is_site_value($item->vtype2); 
		} elseif($column_name == 'crefsum'){
			if(current_user_can('administrator') or current_user_can('pn_pp_bids')){
				return '<input type="text" style="width: 50px;" name="summp['. $item->id .']" value="'. is_my_money($item->summp) .'" /> ' . cur_type();
			} else {	
				return is_my_money($item->summp) .' '. cur_type();
			}
		} elseif($column_name == 'cpers'){
			return is_my_money($item->partpr) .'%';		
		} elseif($column_name == 'cprofit'){
			return is_my_money($item->profit) .' '. cur_type();
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
		return apply_filters('pexch_manage_ap_col', '', $column_name,$item);
		
    }	
	
    function get_columns(){
        $columns = array(          
			'cdate'    => __('Date','pn'),
			'cuser'    => __('User','pn'),
			'cbid' => __('ID Request','pn'),
			'cdata' => __('Exchange rate','pn'),
			'cprofit' => __('Profit','pn'),
			'crefsum' => __('Partner earned','pn'),
			'cpers' => __('Partner percent','pn'),
			'cref'    => __('Referral','pn'),
        );
		$columns = apply_filters('pexch_manage_ap_columns', $columns);
        return $columns;
    }	
	

    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_pexch_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;

		$where = '';
		$suser = is_user(pn_strip_input(is_param_get('suser'))); 
		if($suser){
			$suser_id = username_exists($suser);
			$where = " AND ref_id='$suser_id'";
		}
		
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."bids WHERE ref_id > 0 AND pcalc='1' AND status='success' $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."bids WHERE ref_id > 0 AND pcalc='1' AND status='success' $where ORDER BY id DESC LIMIT $offset , $per_page");  		

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
		</div>		
	<?php 
	}	
	
}

add_action('premium_screen_pn_pexch','my_myscreen_pn_pexch');
function my_myscreen_pn_pexch() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_pexch_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_pexch_List_Table')){
		new trev_pexch_List_Table;
	}
} 