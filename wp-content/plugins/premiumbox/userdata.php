<?php
/*
Будьте внимательны! Данный файл необходимо редактировать только в кодировке UTF-8 без (BOM).
Attention please! You should edit this file in UTF-8 w/o (BOM) only.
*/
/**************** user data ******************/

	/* 
	Ваш личный код безопасности
	Your personal security code
	*/
	if(!defined('PN_SECRET_KEY')){
		define('PN_SECRET_KEY', '');
	}
	
	/* 
	Персональный хэш для URL кронов
	Personal hash for cron URLs
	*/
	if(!defined('PN_HASH_CRON')){
		define('PN_HASH_CRON', ''); 
	}	
	
	/* 
	Время жизни сессии авторизации, в днях
	Authorization session length (days)
	*/
	if(!defined('PN_USERSESS_DAY')){
		define('PN_USERSESS_DAY', '3');
	}	

	/* 
	Если забыли доступ, ставим true и заходим как обычно через /wp-admin. Также будет отключена ссылка авторизация по e-mail
	If you forgot to access the system, then set up the «True» and enter the system through /wp-admin. Note that in this case the authorization via e-mail link will be disabled.

	true(да/yes) false(нет/no)
	*/
	if(!defined('PN_ADMIN_GOWP')){
		define('PN_ADMIN_GOWP', 'false'); 
	}	

	/* 
	Комментарии в скрипте. Внимание! Комментарии - это самый распространенный метод взлома, открыв комментарии, вы подвергаете себя риску.
	Script comments. Attention please! Comments are the most known way usually used by hackers. If you enter comments section then you are putting yourself at risk.
	
	true(да/yes) false(нет/no)
	*/
	if(!defined('PN_COMMENT_STATUS')){ 
		define('PN_COMMENT_STATUS', 'false'); 
	}	

/**************** end user data ******************/