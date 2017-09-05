<?php 
/*
Внимание! Не удаляйте кавычки при указание ваших значений.
Urgent! Do not delete quotation marks while entering data
*/
$marr = array();
$marr['CONFIRM_COUNT'] = "3";						// Количество подтверждения платежа, чтобы считать его выполненым / The required number of transaction confirmations
$marr['PUBLIC_KEY'] = "Вписать сюда/Write here";	// Публичный Ключ / Public key
$marr['PRIVAT_KEY'] = "Вписать сюда/Write here";	// Приватный Ключ / Privat key
$marr['SECRET'] = "Вписать сюда/Write here";		// Пароль №1. Любые символы без пробелов (не относится к аккаунту coinpayments, отвечает за безопасность платежа) / Password №1. Any characters with no spaces (responsible for the security of payment)
$marr['SECRET2'] = "Вписать сюда/Write here";		// Пароль №2. Любые символы без пробелов (не относится к аккаунту coinpayments, отвечает за безопасность платежа) / Password №2. Any characters with no spaces (responsible for the security of payment)