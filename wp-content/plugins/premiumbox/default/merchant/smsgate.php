<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_smsgate', 'pn_admin_title_pn_smsgate');
function pn_admin_title_pn_smsgate($page){
	_e('SMS gate settings','pn');
} 

/* настройки */
add_action('pn_adminpage_content_pn_smsgate','def_pn_admin_content_pn_smsgate');
function def_pn_admin_content_pn_smsgate(){

	if(class_exists('trev_smsgate_List_Table')){
		$Table = new trev_smsgate_List_Table();
		$Table->prepare_items();
		
		$options = array(
			'1' => __('active SMS gate','pn'),
			'2' => __('inactive SMS gate','pn'),
		);
		pn_admin_submenu('mod', $options, 'reply');
?>
<style>
.column-title{ width: 200px!important; }
</style>

	<form method="post" action="<?php pn_the_link_post(); ?>">
		<?php $Table->display() ?>
	</form>
	
<script type="text/javascript">	
$(function(){

	$('select.merchant_change').change(function(){ 
		var id = $(this).attr('data-id');
		var wid = $(this).val();
		var thet = $(this);
		thet.attr('disabled',true);
		
		$('#premium_ajax').show();
		var dataString='id=' + id + '&wid=' + wid;
		
        $.ajax({
			type: "POST",
			url: "<?php pn_the_link_post('smsgate_settings_save'); ?>",
			data: dataString,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{
				$('#premium_ajax').hide();	
				thet.attr('disabled',false);
			}
        });
	
        return false;
	});	
	
});
</script>	
	
<?php 
	} else {
		echo 'Class not found';
	}
	
} 

add_action('premium_action_smsgate_settings_save', 'pn_premium_action_smsgate_settings_save');
function pn_premium_action_smsgate_settings_save(){
global $wpdb;

	only_post();
	
	if(current_user_can('administrator') or current_user_can('pn_merchants')){
		
		$id = is_extension_name(is_param_post('id'));
		$wid = intval(is_param_post('wid'));
		
		$merchants = get_option('smsgate');
		if(!is_array($merchants)){ $merchants = array(); }
		
		$merchants[$id] = $wid;
		
		$merchants = apply_filters('smsgate_settings_save', $merchants, $id, $wid);
		
		update_option('smsgate', $merchants);
			
	}  		
}
	
add_action('premium_action_pn_smsgate','def_premium_action_pn_smsgate');
function def_premium_action_pn_smsgate(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_merchants'));
	
	$reply = '';
	$action = get_admin_action();
	if(isset($_POST['id']) and is_array($_POST['id'])){

		if($action == 'active'){
					
			$pn_extended = get_option('pn_extended');
			if(!is_array($pn_extended)){ $pn_extended = array(); }
					
			foreach($_POST['id'] as $id){
				$id = is_extension_name($id);
				if($id and !isset($pn_extended['sms'][$id])){
					$pn_extended['sms'][$id] = $id;
					pn_include_extanded('sms', $id);
					do_action('pn_smsgate_active_'.$id);
				}	
			}
			update_option('pn_extended', $pn_extended);
					
			$reply = '&reply=true';	
		}

		if($action == 'deactive'){
					
			$pn_extended = get_option('pn_extended');
			if(!is_array($pn_extended)){ $pn_extended = array(); }
					
			foreach($_POST['id'] as $id){
				$id = is_extension_name($id);
				if($id and isset($pn_extended['sms'][$id])){
					unset($pn_extended['sms'][$id]);
					do_action('pn_smsgate_deactive_'.$id);
							
					$merchants = get_option('smsgate');
					if(!is_array($merchants)){ $merchants = array(); }
					$merchants[$id] = 0;
					update_option('smsgate', $merchants);							
							
				}	
			}
			update_option('pn_extended', $pn_extended);
					
			$reply = '&reply=true';	
		}				

	} 
			
	$url = is_param_post('_wp_http_referer') . $reply;
	$paged = intval(is_param_post('paged'));
	if($paged > 1){ $url .= '&paged='.$paged; }		
	wp_redirect($url);
	exit;			
} 

