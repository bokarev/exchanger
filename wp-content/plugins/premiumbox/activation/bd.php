<?php
if( !defined( 'ABSPATH')){ exit(); }		
	
/* 
Создаем таблицы, необходимые нам	
*/	
		
global $wpdb;
$prefix = $wpdb->prefix;

	$table_name = $wpdb->prefix ."change";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`meta_key` varchar(250) NOT NULL,
		`meta_key2` varchar(250) NOT NULL,
		`meta_value` longtext NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);

	$table_name = $wpdb->prefix ."login_check";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`datelogin` datetime NOT NULL,
		`user_id` bigint(20) NOT NULL,
		`user_login` varchar(250) NOT NULL,
		`user_ip` varchar(250) NOT NULL,
		`user_browser` varchar(250) NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	
	
	/* безопасность */
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."users LIKE 'sec_lostpass'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."users ADD `sec_lostpass` int(1) NOT NULL default '1'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."users LIKE 'sec_login'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."users ADD `sec_login` int(1) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."users LIKE 'email_login'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."users ADD `email_login` int(1) NOT NULL default '0'");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."users LIKE 'enable_ips'");
    if ($query == 0) {
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."users ADD `enable_ips` longtext NOT NULL");
    }		
	/* end безопаность */

	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."users LIKE 'auto_login1'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."users ADD `auto_login1` varchar(250) NOT NULL");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."users LIKE 'auto_login2'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."users ADD `auto_login2` varchar(250) NOT NULL");
    }
	$query = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix ."users LIKE 'user_discount'");
    if ($query == 0){
		$wpdb->query("ALTER TABLE ".$wpdb->prefix ."users ADD `user_discount` varchar(50) NOT NULL default '0'");
    }	

/* архив */
	$table_name= $wpdb->prefix ."archive_data";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`meta_key` varchar(250) NOT NULL,
		`meta_key2` varchar(250) NOT NULL,
		`meta_key3` varchar(250) NOT NULL,
		`item_id` bigint(20) NOT NULL default '0',
		`meta_value` varchar(20) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);

/*
платежные системы

psys_title - значение
psys_logo - логотип
*/
	$table_name = $wpdb->prefix ."psys";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`psys_title` longtext NOT NULL,
		`psys_logo` longtext NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);
/* end платежные системы */

/*
коды валют

vtype_title - значение
vncurs - внутренний курс за 1 доллар
parser - id парсера
nums - число
elem - 0-сумма, 1-процент
*/
	$table_name = $wpdb->prefix ."vtypes";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`vtype_title` longtext NOT NULL,
		`vncurs` varchar(50) NOT NULL default '0',
		`parser` bigint(20) NOT NULL default '0',
		`nums` varchar(50) NOT NULL default '0',
		`elem` int(2) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);
/* end коды валют */

/* создаем коды валют */
	$vtypes = array('RUB','EUR','USD','UAH','AMD','KZT','GLD','BYN','UZS','BTC','TRY');
	if(is_array($vtypes)){
		foreach($vtypes as $type){
			$cc = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."vtypes WHERE vtype_title='$type'");
			if($cc == 0){
				$wpdb->insert($wpdb->prefix ."vtypes", array('vtype_title'=>$type, 'vncurs'=>'1'));
			}
		}
	}
/* end создаем коды валют */

/*
Расписание оператора

status - статус
*/
	$table_name= $wpdb->prefix ."operator_schedules";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`status` int(5) NOT NULL default '0',
		`h1` varchar(5) NOT NULL default '0',
		`m1` varchar(5) NOT NULL default '0',
		`h2` varchar(5) NOT NULL default '0',
		`m2` varchar(5) NOT NULL default '0',		
		`d1` int(1) NOT NULL default '0',
		`d2` int(1) NOT NULL default '0',
		`d3` int(1) NOT NULL default '0',
		`d4` int(1) NOT NULL default '0',
		`d5` int(1) NOT NULL default '0',
		`d6` int(1) NOT NULL default '0',
		`d7` int(1) NOT NULL default '0',
		`save_order` bigint(20) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	

