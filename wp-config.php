<?php
/**
* Основные параметры WordPress.
*
* Скрипт для создания wp-config.php использует этот файл в процессе
* установки. Необязательно использовать веб-интерфейс, можно
* скопировать файл в "wp-config.php" и заполнить значения вручную.
*
* Этот файл содержит следующие параметры:
*
* * Настройки MySQL
* * Секретные ключи
* * Префикс таблиц базы данных
* * ABSPATH
*
* @link https://codex.wordpress.org/Editing_wp-config.php
*
* @package WordPress
*/
// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'premium');
/** Имя пользователя MySQL */
define('DB_USER', 'premium');
/** Пароль к базе данных MySQL */
define('DB_PASSWORD', 'qwe123');
/** Имя сервера MySQL */
define('DB_HOST', 'localhost');
/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8');
/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');
/**#@+
* Уникальные ключи и соли для аутентификации.
*
* Смените значение каждой константы на уникальную фразу.
* Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
* Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
*
* @since 2.6.0
*/
define('AUTH_KEY',         '6/zMSO^ZKsAi1wZW`R+O#[[V5p*]!2Uz4l-Py<YL5JSPr-f&tI+_KBE(y$_C^ h;');
define('SECURE_AUTH_KEY',  'ChYEZ93 >Y(m <@^_h>?~6~4<HOzH_Fmdyfa^M-&-K]&1qo%-|f+!S Q->?/rK{r');
define('LOGGED_IN_KEY',    'E5(jSv;ntJ-kYPY;wfFOFc|5WM:E:IymSaXTA1qI9sC|2FSfhbQGQzxx3@xkK.kZ');
define('NONCE_KEY',        ';9e+t|aqz1bkuENbkcl&h<l~t}U-!G!{Ua;|/,cG~M`|YSx;NAEp^XO|!t&i:|RS');
define('AUTH_SALT',        'Q /)5lYrG@}3>>3u+^^k)Zo{ytPs,w2G8|9^fAeOw&+Bz!|JnjZ|aTW+2NRaag+T');
define('SECURE_AUTH_SALT', 'Mq=3P=ik9 %pT2Zyb4pQ+3Vf|q:2Yta9}n=z)I|nMo1VZF1A`-/W0D}Viw|xqj>?');
define('LOGGED_IN_SALT',   '/]0X6_w}kZ?`0e^`TrTxMOl+6A1_&WT$tF[LFab@fn[TP+A++rZ7x`Q|6vK|6D}v');
define('NONCE_SALT',       'PP%m[D9z,dHB4,<CgH{.i`KL]kx]cM>6s:(B,QID}]Lh=;VPe-kRI<@l7SZ|hC!|');
/**#@-*/
/**
* Префикс таблиц в базе данных WordPress.
*
* Можно установить несколько сайтов в одну базу данных, если использовать
* разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
*/
$table_prefix  = 'pr_';
/**
* Для разработчиков: Режим отладки WordPress.
*
* Измените это значение на true, чтобы включить отображение уведомлений при разработке.
* Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
* в своём рабочем окружении.
* 
* Информацию о других отладочных константах можно найти в Кодексе.
*
* @link https://codex.wordpress.org/Debugging_in_WordPress
*/
define('WP_DEBUG', false);
define('DISALLOW_FILE_MODS', true);
/* Это всё, дальше не редактируем. Успехов! */
/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
define('ABSPATH', dirname(__FILE__) . '/');
/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
