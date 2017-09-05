<?php
if( !defined( 'ABSPATH')){ exit(); }

/*
title: [ru_RU:]Статус "Ошибка авто выплаты"[:ru_RU][en_US:]Status "Automatic payout error"[:en_US]
description: [ru_RU:]Статус "Ошибка авто выплаты"[:ru_RU][en_US:]Status "Automatic payout error"[:en_US]
version: 1.0
category: [ru_RU:]Заявки[:ru_RU][en_US:]Orders[:en_US]
cat: req
*/

$path = get_extension_file(__FILE__);
$name = get_extension_name($path);

add_action('paymerchant_error', 'payouterror_paymerchant_error', 10, 4);
function payouterror_paymerchant_error($m_id, $error, $item_id, $place){
	$params = array();
	the_merchant_bid_status('payouterror', $item_id, 'user', 1, $place, $params);	
}