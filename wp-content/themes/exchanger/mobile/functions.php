<?php
if( !defined( 'ABSPATH')){ exit(); }

function mobile_template($page){
$pager = TEMPLATEPATH . "/mobile/".$page.".php";
    if(file_exists($pager)){
        include($pager);
    }
}

if ( is_plugin_inactive( 'premiumbox/premiumbox.php' )) {
	return;
}

mobile_template('includes/sites_func');
mobile_template('includes/api');

mobile_template('change/all');
mobile_template('change/home');