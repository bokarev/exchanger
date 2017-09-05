<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_moduls', 'def_adminpage_title_pn_moduls');
function def_adminpage_title_pn_moduls($page){
	_e('Modules','pn');
} 

add_action('pn_adminpage_content_pn_moduls','def_pn_adminpage_content_pn_moduls');
function def_pn_adminpage_content_pn_moduls(){

	if(class_exists('trev_moduls_List_Table')){
		$Table = new trev_moduls_List_Table();
		$Table->prepare_items();
		
		$search = array();
		
		$list = pn_list_extended('moduls');
		$cats = array('0'=>'--'. __('All categories','pn') .'--');
		foreach($list as $data){
			$c = is_extension_name($data['cat']);
			$n = pn_strip_input(ctv_ml($data['category']));
			if($c and $n){
				$cats[$c] = $n;
			}
		}
		
		$search[] = array(
			'view' => 'select',
			'options' => $cats,
			'title' => __('Module categories','pn'),
			'default' => is_extension_name(is_param_get('cat')),
			'name' => 'cat',
		);		
		pn_admin_searchbox($search, 'reply');		
?>
	<style>
	.column-title{ width: 200px!important; }
	</style>

	<?php 
	$options = array(
		'1' => __('active moduls','pn'),
		'2' => __('inactive moduls','pn'),
	);
	pn_admin_submenu('mod', $options, 'reply'); 	
	?>	
	
	<form method="post" action="<?php pn_the_link_post(); ?>">
		<?php $Table->display(); ?>
	</form>
<?php 
	} else {
		echo 'Class not found';
	}
} 

add_action('premium_action_pn_moduls','def_premium_action_pn_moduls');
function def_premium_action_pn_moduls(){
global $wpdb, $premiumbox;	

	only_post();
	pn_only_caps(array('administrator'));

	$reply = '';
	$action = get_admin_action();
			
	$ref = is_param_post('_wp_http_referer');
	$ref = pn_admin_filter_data($ref, 'reply, paged');
	$paged = intval(is_param_post('paged'));
	if($paged > 1){ $ref .= '&paged='.$paged; }
	
	if(isset($_POST['id']) and is_array($_POST['id'])){
		if($action == 'active'){
						
			$pn_extended = get_option('pn_extended');
			if(!is_array($pn_extended)){ $pn_extended = array(); }
						
			foreach($_POST['id'] as $id){
				$id = is_extension_name($id);
				if($id and !isset($pn_extended['moduls'][$id])){
					$pn_extended['moduls'][$id] = $id;
					pn_include_extanded('moduls', $id);
					do_action('pn_moduls_active_'.$id);
					do_action('pn_moduls_active', $id);
				}	
			}
			update_option('pn_extended', $pn_extended);
			$premiumbox->plugin_create_pages();
						
			$reply = '&reply=true';		
		}
			
		if($action == 'deactive'){
						
			$pn_extended = get_option('pn_extended');
			if(!is_array($pn_extended)){ $pn_extended = array(); }
						
			foreach($_POST['id'] as $id){
				$id = is_extension_name($id);
				if($id and isset($pn_extended['moduls'][$id])){
					unset($pn_extended['moduls'][$id]);
					do_action('pn_moduls_deactive_'.$id);
					do_action('pn_moduls_deactive', $id);
				}	
			}
			update_option('pn_extended', $pn_extended);
						
			$reply = '&reply=true';		
		}				
	}
			
	$url = $ref . $reply;
	wp_redirect($url);
	exit;			
} 

add_action('premium_action_pn_moduls_activate','def_premium_action_pn_moduls_activate');
function def_premium_action_pn_moduls_activate(){
global $wpdb, $premiumbox;

	pn_only_caps(array('administrator'));	
	
	$id = is_extension_name(is_param_get('key'));	
	if($id){
		
		$pn_extended = get_option('pn_extended');
		if(!is_array($pn_extended)){ $pn_extended = array(); }
			
		if(!isset($pn_extended['moduls'][$id])){
			$pn_extended['moduls'][$id] = $id;
				
			pn_include_extanded('moduls', $id);
			do_action('pn_moduls_active_'.$id);
			do_action('pn_moduls_active', $id);
		}	

		update_option('pn_extended', $pn_extended);
		$premiumbox->plugin_create_pages();
	}
	
	$ref = is_param_get('_wp_http_referer');
	$url = pn_admin_filter_data($ref, 'reply').'reply=true';
	wp_redirect($url);
	exit;		
}

