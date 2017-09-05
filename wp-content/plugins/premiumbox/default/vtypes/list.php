<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_pn_vtypes', 'pn_admin_title_pn_vtypes');
function pn_admin_title_pn_vtypes(){
	_e('Currency codes','pn');
}

add_action('pn_adminpage_content_pn_vtypes','def_pn_admin_content_pn_vtypes');
function def_pn_admin_content_pn_vtypes(){

	if(class_exists('trev_vtypes_List_Table')){
		$Table = new trev_vtypes_List_Table();
		$Table->prepare_items();
		
		pn_admin_searchbox(array(), 'reply');
?>
	<form method="post" action="<?php pn_the_link_post(); ?>">
		<?php $Table->display() ?>
	</form>
	
	<style>
	.column-cid{ width: 80px!important; }
	.column-parser{ width: 200px!important; }
	</style>	
	<script type="text/javascript">
	$(function(){
		$('.vtype_parser').on('change', function(){
			var id = $(this).attr('id').replace('vtype_parser_','');
			var vale = $(this).val();
			if(vale > 0){
				$('#the_vtype_parser_'+id).show();
			} else {
				$('#the_vtype_parser_'+id).hide();
			}
		});		
	});
	</script>
<?php 
	} else {
		echo 'Class not found';
	}
}


add_action('premium_action_pn_vtypes','def_premium_action_pn_vtypes');
function def_premium_action_pn_vtypes(){
global $wpdb;
	
	only_post();
	pn_only_caps(array('administrator','pn_vtypes'));
	
	
	$reply = '';
	$action = get_admin_action();
	if(isset($_POST['save'])){
				
		if(isset($_POST['vncurs']) and is_array($_POST['vncurs'])){
			foreach($_POST['vncurs'] as $id => $vncurs){
				$id = intval($id);
				$vncurs = is_my_money($vncurs);
				if($vncurs <= 0){ $vncurs = 1; }
						
				$wpdb->query("UPDATE ".$wpdb->prefix."vtypes SET vncurs = '$vncurs' WHERE id = '$id'");
			}
		}
				
		if(isset($_POST['parser']) and is_array($_POST['parser'])){ /* parser */	
			foreach($_POST['parser'] as $id => $parser_id){		
				$id = intval($id);
				$parser = intval($parser_id);
				$elem = intval($_POST['elem'][$id]);
				$nums = pn_parser_num($_POST['nums'][$id]);							
					
				$array = array();
				if($parser > 0){
					$array['parser'] = $parser;
					$array['elem'] = $elem;
					$array['nums'] = $nums;			
				} else {
					$array['parser'] = 0;
					$array['elem'] = 0;
					$array['nums'] = 0;										
				}
					
				$wpdb->update($wpdb->prefix.'vtypes', $array, array('id'=>$id));		
			}		
		}	/* end parser */				
				
		do_action('pn_vtypes_save');
		$reply = '&reply=true';
		
	} else {
		if(isset($_POST['id']) and is_array($_POST['id'])){				
			if($action == 'delete'){
				foreach($_POST['id'] as $id){
					$id = intval($id);		
					$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."vtypes WHERE id='$id'");
					if(isset($item->id)){
						do_action('pn_vtypes_delete_before', $id, $item);
						$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."vtypes WHERE id = '$id'");
						if($result){
							do_action('pn_vtypes_delete', $id, $item);
						}
					}
				}		
				$reply = '&reply=true';
			}
		} 
	}
					
	$url = is_param_post('_wp_http_referer') . $reply;
	$paged = intval(is_param_post('paged'));
	if($paged > 1){ $url .= '&paged='.$paged; }	
	wp_redirect($url);
	exit;			
} 

class trev_vtypes_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'cid'){
			return $item->id;
		} elseif($column_name == 'reserve'){
			return get_summ_color(get_reserv_vtype($item->id));
		} elseif($column_name == 'od'){	
		    return '<input type="text" style="width: 100px;" name="vncurs['. $item->id .']" value="'. is_my_money($item->vncurs) .'" />';
		} elseif($column_name == 'parser'){	
			$en_parsers = array();
			if(function_exists('get_list_parsers')){
				$en_parsers = get_list_parsers();
			}
			$html = '
			<div style="width: 200px;">
			';
			$html = '
			<select name="parser['. $item->id .']" autocomplete="off" id="vtype_parser_'. $item->id .'" class="vtype_parser" style="width: 200px; display: block; margin: 0 0 10px;"> 
			';
				$enable = 0;
					$html .= '<option value="0" '. selected($item->parser,0,false) .'>-- '. __('No item','pn') .' --</option>';
					if(is_array($en_parsers)){
						foreach($en_parsers as $parser_key => $parser_data){
							if($item->parser == $parser_key){
								$enable = 1;
							}
							
							$html .= '<option value="'. $parser_key .'" '. selected($item->parser,$parser_key,false) .'>'. is_isset($parser_data,'title') .'</option>';
						}
					}
			$style = 'style="display: none;"';	
			if($enable == 1){
				$style = '';
			}
				$html .= '
					</select>
					<div id="the_vtype_parser_'. $item->id .'" '. $style .'>
						<input type="text" name="nums['. $item->id .']" style="width: 60px; float: left; margin: 2px 5px 0 0;" value="'. pn_strip_input($item->nums) .'" />
						<select name="elem['. $item->id .']" style="float: left;" autocomplete="off">	
							<option value="0" '. selected($item->elem,0,false) .'>S</option>
							<option value="1" '. selected($item->elem,1,false) .'>%</option>
						</select>				
							<div class="rclear"></div>
					</div>		
				';
				$html .= '</div>';
			return $html;			
		} 
		return apply_filters('vtypes_manage_ap_col', '', $column_name,$item);
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
            'edit'      => '<a href="'. admin_url('admin.php?page=pn_add_vtypes&item_id='. $item->id) .'">'. __('Edit','pn') .'</a>',
        );
  		$primary = apply_filters('psys_manage_ap_primary', is_site_value($item->vtype_title), $item);
		$actions = apply_filters('psys_manage_ap_actions', $actions, $item);        
        return sprintf('%1$s %2$s',
            $primary,
            $this->row_actions($actions)
        );
		
    }	
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
			'cid' => __('ID','pn'),
			'title'     => __('Currency code','pn'),
			'reserve'     => __('Reserve','pn'),
			'od'    => __('Internal rate','pn'). '(1 '. cur_type() .')',
			'parser' => __('Rate automatic adjustment','pn'),
        );
		$columns = apply_filters('vtypes_manage_ap_columns', $columns);
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
			'cid'     => array('cid',false),
            'title'     => array('title',false),
			'od'     => array('od',false),
        );
        return $sortable_columns;
    }	
	
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_vtypes_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		$oby = is_param_get('orderby');
		if($oby == 'title'){
		    $orderby = 'vtype_title';
		} elseif($oby == 'od'){
			$orderby = '(vncurs -0.0)';
		} else {
		    $orderby = 'id';
		}
		$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc';		
		if($order != 'asc'){ $order = 'desc'; }
		
		$where = '';
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."vtypes WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."vtypes WHERE id > 0 $where ORDER BY $orderby $order LIMIT $offset , $per_page");  		

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
            <a href="<?php echo admin_url('admin.php?page=pn_add_vtypes');?>" class="button"><?php _e('Add new','pn'); ?></a>
		</div>
		<?php
	}	  
}


add_action('premium_screen_pn_vtypes','my_myscreen_pn_vtypes');
function my_myscreen_pn_vtypes() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_vtypes_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_vtypes_List_Table')){
		new trev_vtypes_List_Table;
	}
}