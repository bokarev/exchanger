<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Логин юзера в заявке[:ru_RU][en_US:]User login in order[:en_US]
description: [ru_RU:]Логин юзера в заявке[:ru_RU][en_US:]User login in order[:en_US]
version: 0.3
category: [ru_RU:]Заявки[:ru_RU][en_US:]Orders[:en_US]
cat: req
*/

add_filter('onebid_icons','onebid_icons_uslogin',1000,3);
function onebid_icons_uslogin($onebid_icon, $item, $data_fs){
	
	$user_id = $item->user_id;
	if($user_id > 0){
		$ui = get_userdata($user_id);
		if(isset($ui->user_login)){
			$user_login = $ui->user_login;
			
			$onebid_icon['uslogin'] = array(
				'type' => 'text',
				'title' => __('User login','pn'),
				'label' => $user_login .': [last_name] [first_name] [second_name]',
				'link' => admin_url('user-edit.php?user_id=[user_id]'),
				'link_target' => '_blank',
			);			
		}
	}
	
	return $onebid_icon;
}