/*
валюты

valut_logo - логотип валюты
psys_logo - лого платежки
psys_id - id ПС
psys_title - название ПС
vtype_id - id кода валюты
vtype_title - название кода валюты
valut_decimal - знаков после запятой
xml_value - значение для XML
valut_status - активность валюты (1 - активна, 0 - не активна)
valut_reserv - резерв (автосумма)
reserv_place - откуда брать резерв (0-считать)
minzn - минимальное кол-во символов
maxzn - максимальное кол-во символов
firstzn - первые буквы
cifrzn - что используется (0-буквы и цифры, 1-только цифры, 2-только буквы, 3-email, 4-все символы, 5-телефон)
vidzn - вид счета (0-счет, 1-карта, 2-номер телефона)
helps - подсказка при заполнении (отдаю)
helps2 - подсказка при заполнении (получаю)
txt1 - название отдаете
show1 - выводить при отдаете
txt2 - название получаете
show2 - выводить при получаете
lead_num - число приведения
cf_hidden - видимость на сайте
check_text - текст проверенного кошелька
check_purse - интерфейс проверки кошелька
*/
	$table_name= $wpdb->prefix ."valuts";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`valut_logo` longtext NOT NULL,
		`psys_logo` longtext NOT NULL, 
		`valut_decimal` int(2) NOT NULL default '2',
		`valut_status` int(1) NOT NULL default '1',
		`valut_reserv` varchar(50) NOT NULL default '0',
		`reserv_place` varchar(150) NOT NULL default '0',
		`xml_value` varchar(250) NOT NULL,		
		`minzn` int(5) NOT NULL default '0',
		`maxzn` int(5) NOT NULL default '100',
		`firstzn` varchar(20) NOT NULL,
		`cifrzn` int(2) NOT NULL default '0',
		`vidzn` int(2) NOT NULL default '0',
		`lead_num` varchar(20) NOT NULL default '0',
		`helps` longtext NOT NULL,
		`helps2` longtext NOT NULL,
		`txt1` longtext NOT NULL,
		`txt2` longtext NOT NULL,
		`show1` int(2) NOT NULL default '1',
		`show2` int(2) NOT NULL default '1',
		`psys_id` bigint(20) NOT NULL default '0',
		`psys_title` longtext NOT NULL,		
		`vtype_id` bigint(20) NOT NULL default '0',
		`vtype_title` longtext NOT NULL,
		`cf_hidden` int(2) NOT NULL default '0',
		`site_order` bigint(20) NOT NULL default '0',
		`reserv_order` bigint(20) NOT NULL default '0',
		`check_text` longtext NOT NULL,
		`check_purse` varchar(150) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);

	$table_name= $wpdb->prefix ."valuts_meta";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`item_id` bigint(20) NOT NULL default '0',
		`meta_key` longtext NOT NULL,
		`meta_value` longtext NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);

/*
транзакции резерва

trans_title - название транзакции
trans_create - дата создания транзакции
trans_edit - дата изменения транзакции
user_creator - id юзера создавшего транзакцию
user_editor - id юзера отредактировавшего транзакцию
trans_summ - сумма
valut_id - id валюты
vtype_id - id типа валюты
vtype_title - название типа валюты
*/
	$table_name= $wpdb->prefix ."trans_reserv";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		`trans_title` longtext NOT NULL,
		`trans_create` datetime NOT NULL,
		`trans_edit` datetime NOT NULL,
		`user_creator` bigint(20) NOT NULL default '0',
		`user_editor` bigint(20) NOT NULL default '0',
		`trans_summ` varchar(50) NOT NULL default '0',
		`valut_id` bigint(20) NOT NULL default '0',
		`vtype_id` bigint(20) NOT NULL default '0',
		`vtype_title` longtext NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);

/*
Дополнительные поля валют

tech_name - техническое название
cf_name - название
vid - 0 текст, 1- select
cf_req - 0-не обязательно, 1-обязательно
minzn - мин.длинна
maxzn - макс длинна
firstzn - начальное значение
helps - подсказка отдаете
datas - если селект, то массив выборки
cf_hidden - видимость на сайте
valut_id - id валюты
place_id - 0 - и там и там, 1 - отдаете, 2 - получаете
uniqueid - идентификатор для автовыплат и прочего
*/	
	$table_name= $wpdb->prefix ."custom_fields_valut";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`tech_name` longtext NOT NULL,
		`cf_name` longtext NOT NULL,
		`vid` int(1) NOT NULL default '0',
		`valut_id` bigint(20) NOT NULL default '0',
		`cf_req` int(1) NOT NULL default '0',
		`place_id` int(1) NOT NULL default '0',
		`minzn` int(2) NOT NULL default '0',
		`maxzn` int(5) NOT NULL default '100',
		`firstzn` varchar(20) NOT NULL,
		`uniqueid` varchar(250) NOT NULL,
		`helps` longtext NOT NULL,
		`datas` longtext NOT NULL,
		`status` int(2) NOT NULL default '1',
		`cf_hidden` int(2) NOT NULL default '0',
		`cf_order` bigint(20) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);

