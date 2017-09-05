<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/

add_action('pn_adminpage_title_pn_usac', 'pn_admin_title_pn_usac');
function pn_admin_title_pn_usac(){
	_e('Account verification','pn');
}

add_action('pn_adminpage_content_pn_usac','def_pn_admin_content_pn_usac');
function def_pn_admin_content_pn_usac(){
global $wpdb;

 	if(class_exists('trev_usac_List_Table')){
		$Table = new trev_usac_List_Table();
		$Table->prepare_items();
			
		$search = array();
		$search[] = array(
			'view' => 'input',
			'title' => __('User login','pn'),
			'default' => is_user(is_param_get('ulogin')),
			'name' => 'ulogin',
		);
		$search[] = array(
			'view' => 'input',
			'title' => __('Account number','pn'),
			'default' => pn_strip_input(is_param_get('uaccount')),
			'name' => 'uaccount',
		);
		$search[] = array(
			'view' => 'input',
			'title' => __('IP','pn'),
			'default' => pn_strip_input(is_param_get('theip')),
			'name' => 'theip',
		);		
		
		$valuts = apply_filters('list_valuts_manage', array(), __('All currency','pn'));
		$search[] = array(
			'view' => 'select',
			'options' => $valuts,
			'title' => __('Currency','pn'),
			'default' => pn_strip_input(is_param_get('valut_id')),
			'name' => 'valut_id',
		);		
		pn_admin_searchbox($search, 'reply');			
			
		$options = array(
			'1' => __('pending request','pn'),
			'2' => __('verified request','pn'),
			'3' => __('unverified request','pn'),
		);
		pn_admin_submenu('mod', $options, 'reply');
	?>
		<form method="post" action="<?php pn_the_link_post(); ?>">
			<?php $Table->display() ?>
		</form>
		
<script type="text/javascript">
jQuery(function($){	
    $(document).on('click', '.js_usac_del', function(){
		var id = $(this).attr('data-id');
		var thet = $(this);
		if(!thet.hasClass('act')){
			if(confirm("<?php _e('Are you sure you want to delete the file?','pn'); ?>")){
				thet.addClass('act');
				var dataString='id=' + id;
				$.ajax({
				type: "POST",
				url: "<?php echo get_ajax_link('delete_accverify');?>",
				dataType: 'json',
				data: dataString,
				error: function(res, res2, res3){
					<?php do_action('pn_js_error_response', 'ajax'); ?>
				},			
				success: function(res)
				{
					if(res['status'] == 'success'){
						$('.accline_' + id).remove();
					} 
					if(res['status'] == 'error'){
						<?php do_action('pn_js_alert_response'); ?>
					}
					thet.removeClass('act');
				}
				});
			}
		}
        return false;
    });	
});
</script>		
	<?php 
	} else {
		echo 'Class not found';
	}  
}

