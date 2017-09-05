<?php
if( !defined( 'ABSPATH')){ exit(); }

/* 
Подключаем к меню
*/
add_action('admin_menu', 'admin_menu_theme_home');
function admin_menu_theme_home(){
global $premiumbox;

	add_submenu_page("pn_themeconfig", __('Homepage','pntheme'), __('Homepage','pntheme'), 'administrator', "pn_theme_home", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_theme_home', 'def_adminpage_title_pn_theme_home');
function def_adminpage_title_pn_theme_home($page){
	_e('Homepage','pntheme');
} 

/* настройки */
add_action('pn_adminpage_content_pn_theme_home','def_pn_adminpage_content_pn_theme_home');
function def_pn_adminpage_content_pn_theme_home(){
	
	$change = get_option('ho_change');
	
	$options = array();
	$options['top_title'] = array(
		'view' => 'h3',
		'title' => __('Welcome message','pntheme'),
		'submit' => __('Save','pntheme'),
		'colspan' => 2,
	);

	$options['wtitle'] = array(
		'view' => 'inputbig',
		'title' => __('Title', 'pntheme'),
		'default' => is_isset($change,'wtitle'),
		'name' => 'wtitle',
		'work' => 'input',
		'ml' => 1,
	);

	$options['wtext'] = array(
		'view' => 'editor',
		'title' => __('Text', 'pntheme'),
		'default' => is_isset($change,'wtext'),
		'name' => 'wtext',
		'work' => 'text',
		'rows' => 14,
		'media' => false,
		'ml' => 1,
	);		
	
	$options['center_title'] = array(
		'view' => 'h3',
		'title' => __('Information','pntheme'),
		'submit' => __('Save','pntheme'),
		'colspan' => 2,
	);	
	
	$options['ititle'] = array(
		'view' => 'inputbig',
		'title' => __('Title', 'pntheme'),
		'default' => is_isset($change,'ititle'),
		'name' => 'ititle',
		'work' => 'input',
		'ml' => 1,
	);	
	
	$options['itext'] = array(
		'view' => 'editor',
		'title' => __('Text', 'pntheme'),
		'default' => is_isset($change,'itext'),
		'name' => 'itext',
		'work' => 'text',
		'rows' => 14,
		'media' => false,
		'ml' => 1,
	);	
	
	$options['line1'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	
	$options['blocknews'] = array(
		'view' => 'select',
		'title' => __('News column','pntheme'),
		'options' => array('0'=>__('hide','pntheme'), '1'=>__('show','pntheme')),
		'default' => is_isset($change,'blocknews'),
		'name' => 'blocknews',
		'work' => 'int',
	);

	$categories = get_categories('hide_empty=0');
	$array = array();
	$array[0] = '--'.__('All','pntheme').'--';
	if(is_array($categories)){
		foreach($categories as $cat){
			$array[$cat->cat_ID] = ctv_ml($cat->name);
		}
	}	
	
	$options['catnews'] = array(
		'view' => 'select',
		'title' => __('Category','pntheme'),
		'options' => $array,
		'default' => is_isset($change,'catnews'),
		'name' => 'catnews',
		'work' => 'int',
	);	
	
	$options['line2'] = array(
		'view' => 'line',
		'colspan' => 2,
	);

	$options['blocreviews'] = array(
		'view' => 'select',
		'title' => __('Reviews column','pntheme'),
		'options' => array('0'=>__('hide','pntheme'), '1'=>__('show','pntheme')),
		'default' => is_isset($change,'blocreviews'),
		'name' => 'blocreviews',
		'work' => 'int',
	);

	$options['line3'] = array(
		'view' => 'line',
		'colspan' => 2,
	);

	$options['lastobmen'] = array(
		'view' => 'select',
		'title' => __('Last exchange','pntheme'),
		'options' => array('0'=>__('hide','pntheme'), '1'=>__('show','pntheme')),
		'default' => is_isset($change,'lastobmen'),
		'name' => 'lastobmen',
		'work' => 'int',
	);

	$options['line4'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	
	$options['hidecurr'] = array(
		'view' => 'user_func',
		'func_data' => array(),
		'func' => 'pn_theme_home_hidecurr',
		'work' => 'input_array',
	);	
	
	$options['bottom_title'] = array(
		'view' => 'h3',
		'title' => '',
		'submit' => __('Save','pntheme'),
		'colspan' => 2,
	);
	pn_admin_one_screen('', $options);	
} 

function pn_theme_home_hidecurr($data){
	$change = get_option('ho_change');
?>
	<tr>
		<th><?php _e('Hide currency reserve in widget','pntheme'); ?></th>
		<td>
			<div class="premium_wrap_standart">
				<div style="max-height: 200px; overflow-y: scroll;" class="cf_div">
					<?php
					$hidecurr = explode(',',is_isset($change,'hidecurr'));
					$valuts = array();
					if(function_exists('list_view_valuts')){
						$valuts = list_view_valuts();
					}
					if(is_array($valuts)){
						foreach($valuts as $item){
					?>
						<div><label><input type="checkbox" name="hidecurr[]" <?php if(in_array($item['id'], $hidecurr)){ ?>checked="checked"<?php } ?> value="<?php echo $item['id']; ?>"> <?php echo $item['title']; ?></label></div>
					<?php } 
					}
					?>
				</div>
			</div>
		</td>		
	</tr>				
<?php
}

/* обработка */
add_action('premium_action_pn_theme_home','def_premium_action_pn_theme_home');
function def_premium_action_pn_theme_home(){
global $wpdb;

	only_post();

	pn_only_caps(array('administrator'));	

	$options = array();
	$options['wtitle'] = array(
		'name' => 'wtitle',
		'work' => 'input',
		'ml' => 1,
	);
	$options['wtext'] = array(
		'name' => 'wtext',
		'work' => 'text',
		'ml' => 1,
	);		
	$options['ititle'] = array(
		'name' => 'ititle',
		'work' => 'input',
		'ml' => 1,
	);	
	$options['itext'] = array(
		'name' => 'itext',
		'work' => 'text',
		'ml' => 1,
	);	
	$options['blocknews'] = array(
		'name' => 'blocknews',
		'work' => 'int',
	);
	$options['catnews'] = array(
		'name' => 'catnews',
		'work' => 'int',
	);	
	$options['blocreviews'] = array(
		'name' => 'blocreviews',
		'work' => 'int',
	);
	$options['lastobmen'] = array(
		'name' => 'lastobmen',
		'work' => 'int',
	);
	$options['hidecurr'] = array(
		'name' => 'hidecurr',
		'work' => 'input_array',
	);	
	$data = pn_strip_options('', $options, 'post');
	
	$change = get_option('ho_change');
	if(!is_array($change)){ $change = array(); } 
					
	$change['blocknews'] = $data['blocknews'];
	$change['catnews'] = $data['catnews'];
	$change['blocreviews'] = $data['blocreviews'];	
			
	$change['lastobmen'] = $data['lastobmen'];
				
	$change['wtitle'] = $data['wtitle'];
	$change['ititle'] = $data['ititle'];
			
	$change['wtext'] = $data['wtext'];
	$change['itext'] = $data['itext'];
			
	$change['hidecurr'] = join(',',$data['hidecurr']);
					
	update_option('ho_change',$change);	
	
	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
	
	wp_safe_redirect($back_url);
	exit;
}