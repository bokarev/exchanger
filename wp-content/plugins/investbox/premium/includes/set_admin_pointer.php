<?php
/* защита от прямого обращения */
if( !defined( 'ABSPATH')){ exit(); }

if(!function_exists('set_admin_pointer')){
	function set_admin_pointer(){
		$data = premium_rewrite_data();
		$super_base = $data['super_base'];
		$admin_pages = array('premium_post.html','premium_quicktags.js');
		if(in_array($super_base, $admin_pages)){	
			pn_set_wp_admin();			
		}	
	}
	set_admin_pointer();
}