add_action('premium_action_pn_usac','def_premium_action_pn_usac');
function def_premium_action_pn_usac(){
global $wpdb;

	only_post();
	pn_only_caps(array('administrator','pn_userverify'));	

	$reply = '';
	$action = get_admin_action();
					
	if(isset($_POST['id']) and is_array($_POST['id'])){			
				
		$mailtemp = get_option('mailtemp');
				
		if($action == 'true'){
						
			foreach($_POST['id'] as $id){
				$id = intval($id);
							
				$item = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."uv_accounts WHERE id = '$id' AND status != '1'");
				if(isset($item->id)){
								
					$account_id = $item->usac_id;
								
					$arr = array();
					$arr['status'] = 1;
					$wpdb->update($wpdb->prefix.'uv_accounts', $arr, array('id'=>$item->id));

					$arr = array();
					$arr['verify'] = 1;
					$wpdb->update($wpdb->prefix.'user_accounts', $arr, array('id'=>$account_id));								

					do_action('pn_user_accounts_verify', $account_id);
									
					$user_locale = pn_strip_input($item->locale);
					$purse = pn_strip_input($item->accountnum);
									
					if(isset($mailtemp['userverify3_u'])){								
						$data = $mailtemp['userverify3_u'];
						if($data['send'] == 1){
							$ot_mail = is_email($data['mail']);
							$ot_name = pn_strip_input($data['name']);
							
							$subject = pn_strip_input(ctv_ml($data['title'], $user_locale));
							$sitename = pn_strip_input(get_bloginfo('sitename'));
							$html = pn_strip_text(ctv_ml($data['text'], $user_locale));
							
							$to_mail = is_email($item->user_email);
							
							$subject = str_replace('[sitename]', $sitename ,$subject);
							$subject = str_replace('[user_login]', $item->user_login ,$subject);
							$subject = str_replace('[purse]', $purse ,$subject);
							$subject = apply_filters('mail_userverify3_u_subject',$subject);
								
							$html = str_replace('[sitename]', $sitename ,$html);
							$html = str_replace('[user_login]', $item->user_login ,$html);
							$html = str_replace('[purse]', $purse ,$html);
							$html = apply_filters('mail_userverify3_u_text',$html);
							$html = apply_filters('comment_text',$html);
																				
							pn_mail($to_mail, $subject, $html, $ot_name, $ot_mail); 	
						}			
					}								
							
				}
			}
						
			$reply = '&reply=true';
						
		}

		if($action == 'false'){
						
			foreach($_POST['id'] as $id){
				$id = intval($id);
							
				$item = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."uv_accounts WHERE id = '$id' AND status != '2'");
				if(isset($item->id)){

					$account_id = $item->usac_id;
								
					$arr = array();
					$arr['status'] = 2;
					$wpdb->update($wpdb->prefix.'uv_accounts', $arr, array('id'=>$item->id));

					$verify_request = $wpdb->query("SELECT * FROM ". $wpdb->prefix ."uv_accounts WHERE usac_id='$account_id' AND status='1'");
					if($verify_request == 0){
									
						$arr = array();
						$arr['verify'] = 0;
						$wpdb->update($wpdb->prefix.'user_accounts', $arr, array('id'=>$account_id));								

						do_action('pn_user_accounts_notverify', $account_id);							

						$user_locale = pn_strip_input($item->locale);
						$purse = pn_strip_input($item->accountnum);
									
						if(isset($mailtemp['userverify4_u'])){					
							$data = $mailtemp['userverify4_u'];
							if($data['send'] == 1){
								$ot_mail = is_email($data['mail']);
								$ot_name = pn_strip_input($data['name']);
							
								$subject = pn_strip_input(ctv_ml($data['title'], $user_locale));
								$sitename = pn_strip_input(get_bloginfo('sitename'));
								$html = pn_strip_text(ctv_ml($data['text'], $user_locale));
							
								$to_mail = is_email($item->user_email);
							
								$subject = str_replace('[sitename]', $sitename ,$subject);
								$subject = str_replace('[user_login]', $item->user_login ,$subject);
								$subject = str_replace('[purse]', $purse ,$subject);
								$subject = apply_filters('mail_userverify4_u_subject',$subject);
								
								$html = str_replace('[sitename]', $sitename ,$html);
								$html = str_replace('[user_login]', $item->user_login ,$html);
								$html = str_replace('[purse]', $purse ,$html);
								$html = apply_filters('mail_userverify4_u_text',$html);
								$html = apply_filters('comment_text',$html);
												
								pn_mail($to_mail, $subject, $html, $ot_name, $ot_mail); 	

							}			
						}								
							
					}
				}
			}
						
			$reply = '&reply=true';
						
		}				
				
		if($action == 'delete'){
						
			foreach($_POST['id'] as $id){
				$id = intval($id);
							
				$item = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."uv_accounts WHERE id = '$id'");
				if(isset($item->id)){
					do_action('pn_user_accounts_delete_before', $id, $item);
					$result = $wpdb->query("DELETE FROM ".$wpdb->prefix."uv_accounts WHERE id = '$id'");
					if($result){
						do_action('pn_user_accounts_delete', $id, $item);
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

class trev_usac_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ));
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'cnums'){
			
			return pn_strip_input($item->accountnum);
			
		} elseif($column_name == 'cip'){
			
			return pn_strip_input($item->theip);
			
		} elseif($column_name == 'cuser'){
			
		    $user_id = $item->user_id;
		    $us = '<a href="'. admin_url('user-edit.php?user_id='. $user_id) .'">'. is_user($item->user_login) . '</a>';
			
		    return $us;
			
		} elseif($column_name == 'cps'){ 	
			
			return get_vtitle($item->valut_id);
			
		} elseif($column_name == 'cfiles'){	
			
			return get_usac_files($item->usac_id);
			
		} elseif($column_name == 'cstatus'){
			
			if($item->status == 1){
				$status ='<span class="bgreen">'. __('Verified','pn') .'</span>';
			} elseif($item->status == 2){
				$status ='<span class="bred">'. __('Unverified','pn') .'</span>';
			} else {
				$status = '<b>'.  __('Pending verification','pn')  .'</b>';
			}
 	
			return $status;
		} 
		
		return apply_filters('manage_restrict_usac_column_col', '', $column_name,$item);
		
    }	
	
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'], 
            $item->id                
        );
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
	
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',          
			'cuser'     => __('User','pn'),
			'cip' => __('IP','pn'),
			'cps' => __('PS','pn'),
			'cnums' => __('Account number','pn'),
			'cfiles' => __('Files','pn'),
			'cstatus'  => __('Status','pn'),			
        );
		
		$columns = apply_filters('manage_restrict_usac_column', $columns);
		
        return $columns;
    }	
	

    function get_bulk_actions() {
        $actions = array(
			'true'    => __('Verify','pn'),
			'false'    => __('Unverify','pn'),
            'delete'    => __('Delete','pn'),
        );
        return $actions;
    }
    
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_usac_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		$where = '';

        $mod = intval(is_param_get('mod'));
        if($mod==1){ //в ожидании
            $where .= " AND status = '0'";
		} elseif($mod==2) { //верифицированные
			$where .= " AND status = '1'";
		} elseif($mod==3) { //не верифицированные
			$where .= " AND status = '2'";
		}  		

		$ulogin = is_user(is_param_get('ulogin'));
		if($ulogin){
		    $where .= " AND user_login LIKE '%$ulogin%'";
		}

		$theip = pn_sfilter(pn_strip_input(is_param_get('theip')));
		if($theip){
		    $where .= " AND theip LIKE '%$theip%'";
		}

		$uaccount = pn_sfilter(pn_strip_input(is_param_get('uaccount')));
		if($uaccount){
		    $where .= " AND accountnum LIKE '%$uaccount%'";
		}

		$valut_id = intval(is_param_get('valut_id'));
        if($valut_id){ 
            $where .= " AND valut_id = '$valut_id'";
		}		
		
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."uv_accounts WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."uv_accounts WHERE id > 0 $where ORDER BY id DESC LIMIT $offset , $per_page");  		

        $current_page = $this->get_pagenum();
        $this->items = $data;
		
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)  
        ));
    }	
	
}  

add_action('premium_screen_pn_usac','my_myscreen_pn_usac');
function my_myscreen_pn_usac() {
	$args = array(
		'label' => __('Display','pn'),
		'default' => 20,
		'option' => 'trev_usac_per_page'
	);
	add_screen_option('per_page', $args );
	if(class_exists('trev_usac_List_Table')){
		new trev_usac_List_Table;
	}
} 