<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_pn_vaccounts', 'def_admin_title_pn_vaccounts');
function def_admin_title_pn_vaccounts(){
	_e('Currency accounts','pn');
}

add_action('pn_adminpage_content_pn_vaccounts','def_admin_content_pn_vaccounts');
function def_admin_content_pn_vaccounts(){

	if(class_exists('trev_vaccounts_List_Table')){
		$Table = new trev_vaccounts_List_Table();
		$Table->prepare_items();
		
		$search = array();
		$search[] = array(
			'view' => 'input',
			'title' => __('Account','pn'),
			'default' => pn_strip_input(is_param_get('item')),
			'name' => 'item',
		);	
		$valuts = apply_filters('list_valuts_manage', array(), __('All currency','pn'));
		$search[] = array(
			'view' => 'select',
			'title' => __('Currency','pn'),
			'default' => intval(is_param_get('valut_id')),
			'name' => 'valut_id',
			'options' => $valuts,
		);				
		pn_admin_searchbox($search, 'reply');		
		
		$options = array(
			'1' => __('active accounts','pn'),
			'2' => __('inactive accounts','pn'),
		);
		pn_admin_submenu('mod', $options, 'reply');			
		
		pn_help(__('On shortcodes','pn'), 
		__('vid = "0" - show once randomly','pn') . '<br />' .
		__('vid = "1" - show always randomly','pn') . '<br />' .
		__('vid = "2" - show consistently within each order','pn') . '<br />' .
		__('hide = "0" - visible account number','pn') . '<br />' .
		__('hide = "1" - invisible (hide) account number','pn')
		); 
		?>
		<div class="premium_clear"></div>
		<?php	
	?>
	<form method="post" action="<?php pn_the_link_post(); ?>">
		<?php $Table->display() ?>
	</form>
	
	<div class="standart_shadow"></div>
	<div class="standart_window" id="window_to_comment">
		<div class="standart_windowins">
			<div class="standart_window_close"></div>
			<div class="standart_window_title" id="comment_the_title"><?php _e('Comment','pn'); ?></div>			
			<div class="standart_windowcontent">
				<form action="<?php pn_the_link_post('add_vaccounts_comment'); ?>" class="ajaxed_comment_form" method="post">
					<p><textarea id="comment_the_text" name="comment"></textarea></p>
					<p><input type="submit" name="submit" class="button-primary" value="<?php _e('Save','pn'); ?>" /></p>
					<input type="hidden" name="id" id="comment_the_id" value="" />	
				</form>
			</div>
		</div>
	</div>	
	
<script type="text/javascript">
jQuery(function($){	 
/* comment */
	$('.uthametka').click(function(){
		var id = $(this).attr('id').replace('uthame-','');
		$('#comment_the_id').val(id);
		$('.standart_shadow:first, #window_to_comment').show();
		$('#window_to_comment input[type=submit]').attr('disabled',true);
		$('#comment_the_text').val('');
		
		var param = 'id=' + id;
		
		$.ajax({
			type: "POST",
			url: "<?php pn_the_link_post('vaccounts_comment_get');?>",
			dataType: 'json',
			data: param,
			error: function(res, res2, res3){
				<?php do_action('pn_js_error_response', 'ajax'); ?>
			},			
			success: function(res)
			{		
			 
				$('#window_to_comment input[type=submit]').attr('disabled',false);
				if(res['status'] == 'error'){
					<?php do_action('pn_js_alert_response'); ?>
				} else {
					$('#comment_the_text').val(res['comment']);						
				}					
			}
		});		
		
	});
	
	$('.standart_window_close').click(function(){
		$('.standart_shadow, #window_to_comment').hide();
	});
	
	$('.ajaxed_comment_form').ajaxForm({
	    dataType:  'json',
        beforeSubmit: function(a,f,o) {
		    $('#window_to_comment input[type=submit]').attr('disabled',true);
        },
		error: function(res, res2, res3) {
			<?php do_action('pn_js_error_response', 'form'); ?>
		},		
        success: function(res) {
			$('#window_to_comment input[type=submit]').attr('disabled',false);
			if(res['status'] == 'error'){
				<?php do_action('pn_js_alert_response'); ?>
			} else {
				var id = $('#comment_the_id').val();
				var comf = $('#uthame-'+id);
				
				if(res['comment'] == 'true'){
					comf.addClass('active');
				} else {
					comf.removeClass('active');
				}
				$('.standart_shadow, #window_to_comment').hide();
			}
        }
    });	
/* end comment */

});
</script>
<?php 
	} else {
		echo 'Class not found';
	}
}