add_action('premium_action_pn_smsgate_activate','def_premium_action_pn_smsgate_activate');
function def_premium_action_pn_smsgate_activate(){
global $wpdb;	

	pn_only_caps(array('administrator','pn_merchants'));
	
	$id = is_extension_name(is_param_get('key'));	
	if($id){
		
		$pn_extended = get_option('pn_extended');
		if(!is_array($pn_extended)){ $pn_extended = array(); }
			
		if(!isset($pn_extended['sms'][$id])){
			$pn_extended['sms'][$id] = $id;
				
			pn_include_extanded('sms', $id);
			do_action('pn_smsgate_active_'.$id);
		}	

		update_option('pn_extended', $pn_extended);
	}
		
	$ref = is_param_get('_wp_http_referer');
	$url = pn_admin_filter_data($ref, 'reply').'reply=true';		
	
	wp_redirect($url);
	exit;		
}

add_action('premium_action_pn_smsgate_deactivate','def_premium_action_pn_smsgate_deactivate');
function def_premium_action_pn_smsgate_deactivate(){
global $wpdb;	

	pn_only_caps(array('administrator','pn_merchants'));
			
	$id = is_extension_name(is_param_get('key'));	
	if($id){
		
		$pn_extended = get_option('pn_extended');
		if(!is_array($pn_extended)){ $pn_extended = array(); }
			
		if(isset($pn_extended['sms'][$id])){
			unset($pn_extended['sms'][$id]);
			do_action('pn_smsgate_deactive_'.$id);
				
			$merchants = get_option('smsgate');
			if(!is_array($merchants)){ $merchants = array(); }
			$merchants[$id] = 0;
			update_option('smsgate', $merchants);				
		}	

		update_option('pn_extended', $pn_extended);
		
	}

	$ref = is_param_get('_wp_http_referer');
	$url = pn_admin_filter_data($ref, 'reply').'reply=true';		
	
	wp_redirect($url);
	exit;		
}	

class trev_smsgate_List_Table extends WP_List_Table { 

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
				<div class="active second plugin-version-author-uri">'. __('Version','pn') .': '. pn_strip_input($item['version']) .' '. apply_filters('smsgate_settingtext_'. $item['name'],'') .'</div>
			';
			
			return $html;
		} elseif($column_name == 'status'){
			if($item['status'] == 'active'){
			
				$default = is_enable_smsgate($item['name']);
			
				$html = '
				<select name="" data-id="'. $item['name'] .'" class="merchant_change" autocomplete="off">	
					<option value="0" '. selected($default,0,false) .'>'. __('Disable','pn') .'</option>
					<option value="1" '. selected($default,1,false) .'>'. __('Enable','pn') .'</option>
				</select>
				';
				
				return $html;
			}
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
			$actions['deactive']  = '<a href="'. pn_link_post('pn_smsgate_deactivate'). '&key='. $item['name'] .'&_wp_http_referer=' . urlencode($_SERVER['REQUEST_URI']) .'">'. __('Deactivate','pn') .'</a>';
		} else {
			$actions['active']  = '<a href="'. pn_link_post('pn_smsgate_activate'). '&key='. $item['name'] .'&_wp_http_referer=' . urlencode($_SERVER['REQUEST_URI']) .'">'. __('Activate','pn') .'</a>';
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
			'descr'     => __('SMS gate description','pn'),
			'status'     => __('Status','pn'),
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
		
        $per_page = $this->get_items_per_page('trev_smsgate_per_page', 50);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();
		$offset = ($current_page-1)*$per_page;

		$list = pn_list_extended('sms');
		$ndata = array();
		$mod = intval(is_param_get('mod'));
		
		if($mod == 1){
			foreach($list as $val){
				if($val['status'] == 'active'){
					$ndata[] = $val;
				}
			}
		} elseif($mod == 2){
			foreach($list as $val){
				if($val['status'] == 'deactive'){
					$ndata[] = $val;
				}
			}			
		} else {
			$ndata = $list;
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

add_action('premium_screen_pn_smsgate','my_myscreen_pn_smsgate');
function my_myscreen_pn_smsgate() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 50,
        'option' => 'trev_smsgate_per_page'
    );
    add_screen_option('per_page', $args );	
	if(class_exists('trev_smsgate_List_Table')){
		new trev_smsgate_List_Table;
	}
}