/*
Дополнительные поля направлений

tech_name - техническое название
cf_name - название
vid - 0 текст, 1- select
cf_req - 0-не обязательно, 1-обязательно
cf_hidden - видимость на сайте
minzn - мин.длинна
maxzn - макс длинна
firstzn - начальное значение
helps - подсказка
datas - если селект, то массив выборки
*/	
	$table_name= $wpdb->prefix ."custom_fields";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`tech_name` longtext NOT NULL,
		`cf_name` longtext NOT NULL,
		`vid` int(1) NOT NULL default '0',
		`cf_req` int(1) NOT NULL default '0',
		`minzn` int(2) NOT NULL default '0',
		`maxzn` int(5) NOT NULL default '100',
		`firstzn` varchar(20) NOT NULL,
		`uniqueid` varchar(250) NOT NULL,
		`helps` longtext NOT NULL,
		`cf_auto` varchar(250) NOT NULL,
		`datas` longtext NOT NULL,
		`status` int(2) NOT NULL default '1',
		`cf_hidden` int(2) NOT NULL default '0',
		`cf_order` bigint(20) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	
	
	$table_name= $wpdb->prefix ."cf_naps";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`naps_id` bigint(20) NOT NULL default '0',
		`cf_id` bigint(20) NOT NULL default '0',
		`place_id` bigint(20) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);

/*
направления обменов

valut_id1 - валюта отдаете
valut_id2 - валюта получаете
psys_id1 - id платежной системы отдаете
psys_id2 - id платежной системы получаете
naps_status - статус направления (1-активно, 0-неактивно)
show_file - 0-не выводить в файлах, 1-выводить в файлах
curs1 - курс 1
curs2 - курс 2
minsumm1 - Мин. сумма обмена (для Отдаю)
minsumm2 - Мин. сумма обмена (для Получаю)
maxsumm1 - Макс. сумма обмена (для Отдаю)
maxsumm2 - Макс. сумма обмена (для Получаю)

com_summ1 - сумма коммисси 1
com_pers1 - процент коммисси 1
com_summ2 - сумма коммисси 2
com_pers2 - процент коммисси 2

profit_summ1 - сумма прибыли 1
profit_pers1 - процент прибыли 1
profit_summ2 - сумма прибыли 2
profit_pers2 - процент прибыли 2

minsumm1com - минимальная сумма комиссии
maxsumm1com - максимальная сумма комиссии

pay_com1 - оплата комиссии (0-юзер, 1-обменник)
pay_com2 - оплата комиссии (0-юзер, 1-обменник)
nscom1 - нестандартная комиссия (0-нет, 1-да)
nscom2 - нестандартная комиссия (0-нет, 1-да)

com_box_summ1 - доп.комиссия с отправителя сумма
com_box_pers1 - доп.комиссия с отправителя процент
com_box_min1 - доп.комиссия с отправителя
com_box_summ2 - доп.комиссия с получателя сумма
com_box_pers2 - доп.комиссия с получателя процент
com_box_min2 - доп.комиссия с получателя

user_sk - скидка пользователей
max_user_sk - максимальная скидка
maxnaps - ограничение резерва по направлению обмена

not_ip - запрещенные ip или маски, массив

m_in - мертчант приема оплаты 
m_out - мерчант автовыплаты

naps_name - ЧПУ направления

site_order1 - сортировка для тарифов и карты сайта
*/
	$table_name = $wpdb->prefix ."naps";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`createdate` datetime NOT NULL,
		`editdate` datetime NOT NULL,
		`autostatus` int(1) NOT NULL default '1',
		`valut_id1` bigint(20) NOT NULL default '0',
		`valut_id2` bigint(20) NOT NULL default '0',
		`psys_id1` bigint(20) NOT NULL default '0',
		`psys_id2` bigint(20) NOT NULL default '0',	
		`tech_name` longtext NOT NULL,
		`curs1` varchar(50) NOT NULL default '0',
		`curs2` varchar(50) NOT NULL default '0',
		`profit_summ1` varchar(50) NOT NULL default '0',
		`profit_summ2` varchar(50) NOT NULL default '0',
		`profit_pers1` varchar(20) NOT NULL default '0',
		`profit_pers2` varchar(20) NOT NULL default '0',		
		`com_summ1` varchar(50) NOT NULL default '0',
		`com_summ2` varchar(50) NOT NULL default '0',		
		`com_pers1` varchar(20) NOT NULL default '0',
		`com_pers2` varchar(20) NOT NULL default '0',		
		`com_summ1_check` varchar(50) NOT NULL default '0',
		`com_summ2_check` varchar(50) NOT NULL default '0',
		`com_pers1_check` varchar(20) NOT NULL default '0',
		`com_pers2_check` varchar(20) NOT NULL default '0',
		`pay_com1` int(1) NOT NULL default '0',
		`pay_com2` int(1) NOT NULL default '0',
		`nscom1` int(1) NOT NULL default '0',
		`nscom2` int(1) NOT NULL default '0',		
		`maxsumm1com` varchar(250) NOT NULL default '0', 
		`maxsumm2com` varchar(250) NOT NULL default '0',
		`minsumm1com` varchar(50) NOT NULL default '0',  
		`minsumm2com` varchar(50) NOT NULL default '0',	
		`minsumm1` varchar(250) NOT NULL default '0',
		`minsumm2` varchar(250) NOT NULL default '0',
		`maxsumm1` varchar(250) NOT NULL default '0',
		`maxsumm2` varchar(250) NOT NULL default '0',
		`com_box_summ1` varchar(250) NOT NULL default '0',
		`com_box_pers1` varchar(250) NOT NULL default '0',
		`com_box_min1` varchar(250) NOT NULL default '0',
		`com_box_summ2` varchar(250) NOT NULL default '0',
		`com_box_pers2` varchar(250) NOT NULL default '0',
		`com_box_min2` varchar(250) NOT NULL default '0',
		`m_in` varchar(150) NOT NULL default '0',
		`m_out` varchar(150) NOT NULL default '0',		
		`user_sk` int(1) NOT NULL default '1',
		`max_user_sk` varchar(5) NOT NULL default '50',		
		`check_purse` int(1) NOT NULL default '0',
		`req_check_purse` int(1) NOT NULL default '0',		
		`naps_name` varchar(250) NOT NULL,
		`naps_status` int(2) NOT NULL default '1',
		`site_order1` bigint(20) NOT NULL default '0',
		`to1` bigint(20) NOT NULL default '0',
		`to2_1` bigint(20) NOT NULL default '0',
		`to2_2` bigint(20) NOT NULL default '0',
		`to3_1` bigint(20) NOT NULL default '0',		
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);	
	
	$table_name = $wpdb->prefix ."naps_meta";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`item_id` bigint(20) NOT NULL default '0',
		`meta_key` longtext NOT NULL,
		`meta_value` longtext NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);

	$table_name = $wpdb->prefix ."naps_order";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`naps_id` bigint(20) NOT NULL default '0',
		`v_id` bigint(20) NOT NULL default '0',
		`order1` bigint(20) NOT NULL default '0',
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);