/* comments */
add_action('premium_action_vaccounts_comment_get', 'pn_premium_action_vaccounts_comment_get');
function pn_premium_action_vaccounts_comment_get(){
global $wpdb;
	only_post();

	$log = array();
	$log['status'] = '';
	$log['response'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = __('Error','pn');		

	if(current_user_can('administrator') or current_user_can('pn_vaccounts')){
		$id = intval(is_param_post('id'));
		$item = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."valuts_account WHERE id='$id'");
		$comment = pn_strip_text(is_isset($item,'text_comment'));
		$log['status'] = 'success';
		$log['comment'] = $comment;
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1; 
		$log['status_text'] = __('Authorisation Error','pn');
	}	
	
	echo json_encode($log);
	exit;
}

add_action('premium_action_add_vaccounts_comment', 'pn_premium_action_add_vaccounts_comment');
function pn_premium_action_add_vaccounts_comment(){
global $wpdb;
	only_post();

	$log = array();
	$log['status'] = '';
	$log['response'] = '';
	$log['status_code'] = 0; 
	$log['status_text'] = __('Error','pn');
	
	if(current_user_can('administrator') or current_user_can('pn_vaccounts')){
	
		$id = intval(is_param_post('id'));
		$text = pn_strip_input(is_param_post('comment'));
		
		$wpdb->update($wpdb->prefix.'valuts_account', array('text_comment'=>$text), array('id'=>$id));

		$log['status'] = 'success';
		if($text){
			$log['comment'] = 'true';
		} else {
			$log['comment'] = 'false';
		}
		
	} else {
		$log['status'] = 'error';
		$log['status_code'] = 1; 
		$log['status_text'] = __('Authorisation Error','pn');
	}	
	
	echo json_encode($log);	
	exit;
}
/* end comments */

