<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Уведомление в шапке[:ru_RU][en_US:]Warning messages in header[:en_US]
description: [ru_RU:]Блок уведомления на красном фоне в шапке сайта[:ru_RU][en_US:]Warning messages column marked in red located in header[:en_US]
version: 1.0
category: [ru_RU:]Безопасность[:ru_RU][en_US:]Security[:en_US]
cat: secur
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

/* BD */
add_action('pn_moduls_active_'.$name, 'bd_pn_moduls_active_headmess');
function bd_pn_moduls_active_headmess(){
global $wpdb;
	
	$table_name= $wpdb->prefix ."head_mess";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`h1` varchar(5) NOT NULL default '0',
		`m1` varchar(5) NOT NULL default '0',
		`h2` varchar(5) NOT NULL default '0',
		`m2` varchar(5) NOT NULL default '0',		
		`d1` int(1) NOT NULL default '0',
		`d2` int(1) NOT NULL default '0',
		`d3` int(1) NOT NULL default '0',
		`d4` int(1) NOT NULL default '0',
		`d5` int(1) NOT NULL default '0',
		`d6` int(1) NOT NULL default '0',
		`d7` int(1) NOT NULL default '0',
		`op_status` int(5) NOT NULL default '-1',
        `url` longtext NOT NULL,
		`text` longtext NOT NULL,
		`status` int(1) NOT NULL default '0',
		`theclass` varchar(250) NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	
}
/* end BD */

add_filter('pn_caps','headmess_pn_caps');
function headmess_pn_caps($pn_caps){
	
	$pn_caps['pn_headmess'] = __('Warning messages','pn');
	
	return $pn_caps;
}

/* 
Подключаем к меню
*/
add_action('admin_menu', 'pn_adminpage_headmess');
function pn_adminpage_headmess(){
global $premiumbox;	
	if(current_user_can('administrator') or current_user_can('pn_headmess')){
		$hook = add_menu_page(__('Warning messages','pn'), __('Warning messages','pn'), 'read', 'pn_headmess', array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('icon'));  
		add_action( "load-$hook", 'pn_trev_hook' );
		add_submenu_page("pn_headmess", __('Add','pn'), __('Add','pn'), 'read', "pn_add_headmess", array($premiumbox, 'admin_temp'));	
	}
}

add_action('pn_header_theme','pn_header_theme_headmess');
function pn_header_theme_headmess(){
global $wpdb;
	$now_date = current_time('mysql');
	$mess = $wpdb->get_results("SELECT * FROM ". $wpdb->prefix ."head_mess WHERE status='1'");
	foreach($mess as $mes){
		$text = pn_strip_text(ctv_ml($mes->text));
		$url = pn_strip_input(ctv_ml($mes->url));
		$status = get_headmess_status($mes);
 		$closest = intval(get_mycookie('hmes'.$mes->id));
		if($closest != 1 and $status == 1){
			$cl = '';
			$theclass = pn_strip_input($mes->theclass);
			if($theclass){
				$cl = ' '.$theclass;
			}
	?>	
	<div class="wclosearea <?php echo $cl; ?> js_hmess" id="hmess_<?php echo $mes->id; ?>">
		<div class="wclosearea_ins">
			<div class="wclosearea_hide js_hmess_close"><div class="wclosearea_hide_ins"></div></div>
			<div class="wclosearea_text">
				<div class="wclosearea_text_ins">
					<?php if($url){ ?><a href="<?php echo $url; ?>"><?php } ?>
						<?php echo $text; ?>
					<?php if($url){ ?></a><?php } ?>
				</div>	
			</div>
		</div>
	</div>
	<?php } 
	} 
}  

add_action('siteplace_js','siteplace_js_headmess');
function siteplace_js_headmess(){	
?>	 
jQuery(function($){ 
    $('.js_hmess_close').on('click',function(){
		
		var thet = $(this);
		var id = $(this).parents('.js_hmess').attr('id').replace('hmess_','');
		thet.addClass('active');
		
		Cookies.set("hmes"+id, 1, { expires: 7, path: '/' });
		
		$('#hmess_' + id).hide();
		thet.removeClass('active');
 
        return false;
    });
});	
<?php	
}  

global $premiumbox;
$premiumbox->file_include($path.'/add');
$premiumbox->file_include($path.'/list'); 