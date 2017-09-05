<?php
if( !defined( 'ABSPATH')){ exit(); }

/****************************** список ************************************************/
add_action('admin_menu', 'pn_adminpage_archive_bids');
function pn_adminpage_archive_bids(){
global $premiumbox;	
	
	if(current_user_can('administrator')){
		$hook = add_submenu_page('pn_bids', __('Archived orders','pn'), __('Archived orders','pn'), 'read', 'pn_archive_bids', array($premiumbox, 'admin_temp'));  
		add_action( "load-$hook", 'pn_trev_hook' );
	}
}

add_action('pn_adminpage_title_pn_archive_bids', 'pn_admin_title_pn_archive_bids');
function pn_admin_title_pn_archive_bids(){
	_e('Archived orders','pn');
}

add_action('pn_adminpage_content_pn_archive_bids','def_pn_admin_content_pn_archive_bids');
function def_pn_admin_content_pn_archive_bids(){
global $wpdb;

	$superaction = is_param_get('maction');
	if($superaction == 'edit' or $superaction == 'add'){ /* внутренняя страница */
		
		$data_id = 0;
		$data = '';
		$id = intval(is_param_get('item_id'));

		if($id){
			$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."archive_bids WHERE id='$id'");
			if(isset($data->id)){
				$data_id = $data->id;
			}	
		}		
		
		if($data_id){
			$archive_content = @unserialize($data->archive_content);
			?>
			<div class="premium_single">
				
				<div class="premium_single_line">
					<strong><?php _e('Archivation date','pn'); ?>:</strong> <?php echo get_mytime($data->archive_date,'d.m.Y H:i'); ?>
				</div>	
				
				<div class="premium_single_line">
					<strong><?php _e('Status','pn'); ?>:</strong> <?php echo get_bid_status($data->status); ?>
				</div>
				
				<?php 
				$title_arr = array(
					'createdate' => __('Creation date','pn'),
					'editdate' => __('Modification date','pn'),
					'naschet' => __('Merchant account','pn'),
					'soschet' => __('Account used for automatic payout','pn'),
					'trans_in' => __('Merchant transaction ID','pn'),
					'trans_out' => __('Auto payout transaction ID','pn'),
					'account1' => __('From account','pn'),
					'account2' => __('Into account','pn'),
					'last_name' => __('Last name','pn'),
					'first_name' => __('First name','pn'),
					'second_name' => __('Second name','pn'),
					'user_phone' => __('Phone','pn'),
					'user_skype' => __('Skype','pn'),
					'user_email' => __('E-mail','pn'),
					'user_passport' => __('Passport number','pn'),
					'user_id' => __('User ID','pn'),
					'user_ip' => __('User IP','pn'),
					'profit' => __('Profit','pn'),
					'exsum' => __('Amount in internal currency','pn'),
					'vtype1' => __('Currency code for Send','pn'),
					'summ1c' => __('Amount To send (add.fee and PS fee)','pn'),
					'summ1cr' => __('Amount Send for reserve','pn'),
					'vtype1' => __('Currency code for Receive','pn'),
					'summ2c' => __('Amount To receive (add.fees and PS fees)','pn'),
					'summ2cr' => __('Amount Receive for reserve','pn'),
					'ref_id' => __('Referral ID','pn'),
					'summp' => __('Partner earned','pn'),					
				);
				$en_key = array();
				foreach($title_arr as $k => $v){
					$en_key[] = $k;
				}
				if(is_array($archive_content)){
					foreach($archive_content as $key => $val){
						if(in_array($key, $en_key)){
							$title = is_isset($title_arr, $key);
						?>
						<div class="premium_single_line">
							<strong><?php echo $title; ?>:</strong> <?php echo $val; ?>
						</div>						
						<?php
						}
					}
				}
				?>
				
			</div> 
			<?php 	
		} else {
			_e('Error! Not found','pn');
		}  
		
	} else {	

 		if(class_exists('trev_archive_bids_List_Table')){
			$Table = new trev_archive_bids_List_Table();
			$Table->prepare_items();
			
			$search = array();
			$search[] = array(
				'view' => 'input',
				'title' => __('User ID','pn'),
				'default' => pn_strip_input(is_param_get('user_id')),
				'name' => 'user_id',
			);
			$search[] = array(
				'view' => 'input',
				'title' => __('Referral ID','pn'),
				'default' => pn_strip_input(is_param_get('ref_id')),
				'name' => 'ref_id',
			);
			$search[] = array(
				'view' => 'input',
				'title' => __('Order ID','pn'),
				'default' => pn_strip_input(is_param_get('bid_id')),
				'name' => 'bid_id',
			);
			$search[] = array(
				'view' => 'input',
				'title' => __('Account Send','pn'),
				'default' => pn_strip_input(is_param_get('account1')),
				'name' => 'account1',
			);
			$search[] = array(
				'view' => 'input',
				'title' => __('Account Receive','pn'),
				'default' => pn_strip_input(is_param_get('account2')),
				'name' => 'account2',
			);	
			$search[] = array(
				'view' => 'line',
			);
			$search[] = array(
				'view' => 'input',
				'title' => __('First name','pn'),
				'default' => pn_strip_input(is_param_get('first_name')),
				'name' => 'first_name',
			);
			$search[] = array(
				'view' => 'input',
				'title' => __('Last name','pn'),
				'default' => pn_strip_input(is_param_get('last_name')),
				'name' => 'last_name',
			);
			$search[] = array(
				'view' => 'input',
				'title' => __('Second name','pn'),
				'default' => pn_strip_input(is_param_get('second_name')),
				'name' => 'second_name',
			);	
			$search[] = array(
				'view' => 'line',
			);			
			$search[] = array(
				'view' => 'input',
				'title' => __('User phone','pn'),
				'default' => pn_strip_input(is_param_get('user_phone')),
				'name' => 'user_phone',
			);
			$search[] = array(
				'view' => 'input',
				'title' => __('User skype','pn'),
				'default' => pn_strip_input(is_param_get('user_skype')),
				'name' => 'user_skype',
			);
			$search[] = array(
				'view' => 'input',
				'title' => __('User email','pn'),
				'default' => pn_strip_input(is_param_get('user_email')),
				'name' => 'user_email',
			);
			$search[] = array(
				'view' => 'input',
				'title' => __('User passport','pn'),
				'default' => pn_strip_input(is_param_get('user_passport')),
				'name' => 'user_passport',
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
}
 
add_action('premium_action_pn_archive_bids','def_premium_action_pn_archive_bids');
function def_premium_action_pn_archive_bids(){
global $wpdb;
	
	only_post();
	
	$url = is_param_post('_wp_http_referer');
	$paged = intval(is_param_post('paged'));
	if($paged > 1){ $url .= '&paged='.$paged; }	
	wp_redirect($url);
	exit;			
}  
 
class trev_archive_bids_List_Table extends WP_List_Table {

    function __construct(){
        global $status, $page;
                
        parent::__construct( array(
            'singular'  => 'id',      
			'ajax' => false,  
        ) );
        
    }
	
    function column_default($item, $column_name){
        
		if($column_name == 'status'){
			return get_bid_status($item->status);
		} else {
			return pn_strip_input(is_isset($item,$column_name));
		} 
		
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
            'edit'      => '<a href="'. admin_url('admin.php?page=pn_archive_bids&maction=edit&item_id='. $item->id) .'">'. __('View order','pn') .'</a>',
        );
        
        return sprintf('%1$s %2$s',
            $item->bid_id,
            $this->row_actions($actions)
        );
    }		
	
    function get_columns(){
        
		$columns = array(       
			'title'     => __('ID','pn'),
			'user_id' => __('User ID','pn'),
			'ref_id' => __('Referral ID','pn'),
			'account1' => __('Account To send','pn'),
			'account2' => __('Account To receive','pn'),
			'user_phone' => __('Phone no.','pn'),
			'user_skype' => __('User skype','pn'),
			'user_email' => __('User e-mail','pn'),
			'user_passport' => __('User passport number','pn'),
			'status'  => __('Status','pn'),			
        );
		
        return $columns;
    }	
    
    function prepare_items() {
        global $wpdb; 
		
        $per_page = $this->get_items_per_page('trev_archive_bids_per_page', 20);
        $current_page = $this->get_pagenum();
        
        $this->_column_headers = $this->get_column_info();

		$offset = ($current_page-1)*$per_page;
		$where = '';

		$user_id = intval(is_param_get('user_id'));
		if($user_id){
		    $where .= " AND user_id = '$user_id'";
		}
		$ref_id = intval(is_param_get('ref_id'));
		if($ref_id){
		    $where .= " AND ref_id = '$ref_id'";
		}		
		$bid_id = intval(is_param_get('bid_id'));
		if($bid_id){
		    $where .= " AND bid_id = '$bid_id'";
		}		
		$account1 = pn_sfilter(pn_strip_input(is_param_get('account1')));
		if($account1){
		    $where .= " AND account1 LIKE '%$account1%'";
		}		
		$account2 = pn_sfilter(pn_strip_input(is_param_get('account2')));
		if($account2){
		    $where .= " AND account2 LIKE '%$account2%'";
		}
		$first_name = pn_sfilter(pn_strip_input(is_param_get('first_name')));
		if($first_name){
		    $where .= " AND first_name LIKE '%$first_name%'";
		}
		$last_name = pn_sfilter(pn_strip_input(is_param_get('last_name')));
		if($last_name){
		    $where .= " AND last_name LIKE '%$last_name%'";
		}
		$second_name = pn_sfilter(pn_strip_input(is_param_get('second_name')));
		if($second_name){
		    $where .= " AND second_name LIKE '%$second_name%'";
		}

		$user_phone = pn_sfilter(pn_strip_input(is_param_get('user_phone')));
		if($user_phone){
		    $where .= " AND user_phone LIKE '%$user_phone%'";
		}
		$user_skype = pn_sfilter(pn_strip_input(is_param_get('user_skype')));
		if($user_skype){
		    $where .= " AND user_skype LIKE '%$user_skype%'";
		}
		$user_email = pn_sfilter(pn_strip_input(is_param_get('user_email')));
		if($user_email){
		    $where .= " AND user_email LIKE '%$user_email%'";
		}
		$user_passport = pn_sfilter(pn_strip_input(is_param_get('user_passport')));
		if($user_passport){
		    $where .= " AND user_passport LIKE '%$user_passport%'";
		}		
		
		$where = pn_admin_search_where($where);
		$total_items = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."archive_bids WHERE id > 0 $where");
		$data = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."archive_bids WHERE id > 0 $where ORDER BY id DESC LIMIT $offset , $per_page");  		

        $current_page = $this->get_pagenum();
        $this->items = $data;
		
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  
            'per_page'    => $per_page,                     
            'total_pages' => ceil($total_items/$per_page)  
        ));
    }	  
	
} 

add_action('premium_screen_pn_archive_bids','my_myscreen_pn_archive_bids');
function my_myscreen_pn_archive_bids() {
	if(!isset($_GET['maction'])){
		$args = array(
			'label' => __('Display','pn'),
			'default' => 20,
			'option' => 'trev_archive_bids_per_page'
		);
		add_screen_option('per_page', $args );
		if(class_exists('trev_archive_bids_List_Table')){
			new trev_archive_bids_List_Table;
		}
	}
}