<?php 
if( !defined( 'ABSPATH')){ exit(); }

/* 
Подключаем к меню
*/
add_action('admin_menu', 'pn_adminpage_theme_mobile_home');
function pn_adminpage_theme_mobile_home(){
global $premiumbox;

	add_submenu_page("pn_themeconfig", __('Homepage (mobile version)','pntheme'), __('Homepage (mobile version)','pntheme'), 'administrator', "pn_theme_mobile_home", array($premiumbox, 'admin_temp'));
}

add_action('pn_adminpage_title_pn_theme_mobile_home', 'pn_adminpage_title_pn_theme_mobile_home');
function pn_adminpage_title_pn_theme_mobile_home($page){
	_e('Homepage (mobile version)','pntheme');
} 

/* настройки */
add_action('pn_adminpage_content_pn_theme_mobile_home','def_pn_adminpage_content_pn_theme_mobile_home');
function def_pn_adminpage_content_pn_theme_mobile_home(){
	
	$ho_change = get_option('mho_change');
	
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
		'default' => is_isset($ho_change,'wtitle'),
		'name' => 'wtitle',
		'work' => 'input',
		'ml' => 1,
	);

	$options['wtext'] = array(
		'view' => 'editor',
		'title' => __('Text', 'pntheme'),
		'default' => is_isset($ho_change,'wtext'),
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
		'default' => is_isset($ho_change,'ititle'),
		'name' => 'ititle',
		'work' => 'input',
		'ml' => 1,
	);	
	
	$options['itext'] = array(
		'view' => 'editor',
		'title' => __('Text', 'pntheme'),
		'default' => is_isset($ho_change,'itext'),
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

	$options['blocreviews'] = array(
		'view' => 'select',
		'title' => __('Reviews column','pntheme'),
		'options' => array('0'=>__('hide','pntheme'), '1'=>__('show','pntheme')),
		'default' => is_isset($ho_change,'blocreviews'),
		'name' => 'blocreviews',
		'work' => 'int',
	);

	$options['line2'] = array(
		'view' => 'line',
		'colspan' => 2,
	);	
	
	$options['partners'] = array(
		'view' => 'select',
		'title' => __('Partners','pntheme'),
		'options' => array('0'=>__('hide','pntheme'), '1'=>__('show','pntheme')),
		'default' => is_isset($ho_change,'partners'),
		'name' => 'partners',
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
		'default' => is_isset($ho_change,'lastobmen'),
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
		'func' => 'pn_theme_home_mobile_hidecurr',
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

function pn_theme_home_mobile_hidecurr($data){
	$ho_change = get_option('mho_change');
?>
	<tr>
		<th><?php _e('Hide currency reserve in widget','pntheme'); ?></th>
		<td>
			<div class="premium_wrap_standart">
				<div style="max-height: 200px; overflow-y: scroll;" class="cf_div">
					<?php
					$hidecurr = explode(',',is_isset($ho_change,'hidecurr'));
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
add_action('premium_action_pn_theme_mobile_home','def_premium_action_pn_theme_mobile_home');
function def_premium_action_pn_theme_mobile_home(){
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
	$options['blocreviews'] = array(
		'name' => 'blocreviews',
		'work' => 'int',
	);
	$options['partners'] = array(
		'name' => 'partners',
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
	
	$ho_change = get_option('mho_change');
	if(!is_array($ho_change)){ $ho_change = array(); } 
					
	$ho_change['blocreviews'] = $data['blocreviews'];	
	$ho_change['partners'] = $data['partners'];		
	$ho_change['lastobmen'] = $data['lastobmen'];
				
	$ho_change['wtitle'] = $data['wtitle'];
	$ho_change['ititle'] = $data['ititle'];
			
	$ho_change['wtext'] = $data['wtext'];
	$ho_change['itext'] = $data['itext'];
			
	$ho_change['hidecurr'] = join(',',$data['hidecurr']);
					
	update_option('mho_change',$ho_change);	
	
	$back_url = is_param_post('_wp_http_referer');
	$back_url .= '&reply=true';
	
	wp_safe_redirect($back_url);
	exit;
}