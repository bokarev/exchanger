<?php
if( !defined( 'ABSPATH')){ exit(); }	

/* 
Миграция с версии на версию
*/

global $wpdb; 
$prefix = $wpdb->prefix; 
 
	/* valuts */		
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."valuts LIKE 'psys_logo'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."valuts ADD `psys_logo` longtext NOT NULL");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."valuts LIKE 'helps2'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."valuts ADD `helps2` longtext NOT NULL");
    }	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."valuts LIKE 'reserv_place'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."valuts ADD `reserv_place` varchar(150) NOT NULL default '0'");
    } else {
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."valuts CHANGE `reserv_place` `reserv_place` varchar(150) NOT NULL default '0'");
	}
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."valuts LIKE 'reserv_order'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."valuts ADD `reserv_order` bigint(20) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."valuts LIKE 'site_order'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."valuts ADD `site_order` bigint(20) NOT NULL default '0'");
    }	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."valuts LIKE 'check_text'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."valuts ADD `check_text` longtext NOT NULL");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."valuts LIKE 'check_purse'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."valuts ADD `check_purse` varchar(150) NOT NULL default '0'");
    }	
	/* end valuts */  
 
	/* bids */		 
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'm_in'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `m_in` varchar(150) NOT NULL default '0'");
    } else {
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids CHANGE `m_in` `m_in` varchar(150) NOT NULL default '0'");
	}
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'm_out'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `m_out` varchar(150) NOT NULL default '0'");
    } else {
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids CHANGE `m_out` `m_out` varchar(150) NOT NULL default '0'");
	}
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'soschet'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `soschet` varchar(250) NOT NULL");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'trans_in'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `trans_in` varchar(250) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'trans_out'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `trans_out` varchar(250) NOT NULL default '0'");
    }	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'check_purse1'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `check_purse1` varchar(20) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'check_purse2'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `check_purse2` varchar(20) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'exceed_pay'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `exceed_pay` int(1) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'unmetas'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `unmetas` longtext NOT NULL");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'hashdata'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `hashdata` longtext NOT NULL");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."bids LIKE 'touap_date'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."bids ADD `touap_date` datetime NOT NULL");
    }	
	/* end bids */
 
	/* naps */
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'tech_name'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `tech_name` longtext NOT NULL");
    }	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'createdate'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `createdate` datetime NOT NULL");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'autostatus'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `autostatus` int(1) NOT NULL default '1'");
    }	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'editdate'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `editdate` datetime NOT NULL");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'to1'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `to1` bigint(20) NOT NULL default '0'");
    }	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'to2_1'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `to2_1` bigint(20) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'to2_2'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `to2_2` bigint(20) NOT NULL default '0'");
    }	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'to3_1'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `to3_1` bigint(20) NOT NULL default '0'");
    }	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'com_summ1_check'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `com_summ1_check` varchar(50) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'com_summ2_check'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `com_summ2_check` varchar(50) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'com_pers1_check'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `com_pers1_check` varchar(20) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'com_pers2_check'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `com_pers2_check` varchar(20) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'check_purse'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `check_purse` int(1) NOT NULL default '0'");
    }	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'req_check_purse'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps ADD `req_check_purse` int(1) NOT NULL default '0'");
    }	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'm_in'");
    if ($query == 1) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps CHANGE `m_in` `m_in` varchar(150) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."naps LIKE 'm_out'");
    if ($query == 1) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."naps CHANGE `m_out` `m_out` varchar(150) NOT NULL default '0'");
    }		
	/* end naps */

	/* other */
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."archive_data LIKE 'meta_key3'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."archive_data ADD `meta_key3` varchar(250) NOT NULL");
    }		
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."custom_fields_valut LIKE 'tech_name'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."custom_fields_valut ADD `tech_name` longtext NOT NULL");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."custom_fields_valut LIKE 'uniqueid'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."custom_fields_valut ADD `uniqueid` varchar(250) NOT NULL");
    }	
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."custom_fields LIKE 'tech_name'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."custom_fields ADD `tech_name` longtext NOT NULL");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."custom_fields LIKE 'uniqueid'");
    if ($query == 0) { 
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."custom_fields ADD `uniqueid` varchar(250) NOT NULL");
    }	
	/* end other */ 