add_action('premium_action_pn_moduls_deactivate','def_premium_action_pn_moduls_deactivate');
function def_premium_action_pn_moduls_deactivate(){
global $wpdb;	

	pn_only_caps(array('administrator'));	
	
	$id = is_extension_name(is_param_get('key'));	
	if($id){
		
		$pn_extended = get_option('pn_extended');
		if(!is_array($pn_extended)){ $pn_extended = array(); }
			
		if(isset($pn_extended['moduls'][$id])){
			unset($pn_extended['moduls'][$id]);
			do_action('pn_moduls_deactive_'.$id);
			do_action('pn_moduls_deactive', $id);
		}	

		update_option('pn_extended', $pn_extended);
		
	}
	
	$ref = is_param_get('_wp_http_referer');
	$url = pn_admin_filter_data($ref, 'reply').'reply=true';
	wp_redirect($url);
	exit;
}	

class trev_moduls_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'descr'){
			
			$html = '
				<div>'. pn_strip_input(ctv_ml($item['description'])) .'</div>
				<div class="active second plugin-version-author-uri">'. __('Version','pn') .': '. pn_strip_input($item['version']) .'</div>
			';
			
			return $html;
		} elseif($column_name == 'category'){	
			return '<a href="'. admin_url('admin.php?page=pn_moduls&cat='. is_isset($item, 'cat')) .'">'. pn_strip_input(ctv_ml($item['category'])) . '</a>';
		}
		
    }	
	
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'], 
            $item['name']                
        );
    }	

    function column_title($item){

		if($item['status'] == 'active'){
			$actions['deactive']  = '<a href="'. pn_link_ajax('pn_moduls_deactivate') . '&key=' . $item['name'] . '&_wp_http_referer=' . urlencode($_SERVER['REQUEST_URI']) .'">'. __('Deactivate','pn') .'</a>';
		} else {
			$actions['active']  = '<a href="'. pn_link_ajax('pn_moduls_activate') . '&key=' . $item['name'] . '&_wp_http_referer=' . urlencode($_SERVER['REQUEST_URI']) .'">'. __('Activate','pn') .'</a>';
		}
        
        return sprintf('%1$s %2$s',
            '<strong>'.pn_strip_input(ctv_ml($item['title'])).'</strong>',
            $this->row_actions($actions)
        );
		
    }	
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
			'title'     => __('Title','pn'),
			'descr'     => __('Description','pn'),
			'category'     => __('Category','pn'),
        );
		
        return $columns;
    }	
	
	function get_primary_column_name() {
		return 'title';
	}

	function single_row( $item ) {
		$class = '';
		if($item['status'] == 'active'){
			$class = 'active';
		}
		echo '<tr class="pn_tr '. $class .'">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}		

    function get_bulk_actions() {
        $actions = array(
			'active'    => __('Activate','pn'),
			'deactive'    => __('Deactivate','pn'),
        );
        return $actions;
    }
    
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_moduls_per_page', 50);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();
		$offset = ($current_page-1)*$per_page;

		$list = pn_list_extended('moduls');
		$ndata = $ndata2 = array();
		$mod = intval(is_param_get('mod'));
		$cat = is_extension_name(is_param_get('cat'));
		
		if($mod == 1){
			foreach($list as $val){
				if($val['status'] == 'active'){
					$ndata2[] = $val;
				}
			}
		} elseif($mod == 2){
			foreach($list as $val){
				if($val['status'] == 'deactive'){
					$ndata2[] = $val;
				}
			}			
		} else {
			$ndata2 = $list;
		}
		
		if($cat){
			foreach($ndata2 as $val){
				$c = is_extension_name($val['cat']);
				if($c and $c == $cat){
					$ndata[] = $val;
				}
			}			
		} else {
			$ndata = $ndata2;
		}
		
		$data = $ndata;
		$items = array_slice($data,$offset,$per_page);
		
		$total_items = count($data);
        $current_page = $this->get_pagenum();
        $this->items = $items;
		
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)  
        ));
    }	
	
} 

add_action('premium_screen_pn_moduls','my_premium_screen_pn_moduls');
function my_premium_screen_pn_moduls() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 50,
        'option' => 'trev_moduls_per_page'
    );
    add_screen_option('per_page', $args );	
	
	if(class_exists('trev_moduls_List_Table')){
		new trev_moduls_List_Table;
	}
}