add_action('premium_action_pn_vaccounts','def_premium_action_pn_vaccounts');
function def_premium_action_pn_vaccounts(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_vaccounts'));

	$reply = '';
	$action = get_admin_action();
			
	if(isset($_POST['id']) and is_array($_POST['id'])){				
				
		if($action=='active'){
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."valuts_account WHERE id='$id' AND status != '1'");
				if(isset($item->id)){
					do_action('pn_vaccounts_active_before', $id, $item);
					$result = $wpdb->update($wpdb->prefix.'valuts_account', array('status'=>'1'), array('id'=>$id));
					if($result){
						do_action('pn_vaccounts_active', $id, $item);
					}
				}
			}
			$reply = '&reply=true';
		}	

		if($action=='notactive'){
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."valuts_account WHERE id='$id' AND status != '0'");
				if(isset($item->id)){
					do_action('pn_vaccounts_notactive_before', $id, $item);
					$result = $wpdb->update($wpdb->prefix.'valuts_account', array('status'=>'0'), array('id'=>$id));
					if($result){
						do_action('pn_vaccounts_notactive', $id, $item);
					}
				}	
			}
			$reply = '&reply=true';
		}	

		if($action=='clearpr'){
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."valuts_account WHERE id='$id'");
				if(isset($item->id)){
					do_action('pn_vaccounts_clearpr_before', $id, $item);
					$result = $wpdb->update($wpdb->prefix.'valuts_account', array('count_visit'=>'0'), array('id'=>$id));
					if($result){
						do_action('pn_vaccounts_clearpr', $id, $item);
					}
				}
			}
			$reply = '&reply=true';
		}	

		if($action=='delete'){
			foreach($_POST['id'] as $id){
				$id = intval($id);
				$item = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."valuts_account WHERE id='$id'");
				if(isset($item->id)){
					do_action('pn_vaccounts_delete_before', $id, $item);
					$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."valuts_account WHERE id = '$id'");
					if($result){
						delete_vaccs_txtmeta($id);
						do_action('pn_vaccounts_delete', $id, $item);
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

class trev_vaccounts_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'idsnew'){
			$code = "[num_schet valuts='". $item->valut_id ."' vid='2' hide='0']";
			return '<input type="text" style="width: 100%;" name="" onclick="this.select()" value="'. $code .'" />';
		} elseif($column_name == 'valuts'){
		    return get_vtitle($item->valut_id);
		} elseif($column_name == 'title'){
			$accountnum_or = pn_strip_input(get_vaccs_txtmeta($item->id, 'accountnum'));
			$accountnum = $item->accountnum;
			
			if($accountnum != $accountnum_or and $accountnum_or){
				return '<span class="bred_dash">'. $accountnum .'</span> <span class="bgreen">'. $accountnum_or .'</span>';
			} else {
				return $accountnum;
			}
			
		} elseif($column_name == 'cv'){
			return intval($item->count_visit);	
		} elseif($column_name == 'mcv'){
			return intval($item->max_visit);			
		} elseif($column_name == 'inday'){
			return is_my_money($item->inday);
		} elseif($column_name == 'inmonth'){
			return is_my_money($item->inmonth);
		} elseif($column_name == 'sinday'){
			$time = current_time('timestamp');
			$date = date('Y-m-d 00:00:00',$time);
			return get_vaccount_sum($item->accountnum, 'in', $date);
		} elseif($column_name == 'sinmonth'){
			$time = current_time('timestamp');
			$date = date('Y-m-01 00:00:00',$time);			
			return get_vaccount_sum($item->accountnum, 'in', $date);	
		} elseif($column_name == 'comment'){
			$id = $item->id;
			$text_comment = trim($item->text_comment);
			if($text_comment){ $cl='active'; } else { $cl=''; }		
			return '<div class="uthametka question_div '. $cl .'" id="uthame-'. $id .'""></div>';			
		} elseif($column_name == 'status'){
	        $st = $item->status;
		    if($st == 0){
		        return '<span class="bred">'. __('inactive account','pn') .'</span>';
		    } else { 
		        return '<span class="bgreen">'. __('active account','pn') .'</span>';
		    }		
		} 	
		return apply_filters('vaccounts_manage_ap_col', '', $column_name,$item);
		
    }	
	
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'], 
            $item->id                
        );
    }	

    function column_cid($item){

        $actions = array(
            'edit'      => '<a href="'. admin_url('admin.php?page=pn_add_vaccounts&item_id='. $item->id) .'">'. __('Edit','pn') .'</a>',
        );
 		$primary = apply_filters('vaccounts_manage_ap_primary', $item->valut_id, $item);
		$actions = apply_filters('vaccounts_manage_ap_actions', $actions, $item);       
        return sprintf('%1$s %2$s',
            $primary,
            $this->row_actions($actions)
        );
		
    }	
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
			'cid'    => __('Shortcode ID','pn'),
			'idsnew'    => __('Shortcode','pn'),
			'valuts'    => __('Currency name','pn'),
		    'title'    => __('Account','pn'),
			'status'    => __('Status','pn'),
			'mcv'    => __('Hits limit','pn'),	
            'cv'    => __('Hits','pn'),	
			'inday' => __('Daily limit','pn'),
			'inmonth' => __('Monthly limit','pn'),
			'sinday' => __('Amount of exchanges (today)','pn'),
			'sinmonth' => __('Amount of exchanges (month)','pn'),
			'comment'     => __('Comment','pn'),
        );
		$columns = apply_filters('vaccounts_manage_ap_columns', $columns);
        return $columns;
    }	
	

    function get_bulk_actions() {
        $actions = array(
			'active'    => __('Activated','pn'),
			'notactive'    => __('Deactivate','pn'),
			'clearpr'    => __('Reset counter','pn'),
            'delete'    => __('Delete','pn'),
        );
        return $actions;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array( 
            'cid'     => array('cid',false),
			'cv'     => array('cv',false),
			'mcv'     => array('mcv',false),
			'inday'     => array('inday',false),
			'inmonth'     => array('inmonth',false),
        );
        return $sortable_columns;
    }	
	
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_vaccounts_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		
		$oby = is_param_get('orderby');
		if($oby == 'cid'){
		    $orderby = 'valut_id';
		} elseif($oby == 'mcv'){	
			$orderby = "(max_visit -0.0)";			
		} elseif($oby == 'cv'){	
			$orderby = "(count_visit -0.0)";
		} elseif($oby == 'inday'){	
			$orderby = "(inday -0.0)";
		} elseif($oby == 'inmonth'){	
			$orderby = "(inmonth -0.0)";			
		} else {
		    $orderby = 'id';
		}
		$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc';
		if($order != 'asc'){ $order = 'desc'; }		
		
		$where = '';
		
        $mod = intval(is_param_get('mod'));
        if($mod == 1){ 
            $where .= " AND status='1'"; 
		} elseif($mod == 2){
			$where .= " AND status='0'";
		}		
		
        $valut_id = intval(is_param_get('valut_id'));
        if($valut_id > 0){ 
            $where .= " AND valut_id='$valut_id'"; 
		}

        $accountnum = pn_sfilter(pn_strip_input(is_param_get('item')));
        if($accountnum){ 
            $where .= " AND accountnum LIKE '%$accountnum%'"; 
		}		
		
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."valuts_account WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."valuts_account WHERE id > 0 $where ORDER BY $orderby $order LIMIT $offset , $per_page");  		

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
            <a href="<?php echo admin_url('admin.php?page=pn_add_vaccounts');?>" class="button"><?php _e('Add new','pn'); ?></a>
			<a href="<?php echo admin_url('admin.php?page=pn_add_vaccounts_many');?>" class="button"><?php _e('Add list','pn'); ?></a>
		</div>		
	<?php 
	}	  
	
}


add_action('premium_screen_pn_vaccounts','my_myscreen_pn_vaccounts');
function my_myscreen_pn_vaccounts() {
    $args = array(
        'label' => __('Display','pn'),
        'default' => 20,
        'option' => 'trev_vaccounts_per_page'
    );
    add_screen_option('per_page', $args );
	if(class_exists('trev_vaccounts_List_Table')){
		new trev_vaccounts_List_Table;
	}
}