<?php
if( !defined( 'ABSPATH')){ exit(); }		
	
global $wpdb;
$prefix = $wpdb->prefix;

	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."inex_system LIKE 'gid'");
    if ($query == 1) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."inex_system CHANGE `gid` `gid` varchar(250) NOT NULL");
	}
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."inex_tars LIKE 'gid'");
    if ($query == 1) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."inex_tars CHANGE `gid` `gid` varchar(250) NOT NULL");
	}
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."inex_tars LIKE 'maxsumtar'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."inex_tars ADD `maxsumtar` varchar(250) NOT NULL");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."inex_deposit LIKE 'gid'");
    if ($query == 1) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."inex_deposit CHANGE `gid` `gid` varchar(250) NOT NULL");
	}	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."inex_deposit LIKE 'locale'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."inex_deposit ADD `locale` varchar(20) NOT NULL");
    }		