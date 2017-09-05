<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/
add_action('pn_adminpage_quicktags_page','adminpage_quicktags_page_partnpers');
function adminpage_quicktags_page_partnpers(){
?>
edButtons[edButtons.length] = 
new edButton('premium_partner_pers', '<?php _e('Royalties','pn'); ?>','[partner_pers]');
<?php	
}

function shortcode_partner_pers($atts,$content=""){ 
global $wpdb;

    $datas = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."partner_pers ORDER BY (sumec -0.0) ASC");
	$temp = '
	<div class="discont_div">
		<div class="discont_div_ins">
			<table>
				<tr>
					<th>'. __('Amount','pn') .'</th>
					<th>'. __('Royalties','pn') .'</th>
				</tr>';
				
				foreach($datas as $item){
					
					$temp .= '
					<tr>
						<td> > '. is_out_sum(is_my_money($item->sumec), 12, 'all') .'</td>
						<td>'. is_out_sum(is_my_money($item->pers), 12, 'all') .'%</td>
					</tr>
					';
					
				}
				
				$temp .= '
			</table>
		</div>	
	</div>
	';
	return $temp;
}
add_shortcode('partner_pers', 'shortcode_partner_pers');

add_action('pn_adminpage_title_pn_partnpers', 'pn_admin_title_pn_partnpers');
function pn_admin_title_pn_partnpers(){
	_e('Royalties','pn');
}

add_action('pn_adminpage_content_pn_partnpers','def_pn_admin_content_pn_partnpers');
function def_pn_admin_content_pn_partnpers(){

	if(class_exists('trev_partnpers_List_Table')){
		$Table = new trev_partnpers_List_Table();
		$Table->prepare_items();
		
		pn_admin_searchbox(array(), 'reply');	
?>
	<div class="premium_clear"></div>
	<form method="post" action="<?php pn_the_link_post(); ?>">
		<?php $Table->display() ?>
	</form>
<?php 
	} else {
		echo 'Class not found';
	}
}


add_action('premium_action_pn_partnpers','def_premium_action_pn_partnpers');
function def_premium_action_pn_partnpers(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_pp'));

	$reply = '';
	$action = get_admin_action();

	if(isset($_POST['id']) and is_array($_POST['id'])){				
				
		if($action == 'delete'){
						
			foreach($_POST['id'] as $id){
				$id = intval($id);
							
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."partner_pers WHERE id='$id'");
				if(isset($item->id)){							
					do_action('pn_partnpers_delete_before', $id, $item);
					$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."partner_pers WHERE id = '$id'");
					if($result){
						do_action('pn_partnpers_delete', $id, $item);
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

class trev_partnpers_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'pers'){
		    return is_my_money($item->pers) . '%';	
		} 
		return apply_filters('partnpers_manage_ap_col', '', $column_name,$item);
		
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
            'edit'      => '<a href="'. admin_url('admin.php?page=pn_add_partnpers&item_id='. $item->id) .'">'. __('Edit','pn') .'</a>',
        );
   		$primary = apply_filters('partnpers_manage_ap_primary',  '>' . is_my_money($item->sumec), $item);
		$actions = apply_filters('partnpers_manage_ap_actions', $actions, $item);       
        return sprintf('%1$s %2$s',
            $primary,
            $this->row_actions($actions)
        );
		
    }	
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',          
			'title'     => __('Amount more than','pn'),
			'pers'    => __('Percent','pn'),
        );
		$columns = apply_filters('partnpers_manage_ap_columns', $columns);
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
			'pers'     => array('pers',false),
            'title'     => array('title',false),
        );
        return $sortable_columns;
    }	
	
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_partnpers_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		$oby = is_param_get('orderby');
		if($oby == 'title'){
		    $orderby = '(sumec -0.0)';
		} else {
		    $orderby = '(pers -0.0)';
		}
		$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc';		
		if($order != 'asc'){ $order = 'desc'; }		

		$where = pn_admin_search_where('');
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."partner_pers WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."partner_pers WHERE id > 0 $where ORDER BY $orderby $order LIMIT $offset , $per_page");  		

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
            <a href="<?php echo admin_url('admin.php?page=pn_add_partnpers');?>" class="button"><?php _e('Add new','pn'); ?></a>
		</div>
		<?php
	}	  
	
}


add_action('premium_screen_pn_partnpers','my_myscreen_pn_partnpers');
function my_myscreen_pn_partnpers() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_partnpers_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_partnpers_List_Table')){
		new trev_partnpers_List_Table;
	}
}