/* обмены 

createdate - дата создания
editdate - дата смены статуса
naps_id - id направления
curs1 - курс 1
curs2 - курс 2

valut1 - название валюты 1
valut2 - название валюты 2
valut1i - id валюты 1
valut2i - id валюты 2
vtype1 - тип валюты 1
vtype2 - тип валюты 1
vtype1i - id тип валюты 1
vtype2i - id тип валюты 2
psys1i - id платежной системы 1
psys2i - id платежной системы 2

user_id - id юзера
user_sk - скидка пользователя в момент обмена
user_sksumm - сумма скидки юзера
user_country - страна
user_ip - ip
first_name
last_name
second_name
user_phone
user_skype
user_email
account1 - счет 1
account2 - счет 2

metas - обычные поля
dmetas - доп.поля валют

ref_id - id реферала
profit - сумма прибыли($)
summp - сумма партнера($)
partpr - партнерский процент
pcalc - партнерское начисление (0-не насчитано, 1-насчитано) 
		защита от изменения условий начисления по юзерам.

exsum - сумма обмена в валюте
summ1 - сумма отдаете
dop_com1 - сумма доп.комиссии
summ1_dc - сумма отдаете с доп.комиссией
com_ps1 - комисия платежной системы по отдаете
summ1c - сумма с комиссией для юзера
summ1cr - сумма с комиссией для резерва обменника

dop_com2 - сумма доп.комиссии
com_ps2 - комисия платежной системы по получаете
summ2t - сумма получаете по курсу
summ2 - сумма получаете по курсу (учтена скидка)
summ2_dc - сумма получаете с доп.комиссией
summ2c - сумма с комиссией для юзера
summ2cr - сумма с комиссией для резерва обменника

mystatus - id пользовательского статуса
status - статус 
	auto - автоматически(не заявка)
	new - новая заявка
	cancel - отменена пользователем
	delete - удалена
	techpay - техническая оплата
	payed - юзер сказал что оплачена(я оплатил!)
	realpay - реально оплачена
	verify - оплачена с другого кошелька	
	error - ошибка
	success - выполненая
	
	realdelete - условно(фильтр)
	autodelete
	
hashed - хэш
user_hash - хэш пользователя
bid_locale - локализация
naschet - счет для оплаты

m_in - мертчант приема оплаты
m_out - мерчант автовыплаты

exceed_pay - превышена оплата или нет

hashdata - данные в хэше
*/
	$table_name= $wpdb->prefix ."bids";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`createdate` datetime NOT NULL,
		`editdate` datetime NOT NULL,		
		`naps_id` bigint(20) NOT NULL default '0',
		`curs1` varchar(50) NOT NULL default '0',
		`curs2` varchar(50) NOT NULL default '0',		
		`valut1` longtext NOT NULL,
		`valut2` longtext NOT NULL,
		`valut1i` bigint(20) NOT NULL default '0',
		`valut2i` bigint(20) NOT NULL default '0',
		`vtype1` varchar(35) NOT NULL,
		`vtype2` varchar(35) NOT NULL,
		`vtype1i` bigint(20) NOT NULL default '0',
		`vtype2i` bigint(20) NOT NULL default '0',
		`psys1i` bigint(20) NOT NULL default '0',
		`psys2i` bigint(20) NOT NULL default '0',		
		`exsum` varchar(50) NOT NULL default '0',
		`summ1` varchar(50) NOT NULL default '0',
		`dop_com1` varchar(50) NOT NULL default '0',
		`summ1_dc` varchar(50) NOT NULL default '0',
		`com_ps1` varchar(50) NOT NULL default '0',
		`summ1c` varchar(50) NOT NULL default '0',
		`summ1cr` varchar(50) NOT NULL default '0',
		`summ2t` varchar(50) NOT NULL default '0',
		`summ2` varchar(50) NOT NULL default '0',
		`dop_com2` varchar(50) NOT NULL default '0',
		`com_ps2` varchar(50) NOT NULL default '0',
		`summ2_dc` varchar(50) NOT NULL default '0',
		`summ2c` varchar(50) NOT NULL default '0',
		`summ2cr` varchar(50) NOT NULL default '0',
		`profit` varchar(50) NOT NULL default '0',
		`user_id` bigint(20) NOT NULL default '0',
		`user_sk` varchar(10) NOT NULL default '0',
		`user_sksumm` varchar(50) NOT NULL default '0',
		`user_ip` varchar(150) NOT NULL,
		`first_name` varchar(150) NOT NULL,
		`last_name` varchar(150) NOT NULL,
		`second_name` varchar(150) NOT NULL,
		`user_phone` varchar(150) NOT NULL,
		`user_skype` varchar(150) NOT NULL,
		`user_email` varchar(150) NOT NULL,
		`user_passport` varchar(250) NOT NULL,
		`metas` longtext NOT NULL,
		`dmetas` longtext NOT NULL,
		`unmetas` longtext NOT NULL,
		`account1` varchar(250) NOT NULL,
		`account2` varchar(250) NOT NULL,		
		`naschet` varchar(250) NOT NULL,
		`soschet` varchar(250) NOT NULL,
		`trans_in` varchar(250) NOT NULL default '0',
		`trans_out` varchar(250) NOT NULL default '0',		
		`status` varchar(35) NOT NULL,
		`hashed` varchar(35) NOT NULL,
		`user_hash` varchar(150) NOT NULL,
		`bid_locale` varchar(10) NOT NULL,
		`m_in` varchar(150) NOT NULL default '0',
		`m_out` varchar(150) NOT NULL default '0',
		`check_purse1` varchar(20) NOT NULL default '0',
		`check_purse2` varchar(20) NOT NULL default '0',
		`exceed_pay` int(1) NOT NULL default '0',
		`hashdata` longtext NOT NULL,
		`touap_date` datetime NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql); 
	
/*
мета

comment_user - комментарий для юзера
comment_admin - комментарий для админа
device - 0-веб, 1-мобильная версия
new - новичок или нет
pay_sum - сумма оплаты
pay_ac - аккаунт с которого оплатили
*/	
	$table_name= $wpdb->prefix ."bids_meta";
    $sql = "CREATE TABLE IF NOT EXISTS $table_name(
		`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT ,
		`item_id` bigint(20) NOT NULL default '0',
		`meta_key` longtext NOT NULL,
		`meta_value` longtext NOT NULL,
		PRIMARY KEY ( `id` )	
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	$wpdb->query($sql);															

	do_action('pn_bd_activated');				 