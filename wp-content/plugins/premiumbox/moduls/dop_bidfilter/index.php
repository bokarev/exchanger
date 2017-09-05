<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Дополнительные фильтры для раздела Заявки[:ru_RU][en_US:]Additional filters for Requests section[:en_US]
description: [ru_RU:]Дополнительные фильтры для раздела Заявки[:ru_RU][en_US:]Additional filters for Requests section[:en_US]
version: 1.0
category: [ru_RU:]Заявки[:ru_RU][en_US:]Orders[:en_US]
cat: req
*/

add_filter('onebid_icons','onebid_icons_dop_bidfilter',99,3);
function onebid_icons_dop_bidfilter($onebid_icon, $item, $data_fs){
	
	if($item->user_id){
		$onebid_icon['user_id'] = array(
			'type' => 'text',
			'title' => __('User','pn') . ': [user_id]',
			'label' => __('User','pn') . ': [user_id]',
			'link' => admin_url('admin.php?page=pn_bids&iduser=[user_id]'),
			'link_target' => '_blank',
		);		
	}
	
	return $onebid_icon;
}

add_filter('onebid_actions','onebid_actions_dop_bidfilter',99,3);
function onebid_actions_dop_bidfilter($onebid_actions, $item, $data_fs){
	
	$onebid_actions['similar_account_give'] = array(
		'type' => 'link',
		'title' => __('Similar by account Send','pn'),
		'label' => __('By account Send','pn'),
		'link' => admin_url('admin.php?page=pn_bids&ac1=[account_give]'),
		'link_target' => '_blank',
		'link_class' => '',
	);	
	$onebid_actions['similar_account_get'] = array(
		'type' => 'link',
		'title' => __('Similar by account Receive','pn'),
		'label' => __('By account Receive','pn'),
		'link' => admin_url('admin.php?page=pn_bids&ac2=[account_get]'),
		'link_target' => '_blank',
		'link_class' => '',
	);	
	$onebid_actions['similar_user_email'] = array(
		'type' => 'link',
		'title' => __('Similar by e-mail','pn'),
		'label' => __('By e-mail','pn'),
		'link' => admin_url('admin.php?page=pn_bids&user_email=[user_email]'),
		'link_target' => '_blank',
		'link_class' => '',
	);
	$onebid_actions['similar_user_ip'] = array(
		'type' => 'link',
		'title' => __('Similar by IP','pn'),
		'label' => __('By IP','pn'),
		'link' => admin_url('admin.php?page=pn_bids&user_ip=[user_ip]'),
		'link_target' => '_blank',
		'link_class' => '',
	);	

	return $onebid_actions;
}