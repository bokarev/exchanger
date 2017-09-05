<?php 
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Webmoney x19[:ru_RU][en_US:]Webmoney x19[:en_US]
description: [ru_RU:]Webmoney x19[:ru_RU][en_US:]Webmoney x19[:en_US]
version: 1.0
category: [ru_RU:]Направления обменов[:ru_RU][en_US:]Exchange directions[:en_US]
cat: naps
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

global $premiumbox;
$premiumbox->file_include($path.'/webmoney/info'); /* deprecated */
$premiumbox->file_include($path.'/webmoney/index');
$premiumbox->file_include($path.'/classed/wmxicore.class');	
$premiumbox->file_include($path.'/classed/wmxi.class');
$premiumbox->file_include($path.'/classed/wmxilogin.class');
$premiumbox->file_include($path.'/classed/wmxiresult.class');
$premiumbox->file_include($path.'/classed/wmxilogger.class');
$premiumbox->file_include($path.'/classed/wminterfaces.class');
$premiumbox->file_include($path.'/classed/wmsigner.class');
	
if(!function_exists('WMXI_X19')){
	function & WMXI_X19() {
		static $oObject = null;
		if( is_null( $oObject ) ) {
			if(defined('WMX19_KEEPER_TYPE')){
				$oObject = new WMXI( PN_PLUGIN_DIR.'moduls/x19/classed/wmxi.crt', 'UTF-8' );
				if( WMX19_KEEPER_TYPE == 'CLASSIC' ){
					if(defined('WMX19_ID') and defined('WMX19_CLASSIC_KEYPASS') and defined('WMX19_CLASSIC_KEYPATH')){
						$oObject->Classic( WMX19_ID, array( 'pass' => WMX19_CLASSIC_KEYPASS, 'file' => WMX19_CLASSIC_KEYPATH ) );
					}
				} else {
					if(defined('WMX19_LIGHT_KEYPATH') and defined('WMX19_LIGHT_CERTPATH') and defined('WMX19_LIGHT_KEYPASS')){
						$oObject->Light( array( 'key' => WMX19_LIGHT_KEYPATH, 'cer' => WMX19_LIGHT_CERTPATH, 'pass' => WMX19_LIGHT_KEYPASS ) );
					}
				}
			}
		}
		return $oObject;
	}
} 

add_action('admin_menu', 'admin_menu_x19');
function admin_menu_x19(){
global $premiumbox;
	add_submenu_page("pn_moduls", __('X19','pn'), __('X19','pn'), 'administrator', "pn_x19_config", array($premiumbox, 'admin_temp'));
}

$premiumbox->file_include($path.'/function');
$premiumbox->file_include($path.'/x19');
$premiumbox->file_include($path.'/filters');