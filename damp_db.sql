-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Сен 01 2017 г., 13:54
-- Версия сервера: 5.5.25
-- Версия PHP: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `premium`
--

-- --------------------------------------------------------

--
-- Структура таблицы `pr_admin_captcha`
--

CREATE TABLE IF NOT EXISTS `pr_admin_captcha` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `createdate` datetime NOT NULL,
  `sess_hash` varchar(150) NOT NULL,
  `num1` varchar(10) NOT NULL DEFAULT '0',
  `num2` varchar(10) NOT NULL DEFAULT '0',
  `symbol` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `pr_admin_captcha`
--

INSERT INTO `pr_admin_captcha` (`id`, `createdate`, `sess_hash`, `num1`, `num2`, `symbol`) VALUES
(1, '2016-08-15 16:18:44', '8ffb4dc93192c7bf68d7c81bfbe1ce5e', '3', '8', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `pr_admin_captcha_plus`
--

CREATE TABLE IF NOT EXISTS `pr_admin_captcha_plus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `createdate` datetime NOT NULL,
  `sess_hash` varchar(150) NOT NULL,
  `num1` varchar(10) NOT NULL DEFAULT '0',
  `num2` varchar(10) NOT NULL DEFAULT '0',
  `symbol` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Дамп данных таблицы `pr_admin_captcha_plus`
--

INSERT INTO `pr_admin_captcha_plus` (`id`, `createdate`, `sess_hash`, `num1`, `num2`, `symbol`) VALUES
(10, '2017-09-01 12:52:16', '644e61d2cf1cd48b14532df27f8b180e', '4', '5', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `pr_archive_bids`
--

CREATE TABLE IF NOT EXISTS `pr_archive_bids` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `archive_date` datetime NOT NULL,
  `bid_id` bigint(20) NOT NULL DEFAULT '0',
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `ref_id` bigint(20) NOT NULL DEFAULT '0',
  `account1` varchar(250) NOT NULL,
  `account2` varchar(250) NOT NULL,
  `first_name` varchar(150) NOT NULL,
  `last_name` varchar(150) NOT NULL,
  `second_name` varchar(150) NOT NULL,
  `user_phone` varchar(150) NOT NULL,
  `user_skype` varchar(150) NOT NULL,
  `user_email` varchar(150) NOT NULL,
  `user_passport` varchar(250) NOT NULL,
  `archive_content` longtext NOT NULL,
  `archive_meta` longtext NOT NULL,
  `status` varchar(35) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_archive_data`
--

CREATE TABLE IF NOT EXISTS `pr_archive_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `meta_key` varchar(250) NOT NULL,
  `meta_key2` varchar(250) NOT NULL,
  `item_id` bigint(20) NOT NULL DEFAULT '0',
  `meta_value` varchar(20) NOT NULL DEFAULT '0',
  `meta_key3` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_autobroker_lite`
--

CREATE TABLE IF NOT EXISTS `pr_autobroker_lite` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `naps_id` bigint(20) NOT NULL DEFAULT '0',
  `site_id` bigint(20) NOT NULL DEFAULT '0',
  `step_column` int(20) NOT NULL DEFAULT '0',
  `step` varchar(20) NOT NULL DEFAULT '0',
  `min_sum` varchar(20) NOT NULL DEFAULT '0',
  `max_sum` varchar(20) NOT NULL DEFAULT '0',
  `cours1` varchar(20) NOT NULL DEFAULT '0',
  `cours2` varchar(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_autodel_bids_time`
--

CREATE TABLE IF NOT EXISTS `pr_autodel_bids_time` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `naps_id` bigint(20) NOT NULL DEFAULT '0',
  `enable_autodel` int(1) NOT NULL DEFAULT '0',
  `cou_hour` varchar(20) NOT NULL DEFAULT '0',
  `cou_minute` varchar(20) NOT NULL DEFAULT '0',
  `statused` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_bcc_logs`
--

CREATE TABLE IF NOT EXISTS `pr_bcc_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `createdate` datetime NOT NULL,
  `bid_id` bigint(20) NOT NULL DEFAULT '0',
  `counter` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_bids`
--

CREATE TABLE IF NOT EXISTS `pr_bids` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `createdate` datetime NOT NULL,
  `editdate` datetime NOT NULL,
  `naps_id` bigint(20) NOT NULL DEFAULT '0',
  `curs1` varchar(50) NOT NULL DEFAULT '0',
  `curs2` varchar(50) NOT NULL DEFAULT '0',
  `valut1` longtext NOT NULL,
  `valut2` longtext NOT NULL,
  `valut1i` bigint(20) NOT NULL DEFAULT '0',
  `valut2i` bigint(20) NOT NULL DEFAULT '0',
  `vtype1` varchar(35) NOT NULL,
  `vtype2` varchar(35) NOT NULL,
  `vtype1i` bigint(20) NOT NULL DEFAULT '0',
  `vtype2i` bigint(20) NOT NULL DEFAULT '0',
  `psys1i` bigint(20) NOT NULL DEFAULT '0',
  `psys2i` bigint(20) NOT NULL DEFAULT '0',
  `exsum` varchar(50) NOT NULL DEFAULT '0',
  `summ1` varchar(50) NOT NULL DEFAULT '0',
  `dop_com1` varchar(50) NOT NULL DEFAULT '0',
  `summ1_dc` varchar(50) NOT NULL DEFAULT '0',
  `com_ps1` varchar(50) NOT NULL DEFAULT '0',
  `summ1c` varchar(50) NOT NULL DEFAULT '0',
  `summ1cr` varchar(50) NOT NULL DEFAULT '0',
  `summ2t` varchar(50) NOT NULL DEFAULT '0',
  `summ2` varchar(50) NOT NULL DEFAULT '0',
  `dop_com2` varchar(50) NOT NULL DEFAULT '0',
  `com_ps2` varchar(50) NOT NULL DEFAULT '0',
  `summ2_dc` varchar(50) NOT NULL DEFAULT '0',
  `summ2c` varchar(50) NOT NULL DEFAULT '0',
  `summ2cr` varchar(50) NOT NULL DEFAULT '0',
  `ref_id` bigint(20) NOT NULL DEFAULT '0',
  `profit` varchar(50) NOT NULL DEFAULT '0',
  `summp` varchar(50) NOT NULL DEFAULT '0',
  `partpr` varchar(50) NOT NULL DEFAULT '0',
  `pcalc` int(1) NOT NULL DEFAULT '0',
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `user_sk` varchar(10) NOT NULL DEFAULT '0',
  `user_sksumm` varchar(50) NOT NULL DEFAULT '0',
  `user_country` varchar(10) NOT NULL,
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
  `account1` varchar(250) NOT NULL,
  `account2` varchar(250) NOT NULL,
  `account1h` varchar(250) NOT NULL,
  `account2h` varchar(250) NOT NULL,
  `naschet` varchar(250) NOT NULL,
  `status` varchar(35) NOT NULL,
  `mystatus` bigint(20) NOT NULL DEFAULT '0',
  `hashed` varchar(35) NOT NULL,
  `user_hash` varchar(150) NOT NULL,
  `bid_locale` varchar(10) NOT NULL,
  `m_in` varchar(150) NOT NULL DEFAULT '0',
  `m_out` varchar(150) NOT NULL DEFAULT '0',
  `naschet_h` varchar(250) NOT NULL,
  `soschet` varchar(250) NOT NULL,
  `trans_in` varchar(250) NOT NULL DEFAULT '0',
  `trans_in_h` varchar(250) NOT NULL,
  `trans_out` varchar(250) NOT NULL DEFAULT '0',
  `trans_out_h` varchar(250) NOT NULL,
  `check_purse1` varchar(20) NOT NULL DEFAULT '0',
  `check_purse2` varchar(20) NOT NULL DEFAULT '0',
  `domacc` int(1) NOT NULL DEFAULT '0',
  `new_user` int(2) NOT NULL DEFAULT '0',
  `exceed_pay` int(1) NOT NULL DEFAULT '0',
  `device` int(1) NOT NULL DEFAULT '0',
  `napsidenty` varchar(250) NOT NULL,
  `touap_date` datetime NOT NULL,
  `domacc1` int(1) NOT NULL DEFAULT '0',
  `domacc2` int(1) NOT NULL DEFAULT '0',
  `unmetas` longtext NOT NULL,
  `hashdata` longtext NOT NULL,
  `sumbonus` varchar(50) NOT NULL DEFAULT '0',
  `recalcdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_bidstatus`
--

CREATE TABLE IF NOT EXISTS `pr_bidstatus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` longtext NOT NULL,
  `status_title` longtext NOT NULL,
  `status_text` longtext NOT NULL,
  `status_descr` longtext NOT NULL,
  `send_mail` int(1) NOT NULL DEFAULT '0',
  `sender_mail` varchar(250) NOT NULL,
  `sender_name` varchar(250) NOT NULL,
  `letter_subject` longtext NOT NULL,
  `letter_text` longtext NOT NULL,
  `status_order` bigint(20) NOT NULL DEFAULT '0',
  `refresh_page` int(1) NOT NULL DEFAULT '0',
  `bg_color` varchar(250) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_bids_fstats`
--

CREATE TABLE IF NOT EXISTS `pr_bids_fstats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bid_id` bigint(20) NOT NULL DEFAULT '0',
  `statusdate` datetime NOT NULL,
  `naps_id` bigint(20) NOT NULL DEFAULT '0',
  `valut1` longtext NOT NULL,
  `valut2` longtext NOT NULL,
  `account1` varchar(250) NOT NULL,
  `account2` varchar(250) NOT NULL,
  `user_phone` varchar(150) NOT NULL,
  `user_fio` varchar(250) NOT NULL,
  `user_email` varchar(150) NOT NULL,
  `partner_sum` varchar(50) NOT NULL DEFAULT '0',
  `sum1or` varchar(50) NOT NULL DEFAULT '0',
  `sum2or` varchar(50) NOT NULL DEFAULT '0',
  `cours1` varchar(50) NOT NULL DEFAULT '0',
  `cours2` varchar(50) NOT NULL DEFAULT '0',
  `comis_or` varchar(50) NOT NULL DEFAULT '0',
  `profit_sum` varchar(50) NOT NULL DEFAULT '0',
  `pcours1` varchar(50) NOT NULL DEFAULT '0',
  `pcours2` varchar(50) NOT NULL DEFAULT '0',
  `status` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `pr_bids_fstats`
--

INSERT INTO `pr_bids_fstats` (`id`, `bid_id`, `statusdate`, `naps_id`, `valut1`, `valut2`, `account1`, `account2`, `user_phone`, `user_fio`, `user_email`, `partner_sum`, `sum1or`, `sum2or`, `cours1`, `cours2`, `comis_or`, `profit_sum`, `pcours1`, `pcours2`, `status`) VALUES
(2, 2, '2017-07-22 15:55:16', 1, 'Perfect Money USD', 'Сбербанк RUB', 'U1234567', '1234567812345678', '1234567', 'Иванов Иван Иванович', 'info@premium.ru', '0', '15.744', '899.9979', '1', '57.1645', '0', '0', '0', '0', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `pr_bids_meta`
--

CREATE TABLE IF NOT EXISTS `pr_bids_meta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) NOT NULL DEFAULT '0',
  `meta_key` longtext NOT NULL,
  `meta_value` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_bids_operators`
--

CREATE TABLE IF NOT EXISTS `pr_bids_operators` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `createdate` datetime NOT NULL,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `user_login` varchar(250) NOT NULL,
  `bid_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_bid_logs`
--

CREATE TABLE IF NOT EXISTS `pr_bid_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `createdate` datetime NOT NULL,
  `bid_id` bigint(20) NOT NULL DEFAULT '0',
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `user_login` varchar(150) NOT NULL,
  `old_status` varchar(150) NOT NULL,
  `new_status` varchar(150) NOT NULL,
  `place` varchar(50) NOT NULL,
  `who` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Дамп данных таблицы `pr_bid_logs`
--

INSERT INTO `pr_bid_logs` (`id`, `createdate`, `bid_id`, `user_id`, `user_login`, `old_status`, `new_status`, `place`, `who`) VALUES
(1, '2017-07-22 13:28:30', 1, 1, 'superboss', 'auto', 'new', 'site', 'user'),
(2, '2017-07-22 13:43:32', 1, 1, 'superboss', 'new', 'delete', 'admin', 'system'),
(3, '2017-07-22 14:56:23', 1, 1, 'superboss', 'delete', 'new', 'admin', 'user'),
(4, '2017-07-22 14:59:08', 1, 1, 'superboss', 'new', 'realpay', 'admin', 'user'),
(5, '2017-07-22 15:26:46', 1, 1, 'superboss', 'realpay', 'new', 'admin', 'user'),
(6, '2017-07-22 15:35:51', 1, 1, 'superboss', 'new', 'realdelete', 'admin', 'user'),
(7, '2017-07-22 15:56:50', 2, 1, 'superboss', 'auto', 'new', 'site', 'user'),
(8, '2017-07-22 16:11:51', 2, 1, 'superboss', 'new', 'delete', 'admin', 'system'),
(9, '2017-08-01 13:14:11', 2, 1, 'superboss', 'delete', 'realdelete', 'admin', 'user');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_blackbrokers`
--

CREATE TABLE IF NOT EXISTS `pr_blackbrokers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` longtext NOT NULL,
  `url` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_blackbrokers_naps`
--

CREATE TABLE IF NOT EXISTS `pr_blackbrokers_naps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `naps_id` bigint(20) NOT NULL DEFAULT '0',
  `site_id` bigint(20) NOT NULL DEFAULT '0',
  `step_column` int(20) NOT NULL DEFAULT '0',
  `step` varchar(150) NOT NULL DEFAULT '0',
  `min_sum` varchar(150) NOT NULL DEFAULT '0',
  `max_sum` varchar(150) NOT NULL DEFAULT '0',
  `cours1` varchar(150) NOT NULL DEFAULT '0',
  `cours2` varchar(150) NOT NULL DEFAULT '0',
  `item_from` varchar(150) NOT NULL,
  `item_to` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_blacklist`
--

CREATE TABLE IF NOT EXISTS `pr_blacklist` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `meta_key` varchar(12) NOT NULL DEFAULT '0',
  `meta_value` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_bonus_adj`
--

CREATE TABLE IF NOT EXISTS `pr_bonus_adj` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `adj_title` longtext NOT NULL,
  `adj_create` datetime NOT NULL,
  `adj_edit` datetime NOT NULL,
  `user_creator` bigint(20) NOT NULL DEFAULT '0',
  `user_editor` bigint(20) NOT NULL DEFAULT '0',
  `adj_sum` varchar(50) NOT NULL DEFAULT '0',
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_bonus_payouts`
--

CREATE TABLE IF NOT EXISTS `pr_bonus_payouts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pay_date` datetime NOT NULL,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `user_login` varchar(250) NOT NULL,
  `bonus_sum` varchar(250) NOT NULL DEFAULT '0',
  `pay_sum` varchar(250) NOT NULL DEFAULT '0',
  `pay_sum_or` varchar(250) NOT NULL DEFAULT '0',
  `valut_id` bigint(20) NOT NULL DEFAULT '0',
  `psys_title` longtext NOT NULL,
  `vtype_id` bigint(20) NOT NULL DEFAULT '0',
  `vtype_title` varchar(250) NOT NULL,
  `pay_account` varchar(250) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `comment` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_cf_naps`
--

CREATE TABLE IF NOT EXISTS `pr_cf_naps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `naps_id` bigint(20) NOT NULL DEFAULT '0',
  `cf_id` bigint(20) NOT NULL DEFAULT '0',
  `place_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Дамп данных таблицы `pr_cf_naps`
--

INSERT INTO `pr_cf_naps` (`id`, `naps_id`, `cf_id`, `place_id`) VALUES
(1, 1, 1, 0),
(2, 1, 2, 0),
(3, 1, 3, 0),
(4, 1, 6, 0),
(5, 1, 4, 0),
(6, 1, 5, 0),
(7, 2, 1, 0),
(8, 2, 2, 0),
(9, 2, 3, 0),
(10, 2, 6, 0),
(11, 2, 4, 0),
(12, 2, 5, 0),
(13, 3, 1, 0),
(14, 3, 2, 0),
(15, 3, 3, 0),
(16, 3, 6, 0),
(17, 3, 4, 0),
(18, 3, 5, 0),
(19, 4, 1, 0),
(20, 4, 2, 0),
(21, 4, 3, 0),
(22, 4, 6, 0),
(23, 4, 4, 0),
(24, 4, 5, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `pr_change`
--

CREATE TABLE IF NOT EXISTS `pr_change` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `meta_key` varchar(250) NOT NULL,
  `meta_key2` varchar(250) NOT NULL,
  `meta_value` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=176 ;

--
-- Дамп данных таблицы `pr_change`
--

INSERT INTO `pr_change` (`id`, `meta_key`, `meta_key2`, `meta_value`) VALUES
(1, 'cron', '', '0'),
(2, 'admincaptcha', '', '0'),
(3, 'lang_redir', '', '1'),
(4, 'admin_panel_url', '', 'prmmxchngr'),
(5, 'txtxml', 'txt', '1'),
(6, 'txtxml', 'xml', '1'),
(7, 'txtxml', 'numtxt', '12'),
(8, 'txtxml', 'numxml', '12'),
(9, 'htmlmap', 'exclude_page', 'a:22:{i:0;i:136;i:1;i:90;i:2;i:85;i:3;i:79;i:4;i:29;i:5;i:28;i:6;i:27;i:7;i:26;i:8;i:25;i:9;i:24;i:10;i:23;i:11;i:21;i:12;i:20;i:13;i:19;i:14;i:15;i:15;i:14;i:16;i:13;i:17;i:12;i:18;i:11;i:19;i:8;i:20;i:7;i:21;i:6;}'),
(10, 'htmlmap', 'exchanges', '1'),
(11, 'htmlmap', 'pages', '1'),
(12, 'htmlmap', 'news', '1'),
(13, 'exchange', 'techregtext', ''),
(14, 'exchange', 'techreg', '0'),
(15, 'exchange', 'gostnaphide', '0'),
(16, 'exchange', 'tablevid', '0'),
(17, 'exchange', 'exch_method', '0'),
(18, 'exchange', 'tablenot', '0'),
(19, 'exchange', 'reserv', '1'),
(20, 'exchange', 'flysum', '1'),
(21, 'exchange', 'redirect', '1'),
(22, 'exchange', 'autodelete', '1'),
(23, 'exchange', 'ad_h', '1'),
(24, 'exchange', 'ad_m', ''),
(25, 'exchange', 'exch_exsum', '1'),
(26, 'exchange', 'auto_reg', '1'),
(27, 'exchange', 'an1_hidden', '3'),
(28, 'exchange', 'an2_hidden', '0'),
(29, 'operator', '', '0'),
(30, 'statuswork', 'location', '1'),
(31, 'statuswork', 'text0', '[ru_RU:]Оператор offline[:ru_RU][en_US:]Operator offline[:en_US]'),
(32, 'statuswork', 'text1', '[ru_RU:]Оператор online[:ru_RU][en_US:]Operator online[:en_US]'),
(33, 'seo', 'home_title', '[ru_RU:]Обменный пункт электронных валют[:ru_RU][en_US:]Electronic currencies exchanger[:en_US]'),
(34, 'seo', 'home_key', ''),
(35, 'seo', 'home_descr', ''),
(36, 'seo', 'news_title', ''),
(37, 'seo', 'news_key', ''),
(38, 'seo', 'news_descr', ''),
(39, 'seo', 'news_temp', ''),
(40, 'seo', 'page_temp', ''),
(41, 'seo', 'exch_temp', ''),
(42, 'seo', 'exch_temp2', ''),
(43, 'seo', 'ogp_home_title', ''),
(44, 'seo', 'ogp_home_descr', ''),
(45, 'seo', 'ogp_news_title', ''),
(46, 'seo', 'ogp_news_descr', ''),
(47, 'seo', 'ogp_home_img', ''),
(48, 'seo', 'ogp_news_img', ''),
(49, 'reviews', 'count', '10'),
(50, 'reviews', 'deduce', '0'),
(51, 'reviews', 'method', 'moderation'),
(52, 'reviews', 'website', '0'),
(53, 'partners', 'status', '1'),
(54, 'partners', 'calc', '0'),
(55, 'partners', 'reserv', '1'),
(56, 'partners', 'minpay', '10'),
(57, 'naps_temp', 'description_txt', '[ru_RU:]Для обмена Вам необходимо выполнить несколько шагов:\r\n<ol>\r\n	<li>Заполните все поля представленной формы. Нажмите кнопку «Обменять».</li>\r\n	<li>Ознакомьтесь с условиями договора на оказание услуг обмена, если вы принимаете их, поставьте галочку в соответствующем поле и нажмите кнопку «Создать заявку».</li>\r\n	<li>Оплатите заявку.  Для этого следует совершить перевод необходимой суммы, следуя инструкциям на нашем сайте.</li>\r\n	<li>После выполнения указанных действий, система переместит Вас на страницу «Состояние заявки», где будет указан статус вашего перевода.</li>\r\n</ol>\r\n<strong>Внимание</strong>: для выполнения данной операции потребуется участие оператора (см. статус оператора).[:ru_RU][en_US:]For exchange you need to follow a few steps:\r\n<ol>\r\n	<li>Fill in all the fields of the form submitted. Click «Exchange».</li>\r\n	<li>Read the terms of the agreement on exchange services, when accepting it, please tick the appropriate field and press the button «Create bid».</li>\r\n	<li>Pay for the bid. To do this, transfer the necessary amount, following the instructions on our website.</li>\r\n	<li>After this is done, the system will redirect you to the «Bid status» page, where the status of your transferwill be shown.</li>\r\n</ol>\r\nNote: this operation will require the participation of the operator. The application process takes about 20 minutes.[:en_US]'),
(58, 'naps_temp', 'timeline_txt', '[ru_RU:]Данная операция производится оператором в ручном режиме и занимает от 5 до 30 минут в рабочее время (см. статус оператора).[:ru_RU][en_US:]<span style="line-height: 1.5;">Note: This transaction is performed by the operator in manual mode and takes 5 to 30 minutes during working hours (daily from 9:00 to 24:00 MSK).</span>[:en_US]'),
(59, 'naps_temp', 'status_new', '[ru_RU:]<ol>\r\n 	<li>Авторизуйтесь в платежной системе XXXXXXX;</li>\r\n 	<li>Переведите указанную ниже сумму на кошелек XXXXXXX;</li>\r\n 	<li>Нажмите на кнопку "Я оплатил заявку";</li>\r\n 	<li>Ожидайте обработку заявки оператором.</li>\r\n</ol>[:ru_RU][en_US:]<ol>\r\n 	<li> <span style="line-height: 1.5;">Log in to the system XXXXXXX;</span></li>\r\n 	<li>Turn the amounts shown below on the wallet XXXXXXX;</li>\r\n 	<li>Click on the "I paid bid";</li>\r\n 	<li>Expect the processing of the application by the operator.</li>\r\n</ol>[:en_US]'),
(60, 'naps_temp', 'status_cancel', '[ru_RU:]Оплата по заявке была возвращена на ваш кошелек.[:ru_RU][en_US:]Payment on the bid has been returned to your wallet.[:en_US]'),
(61, 'naps_temp', 'status_delete', '[ru_RU:]Заявка была удалена.[:ru_RU][en_US:]Bid has been deleted.[:en_US]'),
(62, 'naps_temp', 'status_payed', '[ru_RU:]Подтверждение оплаты принято.\r\nВаша заявка обрабатывается оператором.[:ru_RU][en_US:]Confirmation of payment accepted.\r\nYour request is being processed by the operator.[:en_US]'),
(63, 'naps_temp', 'status_realpay', '[ru_RU:]Подтверждение оплаты принято.\r\nВаша заявка обрабатывается оператором.[:ru_RU][en_US:]Confirmation of payment accepted.\r\nYour request is being processed by the operator.[:en_US]'),
(64, 'naps_temp', 'status_verify', '[ru_RU:]Подтверждение оплаты принято.\r\nВаша заявка обрабатывается оператором.[:ru_RU][en_US:]Confirmation of payment accepted.\r\nYour request is being processed by the operator.[:en_US]'),
(65, 'naps_temp', 'status_error', '[ru_RU:]В заявке есть ошибки. Обратитесь в техническую поддержку.[:ru_RU][en_US:]In the bid there is an error. Please contact technical support.[:en_US]'),
(66, 'naps_temp', 'status_success', '[ru_RU:]Ваша заявка выполнена.\r\nБлагодарим за то, что воспользовались услугами нашего сервиса.\r\nОставьте, пожалуйста, <a href="/reviews/">отзыв</a> о работе нашего сервиса![:ru_RU][en_US:]Your application is complete.\r\nThank you for using the services of our service.\r\nPlease leave <a href="/reviews/">review</a> review of the work of our service![:en_US]'),
(67, 'favicon', '', '/wp-content/uploads/favicon.png'),
(68, 'logo', '', ''),
(69, 'robotstxt', 'txt', ''),
(70, 'exchange', 'adjust', '0'),
(97, 'autodel', 'enable', '1'),
(72, 'ga', 'admin_time', '60'),
(73, 'ga', 'site_time', '60'),
(74, 'exchange', 'beautynum', '0'),
(75, 'exchange', 'admin_mail', '1'),
(76, 'exchange', 'rateconv', '0'),
(77, 'naps_title', 'status_coldpay', ''),
(78, 'naps_status', 'status_coldpay', ''),
(79, 'naps_timer', 'status_coldpay', '1'),
(80, 'naps_temp', 'status_coldpay', '[ru_RU:]Ожидаем подтверждения оплаты от платежной системы. Это может занять некоторое время.[:ru_RU][en_US:]Waiting for payment confirmation from payment system. This may take some time.[:en_US]'),
(81, 'naps_title', 'status_coldsuccess', ''),
(82, 'naps_status', 'status_coldsuccess', ''),
(83, 'naps_timer', 'status_coldsuccess', '1'),
(84, 'naps_temp', 'status_coldsuccess', '[ru_RU:]Ожидаем подтверждения статуса транзакции от платежной системы. Это может занять некоторое время.[:ru_RU][en_US:]Waiting for transaction status from payment system. This may take some time.[:en_US]'),
(85, 'partners', 'wref', '0'),
(86, 'textlogo', '', ''),
(87, 'up_mode', '', '0'),
(88, 'exchange', 'calctype', '1'),
(89, 'exchange', 'allow_dev', '0'),
(90, 'partners', 'clife', '0'),
(91, 'partners', 'pages', 'a:8:{i:0;s:8:"paccount";i:1;s:11:"promotional";i:2;s:6:"plinks";i:3;s:5:"pexch";i:4;s:9:"preferals";i:5;s:7:"payouts";i:6;s:11:"partnersfaq";i:7;s:5:"terms";}'),
(93, 'ga', 'ga_admin', '1'),
(94, 'ga', 'ga_site', '1'),
(120, 'apbytime', 'meneger', 'a:5:{s:6:"status";i:0;s:2:"h1";s:2:"00";s:2:"h2";s:2:"00";s:2:"m1";s:2:"00";s:2:"m2";s:2:"00";}'),
(96, 'adminpass', '', '0'),
(98, 'autodel', 'ad_h', '0'),
(99, 'autodel', 'ad_m', '15'),
(100, 'naps_title', 'status_techpay', ''),
(101, 'naps_status', 'status_techpay', ''),
(102, 'naps_timer', 'status_techpay', '1'),
(103, 'naps_temp', 'status_techpay', '[ru_RU:]Заявка находится в процессе оплаты.[:ru_RU][en_US:]Bid is in the payment process.[:en_US]'),
(104, 'operworks', 'minuts', '0'),
(105, 'xmlmap', 'exclude_page', 'a:22:{i:0;i:136;i:1;i:90;i:2;i:85;i:3;i:79;i:4;i:29;i:5;i:28;i:6;i:27;i:7;i:26;i:8;i:25;i:9;i:24;i:10;i:23;i:11;i:21;i:12;i:20;i:13;i:19;i:14;i:15;i:15;i:14;i:16;i:13;i:17;i:12;i:18;i:11;i:19;i:8;i:20;i:7;i:21;i:6;}'),
(106, 'xmlmap', 'exchanges', '1'),
(107, 'xmlmap', 'pages', '1'),
(108, 'xmlmap', 'news', '1'),
(109, 'nocopydata', '', '1'),
(110, 'exchange', 'mini_navi', '0'),
(111, 'exchange', 'mhead_style', '0'),
(112, 'exchange', 'm_ins', '0'),
(113, 'exchange', 'mp_ins', '0'),
(114, 'exchange', 'currtable', '0'),
(115, 'currtable', 'v1', '0'),
(116, 'currtable', 'v2', '0'),
(117, 'numsybm_count', '', '8'),
(118, 'user_fields', '', 'a:8:{s:5:"login";i:1;s:9:"last_name";i:1;s:10:"first_name";i:1;s:11:"second_name";i:1;s:10:"user_phone";i:1;s:10:"user_skype";i:1;s:7:"website";i:0;s:13:"user_passport";i:1;}'),
(119, 'user_fields_change', '', 'a:8:{s:9:"last_name";i:1;s:10:"first_name";i:1;s:11:"second_name";i:1;s:10:"user_phone";i:1;s:10:"user_skype";i:1;s:7:"website";i:1;s:13:"user_passport";i:1;s:5:"email";i:1;}'),
(121, 'tech', 'maintrance', '0'),
(122, 'tech', 'manualy', '0'),
(123, 'exchange', 'tableselect', '1'),
(124, 'exchange', 'ipuserhash', '0'),
(125, 'exchange', 'bacc_admin', '0'),
(126, 'exchange', 'bacc_site', '1'),
(127, 'exchange', 'maxsymb_all', '0'),
(128, 'exchange', 'maxsymb_reserv', '0'),
(129, 'exchange', 'maxsymb_course', '0'),
(130, 'exchange', 'maxpaybutton', '3'),
(131, 'naps_title', 'status_new', ''),
(132, 'naps_status', 'status_new', ''),
(133, 'naps_timer', 'status_new', '1'),
(134, 'naps_nodescr', 'status_new', '0'),
(135, 'usve', 'acc_status', '1'),
(136, 'mobile', 'tablevid', '0'),
(137, 'mobile', 'currtable', '0'),
(138, 'currtable', 'mob_v1', '0'),
(139, 'currtable', 'mob_v2', '0'),
(140, 'naps_title', 'status_payouterror', ''),
(141, 'naps_status', 'status_payouterror', ''),
(142, 'naps_timer', 'status_payouterror', '1'),
(143, 'naps_temp', 'status_payouterror', '[ru_RU:]Ошибка автоматической выплаты. Обратитесь в техническую поддержку сайта.[:ru_RU][en_US:]Automatic payout error. Contact technical support.[:en_US]'),
(144, 'naps_nodescr', 'status_payouterror', '0'),
(145, 'naps_title', 'status_cancel', ''),
(146, 'naps_status', 'status_cancel', ''),
(147, 'naps_timer', 'status_cancel', '1'),
(148, 'naps_nodescr', 'status_cancel', '0'),
(149, 'naps_title', 'status_delete', ''),
(150, 'naps_status', 'status_delete', ''),
(151, 'naps_timer', 'status_delete', '1'),
(152, 'naps_nodescr', 'status_delete', '0'),
(153, 'naps_nodescr', 'status_techpay', '0'),
(154, 'naps_title', 'status_payed', ''),
(155, 'naps_status', 'status_payed', ''),
(156, 'naps_timer', 'status_payed', '1'),
(157, 'naps_nodescr', 'status_payed', '0'),
(158, 'naps_nodescr', 'status_coldpay', '0'),
(159, 'naps_title', 'status_realpay', ''),
(160, 'naps_status', 'status_realpay', ''),
(161, 'naps_timer', 'status_realpay', '1'),
(162, 'naps_nodescr', 'status_realpay', '0'),
(163, 'naps_title', 'status_verify', ''),
(164, 'naps_status', 'status_verify', ''),
(165, 'naps_timer', 'status_verify', '1'),
(166, 'naps_nodescr', 'status_verify', '0'),
(167, 'naps_title', 'status_error', ''),
(168, 'naps_status', 'status_error', ''),
(169, 'naps_timer', 'status_error', '1'),
(170, 'naps_nodescr', 'status_error', '0'),
(171, 'naps_nodescr', 'status_coldsuccess', '0'),
(172, 'naps_title', 'status_success', ''),
(173, 'naps_status', 'status_success', ''),
(174, 'naps_timer', 'status_success', '1'),
(175, 'naps_nodescr', 'status_success', '0');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_commentmeta`
--

CREATE TABLE IF NOT EXISTS `pr_commentmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `comment_id` (`comment_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_comments`
--

CREATE TABLE IF NOT EXISTS `pr_comments` (
  `comment_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_post_ID` bigint(20) unsigned NOT NULL DEFAULT '0',
  `comment_author` tinytext NOT NULL,
  `comment_author_email` varchar(100) NOT NULL DEFAULT '',
  `comment_author_url` varchar(200) NOT NULL DEFAULT '',
  `comment_author_IP` varchar(100) NOT NULL DEFAULT '',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text NOT NULL,
  `comment_karma` int(11) NOT NULL DEFAULT '0',
  `comment_approved` varchar(20) NOT NULL DEFAULT '1',
  `comment_agent` varchar(255) NOT NULL DEFAULT '',
  `comment_type` varchar(20) NOT NULL DEFAULT '',
  `comment_parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_ID`),
  KEY `comment_post_ID` (`comment_post_ID`),
  KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`),
  KEY `comment_date_gmt` (`comment_date_gmt`),
  KEY `comment_parent` (`comment_parent`),
  KEY `comment_author_email` (`comment_author_email`(10))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_course_logs`
--

CREATE TABLE IF NOT EXISTS `pr_course_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `createdate` datetime NOT NULL,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `user_login` varchar(150) NOT NULL,
  `naps_id` bigint(20) DEFAULT '0',
  `v1` bigint(20) NOT NULL DEFAULT '0',
  `v2` bigint(20) NOT NULL DEFAULT '0',
  `lcurs1` varchar(150) NOT NULL DEFAULT '0',
  `lcurs2` varchar(150) NOT NULL DEFAULT '0',
  `curs1` varchar(150) NOT NULL DEFAULT '0',
  `curs2` varchar(150) NOT NULL DEFAULT '0',
  `who` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=48 ;

--
-- Дамп данных таблицы `pr_course_logs`
--

INSERT INTO `pr_course_logs` (`id`, `createdate`, `user_id`, `user_login`, `naps_id`, `v1`, `v2`, `lcurs1`, `lcurs2`, `curs1`, `curs2`, `who`) VALUES
(1, '2017-07-22 13:58:01', 1, 'superboss', 4, 6, 1, '1030', '16.96856573', '1030', '16.9685', 'editnaps'),
(2, '2017-07-22 13:58:26', 1, 'superboss', 4, 6, 1, '1030', '16.9685', '1030', '16.96856573', 'parser'),
(3, '2017-07-22 13:58:39', 1, 'superboss', 4, 6, 1, '1030', '16.9685', '1030', '16.96856573', 'parser'),
(4, '2017-07-22 13:59:02', 1, 'superboss', 4, 6, 1, '1030', '16.9685', '1030', '16.96856573', 'parser'),
(5, '2017-07-22 14:06:03', 1, 'superboss', 4, 6, 1, '1030', '16.9685', '1030', '16.96856573', 'parser'),
(6, '2017-07-22 14:06:03', 1, 'superboss', 5, 7, 1, '1002', '16.96856573', '1002', '16.9685', 'editnaps'),
(7, '2017-07-22 14:06:49', 1, 'superboss', 5, 7, 1, '1002', '16.9685', '1002', '16.96856573', 'parser'),
(8, '2017-07-22 14:07:11', 1, 'superboss', 5, 7, 1, '1002', '16.9685', '1002', '16.96856573', 'parser'),
(9, '2017-07-22 14:07:25', 1, 'superboss', 5, 7, 1, '1002', '16.9685', '1002', '16.96856573', 'parser'),
(10, '2017-07-22 14:08:29', 1, 'superboss', 5, 7, 1, '1002', '16.9685', '1002', '16.96856573', 'parser'),
(11, '2017-07-22 14:09:31', 1, 'superboss', 5, 7, 1, '1002', '16.9685', '1002', '16.96856573', 'parser'),
(12, '2017-07-22 14:10:41', 1, 'superboss', 5, 7, 1, '1002', '16.9685', '1002', '16.96856573', 'parser'),
(13, '2017-07-22 14:45:04', 1, 'superboss', 1, 1, 7, '1', '57.164525', '1', '57.1645', 'editnaps'),
(14, '2017-07-22 14:46:38', 1, 'superboss', 1, 1, 7, '1', '57.1645', '1', '57.164525', 'parser'),
(15, '2017-07-22 15:11:44', 1, 'superboss', 1, 1, 7, '1', '57.1645', '1', '57.164525', 'parser'),
(16, '2017-07-22 15:26:41', 1, 'superboss', 1, 1, 7, '1', '57.164525', '1', '57.1645', 'editnaps'),
(17, '2017-07-22 15:27:12', 1, 'superboss', 1, 1, 7, '1', '57.1645', '1', '57.164525', 'parser'),
(18, '2017-07-22 15:27:52', 1, 'superboss', 1, 1, 7, '1', '57.1645', '1', '57.164525', 'parser'),
(19, '2017-07-22 15:59:02', 1, 'superboss', 1, 1, 7, '1', '57.1645', '1', '57.164525', 'parser'),
(20, '2017-07-22 15:59:31', 1, 'superboss', 1, 1, 7, '1', '57.1645', '1', '57.164525', 'parser'),
(21, '2017-07-22 15:59:52', 1, 'superboss', 1, 1, 7, '1', '57.1645', '1', '57.164525', 'parser'),
(22, '2017-07-22 16:11:30', 1, 'superboss', 1, 1, 7, '1', '57.1645', '1', '57.164525', 'parser'),
(23, '2017-08-01 12:49:37', 0, '', 1, 1, 7, '1', '57.164525', '1', '58.050038', 'parser'),
(24, '2017-08-01 12:49:37', 0, '', 2, 1, 6, '1', '57.75385', '1', '58.648492', 'parser'),
(25, '2017-08-01 12:49:37', 0, '', 3, 1, 5, '100', '2515.04801', '100', '2507.91463', 'parser'),
(26, '2017-08-01 12:49:37', 0, '', 4, 6, 1, '1030', '16.96856573', '1030', '16.70972205', 'parser'),
(27, '2017-08-01 12:49:37', 0, '', 5, 7, 1, '1002', '16.96856573', '1002', '16.70972205', 'parser'),
(28, '2017-08-06 09:31:20', 0, '', 1, 1, 7, '1', '58.050038', '1', '58.518257', 'parser'),
(29, '2017-08-06 09:31:20', 0, '', 2, 1, 6, '1', '58.648492', '1', '59.121538', 'parser'),
(30, '2017-08-06 09:31:20', 0, '', 3, 1, 5, '100', '2507.91463', '100', '2510.91775', 'parser'),
(31, '2017-08-06 09:31:20', 0, '', 4, 6, 1, '1030', '16.70972205', '1030', '16.57602345', 'parser'),
(32, '2017-08-06 09:31:20', 0, '', 5, 7, 1, '1002', '16.70972205', '1002', '16.57602345', 'parser'),
(33, '2017-08-09 10:31:08', 0, '', 1, 1, 7, '1', '58.518257', '1', '58.188942', 'parser'),
(34, '2017-08-09 10:31:08', 0, '', 2, 1, 6, '1', '59.121538', '1', '58.788828', 'parser'),
(35, '2017-08-09 10:31:08', 0, '', 3, 1, 5, '100', '2510.91775', '100', '2496.05832', 'parser'),
(36, '2017-08-09 10:31:08', 0, '', 4, 6, 1, '1030', '16.57602345', '1030', '16.66983394', 'parser'),
(37, '2017-08-09 10:31:08', 0, '', 5, 7, 1, '1002', '16.57602345', '1002', '16.66983394', 'parser'),
(38, '2017-08-15 19:55:34', 0, '', 1, 1, 7, '1', '58.188942', '1', '58.128802', 'parser'),
(39, '2017-08-15 19:55:34', 0, '', 2, 1, 6, '1', '58.788828', '1', '58.728068', 'parser'),
(40, '2017-08-15 19:55:34', 0, '', 3, 1, 5, '100', '2496.05832', '100', '2486.17596', 'parser'),
(41, '2017-08-15 19:55:34', 0, '', 4, 6, 1, '1030', '16.66983394', '1030', '16.68708053', 'parser'),
(42, '2017-08-15 19:55:34', 0, '', 5, 7, 1, '1002', '16.66983394', '1002', '16.68708053', 'parser'),
(43, '2017-09-01 12:51:58', 0, '', 1, 1, 7, '1', '58.128802', '1', '56.314029', 'parser'),
(44, '2017-09-01 12:51:58', 0, '', 2, 1, 6, '1', '58.728068', '1', '56.894586', 'parser'),
(45, '2017-09-01 12:51:58', 0, '', 3, 1, 5, '100', '2486.17596', '100', '2492.31412', 'parser'),
(46, '2017-09-01 12:51:58', 0, '', 4, 6, 1, '1030', '16.68708053', '1030', '17.22483753', 'parser'),
(47, '2017-09-01 12:51:58', 0, '', 5, 7, 1, '1002', '16.68708053', '1002', '17.22483753', 'parser');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_custom_fields`
--

CREATE TABLE IF NOT EXISTS `pr_custom_fields` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cf_name` longtext NOT NULL,
  `vid` int(1) NOT NULL DEFAULT '0',
  `cf_req` int(1) NOT NULL DEFAULT '0',
  `minzn` int(2) NOT NULL DEFAULT '0',
  `maxzn` int(5) NOT NULL DEFAULT '100',
  `firstzn` varchar(20) NOT NULL,
  `helps` longtext NOT NULL,
  `cf_auto` varchar(250) NOT NULL,
  `datas` longtext NOT NULL,
  `status` int(2) NOT NULL DEFAULT '1',
  `cf_hidden` int(2) NOT NULL DEFAULT '0',
  `cf_order` bigint(20) NOT NULL DEFAULT '0',
  `tech_name` longtext NOT NULL,
  `uniqueid` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `pr_custom_fields`
--

INSERT INTO `pr_custom_fields` (`id`, `cf_name`, `vid`, `cf_req`, `minzn`, `maxzn`, `firstzn`, `helps`, `cf_auto`, `datas`, `status`, `cf_hidden`, `cf_order`, `tech_name`, `uniqueid`) VALUES
(1, '[ru_RU:]Фамилия[:ru_RU][en_US:]Surname[:en_US]', 0, 1, 0, 0, '', '[ru_RU:]Введите вашу фамилию как в паспорте[:ru_RU][en_US:]Enter your surname as in passport[:en_US]', 'last_name', '', 1, 0, 1, '[ru_RU:]Фамилия[:ru_RU][en_US:]Surname[:en_US]', ''),
(2, '[ru_RU:]Имя[:ru_RU][en_US:]Name[:en_US]', 0, 1, 0, 0, '', '[ru_RU:]Введите ваше имя как в паспорте[:ru_RU][en_US:]Enter your name as in passport[:en_US]', 'first_name', '', 1, 0, 2, '[ru_RU:]Имя[:ru_RU][en_US:]Name[:en_US]', ''),
(3, '[ru_RU:]Отчество[:ru_RU][en_US:]Middle name[:en_US]', 0, 1, 0, 0, '', '[ru_RU:]Введите ваше отчество как в паспорте[:ru_RU][en_US:]Enter your middle name as in passport[:en_US]', 'second_name', '', 1, 0, 3, '[ru_RU:]Отчество[:ru_RU][en_US:]Middle name[:en_US]', ''),
(4, '[ru_RU:]Телефон[:ru_RU][en_US:]Phone number[:en_US]', 0, 1, 0, 0, '', '[ru_RU:]Введите ваш номер телефона для связи[:ru_RU][en_US:]Enter your phone number[:en_US]', 'user_phone', '', 1, 3, 5, '[ru_RU:]Телефон[:ru_RU][en_US:]Phone number[:en_US]', ''),
(5, '[ru_RU:]Skype[:ru_RU][en_US:]Skype[:en_US]', 0, 1, 0, 0, '', '[ru_RU:]Введите ваш логин skype[:ru_RU][en_US:]Enter your skype login[:en_US]', 'user_skype', '', 1, 0, 6, '[ru_RU:]Skype[:ru_RU][en_US:]Skype[:en_US]', ''),
(6, '[ru_RU:]E-mail[:ru_RU][en_US:]E-mail[:en_US]', 0, 1, 0, 0, '', '[ru_RU:]Введите ваш e-mail[:ru_RU][en_US:]Enter your e-mail[:en_US]', 'user_email', '', 1, 0, 4, '[ru_RU:]E-mail[:ru_RU][en_US:]E-mail[:en_US]', ''),
(7, '[ru_RU:]Номер паспорта[:ru_RU][en_US:]Passport number[:en_US]', 0, 1, 0, 0, '', '[ru_RU:]Введите номер вашего паспорта[:ru_RU][en_US:]Enter number of your passport[:en_US]', 'user_passport', '', 1, 3, 7, '[ru_RU:]Номер паспорта[:ru_RU][en_US:]Passport number[:en_US]', '');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_custom_fields_valut`
--

CREATE TABLE IF NOT EXISTS `pr_custom_fields_valut` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cf_name` longtext NOT NULL,
  `vid` int(1) NOT NULL DEFAULT '0',
  `valut_id` bigint(20) NOT NULL DEFAULT '0',
  `cf_req` int(1) NOT NULL DEFAULT '0',
  `place_id` int(1) NOT NULL DEFAULT '0',
  `minzn` int(2) NOT NULL DEFAULT '0',
  `maxzn` int(5) NOT NULL DEFAULT '100',
  `firstzn` varchar(20) NOT NULL,
  `helps` longtext NOT NULL,
  `datas` longtext NOT NULL,
  `status` int(2) NOT NULL DEFAULT '1',
  `cf_hidden` int(2) NOT NULL DEFAULT '0',
  `cf_order` bigint(20) NOT NULL DEFAULT '0',
  `tech_name` longtext NOT NULL,
  `uniqueid` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_db_admin_logs`
--

CREATE TABLE IF NOT EXISTS `pr_db_admin_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) NOT NULL DEFAULT '0',
  `tbl_name` varchar(250) NOT NULL DEFAULT '0',
  `trans_type` int(1) NOT NULL DEFAULT '0',
  `trans_date` datetime NOT NULL,
  `old_data` longtext NOT NULL,
  `new_data` longtext NOT NULL,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `user_login` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_geoip_blackip`
--

CREATE TABLE IF NOT EXISTS `pr_geoip_blackip` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `theip` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_geoip_country`
--

CREATE TABLE IF NOT EXISTS `pr_geoip_country` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attr` varchar(20) NOT NULL,
  `title` longtext NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `temp_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_geoip_iplist`
--

CREATE TABLE IF NOT EXISTS `pr_geoip_iplist` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `before_cip` bigint(20) NOT NULL DEFAULT '0',
  `after_cip` bigint(20) NOT NULL DEFAULT '0',
  `before_ip` varchar(250) NOT NULL,
  `after_ip` varchar(250) NOT NULL,
  `country_attr` varchar(20) NOT NULL,
  `place_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_geoip_template`
--

CREATE TABLE IF NOT EXISTS `pr_geoip_template` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `temptitle` longtext NOT NULL,
  `title` longtext NOT NULL,
  `content` longtext NOT NULL,
  `default_temp` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_geoip_whiteip`
--

CREATE TABLE IF NOT EXISTS `pr_geoip_whiteip` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `theip` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_head_mess`
--

CREATE TABLE IF NOT EXISTS `pr_head_mess` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `h1` varchar(5) NOT NULL DEFAULT '0',
  `m1` varchar(5) NOT NULL DEFAULT '0',
  `h2` varchar(5) NOT NULL DEFAULT '0',
  `m2` varchar(5) NOT NULL DEFAULT '0',
  `d1` int(1) NOT NULL DEFAULT '0',
  `d2` int(1) NOT NULL DEFAULT '0',
  `d3` int(1) NOT NULL DEFAULT '0',
  `d4` int(1) NOT NULL DEFAULT '0',
  `d5` int(1) NOT NULL DEFAULT '0',
  `d6` int(1) NOT NULL DEFAULT '0',
  `d7` int(1) NOT NULL DEFAULT '0',
  `op_status` int(5) NOT NULL DEFAULT '-1',
  `url` longtext NOT NULL,
  `text` longtext NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `theclass` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_inex_change`
--

CREATE TABLE IF NOT EXISTS `pr_inex_change` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `meta_key` varchar(250) NOT NULL,
  `meta_key2` varchar(250) NOT NULL,
  `meta_value` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_inex_deposit`
--

CREATE TABLE IF NOT EXISTS `pr_inex_deposit` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `createdate` datetime NOT NULL,
  `indate` datetime NOT NULL,
  `enddate` datetime NOT NULL,
  `outdate` datetime NOT NULL,
  `couday` int(5) NOT NULL DEFAULT '0',
  `pers` varchar(250) NOT NULL,
  `insumm` varchar(250) NOT NULL DEFAULT '0',
  `outsumm` varchar(250) NOT NULL DEFAULT '0',
  `plussumm` varchar(250) NOT NULL DEFAULT '0',
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `user_login` varchar(250) NOT NULL,
  `user_email` varchar(250) NOT NULL,
  `user_schet` varchar(250) NOT NULL,
  `gid` varchar(250) NOT NULL,
  `gtitle` tinytext NOT NULL,
  `gvalut` varchar(250) NOT NULL,
  `paystatus` int(3) NOT NULL DEFAULT '0',
  `vipstatus` int(3) NOT NULL DEFAULT '0',
  `zakstatus` int(3) NOT NULL DEFAULT '0',
  `locale` varchar(20) NOT NULL,
  `mail1` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_inex_system`
--

CREATE TABLE IF NOT EXISTS `pr_inex_system` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` tinytext NOT NULL,
  `valut` varchar(250) NOT NULL,
  `gid` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_inex_tars`
--

CREATE TABLE IF NOT EXISTS `pr_inex_tars` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` tinytext NOT NULL,
  `minsum` varchar(250) NOT NULL DEFAULT '0',
  `maxsum` varchar(250) NOT NULL DEFAULT '0',
  `gid` varchar(250) NOT NULL,
  `gtitle` tinytext NOT NULL,
  `gvalut` varchar(250) NOT NULL,
  `mpers` varchar(250) NOT NULL,
  `cdays` bigint(20) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '1',
  `maxsumtar` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_links`
--

CREATE TABLE IF NOT EXISTS `pr_links` (
  `link_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `link_url` varchar(255) NOT NULL DEFAULT '',
  `link_name` varchar(255) NOT NULL DEFAULT '',
  `link_image` varchar(255) NOT NULL DEFAULT '',
  `link_target` varchar(25) NOT NULL DEFAULT '',
  `link_description` varchar(255) NOT NULL DEFAULT '',
  `link_visible` varchar(20) NOT NULL DEFAULT 'Y',
  `link_owner` bigint(20) unsigned NOT NULL DEFAULT '1',
  `link_rating` int(11) NOT NULL DEFAULT '0',
  `link_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link_rel` varchar(255) NOT NULL DEFAULT '',
  `link_notes` mediumtext NOT NULL,
  `link_rss` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`link_id`),
  KEY `link_visible` (`link_visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_login_check`
--

CREATE TABLE IF NOT EXISTS `pr_login_check` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `datelogin` datetime NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `user_login` varchar(250) NOT NULL,
  `user_ip` varchar(250) NOT NULL,
  `user_browser` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Дамп данных таблицы `pr_login_check`
--

INSERT INTO `pr_login_check` (`id`, `datelogin`, `user_id`, `user_login`, `user_ip`, `user_browser`) VALUES
(9, '2017-07-22 10:06:36', 1, 'superboss', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36'),
(10, '2017-07-22 10:48:53', 1, 'superboss', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36'),
(11, '2017-07-22 10:50:40', 1, 'superboss', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 YaBrowser/17.6.1.749 Yowser/2.5 Safari/537.36'),
(12, '2017-07-22 10:53:02', 1, 'superboss', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36'),
(13, '2017-08-01 12:51:02', 1, 'superboss', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36'),
(14, '2017-08-01 21:30:48', 1, 'superboss', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36'),
(15, '2017-08-06 09:31:44', 1, 'superboss', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36'),
(16, '2017-08-09 10:31:19', 1, 'superboss', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36'),
(17, '2017-08-15 19:56:10', 1, 'superboss', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36'),
(18, '2017-09-01 12:52:26', 1, 'superboss', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_maintrance`
--

CREATE TABLE IF NOT EXISTS `pr_maintrance` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `the_title` tinytext NOT NULL,
  `operator_status` varchar(150) NOT NULL DEFAULT '-1',
  `show_text` longtext NOT NULL,
  `for_whom` int(1) NOT NULL DEFAULT '0',
  `pages_law` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `pr_maintrance`
--

INSERT INTO `pr_maintrance` (`id`, `the_title`, `operator_status`, `show_text`, `for_whom`, `pages_law`) VALUES
(1, '[ru_RU:]Тех. обслуживание[:ru_RU]', '0', '[ru_RU:]Тех. обслуживание[:ru_RU]', 1, 'a:6:{s:4:"home";s:1:"2";s:8:"exchange";s:1:"2";s:2:"sm";s:1:"2";s:5:"files";s:1:"2";s:5:"smxml";s:1:"2";s:3:"tar";s:1:"2";}');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_masschange`
--

CREATE TABLE IF NOT EXISTS `pr_masschange` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` tinytext NOT NULL,
  `curs1` varchar(50) NOT NULL DEFAULT '0',
  `curs2` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_merchant_logs`
--

CREATE TABLE IF NOT EXISTS `pr_merchant_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `createdate` datetime NOT NULL,
  `mdata` longtext NOT NULL,
  `merchant` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_naps`
--

CREATE TABLE IF NOT EXISTS `pr_naps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `valut_id1` bigint(20) NOT NULL DEFAULT '0',
  `valut_id2` bigint(20) NOT NULL DEFAULT '0',
  `psys_id1` bigint(20) NOT NULL DEFAULT '0',
  `psys_id2` bigint(20) NOT NULL DEFAULT '0',
  `curs1` varchar(50) NOT NULL DEFAULT '0',
  `curs2` varchar(50) NOT NULL DEFAULT '0',
  `minsumm1` varchar(250) NOT NULL DEFAULT '0',
  `minsumm2` varchar(250) NOT NULL DEFAULT '0',
  `maxsumm1` varchar(250) NOT NULL DEFAULT '0',
  `maxsumm2` varchar(250) NOT NULL DEFAULT '0',
  `com_box_summ1` varchar(250) NOT NULL DEFAULT '0',
  `com_box_pers1` varchar(250) NOT NULL DEFAULT '0',
  `com_box_min1` varchar(250) NOT NULL DEFAULT '0',
  `com_box_summ2` varchar(250) NOT NULL DEFAULT '0',
  `com_box_pers2` varchar(250) NOT NULL DEFAULT '0',
  `com_box_min2` varchar(250) NOT NULL DEFAULT '0',
  `com_summ1` varchar(50) NOT NULL DEFAULT '0',
  `com_summ2` varchar(50) NOT NULL DEFAULT '0',
  `com_pers1` varchar(20) NOT NULL DEFAULT '0',
  `com_pers2` varchar(20) NOT NULL DEFAULT '0',
  `pay_com1` int(1) NOT NULL DEFAULT '0',
  `pay_com2` int(1) NOT NULL DEFAULT '0',
  `nscom1` int(1) NOT NULL DEFAULT '0',
  `nscom2` int(1) NOT NULL DEFAULT '0',
  `maxsumm1com` varchar(250) NOT NULL DEFAULT '0',
  `maxsumm2com` varchar(250) NOT NULL DEFAULT '0',
  `minsumm1com` varchar(50) NOT NULL DEFAULT '0',
  `minsumm2com` varchar(50) NOT NULL DEFAULT '0',
  `profit_summ1` varchar(50) NOT NULL DEFAULT '0',
  `profit_summ2` varchar(50) NOT NULL DEFAULT '0',
  `profit_pers1` varchar(20) NOT NULL DEFAULT '0',
  `profit_pers2` varchar(20) NOT NULL DEFAULT '0',
  `parser` bigint(20) NOT NULL DEFAULT '0',
  `nums1` varchar(50) NOT NULL DEFAULT '0',
  `elem1` int(2) NOT NULL DEFAULT '0',
  `nums2` varchar(50) NOT NULL DEFAULT '0',
  `elem2` int(2) NOT NULL DEFAULT '0',
  `masschange` bigint(20) NOT NULL DEFAULT '0',
  `mnums1` varchar(50) NOT NULL DEFAULT '0',
  `melem1` int(2) NOT NULL DEFAULT '0',
  `mnums2` varchar(50) NOT NULL DEFAULT '0',
  `melem2` int(2) NOT NULL DEFAULT '0',
  `max_user_sk` varchar(5) NOT NULL DEFAULT '50',
  `maxnaps` varchar(50) NOT NULL DEFAULT '0',
  `user_sk` int(1) NOT NULL DEFAULT '1',
  `not_country` longtext NOT NULL,
  `not_ip` longtext NOT NULL,
  `hidegost` int(1) NOT NULL DEFAULT '0',
  `naps_lang` longtext NOT NULL,
  `site_order1` bigint(20) NOT NULL DEFAULT '0',
  `site_order2` bigint(20) NOT NULL DEFAULT '0',
  `naps_status` int(2) NOT NULL DEFAULT '1',
  `m_in` varchar(150) NOT NULL DEFAULT '0',
  `m_out` varchar(150) NOT NULL DEFAULT '0',
  `naps_name` varchar(250) NOT NULL,
  `createdate` datetime NOT NULL,
  `autostatus` int(1) NOT NULL DEFAULT '1',
  `editdate` datetime NOT NULL,
  `show_file` int(1) NOT NULL DEFAULT '1',
  `com_summ1_check` varchar(50) NOT NULL DEFAULT '0',
  `com_summ2_check` varchar(50) NOT NULL DEFAULT '0',
  `com_pers1_check` varchar(20) NOT NULL DEFAULT '0',
  `com_pers2_check` varchar(20) NOT NULL DEFAULT '0',
  `check_purse` int(1) NOT NULL DEFAULT '0',
  `req_check_purse` int(1) NOT NULL DEFAULT '0',
  `to1` bigint(20) NOT NULL DEFAULT '0',
  `to2_1` bigint(20) NOT NULL DEFAULT '0',
  `to2_2` bigint(20) NOT NULL DEFAULT '0',
  `to3_1` bigint(20) NOT NULL DEFAULT '0',
  `maxexip` bigint(20) NOT NULL DEFAULT '0',
  `xml_city` varchar(150) NOT NULL,
  `xml_manual` int(1) NOT NULL DEFAULT '0',
  `xml_juridical` int(1) NOT NULL DEFAULT '0',
  `xml_show1` varchar(50) NOT NULL,
  `xml_show2` varchar(50) NOT NULL,
  `tech_name` longtext NOT NULL,
  `mobile` int(1) NOT NULL DEFAULT '0',
  `only_country` longtext NOT NULL,
  `naps_reserv` varchar(250) NOT NULL DEFAULT '0',
  `reserv_place` varchar(250) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `pr_naps`
--

INSERT INTO `pr_naps` (`id`, `valut_id1`, `valut_id2`, `psys_id1`, `psys_id2`, `curs1`, `curs2`, `minsumm1`, `minsumm2`, `maxsumm1`, `maxsumm2`, `com_box_summ1`, `com_box_pers1`, `com_box_min1`, `com_box_summ2`, `com_box_pers2`, `com_box_min2`, `com_summ1`, `com_summ2`, `com_pers1`, `com_pers2`, `pay_com1`, `pay_com2`, `nscom1`, `nscom2`, `maxsumm1com`, `maxsumm2com`, `minsumm1com`, `minsumm2com`, `profit_summ1`, `profit_summ2`, `profit_pers1`, `profit_pers2`, `parser`, `nums1`, `elem1`, `nums2`, `elem2`, `masschange`, `mnums1`, `melem1`, `mnums2`, `melem2`, `max_user_sk`, `maxnaps`, `user_sk`, `not_country`, `not_ip`, `hidegost`, `naps_lang`, `site_order1`, `site_order2`, `naps_status`, `m_in`, `m_out`, `naps_name`, `createdate`, `autostatus`, `editdate`, `show_file`, `com_summ1_check`, `com_summ2_check`, `com_pers1_check`, `com_pers2_check`, `check_purse`, `req_check_purse`, `to1`, `to2_1`, `to2_2`, `to3_1`, `maxexip`, `xml_city`, `xml_manual`, `xml_juridical`, `xml_show1`, `xml_show2`, `tech_name`, `mobile`, `only_country`, `naps_reserv`, `reserv_place`) VALUES
(1, 1, 7, 2, 6, '1', '56.314029', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2', '1', 0, 0, 0, 0, '0', '0', '0', '0', '0', '0', '0', '0', 1, '0', 0, '-3', 1, 0, '0', 0, '0', 0, '0', '0', 1, '', '', 0, '[d]ru_RU[/d][d]en_US[/d]', 0, 0, 1, '0', '0', 'PMUSD_to_SBERRUB', '0000-00-00 00:00:00', 1, '2017-09-01 12:51:58', 1, '0', '0', '0.5', '1', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '0:0', '0:0', 'Perfect Money USD → Сбербанк RUB', 0, '', '0', '0'),
(2, 1, 6, 2, 3, '1', '56.894586', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2', '0.5', 0, 0, 0, 0, '0', '0', '0', '0', '0', '0', '0', '0', 1, '0', 0, '-2', 1, 0, '0', 0, '0', 0, '0', '0', 1, '', '', 0, '[d]ru_RU[/d][d]en_US[/d]', 0, 0, 1, '0', '0', 'PMUSD_to_YAMRUB', '0000-00-00 00:00:00', 1, '2017-09-01 12:51:58', 1, '0', '0', '0.5', '0.5', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 'Perfect Money USD &rarr; Яндекс.Деньги RUB', 0, '', '0', '0'),
(3, 1, 5, 2, 5, '100', '2492.31412', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2', '0', 0, 0, 0, 0, '0', '0', '0', '0', '0', '0', '0', '0', 101, '0', 0, '-3', 1, 0, '0', 0, '0', 0, '0', '0', 1, '', '', 0, '[d]ru_RU[/d][d]en_US[/d]', 0, 0, 1, '0', '0', 'PMUSD_to_P24UAH', '0000-00-00 00:00:00', 1, '2017-09-01 12:51:58', 1, '0', '0', '0.5', '0', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '', '', 'Perfect Money USD &rarr; Приват24 UAH', 0, '', '0', '0'),
(4, 6, 1, 3, 2, '1030', '17.22483753', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0.5', '2', 0, 0, 0, 0, '0', '0', '0', '0', '0', '0', '0', '0', 2, '3', 1, '0', 0, 0, '0', 0, '0', 0, '0', '0', 1, '', '', 0, '[d]ru_RU[/d][d]en_US[/d]', 0, 0, 1, '0', '0', 'YAMRUB_to_PMUSD', '0000-00-00 00:00:00', 1, '2017-09-01 12:51:58', 1, '0', '0', '0.5', '0.5', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '0:0', '0:0', 'Яндекс.Деньги RUB → Perfect Money USD', 0, '', '0', '1'),
(5, 7, 1, 6, 2, '1002', '17.22483753', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '2', 0, 0, 0, 0, '0', '0', '0', '0', '0', '0', '0', '0', 2, '2', 0, '0', 0, 0, '0', 0, '0', 0, '0', '0', 1, '', '', 0, '[d]ru_RU[/d][d]en_US[/d]', 0, 0, 1, '0', '0', 'SBERRUB_to_PMUSD', '0000-00-00 00:00:00', 1, '2017-09-01 12:51:58', 1, '0', '0', '1', '0.5', 0, 0, 0, 0, 0, 0, 0, '', 0, 0, '0:0', '0:0', 'Сбербанк RUB → Perfect Money USD', 0, '', '0', '0');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_naps_meta`
--

CREATE TABLE IF NOT EXISTS `pr_naps_meta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) NOT NULL DEFAULT '0',
  `meta_key` longtext NOT NULL,
  `meta_value` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=117 ;

--
-- Дамп данных таблицы `pr_naps_meta`
--

INSERT INTO `pr_naps_meta` (`id`, `item_id`, `meta_key`, `meta_value`) VALUES
(1, 1, 'seo_exch_title', ''),
(2, 1, 'seo_title', ''),
(3, 1, 'seo_key', ''),
(4, 1, 'seo_descr', ''),
(5, 1, 'ogp_title', ''),
(6, 1, 'ogp_descr', ''),
(7, 1, 'p_enable', '1'),
(8, 1, 'p_pers', '0'),
(9, 1, 'p_max', '0'),
(10, 2, 'seo_exch_title', ''),
(11, 2, 'seo_title', ''),
(12, 2, 'seo_key', ''),
(13, 2, 'seo_descr', ''),
(14, 2, 'ogp_title', ''),
(15, 2, 'ogp_descr', ''),
(16, 2, 'p_enable', '1'),
(17, 2, 'p_pers', '0'),
(18, 2, 'p_max', '0'),
(19, 3, 'seo_exch_title', ''),
(20, 3, 'seo_title', ''),
(21, 3, 'seo_key', ''),
(22, 3, 'seo_descr', ''),
(23, 3, 'ogp_title', ''),
(24, 3, 'ogp_descr', ''),
(25, 3, 'p_enable', '1'),
(26, 3, 'p_pers', '0'),
(27, 3, 'p_max', '0'),
(28, 4, 'seo_exch_title', ''),
(29, 4, 'seo_title', ''),
(30, 4, 'seo_key', ''),
(31, 4, 'seo_descr', ''),
(32, 4, 'ogp_title', ''),
(33, 4, 'ogp_descr', ''),
(34, 4, 'p_enable', '1'),
(35, 4, 'p_pers', '0'),
(36, 4, 'p_max', '0'),
(37, 5, 'seo_exch_title', ''),
(38, 5, 'seo_title', ''),
(39, 5, 'seo_key', ''),
(40, 5, 'seo_descr', ''),
(41, 5, 'ogp_title', ''),
(42, 5, 'ogp_descr', ''),
(43, 5, 'p_enable', '1'),
(44, 5, 'p_pers', '0'),
(45, 5, 'p_max', '0'),
(46, 5, 'p_ind_sum', '0'),
(47, 5, 'p_min_sum', '0'),
(48, 5, 'p_max_sum', '0'),
(49, 4, 'p_ind_sum', '0'),
(50, 4, 'p_min_sum', '0'),
(51, 4, 'p_max_sum', '0'),
(52, 3, 'p_ind_sum', '0'),
(53, 3, 'p_min_sum', '0'),
(54, 3, 'p_max_sum', '0'),
(55, 2, 'p_ind_sum', '0'),
(56, 2, 'p_min_sum', '0'),
(57, 2, 'p_max_sum', '0'),
(58, 1, 'p_ind_sum', '0'),
(59, 1, 'p_min_sum', '0'),
(60, 1, 'p_max_sum', '0'),
(61, 8, 'verify_account', '0'),
(62, 8, 'enable_naps_identy', '0'),
(63, 8, 'naps_identy_text', ''),
(64, 8, 'sms_button', '0'),
(65, 8, 'sms_button_verify', '0'),
(66, 8, 'p_enable', '1'),
(67, 8, 'p_pers', '0'),
(68, 8, 'p_max', '0'),
(69, 8, 'p_ind_sum', '0'),
(70, 8, 'p_min_sum', '0'),
(71, 8, 'p_max_sum', '0'),
(72, 8, 'seo_exch_title', ''),
(73, 8, 'seo_title', ''),
(74, 8, 'seo_key', ''),
(75, 8, 'seo_descr', ''),
(76, 8, 'ogp_title', ''),
(77, 8, 'ogp_descr', ''),
(78, 8, 'verify', '0'),
(79, 8, 'x19mod', '0'),
(80, 8, 'paymerch_data', 'a:2:{s:13:"m_out_realpay";i:0;s:12:"m_out_verify";i:0;}'),
(81, 4, 'verify_account', '0'),
(82, 4, 'email_button', '0'),
(83, 4, 'email_button_verify', '0'),
(84, 4, 'enable_naps_identy', '0'),
(85, 4, 'naps_identy_text', ''),
(86, 4, 'sms_button', '0'),
(87, 4, 'sms_button_verify', '0'),
(88, 4, 'sb_gb', 'a:5:{i:1;a:6:{s:4:"sum1";i:0;s:2:"s1";i:0;s:2:"b1";i:0;s:4:"sum2";i:0;s:2:"s2";i:0;s:2:"b2";i:0;}i:2;a:6:{s:4:"sum1";i:0;s:2:"s1";i:0;s:2:"b1";i:0;s:4:"sum2";i:0;s:2:"s2";i:0;s:2:"b2";i:0;}i:3;a:6:{s:4:"sum1";i:0;s:2:"s1";i:0;s:2:"b1";i:0;s:4:"sum2";i:0;s:2:"s2";i:0;s:2:"b2";i:0;}i:4;a:6:{s:4:"sum1";i:0;s:2:"s1";i:0;s:2:"b1";i:0;s:4:"sum2";i:0;s:2:"s2";i:0;s:2:"b2";i:0;}i:5;a:6:{s:4:"sum1";i:0;s:2:"s1";i:0;s:2:"b1";i:0;s:4:"sum2";i:0;s:2:"s2";i:0;s:2:"b2";i:0;}}'),
(89, 4, 'verify', '0'),
(90, 4, 'verify_sum', '0'),
(91, 4, 'x19mod', '0'),
(92, 4, 'paymerch_data', 'a:6:{s:13:"m_out_realpay";i:0;s:12:"m_out_verify";i:0;s:9:"m_out_max";i:0;s:13:"m_out_max_sum";i:0;s:13:"m_out_timeout";i:0;s:18:"m_out_timeout_user";i:0;}'),
(93, 5, 'verify_account', '0'),
(94, 5, 'email_button', '0'),
(95, 5, 'email_button_verify', '0'),
(96, 5, 'enable_naps_identy', '0'),
(97, 5, 'naps_identy_text', ''),
(98, 5, 'sms_button', '0'),
(99, 5, 'sms_button_verify', '0'),
(100, 5, 'sb_gb', 'a:5:{i:1;a:6:{s:4:"sum1";i:0;s:2:"s1";i:0;s:2:"b1";i:0;s:4:"sum2";i:0;s:2:"s2";i:0;s:2:"b2";i:0;}i:2;a:6:{s:4:"sum1";i:0;s:2:"s1";i:0;s:2:"b1";i:0;s:4:"sum2";i:0;s:2:"s2";i:0;s:2:"b2";i:0;}i:3;a:6:{s:4:"sum1";i:0;s:2:"s1";i:0;s:2:"b1";i:0;s:4:"sum2";i:0;s:2:"s2";i:0;s:2:"b2";i:0;}i:4;a:6:{s:4:"sum1";i:0;s:2:"s1";i:0;s:2:"b1";i:0;s:4:"sum2";i:0;s:2:"s2";i:0;s:2:"b2";i:0;}i:5;a:6:{s:4:"sum1";i:0;s:2:"s1";i:0;s:2:"b1";i:0;s:4:"sum2";i:0;s:2:"s2";i:0;s:2:"b2";i:0;}}'),
(101, 5, 'verify', '0'),
(102, 5, 'verify_sum', '0'),
(103, 5, 'x19mod', '0'),
(104, 5, 'paymerch_data', 'a:6:{s:13:"m_out_realpay";i:0;s:12:"m_out_verify";i:0;s:9:"m_out_max";i:0;s:13:"m_out_max_sum";i:0;s:13:"m_out_timeout";i:0;s:18:"m_out_timeout_user";i:0;}'),
(105, 1, 'verify_account', '0'),
(106, 1, 'email_button', '0'),
(107, 1, 'email_button_verify', '0'),
(108, 1, 'enable_naps_identy', '0'),
(109, 1, 'naps_identy_text', ''),
(110, 1, 'sms_button', '0'),
(111, 1, 'sms_button_verify', '0'),
(112, 1, 'sb_gb', 'a:5:{i:1;a:6:{s:4:"sum1";i:0;s:2:"s1";i:0;s:2:"b1";i:0;s:4:"sum2";i:0;s:2:"s2";i:0;s:2:"b2";i:0;}i:2;a:6:{s:4:"sum1";i:0;s:2:"s1";i:0;s:2:"b1";i:0;s:4:"sum2";i:0;s:2:"s2";i:0;s:2:"b2";i:0;}i:3;a:6:{s:4:"sum1";i:0;s:2:"s1";i:0;s:2:"b1";i:0;s:4:"sum2";i:0;s:2:"s2";i:0;s:2:"b2";i:0;}i:4;a:6:{s:4:"sum1";i:0;s:2:"s1";i:0;s:2:"b1";i:0;s:4:"sum2";i:0;s:2:"s2";i:0;s:2:"b2";i:0;}i:5;a:6:{s:4:"sum1";i:0;s:2:"s1";i:0;s:2:"b1";i:0;s:4:"sum2";i:0;s:2:"s2";i:0;s:2:"b2";i:0;}}'),
(113, 1, 'verify', '0'),
(114, 1, 'verify_sum', '0'),
(115, 1, 'x19mod', '0'),
(116, 1, 'paymerch_data', 'a:6:{s:13:"m_out_realpay";i:0;s:12:"m_out_verify";i:0;s:9:"m_out_max";i:0;s:13:"m_out_max_sum";i:0;s:13:"m_out_timeout";i:0;s:18:"m_out_timeout_user";i:0;}');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_naps_order`
--

CREATE TABLE IF NOT EXISTS `pr_naps_order` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `naps_id` bigint(20) NOT NULL DEFAULT '0',
  `v_id` bigint(20) NOT NULL DEFAULT '0',
  `order1` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

--
-- Дамп данных таблицы `pr_naps_order`
--

INSERT INTO `pr_naps_order` (`id`, `naps_id`, `v_id`, `order1`) VALUES
(1, 1, 1, 0),
(2, 2, 1, 0),
(3, 3, 1, 0),
(4, 4, 1, 0),
(5, 5, 1, 0),
(6, 1, 2, 0),
(7, 2, 2, 0),
(8, 3, 2, 0),
(9, 4, 2, 0),
(10, 5, 2, 0),
(11, 1, 3, 0),
(12, 2, 3, 0),
(13, 3, 3, 0),
(14, 4, 3, 0),
(15, 5, 3, 0),
(16, 1, 4, 0),
(17, 2, 4, 0),
(18, 3, 4, 0),
(19, 4, 4, 0),
(20, 5, 4, 0),
(21, 1, 5, 0),
(22, 2, 5, 0),
(23, 3, 5, 0),
(24, 4, 5, 0),
(25, 5, 5, 0),
(26, 1, 6, 0),
(27, 2, 6, 0),
(28, 3, 6, 0),
(29, 4, 6, 0),
(30, 5, 6, 0),
(31, 1, 7, 0),
(32, 2, 7, 0),
(33, 3, 7, 0),
(34, 4, 7, 0),
(35, 5, 7, 0),
(36, 1, 8, 0),
(37, 2, 8, 0),
(38, 3, 8, 0),
(39, 4, 8, 0),
(40, 5, 8, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `pr_naps_reservcurs`
--

CREATE TABLE IF NOT EXISTS `pr_naps_reservcurs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `naps_id` bigint(20) NOT NULL DEFAULT '0',
  `sum_val` varchar(50) NOT NULL DEFAULT '0',
  `curs1` varchar(50) NOT NULL DEFAULT '0',
  `curs2` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_naps_sumcomis`
--

CREATE TABLE IF NOT EXISTS `pr_naps_sumcomis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `naps_id` bigint(20) NOT NULL DEFAULT '0',
  `sum_val` varchar(150) NOT NULL DEFAULT '0',
  `com_pers1` varchar(150) NOT NULL DEFAULT '0',
  `com_summ1` varchar(150) NOT NULL DEFAULT '0',
  `com_pers1_check` varchar(150) NOT NULL DEFAULT '0',
  `com_summ1_check` varchar(150) NOT NULL DEFAULT '0',
  `com_pers2` varchar(150) NOT NULL DEFAULT '0',
  `com_summ2` varchar(150) NOT NULL DEFAULT '0',
  `com_pers2_check` varchar(150) NOT NULL DEFAULT '0',
  `com_summ2_check` varchar(150) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_naps_sumcurs`
--

CREATE TABLE IF NOT EXISTS `pr_naps_sumcurs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `naps_id` bigint(20) NOT NULL DEFAULT '0',
  `sum_val` varchar(50) NOT NULL DEFAULT '0',
  `curs1` varchar(50) NOT NULL DEFAULT '0',
  `curs2` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_operator_schedules`
--

CREATE TABLE IF NOT EXISTS `pr_operator_schedules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `status` int(5) NOT NULL DEFAULT '0',
  `h1` varchar(5) NOT NULL DEFAULT '0',
  `m1` varchar(5) NOT NULL DEFAULT '0',
  `h2` varchar(5) NOT NULL DEFAULT '0',
  `m2` varchar(5) NOT NULL DEFAULT '0',
  `d1` int(1) NOT NULL DEFAULT '0',
  `d2` int(1) NOT NULL DEFAULT '0',
  `d3` int(1) NOT NULL DEFAULT '0',
  `d4` int(1) NOT NULL DEFAULT '0',
  `d5` int(1) NOT NULL DEFAULT '0',
  `d6` int(1) NOT NULL DEFAULT '0',
  `d7` int(1) NOT NULL DEFAULT '0',
  `save_order` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_options`
--

CREATE TABLE IF NOT EXISTS `pr_options` (
  `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(191) DEFAULT NULL,
  `option_value` longtext NOT NULL,
  `autoload` varchar(20) NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=430 ;

--
-- Дамп данных таблицы `pr_options`
--

INSERT INTO `pr_options` (`option_id`, `option_name`, `option_value`, `autoload`) VALUES
(1, 'siteurl', 'http://premiumexchanger.ru', 'yes'),
(2, 'home', 'http://premiumexchanger.ru', 'yes'),
(3, 'blogname', '[ru_RU:]Обменный пункт электронных валют[:ru_RU][en_US:]Electronic currencies exchanger[:en_US]', 'yes'),
(4, 'blogdescription', '[ru_RU:]Обменный пункт электронных валют[:ru_RU][en_US:]Electronic currencies exchanger[:en_US]', 'yes'),
(5, 'users_can_register', '0', 'yes'),
(6, 'admin_email', 'info@premium.ru', 'yes'),
(7, 'start_of_week', '1', 'yes'),
(8, 'use_balanceTags', '0', 'yes'),
(9, 'use_smilies', '', 'yes'),
(10, 'require_name_email', '1', 'yes'),
(11, 'comments_notify', '0', 'yes'),
(12, 'posts_per_rss', '5', 'yes'),
(13, 'rss_use_excerpt', '1', 'yes'),
(14, 'mailserver_url', 'mail.example.com', 'yes'),
(15, 'mailserver_login', 'login@example.com', 'yes'),
(16, 'mailserver_pass', 'password', 'yes'),
(17, 'mailserver_port', '110', 'yes'),
(18, 'default_category', '1', 'yes'),
(19, 'default_comment_status', 'open', 'yes'),
(20, 'default_ping_status', 'closed', 'yes'),
(21, 'default_pingback_flag', '0', 'yes'),
(22, 'posts_per_page', '10', 'yes'),
(23, 'date_format', 'd.m.Y', 'yes'),
(24, 'time_format', 'H:i', 'yes'),
(25, 'links_updated_date_format', 'd.m.Y H:i', 'yes'),
(26, 'comment_moderation', '1', 'yes'),
(27, 'moderation_notify', '0', 'yes'),
(28, 'permalink_structure', '/%postname%/', 'yes'),
(30, 'hack_file', '0', 'yes'),
(31, 'blog_charset', 'UTF-8', 'yes'),
(32, 'moderation_keys', '', 'no'),
(33, 'active_plugins', 'a:1:{i:0;s:25:"premiumbox/premiumbox.php";}', 'yes'),
(34, 'category_base', '', 'yes'),
(35, 'ping_sites', 'http://rpc.pingomatic.com/', 'yes'),
(37, 'comment_max_links', '2', 'yes'),
(38, 'gmt_offset', '3', 'yes'),
(39, 'default_email_category', '1', 'yes'),
(40, 'recently_edited', '', 'no'),
(41, 'template', 'exchanger', 'yes'),
(42, 'stylesheet', 'exchanger', 'yes'),
(43, 'comment_whitelist', '1', 'yes'),
(44, 'blacklist_keys', '', 'no'),
(45, 'comment_registration', '0', 'yes'),
(46, 'html_type', 'text/html', 'yes'),
(47, 'use_trackback', '0', 'yes'),
(48, 'default_role', 'users', 'yes'),
(49, 'db_version', '38590', 'yes'),
(50, 'uploads_use_yearmonth_folders', '', 'yes'),
(51, 'upload_path', '', 'yes'),
(52, 'blog_public', '1', 'yes'),
(53, 'default_link_category', '2', 'yes'),
(54, 'show_on_front', 'page', 'yes'),
(55, 'tag_base', '', 'yes'),
(56, 'show_avatars', '1', 'yes'),
(57, 'avatar_rating', 'G', 'yes'),
(58, 'upload_url_path', '', 'yes'),
(59, 'thumbnail_size_w', '150', 'yes'),
(60, 'thumbnail_size_h', '150', 'yes'),
(61, 'thumbnail_crop', '1', 'yes'),
(62, 'medium_size_w', '300', 'yes'),
(63, 'medium_size_h', '300', 'yes'),
(64, 'avatar_default', 'mystery', 'yes'),
(65, 'large_size_w', '1024', 'yes'),
(66, 'large_size_h', '1024', 'yes'),
(67, 'image_default_link_type', 'file', 'yes'),
(68, 'image_default_size', '', 'yes'),
(69, 'image_default_align', '', 'yes'),
(70, 'close_comments_for_old_posts', '0', 'yes'),
(71, 'close_comments_days_old', '14', 'yes'),
(72, 'thread_comments', '1', 'yes'),
(73, 'thread_comments_depth', '5', 'yes'),
(74, 'page_comments', '0', 'yes'),
(75, 'comments_per_page', '50', 'yes'),
(76, 'default_comments_page', 'newest', 'yes'),
(77, 'comment_order', 'asc', 'yes'),
(78, 'sticky_posts', 'a:0:{}', 'yes'),
(79, 'widget_categories', 'a:2:{i:2;a:4:{s:5:"title";s:0:"";s:5:"count";i:0;s:12:"hierarchical";i:0;s:8:"dropdown";i:0;}s:12:"_multiwidget";i:1;}', 'yes'),
(80, 'widget_text', 'a:0:{}', 'yes'),
(81, 'widget_rss', 'a:0:{}', 'yes'),
(82, 'uninstall_plugins', 'a:1:{s:26:"wp-security-scan/index.php";a:2:{i:0;s:9:"WsdPlugin";i:1;s:9:"uninstall";}}', 'no'),
(83, 'timezone_string', '', 'yes'),
(84, 'page_for_posts', '5', 'yes'),
(85, 'page_on_front', '4', 'yes'),
(86, 'default_post_format', '0', 'yes'),
(87, 'link_manager_enabled', '0', 'yes'),
(88, 'finished_splitting_shared_terms', '1', 'yes'),
(89, 'initial_db_version', '33056', 'yes'),
(90, 'pr_user_roles', 'a:4:{s:13:"administrator";a:2:{s:4:"name";s:13:"Administrator";s:12:"capabilities";a:61:{s:13:"switch_themes";b:1;s:11:"edit_themes";b:1;s:16:"activate_plugins";b:1;s:12:"edit_plugins";b:1;s:10:"edit_users";b:1;s:10:"edit_files";b:1;s:14:"manage_options";b:1;s:17:"moderate_comments";b:1;s:17:"manage_categories";b:1;s:12:"manage_links";b:1;s:12:"upload_files";b:1;s:6:"import";b:1;s:15:"unfiltered_html";b:1;s:10:"edit_posts";b:1;s:17:"edit_others_posts";b:1;s:20:"edit_published_posts";b:1;s:13:"publish_posts";b:1;s:10:"edit_pages";b:1;s:4:"read";b:1;s:8:"level_10";b:1;s:7:"level_9";b:1;s:7:"level_8";b:1;s:7:"level_7";b:1;s:7:"level_6";b:1;s:7:"level_5";b:1;s:7:"level_4";b:1;s:7:"level_3";b:1;s:7:"level_2";b:1;s:7:"level_1";b:1;s:7:"level_0";b:1;s:17:"edit_others_pages";b:1;s:20:"edit_published_pages";b:1;s:13:"publish_pages";b:1;s:12:"delete_pages";b:1;s:19:"delete_others_pages";b:1;s:22:"delete_published_pages";b:1;s:12:"delete_posts";b:1;s:19:"delete_others_posts";b:1;s:22:"delete_published_posts";b:1;s:20:"delete_private_posts";b:1;s:18:"edit_private_posts";b:1;s:18:"read_private_posts";b:1;s:20:"delete_private_pages";b:1;s:18:"edit_private_pages";b:1;s:18:"read_private_pages";b:1;s:12:"delete_users";b:1;s:12:"create_users";b:1;s:17:"unfiltered_upload";b:1;s:14:"edit_dashboard";b:1;s:14:"update_plugins";b:1;s:14:"delete_plugins";b:1;s:15:"install_plugins";b:1;s:13:"update_themes";b:1;s:14:"install_themes";b:1;s:11:"update_core";b:1;s:10:"list_users";b:1;s:12:"remove_users";b:1;s:13:"promote_users";b:1;s:18:"edit_theme_options";b:1;s:13:"delete_themes";b:1;s:6:"export";b:1;}}s:10:"topmeneger";a:2:{s:4:"name";s:10:"topmeneger";s:12:"capabilities";a:0:{}}s:7:"meneger";a:2:{s:4:"name";s:7:"meneger";s:12:"capabilities";a:0:{}}s:5:"users";a:2:{s:4:"name";s:5:"users";s:12:"capabilities";a:0:{}}}', 'yes'),
(91, 'WPLANG', 'ru_RU', 'yes'),
(92, 'widget_search', 'a:2:{i:2;a:1:{s:5:"title";s:0:"";}s:12:"_multiwidget";i:1;}', 'yes'),
(93, 'widget_recent-posts', 'a:2:{i:2;a:2:{s:5:"title";s:0:"";s:6:"number";i:5;}s:12:"_multiwidget";i:1;}', 'yes'),
(94, 'widget_recent-comments', 'a:2:{i:2;a:2:{s:5:"title";s:0:"";s:6:"number";i:5;}s:12:"_multiwidget";i:1;}', 'yes'),
(95, 'widget_archives', 'a:2:{i:2;a:3:{s:5:"title";s:0:"";s:5:"count";i:0;s:8:"dropdown";i:0;}s:12:"_multiwidget";i:1;}', 'yes'),
(96, 'widget_meta', 'a:2:{i:2;a:1:{s:5:"title";s:0:"";}s:12:"_multiwidget";i:1;}', 'yes'),
(97, 'sidebars_widgets', 'a:3:{s:19:"wp_inactive_widgets";a:0:{}s:17:"unique-sidebar-id";a:3:{i:0;s:14:"get_pn_login-3";i:1;s:11:"get_pn_lk-2";i:2;s:16:"get_pn_reviews-2";}s:13:"array_version";i:3;}', 'yes'),
(99, 'cron', 'a:4:{i:1504275905;a:3:{s:16:"wp_version_check";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:10:"twicedaily";s:4:"args";a:0:{}s:8:"interval";i:43200;}}s:17:"wp_update_plugins";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:10:"twicedaily";s:4:"args";a:0:{}s:8:"interval";i:43200;}}s:16:"wp_update_themes";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:10:"twicedaily";s:4:"args";a:0:{}s:8:"interval";i:43200;}}}i:1504276221;a:1:{s:19:"wp_scheduled_delete";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:5:"daily";s:4:"args";a:0:{}s:8:"interval";i:86400;}}}i:1504294396;a:1:{s:30:"wp_scheduled_auto_draft_delete";a:1:{s:32:"40cd750bba9870f18aada2478b24840a";a:3:{s:8:"schedule";s:5:"daily";s:4:"args";a:0:{}s:8:"interval";i:86400;}}}s:7:"version";i:2;}', 'yes'),
(110, '_transient_random_seed', '3350443e7f9bd30bc9deb22934d7df29', 'yes'),
(112, '_site_transient_update_plugins', 'O:8:"stdClass":4:{s:12:"last_checked";i:1504259523;s:8:"response";a:0:{}s:12:"translations";a:0:{}s:9:"no_update";a:0:{}}', 'no'),
(113, '_site_transient_update_themes', 'O:8:"stdClass":4:{s:12:"last_checked";i:1504259525;s:7:"checked";a:1:{s:9:"exchanger";s:3:"2.0";}s:8:"response";a:0:{}s:12:"translations";a:0:{}}', 'no'),
(123, '_transient_timeout_feed_d117b5738fbd35bd8c0391cda1f2b5d9', '1445567124', 'no'),
(134, 'recently_activated', 'a:1:{s:23:"investbox/investbox.php";i:1501582355;}', 'yes'),
(136, 'theme_mods_twentyfifteen', 'a:1:{s:16:"sidebars_widgets";a:2:{s:4:"time";i:1445524265;s:4:"data";a:2:{s:19:"wp_inactive_widgets";a:0:{}s:9:"sidebar-1";a:6:{i:0;s:8:"search-2";i:1;s:14:"recent-posts-2";i:2;s:17:"recent-comments-2";i:3;s:10:"archives-2";i:4;s:12:"categories-2";i:5;s:6:"meta-2";}}}}', 'yes'),
(137, 'current_theme', 'Premium Exchanger Theme', 'yes'),
(138, 'theme_mods_exchanger', 'a:3:{i:0;b:0;s:18:"nav_menu_locations";a:8:{s:8:"top_menu";i:2;s:11:"bottom_menu";i:3;s:13:"top_menu_user";i:2;s:15:"mobile_top_menu";i:4;s:20:"mobile_top_menu_user";i:4;s:12:"the_top_menu";i:2;s:17:"the_top_menu_user";i:2;s:15:"the_bottom_menu";i:3;}s:18:"custom_css_post_id";i:-1;}', 'yes'),
(139, 'theme_switched', '', 'yes'),
(140, 'first_pn', '1', 'yes'),
(141, 'the_pages', 'a:35:{s:4:"home";i:4;s:4:"news";i:5;s:3:"tos";i:6;s:6:"notice";i:7;s:11:"partnersfaq";i:8;s:8:"feedback";i:10;s:5:"login";i:11;s:8:"register";i:12;s:8:"lostpass";i:13;s:7:"account";i:14;s:8:"security";i:15;s:7:"sitemap";i:16;s:6:"tarifs";i:17;s:7:"reviews";i:18;s:11:"userwallets";i:19;s:10:"userverify";i:20;s:7:"userxch";i:21;s:8:"exchange";i:206;s:12:"exchangestep";i:23;s:8:"paccount";i:24;s:11:"promotional";i:25;s:5:"pexch";i:26;s:6:"plinks";i:27;s:9:"preferals";i:28;s:7:"payouts";i:29;s:6:"domacc";i:90;s:2:"ex";i:146;s:3:"hst";i:181;s:12:"bonusarchive";i:184;s:12:"bonuspayouts";i:185;s:7:"support";i:186;s:7:"reservs";i:187;s:5:"terms";i:200;i:0;i:182;s:11:"checkstatus";i:183;}', 'yes'),
(143, 'lcurs_parser', 'a:200:{i:8;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:7:"12.5369";}i:9;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:13:"7976.45350924";}i:10;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:7:"30.7552";}i:1;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:7:"59.9266";}i:2;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:11:"16.68708053";}i:3;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:7:"70.3718";}i:4;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:11:"14.21023762";}i:7;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:7:"17.9914";}i:5;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:7:"234.111";}i:6;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:10:"42.7147806";}i:51;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"1.1744";}i:52;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:10:"0.85149864";}i:101;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:8:"2563.068";}i:102;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:11:"39.01574207";}i:103;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:8:"3023.652";}i:104;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:11:"33.07258904";}i:151;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"332.91";}i:152;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"3.00381484";}i:153;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"391.17";}i:154;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"2.55643326";}i:155;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:4:"5.55";}i:156;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:11:"18.01801802";}i:201;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"1.9454";}i:202;a:2:{s:5:"curs1";s:2:"10";s:5:"curs2";s:10:"5.14033104";}i:203;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"2.2973";}i:204;a:2:{s:5:"curs1";s:2:"10";s:5:"curs2";s:10:"4.35293606";}i:205;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:6:"3.2527";}i:251;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:61.1554;}i:252;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.016199999999999999;}i:253;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:69.929199999999994;}i:254;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.0141;}i:301;a:2:{s:5:"curs1";i:1;s:5:"curs2";s:6:"417.03";}i:351;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:2681.7139999999999;}i:352;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.37289584";}i:353;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:161039.01371999999;}i:354;a:2:{s:5:"curs1";s:5:"10000";s:5:"curs2";s:10:"0.06209675";}i:355;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:2331.3217500000001;}i:356;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.42894122";}i:357;a:2:{s:5:"curs1";s:2:"10";s:5:"curs2";s:10:"690541.355";}i:358;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.01448139";}i:359;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:43.948880000000003;}i:360;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"22.7537084";}i:361;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:38.185000000000002;}i:362;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:11:"26.18829383";}i:363;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:2638.8871800000002;}i:364;a:2:{s:5:"curs1";s:5:"10000";s:5:"curs2";s:10:"3.78947614";}i:365;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.016379999999999999;}i:366;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:11:"61.05006105";}i:367;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.00297;}i:368;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.00081999999999999998;}i:369;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:8.0739999999999998;}i:370;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:2.1920000000000002;}i:107;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:4:"2980";}i:108;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:11:"33.55704698";}i:105;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:4:"2547";}i:106;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:11:"39.26187672";}i:400;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:7:"59.7945";}i:401;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.8525";}i:402;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"25.825";}i:403;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"1.1733";}i:404;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"70.161";}i:405;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:7:"30.1611";}i:406;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.0168";}i:407;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.0143";}i:408;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.4321";}i:409;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.0391";}i:410;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.0333";}i:411;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:5:"2.338";}i:255;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.85319999999999996;}i:257;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:25.104199999999999;}i:258;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.039600000000000003;}i:259;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.40600000000000003;}i:260;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:2.4420000000000002;}i:261;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.0339;}i:262;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:28.528500000000001;}i:263;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.024500000000000001;}i:264;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:39.369999999999997;}i:265;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.028299999999999999;}i:266;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:32.584000000000003;}i:267;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.00040000000000000002;}i:268;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";i:2356;}i:269;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.00089999999999999998;}i:270;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:996.29999999999995;}i:272;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:3.9529999999999998;}i:273;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.27210000000000001;}i:274;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:3.2679;}i:275;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.0040000000000000001;}i:276;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:245.4504;}i:277;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.0094999999999999998;}i:280;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:324.67529999999999;}i:281;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.0025000000000000001;}i:283;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:5.1554000000000002;}i:284;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.1865;}i:371;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.067979999999999999;}i:372;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:11:"14.71020888";}i:373;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.080869999999999997;}i:374;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:11:"12.36552492";}i:375;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:216.30026000000001;}i:376;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:9:"0.0046232";}i:377;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:4.9131499999999999;}i:378;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:10:"0.20353541";}i:379;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:13012.56601;}i:380;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.07684879";}i:256;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:1.1601999999999999;}i:271;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.24579999999999999;}i:278;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:94.937299999999993;}i:279;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.0028999999999999998;}i:282;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:364.67099999999999;}i:412;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"6.6854";}i:413;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.1496";}i:414;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.2614";}i:415;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"3.8635";}i:416;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"8.9472";}i:417;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.1123";}i:381;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:60.280000000000001;}i:382;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:11:"16.58925017";}i:383;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:68.81317;}i:384;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:11:"14.53210192";}i:385;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:1.149;}i:386;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:10:"0.87032202";}i:421;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.2614";}i:420;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"3.8635";}i:418;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.1275";}i:419;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"7.8467";}i:422;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.2917";}i:423;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"3.4284";}i:424;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:7:"49.8414";}i:425;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.0201";}i:285;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:2.0181;}i:286;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.4929;}i:288;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.4194;}i:290;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.0332;}i:291;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.077499999999999999;}i:293;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:7.6273;}i:294;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.1158;}i:295;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:75.477999999999994;}i:296;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.012200000000000001;}i:297;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.0057999999999999996;}i:298;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:159.0292;}i:388;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:182.49941999999999;}i:389;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"5.47946947";}i:390;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:10980.273999999999;}i:391;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.09107241";}i:392;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:158.60900000000001;}i:393;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"6.30481246";}i:287;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:2.2786;}i:289;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:29.549499999999998;}i:292;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:12.279999999999999;}i:551;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:4038.93797;}i:552;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.24758984";}i:553;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:3486.4000000000001;}i:554;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.28682882";}i:555;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:246000.06;}i:556;a:2:{s:5:"curs1";s:5:"10000";s:5:"curs2";s:9:"0.0406504";}i:557;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:105101.03;}i:558;a:2:{s:5:"curs1";s:5:"10000";s:5:"curs2";s:10:"0.08714673";}i:559;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.049700000000000001;}i:560;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:11:"20.12072435";}i:561;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:201.10004028;}i:562;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"4.97264943";}i:563;a:2:{s:5:"curs1";s:2:"10";s:5:"curs2";d:0.69269999999999998;}i:564;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:11:"14.43626389";}i:565;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";i:280;}i:566;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"3.57142857";}i:567;a:2:{s:5:"curs1";s:7:"1000000";s:5:"curs2";d:0.40999999999999998;}i:568;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:15:"2439024.3902439";}i:569;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.01059;}i:570;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:11:"94.42870633";}i:571;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";i:16980;}i:572;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.05889282";}i:573;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:240.97999999999999;}i:574;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:10:"0.00414972";}i:575;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:2607.9999990000001;}i:576;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.38343558";}i:577;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:12179.99756403;}i:578;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.08210182";}i:579;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:6.5279999999999996;}i:580;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:10:"0.15318627";}i:581;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:60.36000001;}i:582;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:10:"0.01656726";}i:583;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";d:1.1789000000000001;}i:584;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:12:"848.24836712";}i:585;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";i:7121;}i:587;a:2:{s:5:"curs1";i:1;s:5:"curs2";s:13:"1114.00147762";}i:589;a:2:{s:5:"curs1";i:1;s:5:"curs2";s:13:"5122.01802593";}i:586;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.16058066";}i:588;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.89766488";}i:590;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.19523555";}i:591;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:43.071100000000001;}i:593;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:37.840619910000001;}i:595;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";d:5.0008240000000006;}i:597;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:204.19999999999999;}i:598;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:10:"0.00489716";}i:600;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:10:"0.00526097";}i:602;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.08104021";}i:592;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:10:"2.32174242";}i:594;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:10:"2.64266284";}i:596;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:11:"22.22221235";}i:599;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";i:170;}i:601;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:12339.553555;}}', 'yes'),
(144, 'curs_parser', 'a:200:{i:8;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:7:"12.1455";}i:9;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:13:"8233.50212013";}i:10;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:7:"30.0885";}i:1;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:7:"58.0557";}i:2;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:11:"17.22483753";}i:3;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:7:"68.9992";}i:4;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:11:"14.49292166";}i:7;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:7:"17.1755";}i:5;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:7:"225.503";}i:6;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:11:"44.34530804";}i:51;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"1.1825";}i:52;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:10:"0.84566596";}i:101;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:8:"2569.396";}i:102;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:11:"38.91965271";}i:103;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:7:"3038.31";}i:104;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:11:"32.91303389";}i:151;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"337.04";}i:152;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"2.96700688";}i:153;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:5:"400.2";}i:154;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"2.49875062";}i:155;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:4:"5.77";}i:156;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:11:"17.33102253";}i:201;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"1.9353";}i:202;a:2:{s:5:"curs1";s:2:"10";s:5:"curs2";s:10:"5.16715755";}i:203;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"2.2994";}i:204;a:2:{s:5:"curs1";s:2:"10";s:5:"curs2";s:9:"4.3489606";}i:205;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:6:"3.3056";}i:251;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:59.559199999999997;}i:252;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.0166;}i:253;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:71.515699999999995;}i:254;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.0138;}i:301;a:2:{s:5:"curs1";i:1;s:5:"curs2";s:6:"417.03";}i:351;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:2681.7139999999999;}i:352;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.37289584";}i:353;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:161039.01371999999;}i:354;a:2:{s:5:"curs1";s:5:"10000";s:5:"curs2";s:10:"0.06209675";}i:355;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:2331.3217500000001;}i:356;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.42894122";}i:357;a:2:{s:5:"curs1";s:2:"10";s:5:"curs2";s:10:"690541.355";}i:358;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.01448139";}i:359;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:43.948880000000003;}i:360;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"22.7537084";}i:361;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:38.185000000000002;}i:362;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:11:"26.18829383";}i:363;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:2638.8871800000002;}i:364;a:2:{s:5:"curs1";s:5:"10000";s:5:"curs2";s:10:"3.78947614";}i:365;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.016379999999999999;}i:366;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:11:"61.05006105";}i:367;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.00297;}i:368;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.00081999999999999998;}i:369;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:8.0739999999999998;}i:370;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:2.1920000000000002;}i:107;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:4:"3020";}i:108;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:11:"33.11258278";}i:105;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:4:"2550";}i:106;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:11:"39.21568627";}i:400;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:7:"59.7945";}i:401;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.8525";}i:402;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"25.825";}i:403;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"1.1733";}i:404;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"70.161";}i:405;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:7:"30.1611";}i:406;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.0168";}i:407;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.0143";}i:408;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.4321";}i:409;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.0391";}i:410;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.0333";}i:411;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:5:"2.338";}i:255;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.80640000000000001;}i:257;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:25.254899999999999;}i:258;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.039199999999999999;}i:259;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.4219;}i:260;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:2.3569;}i:261;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.032500000000000001;}i:262;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:30.059999999999999;}i:263;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.024400000000000002;}i:264;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:39.473599999999998;}i:265;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.028299999999999999;}i:266;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:33.0944;}i:267;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.00040000000000000002;}i:268;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:2354.6959999999999;}i:269;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.00089999999999999998;}i:270;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:1007.7121;}i:272;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:4.7618999999999998;}i:273;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.24030000000000001;}i:274;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:3.6362999999999999;}i:275;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.0033999999999999998;}i:276;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:285.09460000000001;}i:277;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.0083000000000000001;}i:280;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:319.96679999999998;}i:281;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.0023999999999999998;}i:283;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:5.3422999999999998;}i:284;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.1784;}i:371;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.067979999999999999;}i:372;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:11:"14.71020888";}i:373;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.080869999999999997;}i:374;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:11:"12.36552492";}i:375;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:216.30026000000001;}i:376;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:9:"0.0046232";}i:377;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:4.9131499999999999;}i:378;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:10:"0.20353541";}i:379;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:13012.56601;}i:380;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.07684879";}i:256;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:1.2149000000000001;}i:271;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.2092;}i:278;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:112.68600000000001;}i:279;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.0030000000000000001;}i:282;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:374.70569999999998;}i:412;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"6.6854";}i:413;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.1496";}i:414;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.2614";}i:415;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"3.8635";}i:416;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"8.9472";}i:417;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.1123";}i:381;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:60.280000000000001;}i:382;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:11:"16.58925017";}i:383;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:68.81317;}i:384;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:11:"14.53210192";}i:385;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:1.149;}i:386;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:10:"0.87032202";}i:421;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.2614";}i:420;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"3.8635";}i:418;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.1275";}i:419;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"7.8467";}i:422;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.2917";}i:423;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"3.4284";}i:424;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:7:"49.8414";}i:425;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:6:"0.0201";}i:285;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:1.9782;}i:286;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.49609999999999999;}i:288;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.41410000000000002;}i:290;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.033300000000000003;}i:291;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.074499999999999997;}i:293;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:8.6625999999999994;}i:294;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.10580000000000001;}i:295;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:77.534899999999993;}i:296;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.0118;}i:297;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.0057000000000000002;}i:298;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:161.65170000000001;}i:388;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:182.49941999999999;}i:389;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"5.47946947";}i:390;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:10980.273999999999;}i:391;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.09107241";}i:392;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:158.60900000000001;}i:393;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"6.30481246";}i:287;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:2.2884000000000002;}i:289;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:29.603300000000001;}i:292;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:12.3001;}i:551;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";i:4733;}i:552;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.21128248";}i:553;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:4039.3000000000002;}i:554;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.24756765";}i:555;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";i:260860;}i:556;a:2:{s:5:"curs1";s:5:"10000";s:5:"curs2";s:10:"0.03833474";}i:557;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:124794.975041;}i:558;a:2:{s:5:"curs1";s:5:"10000";s:5:"curs2";s:10:"0.07927699";}i:559;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.080914849999999996;}i:560;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:11:"12.35867087";}i:561;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";i:383;}i:562;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"2.61096606";}i:563;a:2:{s:5:"curs1";s:2:"10";s:5:"curs2";d:0.82150000000000001;}i:564;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:11:"12.17285453";}i:565;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:390.20999999999998;}i:566;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"2.56272264";}i:567;a:2:{s:5:"curs1";s:7:"1000000";s:5:"curs2";d:0.44;}i:568;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:15:"2272727.2727273";}i:569;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:0.01623805;}i:570;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:11:"61.58374928";}i:571;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";i:21450;}i:572;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.04662005";}i:573;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:333.97500000000002;}i:574;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:10:"0.00299424";}i:575;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";i:4300;}i:576;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.23255814";}i:577;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:21025.000000010001;}i:578;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.04756243";}i:579;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:4.9386400000000004;}i:580;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:10:"0.20248489";}i:581;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";i:55;}i:582;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:10:"0.01818182";}i:583;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";d:1.2249999999999999;}i:584;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:12:"816.32653061";}i:585;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:9699.0119398799998;}i:587;a:2:{s:5:"curs1";i:1;s:5:"curs2";s:13:"1906.84824572";}i:589;a:2:{s:5:"curs1";i:1;s:5:"curs2";s:6:"9766.5";}i:586;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.16058066";}i:588;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.52442558";}i:590;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.10239083";}i:591;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:77.099999999999994;}i:593;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:66.518000000000001;}i:595;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";d:5.9100100000000007;}i:597;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:284.89999999999998;}i:598;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:7:"0.00351";}i:600;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:10:"0.00411465";}i:602;a:2:{s:5:"curs1";s:4:"1000";s:5:"curs2";s:10:"0.06512535";}i:592;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:10:"1.29701686";}i:594;a:2:{s:5:"curs1";s:3:"100";s:5:"curs2";s:10:"1.50335248";}i:596;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";s:10:"17.3882803";}i:599;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:229.84999999999999;}i:601;a:2:{s:5:"curs1";s:1:"1";s:5:"curs2";d:15355.00307106;}}', 'yes'),
(145, 'time_parser', '1504270311', 'yes'),
(146, 'the_cron', 'a:9:{s:3:"now";i:1504270414;s:4:"2min";i:1504270311;s:4:"5min";i:1504270311;s:5:"10min";i:1504270311;s:5:"30min";i:1504270311;s:5:"1hour";i:1504270311;s:5:"3hour";i:1504270311;s:5:"05day";i:1504270311;s:4:"1day";i:1504270311;}', 'yes'),
(147, 'pn_lang', 'a:4:{s:10:"admin_lang";s:5:"ru_RU";s:9:"site_lang";s:5:"ru_RU";s:12:"multilingual";i:1;s:14:"multisite_lang";a:2:{i:0;s:5:"ru_RU";i:1;s:5:"en_US";}}', 'yes'),
(148, 'nav_menu_options', 'a:2:{i:0;b:0;s:8:"auto_add";a:0:{}}', 'yes'),
(149, 'check_new_user', 'a:1:{i:0;s:1:"0";}', 'yes'),
(150, 'reserv_out', 'a:8:{i:0;s:3:"new";i:1;s:7:"techpay";i:2;s:5:"payed";i:3;s:7:"coldpay";i:4;s:7:"realpay";i:5;s:6:"verify";i:6;s:11:"coldsuccess";i:7;s:7:"success";}', 'yes'),
(151, 'reserv_in', 'a:5:{i:0;s:5:"payed";i:1;s:7:"coldpay";i:2;s:7:"realpay";i:3;s:6:"verify";i:4;s:7:"success";}', 'yes'),
(152, 'widget_get_pn_login', 'a:2:{i:3;a:2:{s:10:"titleru_RU";s:0:"";s:10:"titleen_US";s:0:"";}s:12:"_multiwidget";i:1;}', 'yes'),
(153, 'widget_get_pn_lk', 'a:2:{i:2;a:2:{s:10:"titleru_RU";s:0:"";s:10:"titleen_US";s:0:"";}s:12:"_multiwidget";i:1;}', 'yes'),
(154, 'widget_get_pn_lastobmen', 'a:2:{i:2;a:2:{s:10:"titleru_RU";s:0:"";s:10:"titleen_US";s:0:"";}s:12:"_multiwidget";i:1;}', 'yes'),
(155, 'widget_get_pn_reviews', 'a:2:{i:2;a:3:{s:10:"titleru_RU";s:0:"";s:10:"titleen_US";s:0:"";s:5:"count";s:1:"3";}s:12:"_multiwidget";i:1;}', 'yes'),
(156, 'widget_get_pn_cbr', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(157, 'widget_get_userverify', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(158, 'h_change', 'a:6:{s:9:"fixheader";i:1;s:8:"linkhead";i:0;s:5:"phone";s:62:"[ru_RU:]8 800 123 45 67[:ru_RU][en_US:]8 800 123 45 67[:en_US]";s:3:"icq";s:54:"[ru_RU:]123 456 789[:ru_RU][en_US:]123 456 789[:en_US]";s:5:"skype";s:46:"[ru_RU:]premium[:ru_RU][en_US:]premium[:en_US]";s:5:"email";s:62:"[ru_RU:]info@premium.ru[:ru_RU][en_US:]info@premium.ru[:en_US]";}', 'yes'),
(159, 'ho_change', 'a:10:{s:9:"blocknews";i:1;s:7:"catnews";i:0;s:11:"blocreviews";i:1;s:13:"blockarticles";i:0;s:6:"wtitle";s:0:"";s:6:"ititle";s:117:"[ru_RU:]Приветствуем на сайте обменного пункта![:ru_RU][en_US:]Dear guests![:en_US]";s:5:"wtext";s:0:"";s:5:"itext";s:3182:"[ru_RU:]Наш On-line сервис предназначен для тех, кто хочет быстро, безопасно и по выгодному курсу обменять такие виды электронных валют: Webmoney, Perfect Money, Qiwi, PayPal, Яндекс. Деньги, Альфа-Банк, ВТБ 24, Приват24, Visa/Master Card, Western uniоn, MoneyGram.\r\n\r\nЭтим возможности нашего сервиса не ограничиваются. В рамках проекта действуют программа лояльности, накопительная скидка и партнерская программа, воспользовавшись преимуществами которых, вы сможете совершать обмен электронных валют на более выгодных условиях. Для этого нужно просто зарегистрироваться на сайте.\r\n\r\nНаш пункт обмена электронных валют – система, созданная на базе современного программного обеспечения и содержащая весь набор необходимых функций для удобной и безопасной конвертации наиболее распространенных видов электронных денег. За время работы мы приобрели репутацию проверенного партнера и делаем все возможное, чтобы ваши впечатления от нашего сервиса были только благоприятными.[:ru_RU][en_US:]ur website is dedicated to those who wish to exchange own currency in a luxurious way! We welcome you on our grounds and hope that our online service will deliver you the most awesome experience you ever had. Our major point is to provide our clients the safest and quickest method to make transactions using each of the following payment systems: Webmoney, Perfect Money, LiqPay, Pecunix, Payza, Visa/Master Card, Western uniоn, MoneyGram. \r\n\r\nMoreover, we are also pleased to inform you that here you are to encounter the best rates on the whole net. Be sure to know that we provide much more useful and popular up-to-date services among which you are to encounter affiliate programs and additional discounts as well as loyalty program. All these services will provide you to trade your currency in the most profitable ways and under the best conditions. All you need to do is register on our website and start to burn financial skies.\r\n\r\nAt last, we are pleased to announce you that our exchange system of electronic currencies based on the up-to-date software and willing to provide you with the most awesome functions, you might need for convenient and safest converting any type of electronic currency you possess. Note that our team already gained a reputation as a trusted partner and is well known worldwide. For now, we are willing to do our best for you as well as for your wallet to provide the next-level experience and positive emotions.[:en_US]";s:9:"lastobmen";i:1;s:8:"hidecurr";s:0:"";}', 'yes'),
(160, 'f_change', 'a:7:{s:5:"ctext";s:120:"[ru_RU:]Сервис обмена электронных валют.[:ru_RU][en_US:]E-currency exchange service.[:en_US]";s:9:"timetable";s:188:"[ru_RU:]Пн. — Пт. с 10:00 до 23:00 по мск.\r\nСб. — Вск. свободный график.[:ru_RU][en_US:]Mon – Fri 10 a.m. till 11 p.m.\r\nSat – Sun free time.[:en_US]";s:5:"phone";s:62:"[ru_RU:]8 800 123 45 67[:ru_RU][en_US:]8 800 123 45 67[:en_US]";s:2:"vk";s:10:"[soc_link]";s:2:"fb";s:10:"[soc_link]";s:2:"gp";s:10:"[soc_link]";s:2:"tw";s:10:"[soc_link]";}', 'yes'),
(163, 'work_parser', 'a:196:{i:1;i:1;i:2;i:1;i:3;i:1;i:4;i:1;i:5;i:1;i:6;i:1;i:8;i:1;i:9;i:1;i:10;i:1;i:51;i:1;i:52;i:1;i:101;i:1;i:102;i:1;i:103;i:1;i:104;i:1;i:151;i:1;i:152;i:1;i:153;i:1;i:154;i:1;i:155;i:1;i:156;i:1;i:201;i:1;i:202;i:1;i:203;i:1;i:204;i:1;i:205;i:1;i:301;i:1;i:352;i:1;i:351;i:1;i:353;i:1;i:354;i:1;i:355;i:1;i:356;i:1;i:357;i:1;i:359;i:1;i:358;i:1;i:360;i:1;i:361;i:1;i:362;i:1;i:363;i:1;i:364;i:1;i:365;i:1;i:366;i:1;i:367;i:1;i:368;i:1;i:370;i:1;i:369;i:1;i:253;i:1;i:251;i:1;i:252;i:1;i:254;i:1;i:7;i:1;i:105;i:1;i:106;i:1;i:107;i:1;i:108;i:1;i:255;i:1;i:257;i:1;i:258;i:1;i:259;i:1;i:260;i:1;i:261;i:1;i:262;i:1;i:263;i:1;i:264;i:1;i:265;i:1;i:266;i:1;i:267;i:1;i:268;i:1;i:269;i:1;i:270;i:1;i:272;i:1;i:273;i:1;i:274;i:1;i:275;i:1;i:276;i:1;i:277;i:1;i:280;i:1;i:281;i:1;i:283;i:1;i:284;i:1;i:371;i:1;i:372;i:1;i:374;i:1;i:375;i:1;i:376;i:1;i:377;i:1;i:378;i:1;i:379;i:1;i:380;i:1;i:400;i:1;i:401;i:1;i:402;i:1;i:403;i:1;i:404;i:1;i:405;i:1;i:406;i:1;i:407;i:1;i:408;i:1;i:409;i:1;i:410;i:1;i:411;i:1;i:256;i:1;i:271;i:1;i:278;i:1;i:279;i:1;i:282;i:1;i:373;i:1;i:381;i:1;i:382;i:1;i:383;i:1;i:384;i:1;i:385;i:1;i:386;i:1;i:412;i:1;i:414;i:1;i:416;i:1;i:417;i:1;i:285;i:1;i:286;i:1;i:288;i:1;i:290;i:1;i:291;i:1;i:293;i:1;i:294;i:1;i:295;i:1;i:296;i:1;i:297;i:1;i:298;i:1;i:388;i:1;i:389;i:1;i:391;i:1;i:392;i:1;i:393;i:1;i:415;i:1;i:413;i:1;i:418;i:1;i:419;i:1;i:420;i:1;i:422;i:1;i:424;i:1;i:425;i:1;i:289;i:1;i:287;i:1;i:292;i:1;i:551;i:1;i:552;i:1;i:553;i:1;i:554;i:1;i:555;i:1;i:556;i:1;i:557;i:1;i:558;i:1;i:559;i:1;i:560;i:1;i:561;i:1;i:562;i:1;i:563;i:1;i:564;i:1;i:565;i:1;i:566;i:1;i:585;i:1;i:567;i:1;i:568;i:1;i:569;i:1;i:570;i:1;i:587;i:1;i:571;i:1;i:572;i:1;i:573;i:1;i:574;i:1;i:575;i:1;i:576;i:1;i:577;i:1;i:578;i:1;i:579;i:1;i:580;i:1;i:581;i:1;i:582;i:1;i:583;i:1;i:584;i:1;i:589;i:1;i:588;i:1;i:590;i:1;i:591;i:1;i:593;i:1;i:595;i:1;i:597;i:1;i:598;i:1;i:600;i:1;i:602;i:1;i:601;i:1;i:599;i:1;i:596;i:1;i:594;i:1;i:592;i:1;}', 'yes'),
(164, 'config_parser', 'a:63:{i:301;s:4:"last";i:351;s:4:"last";i:353;s:4:"last";i:355;s:4:"last";i:357;s:4:"last";i:359;s:4:"last";i:361;s:4:"last";i:363;s:4:"last";i:365;s:4:"last";i:367;s:4:"last";i:368;s:4:"last";i:369;s:4:"last";i:370;s:4:"last";i:371;s:4:"last";i:373;s:4:"last";i:375;s:4:"last";i:377;s:4:"last";i:379;s:4:"last";i:388;s:4:"last";i:551;s:10:"last_trade";i:552;s:10:"last_trade";i:553;s:10:"last_trade";i:554;s:10:"last_trade";i:555;s:10:"last_trade";i:556;s:10:"last_trade";i:557;s:10:"last_trade";i:558;s:4:"high";i:559;s:10:"last_trade";i:560;s:10:"last_trade";i:561;s:10:"last_trade";i:562;s:10:"last_trade";i:563;s:10:"last_trade";i:564;s:10:"last_trade";i:565;s:10:"last_trade";i:566;s:10:"last_trade";i:567;s:10:"last_trade";i:568;s:10:"last_trade";i:569;s:10:"last_trade";i:570;s:10:"last_trade";i:571;s:10:"last_trade";i:572;s:10:"last_trade";i:573;s:10:"last_trade";i:574;s:10:"last_trade";i:575;s:10:"last_trade";i:576;s:10:"last_trade";i:577;s:10:"last_trade";i:578;s:10:"last_trade";i:579;s:10:"last_trade";i:580;s:10:"last_trade";i:581;s:10:"last_trade";i:582;s:10:"last_trade";i:583;s:10:"last_trade";i:584;s:10:"last_trade";i:591;s:10:"last_trade";i:592;s:10:"last_trade";i:593;s:10:"last_trade";i:594;s:10:"last_trade";i:595;s:10:"last_trade";i:597;s:10:"last_trade";i:598;s:10:"last_trade";i:600;s:10:"last_trade";i:602;s:10:"last_trade";i:601;s:10:"last_trade";}', 'yes'),
(166, 'banners', 'a:5:{s:7:"banner1";a:1:{i:0;s:211:"<a href="[partner_link]"><img src="[url]/wp-content/plugins/premiumbox/images/banners/468x60_1.gif" alt="Обменный пункт" title="Обменный пункт" width="468" height="60" border="0" /></a>";}s:7:"banner2";a:1:{i:0;s:213:"<a href="[partner_link]"><img src="[url]/wp-content/plugins/premiumbox/images/banners/200x200_1.gif" alt="Обменный пункт" title="Обменный пункт" width="200" height="200" border="0" /></a>";}s:7:"banner3";a:1:{i:0;s:213:"<a href="[partner_link]"><img src="[url]/wp-content/plugins/premiumbox/images/banners/120x600_1.gif" alt="Обменный пункт" title="Обменный пункт" width="120" height="600" border="0" /></a>";}s:7:"banner4";a:1:{i:0;s:213:"<a href="[partner_link]"><img src="[url]/wp-content/plugins/premiumbox/images/banners/100x100_1.gif" alt="Обменный пункт" title="Обменный пункт" width="100" height="100" border="0" /></a>";}s:7:"banner5";a:1:{i:0;s:209:"<a href="[partner_link]"><img src="[url]/wp-content/plugins/premiumbox/images/banners/88x31_1.gif" alt="Обменный пункт" title="Обменный пункт" width="88" height="31" border="0" /></a>";}}', 'yes');
INSERT INTO `pr_options` (`option_id`, `option_name`, `option_value`, `autoload`) VALUES
(179, 'mailtemp', 'a:54:{s:11:"userverify1";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:122:"[ru_RU:]Запрос на верификацию личности[:ru_RU][en_US:]Request for identity verification[:en_US]";s:4:"name";s:27:"Обменный пункт";s:4:"text";s:207:"[ru_RU:]На сайте [sitename] поступил запрос на верификацию личности.[:ru_RU][en_US:]You received a request for identity verification on the site [site name].[:en_US]";}s:11:"userverify2";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:107:"[ru_RU:]Запрос на верификацию счета[:ru_RU][en_US:]Request for verification[:en_US]";s:4:"name";s:27:"Обменный пункт";s:4:"text";s:200:"[ru_RU:]На сайте [sitename] поступил запрос на верификацию счета.[:ru_RU][en_US:]You received a request for account verification on the site [site name].[:en_US]";}s:9:"newreview";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:63:"[ru_RU:]Новый отзыв[:ru_RU][en_US:]New review[:en_US]";s:4:"name";s:27:"Обменный пункт";s:4:"text";s:251:"[ru_RU:]Пользователь [user] оставил отзыв на сайте [sitename] .<br>\r\nУправление отзывом [management][:ru_RU][en_US:]User [user] write a review on site [site name].\r\nTo control review [management][:en_US]";}s:6:"payout";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:152:"[ru_RU:]Запрос выплаты партнерского вознаграждения[:ru_RU][en_US:]Request for аffiliate money withdrawal[:en_US]";s:4:"name";s:27:"Обменный пункт";s:4:"text";s:206:"[ru_RU:]Пользователь [user] запросил выплату в размере [sum] на сайте [sitename].[:ru_RU][en_US:]User [user] requested payment of [sum] on site [site name].[:en_US]";}s:11:"contactform";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:67:"[ru_RU:]Обратная связь[:ru_RU][en_US:]Feedback[:en_US]";s:4:"name";s:27:"Обменный пункт";s:4:"text";s:199:"[ru_RU:]Имя: [name]<br>\r\nID обмена: [idz]<br>\r\nE-mail: [email]<br>\r\nСообщение:<br>\r\n[text][:ru_RU][en_US:]Name: [name]\r\nExchange ID: [idz]\r\nE-mail: [email]\r\nMessage:\r\n[text][:en_US]";}s:9:"new_bids1";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:88:"[ru_RU:]Заявка на обмен [id][:ru_RU][en_US:]Order for exchange [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:1113:"[ru_RU:]<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong><br>\r\nСсылка на заявку: [bidurl] <br><br>\r\n\r\n<strong>Информация о клиенте</strong><br>\r\nИмя: [last_name] [first_name] [second_name]<br>\r\nТелефон: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:ru_RU][en_US:]<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]<br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]<br>\r\nLink to order: [bidurl] <br><br>\r\n\r\n<strong>Customer information</strong><br>\r\nName: [last_name] [first_name] [second_name]<br>\r\nPhone: [user_phone]<br>\r\nEmail: [user_email] <br>\r\nSkype: [user_skype]<br>[:en_US]";}s:12:"cancel_bids1";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:89:"[ru_RU:]Отмененная заявка [id][:ru_RU][en_US:]Canceled order [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:1113:"[ru_RU:]<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong><br>\r\nСсылка на заявку: [bidurl] <br><br>\r\n\r\n<strong>Информация о клиенте</strong><br>\r\nИмя: [last_name] [first_name] [second_name]<br>\r\nТелефон: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:ru_RU][en_US:]<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]<br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]<br>\r\nLink to order: [bidurl] <br><br>\r\n\r\n<strong>Customer information</strong><br>\r\nName: [last_name] [first_name] [second_name]<br>\r\nPhone: [user_phone]<br>\r\nEmail: [user_email] <br>\r\nSkype: [user_skype]<br>[:en_US]";}s:11:"payed_bids1";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:103:"[ru_RU:]Оплата заявки [id] (вручную)[:ru_RU][en_US:]Paid order [id] (manual)[:en_US]";s:4:"name";s:0:"";s:4:"text";s:1110:"[ru_RU:]<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong><br>\r\nСсылка на заявку: [bidurl] <br><br>\r\n\r\n<strong>Информация о клиенте</strong><br>\r\nИмя: [last_name] [first_name] [second_name]<br>\r\nТелефон: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:ru_RU][en_US:]<strong>Orderinformation</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]<br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]<br>\r\nLink to order: [bidurl]<br><br>\r\n\r\n<strong>Customer information</strong><br>\r\nName: [last_name] [first_name] [second_name]<br>\r\nPhone: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:en_US]";}s:13:"realpay_bids1";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:86:"[ru_RU:]Оплата заявки [id] (merchant)[:ru_RU][en_US:]Paid bid [id][:en_US]";s:4:"name";s:27:"Обменный пункт";s:4:"text";s:1110:"[ru_RU:]<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong><br>\r\nСсылка на заявку: [bidurl] <br><br>\r\n\r\n<strong>Информация о клиенте</strong><br>\r\nИмя: [last_name] [first_name] [second_name]<br>\r\nТелефон: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:ru_RU][en_US:]<strong>Bid information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]<br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]<br>\r\nLink to bid: [bidurl]  <br><br>\r\n\r\n<strong>Customer information</strong><br>\r\nName: [last_name] [first_name] [second_name]<br>\r\nPhone: [user_phone]<br>\r\nEmail: [user_email] <br>\r\nSkype: [user_skype]<br>[:en_US]";}s:12:"verify_bids1";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:108:"[ru_RU:]Оплата заявки [id] (на проверке)[:ru_RU][en_US:]Paid order [id] (hold)[:en_US]";s:4:"name";s:0:"";s:4:"text";s:1112:"[ru_RU:]<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong><br>\r\nСсылка на заявку: [bidurl] <br><br>\r\n\r\n<strong>Информация о клиенте</strong><br>\r\nИмя: [last_name] [first_name] [second_name]<br>\r\nТелефон: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:ru_RU][en_US:]<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]<br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]<br>\r\nLink to order: [bidurl] <br><br>\r\n\r\n<strong>Customer information</strong><br>\r\nName: [last_name] [first_name] [second_name]<br>\r\nPhone: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:en_US]";}s:11:"error_bids1";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:84:"[ru_RU:]Ошибка в заявке [id][:ru_RU][en_US:]Error in order [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:1112:"[ru_RU:]<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong><br>\r\nСсылка на заявку: [bidurl] <br><br>\r\n\r\n<strong>Информация о клиенте</strong><br>\r\nИмя: [last_name] [first_name] [second_name]<br>\r\nТелефон: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:ru_RU][en_US:]<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]<br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]<br>\r\nLink to order: [bidurl]<br><br> \r\n\r\n<strong>Customer information</strong><br>\r\nName: [last_name] [first_name] [second_name]<br>\r\nPhone: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:en_US]";}s:13:"success_bids1";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:92:"[ru_RU:]Выполненная заявка [id][:ru_RU][en_US:]Completed order [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:1112:"[ru_RU:]<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong><br>\r\nСсылка на заявку: [bidurl] <br><br>\r\n\r\n<strong>Информация о клиенте</strong><br>\r\nИмя: [last_name] [first_name] [second_name]<br>\r\nТелефон: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:ru_RU][en_US:]<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]<br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]<br>\r\nLink to order: [bidurl]<br><br>\r\n\r\n<strong>Customer information</strong><br>\r\nName: [last_name] [first_name] [second_name]<br>\r\nPhone: [user_phone]<br>\r\nEmail: [user_email] <br>\r\nSkype: [user_skype]<br>[:en_US]";}s:16:"realdelete_bids1";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:107:"[ru_RU:]Полностью удалена заявка [id][:ru_RU][en_US:]Fully deleted order [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:1112:"[ru_RU:]<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong><br>\r\nСсылка на заявку: [bidurl] <br><br>\r\n\r\n<strong>Информация о клиенте</strong><br>\r\nИмя: [last_name] [first_name] [second_name]<br>\r\nТелефон: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:ru_RU][en_US:]<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]<br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]<br>\r\nLink to order: [bidurl]<br><br> \r\n\r\n<strong>Customer information</strong><br>\r\nName: [last_name] [first_name] [second_name]<br>\r\nPhone: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:en_US]";}s:12:"delete_bids1";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:78:"[ru_RU:]Удалена заявка [id][:ru_RU][en_US:]Order bid [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:1111:"[ru_RU:]<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong><br>\r\nСсылка на заявку: [bidurl] <br><br>\r\n\r\n<strong>Информация о клиенте</strong><br>\r\nИмя: [last_name] [first_name] [second_name]<br>\r\nТелефон: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:ru_RU][en_US:]<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]<br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]<br>\r\nLink to order: [bidurl]<br><br>\r\n\r\n<strong>Customer information</strong><br>\r\nName: [last_name] [first_name] [second_name]<br>\r\nPhone: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:en_US]";}s:9:"new_bids2";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:88:"[ru_RU:]Заявка на обмен [id][:ru_RU][en_US:]Order for exchange [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:871:"[ru_RU:]Здравствуйте.<br><br>\r\n\r\nСтатус заявки: новая<br>\r\nСсылка на заявку: [bidurl]<br><br>\r\n\r\n<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong>[:ru_RU][en_US:]Hello.<br><br>\r\n\r\nOrder status: new<br>\r\nLink to order: [bidurl]<br><br>\r\n\r\n<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]<br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]</strong><br>[:en_US]";}s:12:"cancel_bids2";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:89:"[ru_RU:]Отмененная заявка [id][:ru_RU][en_US:]Canceled order [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:890:"[ru_RU:]Здравствуйте.<br><br>\r\n\r\nСтатус заявки: отмененная<br>\r\nСсылка на заявку: [bidurl]<br><br>\r\n\r\n<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong><br>[:ru_RU][en_US:]Hello.<br><br>\r\n\r\nOrder status: canceled<br>\r\nLink to order: [bidurl]<br><br>\r\n\r\n<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]<br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]</strong><br>[:en_US]";}s:13:"userverify1_u";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:120:"[ru_RU:]Успешная верификация личности[:ru_RU][en_US:]Successful identity verification[:en_US]";s:4:"name";s:27:"Обменный пункт";s:4:"text";s:159:"[ru_RU:]Ваш аккаунт верифицирован на сайте [sitename].[:ru_RU][en_US:]Your account has been verified on site [site name].[:en_US]";}s:13:"userverify2_u";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:111:"[ru_RU:]Отказ верификации личности[:ru_RU][en_US:]Refused identity verification[:en_US]";s:4:"name";s:27:"Обменный пункт";s:4:"text";s:237:"[ru_RU:]Вам было отказано в верификации аккаунта на сайте [sitename] по причине: [text][:ru_RU][en_US:]You were refused verify your account on site [site name] because of: [text][:en_US]";}s:11:"payed_bids2";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:77:"[ru_RU:]Оплата заявки [id][:ru_RU][en_US:]Paid order [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:891:"[ru_RU:]Здравствуйте.<br><br>\r\n\r\nСтатус заявки: оплаченная<br>\r\nСсылка на заявку: [bidurl]<br><br>\r\n\r\n<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong>[:ru_RU][en_US:]Hello.<br><br>\r\n\r\nOrder status: paid<br>\r\nLink to order: [bidurl]<br><br>\r\n\r\n<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]</strong><br>[:en_US]";}s:13:"realpay_bids2";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:77:"[ru_RU:]Оплата заявки [id][:ru_RU][en_US:]Paid order [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:891:"[ru_RU:]Здравствуйте.<br><br>\r\n\r\nСтатус заявки: оплаченная<br>\r\nСсылка на заявку: [bidurl]<br><br>\r\n\r\n<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong>[:ru_RU][en_US:]Hello.<br><br>\r\n\r\nOrder status: paid<br>\r\nLink to order: [bidurl]<br><br>\r\n\r\n<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]</strong><br>[:en_US]";}s:12:"verify_bids2";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:77:"[ru_RU:]Оплата заявки [id][:ru_RU][en_US:]Paid order [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:891:"[ru_RU:]Здравствуйте.<br><br>\r\n\r\nСтатус заявки: оплаченная<br>\r\nСсылка на заявку: [bidurl]<br><br>\r\n\r\n<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong>[:ru_RU][en_US:]Hello.<br><br>\r\n\r\nOrder status: paid<br>\r\nLink to order: [bidurl]<br><br>\r\n\r\n<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]</strong><br>[:en_US]";}s:11:"error_bids2";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:84:"[ru_RU:]Ошибка в заявке [id][:ru_RU][en_US:]Error in order [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:884:"[ru_RU:]Здравствуйте.<br><br>\r\n\r\nСтатус заявки: ошибка<br>\r\nСсылка на заявку: [bidurl]<br><br>\r\n\r\n<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong>[:ru_RU][en_US:]Hello.<br><br>\r\n\r\nOrder status: error<br>\r\nLink to order: [bidurl]<br><br>\r\n\r\n<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]</strong><br>[:en_US]";}s:13:"success_bids2";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:92:"[ru_RU:]Выполненная заявка [id][:ru_RU][en_US:]Completed order [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:898:"[ru_RU:]Здравствуйте.<br><br>\r\n\r\nСтатус заявки: выполненная<br>\r\nСсылка на заявку: [bidurl]<br><br>\r\n\r\n<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong>[:ru_RU][en_US:]Hello.<br><br>\r\n\r\nOrder status: completed<br>\r\nLink to order: [bidurl]<br><br>\r\n\r\n<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]</strong><br>[:en_US]";}s:16:"realdelete_bids2";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:111:"[ru_RU:]Полностью удаленная заявка [id][:ru_RU][en_US:]Fully deleted order [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:913:"[ru_RU:]Здравствуйте.<br><br>\r\n\r\nСтатус заявки: полностью удалена<br>\r\nСсылка на заявку: [bidurl]<br><br>\r\n\r\n<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong>[:ru_RU][en_US:]Hello.<br><br>\r\n\r\nOrder status: fully deleted<br>\r\nLink to order: [bidurl]<br><br>\r\n\r\n<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]</strong><br>[:en_US]";}s:12:"delete_bids2";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:82:"[ru_RU:]Удалена заявка [id][:ru_RU][en_US:]Deleted order [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:879:"[ru_RU:]Здравствуйте.<br><br>\r\n\r\nСтатус заявки: удалена<br>\r\nСсылка на заявку: [bidurl]<br><br>\r\n\r\n<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong>[:ru_RU][en_US:]Hello.<br><br>\r\n\r\nOrder status: deleted<br>\r\nLink to order: [bidurl]<br><br>\r\n\r\n<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]<br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]</strong><br>[:en_US]";}s:16:"autoregisterform";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:96:"[ru_RU:]Регистрация пользователя[:ru_RU][en_US:]User registration[:en_US]";s:4:"name";s:27:"Обменный пункт";s:4:"text";s:276:"[ru_RU:]Вы зарегистрировались на сайте [sitename].<br>\r\nЛогин: [login]<br>\r\nПароль: [pass]<br>\r\nEmail: [email]<br>[:ru_RU][en_US:]You registered on site [sitename].<br>\r\nLogin: [login]<br>\r\nPassword: [pass]<br>\r\nEmail: [email]<br>[:en_US]";}s:12:"lostpassform";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:90:"[ru_RU:]Восстановление пароля[:ru_RU][en_US:]Password recovery[:en_US]";s:4:"name";s:27:"Обменный пункт";s:4:"text";s:171:"[ru_RU:]Для восстановления пароля перейдите по ссылке: [link][:ru_RU][en_US:]To recover your password click on link: [link][:en_US]";}s:12:"registerform";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:96:"[ru_RU:]Регистрация пользователя[:ru_RU][en_US:]User registration[:en_US]";s:4:"name";s:27:"Обменный пункт";s:4:"text";s:276:"[ru_RU:]Вы зарегистрировались на сайте [sitename].<br>\r\nЛогин: [login]<br>\r\nПароль: [pass]<br>\r\nEmail: [email]<br>[:ru_RU][en_US:]You registered on site [sitename].<br>\r\nLogin: [login]<br>\r\nPassword: [pass]<br>\r\nEmail: [email]<br>[:en_US]";}s:13:"userverify3_u";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:113:"[ru_RU:]Успешная верификация счета[:ru_RU][en_US:]Successful account verification[:en_US]";s:4:"name";s:27:"Обменный пункт";s:4:"text";s:169:"[ru_RU:]Ваш счет [purse] верифицирован на сайте [sitename].[:ru_RU][en_US:]Your account [purse] has been verified on site [site name].[:en_US]";}s:13:"userverify4_u";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:104:"[ru_RU:]Отказ верификации счета[:ru_RU][en_US:]Refused account verification[:en_US]";s:4:"name";s:27:"Обменный пункт";s:4:"text";s:143:"[ru_RU:]Ваш отказано в верификации счета [purse].[:ru_RU][en_US:]Your account verification refused [purse].[:en_US]";}s:7:"zreserv";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:74:"[ru_RU:]Запрос резерва[:ru_RU][en_US:]Request reserve[:en_US]";s:4:"name";s:27:"Обменный пункт";s:4:"text";s:447:"[ru_RU:]На сайте [sitename] вы оставляли запрос на резерв в размере [sum] для направления обмена [direction]. В данный момент доступен резерв в размере [summrez].[:ru_RU][en_US:]You leave a request to reserve the amount [sum] [valut] for the exchange direction [direction] on site [site name].  Currently available reserve is [summrez] [valut].[:en_US]";}s:13:"confirmreview";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:79:"[ru_RU:]Подтвердите отзыв[:ru_RU][en_US:]Confirm review[:en_US]";s:4:"name";s:0:"";s:4:"text";s:152:"[ru_RU:]Для подтверждения отзывы перейдите по ссылке [link][:ru_RU][en_US:]To confirm review go to [link][:en_US]";}s:10:"partprofit";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:120:"[ru_RU:]Начислено партнерское вознаграждение[:ru_RU][en_US:]Charge your profit[:en_US]";s:4:"name";s:27:"Обменный пункт";s:4:"text";s:247:"[ru_RU:]На сайте [sitename] вам было начислено партнерского вознаграждение в размере [sum] [ctype].[:ru_RU][en_US:]You received profit in the amount [sum] [ctype] on site [sitename].[:en_US]";}s:10:"letterauth";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:67:"[ru_RU:]Авторизация[:ru_RU][en_US:]Authorization[:en_US]";s:4:"name";s:27:"Обменный пункт";s:4:"text";s:133:"[ru_RU:]Для авторизации перейдите по ссылке [link][:ru_RU][en_US:]To login please go to [link][:en_US]";}s:5:"alogs";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:95:"[ru_RU:]Зафиксирован вход в аккаунт[:ru_RU][en_US:]Login report[:en_US]";s:4:"name";s:27:"Обменный пункт";s:4:"text";s:292:"[ru_RU:][date] был зафиксирован вход в ваш аккаунта на сайте [sitename] с IP адреса [ip] и браузера [browser].[:ru_RU][en_US:][date] was fixed log in into your account on site [sitename] with IP address [ip] and browser [browser].[:en_US]";}s:17:"paymerchant_error";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:85:"[ru_RU:]Ошибка авто выплаты[:ru_RU][en_US:]Auto payout error[:en_US]";s:4:"name";s:0:"";s:4:"text";s:217:"[ru_RU:]Для заявки [bid_id] во время авто выплаты произошла ошибка: [error_txt][:ru_RU][en_US:]An error occurred for the [bid_id] order during auto payout: [error_txt][:en_US]";}s:13:"coldpay_bids1";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:145:"[ru_RU:]Ожидание подтверждения от мерчанта [id][:ru_RU][en_US:]Waiting for confirmation from merchant [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:1111:"[ru_RU:]<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong><br>\r\nСсылка на заявку: [bidurl] <br><br>\r\n\r\n<strong>Информация о клиенте</strong><br>\r\nИмя: [last_name] [first_name] [second_name]<br>\r\nТелефон: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:ru_RU][en_US:]<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]<br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]<br>\r\nLink to order: [bidurl]<br><br>\r\n\r\n<strong>Customer information</strong><br>\r\nName: [last_name] [first_name] [second_name]<br>\r\nPhone: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:en_US]";}s:17:"coldsuccess_bids1";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:176:"[ru_RU:]Ожидание подтверждения от модуля автовыплат [id][:ru_RU][en_US:]Waiting for confirmation from the auto payout module [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:1112:"[ru_RU:]<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong><br>\r\nСсылка на заявку: [bidurl] <br><br>\r\n\r\n<strong>Информация о клиенте</strong><br>\r\nИмя: [last_name] [first_name] [second_name]<br>\r\nТелефон: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:ru_RU][en_US:]<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]<br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]<br>\r\nLink to order: [bidurl]<br><br> \r\n\r\n<strong>Customer information</strong><br>\r\nName: [last_name] [first_name] [second_name]<br>\r\nPhone: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:en_US]";}s:13:"coldpay_bids2";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:175:"[ru_RU:]Ожидание подтверждения оплаты от платежной системы [id][:ru_RU][en_US:]Waiting for confirmation from merchant [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:998:"[ru_RU:]Здравствуйте.<br><br>\r\n\r\nСтатус заявки: ожидаем подтверждения оплаты от платежной системы<br>\r\nСсылка на заявку: [bidurl]<br><br>\r\n\r\n<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong>[:ru_RU][en_US:]Hello.<br><br>\r\n\r\nOrder status: waiting for confirmation from merchant<br>\r\nLink to order: [bidurl]<br><br>\r\n\r\n<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]</strong><br>[:en_US]";}s:17:"coldsuccess_bids2";a:6:{s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"send";i:0;s:5:"title";s:207:"[ru_RU:]Ожидание подтверждения статуса транзакции от платежной системы[:ru_RU][en_US:]Waiting for confirmation from the auto payout module [id][:en_US]";s:4:"name";s:0:"";s:4:"text";s:1035:"[ru_RU:]Здравствуйте.<br><br>\r\n\r\nСтатус заявки: ожидаем подтверждения статуса транзакции от платежной системы<br>\r\nСсылка на заявку: [bidurl]<br><br>\r\n\r\n<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong>[:ru_RU][en_US:]Hello.<br><br>\r\n\r\nOrder status: waiting for confirmation from the auto payout module<br>\r\nLink to order: [bidurl]<br><br>\r\n\r\n<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]</strong><br>[:en_US]";}s:13:"zreserv_admin";a:4:{s:4:"send";i:0;s:5:"title";s:74:"[ru_RU:]Запрос резерва[:ru_RU][en_US:]Reserve request[:en_US]";s:6:"tomail";s:0:"";s:4:"text";s:386:"[ru_RU:]Пользователь ([email]) отставил запрос на резерв в размере [sum] для направления обмена [direction]. Комментарий пользователя: [comment][:ru_RU][en_US:]User ([email]) left a request for a reserve in the amount of [sum] for the exchange of direction [direction]. User comment: [comment][:en_US]";}s:25:"generate_Address2_blockio";a:4:{s:4:"send";i:0;s:5:"title";s:133:"[ru_RU:]Создан адрес для оплаты заявки [id][:ru_RU][en_US:]Address was created for payment bid [id][:en_US]";s:6:"tomail";s:0:"";s:4:"text";s:162:"[ru_RU:]Создан адрес [Address] для оплаты заявки ID [bid_id].[:ru_RU][en_US:]Address [Address] was created for payment bid [id].[:en_US]";}s:28:"generate_Address2_blockchain";a:4:{s:4:"send";i:0;s:5:"title";s:133:"[ru_RU:]Создан адрес для оплаты заявки [id][:ru_RU][en_US:]Address was created for payment bid [id][:en_US]";s:6:"tomail";s:0:"";s:4:"text";s:162:"[ru_RU:]Создан адрес [Address] для оплаты заявки ID [bid_id].[:ru_RU][en_US:]Address [Address] was created for payment bid [id].[:en_US]";}s:25:"generate_Address1_blockio";a:4:{s:4:"send";i:0;s:5:"title";s:133:"[ru_RU:]Создан адрес для оплаты заявки [id][:ru_RU][en_US:]Address was created for payment bid [id][:en_US]";s:6:"tomail";s:0:"";s:4:"text";s:478:"[ru_RU:]Создан адрес [Address] для оплаты заявки ID [bid_id]. Необходимо оплатить сумму [sum]. Заявка будет считаться оплаченной при следующем количеств подтверждений: [count].[:ru_RU][en_US:]Address [Address] was created for payment bid [id]. You hav to pay the amount [sum]. The bid will be considered as paid in the next number of confirmations: [count].[:en_US]";}s:28:"generate_Address1_blockchain";a:4:{s:4:"send";i:0;s:5:"title";s:133:"[ru_RU:]Создан адрес для оплаты заявки [id][:ru_RU][en_US:]Address was created for payment bid [id][:en_US]";s:6:"tomail";s:0:"";s:4:"text";s:478:"[ru_RU:]Создан адрес [Address] для оплаты заявки ID [bid_id]. Необходимо оплатить сумму [sum]. Заявка будет считаться оплаченной при следующем количеств подтверждений: [count].[:ru_RU][en_US:]Address [Address] was created for payment bid [id]. You hav to pay the amount [sum]. The bid will be considered as paid in the next number of confirmations: [count].[:en_US]";}s:14:"btce_paycoupon";a:4:{s:4:"send";i:0;s:5:"title";s:120:"[ru_RU:]Ваш код купона для заявки [bid_id][:ru_RU][en_US:]Your coupon code for bid [bid_id][:en_US]";s:6:"tomail";s:0:"";s:4:"text";s:131:"[ru_RU:]Ваш код купона [id] для заявки [bid_id].[:ru_RU][en_US:]Your coupon [id] code for bid [bid_id][:en_US]";}s:14:"exmo_paycoupon";a:4:{s:4:"send";i:0;s:5:"title";s:120:"[ru_RU:]Ваш код купона для заявки [bid_id][:ru_RU][en_US:]Your coupon code for bid [bid_id][:en_US]";s:6:"tomail";s:0:"";s:4:"text";s:131:"[ru_RU:]Ваш код купона [id] для заявки [bid_id].[:ru_RU][en_US:]Your coupon [id] code for bid [bid_id][:en_US]";}s:18:"livecoin_paycoupon";a:4:{s:4:"send";i:0;s:5:"title";s:120:"[ru_RU:]Ваш код купона для заявки [bid_id][:ru_RU][en_US:]Your coupon code for bid [bid_id][:en_US]";s:6:"tomail";s:0:"";s:4:"text";s:131:"[ru_RU:]Ваш код купона [id] для заявки [bid_id].[:ru_RU][en_US:]Your coupon [id] code for bid [bid_id][:en_US]";}s:16:"contactform_auto";a:6:{s:4:"send";i:0;s:5:"title";s:105:"[ru_RU:]Мы получили ваше сообщение[:ru_RU][en_US:]We received your message[:en_US]";s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"name";s:0:"";s:4:"text";s:189:"[ru_RU:]Мы получили ваше сообщение. Ожидайте пожалуйста ответа.[:ru_RU][en_US:]We have received your message. Expect an answer please.[:en_US]";}s:13:"techpay_bids1";a:6:{s:4:"send";i:0;s:5:"title";s:166:"[ru_RU:]Пользователь перешел на страницу оплаты по заявка [id][:ru_RU][en_US:]User go to the payment for order [id][:en_US]";s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"name";s:0:"";s:4:"text";s:1111:"[ru_RU:]<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong><br>\r\nСсылка на заявку: [bidurl] <br><br>\r\n\r\n<strong>Информация о клиенте</strong><br>\r\nИмя: [last_name] [first_name] [second_name]<br>\r\nТелефон: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:ru_RU][en_US:]<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]<br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]<br>\r\nLink to order: [bidurl]<br><br>\r\n\r\n<strong>Customer information</strong><br>\r\nName: [last_name] [first_name] [second_name]<br>\r\nPhone: [user_phone]<br>\r\nEmail: [user_email]<br>\r\nSkype: [user_skype]<br>[:en_US]";}s:13:"techpay_bids2";a:6:{s:4:"send";i:0;s:5:"title";s:166:"[ru_RU:]Пользователь перешел на страницу оплаты по заявка [id][:ru_RU][en_US:]User go to the payment for order [id][:en_US]";s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"name";s:0:"";s:4:"text";s:935:"[ru_RU:]Здравствуйте.<br><br>\r\n\r\nСтатус заявки: пользователь перешел к оплате<br>\r\nСсылка на заявку: [bidurl]<br><br>\r\n\r\n<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong>[:ru_RU][en_US:]Hello.<br><br>\r\n\r\nOrder status: user go to the payment<br>\r\nLink to order: [bidurl]<br><br>\r\n\r\n<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]<br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]</strong><br>[:en_US]";}s:17:"payouterror_bids2";a:6:{s:4:"send";i:0;s:5:"title";s:125:"[ru_RU:]Ошибка авто выплаты в заявке [id][:ru_RU][en_US:]Automatic payout error in order [id][:en_US]";s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"name";s:0:"";s:4:"text";s:925:"[ru_RU:]Здравствуйте.<br><br>\r\n\r\nСтатус заявки: ошибка авто выплаты<br>\r\nСсылка на заявку: [bidurl]<br><br>\r\n\r\n<strong>Информация о заявке</strong><br>\r\nID [id] от [createdate]<br>\r\nКурс обмена: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nСумма обмена: <strong>[summ1] [valut1] [vtype1] со счета [account1] -> [summ2c] [valut2] [vtype2] на счет [account2]</strong>[:ru_RU][en_US:]Hello.<br><br>\r\n\r\nOrder status: automatic payout error<br>\r\nLink to order: [bidurl]<br><br>\r\n\r\n<strong>Order information</strong><br>\r\nID [id] by [createdate]<br>\r\nExchange rate: <strong>[curs1] [valut1] [vtype1] -> [curs2] [valut2] [vtype2]</strong><br>\r\nAmount of exchange: <strong>[summ1] [valut1] [vtype1] account [account1] -> [summ2c] [valut2] [vtype2] on account of [account2]</strong><br>[:en_US]";}s:14:"newreview_auto";a:6:{s:4:"send";i:0;s:5:"title";s:94:"[ru_RU:]Мы получили ваш отзыв[:ru_RU][en_US:]We received your review[:en_US]";s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"name";s:0:"";s:4:"text";s:187:"[ru_RU:]Мы получили ваш отзыв. Ожидайте пожалуйста модерации.[:ru_RU][en_US:]We have received your review. Expect a moderation please.[:en_US]";}s:9:"napsemail";a:6:{s:4:"send";i:0;s:5:"title";s:53:"[ru_RU:]Пин код[:ru_RU][en_US:]Pin code[:en_US]";s:4:"mail";s:0:"";s:6:"tomail";s:0:"";s:4:"name";s:0:"";s:4:"text";s:69:"[ru_RU:]Пин код: [code][:ru_RU][en_US:]Pin code: [code][:en_US]";}}', 'yes'),
(194, 'WPS_KEEP_NUM_ENTRIES_LT', '500', 'yes'),
(195, 'WPS_REFRESH_RATE_AJAX_LT', '10', 'yes'),
(231, 'inex_pages', 'a:2:{s:8:"toinvest";i:136;s:9:"indeposit";i:85;}', 'yes'),
(234, 'widget_get_pn_news', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(235, 'widget_get_pn_register', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(236, 'widget_get_pn_reserv', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(237, 'widget_get_pn_text', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(238, 'widget_pages', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(239, 'widget_calendar', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(240, 'widget_tag_cloud', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(241, 'widget_nav_menu', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(242, 'site_icon', '0', 'yes'),
(243, 'medium_large_size_w', '768', 'yes'),
(244, 'medium_large_size_h', '0', 'yes'),
(245, 'db_upgraded', '', 'yes'),
(259, 'widget_get_pn_lastobmens', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(262, 'pn_extended', 'a:3:{s:6:"moduls";a:81:{s:10:"timeout_ap";s:10:"timeout_ap";s:13:"accountverify";s:13:"accountverify";s:12:"admincaptcha";s:12:"admincaptcha";s:9:"apbd_logs";s:9:"apbd_logs";s:8:"apbytime";s:8:"apbytime";s:12:"archive_bids";s:12:"archive_bids";s:12:"autodel_bids";s:12:"autodel_bids";s:7:"autoreg";s:7:"autoreg";s:3:"bcc";s:3:"bcc";s:13:"beautyaccount";s:13:"beautyaccount";s:9:"beautynum";s:9:"beautynum";s:7:"bidlogs";s:7:"bidlogs";s:12:"bids_comment";s:12:"bids_comment";s:11:"bids_status";s:11:"bids_status";s:9:"blacklist";s:9:"blacklist";s:4:"cexp";s:4:"cexp";s:11:"checkstatus";s:11:"checkstatus";s:8:"contacts";s:8:"contacts";s:10:"corrreserv";s:10:"corrreserv";s:10:"courselogs";s:10:"courselogs";s:11:"cron_reserv";s:11:"cron_reserv";s:9:"currlimit";s:9:"currlimit";s:13:"currmaxreserv";s:13:"currmaxreserv";s:9:"currtable";s:9:"currtable";s:9:"discounts";s:9:"discounts";s:6:"domacc";s:6:"domacc";s:13:"dop_bidfilter";s:13:"dop_bidfilter";s:8:"editbids";s:8:"editbids";s:6:"export";s:6:"export";s:3:"fav";s:3:"fav";s:12:"files_reserv";s:12:"files_reserv";s:12:"finstats_bid";s:12:"finstats_bid";s:5:"geoip";s:5:"geoip";s:8:"headmess";s:8:"headmess";s:6:"hotkey";s:6:"hotkey";s:7:"htmlmap";s:7:"htmlmap";s:4:"live";s:4:"live";s:10:"livestatus";s:10:"livestatus";s:8:"mailsmtp";s:8:"mailsmtp";s:9:"mailtemps";s:9:"mailtemps";s:10:"maintrance";s:10:"maintrance";s:14:"many_operators";s:14:"many_operators";s:10:"masschange";s:10:"masschange";s:12:"maxpaybutton";s:12:"maxpaybutton";s:6:"mobile";s:6:"mobile";s:9:"mywarning";s:9:"mywarning";s:10:"naps_email";s:10:"naps_email";s:10:"naps_guest";s:10:"naps_guest";s:11:"naps_identy";s:11:"naps_identy";s:8:"naps_max";s:8:"naps_max";s:11:"naps_reserv";s:11:"naps_reserv";s:8:"naps_sms";s:8:"naps_sms";s:9:"napsfiles";s:9:"napsfiles";s:6:"napsip";s:6:"napsip";s:9:"napslangs";s:9:"napslangs";s:12:"napsredirect";s:12:"napsredirect";s:7:"newuser";s:7:"newuser";s:7:"numsymb";s:7:"numsymb";s:6:"parser";s:6:"parser";s:8:"partners";s:8:"partners";s:15:"paymerchantlogs";s:15:"paymerchantlogs";s:11:"payouterror";s:11:"payouterror";s:2:"pp";s:2:"pp";s:9:"qr_adress";s:9:"qr_adress";s:11:"recalc_bids";s:11:"recalc_bids";s:10:"reservcurs";s:10:"reservcurs";s:7:"reviews";s:7:"reviews";s:14:"search_realacc";s:14:"search_realacc";s:3:"seo";s:3:"seo";s:8:"setbidid";s:8:"setbidid";s:16:"sitecaptcha_plus";s:16:"sitecaptcha_plus";s:7:"sumcurs";s:7:"sumcurs";s:6:"tarifs";s:6:"tarifs";s:10:"user_login";s:10:"user_login";s:11:"userfilters";s:11:"userfilters";s:10:"userverify";s:10:"userverify";s:11:"userwallets";s:11:"userwallets";s:7:"userxch";s:7:"userxch";s:9:"vaccounts";s:9:"vaccounts";s:3:"x19";s:3:"x19";s:8:"zreserve";s:8:"zreserve";}s:12:"paymerchants";a:0:{}s:9:"merchants";a:0:{}}', 'yes');
INSERT INTO `pr_options` (`option_id`, `option_name`, `option_value`, `autoload`) VALUES
(265, 'paymerchants', 'a:13:{s:7:"blockio";i:0;s:4:"btce";i:0;s:6:"domacc";i:0;s:6:"edinar";i:0;s:8:"livecoin";i:0;s:8:"nixmoney";i:0;s:5:"okpay";i:0;s:12:"perfectmoney";i:0;s:6:"privat";i:0;s:8:"webmoney";i:0;s:7:"yamoney";i:0;s:4:"exmo";i:0;s:7:"advcash";i:0;}', 'yes'),
(275, 'merchants', 'a:23:{s:7:"advcash";i:0;s:10:"blockchain";i:0;s:7:"blockio";i:0;s:4:"btce";i:0;s:6:"domacc";i:0;s:6:"edinar";i:0;s:10:"helixmoney";i:0;s:6:"liqpay";i:0;s:8:"livecoin";i:0;s:8:"nixmoney";i:0;s:5:"okpay";i:0;s:6:"ooopay";i:0;s:5:"paxum";i:0;s:6:"payeer";i:0;s:6:"paymer";i:0;s:6:"paypal";i:0;s:12:"perfectmoney";i:0;s:6:"privat";i:0;s:8:"qiwishop";i:0;s:6:"webfin";i:0;s:8:"webmoney";i:0;s:7:"yamoney";i:0;s:8:"zpayment";i:0;}', 'yes'),
(278, 'rewrite_rules', 'a:86:{s:27:"exchange_([\\-_A-Za-z0-9]+)$";s:46:"index.php?pagename=exchange&pnhash=$matches[1]";s:22:"hst_([A-Za-z0-9]{35})$";s:41:"index.php?pagename=hst&hashed=$matches[1]";s:47:"category/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$";s:52:"index.php?category_name=$matches[1]&feed=$matches[2]";s:42:"category/(.+?)/(feed|rdf|rss|rss2|atom)/?$";s:52:"index.php?category_name=$matches[1]&feed=$matches[2]";s:23:"category/(.+?)/embed/?$";s:46:"index.php?category_name=$matches[1]&embed=true";s:35:"category/(.+?)/page/?([0-9]{1,})/?$";s:53:"index.php?category_name=$matches[1]&paged=$matches[2]";s:17:"category/(.+?)/?$";s:35:"index.php?category_name=$matches[1]";s:44:"tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:42:"index.php?tag=$matches[1]&feed=$matches[2]";s:39:"tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:42:"index.php?tag=$matches[1]&feed=$matches[2]";s:20:"tag/([^/]+)/embed/?$";s:36:"index.php?tag=$matches[1]&embed=true";s:32:"tag/([^/]+)/page/?([0-9]{1,})/?$";s:43:"index.php?tag=$matches[1]&paged=$matches[2]";s:14:"tag/([^/]+)/?$";s:25:"index.php?tag=$matches[1]";s:45:"type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?post_format=$matches[1]&feed=$matches[2]";s:40:"type/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?post_format=$matches[1]&feed=$matches[2]";s:21:"type/([^/]+)/embed/?$";s:44:"index.php?post_format=$matches[1]&embed=true";s:33:"type/([^/]+)/page/?([0-9]{1,})/?$";s:51:"index.php?post_format=$matches[1]&paged=$matches[2]";s:15:"type/([^/]+)/?$";s:33:"index.php?post_format=$matches[1]";s:12:"robots\\.txt$";s:18:"index.php?robots=1";s:48:".*wp-(atom|rdf|rss|rss2|feed|commentsrss2)\\.php$";s:18:"index.php?feed=old";s:20:".*wp-app\\.php(/.*)?$";s:19:"index.php?error=403";s:18:".*wp-register.php$";s:23:"index.php?register=true";s:32:"feed/(feed|rdf|rss|rss2|atom)/?$";s:27:"index.php?&feed=$matches[1]";s:27:"(feed|rdf|rss|rss2|atom)/?$";s:27:"index.php?&feed=$matches[1]";s:8:"embed/?$";s:21:"index.php?&embed=true";s:20:"page/?([0-9]{1,})/?$";s:28:"index.php?&paged=$matches[1]";s:27:"comment-page-([0-9]{1,})/?$";s:38:"index.php?&page_id=4&cpage=$matches[1]";s:41:"comments/feed/(feed|rdf|rss|rss2|atom)/?$";s:42:"index.php?&feed=$matches[1]&withcomments=1";s:36:"comments/(feed|rdf|rss|rss2|atom)/?$";s:42:"index.php?&feed=$matches[1]&withcomments=1";s:17:"comments/embed/?$";s:21:"index.php?&embed=true";s:44:"search/(.+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:40:"index.php?s=$matches[1]&feed=$matches[2]";s:39:"search/(.+)/(feed|rdf|rss|rss2|atom)/?$";s:40:"index.php?s=$matches[1]&feed=$matches[2]";s:20:"search/(.+)/embed/?$";s:34:"index.php?s=$matches[1]&embed=true";s:32:"search/(.+)/page/?([0-9]{1,})/?$";s:41:"index.php?s=$matches[1]&paged=$matches[2]";s:14:"search/(.+)/?$";s:23:"index.php?s=$matches[1]";s:47:"author/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?author_name=$matches[1]&feed=$matches[2]";s:42:"author/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:50:"index.php?author_name=$matches[1]&feed=$matches[2]";s:23:"author/([^/]+)/embed/?$";s:44:"index.php?author_name=$matches[1]&embed=true";s:35:"author/([^/]+)/page/?([0-9]{1,})/?$";s:51:"index.php?author_name=$matches[1]&paged=$matches[2]";s:17:"author/([^/]+)/?$";s:33:"index.php?author_name=$matches[1]";s:69:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$";s:80:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]";s:64:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$";s:80:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]";s:45:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/embed/?$";s:74:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&embed=true";s:57:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$";s:81:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]";s:39:"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$";s:63:"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]";s:56:"([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$";s:64:"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]";s:51:"([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$";s:64:"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]";s:32:"([0-9]{4})/([0-9]{1,2})/embed/?$";s:58:"index.php?year=$matches[1]&monthnum=$matches[2]&embed=true";s:44:"([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$";s:65:"index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]";s:26:"([0-9]{4})/([0-9]{1,2})/?$";s:47:"index.php?year=$matches[1]&monthnum=$matches[2]";s:43:"([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$";s:43:"index.php?year=$matches[1]&feed=$matches[2]";s:38:"([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$";s:43:"index.php?year=$matches[1]&feed=$matches[2]";s:19:"([0-9]{4})/embed/?$";s:37:"index.php?year=$matches[1]&embed=true";s:31:"([0-9]{4})/page/?([0-9]{1,})/?$";s:44:"index.php?year=$matches[1]&paged=$matches[2]";s:13:"([0-9]{4})/?$";s:26:"index.php?year=$matches[1]";s:27:".?.+?/attachment/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:37:".?.+?/attachment/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:57:".?.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:52:".?.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:52:".?.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:33:".?.+?/attachment/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:16:"(.?.+?)/embed/?$";s:41:"index.php?pagename=$matches[1]&embed=true";s:20:"(.?.+?)/trackback/?$";s:35:"index.php?pagename=$matches[1]&tb=1";s:40:"(.?.+?)/feed/(feed|rdf|rss|rss2|atom)/?$";s:47:"index.php?pagename=$matches[1]&feed=$matches[2]";s:35:"(.?.+?)/(feed|rdf|rss|rss2|atom)/?$";s:47:"index.php?pagename=$matches[1]&feed=$matches[2]";s:28:"(.?.+?)/page/?([0-9]{1,})/?$";s:48:"index.php?pagename=$matches[1]&paged=$matches[2]";s:35:"(.?.+?)/comment-page-([0-9]{1,})/?$";s:48:"index.php?pagename=$matches[1]&cpage=$matches[2]";s:24:"(.?.+?)(?:/([0-9]+))?/?$";s:47:"index.php?pagename=$matches[1]&page=$matches[2]";s:27:"[^/]+/attachment/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:37:"[^/]+/attachment/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:57:"[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:52:"[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:52:"[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:33:"[^/]+/attachment/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";s:16:"([^/]+)/embed/?$";s:37:"index.php?name=$matches[1]&embed=true";s:20:"([^/]+)/trackback/?$";s:31:"index.php?name=$matches[1]&tb=1";s:40:"([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:43:"index.php?name=$matches[1]&feed=$matches[2]";s:35:"([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:43:"index.php?name=$matches[1]&feed=$matches[2]";s:28:"([^/]+)/page/?([0-9]{1,})/?$";s:44:"index.php?name=$matches[1]&paged=$matches[2]";s:35:"([^/]+)/comment-page-([0-9]{1,})/?$";s:44:"index.php?name=$matches[1]&cpage=$matches[2]";s:24:"([^/]+)(?:/([0-9]+))?/?$";s:43:"index.php?name=$matches[1]&page=$matches[2]";s:16:"[^/]+/([^/]+)/?$";s:32:"index.php?attachment=$matches[1]";s:26:"[^/]+/([^/]+)/trackback/?$";s:37:"index.php?attachment=$matches[1]&tb=1";s:46:"[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:41:"[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$";s:49:"index.php?attachment=$matches[1]&feed=$matches[2]";s:41:"[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$";s:50:"index.php?attachment=$matches[1]&cpage=$matches[2]";s:22:"[^/]+/([^/]+)/embed/?$";s:43:"index.php?attachment=$matches[1]&embed=true";}', 'yes'),
(284, 'pn_version', 'a:3:{s:4:"text";s:133:"Доступно обновление 1.2. Обновитесь по инструкции https://premiumexchanger.com/upinstruction/";s:7:"text_en";s:116:"The required updаte 1.2 is available. Follow instructions https://premiumexchanger.com/upinstruction/ for updating.";s:7:"version";s:3:"1.2";}', 'yes'),
(294, 'reserv_auto', 'a:7:{i:0;s:3:"new";i:1;s:7:"techpay";i:2;s:5:"payed";i:3;s:7:"coldpay";i:4;s:7:"realpay";i:5;s:6:"verify";i:6;s:11:"coldsuccess";}', 'yes'),
(295, 'pn_mailtemp_modul', 'a:2:{s:4:"mail";s:0:"";s:4:"name";s:0:"";}', 'yes'),
(296, 'pn_update_plugin_text', '', 'yes'),
(303, 'widget_get_investbox_menu_widget', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(354, '_site_transient_update_core', 'O:8:"stdClass":4:{s:7:"updates";a:1:{i:0;O:8:"stdClass":10:{s:8:"response";s:6:"latest";s:8:"download";s:65:"https://downloads.wordpress.org/release/ru_RU/wordpress-4.8.1.zip";s:6:"locale";s:5:"ru_RU";s:8:"packages";O:8:"stdClass":5:{s:4:"full";s:65:"https://downloads.wordpress.org/release/ru_RU/wordpress-4.8.1.zip";s:10:"no_content";b:0;s:11:"new_bundled";b:0;s:7:"partial";b:0;s:8:"rollback";b:0;}s:7:"current";s:5:"4.8.1";s:7:"version";s:5:"4.8.1";s:11:"php_version";s:5:"5.2.4";s:13:"mysql_version";s:3:"5.0";s:11:"new_bundled";s:3:"4.7";s:15:"partial_version";s:0:"";}}s:12:"last_checked";i:1504259522;s:15:"version_checked";s:5:"4.8.1";s:12:"translations";a:1:{i:0;a:7:{s:4:"type";s:4:"core";s:4:"slug";s:7:"default";s:8:"language";s:5:"ru_RU";s:7:"version";s:5:"4.8.1";s:7:"updated";s:19:"2017-08-29 20:32:20";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.8.1/ru_RU.zip";s:10:"autoupdate";b:1;}}}', 'no'),
(355, '_site_transient_timeout_browser_e658c1376cbd18257a523918ab6aec0c', '1486294770', 'no'),
(356, '_site_transient_browser_e658c1376cbd18257a523918ab6aec0c', 'a:9:{s:8:"platform";s:7:"Windows";s:4:"name";s:6:"Chrome";s:7:"version";s:13:"54.0.2840.100";s:10:"update_url";s:28:"http://www.google.com/chrome";s:7:"img_src";s:49:"http://s.wordpress.org/images/browsers/chrome.png";s:11:"img_src_ssl";s:48:"https://wordpress.org/images/browsers/chrome.png";s:15:"current_version";s:2:"18";s:7:"upgrade";b:0;s:8:"insecure";b:0;}', 'no'),
(357, '_transient_timeout_plugin_slugs', '1501671577', 'no'),
(358, '_transient_plugin_slugs', 'a:3:{i:0;s:23:"investbox/investbox.php";i:1;s:25:"premiumbox/premiumbox.php";i:2;s:27:"premiumhook/premiumhook.php";}', 'no'),
(359, 'can_compress_scripts', '1', 'no'),
(365, '_site_transient_timeout_browser_754cdcc1e6416d7a56262cf3d275472d', '1490256070', 'no'),
(366, '_site_transient_browser_754cdcc1e6416d7a56262cf3d275472d', 'a:9:{s:8:"platform";s:7:"Windows";s:4:"name";s:6:"Chrome";s:7:"version";s:12:"56.0.2924.87";s:10:"update_url";s:28:"http://www.google.com/chrome";s:7:"img_src";s:49:"http://s.wordpress.org/images/browsers/chrome.png";s:11:"img_src_ssl";s:48:"https://wordpress.org/images/browsers/chrome.png";s:15:"current_version";s:2:"18";s:7:"upgrade";b:0;s:8:"insecure";b:0;}', 'no'),
(370, '_site_transient_timeout_browser_936759bdae83b222a75b49ffff83b023', '1491130057', 'no'),
(371, '_site_transient_browser_936759bdae83b222a75b49ffff83b023', 'a:9:{s:8:"platform";s:7:"Windows";s:4:"name";s:6:"Chrome";s:7:"version";s:13:"57.0.2987.110";s:10:"update_url";s:28:"http://www.google.com/chrome";s:7:"img_src";s:49:"http://s.wordpress.org/images/browsers/chrome.png";s:11:"img_src_ssl";s:48:"https://wordpress.org/images/browsers/chrome.png";s:15:"current_version";s:2:"18";s:7:"upgrade";b:0;s:8:"insecure";b:0;}', 'no'),
(380, 'fresh_site', '0', 'yes'),
(381, 'widget_get_pn_stats', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(388, '_site_transient_timeout_browser_bc5545d9d8bb94c2929ac607a22aba77', '1501314643', 'no'),
(389, '_site_transient_browser_bc5545d9d8bb94c2929ac607a22aba77', 'a:9:{s:8:"platform";s:7:"Windows";s:4:"name";s:6:"Chrome";s:7:"version";s:13:"58.0.3029.110";s:10:"update_url";s:28:"http://www.google.com/chrome";s:7:"img_src";s:49:"http://s.wordpress.org/images/browsers/chrome.png";s:11:"img_src_ssl";s:48:"https://wordpress.org/images/browsers/chrome.png";s:15:"current_version";s:2:"18";s:7:"upgrade";b:0;s:8:"insecure";b:0;}', 'no'),
(390, 'widget_get_pn_checkstatus', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(393, '_site_transient_timeout_available_translations', '1500737891', 'no');
INSERT INTO `pr_options` (`option_id`, `option_name`, `option_value`, `autoload`) VALUES
(394, '_site_transient_available_translations', 'a:108:{s:2:"af";a:8:{s:8:"language";s:2:"af";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-03-27 04:32:49";s:12:"english_name";s:9:"Afrikaans";s:11:"native_name";s:9:"Afrikaans";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.4/af.zip";s:3:"iso";a:2:{i:1;s:2:"af";i:2;s:3:"afr";}s:7:"strings";a:1:{s:8:"continue";s:10:"Gaan voort";}}s:3:"ary";a:8:{s:8:"language";s:3:"ary";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:42:35";s:12:"english_name";s:15:"Moroccan Arabic";s:11:"native_name";s:31:"العربية المغربية";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.7.4/ary.zip";s:3:"iso";a:2:{i:1;s:2:"ar";i:3;s:3:"ary";}s:7:"strings";a:1:{s:8:"continue";s:16:"المتابعة";}}s:2:"ar";a:8:{s:8:"language";s:2:"ar";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:49:08";s:12:"english_name";s:6:"Arabic";s:11:"native_name";s:14:"العربية";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.4/ar.zip";s:3:"iso";a:2:{i:1;s:2:"ar";i:2;s:3:"ara";}s:7:"strings";a:1:{s:8:"continue";s:16:"المتابعة";}}s:2:"as";a:8:{s:8:"language";s:2:"as";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-11-22 18:59:07";s:12:"english_name";s:8:"Assamese";s:11:"native_name";s:21:"অসমীয়া";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.2/as.zip";s:3:"iso";a:3:{i:1;s:2:"as";i:2;s:3:"asm";i:3;s:3:"asm";}s:7:"strings";a:1:{s:8:"continue";s:0:"";}}s:2:"az";a:8:{s:8:"language";s:2:"az";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-11-06 00:09:27";s:12:"english_name";s:11:"Azerbaijani";s:11:"native_name";s:16:"Azərbaycan dili";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.2/az.zip";s:3:"iso";a:2:{i:1;s:2:"az";i:2;s:3:"aze";}s:7:"strings";a:1:{s:8:"continue";s:5:"Davam";}}s:3:"azb";a:8:{s:8:"language";s:3:"azb";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-09-12 20:34:31";s:12:"english_name";s:17:"South Azerbaijani";s:11:"native_name";s:29:"گؤنئی آذربایجان";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.7.2/azb.zip";s:3:"iso";a:2:{i:1;s:2:"az";i:3;s:3:"azb";}s:7:"strings";a:1:{s:8:"continue";s:8:"Continue";}}s:3:"bel";a:8:{s:8:"language";s:3:"bel";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-05-09 11:39:31";s:12:"english_name";s:10:"Belarusian";s:11:"native_name";s:29:"Беларуская мова";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.7.4/bel.zip";s:3:"iso";a:2:{i:1;s:2:"be";i:2;s:3:"bel";}s:7:"strings";a:1:{s:8:"continue";s:20:"Працягнуць";}}s:5:"bg_BG";a:8:{s:8:"language";s:5:"bg_BG";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-05-16 13:06:08";s:12:"english_name";s:9:"Bulgarian";s:11:"native_name";s:18:"Български";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/bg_BG.zip";s:3:"iso";a:2:{i:1;s:2:"bg";i:2;s:3:"bul";}s:7:"strings";a:1:{s:8:"continue";s:12:"Напред";}}s:5:"bn_BD";a:8:{s:8:"language";s:5:"bn_BD";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2017-01-04 16:58:43";s:12:"english_name";s:7:"Bengali";s:11:"native_name";s:15:"বাংলা";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.2/bn_BD.zip";s:3:"iso";a:1:{i:1;s:2:"bn";}s:7:"strings";a:1:{s:8:"continue";s:23:"এগিয়ে চল.";}}s:2:"bo";a:8:{s:8:"language";s:2:"bo";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-09-05 09:44:12";s:12:"english_name";s:7:"Tibetan";s:11:"native_name";s:21:"བོད་ཡིག";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.2/bo.zip";s:3:"iso";a:2:{i:1;s:2:"bo";i:2;s:3:"tib";}s:7:"strings";a:1:{s:8:"continue";s:24:"མུ་མཐུད།";}}s:5:"bs_BA";a:8:{s:8:"language";s:5:"bs_BA";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-09-04 20:20:28";s:12:"english_name";s:7:"Bosnian";s:11:"native_name";s:8:"Bosanski";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.2/bs_BA.zip";s:3:"iso";a:2:{i:1;s:2:"bs";i:2;s:3:"bos";}s:7:"strings";a:1:{s:8:"continue";s:7:"Nastavi";}}s:2:"ca";a:8:{s:8:"language";s:2:"ca";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-05-12 09:29:39";s:12:"english_name";s:7:"Catalan";s:11:"native_name";s:7:"Català";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.4/ca.zip";s:3:"iso";a:2:{i:1;s:2:"ca";i:2;s:3:"cat";}s:7:"strings";a:1:{s:8:"continue";s:8:"Continua";}}s:3:"ceb";a:8:{s:8:"language";s:3:"ceb";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-03-02 17:25:51";s:12:"english_name";s:7:"Cebuano";s:11:"native_name";s:7:"Cebuano";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.7.2/ceb.zip";s:3:"iso";a:2:{i:2;s:3:"ceb";i:3;s:3:"ceb";}s:7:"strings";a:1:{s:8:"continue";s:7:"Padayun";}}s:5:"cs_CZ";a:8:{s:8:"language";s:5:"cs_CZ";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2017-01-12 08:46:26";s:12:"english_name";s:5:"Czech";s:11:"native_name";s:12:"Čeština‎";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.2/cs_CZ.zip";s:3:"iso";a:2:{i:1;s:2:"cs";i:2;s:3:"ces";}s:7:"strings";a:1:{s:8:"continue";s:11:"Pokračovat";}}s:2:"cy";a:8:{s:8:"language";s:2:"cy";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:49:29";s:12:"english_name";s:5:"Welsh";s:11:"native_name";s:7:"Cymraeg";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.4/cy.zip";s:3:"iso";a:2:{i:1;s:2:"cy";i:2;s:3:"cym";}s:7:"strings";a:1:{s:8:"continue";s:6:"Parhau";}}s:5:"da_DK";a:8:{s:8:"language";s:5:"da_DK";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-04-05 09:50:06";s:12:"english_name";s:6:"Danish";s:11:"native_name";s:5:"Dansk";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/da_DK.zip";s:3:"iso";a:2:{i:1;s:2:"da";i:2;s:3:"dan";}s:7:"strings";a:1:{s:8:"continue";s:8:"Fortsæt";}}s:14:"de_CH_informal";a:8:{s:8:"language";s:14:"de_CH_informal";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:39:59";s:12:"english_name";s:30:"German (Switzerland, Informal)";s:11:"native_name";s:21:"Deutsch (Schweiz, Du)";s:7:"package";s:73:"https://downloads.wordpress.org/translation/core/4.7.4/de_CH_informal.zip";s:3:"iso";a:1:{i:1;s:2:"de";}s:7:"strings";a:1:{s:8:"continue";s:6:"Weiter";}}s:5:"de_CH";a:8:{s:8:"language";s:5:"de_CH";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:40:03";s:12:"english_name";s:20:"German (Switzerland)";s:11:"native_name";s:17:"Deutsch (Schweiz)";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/de_CH.zip";s:3:"iso";a:1:{i:1;s:2:"de";}s:7:"strings";a:1:{s:8:"continue";s:6:"Weiter";}}s:12:"de_DE_formal";a:8:{s:8:"language";s:12:"de_DE_formal";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-04-28 14:35:15";s:12:"english_name";s:15:"German (Formal)";s:11:"native_name";s:13:"Deutsch (Sie)";s:7:"package";s:71:"https://downloads.wordpress.org/translation/core/4.7.4/de_DE_formal.zip";s:3:"iso";a:1:{i:1;s:2:"de";}s:7:"strings";a:1:{s:8:"continue";s:6:"Weiter";}}s:5:"de_DE";a:8:{s:8:"language";s:5:"de_DE";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-03-18 13:57:42";s:12:"english_name";s:6:"German";s:11:"native_name";s:7:"Deutsch";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/de_DE.zip";s:3:"iso";a:1:{i:1;s:2:"de";}s:7:"strings";a:1:{s:8:"continue";s:6:"Weiter";}}s:3:"dzo";a:8:{s:8:"language";s:3:"dzo";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-06-29 08:59:03";s:12:"english_name";s:8:"Dzongkha";s:11:"native_name";s:18:"རྫོང་ཁ";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.7.2/dzo.zip";s:3:"iso";a:2:{i:1;s:2:"dz";i:2;s:3:"dzo";}s:7:"strings";a:1:{s:8:"continue";s:0:"";}}s:2:"el";a:8:{s:8:"language";s:2:"el";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-05-11 20:10:39";s:12:"english_name";s:5:"Greek";s:11:"native_name";s:16:"Ελληνικά";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.4/el.zip";s:3:"iso";a:2:{i:1;s:2:"el";i:2;s:3:"ell";}s:7:"strings";a:1:{s:8:"continue";s:16:"Συνέχεια";}}s:5:"en_ZA";a:8:{s:8:"language";s:5:"en_ZA";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:53:43";s:12:"english_name";s:22:"English (South Africa)";s:11:"native_name";s:22:"English (South Africa)";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/en_ZA.zip";s:3:"iso";a:3:{i:1;s:2:"en";i:2;s:3:"eng";i:3;s:3:"eng";}s:7:"strings";a:1:{s:8:"continue";s:8:"Continue";}}s:5:"en_NZ";a:8:{s:8:"language";s:5:"en_NZ";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:54:30";s:12:"english_name";s:21:"English (New Zealand)";s:11:"native_name";s:21:"English (New Zealand)";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/en_NZ.zip";s:3:"iso";a:3:{i:1;s:2:"en";i:2;s:3:"eng";i:3;s:3:"eng";}s:7:"strings";a:1:{s:8:"continue";s:8:"Continue";}}s:5:"en_GB";a:8:{s:8:"language";s:5:"en_GB";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-28 03:10:25";s:12:"english_name";s:12:"English (UK)";s:11:"native_name";s:12:"English (UK)";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/en_GB.zip";s:3:"iso";a:3:{i:1;s:2:"en";i:2;s:3:"eng";i:3;s:3:"eng";}s:7:"strings";a:1:{s:8:"continue";s:8:"Continue";}}s:5:"en_AU";a:8:{s:8:"language";s:5:"en_AU";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-27 00:40:28";s:12:"english_name";s:19:"English (Australia)";s:11:"native_name";s:19:"English (Australia)";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/en_AU.zip";s:3:"iso";a:3:{i:1;s:2:"en";i:2;s:3:"eng";i:3;s:3:"eng";}s:7:"strings";a:1:{s:8:"continue";s:8:"Continue";}}s:5:"en_CA";a:8:{s:8:"language";s:5:"en_CA";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:49:34";s:12:"english_name";s:16:"English (Canada)";s:11:"native_name";s:16:"English (Canada)";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/en_CA.zip";s:3:"iso";a:3:{i:1;s:2:"en";i:2;s:3:"eng";i:3;s:3:"eng";}s:7:"strings";a:1:{s:8:"continue";s:8:"Continue";}}s:2:"eo";a:8:{s:8:"language";s:2:"eo";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-05-04 18:08:49";s:12:"english_name";s:9:"Esperanto";s:11:"native_name";s:9:"Esperanto";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.4/eo.zip";s:3:"iso";a:2:{i:1;s:2:"eo";i:2;s:3:"epo";}s:7:"strings";a:1:{s:8:"continue";s:8:"Daŭrigi";}}s:5:"es_VE";a:8:{s:8:"language";s:5:"es_VE";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-04-23 23:02:31";s:12:"english_name";s:19:"Spanish (Venezuela)";s:11:"native_name";s:21:"Español de Venezuela";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/es_VE.zip";s:3:"iso";a:2:{i:1;s:2:"es";i:2;s:3:"spa";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:5:"es_AR";a:8:{s:8:"language";s:5:"es_AR";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:41:31";s:12:"english_name";s:19:"Spanish (Argentina)";s:11:"native_name";s:21:"Español de Argentina";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/es_AR.zip";s:3:"iso";a:2:{i:1;s:2:"es";i:2;s:3:"spa";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:5:"es_MX";a:8:{s:8:"language";s:5:"es_MX";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:42:28";s:12:"english_name";s:16:"Spanish (Mexico)";s:11:"native_name";s:19:"Español de México";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/es_MX.zip";s:3:"iso";a:2:{i:1;s:2:"es";i:2;s:3:"spa";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:5:"es_GT";a:8:{s:8:"language";s:5:"es_GT";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:54:37";s:12:"english_name";s:19:"Spanish (Guatemala)";s:11:"native_name";s:21:"Español de Guatemala";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/es_GT.zip";s:3:"iso";a:2:{i:1;s:2:"es";i:2;s:3:"spa";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:5:"es_CO";a:8:{s:8:"language";s:5:"es_CO";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:54:37";s:12:"english_name";s:18:"Spanish (Colombia)";s:11:"native_name";s:20:"Español de Colombia";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/es_CO.zip";s:3:"iso";a:2:{i:1;s:2:"es";i:2;s:3:"spa";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:5:"es_PE";a:8:{s:8:"language";s:5:"es_PE";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-09-09 09:36:22";s:12:"english_name";s:14:"Spanish (Peru)";s:11:"native_name";s:17:"Español de Perú";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.2/es_PE.zip";s:3:"iso";a:2:{i:1;s:2:"es";i:2;s:3:"spa";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:5:"es_CL";a:8:{s:8:"language";s:5:"es_CL";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-11-28 20:09:49";s:12:"english_name";s:15:"Spanish (Chile)";s:11:"native_name";s:17:"Español de Chile";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.2/es_CL.zip";s:3:"iso";a:2:{i:1;s:2:"es";i:2;s:3:"spa";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:5:"es_ES";a:8:{s:8:"language";s:5:"es_ES";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-05-10 16:26:52";s:12:"english_name";s:15:"Spanish (Spain)";s:11:"native_name";s:8:"Español";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/es_ES.zip";s:3:"iso";a:1:{i:1;s:2:"es";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:2:"et";a:8:{s:8:"language";s:2:"et";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2017-01-27 16:37:11";s:12:"english_name";s:8:"Estonian";s:11:"native_name";s:5:"Eesti";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.2/et.zip";s:3:"iso";a:2:{i:1;s:2:"et";i:2;s:3:"est";}s:7:"strings";a:1:{s:8:"continue";s:6:"Jätka";}}s:2:"eu";a:8:{s:8:"language";s:2:"eu";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-05-12 06:40:28";s:12:"english_name";s:6:"Basque";s:11:"native_name";s:7:"Euskara";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.4/eu.zip";s:3:"iso";a:2:{i:1;s:2:"eu";i:2;s:3:"eus";}s:7:"strings";a:1:{s:8:"continue";s:8:"Jarraitu";}}s:5:"fa_IR";a:8:{s:8:"language";s:5:"fa_IR";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-02-02 15:21:03";s:12:"english_name";s:7:"Persian";s:11:"native_name";s:10:"فارسی";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/fa_IR.zip";s:3:"iso";a:2:{i:1;s:2:"fa";i:2;s:3:"fas";}s:7:"strings";a:1:{s:8:"continue";s:10:"ادامه";}}s:2:"fi";a:8:{s:8:"language";s:2:"fi";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:42:25";s:12:"english_name";s:7:"Finnish";s:11:"native_name";s:5:"Suomi";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.4/fi.zip";s:3:"iso";a:2:{i:1;s:2:"fi";i:2;s:3:"fin";}s:7:"strings";a:1:{s:8:"continue";s:5:"Jatka";}}s:5:"fr_BE";a:8:{s:8:"language";s:5:"fr_BE";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:40:32";s:12:"english_name";s:16:"French (Belgium)";s:11:"native_name";s:21:"Français de Belgique";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/fr_BE.zip";s:3:"iso";a:2:{i:1;s:2:"fr";i:2;s:3:"fra";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuer";}}s:5:"fr_FR";a:8:{s:8:"language";s:5:"fr_FR";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-05-05 12:10:24";s:12:"english_name";s:15:"French (France)";s:11:"native_name";s:9:"Français";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/fr_FR.zip";s:3:"iso";a:1:{i:1;s:2:"fr";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuer";}}s:5:"fr_CA";a:8:{s:8:"language";s:5:"fr_CA";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-02-03 21:08:25";s:12:"english_name";s:15:"French (Canada)";s:11:"native_name";s:19:"Français du Canada";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/fr_CA.zip";s:3:"iso";a:2:{i:1;s:2:"fr";i:2;s:3:"fra";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuer";}}s:2:"gd";a:8:{s:8:"language";s:2:"gd";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-08-23 17:41:37";s:12:"english_name";s:15:"Scottish Gaelic";s:11:"native_name";s:9:"Gàidhlig";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.2/gd.zip";s:3:"iso";a:3:{i:1;s:2:"gd";i:2;s:3:"gla";i:3;s:3:"gla";}s:7:"strings";a:1:{s:8:"continue";s:15:"Lean air adhart";}}s:5:"gl_ES";a:8:{s:8:"language";s:5:"gl_ES";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2017-01-26 15:40:27";s:12:"english_name";s:8:"Galician";s:11:"native_name";s:6:"Galego";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.2/gl_ES.zip";s:3:"iso";a:2:{i:1;s:2:"gl";i:2;s:3:"glg";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:2:"gu";a:8:{s:8:"language";s:2:"gu";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-04-21 14:17:42";s:12:"english_name";s:8:"Gujarati";s:11:"native_name";s:21:"ગુજરાતી";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.4/gu.zip";s:3:"iso";a:2:{i:1;s:2:"gu";i:2;s:3:"guj";}s:7:"strings";a:1:{s:8:"continue";s:31:"ચાલુ રાખવું";}}s:3:"haz";a:8:{s:8:"language";s:3:"haz";s:7:"version";s:5:"4.4.2";s:7:"updated";s:19:"2015-12-05 00:59:09";s:12:"english_name";s:8:"Hazaragi";s:11:"native_name";s:15:"هزاره گی";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.4.2/haz.zip";s:3:"iso";a:1:{i:3;s:3:"haz";}s:7:"strings";a:1:{s:8:"continue";s:10:"ادامه";}}s:5:"he_IL";a:8:{s:8:"language";s:5:"he_IL";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-29 21:21:10";s:12:"english_name";s:6:"Hebrew";s:11:"native_name";s:16:"עִבְרִית";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/he_IL.zip";s:3:"iso";a:1:{i:1;s:2:"he";}s:7:"strings";a:1:{s:8:"continue";s:8:"המשך";}}s:5:"hi_IN";a:8:{s:8:"language";s:5:"hi_IN";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-05-01 10:53:22";s:12:"english_name";s:5:"Hindi";s:11:"native_name";s:18:"हिन्दी";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/hi_IN.zip";s:3:"iso";a:2:{i:1;s:2:"hi";i:2;s:3:"hin";}s:7:"strings";a:1:{s:8:"continue";s:12:"जारी";}}s:2:"hr";a:8:{s:8:"language";s:2:"hr";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-03-28 13:34:22";s:12:"english_name";s:8:"Croatian";s:11:"native_name";s:8:"Hrvatski";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.4/hr.zip";s:3:"iso";a:2:{i:1;s:2:"hr";i:2;s:3:"hrv";}s:7:"strings";a:1:{s:8:"continue";s:7:"Nastavi";}}s:5:"hu_HU";a:8:{s:8:"language";s:5:"hu_HU";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2017-01-26 15:48:39";s:12:"english_name";s:9:"Hungarian";s:11:"native_name";s:6:"Magyar";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.2/hu_HU.zip";s:3:"iso";a:2:{i:1;s:2:"hu";i:2;s:3:"hun";}s:7:"strings";a:1:{s:8:"continue";s:10:"Folytatás";}}s:2:"hy";a:8:{s:8:"language";s:2:"hy";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-12-03 16:21:10";s:12:"english_name";s:8:"Armenian";s:11:"native_name";s:14:"Հայերեն";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.2/hy.zip";s:3:"iso";a:2:{i:1;s:2:"hy";i:2;s:3:"hye";}s:7:"strings";a:1:{s:8:"continue";s:20:"Շարունակել";}}s:5:"id_ID";a:8:{s:8:"language";s:5:"id_ID";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-05-02 14:01:52";s:12:"english_name";s:10:"Indonesian";s:11:"native_name";s:16:"Bahasa Indonesia";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/id_ID.zip";s:3:"iso";a:2:{i:1;s:2:"id";i:2;s:3:"ind";}s:7:"strings";a:1:{s:8:"continue";s:9:"Lanjutkan";}}s:5:"is_IS";a:8:{s:8:"language";s:5:"is_IS";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-04-13 13:55:54";s:12:"english_name";s:9:"Icelandic";s:11:"native_name";s:9:"Íslenska";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/is_IS.zip";s:3:"iso";a:2:{i:1;s:2:"is";i:2;s:3:"isl";}s:7:"strings";a:1:{s:8:"continue";s:6:"Áfram";}}s:5:"it_IT";a:8:{s:8:"language";s:5:"it_IT";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-04-08 04:57:54";s:12:"english_name";s:7:"Italian";s:11:"native_name";s:8:"Italiano";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/it_IT.zip";s:3:"iso";a:2:{i:1;s:2:"it";i:2;s:3:"ita";}s:7:"strings";a:1:{s:8:"continue";s:8:"Continua";}}s:2:"ja";a:8:{s:8:"language";s:2:"ja";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-05-02 05:13:51";s:12:"english_name";s:8:"Japanese";s:11:"native_name";s:9:"日本語";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.4/ja.zip";s:3:"iso";a:1:{i:1;s:2:"ja";}s:7:"strings";a:1:{s:8:"continue";s:9:"続ける";}}s:5:"ka_GE";a:8:{s:8:"language";s:5:"ka_GE";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-04-05 06:17:00";s:12:"english_name";s:8:"Georgian";s:11:"native_name";s:21:"ქართული";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/ka_GE.zip";s:3:"iso";a:2:{i:1;s:2:"ka";i:2;s:3:"kat";}s:7:"strings";a:1:{s:8:"continue";s:30:"გაგრძელება";}}s:3:"kab";a:8:{s:8:"language";s:3:"kab";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2017-01-26 15:39:13";s:12:"english_name";s:6:"Kabyle";s:11:"native_name";s:9:"Taqbaylit";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.7.2/kab.zip";s:3:"iso";a:2:{i:2;s:3:"kab";i:3;s:3:"kab";}s:7:"strings";a:1:{s:8:"continue";s:6:"Kemmel";}}s:2:"km";a:8:{s:8:"language";s:2:"km";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-12-07 02:07:59";s:12:"english_name";s:5:"Khmer";s:11:"native_name";s:27:"ភាសាខ្មែរ";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.2/km.zip";s:3:"iso";a:2:{i:1;s:2:"km";i:2;s:3:"khm";}s:7:"strings";a:1:{s:8:"continue";s:12:"បន្ត";}}s:5:"ko_KR";a:8:{s:8:"language";s:5:"ko_KR";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-04-18 05:09:08";s:12:"english_name";s:6:"Korean";s:11:"native_name";s:9:"한국어";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/ko_KR.zip";s:3:"iso";a:2:{i:1;s:2:"ko";i:2;s:3:"kor";}s:7:"strings";a:1:{s:8:"continue";s:6:"계속";}}s:3:"ckb";a:8:{s:8:"language";s:3:"ckb";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2017-01-26 15:48:25";s:12:"english_name";s:16:"Kurdish (Sorani)";s:11:"native_name";s:13:"كوردی‎";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.7.2/ckb.zip";s:3:"iso";a:2:{i:1;s:2:"ku";i:3;s:3:"ckb";}s:7:"strings";a:1:{s:8:"continue";s:30:"به‌رده‌وام به‌";}}s:2:"lo";a:8:{s:8:"language";s:2:"lo";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-11-12 09:59:23";s:12:"english_name";s:3:"Lao";s:11:"native_name";s:21:"ພາສາລາວ";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.2/lo.zip";s:3:"iso";a:2:{i:1;s:2:"lo";i:2;s:3:"lao";}s:7:"strings";a:1:{s:8:"continue";s:18:"ຕໍ່​ໄປ";}}s:5:"lt_LT";a:8:{s:8:"language";s:5:"lt_LT";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-03-30 09:46:13";s:12:"english_name";s:10:"Lithuanian";s:11:"native_name";s:15:"Lietuvių kalba";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/lt_LT.zip";s:3:"iso";a:2:{i:1;s:2:"lt";i:2;s:3:"lit";}s:7:"strings";a:1:{s:8:"continue";s:6:"Tęsti";}}s:2:"lv";a:8:{s:8:"language";s:2:"lv";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-03-17 20:40:40";s:12:"english_name";s:7:"Latvian";s:11:"native_name";s:16:"Latviešu valoda";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.4/lv.zip";s:3:"iso";a:2:{i:1;s:2:"lv";i:2;s:3:"lav";}s:7:"strings";a:1:{s:8:"continue";s:9:"Turpināt";}}s:5:"mk_MK";a:8:{s:8:"language";s:5:"mk_MK";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:54:41";s:12:"english_name";s:10:"Macedonian";s:11:"native_name";s:31:"Македонски јазик";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/mk_MK.zip";s:3:"iso";a:2:{i:1;s:2:"mk";i:2;s:3:"mkd";}s:7:"strings";a:1:{s:8:"continue";s:16:"Продолжи";}}s:5:"ml_IN";a:8:{s:8:"language";s:5:"ml_IN";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2017-01-27 03:43:32";s:12:"english_name";s:9:"Malayalam";s:11:"native_name";s:18:"മലയാളം";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.2/ml_IN.zip";s:3:"iso";a:2:{i:1;s:2:"ml";i:2;s:3:"mal";}s:7:"strings";a:1:{s:8:"continue";s:18:"തുടരുക";}}s:2:"mn";a:8:{s:8:"language";s:2:"mn";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2017-01-12 07:29:35";s:12:"english_name";s:9:"Mongolian";s:11:"native_name";s:12:"Монгол";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.2/mn.zip";s:3:"iso";a:2:{i:1;s:2:"mn";i:2;s:3:"mon";}s:7:"strings";a:1:{s:8:"continue";s:24:"Үргэлжлүүлэх";}}s:2:"mr";a:8:{s:8:"language";s:2:"mr";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-03-24 06:52:11";s:12:"english_name";s:7:"Marathi";s:11:"native_name";s:15:"मराठी";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.4/mr.zip";s:3:"iso";a:2:{i:1;s:2:"mr";i:2;s:3:"mar";}s:7:"strings";a:1:{s:8:"continue";s:25:"सुरु ठेवा";}}s:5:"ms_MY";a:8:{s:8:"language";s:5:"ms_MY";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-03-05 09:45:10";s:12:"english_name";s:5:"Malay";s:11:"native_name";s:13:"Bahasa Melayu";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/ms_MY.zip";s:3:"iso";a:2:{i:1;s:2:"ms";i:2;s:3:"msa";}s:7:"strings";a:1:{s:8:"continue";s:8:"Teruskan";}}s:5:"my_MM";a:8:{s:8:"language";s:5:"my_MM";s:7:"version";s:6:"4.1.18";s:7:"updated";s:19:"2015-03-26 15:57:42";s:12:"english_name";s:17:"Myanmar (Burmese)";s:11:"native_name";s:15:"ဗမာစာ";s:7:"package";s:65:"https://downloads.wordpress.org/translation/core/4.1.18/my_MM.zip";s:3:"iso";a:2:{i:1;s:2:"my";i:2;s:3:"mya";}s:7:"strings";a:1:{s:8:"continue";s:54:"ဆက်လက်လုပ်ဆောင်ပါ။";}}s:5:"nb_NO";a:8:{s:8:"language";s:5:"nb_NO";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:42:31";s:12:"english_name";s:19:"Norwegian (Bokmål)";s:11:"native_name";s:13:"Norsk bokmål";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/nb_NO.zip";s:3:"iso";a:2:{i:1;s:2:"nb";i:2;s:3:"nob";}s:7:"strings";a:1:{s:8:"continue";s:8:"Fortsett";}}s:5:"ne_NP";a:8:{s:8:"language";s:5:"ne_NP";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2017-01-26 15:48:31";s:12:"english_name";s:6:"Nepali";s:11:"native_name";s:18:"नेपाली";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.2/ne_NP.zip";s:3:"iso";a:2:{i:1;s:2:"ne";i:2;s:3:"nep";}s:7:"strings";a:1:{s:8:"continue";s:43:"जारी राख्नुहोस्";}}s:5:"nl_NL";a:8:{s:8:"language";s:5:"nl_NL";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-05-11 15:57:29";s:12:"english_name";s:5:"Dutch";s:11:"native_name";s:10:"Nederlands";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/nl_NL.zip";s:3:"iso";a:2:{i:1;s:2:"nl";i:2;s:3:"nld";}s:7:"strings";a:1:{s:8:"continue";s:8:"Doorgaan";}}s:12:"nl_NL_formal";a:8:{s:8:"language";s:12:"nl_NL_formal";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-02-16 13:24:21";s:12:"english_name";s:14:"Dutch (Formal)";s:11:"native_name";s:20:"Nederlands (Formeel)";s:7:"package";s:71:"https://downloads.wordpress.org/translation/core/4.7.4/nl_NL_formal.zip";s:3:"iso";a:2:{i:1;s:2:"nl";i:2;s:3:"nld";}s:7:"strings";a:1:{s:8:"continue";s:8:"Doorgaan";}}s:5:"nl_BE";a:8:{s:8:"language";s:5:"nl_BE";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-05-15 08:29:44";s:12:"english_name";s:15:"Dutch (Belgium)";s:11:"native_name";s:20:"Nederlands (België)";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/nl_BE.zip";s:3:"iso";a:2:{i:1;s:2:"nl";i:2;s:3:"nld";}s:7:"strings";a:1:{s:8:"continue";s:8:"Doorgaan";}}s:5:"nn_NO";a:8:{s:8:"language";s:5:"nn_NO";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:40:57";s:12:"english_name";s:19:"Norwegian (Nynorsk)";s:11:"native_name";s:13:"Norsk nynorsk";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/nn_NO.zip";s:3:"iso";a:2:{i:1;s:2:"nn";i:2;s:3:"nno";}s:7:"strings";a:1:{s:8:"continue";s:9:"Hald fram";}}s:3:"oci";a:8:{s:8:"language";s:3:"oci";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2017-01-02 13:47:38";s:12:"english_name";s:7:"Occitan";s:11:"native_name";s:7:"Occitan";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.7.2/oci.zip";s:3:"iso";a:2:{i:1;s:2:"oc";i:2;s:3:"oci";}s:7:"strings";a:1:{s:8:"continue";s:9:"Contunhar";}}s:5:"pa_IN";a:8:{s:8:"language";s:5:"pa_IN";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2017-01-16 05:19:43";s:12:"english_name";s:7:"Punjabi";s:11:"native_name";s:18:"ਪੰਜਾਬੀ";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.2/pa_IN.zip";s:3:"iso";a:2:{i:1;s:2:"pa";i:2;s:3:"pan";}s:7:"strings";a:1:{s:8:"continue";s:25:"ਜਾਰੀ ਰੱਖੋ";}}s:5:"pl_PL";a:8:{s:8:"language";s:5:"pl_PL";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-04-23 09:31:28";s:12:"english_name";s:6:"Polish";s:11:"native_name";s:6:"Polski";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/pl_PL.zip";s:3:"iso";a:2:{i:1;s:2:"pl";i:2;s:3:"pol";}s:7:"strings";a:1:{s:8:"continue";s:9:"Kontynuuj";}}s:2:"ps";a:8:{s:8:"language";s:2:"ps";s:7:"version";s:6:"4.1.18";s:7:"updated";s:19:"2015-03-29 22:19:48";s:12:"english_name";s:6:"Pashto";s:11:"native_name";s:8:"پښتو";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.1.18/ps.zip";s:3:"iso";a:2:{i:1;s:2:"ps";i:2;s:3:"pus";}s:7:"strings";a:1:{s:8:"continue";s:19:"دوام ورکړه";}}s:5:"pt_BR";a:8:{s:8:"language";s:5:"pt_BR";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-04-17 15:02:48";s:12:"english_name";s:19:"Portuguese (Brazil)";s:11:"native_name";s:20:"Português do Brasil";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/pt_BR.zip";s:3:"iso";a:2:{i:1;s:2:"pt";i:2;s:3:"por";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:5:"pt_PT";a:8:{s:8:"language";s:5:"pt_PT";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-05-15 10:57:32";s:12:"english_name";s:21:"Portuguese (Portugal)";s:11:"native_name";s:10:"Português";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/pt_PT.zip";s:3:"iso";a:1:{i:1;s:2:"pt";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuar";}}s:3:"rhg";a:8:{s:8:"language";s:3:"rhg";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-03-16 13:03:18";s:12:"english_name";s:8:"Rohingya";s:11:"native_name";s:8:"Ruáinga";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.7.2/rhg.zip";s:3:"iso";a:1:{i:3;s:3:"rhg";}s:7:"strings";a:1:{s:8:"continue";s:0:"";}}s:5:"ro_RO";a:8:{s:8:"language";s:5:"ro_RO";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-04-15 14:53:36";s:12:"english_name";s:8:"Romanian";s:11:"native_name";s:8:"Română";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/ro_RO.zip";s:3:"iso";a:2:{i:1;s:2:"ro";i:2;s:3:"ron";}s:7:"strings";a:1:{s:8:"continue";s:9:"Continuă";}}s:5:"ru_RU";a:8:{s:8:"language";s:5:"ru_RU";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-04-20 10:13:53";s:12:"english_name";s:7:"Russian";s:11:"native_name";s:14:"Русский";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/ru_RU.zip";s:3:"iso";a:2:{i:1;s:2:"ru";i:2;s:3:"rus";}s:7:"strings";a:1:{s:8:"continue";s:20:"Продолжить";}}s:3:"sah";a:8:{s:8:"language";s:3:"sah";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2017-01-21 02:06:41";s:12:"english_name";s:5:"Sakha";s:11:"native_name";s:14:"Сахалыы";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.7.2/sah.zip";s:3:"iso";a:2:{i:2;s:3:"sah";i:3;s:3:"sah";}s:7:"strings";a:1:{s:8:"continue";s:12:"Салҕаа";}}s:5:"si_LK";a:8:{s:8:"language";s:5:"si_LK";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-11-12 06:00:52";s:12:"english_name";s:7:"Sinhala";s:11:"native_name";s:15:"සිංහල";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.2/si_LK.zip";s:3:"iso";a:2:{i:1;s:2:"si";i:2;s:3:"sin";}s:7:"strings";a:1:{s:8:"continue";s:44:"දිගටම කරගෙන යන්න";}}s:5:"sk_SK";a:8:{s:8:"language";s:5:"sk_SK";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-05-10 13:48:29";s:12:"english_name";s:6:"Slovak";s:11:"native_name";s:11:"Slovenčina";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/sk_SK.zip";s:3:"iso";a:2:{i:1;s:2:"sk";i:2;s:3:"slk";}s:7:"strings";a:1:{s:8:"continue";s:12:"Pokračovať";}}s:5:"sl_SI";a:8:{s:8:"language";s:5:"sl_SI";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-02-08 17:57:45";s:12:"english_name";s:9:"Slovenian";s:11:"native_name";s:13:"Slovenščina";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/sl_SI.zip";s:3:"iso";a:2:{i:1;s:2:"sl";i:2;s:3:"slv";}s:7:"strings";a:1:{s:8:"continue";s:8:"Nadaljuj";}}s:2:"sq";a:8:{s:8:"language";s:2:"sq";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-04-24 08:35:30";s:12:"english_name";s:8:"Albanian";s:11:"native_name";s:5:"Shqip";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.4/sq.zip";s:3:"iso";a:2:{i:1;s:2:"sq";i:2;s:3:"sqi";}s:7:"strings";a:1:{s:8:"continue";s:6:"Vazhdo";}}s:5:"sr_RS";a:8:{s:8:"language";s:5:"sr_RS";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:41:03";s:12:"english_name";s:7:"Serbian";s:11:"native_name";s:23:"Српски језик";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/sr_RS.zip";s:3:"iso";a:2:{i:1;s:2:"sr";i:2;s:3:"srp";}s:7:"strings";a:1:{s:8:"continue";s:14:"Настави";}}s:5:"sv_SE";a:8:{s:8:"language";s:5:"sv_SE";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-04-03 00:34:10";s:12:"english_name";s:7:"Swedish";s:11:"native_name";s:7:"Svenska";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/sv_SE.zip";s:3:"iso";a:2:{i:1;s:2:"sv";i:2;s:3:"swe";}s:7:"strings";a:1:{s:8:"continue";s:9:"Fortsätt";}}s:3:"szl";a:8:{s:8:"language";s:3:"szl";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-09-24 19:58:14";s:12:"english_name";s:8:"Silesian";s:11:"native_name";s:17:"Ślōnskŏ gŏdka";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.7.2/szl.zip";s:3:"iso";a:1:{i:3;s:3:"szl";}s:7:"strings";a:1:{s:8:"continue";s:13:"Kōntynuować";}}s:5:"ta_IN";a:8:{s:8:"language";s:5:"ta_IN";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2017-01-27 03:22:47";s:12:"english_name";s:5:"Tamil";s:11:"native_name";s:15:"தமிழ்";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.2/ta_IN.zip";s:3:"iso";a:2:{i:1;s:2:"ta";i:2;s:3:"tam";}s:7:"strings";a:1:{s:8:"continue";s:24:"தொடரவும்";}}s:2:"te";a:8:{s:8:"language";s:2:"te";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2017-01-26 15:47:39";s:12:"english_name";s:6:"Telugu";s:11:"native_name";s:18:"తెలుగు";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.2/te.zip";s:3:"iso";a:2:{i:1;s:2:"te";i:2;s:3:"tel";}s:7:"strings";a:1:{s:8:"continue";s:30:"కొనసాగించు";}}s:2:"th";a:8:{s:8:"language";s:2:"th";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2017-01-26 15:48:43";s:12:"english_name";s:4:"Thai";s:11:"native_name";s:9:"ไทย";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.2/th.zip";s:3:"iso";a:2:{i:1;s:2:"th";i:2;s:3:"tha";}s:7:"strings";a:1:{s:8:"continue";s:15:"ต่อไป";}}s:2:"tl";a:8:{s:8:"language";s:2:"tl";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-12-30 02:38:08";s:12:"english_name";s:7:"Tagalog";s:11:"native_name";s:7:"Tagalog";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.2/tl.zip";s:3:"iso";a:2:{i:1;s:2:"tl";i:2;s:3:"tgl";}s:7:"strings";a:1:{s:8:"continue";s:10:"Magpatuloy";}}s:5:"tr_TR";a:8:{s:8:"language";s:5:"tr_TR";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-04-15 09:03:35";s:12:"english_name";s:7:"Turkish";s:11:"native_name";s:8:"Türkçe";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/tr_TR.zip";s:3:"iso";a:2:{i:1;s:2:"tr";i:2;s:3:"tur";}s:7:"strings";a:1:{s:8:"continue";s:5:"Devam";}}s:5:"tt_RU";a:8:{s:8:"language";s:5:"tt_RU";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-11-20 20:20:50";s:12:"english_name";s:5:"Tatar";s:11:"native_name";s:19:"Татар теле";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.2/tt_RU.zip";s:3:"iso";a:2:{i:1;s:2:"tt";i:2;s:3:"tat";}s:7:"strings";a:1:{s:8:"continue";s:17:"дәвам итү";}}s:3:"tah";a:8:{s:8:"language";s:3:"tah";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-03-06 18:39:39";s:12:"english_name";s:8:"Tahitian";s:11:"native_name";s:10:"Reo Tahiti";s:7:"package";s:62:"https://downloads.wordpress.org/translation/core/4.7.2/tah.zip";s:3:"iso";a:3:{i:1;s:2:"ty";i:2;s:3:"tah";i:3;s:3:"tah";}s:7:"strings";a:1:{s:8:"continue";s:0:"";}}s:5:"ug_CN";a:8:{s:8:"language";s:5:"ug_CN";s:7:"version";s:5:"4.7.2";s:7:"updated";s:19:"2016-12-05 09:23:39";s:12:"english_name";s:6:"Uighur";s:11:"native_name";s:9:"Uyƣurqə";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.2/ug_CN.zip";s:3:"iso";a:2:{i:1;s:2:"ug";i:2;s:3:"uig";}s:7:"strings";a:1:{s:8:"continue";s:26:"داۋاملاشتۇرۇش";}}s:2:"uk";a:8:{s:8:"language";s:2:"uk";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-05-17 03:13:31";s:12:"english_name";s:9:"Ukrainian";s:11:"native_name";s:20:"Українська";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.4/uk.zip";s:3:"iso";a:2:{i:1;s:2:"uk";i:2;s:3:"ukr";}s:7:"strings";a:1:{s:8:"continue";s:20:"Продовжити";}}s:2:"ur";a:8:{s:8:"language";s:2:"ur";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-03-27 07:08:07";s:12:"english_name";s:4:"Urdu";s:11:"native_name";s:8:"اردو";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.4/ur.zip";s:3:"iso";a:2:{i:1;s:2:"ur";i:2;s:3:"urd";}s:7:"strings";a:1:{s:8:"continue";s:19:"جاری رکھیں";}}s:5:"uz_UZ";a:8:{s:8:"language";s:5:"uz_UZ";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-05-13 09:55:38";s:12:"english_name";s:5:"Uzbek";s:11:"native_name";s:11:"O‘zbekcha";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/uz_UZ.zip";s:3:"iso";a:2:{i:1;s:2:"uz";i:2;s:3:"uzb";}s:7:"strings";a:1:{s:8:"continue";s:11:"Davom etish";}}s:2:"vi";a:8:{s:8:"language";s:2:"vi";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-04-10 15:33:37";s:12:"english_name";s:10:"Vietnamese";s:11:"native_name";s:14:"Tiếng Việt";s:7:"package";s:61:"https://downloads.wordpress.org/translation/core/4.7.4/vi.zip";s:3:"iso";a:2:{i:1;s:2:"vi";i:2;s:3:"vie";}s:7:"strings";a:1:{s:8:"continue";s:12:"Tiếp tục";}}s:5:"zh_CN";a:8:{s:8:"language";s:5:"zh_CN";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-01-26 15:54:45";s:12:"english_name";s:15:"Chinese (China)";s:11:"native_name";s:12:"简体中文";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/zh_CN.zip";s:3:"iso";a:2:{i:1;s:2:"zh";i:2;s:3:"zho";}s:7:"strings";a:1:{s:8:"continue";s:6:"继续";}}s:5:"zh_HK";a:8:{s:8:"language";s:5:"zh_HK";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-03-28 12:03:30";s:12:"english_name";s:19:"Chinese (Hong Kong)";s:11:"native_name";s:16:"香港中文版	";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/zh_HK.zip";s:3:"iso";a:2:{i:1;s:2:"zh";i:2;s:3:"zho";}s:7:"strings";a:1:{s:8:"continue";s:6:"繼續";}}s:5:"zh_TW";a:8:{s:8:"language";s:5:"zh_TW";s:7:"version";s:5:"4.7.4";s:7:"updated";s:19:"2017-05-08 04:16:08";s:12:"english_name";s:16:"Chinese (Taiwan)";s:11:"native_name";s:12:"繁體中文";s:7:"package";s:64:"https://downloads.wordpress.org/translation/core/4.7.4/zh_TW.zip";s:3:"iso";a:2:{i:1;s:2:"zh";i:2;s:3:"zho";}s:7:"strings";a:1:{s:8:"continue";s:6:"繼續";}}}', 'no'),
(400, '_site_transient_timeout_browser_8ce1bea2f653ca2ce71bdf6183bef333', '1502185865', 'no'),
(401, '_site_transient_browser_8ce1bea2f653ca2ce71bdf6183bef333', 'a:9:{s:8:"platform";s:7:"Windows";s:4:"name";s:6:"Chrome";s:7:"version";s:13:"59.0.3071.115";s:10:"update_url";s:28:"http://www.google.com/chrome";s:7:"img_src";s:49:"http://s.wordpress.org/images/browsers/chrome.png";s:11:"img_src_ssl";s:48:"https://wordpress.org/images/browsers/chrome.png";s:15:"current_version";s:2:"18";s:7:"upgrade";b:0;s:8:"insecure";b:0;}', 'no'),
(402, 'napsip', 'a:8:{i:0;s:3:"new";i:1;s:7:"techpay";i:2;s:5:"payed";i:3;s:7:"coldpay";i:4;s:7:"realpay";i:5;s:6:"verify";i:6;s:11:"coldsuccess";i:7;s:7:"success";}', 'yes'),
(408, 'widget_media_audio', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(409, 'widget_media_image', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(410, 'widget_media_video', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(411, 'widget_custom_html', 'a:1:{s:12:"_multiwidget";i:1;}', 'yes'),
(423, '_site_transient_timeout_browser_3182f755614c06389a440066daa2f279', '1503420973', 'no'),
(424, '_site_transient_browser_3182f755614c06389a440066daa2f279', 'a:9:{s:8:"platform";s:7:"Windows";s:4:"name";s:6:"Chrome";s:7:"version";s:12:"60.0.3112.90";s:10:"update_url";s:28:"http://www.google.com/chrome";s:7:"img_src";s:49:"http://s.wordpress.org/images/browsers/chrome.png";s:11:"img_src_ssl";s:48:"https://wordpress.org/images/browsers/chrome.png";s:15:"current_version";s:2:"18";s:7:"upgrade";b:0;s:8:"insecure";b:0;}', 'no'),
(426, '_site_transient_timeout_theme_roots', '1504261324', 'no'),
(427, '_site_transient_theme_roots', 'a:1:{s:9:"exchanger";s:7:"/themes";}', 'no'),
(428, '_site_transient_timeout_browser_f9694186c5800b9905943d3f44ede836', '1504864349', 'no'),
(429, '_site_transient_browser_f9694186c5800b9905943d3f44ede836', 'a:9:{s:8:"platform";s:7:"Windows";s:4:"name";s:6:"Chrome";s:7:"version";s:13:"60.0.3112.113";s:10:"update_url";s:28:"http://www.google.com/chrome";s:7:"img_src";s:49:"http://s.wordpress.org/images/browsers/chrome.png";s:11:"img_src_ssl";s:48:"https://wordpress.org/images/browsers/chrome.png";s:15:"current_version";s:2:"18";s:7:"upgrade";b:0;s:8:"insecure";b:0;}', 'no');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_partners`
--

CREATE TABLE IF NOT EXISTS `pr_partners` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` longtext NOT NULL,
  `link` tinytext NOT NULL,
  `img` longtext NOT NULL,
  `site_order` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Дамп данных таблицы `pr_partners`
--

INSERT INTO `pr_partners` (`id`, `title`, `link`, `img`, `site_order`) VALUES
(1, '[en_US:]Bitcoin[:en_US][ru_RU:]Bitcoin[:ru_RU]', '#', '/wp-content/uploads/bitcoin-bottom.png', 0),
(2, '[en_US:]Okpay[:en_US][ru_RU:]Okpay[:ru_RU]', '#', '/wp-content/uploads/okpay-bottom.png', 0),
(3, '[en_US:]Perfect Money[:en_US][ru_RU:]Perfect Money[:ru_RU]', '#', '/wp-content/uploads/pm-bottom.png', 0),
(4, '[en_US:]Solidtrustpay[:en_US][ru_RU:]Solidtrustpay[:ru_RU]', '#', '/wp-content/uploads/stp-bottom.png', 0),
(5, '[en_US:]Yandex.Money[:en_US][ru_RU:]Яндекс.Деньги[:ru_RU]', '#', '/wp-content/uploads/ya-bottom.png', 0),
(6, '[en_US:]Webmoney[:en_US][ru_RU:]Webmoney[:ru_RU]', '#', '/wp-content/uploads/wm-botton.png', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `pr_partner_pers`
--

CREATE TABLE IF NOT EXISTS `pr_partner_pers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sumec` varchar(50) NOT NULL DEFAULT '0',
  `pers` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `pr_partner_pers`
--

INSERT INTO `pr_partner_pers` (`id`, `sumec`, `pers`) VALUES
(1, '0', '0.1'),
(2, '500', '0.2'),
(3, '5000', '0.3'),
(4, '10000', '0.4'),
(5, '15000', '0.5');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_paymerchant_logs`
--

CREATE TABLE IF NOT EXISTS `pr_paymerchant_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `createdate` datetime NOT NULL,
  `bid_id` bigint(20) NOT NULL DEFAULT '0',
  `mdata` longtext NOT NULL,
  `merchant` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_payoutuser`
--

CREATE TABLE IF NOT EXISTS `pr_payoutuser` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pay_date` datetime NOT NULL,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `user_login` varchar(250) NOT NULL,
  `pay_sum` varchar(250) NOT NULL DEFAULT '0',
  `pay_sum_or` varchar(250) NOT NULL DEFAULT '0',
  `valut_id` bigint(20) NOT NULL DEFAULT '0',
  `psys_title` longtext NOT NULL,
  `vtype_id` bigint(20) NOT NULL DEFAULT '0',
  `vtype_title` varchar(250) NOT NULL,
  `pay_account` varchar(250) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `comment` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_plinks`
--

CREATE TABLE IF NOT EXISTS `pr_plinks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `user_login` varchar(250) NOT NULL,
  `pdate` datetime NOT NULL,
  `pbrowser` longtext NOT NULL,
  `pip` longtext NOT NULL,
  `prefer` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_postmeta`
--

CREATE TABLE IF NOT EXISTS `pr_postmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `post_id` (`post_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=654 ;

--
-- Дамп данных таблицы `pr_postmeta`
--

INSERT INTO `pr_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES
(2, 4, '_wp_page_template', 'pn-homepage.php'),
(3, 11, '_wp_page_template', 'pn-pluginpage.php'),
(4, 12, '_wp_page_template', 'pn-whitepage.php'),
(5, 13, '_wp_page_template', 'pn-pluginpage.php'),
(6, 14, '_wp_page_template', 'pn-pluginpage.php'),
(7, 15, '_wp_page_template', 'pn-pluginpage.php'),
(8, 16, '_wp_page_template', 'pn-pluginpage.php'),
(9, 17, '_wp_page_template', 'pn-pluginpage.php'),
(10, 18, '_wp_page_template', 'pn-pluginpage.php'),
(11, 19, '_wp_page_template', 'pn-pluginpage.php'),
(12, 20, '_wp_page_template', 'pn-pluginpage.php'),
(13, 21, '_wp_page_template', 'pn-pluginpage.php'),
(16, 24, '_wp_page_template', 'pn-pluginpage.php'),
(17, 25, '_wp_page_template', 'pn-pluginpage.php'),
(18, 26, '_wp_page_template', 'pn-pluginpage.php'),
(19, 27, '_wp_page_template', 'pn-pluginpage.php'),
(20, 28, '_wp_page_template', 'pn-pluginpage.php'),
(21, 29, '_wp_page_template', 'pn-pluginpage.php'),
(22, 30, '_menu_item_type', 'post_type'),
(23, 30, '_menu_item_menu_item_parent', '0'),
(24, 30, '_menu_item_object_id', '4'),
(25, 30, '_menu_item_object', 'page'),
(26, 30, '_menu_item_target', ''),
(27, 30, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(28, 30, '_menu_item_xfn', ''),
(29, 30, '_menu_item_url', ''),
(31, 31, '_menu_item_type', 'post_type'),
(32, 31, '_menu_item_menu_item_parent', '0'),
(33, 31, '_menu_item_object_id', '10'),
(34, 31, '_menu_item_object', 'page'),
(35, 31, '_menu_item_target', ''),
(36, 31, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(37, 31, '_menu_item_xfn', ''),
(38, 31, '_menu_item_url', ''),
(40, 32, '_menu_item_type', 'post_type'),
(41, 32, '_menu_item_menu_item_parent', '0'),
(42, 32, '_menu_item_object_id', '5'),
(43, 32, '_menu_item_object', 'page'),
(44, 32, '_menu_item_target', ''),
(45, 32, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(46, 32, '_menu_item_xfn', ''),
(47, 32, '_menu_item_url', ''),
(49, 33, '_menu_item_type', 'post_type'),
(50, 33, '_menu_item_menu_item_parent', '0'),
(51, 33, '_menu_item_object_id', '18'),
(52, 33, '_menu_item_object', 'page'),
(53, 33, '_menu_item_target', ''),
(54, 33, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(55, 33, '_menu_item_xfn', ''),
(56, 33, '_menu_item_url', ''),
(58, 34, '_menu_item_type', 'post_type'),
(59, 34, '_menu_item_menu_item_parent', '0'),
(60, 34, '_menu_item_object_id', '17'),
(61, 34, '_menu_item_object', 'page'),
(62, 34, '_menu_item_target', ''),
(63, 34, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(64, 34, '_menu_item_xfn', ''),
(65, 34, '_menu_item_url', ''),
(67, 35, '_menu_item_type', 'post_type'),
(68, 35, '_menu_item_menu_item_parent', '0'),
(69, 35, '_menu_item_object_id', '16'),
(70, 35, '_menu_item_object', 'page'),
(71, 35, '_menu_item_target', ''),
(72, 35, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(73, 35, '_menu_item_xfn', ''),
(74, 35, '_menu_item_url', ''),
(76, 36, '_menu_item_type', 'post_type'),
(77, 36, '_menu_item_menu_item_parent', '0'),
(78, 36, '_menu_item_object_id', '7'),
(79, 36, '_menu_item_object', 'page'),
(80, 36, '_menu_item_target', ''),
(81, 36, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(82, 36, '_menu_item_xfn', ''),
(83, 36, '_menu_item_url', ''),
(94, 38, '_edit_last', '1'),
(96, 38, 'seo_title', ''),
(97, 38, 'seo_key', ''),
(98, 38, 'seo_descr', ''),
(99, 38, 'ogp_title', ''),
(100, 38, 'ogp_descr', ''),
(101, 38, '_edit_lock', '1481443290:1'),
(102, 8, '_edit_lock', '1445588870:1'),
(103, 8, '_edit_last', '1'),
(104, 8, '_wp_page_template', 'default'),
(105, 8, 'seo_title', ''),
(106, 8, 'seo_key', ''),
(107, 8, 'seo_descr', ''),
(108, 8, 'ogp_title', ''),
(109, 8, 'ogp_descr', ''),
(110, 6, '_edit_lock', '1460208649:1'),
(111, 6, '_edit_last', '1'),
(112, 6, '_wp_page_template', 'pn-whitepage.php'),
(113, 6, 'seo_title', ''),
(114, 6, 'seo_key', ''),
(115, 6, 'seo_descr', ''),
(116, 6, 'ogp_title', ''),
(117, 6, 'ogp_descr', ''),
(118, 7, '_edit_lock', '1460208650:1'),
(119, 7, '_edit_last', '1'),
(120, 7, '_wp_page_template', 'pn-whitepage.php'),
(121, 7, 'seo_title', ''),
(122, 7, 'seo_key', ''),
(123, 7, 'seo_descr', ''),
(124, 7, 'ogp_title', ''),
(125, 7, 'ogp_descr', ''),
(126, 12, '_edit_lock', '1460208577:1'),
(127, 12, '_edit_last', '1'),
(128, 12, 'seo_title', ''),
(129, 12, 'seo_key', ''),
(130, 12, 'seo_descr', ''),
(131, 12, 'ogp_title', ''),
(132, 12, 'ogp_descr', ''),
(133, 11, '_edit_lock', '1445768110:1'),
(134, 16, '_edit_lock', '1445768193:1'),
(135, 11, '_edit_last', '1'),
(136, 11, 'seo_title', ''),
(137, 11, 'seo_key', ''),
(138, 11, 'seo_descr', ''),
(139, 11, 'ogp_title', ''),
(140, 11, 'ogp_descr', ''),
(141, 21, '_edit_lock', '1445768164:1'),
(142, 19, '_edit_lock', '1445768166:1'),
(143, 20, '_edit_lock', '1445768185:1'),
(144, 13, '_edit_lock', '1445768187:1'),
(145, 29, '_edit_lock', '1445768189:1'),
(146, 4, '_edit_lock', '1445768191:1'),
(147, 10, '_edit_lock', '1445768194:1'),
(148, 10, '_edit_last', '1'),
(149, 10, '_wp_page_template', 'default'),
(150, 10, 'seo_title', ''),
(151, 10, 'seo_key', ''),
(152, 10, 'seo_descr', ''),
(153, 10, 'ogp_title', ''),
(154, 10, 'ogp_descr', ''),
(155, 16, '_edit_last', '1'),
(156, 16, 'seo_title', ''),
(157, 16, 'seo_key', ''),
(158, 16, 'seo_descr', ''),
(159, 16, 'ogp_title', ''),
(160, 16, 'ogp_descr', ''),
(161, 4, '_edit_last', '1'),
(162, 4, 'seo_title', ''),
(163, 4, 'seo_key', ''),
(164, 4, 'seo_descr', ''),
(165, 4, 'ogp_title', ''),
(166, 4, 'ogp_descr', ''),
(167, 29, '_edit_last', '1'),
(168, 29, 'seo_title', ''),
(169, 29, 'seo_key', ''),
(170, 29, 'seo_descr', ''),
(171, 29, 'ogp_title', ''),
(172, 29, 'ogp_descr', ''),
(173, 13, '_edit_last', '1'),
(174, 13, 'seo_title', ''),
(175, 13, 'seo_key', ''),
(176, 13, 'seo_descr', ''),
(177, 13, 'ogp_title', ''),
(178, 13, 'ogp_descr', ''),
(179, 20, '_edit_last', '1'),
(180, 20, 'seo_title', ''),
(181, 20, 'seo_key', ''),
(182, 20, 'seo_descr', ''),
(183, 20, 'ogp_title', ''),
(184, 20, 'ogp_descr', ''),
(185, 19, '_edit_last', '1'),
(186, 19, 'seo_title', ''),
(187, 19, 'seo_key', ''),
(188, 19, 'seo_descr', ''),
(189, 19, 'ogp_title', ''),
(190, 19, 'ogp_descr', ''),
(191, 21, '_edit_last', '1'),
(192, 21, 'seo_title', ''),
(193, 21, 'seo_key', ''),
(194, 21, 'seo_descr', ''),
(195, 21, 'ogp_title', ''),
(196, 21, 'ogp_descr', ''),
(197, 14, '_edit_lock', '1445768196:1'),
(198, 15, '_edit_lock', '1445768198:1'),
(199, 5, '_edit_lock', '1445589805:1'),
(202, 18, '_edit_lock', '1445589832:1'),
(203, 26, '_edit_lock', '1445589871:1'),
(204, 27, '_edit_lock', '1445589887:1'),
(205, 24, '_edit_lock', '1445589908:1'),
(206, 25, '_edit_lock', '1445589934:1'),
(207, 28, '_edit_lock', '1445589953:1'),
(208, 17, '_edit_lock', '1445589956:1'),
(209, 14, '_edit_last', '1'),
(210, 14, 'seo_title', ''),
(211, 14, 'seo_key', ''),
(212, 14, 'seo_descr', ''),
(213, 14, 'ogp_title', ''),
(214, 14, 'ogp_descr', ''),
(215, 15, '_edit_last', '1'),
(216, 15, 'seo_title', ''),
(217, 15, 'seo_key', ''),
(218, 15, 'seo_descr', ''),
(219, 15, 'ogp_title', ''),
(220, 15, 'ogp_descr', ''),
(221, 5, '_edit_last', '1'),
(222, 5, 'seo_title', ''),
(223, 5, 'seo_key', ''),
(224, 5, 'seo_descr', ''),
(225, 5, 'ogp_title', ''),
(226, 5, 'ogp_descr', ''),
(239, 18, '_edit_last', '1'),
(240, 18, 'seo_title', ''),
(241, 18, 'seo_key', ''),
(242, 18, 'seo_descr', ''),
(243, 18, 'ogp_title', ''),
(244, 18, 'ogp_descr', ''),
(245, 26, '_edit_last', '1'),
(246, 26, 'seo_title', ''),
(247, 26, 'seo_key', ''),
(248, 26, 'seo_descr', ''),
(249, 26, 'ogp_title', ''),
(250, 26, 'ogp_descr', ''),
(251, 27, '_edit_last', '1'),
(252, 27, 'seo_title', ''),
(253, 27, 'seo_key', ''),
(254, 27, 'seo_descr', ''),
(255, 27, 'ogp_title', ''),
(256, 27, 'ogp_descr', ''),
(257, 24, '_edit_last', '1'),
(258, 24, 'seo_title', ''),
(259, 24, 'seo_key', ''),
(260, 24, 'seo_descr', ''),
(261, 24, 'ogp_title', ''),
(262, 24, 'ogp_descr', ''),
(263, 25, '_edit_last', '1'),
(264, 25, 'seo_title', ''),
(265, 25, 'seo_key', ''),
(266, 25, 'seo_descr', ''),
(267, 25, 'ogp_title', ''),
(268, 25, 'ogp_descr', ''),
(269, 28, '_edit_last', '1'),
(270, 28, 'seo_title', ''),
(271, 28, 'seo_key', ''),
(272, 28, 'seo_descr', ''),
(273, 28, 'ogp_title', ''),
(274, 28, 'ogp_descr', ''),
(275, 17, '_edit_last', '1'),
(276, 17, 'seo_title', ''),
(277, 17, 'seo_key', ''),
(278, 17, 'seo_descr', ''),
(279, 17, 'ogp_title', ''),
(280, 17, 'ogp_descr', ''),
(281, 77, '_menu_item_type', 'post_type'),
(282, 77, '_menu_item_menu_item_parent', '0'),
(283, 77, '_menu_item_object_id', '6'),
(284, 77, '_menu_item_object', 'page'),
(285, 77, '_menu_item_target', ''),
(286, 77, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(287, 77, '_menu_item_xfn', ''),
(288, 77, '_menu_item_url', ''),
(291, 85, '_wp_page_template', 'pn-pluginpage.php'),
(292, 90, '_wp_page_template', 'pn-pluginpage.php'),
(293, 90, '_edit_lock', '1500728642:1'),
(295, 85, '_edit_lock', '1456238988:1'),
(296, 90, '_edit_last', '1'),
(297, 90, 'seo_title', ''),
(298, 90, 'seo_key', ''),
(299, 90, 'seo_descr', ''),
(300, 90, 'ogp_title', ''),
(301, 90, 'ogp_descr', ''),
(308, 85, '_edit_last', '1'),
(309, 85, 'seo_title', ''),
(310, 85, 'seo_key', ''),
(311, 85, 'seo_descr', ''),
(312, 85, 'ogp_title', ''),
(313, 85, 'ogp_descr', ''),
(321, 96, '_wp_attached_file', 'Advcash.png'),
(322, 96, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:11:"Advcash.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(323, 97, '_wp_attached_file', 'Alfabank.png'),
(324, 97, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:12:"Alfabank.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(325, 98, '_wp_attached_file', 'Bitcoin.png'),
(326, 98, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:11:"Bitcoin.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(327, 99, '_wp_attached_file', 'BTC-e.png'),
(328, 99, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:9:"BTC-e.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(329, 100, '_wp_attached_file', 'Helixmoney.png'),
(330, 100, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:14:"Helixmoney.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(331, 101, '_wp_attached_file', 'Liqpay.png'),
(332, 101, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:10:"Liqpay.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(333, 102, '_wp_attached_file', 'Litecoin.png'),
(334, 102, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:12:"Litecoin.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(335, 103, '_wp_attached_file', 'Livecoin.png'),
(336, 103, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:12:"Livecoin.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(337, 104, '_wp_attached_file', 'NixMoney.png'),
(338, 104, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:12:"NixMoney.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(339, 105, '_wp_attached_file', 'Okpay.png'),
(340, 105, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:9:"Okpay.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(341, 106, '_wp_attached_file', 'Ooopay.png'),
(342, 106, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:10:"Ooopay.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(343, 107, '_wp_attached_file', 'Paxum.png'),
(344, 107, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:9:"Paxum.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(345, 108, '_wp_attached_file', 'Payeer.png'),
(346, 108, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:10:"Payeer.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(347, 109, '_wp_attached_file', 'Paymer.png'),
(348, 109, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:10:"Paymer.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(349, 110, '_wp_attached_file', 'Paypal.png'),
(350, 110, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:10:"Paypal.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(351, 111, '_wp_attached_file', 'Payza.png'),
(352, 111, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:9:"Payza.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(353, 112, '_wp_attached_file', 'Perfect-Money.png'),
(354, 112, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:17:"Perfect-Money.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(355, 113, '_wp_attached_file', 'Privatbank.png'),
(356, 113, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:14:"Privatbank.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(357, 114, '_wp_attached_file', 'Qiwi.png'),
(358, 114, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:8:"Qiwi.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(359, 115, '_wp_attached_file', 'Sberbank.png'),
(360, 115, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:12:"Sberbank.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(361, 116, '_wp_attached_file', 'Skrill.png'),
(362, 116, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:10:"Skrill.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(363, 117, '_wp_attached_file', 'SolidTrustPay.png'),
(364, 117, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:17:"SolidTrustPay.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(365, 118, '_wp_attached_file', 'Tinkoff.png'),
(366, 118, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:11:"Tinkoff.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(367, 119, '_wp_attached_file', 'Visa-MasterCard.png'),
(368, 119, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:19:"Visa-MasterCard.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(369, 120, '_wp_attached_file', 'WebMoney.png'),
(370, 120, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:12:"WebMoney.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(371, 121, '_wp_attached_file', 'Webtransfer.png'),
(372, 121, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:15:"Webtransfer.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(373, 122, '_wp_attached_file', 'Yandex.png'),
(374, 122, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:10:"Yandex.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(375, 123, '_wp_attached_file', 'Z-payment.png'),
(376, 123, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:13:"Z-payment.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(377, 124, '_wp_attached_file', 'favicon.png'),
(378, 124, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:54;s:6:"height";i:38;s:4:"file";s:11:"favicon.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(379, 125, '_wp_attached_file', 'bitcoin-bottom.png'),
(380, 125, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:88;s:6:"height";i:31;s:4:"file";s:18:"bitcoin-bottom.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(381, 126, '_wp_attached_file', 'okpay-bottom.png'),
(382, 126, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:88;s:6:"height";i:31;s:4:"file";s:16:"okpay-bottom.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(383, 127, '_wp_attached_file', 'pm-bottom.png'),
(384, 127, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:88;s:6:"height";i:31;s:4:"file";s:13:"pm-bottom.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(385, 128, '_wp_attached_file', 'stp-bottom.png'),
(386, 128, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:88;s:6:"height";i:31;s:4:"file";s:14:"stp-bottom.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(387, 129, '_wp_attached_file', 'wm-botton.png'),
(388, 129, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:88;s:6:"height";i:31;s:4:"file";s:13:"wm-botton.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(389, 130, '_wp_attached_file', 'ya-bottom.png'),
(390, 130, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:88;s:6:"height";i:31;s:4:"file";s:13:"ya-bottom.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(393, 136, '_wp_page_template', 'pn-pluginpage.php'),
(394, 136, '_edit_lock', '1456238927:1'),
(395, 136, '_edit_last', '1'),
(396, 136, 'seo_title', ''),
(397, 136, 'seo_key', ''),
(398, 136, 'seo_descr', ''),
(399, 136, 'ogp_title', ''),
(400, 136, 'ogp_descr', ''),
(402, 139, '_wp_attached_file', 'exmo.png'),
(403, 139, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:8:"exmo.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(405, 143, '_wp_attached_file', 'Alipay.png'),
(406, 143, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:10:"Alipay.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(416, 148, '_menu_item_type', 'post_type'),
(417, 148, '_menu_item_menu_item_parent', '0'),
(418, 148, '_menu_item_object_id', '4'),
(419, 148, '_menu_item_object', 'page'),
(420, 148, '_menu_item_target', ''),
(421, 148, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(422, 148, '_menu_item_xfn', ''),
(423, 148, '_menu_item_url', ''),
(425, 149, '_menu_item_type', 'post_type'),
(426, 149, '_menu_item_menu_item_parent', '0'),
(427, 149, '_menu_item_object_id', '11'),
(428, 149, '_menu_item_object', 'page'),
(429, 149, '_menu_item_target', ''),
(430, 149, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(431, 149, '_menu_item_xfn', ''),
(432, 149, '_menu_item_url', ''),
(434, 150, '_menu_item_type', 'post_type'),
(435, 150, '_menu_item_menu_item_parent', '0'),
(436, 150, '_menu_item_object_id', '21'),
(437, 150, '_menu_item_object', 'page'),
(438, 150, '_menu_item_target', ''),
(439, 150, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(440, 150, '_menu_item_xfn', ''),
(441, 150, '_menu_item_url', ''),
(443, 151, '_menu_item_type', 'post_type'),
(444, 151, '_menu_item_menu_item_parent', '0'),
(445, 151, '_menu_item_object_id', '19'),
(446, 151, '_menu_item_object', 'page'),
(447, 151, '_menu_item_target', ''),
(448, 151, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(449, 151, '_menu_item_xfn', ''),
(450, 151, '_menu_item_url', ''),
(452, 152, '_menu_item_type', 'post_type'),
(453, 152, '_menu_item_menu_item_parent', '0'),
(454, 152, '_menu_item_object_id', '16'),
(455, 152, '_menu_item_object', 'page'),
(456, 152, '_menu_item_target', ''),
(457, 152, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(458, 152, '_menu_item_xfn', ''),
(459, 152, '_menu_item_url', ''),
(461, 153, '_menu_item_type', 'post_type'),
(462, 153, '_menu_item_menu_item_parent', '0'),
(463, 153, '_menu_item_object_id', '10'),
(464, 153, '_menu_item_object', 'page'),
(465, 153, '_menu_item_target', ''),
(466, 153, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(467, 153, '_menu_item_xfn', ''),
(468, 153, '_menu_item_url', ''),
(470, 154, '_menu_item_type', 'post_type'),
(471, 154, '_menu_item_menu_item_parent', '0'),
(472, 154, '_menu_item_object_id', '14'),
(473, 154, '_menu_item_object', 'page'),
(474, 154, '_menu_item_target', ''),
(475, 154, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(476, 154, '_menu_item_xfn', ''),
(477, 154, '_menu_item_url', ''),
(479, 155, '_menu_item_type', 'post_type'),
(480, 155, '_menu_item_menu_item_parent', '0'),
(481, 155, '_menu_item_object_id', '15'),
(482, 155, '_menu_item_object', 'page'),
(483, 155, '_menu_item_target', ''),
(484, 155, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(485, 155, '_menu_item_xfn', ''),
(486, 155, '_menu_item_url', ''),
(488, 156, '_menu_item_type', 'post_type'),
(489, 156, '_menu_item_menu_item_parent', '0'),
(490, 156, '_menu_item_object_id', '5'),
(491, 156, '_menu_item_object', 'page'),
(492, 156, '_menu_item_target', ''),
(493, 156, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(494, 156, '_menu_item_xfn', ''),
(495, 156, '_menu_item_url', ''),
(497, 157, '_menu_item_type', 'post_type'),
(498, 157, '_menu_item_menu_item_parent', '0'),
(499, 157, '_menu_item_object_id', '18'),
(500, 157, '_menu_item_object', 'page'),
(501, 157, '_menu_item_target', ''),
(502, 157, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(503, 157, '_menu_item_xfn', ''),
(504, 157, '_menu_item_url', ''),
(506, 158, '_menu_item_type', 'post_type'),
(507, 158, '_menu_item_menu_item_parent', '0'),
(508, 158, '_menu_item_object_id', '26'),
(509, 158, '_menu_item_object', 'page'),
(510, 158, '_menu_item_target', ''),
(511, 158, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(512, 158, '_menu_item_xfn', ''),
(513, 158, '_menu_item_url', ''),
(515, 159, '_menu_item_type', 'post_type'),
(516, 159, '_menu_item_menu_item_parent', '0'),
(517, 159, '_menu_item_object_id', '27'),
(518, 159, '_menu_item_object', 'page'),
(519, 159, '_menu_item_target', ''),
(520, 159, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(521, 159, '_menu_item_xfn', ''),
(522, 159, '_menu_item_url', ''),
(524, 160, '_menu_item_type', 'post_type'),
(525, 160, '_menu_item_menu_item_parent', '0'),
(526, 160, '_menu_item_object_id', '8'),
(527, 160, '_menu_item_object', 'page'),
(528, 160, '_menu_item_target', ''),
(529, 160, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(530, 160, '_menu_item_xfn', ''),
(531, 160, '_menu_item_url', ''),
(533, 161, '_menu_item_type', 'post_type'),
(534, 161, '_menu_item_menu_item_parent', '0'),
(535, 161, '_menu_item_object_id', '24'),
(536, 161, '_menu_item_object', 'page'),
(537, 161, '_menu_item_target', ''),
(538, 161, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(539, 161, '_menu_item_xfn', ''),
(540, 161, '_menu_item_url', ''),
(542, 162, '_menu_item_type', 'post_type'),
(543, 162, '_menu_item_menu_item_parent', '0'),
(544, 162, '_menu_item_object_id', '6'),
(545, 162, '_menu_item_object', 'page'),
(546, 162, '_menu_item_target', ''),
(547, 162, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(548, 162, '_menu_item_xfn', ''),
(549, 162, '_menu_item_url', ''),
(551, 163, '_menu_item_type', 'post_type'),
(552, 163, '_menu_item_menu_item_parent', '0'),
(553, 163, '_menu_item_object_id', '12'),
(554, 163, '_menu_item_object', 'page'),
(555, 163, '_menu_item_target', ''),
(556, 163, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(557, 163, '_menu_item_xfn', ''),
(558, 163, '_menu_item_url', ''),
(560, 164, '_menu_item_type', 'post_type'),
(561, 164, '_menu_item_menu_item_parent', '0'),
(562, 164, '_menu_item_object_id', '25'),
(563, 164, '_menu_item_object', 'page'),
(564, 164, '_menu_item_target', ''),
(565, 164, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(566, 164, '_menu_item_xfn', ''),
(567, 164, '_menu_item_url', ''),
(569, 165, '_menu_item_type', 'post_type'),
(570, 165, '_menu_item_menu_item_parent', '0'),
(571, 165, '_menu_item_object_id', '28'),
(572, 165, '_menu_item_object', 'page'),
(573, 165, '_menu_item_target', ''),
(574, 165, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(575, 165, '_menu_item_xfn', ''),
(576, 165, '_menu_item_url', ''),
(578, 166, '_menu_item_type', 'post_type'),
(579, 166, '_menu_item_menu_item_parent', '0'),
(580, 166, '_menu_item_object_id', '17'),
(581, 166, '_menu_item_object', 'page'),
(582, 166, '_menu_item_target', ''),
(583, 166, '_menu_item_classes', 'a:1:{i:0;s:0:"";}'),
(584, 166, '_menu_item_xfn', ''),
(585, 166, '_menu_item_url', ''),
(586, 170, '_wp_attached_file', 'Avangardbank.png'),
(587, 170, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:16:"Avangardbank.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(588, 171, '_wp_attached_file', 'Bank-perevod.png'),
(589, 171, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:16:"Bank-perevod.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(590, 172, '_wp_attached_file', 'Cash-EUR.png'),
(591, 172, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:12:"Cash-EUR.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(592, 173, '_wp_attached_file', 'Cash-RUB.png'),
(593, 173, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:12:"Cash-RUB.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(594, 174, '_wp_attached_file', 'Cash-USD.png'),
(595, 174, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:12:"Cash-USD.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(596, 175, '_wp_attached_file', 'PMe-voucher.png'),
(597, 175, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:15:"PMe-voucher.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(598, 176, '_wp_attached_file', 'Promsvyazbank.png'),
(599, 176, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:17:"Promsvyazbank.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(600, 177, '_wp_attached_file', 'Russstandart.png'),
(601, 177, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:16:"Russstandart.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(602, 178, '_wp_attached_file', 'VTB24.png'),
(603, 178, '_wp_attachment_metadata', 'a:5:{s:5:"width";i:50;s:6:"height";i:50;s:4:"file";s:9:"VTB24.png";s:5:"sizes";a:0:{}s:10:"image_meta";a:12:{s:8:"aperture";s:1:"0";s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";s:1:"0";s:9:"copyright";s:0:"";s:12:"focal_length";s:1:"0";s:3:"iso";s:1:"0";s:13:"shutter_speed";s:1:"0";s:5:"title";s:0:"";s:11:"orientation";s:1:"0";s:8:"keywords";a:0:{}}}'),
(605, 181, '_wp_page_template', 'pn-pluginpage.php'),
(606, 182, '_wp_page_template', 'pn-pluginpage.php'),
(607, 183, '_wp_page_template', 'pn-pluginpage.php'),
(636, 182, '_edit_lock', '1501585406:1'),
(637, 183, '_edit_lock', '1501585391:1'),
(638, 183, '_edit_last', '1'),
(639, 183, 'seo_title', ''),
(640, 183, 'seo_key', ''),
(641, 183, 'seo_descr', ''),
(642, 183, 'ogp_title', ''),
(643, 183, 'ogp_descr', ''),
(644, 182, '_edit_last', '1'),
(645, 182, 'seo_title', ''),
(646, 182, 'seo_key', ''),
(647, 182, 'seo_descr', ''),
(648, 182, 'ogp_title', ''),
(649, 182, 'ogp_descr', ''),
(653, 206, '_wp_page_template', 'pn-pluginpage.php');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_posts`
--

CREATE TABLE IF NOT EXISTS `pr_posts` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_author` bigint(20) unsigned NOT NULL DEFAULT '0',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext NOT NULL,
  `post_title` text NOT NULL,
  `post_excerpt` text NOT NULL,
  `post_status` varchar(20) NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) NOT NULL DEFAULT 'open',
  `post_password` varchar(255) NOT NULL DEFAULT '',
  `post_name` varchar(200) NOT NULL DEFAULT '',
  `to_ping` text NOT NULL,
  `pinged` text NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` longtext NOT NULL,
  `post_parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `guid` varchar(255) NOT NULL DEFAULT '',
  `menu_order` int(11) NOT NULL DEFAULT '0',
  `post_type` varchar(20) NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) NOT NULL DEFAULT '',
  `comment_count` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `post_name` (`post_name`(191)),
  KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
  KEY `post_parent` (`post_parent`),
  KEY `post_author` (`post_author`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=209 ;

--
-- Дамп данных таблицы `pr_posts`
--

INSERT INTO `pr_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES
(4, 1, '2015-10-22 17:31:49', '2015-10-22 14:31:49', '', '[ru_RU:]Главная[:ru_RU][en_US:]Home[:en_US]', '', 'publish', 'closed', 'closed', '', 'home', '', '', '2015-10-23 11:30:56', '2015-10-23 08:30:56', '', 0, 'http://premiumexchanger.ru/home/', 0, 'page', '', 0),
(5, 1, '2015-10-22 17:31:49', '2015-10-22 14:31:49', '', '[ru_RU:]Новости[:ru_RU][en_US:]News[:en_US]', '', 'publish', 'closed', 'closed', '', 'news', '', '', '2015-10-23 11:45:41', '2015-10-23 08:45:41', '', 0, 'http://premiumexchanger.ru/news/', 0, 'page', '', 0),
(6, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:]<strong>1. Стороны соглашения.</strong>\r\n\r\nДоговор заключается между интернет сервисом по обмену титульных знаков, далее Исполнитель, — с одной стороны, и Заказчик, в лице того, кто воспользовался услугами Исполнителя, — с другой стороны.\r\n\r\n<strong>2. Список терминов.</strong>\r\n\r\n2.1. Обмен титульных знаков — автоматизированный продукт интернет обслуживания, который предоставляется Исполнителем на основании данных правил.\r\n2.2. Заказчик — физическое лицо, соглашающееся с условиями Исполнителя и данного соглашения, к которому присоединяется.\r\n2.3. Титульный знак — условная единица той или иной платежной системы, которая соответствует расчетам электронных систем и обозначает объем прав, соответствующих договору системы электронной оплаты и ее Заказчика.\r\n2.4. Заявка — сведения, переданные Заказчиком для использования средств Исполнителя в электронном виде и свидетельствующие о том, что он принимает условия пользования сервисом, которые предлагаются Исполнителем в данной заявке.\r\n\r\n<strong>3. Условия соглашения.</strong>\r\n\r\nДанные правила считаются организованными за счет условий общественной оферты, которая образуется во время подачи Заказчиком заявки и является одной из главных составляющих настоящего договора. Общественной офертой именуются отображаемые исполнителем сведения об условиях подачи заявки. Главным составляющим общественной оферты являются действия, сделанные в завершении подачи заявки Заказчиком и говорящие о его точных намерениях совершить сделку на условиях предложенных Исполнителем перед завершением данной заявки. Время, дата, и параметры заявки создаются Исполнителем автоматически в момент окончания формирования данной заявки. Предложение должно приняться Заказчиком в течение 24 часов от окончания формирования заявки. Договор по обслуживанию вступает в силу с момента поступления титульных знаков в полном размере, указанном в заявке, от Заказчика на реквизиты Исполнителя. Операции с титульными знаками учитываются согласно правилам, регламенту и формату электронных систем по расчетам. Договор действителен в течение срока , который устанавливается с момента подачи заявки до расторжения по инициативе одной из сторон.\r\n\r\n<strong>4. Предмет соглашения.</strong>\r\n\r\nПутем использования технических методов Исполнитель обязуется выполнять обмен титульных знаков за комиссионное вознаграждение от Заказчика, после подачи данным лицом заявки и совершает это путем продажи титульных знаков лицам, желающим их приобрести по сумме, указанной не ниже, чем в заявке поданной Заказчиком. Денежные средства Исполнитель обязуется переводить на указанные Заказчиком реквизиты. В случае возникновения во время обмена прибыли, она остается на счету Исполнителя, как дополнительная выгода и премия за комиссионные услуги.\r\n\r\n<strong>5. В дополнение.</strong>\r\n\r\n5.1. Если на счет Исполнителя поступает сумма, отличающаяся от указанной в заявке, Исполнитель делает перерасчет, который соответствует фактическому поступлению титульных знаков. Если данная сумма превышает указанную в заявке более чем на 10%, Исполнитель расторгает договор в одностороннем порядке и все средства возвращаются на реквизиты Заказчика, с учетом вычтенной суммы на комиссионные расходы во время перевода.\r\n5.2. В случае, когда титульные знаки не отправляются Исполнителем на указанные реквизиты Заказчика в течение 24 часов, Заказчик имеет полное право потребовать расторжение соглашения и аннулировать свою заявку, тем самым совершая возврат титульных знаков на свой счет в полном объеме. Заявка на расторжение соглашения и возврата титульных знаков выполняется Исполнителем в том случае, если денежные средства еще не были переведены на указанные реквизиты Заказчика. В случае аннулирования договора, возврат электронной валюты производится в течение 24 часов с момента получения требовании о расторжении договора. Если задержки при возврате возникли не по вине Исполнителя, он не несет за них ответственности.\r\n5.3. Если титульные знаки не поступаеют от Заказчика на счет Исполнителя в течение указанного срока, с момента подачи заявки Заказчиком, соглашение между сторонами расторгается Исполнителем с одной стороны, так как договор не вступает в действие. Заказчик может об этом не уведомляться. Если титульные знаки поступает на реквизиты Исполнителя после указанного срока, то такие средства переводятся обратно на счет Заказчика, причем все комиссионные расходы, связанные с переводом, вычитаются из данных средств.\r\n5.4. Если происходит задержка перевода средств на реквизиты, указанные Заказчиком, по вине расчетной системы, Исполнитель не несет ответственности за ущерб, возникающий в результате долгого поступления денежных средств. В этом случае Заказчик должен согласиться с тем, что все претензии будут предъявляться к расчетной системе, а Исполнитель оказывает свою помощь по мере своих возможностей в рамках закона.\r\n5.5. В случае обнаружения подделки коммуникационных потоков или оказания воздействия, с целью ухудшить работу Исполнителя, а именно его программного кода, заявка приостанавливается, а переведенные средства подвергаются перерасчету в соответствии с действующим соглашением. Если Заказчик не согласен с перерасчетом, он имеет полное право расторгнуть договор и титульные знаки отправятся на реквизиты указанные Заказчиком.\r\n5.6. В случае пользования услугами Исполнителя, Заказчик полностью соглашается с тем, что Исполнитель несет ограниченную ответственность соответствующую рамкам настоящих правил полученных титульных знаков и не дает дополнительных гарантий Заказчику, а также не несет перед ним дополнительной ответственности. Соответственно Заказчик  не несет дополнительной ответственности перед Исполнителем.\r\n5.7. Заказчик обязуется выполнять нормы соответствующие законодательству, а также не подделывать коммуникационные потоки и не создавать препятствий для нормальной работы программного кода Исполнителя.\r\n5.8.Исполнитель не несет ответственности за ущерб и последствия при ошибочном переводе электронной валюты в том случае, если Заказчик указал при подаче заявки неверные реквизиты.\r\n\r\n<strong>6. Гарантийный срок</strong>\r\n\r\nВ течение 24 часов с момента исполнения обмена титульных знаков Исполнитель дает гарантию на оказываемые услуги при условии, если не оговорены иные сроки.\r\n\r\n<strong>7. Непредвиденные обстоятельства.</strong>\r\n\r\nВ случае, когда в процессе обработки заявки Заказчика возникают непредвиденные обстоятельства, способствующие невыполнению Исполнителем условий договора, сроки выполнения заявки переносятся на соответствующий срок длительности форс-мажора. За просроченные обязательства Исполнитель ответственности не несет.\r\n\r\n<strong>8. Форма соглашения.</strong>\r\n\r\nДанное соглашение обе стороны, в лице Исполнителя и Заказчика, принимают как равноценный по юридической силе договор, обозначенный в письменной форме.\r\n\r\n<strong>9. Работа с картами Англии, Германии и США.</strong>\r\n\r\nДля владельцев карт стран Англии, Германии и США условия перевода титульных знаков продляются на неопределенный срок, соответствующий полной проверке данных владельца карты. Денежные средства в течение всего срока не подвергаются никаким операциям и в полном размере находятся на счете Исполнителя.\r\n\r\n<strong>10 Претензии и споры.</strong>\r\n\r\nПретензии по настоящему соглашению принимаются Исполнителем в форме электронного письма, в котором Заказчик указывает суть претензии. Данное письмо отправляется на указанные на сайте реквизиты Исполнителя.\r\n\r\n<strong>11. Проведение обменных операций.</strong>\r\n\r\n11.1.Категорически запрещается пользоваться услугами Исполнителя для проведения незаконных переводов и мошеннических действий. При заключении настоящего договора, Заказчик обязуется выполнять эти требования и в случае мошенничества нести уголовную ответственность, установленную законодательством на данный момент.\r\n11.2. В случае невозможности выполнения заявки автоматически, по не зависящим от Исполнителя обстоятельствам, таким как отсутствие связи, нехватка средств, или же ошибочные данные Заказчика, средства поступают на счет в течение последующих 24 часов или же возвращается на реквизиты Заказчика за вычетом комиссионных расходов.\r\n11.3.По первому требованию Исполнитель вправе передавать информацию о переводе электронной валюты правоохранительным органам, администрации расчетных систем, а также жертвам неправомерных действий, пострадавшим в результате доказанного судебными органами мошенничества.\r\n11.4. Заказчик обязуется представить все документы, удостоверяющие его личность, в случае подозрения о мошенничестве и отмывании денег.\r\n11.5. Заказчик обязуется не вмешиваться в работу Исполнителя и не наносить урон его программной и аппаратной части, а также Заказчик обязуется передавать точные сведения для обеспечения выполнения Исполнителем всех условий договора.\r\n\r\n<strong>12.Отказ от обязательств.</strong>\r\n\r\nИсполнитель имеет право отказа на заключение договора и выполнение заявки, причем без объяснения причин. Данный пункт применяется по отношению к любому клиенту.[:ru_RU][en_US:]<strong>1. Parties to the agreement.</strong>\r\n\r\nThe Agreement is concluded between the online service for the exchange of digital currency, hereinafter referred to as the Contractor - for one part, and the Customer, represented by a person who used the services of the Contractor, - for the other part.\r\n\r\n<strong>2. List of terms.</strong>\r\n\r\n2.1. Digital Currency Exchange - automated product of the online service, which is provided by the Contractor under these rules.\r\n\r\n2.2. Customer - a natural person, agreeing to the terms of the Contractor and this agreement that it enters into.\r\n\r\n2.3. Digital currency - a standard unit of a particular payment system, which corresponds to the calculations of electronic systems and indicates the scope of rights corresponding to a specific agreement on electronic payment system and its Customer.\r\n\r\n2.4. Application - information transmitted by the Customer for use of the Contractor''s funds in electronic form and indicating that he accepts the terms of use of the service offered by the Contractor herein.\r\n\r\n<strong>3. Terms and conditions of the agreement.</strong>\r\n\r\nThese rules are considered to be subject to the conditions of the public offer, which enters into force at the time of submission of an application by the Customer and is one of the main components of this agreement. The information about the conditions of application submission specified by the Contractor, is a Public offer. The main part of a public offer are actions made in the completion of the application submission by the Customer showing his exact intentions to make a transaction on the terms proposed by the Contractor before the end of this application. Time, date, and parameters of the application are created automatically by the Contractor by the end of application submission. The proposal should be accepted by the Customer within 24 hours before the end of formation of the application. Service agreement comes into force from the moment of receipt of digital currency in the full amount specified in the application, from the Customer according to the details set forth by the Contractor. Transactions with digital currency are accounted according to the rules, regulations and format of electronic payment systems/ The agreement is valid for a period which is set from the date of submitting the application and continued until terminated by either party.\r\n\r\n<strong>4. Matter of the agreement.</strong>\r\n\r\nUsing technical methods, the Contractor undertakes to perform digital currency exchange for a commission from the Customer, after the submitting the application by this person, and makes it through the sale of digital currency to persons wishing to purchase it for the money amount which is not lower than that in the application submitted by the Customer. The Contractor undertakes to transfer money according to the details specified by the Customer. In case when a profit occurs at the time of exchange, it remains on the account of the Contractor, as an additional benefit and a premium for commission services.\r\n\r\n<strong>5. Additional provisions.</strong>\r\n\r\n5.1. If the Contractor receives an amount on its account that differs from that indicated in the application, the Contractor makes a resettlement, which corresponds to the actual receipt of digital currency. Should this amount exceed the amount specified in the application for more than 10%, the Contractor terminates the contract unilaterally and all funds are returned to the Customer''s details, taking into account the amount deducted for commission expenses during the transfer.\r\n\r\n5.2. Should the digital currency not be sent by the Contractor to the specified details of the Customer within 24 hours, the Customer has the full right to demand the termination of the agreement and cancel the application, thereby making the return of digital currency on its account in full. Application for termination of the agreement and return of digital currency is performed by the Contractor in the event that the money has not yet been transferred according to the details of the Customer. In case of terminating the agreement, the return of e-currencies is made within 24 hours of receipt of the application for termination of the agreement. If a delay in the return occurred through no fault of the Contractor, it will not take responsibility for it.\r\n\r\n5.3. If no digital currency arrives from the Customer to the Contractor within the specified period from the date of submitting the application by the Customer, the agreement between the parties shall be terminated by the Contractor unilaterally, since the agreement does not enter into force. There may be no notice about it sent to the Customer. Shall no digital currency arrive to the details of the Contractor after the deadline, then such funds are transferred back to the account of the Customer, and all commission expenses associated with the transfer are deducted from that amount.\r\n\r\n5.4. If there is a delay in the transfer of funds to the account details specified by the Customer, through a fault of a payment system, the Contractor shall not be liable for any damage caused as a result of a delayed transfer. In this case, the Customer shall agree that all claims would be referred to the payment system, and the Contractor shall provide assistance as far as possible under the law.\r\n\r\n5.5. In case of forgery of communication flows, or due to influence in order to degrade the performance of the Contractor, namely its software code, the application is suspended, and the money transferred are subject to resettlement in accordance with the agreement in effect. Shall the Customer not agree to the resettlement, he has every right to terminate the agreement and the digital currency shall be transferred to the account details specified by the Customer.\r\n\r\n5.6. In the case of using the services of the Contractor, the Customer fully agrees that the Contractor shall bear a limited liability corresponding to these rules for obtaining digital currency and give no additional guarantees to the Customer and shall have no additional liability before the Customer. Accordingly, the Customer shall not bear an additional liability to the Contractor.\r\n\r\n5.7. The Customer agrees to comply with applicable laws and not to tamper any communication flows as well as create any obstacles to the normal operation of the program code of the Contractor.\r\n\r\n5.8. The Contractor shall not be liable for any damage or consequences of an erroneous transfer of e-currency in the event that Customer have specified wrong details during application submission.\r\n\r\n<strong>6. Warranty period</strong>\r\n\r\nWithin 24 hours of the execution of the digital currency exchange, the Contractor warrants for services provided, unless otherwise noted.\r\n\r\n<strong>7. Contingencies.</strong>\r\n\r\nIn the case where unforeseen circumstances that contribute to non-compliance with terms of the agreement by the Contractor during the processing of the Customer''s application, the timing of application accomplishment are delayed for the corresponding period of the duration of the force majeure. The Contractor is not responsible for overdue obligations.\r\n\r\n<strong>8. Form of agreement.</strong>\r\n\r\nBoth parties, represented by the Contractor and the Customer, shall take this agreement as an agreement equivalent to the validity of the contract designated in writing.\r\n\r\n<strong>9. Usage of cards of England, Germany and the United States.</strong>\r\n\r\nFor cardholders from England, Germany and the United States, the arrangements for the transfer of digital currency are extended for an indefinite period, corresponding to the period required for full verification of cardholder data. For the whole period the money is not subject to any transactions and are retained in full in the account of the Contractor.\r\n\r\n<strong>10 Claims and disputes.</strong>\r\nClaims under this agreement are received by the Contractor in the form of e-mail where the Customer specifies the essence of the claim. This mail is sent to the details specified on site of the Contractor.\r\n\r\n<strong>11. Exchange transactions performance.</strong>\r\n\r\n11.1. It is expressly prohibited to use the services of the Contractor to carry out illegal transfers and fraud. At the conclusion of this agreement, the Customer agrees to comply with these requirements and to be criminally liable in the case of fraud under the laws in force.\r\n\r\n11.2. In case of inability to fulfill orders automatically, through no fault of the Contractor, such as lack of communication, lack of funds, or erroneous data of the Customer, the money is transferred to the account within the next 24 hours or returned to the account details of the Customer, net commission expense.\r\n\r\n11.3. On demand the Contractor is entitled to release information on the transfer of electronic currency to law enforcement bodies, administration of payment systems, as well as to victims of misconduct, victims of proven judicial fraud.\r\n\r\n11.4. The Customer agrees to submit all the documents proving his identity, in case of suspicion of fraud and money laundering.\r\n\r\n11.5. The Customer agrees not to interfere with the work of the Contractor and not to cause damage to its hardware and software, as well as the Customer undertakes to provide accurate information to ensure compliance with all terms of the agreement by the Contractor.\r\n\r\n<strong>12. Liability disclaimer.</strong>\r\n\r\nThe Contractor shall have the right to refuse to sign the agreement and accomplish the application without explanation. This paragraph shall apply with respect to any client.[:en_US]', '[ru_RU:]Правила сайта[:ru_RU][en_US:]Site rules[:en_US]', '', 'publish', 'closed', 'closed', '', 'tos', '', '', '2016-04-09 16:32:59', '2016-04-09 13:32:59', '', 0, 'http://premiumexchanger.ru/tos/', 0, 'page', '', 0),
(7, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:]Уважаемые клиенты! Безопасность проведения транзакций может быть поставлена под угрозу, в связи с независящими от нашего сервиса обстоятельствами. Чтобы этого не произошло, рекомендуем ознакомиться со следующими правилами конвертации электронной валюты:\r\n<ul>\r\n	<li> Всегда требуйте подтверждения личности лица, на реквизиты которого вы собираетесь выполнить перевод средств. Сделать это можно посредством личного звонка на skype, icq либо посредством запроса информации о статусе кошелька оппонента на сайте платежной системы;</li>\r\n	<li>Будьте предельно внимательны при заполнении поля «Номер счета» адресата. Допустив ошибку, вы отправляете собственные средства в неизвестном направлении без возможности их возврата;</li>\r\n	<li>Никогда не предоставляете займы, используя «безотзывные» электронные системы оплаты. В данном случае шанс столкнуться с фактом мошенничества чрезвычайно велик;</li>\r\n	<li>Если вам предлагается сделать оплату способом, отличным от указанного в инструкции к использованию нашего сервиса, откажитесь от выполнения платежа и сообщите о случившемся нашему специалисту. То же касается выплат по заявкам, созданным не лично вами;</li>\r\n	<li>Откажитесь от проведения средств, собственниками которых являются третьи лица, через собственные банковские счета. Известны случаи, когда проведение таких транзакций за вознаграждение, приводило к тому, что владелец счета становился соучастником финансового преступления, не подозревая о злом умысле со стороны мошенников;</li>\r\n	<li>Всегда уточняйте у сотрудника обменного пункта информацию, приходящую на вашу почту.</li>\r\n</ul>\r\nНаш и подобные сервисы не предоставляют займов, не берут средства у пользователей в долг или под проценты, не принимают пожертвований. При получении сообщений подозрительного характера от нашего имени с похожих на наши либо иных реквизитов, воздержитесь от выполнения указанных там требований и сообщите о произошедшем в нашу <a href="/feedback/">службы поддержки</a>.\r\n\r\nС заботой о вашем финансовом благополучии.[:ru_RU][en_US:]Dear customers! Security of transactions can be compromised due to circumstances independent from our service. To avoid this, we recommend that you read the following e-currency conversion rules:\r\n<ul>\r\n	<li>Always ask for confirmation of identity of the person on the details of which you are going to transfer the money. This can be done through personal call on skype, icq or by requesting information on the status of the digital wallet of the opponent on the site of payment system;</li>\r\n	<li>Be very careful when filling out the field "Account Number" of the offeree. If you have made a mistake, you are sending your own money in an unknown direction without the possibility of its return;</li>\r\n	<li>Never provide loans using "irrevocable" electronic payment systems. In this case, the risk to face the fact of fraud is extremely high;</li>\r\n	<li>If you are offered to make a payment in a manner different from that specified in the instructions for use of our service, you shall refuse to execute the payment and report the incident to our expert. The same applies to payments on applications that were not created by you;</li>\r\n	<li>Give up of transacting the funds, which are owned by third parties, through your own bank accounts. There are cases when carrying out such transaction for a fee led to the fact that the account holder became an accomplice of a financial crime, being unaware of malice on the part of scams;</li>\r\n	<li>Always verify information that comes to your mail with the exchange office employee.</li>\r\n</ul>\r\nOur services and similar services do not provide loans, do not take money from people under debt or interest, and do not accept donations. When receiving messages of suspicious nature on our behalf with details similar to our or other details, please refrain from the implementation of these requirements there and tell about what happened to our <a href="/feedback/">Support Service</a>.\r\n\r\nTaking care of your financial well-being.[:en_US]', '[ru_RU:]Предупреждение[:ru_RU][en_US:]Caution[:en_US]', '', 'publish', 'closed', 'closed', '', 'notice', '', '', '2016-04-09 16:32:53', '2016-04-09 13:32:53', '', 0, 'http://premiumexchanger.ru/notice/', 0, 'page', '', 0),
(8, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:]<strong>Вопрос: Как работает партнерская программа?</strong>\r\n\r\nОтвет: Зарегистрировавшись в нашей партнерской программе, Вы получаете уникальный партнерский идентификатор, который добавляется во все Ваши ссылки (?rid=777) и HTML-код. Вы можете размещать ссылки на любые страницы нашего сервиса  на своем сайте, блоге, страничке, в сообществах и социальных сетях.<strong>   </strong>\r\n\r\n<strong>Вопрос: Сколько я буду зарабатывать, участвуя в Вашей партнерской программе?</strong>\r\n\r\nОтвет: Это зависит от многих факторов, таких как:\r\n\r\n1. Посещаемость Вашего веб-сайта или сайтов, где Вы размещаете о нас информацию.\r\n\r\n2. Соответствие тематики сайта той целевой аудитории, которая может заинтересоваться услугами обмена валют. Проще говоря, не стоит рассчитывать на большое количество переходов по Вашей партнерской ссылке, размещенной на сайте, посвященном разведению попугаев.\r\n\r\n3. Правильная подача информации. Например, мало кого привлечет одна лишь ссылка "обмен валют" без всяких описаний где-нибудь в углу веб-страницы.\r\n\r\n<strong>Вопрос: Если я поставлю свою партнерскую ссылку в подпись на форуме, будут ли учитываться переходы и все остальные условия ПП?</strong>\r\n\r\nОтвет: Да, конечно будут.\r\n\r\n<strong>Вопрос: На моем сайте уже установлены другие партнерские программы. Могу ли я быть Вашим партнером?</strong>\r\n\r\nОтвет: Да, можете. У нас нет ограничений на работу с другими партнерскими программами.\r\n\r\n<strong>Вопрос: Подходит ли мой сайт для участия в партнерской программе?</strong>\r\n\r\nОтвет: Мы приветствуем любые сайты, которые не противоречат условиям нашей партнерской программы. Посмотреть список условий можно <a href="/register/">здесь</a> (пункт 6).\r\n\r\n<strong>Вопрос: Сколько уровней в Вашей партнерской программе? Оплачивается ли привлечение новых партнеров?</strong>\r\n\r\nОтвет: В нашей партнерской программе 6-ть уровней. Привлечение новых партнеров не оплачивается.\r\n\r\n<strong>Вопрос: Не могу войти в свой аккаунт партнера. Пишет "Неверное сочетание логина и пароля". При этом я уверен, что ввожу пароль правильно.</strong>\r\n\r\nОтвет: Убедитесь, что при вводе пароля у Вас не включена русская раскладка клавиатуры или Caps Lock. Если Вы точно помните только логин – воспользуйтесь функцией <a href="/lostpass/">Напоминания пароля</a>. Пароль будет выслан на Ваш e-mail, указанный при регистрации.\r\n\r\n<strong>Вопрос: Как выплачиваются заработанные деньги?</strong>\r\n\r\nОтвет: Партнерские выплаты производятся через систему WebMoney в валюте WMZ на кошелек, указанный партнером при регистрации в партнерской программе. Как правило, на это уходит не более 2-3 часов. Не спешите отправлять нам сообщения, если с момента подачи заявки не прошло 48 часов – администратор видит все заявки и обработает Вашу в любом случае.[:ru_RU][en_US:]<b>Question: How does the affiliate program work?</b>\r\n\r\nAnswer: By registering in our affiliate program, you will get a unique affiliate ID, which is added to all your links (?rid=777) and the HTML-code. You can post links on any pages of our service on your website, blog, page, in communities and social networks.\r\n\r\n<b>Question: How much will I earn by participating in your affiliate program?</b>\r\n\r\nAnswer: It depends on many factors such as:\r\n<ol>\r\n	<li>1. Traffic to your web site or sites where you post information about us.</li>\r\n	<li>Compliance of the subject of the site with the target audience, which may be interested in the services of currency exchange. Simply put, do not rely on a large number of clicks on your affiliate link posted on the website dedicated to parrot breeding.</li>\r\n	<li>Proper presentation of information. For example, very few people will like only one reference to "foreign exchange" without any description somewhere in the corner of the web page.</li>\r\n</ol>\r\n<b>Question: If I put my affiliate link and a signature, will the transitions and all other conditions of the AP also be considered?</b>\r\n\r\nAnswer: Yes, they certainly will.\r\n\r\n<b>Question: There are other affiliate programs installed on my site. May I be your affiliate?</b>\r\n\r\nAnswer: Yes, you may. We impose no restrictions on working with other affiliate programs.\r\n\r\n<b>Question: Is the qualify of my site sufficient to participate in an affiliate program?</b>\r\n\r\nAnswer: We welcome any sites that do not contradict the conditions of our affiliate program. You can see the list of conditions <a href="/register/"><span style="color: #1155cc;"><span style="text-decoration: underline;">here</span></span></a> (paragraph 6).\r\n\r\n<b>Question: How many levels are there in your affiliate program? Is there any reward for involving new affiliates?</b>\r\n\r\nAnswer: Our affiliate program has 6 levels. There is no reward for involving any further affiliates.\r\n\r\n<b>Question: I can not log in to my affiliate account. It shows "Invalid combination of username and password". But, in this case, I''m sure I enter the password correctly.</b>\r\n\r\nAnswer: Make sure that when you enter a password you Russian keyboard layout or Caps Lock are not turned on. If you just remember only login - use the <a href="/lostpass/"><span style="color: #1155cc;"><span style="text-decoration: underline;">password reminder</span></span></a>. The password will be sent to your e-mail, specified during registration.\r\n\r\n<b>Question: How are the earned money paid off?</b>\r\n\r\nAnswer: Affiliate payments are made via WebMoney in the currency WMZ onto a wallet, said by the affiliate during a registration in affiliate program. As a rule, it takes no more than 2-3 hours. Do not rush to send us a message if it has not passed 48 hours from the filing date yet - the administrator sees all application and will process yours anyway.[:en_US]', '[ru_RU:]Партнёрский FAQ[:ru_RU][en_US:]Affiliate FAQ[:en_US]', '', 'publish', 'closed', 'closed', '', 'partnersfaq', '', '', '2015-10-23 11:30:08', '2015-10-23 08:30:08', '', 0, 'http://premiumexchanger.ru/partnersfaq/', 0, 'page', '', 0),
(10, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:][contact_form][:ru_RU]', '[ru_RU:]Контакты[:ru_RU][en_US:]Feedback[:en_US]', '', 'publish', 'closed', 'closed', '', 'feedback', '', '', '2015-10-23 11:30:26', '2015-10-23 08:30:26', '', 0, 'http://premiumexchanger.ru/feedback/', 0, 'page', '', 0),
(11, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:][login_page][:ru_RU]', '[ru_RU:]Авторизация[:ru_RU][en_US:]Authorization[:en_US]', '', 'publish', 'closed', 'closed', '', 'login', '', '', '2015-10-23 11:26:57', '2015-10-23 08:26:57', '', 0, 'http://premiumexchanger.ru/login/', 0, 'page', '', 0),
(12, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:]1. Зарегистрированные пользователи получают право использовать накопительную систему скидок при совершение обмене:\r\n<ul>\r\n	<li>0-99 USD - 1%</li>\r\n	<li>100-999 USD - 2%</li>\r\n	<li>1000-4999 USD - 3%</li>\r\n	<li>5000- 9999 USD - 4%</li>\r\n	<li>10000-19999 USD - 5%</li>\r\n	<li>свыше 20000 USD - 6%</li>\r\n</ul>\r\n2. Начисления и выплаты по партнерской программе ведутся в долларах (WebMoney WMZ).\r\n\r\n3. Минимальная сумма для снятия заработанных денег с партнерского счета составляет 5 USD.\r\n\r\n4. За каждый совершенный обмен по вашей партнерской ссылке вы получает вознаграждение в размере от 1% до 6% от суммы обмена. Процент отчислений зависит от суммы совершенных обменов по вашей партнерской ссылке:\r\n<ul>\r\n	<li>0-99 USD - 1%</li>\r\n	<li>100-999 USD - 2%</li>\r\n	<li>1000-4999 USD - 3%</li>\r\n	<li>5000- 9999 USD - 4%</li>\r\n	<li>10000-19999 USD - 5%</li>\r\n	<li>свыше 20000 USD - 6%</li>\r\n</ul>\r\n4.1. Указанные значения партнерских вознаграждений быть со временем изменены. При этом все заработанные средства сохраняются на счете с учетом действовавших ранее ставок.\r\n\r\n5. На странице, где вы публикуете о нас информацию должно быть четко указано об услугах, предоставляемых нашим сайтом. В рекламных текстах запрещаются любые упоминания о наличии «бесплатных бонусов» на нашем сайте.\r\n\r\n6. Запрещается размещать партнерскую ссылку:\r\n<ul>\r\n	<li>в массовых рассылках писем (СПАМ);</li>\r\n	<li>на сайтах, принудительно открывающих окна браузера, либо открывающих сайты в скрытых фреймах;</li>\r\n	<li>на сайтах, распространяющих любые материалы, прямо или косвенно нарушающие законодательство РФ;</li>\r\n	<li>на сайтах, публикующих списки сайтов с «бесплатными бонусами»;</li>\r\n	<li>на веб-страницах, закрытых от публичного просмотра с помощью авторизации (различные социальные сети, закрытые разделы форумов и т.п.).</li>\r\n</ul>\r\nСайты, нарушающие одно или несколько вышеперечисленных правил, будут занесены в черный список нашей партнерской программы. Оплата за посетителей, пришедших с подобных сайтов производиться не будет.\r\n\r\n7 . При несоблюдении данных условий аккаунт нарушителя будет заблокирован без выплат и объяснения причин.\r\n\r\n8. Партнер несет полную ответственность за сохранность своих аутентификационных данных (логина и пароля) для доступа к аккаунту.\r\n\r\n9. Данные условия могут изменяться в одностороннем порядке без оповещения участников программы, но с публикацией на этой странице.\r\n<h1>Регистрация</h1>\r\nПожалуйста, внимательно и аккуратно заполните все поля регистрационной формы. На указанный вами e-mail будет выслано уведомление о регистрации.\r\n\r\n[register_page][:ru_RU][en_US:]1. Registered users have the right to use a progressive discount system when committing the exchange:\r\n<ul>\r\n	<li>0-99 USD — 1%</li>\r\n	<li>100-999 USD — 2%</li>\r\n	<li>1000-4999 USD — 3%</li>\r\n	<li>5000- 9999 USD — 4%</li>\r\n	<li>10000-19999 USD — 5%</li>\r\n	<li>over 20000 USD — 6%</li>\r\n</ul>\r\n2. Calculation and payoff within an affiliate program are maintained in United States dollars (USD).\r\n\r\n3. The minimum amount for the withdrawal of money earned from an affiliate account is 5 USD.\r\n\r\n4. For every exchange made using your affiliate link, you receive a reward at a rate of 1% to 6% of the amount exchanged. The percentage depends on the amount of exchanges made using your affiliate link:\r\n<ul>\r\n	<li>0-99 USD — 1%</li>\r\n	<li>100-999 USD — 2%</li>\r\n	<li>1000-4999 USD — 3%</li>\r\n	<li>5000- 9999 USD — 4%</li>\r\n	<li>10000-19999 USD — 5%</li>\r\n	<li>over 20000 USD — 6%</li>\r\n</ul>\r\n4.1. These values ​​of affiliate rewards may be changed from time to time. In this case, all earnings are retained in the account taking into account the previous rates.\r\n\r\n5. It should be clearly stated on the services provided by our site on the page where you post information about us. In advertising texts, it is prohibited to mention of a "free bonus" being available on our site.\r\n\r\n6. It is prohibited to place an affiliate link:\r\n<ul>\r\n	<li>in mass mail (spam);</li>\r\n	<li>on sites, forcing to open a browser window, or opening sites in a hidden frame;</li>\r\n	<li>on sites distributing any materials that, directly or indirectly, violate the laws of the Russian Federation;</li>\r\n	<li>on sites publishing lists of sites with "free bonuses";</li>\r\n	<li>on web pages, enclosed from public view by means of authorization (various social networks, closed sections of forums, etc.).</li>\r\n	<li>Sites that violate one or more of the above rules will be blacklisted for our affiliate program. There will be no payoffs for visitors linked from these sites.</li>\r\n</ul>\r\n7 . When failing to comply with these terms and conditions, your account will be blocked without making payoffs and explanation.\r\n\r\n8. An affiliate is solely responsible for the safety of its credentials (username and password) to access the account.\r\n\r\n9. These terms and conditions may be changed unilaterally without notifying participants of the program, but with the publication on this page.\r\n\r\n[register_page][:en_US]', '[ru_RU:]Регистрация[:ru_RU][en_US:]Registration[:en_US]', '', 'publish', 'closed', 'closed', '', 'register', '', '', '2016-04-09 16:31:54', '2016-04-09 13:31:54', '', 0, 'http://premiumexchanger.ru/register/', 0, 'page', '', 0),
(13, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:][lostpass_page][:ru_RU]', '[ru_RU:]Восстановление пароля[:ru_RU][en_US:]Password recovery[:en_US]', '', 'publish', 'closed', 'closed', '', 'lostpass', '', '', '2015-10-23 11:31:52', '2015-10-23 08:31:52', '', 0, 'http://premiumexchanger.ru/lostpass/', 0, 'page', '', 0);
INSERT INTO `pr_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES
(14, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:][account_page][:ru_RU]', '[ru_RU:]Личный кабинет[:ru_RU][en_US:]Personal account[:en_US]', '', 'publish', 'closed', 'closed', '', 'account', '', '', '2015-10-23 11:45:11', '2015-10-23 08:45:11', '', 0, 'http://premiumexchanger.ru/account/', 0, 'page', '', 0),
(15, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:][security_page][:ru_RU]', '[ru_RU:]Настройки безопасности[:ru_RU][en_US:]Security settings[:en_US]', '', 'publish', 'closed', 'closed', '', 'security', '', '', '2015-10-23 11:45:30', '2015-10-23 08:45:30', '', 0, 'http://premiumexchanger.ru/security/', 0, 'page', '', 0),
(16, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:][sitemap][:ru_RU]', '[ru_RU:]Карта сайта[:ru_RU][en_US:]Sitemap[:en_US]', '', 'publish', 'closed', 'closed', '', 'sitemap', '', '', '2015-10-23 11:30:43', '2015-10-23 08:30:43', '', 0, 'http://premiumexchanger.ru/sitemap/', 0, 'page', '', 0),
(17, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:][tarifs][:ru_RU]', '[ru_RU:]Тарифы[:ru_RU][en_US:]Tariffs[:en_US]', '', 'publish', 'closed', 'closed', '', 'tarifs', '', '', '2015-10-23 11:48:15', '2015-10-23 08:48:15', '', 0, 'http://premiumexchanger.ru/tarifs/', 0, 'page', '', 0),
(18, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:][reviews_page][:ru_RU]', '[ru_RU:]Отзывы[:ru_RU][en_US:]Reviews[:en_US]', '', 'publish', 'closed', 'closed', '', 'reviews', '', '', '2015-10-23 11:46:11', '2015-10-23 08:46:11', '', 0, 'http://premiumexchanger.ru/reviews/', 0, 'page', '', 0),
(19, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:][userwallets][:ru_RU]', '[ru_RU:]Ваши счета[:ru_RU][en_US:]Your payment details[:en_US]', '', 'publish', 'closed', 'closed', '', 'userwallets', '', '', '2015-10-23 11:33:39', '2015-10-23 08:33:39', '', 0, 'http://premiumexchanger.ru/userwallets/', 0, 'page', '', 0),
(20, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:][userverify][:ru_RU]', '[ru_RU:]Верификация аккаунта[:ru_RU][en_US:]Account verification[:en_US]', '', 'publish', 'closed', 'closed', '', 'userverify', '', '', '2015-10-23 11:32:22', '2015-10-23 08:32:22', '', 0, 'http://premiumexchanger.ru/userverify/', 0, 'page', '', 0),
(21, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:][userxch][:ru_RU]', '[ru_RU:]Ваши операции[:ru_RU][en_US:]Your operations[:en_US]', '', 'publish', 'closed', 'closed', '', 'userxch', '', '', '2015-10-23 11:33:56', '2015-10-23 08:33:56', '', 0, 'http://premiumexchanger.ru/userxch/', 0, 'page', '', 0),
(24, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:][paccount_page][:ru_RU]', '[ru_RU:]Партнёрский аккаунт[:ru_RU][en_US:]Affiliate account[:en_US]', '', 'publish', 'closed', 'closed', '', 'paccount', '', '', '2015-10-23 11:47:18', '2015-10-23 08:47:18', '', 0, 'http://premiumexchanger.ru/paccount/', 0, 'page', '', 0),
(25, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:][promotional_page][:ru_RU]', '[ru_RU:]Рекламные материалы[:ru_RU][en_US:]Promotional materials[:en_US]', '', 'publish', 'closed', 'closed', '', 'promotional', '', '', '2015-10-23 11:47:45', '2015-10-23 08:47:45', '', 0, 'http://premiumexchanger.ru/promotional/', 0, 'page', '', 0),
(26, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:][pexch_page][:ru_RU]', '[ru_RU:]Партнёрские обмены[:ru_RU][en_US:]Affiliate exchanges[:en_US]', '', 'publish', 'closed', 'closed', '', 'pexch', '', '', '2015-10-23 11:46:43', '2015-10-23 08:46:43', '', 0, 'http://premiumexchanger.ru/pexch/', 0, 'page', '', 0),
(27, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:][plinks_page][:ru_RU]', '[ru_RU:]Партнёрские переходы[:ru_RU][en_US:]Affiliate transitions[:en_US]', '', 'publish', 'closed', 'closed', '', 'plinks', '', '', '2015-10-23 11:46:58', '2015-10-23 08:46:58', '', 0, 'http://premiumexchanger.ru/plinks/', 0, 'page', '', 0),
(28, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:][preferals_page][:ru_RU]', '[ru_RU:]Рефералы[:ru_RU][en_US:]Referrals[:en_US]', '', 'publish', 'closed', 'closed', '', 'preferals', '', '', '2015-10-23 11:47:56', '2015-10-23 08:47:56', '', 0, 'http://premiumexchanger.ru/preferals/', 0, 'page', '', 0),
(29, 1, '2015-10-22 17:31:50', '2015-10-22 14:31:50', '[ru_RU:][payouts_page][:ru_RU]', '[ru_RU:]Вывод партнёрских средств[:ru_RU][en_US:]Affiliate money withdrawal[:en_US]', '', 'publish', 'closed', 'closed', '', 'payouts', '', '', '2015-10-23 11:31:27', '2015-10-23 08:31:27', '', 0, 'http://premiumexchanger.ru/payouts/', 0, 'page', '', 0),
(30, 1, '2015-10-22 17:36:04', '2015-10-22 14:36:04', '', '[ru_RU:]Главная[:ru_RU][en_US:]Homepage[:en_US]', '', 'publish', 'closed', 'closed', '', '30', '', '', '2017-08-15 20:01:12', '2017-08-15 17:01:12', '', 0, 'http://premiumexchanger.ru/?p=30', 1, 'nav_menu_item', '', 0),
(31, 1, '2015-10-22 17:36:04', '2015-10-22 14:36:04', ' ', '', '', 'publish', 'closed', 'closed', '', '31', '', '', '2017-08-15 20:01:13', '2017-08-15 17:01:13', '', 0, 'http://premiumexchanger.ru/?p=31', 5, 'nav_menu_item', '', 0),
(32, 1, '2015-10-22 17:36:04', '2015-10-22 14:36:04', ' ', '', '', 'publish', 'closed', 'closed', '', '32', '', '', '2017-08-15 20:01:12', '2017-08-15 17:01:12', '', 0, 'http://premiumexchanger.ru/?p=32', 2, 'nav_menu_item', '', 0),
(33, 1, '2015-10-22 17:36:04', '2015-10-22 14:36:04', ' ', '', '', 'publish', 'closed', 'closed', '', '33', '', '', '2017-08-15 20:01:12', '2017-08-15 17:01:12', '', 0, 'http://premiumexchanger.ru/?p=33', 3, 'nav_menu_item', '', 0),
(34, 1, '2015-10-22 17:36:04', '2015-10-22 14:36:04', ' ', '', '', 'publish', 'closed', 'closed', '', '34', '', '', '2017-08-15 20:01:12', '2017-08-15 17:01:12', '', 0, 'http://premiumexchanger.ru/?p=34', 4, 'nav_menu_item', '', 0),
(35, 1, '2015-10-22 17:36:51', '2015-10-22 14:36:51', ' ', '', '', 'publish', 'closed', 'closed', '', '35', '', '', '2017-08-15 20:00:52', '2017-08-15 17:00:52', '', 0, 'http://premiumexchanger.ru/?p=35', 1, 'nav_menu_item', '', 0),
(36, 1, '2015-10-22 17:36:51', '2015-10-22 14:36:51', ' ', '', '', 'publish', 'closed', 'closed', '', '36', '', '', '2017-08-15 20:00:52', '2017-08-15 17:00:52', '', 0, 'http://premiumexchanger.ru/?p=36', 2, 'nav_menu_item', '', 0),
(38, 1, '2015-10-22 22:33:48', '2015-10-22 19:33:48', '[ru_RU:]Добро пожаловать на сайт обменного пункта![:ru_RU][en_US:]Welcome to the website of the exchange office![:en_US]', '[ru_RU:]Добро пожаловать![:ru_RU][en_US:]Welcome![:en_US]', '', 'publish', 'open', 'closed', '', 'dobro-pozhalovat', '', '', '2016-12-11 11:03:33', '2016-12-11 08:03:33', '', 0, 'http://premiumexchanger.ru/?p=38', 0, 'post', '', 0),
(77, 1, '2015-10-23 11:49:02', '2015-10-23 08:49:02', ' ', '', '', 'publish', 'closed', 'closed', '', '77', '', '', '2017-08-15 20:00:52', '2017-08-15 17:00:52', '', 0, 'http://premiumexchanger.ru/?p=77', 3, 'nav_menu_item', '', 0),
(85, 1, '2015-12-28 15:51:34', '2015-12-28 12:51:34', '[en_US:][indeposit][:en_US]', '[en_US:]Pay deposit[:en_US][ru_RU:]Оплатить депозит[:ru_RU]', '', 'publish', 'closed', 'closed', '', 'indeposit', '', '', '2016-02-23 17:49:57', '2016-02-23 14:49:57', '', 0, 'http://premiumexchanger.ru/indeposit/', 0, 'page', '', 0),
(90, 1, '2016-02-23 17:04:52', '2016-02-23 14:04:52', '[en_US:][domacc_page][:en_US][ru_RU:][domacc_page][:ru_RU]', '[en_US:]Internal account[:en_US][ru_RU:]Внутренний счет[:ru_RU]', '', 'publish', 'closed', 'closed', '', 'domacc', '', '', '2016-11-27 12:07:18', '2016-11-27 09:07:18', '', 0, 'http://premiumexchanger.ru/domacc/', 0, 'page', '', 0),
(96, 1, '2016-02-23 17:18:23', '2016-02-23 14:18:23', '', 'Advcash', '', 'inherit', 'open', 'closed', '', 'advcash', '', '', '2016-02-23 17:18:23', '2016-02-23 14:18:23', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Advcash.png', 0, 'attachment', 'image/png', 0),
(97, 1, '2016-02-23 17:18:24', '2016-02-23 14:18:24', '', 'Alfabank', '', 'inherit', 'open', 'closed', '', 'alfabank', '', '', '2016-02-23 17:18:24', '2016-02-23 14:18:24', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Alfabank.png', 0, 'attachment', 'image/png', 0),
(98, 1, '2016-02-23 17:18:24', '2016-02-23 14:18:24', '', 'Bitcoin', '', 'inherit', 'open', 'closed', '', 'bitcoin', '', '', '2016-02-23 17:18:24', '2016-02-23 14:18:24', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Bitcoin.png', 0, 'attachment', 'image/png', 0),
(99, 1, '2016-02-23 17:18:25', '2016-02-23 14:18:25', '', 'BTC-e', '', 'inherit', 'open', 'closed', '', 'btc-e', '', '', '2016-02-23 17:18:25', '2016-02-23 14:18:25', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/BTC-e.png', 0, 'attachment', 'image/png', 0),
(100, 1, '2016-02-23 17:18:26', '2016-02-23 14:18:26', '', 'Helixmoney', '', 'inherit', 'open', 'closed', '', 'helixmoney', '', '', '2016-02-23 17:18:26', '2016-02-23 14:18:26', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Helixmoney.png', 0, 'attachment', 'image/png', 0),
(101, 1, '2016-02-23 17:18:27', '2016-02-23 14:18:27', '', 'Liqpay', '', 'inherit', 'open', 'closed', '', 'liqpay', '', '', '2016-02-23 17:18:27', '2016-02-23 14:18:27', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Liqpay.png', 0, 'attachment', 'image/png', 0),
(102, 1, '2016-02-23 17:18:27', '2016-02-23 14:18:27', '', 'Litecoin', '', 'inherit', 'open', 'closed', '', 'litecoin', '', '', '2016-02-23 17:18:27', '2016-02-23 14:18:27', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Litecoin.png', 0, 'attachment', 'image/png', 0),
(103, 1, '2016-02-23 17:18:28', '2016-02-23 14:18:28', '', 'Livecoin', '', 'inherit', 'open', 'closed', '', 'livecoin', '', '', '2016-02-23 17:18:28', '2016-02-23 14:18:28', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Livecoin.png', 0, 'attachment', 'image/png', 0),
(104, 1, '2016-02-23 17:18:29', '2016-02-23 14:18:29', '', 'NixMoney', '', 'inherit', 'open', 'closed', '', 'nixmoney', '', '', '2016-02-23 17:18:29', '2016-02-23 14:18:29', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/NixMoney.png', 0, 'attachment', 'image/png', 0),
(105, 1, '2016-02-23 17:18:29', '2016-02-23 14:18:29', '', 'Okpay', '', 'inherit', 'open', 'closed', '', 'okpay', '', '', '2016-02-23 17:18:29', '2016-02-23 14:18:29', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Okpay.png', 0, 'attachment', 'image/png', 0),
(106, 1, '2016-02-23 17:18:30', '2016-02-23 14:18:30', '', 'Ooopay', '', 'inherit', 'open', 'closed', '', 'ooopay', '', '', '2016-02-23 17:18:30', '2016-02-23 14:18:30', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Ooopay.png', 0, 'attachment', 'image/png', 0),
(107, 1, '2016-02-23 17:18:31', '2016-02-23 14:18:31', '', 'Paxum', '', 'inherit', 'open', 'closed', '', 'paxum', '', '', '2016-02-23 17:18:31', '2016-02-23 14:18:31', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Paxum.png', 0, 'attachment', 'image/png', 0),
(108, 1, '2016-02-23 17:18:32', '2016-02-23 14:18:32', '', 'Payeer', '', 'inherit', 'open', 'closed', '', 'payeer', '', '', '2016-02-23 17:18:32', '2016-02-23 14:18:32', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Payeer.png', 0, 'attachment', 'image/png', 0),
(109, 1, '2016-02-23 17:18:32', '2016-02-23 14:18:32', '', 'Paymer', '', 'inherit', 'open', 'closed', '', 'paymer', '', '', '2016-02-23 17:18:32', '2016-02-23 14:18:32', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Paymer.png', 0, 'attachment', 'image/png', 0),
(110, 1, '2016-02-23 17:18:34', '2016-02-23 14:18:34', '', 'Paypal', '', 'inherit', 'open', 'closed', '', 'paypal', '', '', '2016-02-23 17:18:34', '2016-02-23 14:18:34', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Paypal.png', 0, 'attachment', 'image/png', 0),
(111, 1, '2016-02-23 17:18:35', '2016-02-23 14:18:35', '', 'Payza', '', 'inherit', 'open', 'closed', '', 'payza', '', '', '2016-02-23 17:18:35', '2016-02-23 14:18:35', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Payza.png', 0, 'attachment', 'image/png', 0),
(112, 1, '2016-02-23 17:18:36', '2016-02-23 14:18:36', '', 'Perfect-Money', '', 'inherit', 'open', 'closed', '', 'perfect-money', '', '', '2016-02-23 17:18:36', '2016-02-23 14:18:36', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Perfect-Money.png', 0, 'attachment', 'image/png', 0),
(113, 1, '2016-02-23 17:18:37', '2016-02-23 14:18:37', '', 'Privatbank', '', 'inherit', 'open', 'closed', '', 'privatbank', '', '', '2016-02-23 17:18:37', '2016-02-23 14:18:37', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Privatbank.png', 0, 'attachment', 'image/png', 0),
(114, 1, '2016-02-23 17:18:39', '2016-02-23 14:18:39', '', 'Qiwi', '', 'inherit', 'open', 'closed', '', 'qiwi', '', '', '2016-02-23 17:18:39', '2016-02-23 14:18:39', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Qiwi.png', 0, 'attachment', 'image/png', 0),
(115, 1, '2016-02-23 17:18:39', '2016-02-23 14:18:39', '', 'Sberbank', '', 'inherit', 'open', 'closed', '', 'sberbank', '', '', '2016-02-23 17:18:39', '2016-02-23 14:18:39', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Sberbank.png', 0, 'attachment', 'image/png', 0),
(116, 1, '2016-02-23 17:18:40', '2016-02-23 14:18:40', '', 'Skrill', '', 'inherit', 'open', 'closed', '', 'skrill', '', '', '2016-02-23 17:18:40', '2016-02-23 14:18:40', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Skrill.png', 0, 'attachment', 'image/png', 0),
(117, 1, '2016-02-23 17:18:41', '2016-02-23 14:18:41', '', 'SolidTrustPay', '', 'inherit', 'open', 'closed', '', 'solidtrustpay', '', '', '2016-02-23 17:18:41', '2016-02-23 14:18:41', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/SolidTrustPay.png', 0, 'attachment', 'image/png', 0),
(118, 1, '2016-02-23 17:18:42', '2016-02-23 14:18:42', '', 'Tinkoff', '', 'inherit', 'open', 'closed', '', 'tinkoff', '', '', '2016-02-23 17:18:42', '2016-02-23 14:18:42', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Tinkoff.png', 0, 'attachment', 'image/png', 0),
(119, 1, '2016-02-23 17:18:42', '2016-02-23 14:18:42', '', 'Visa-MasterCard', '', 'inherit', 'open', 'closed', '', 'visa-mastercard', '', '', '2016-02-23 17:18:42', '2016-02-23 14:18:42', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Visa-MasterCard.png', 0, 'attachment', 'image/png', 0),
(120, 1, '2016-02-23 17:18:43', '2016-02-23 14:18:43', '', 'WebMoney', '', 'inherit', 'open', 'closed', '', 'webmoney', '', '', '2016-02-23 17:18:43', '2016-02-23 14:18:43', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/WebMoney.png', 0, 'attachment', 'image/png', 0),
(121, 1, '2016-02-23 17:18:44', '2016-02-23 14:18:44', '', 'Webtransfer', '', 'inherit', 'open', 'closed', '', 'webtransfer', '', '', '2016-02-23 17:18:44', '2016-02-23 14:18:44', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Webtransfer.png', 0, 'attachment', 'image/png', 0),
(122, 1, '2016-02-23 17:18:45', '2016-02-23 14:18:45', '', 'Yandex', '', 'inherit', 'open', 'closed', '', 'yandex', '', '', '2016-02-23 17:18:45', '2016-02-23 14:18:45', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Yandex.png', 0, 'attachment', 'image/png', 0),
(123, 1, '2016-02-23 17:18:45', '2016-02-23 14:18:45', '', 'Z-payment', '', 'inherit', 'open', 'closed', '', 'z-payment', '', '', '2016-02-23 17:18:45', '2016-02-23 14:18:45', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Z-payment.png', 0, 'attachment', 'image/png', 0),
(124, 1, '2016-02-23 17:27:07', '2016-02-23 14:27:07', '', 'favicon', '', 'inherit', 'open', 'closed', '', 'favicon', '', '', '2016-02-23 17:27:07', '2016-02-23 14:27:07', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/favicon.png', 0, 'attachment', 'image/png', 0),
(125, 1, '2016-02-23 17:28:14', '2016-02-23 14:28:14', '', 'bitcoin_bottom', '', 'inherit', 'open', 'closed', '', 'bitcoin-bottom', '', '', '2016-02-23 17:28:14', '2016-02-23 14:28:14', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/bitcoin-bottom.png', 0, 'attachment', 'image/png', 0),
(126, 1, '2016-02-23 17:28:15', '2016-02-23 14:28:15', '', 'okpay_bottom', '', 'inherit', 'open', 'closed', '', 'okpay-bottom', '', '', '2016-02-23 17:28:15', '2016-02-23 14:28:15', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/okpay-bottom.png', 0, 'attachment', 'image/png', 0),
(127, 1, '2016-02-23 17:28:16', '2016-02-23 14:28:16', '', 'pm_bottom', '', 'inherit', 'open', 'closed', '', 'pm-bottom', '', '', '2016-02-23 17:28:16', '2016-02-23 14:28:16', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/pm-bottom.png', 0, 'attachment', 'image/png', 0),
(128, 1, '2016-02-23 17:28:16', '2016-02-23 14:28:16', '', 'stp_bottom', '', 'inherit', 'open', 'closed', '', 'stp-bottom', '', '', '2016-02-23 17:28:16', '2016-02-23 14:28:16', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/stp-bottom.png', 0, 'attachment', 'image/png', 0),
(129, 1, '2016-02-23 17:28:17', '2016-02-23 14:28:17', '', 'wm_botton', '', 'inherit', 'open', 'closed', '', 'wm-botton', '', '', '2016-02-23 17:28:17', '2016-02-23 14:28:17', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/wm-botton.png', 0, 'attachment', 'image/png', 0),
(130, 1, '2016-02-23 17:28:18', '2016-02-23 14:28:18', '', 'ya_bottom', '', 'inherit', 'open', 'closed', '', 'ya-bottom', '', '', '2016-02-23 17:28:18', '2016-02-23 14:28:18', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/ya-bottom.png', 0, 'attachment', 'image/png', 0),
(136, 1, '2016-02-23 17:50:32', '2016-02-23 14:50:32', '[en_US:][toinvest][:en_US]', '[en_US:]Invest[:en_US][ru_RU:]Инвестировать[:ru_RU]', '', 'publish', 'closed', 'closed', '', 'toinvest', '', '', '2016-02-23 17:51:05', '2016-02-23 14:51:05', '', 0, 'http://premiumexchanger.ru/toinvest/', 0, 'page', '', 0),
(139, 1, '2016-08-15 17:16:20', '2016-08-15 14:16:20', '', 'exmo', '', 'inherit', 'open', 'closed', '', 'exmo', '', '', '2016-08-15 17:16:20', '2016-08-15 14:16:20', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/exmo.png', 0, 'attachment', 'image/png', 0),
(143, 1, '2016-11-28 20:00:43', '2016-11-28 17:00:43', '', 'alipay', '', 'inherit', 'open', 'closed', '', 'alipay', '', '', '2016-11-28 20:00:43', '2016-11-28 17:00:43', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Alipay.png', 0, 'attachment', 'image/png', 0),
(148, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', '148', '', '', '2017-08-15 20:00:37', '2017-08-15 17:00:37', '', 0, 'http://premiumexchanger.ru/?p=148', 1, 'nav_menu_item', '', 0),
(149, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', 'ru-ru-avtorizatsiya-ru-ru-en-us-authorization-en-us', '', '', '2017-08-15 20:00:37', '2017-08-15 17:00:37', '', 0, 'http://premiumexchanger.ru/?p=149', 2, 'nav_menu_item', '', 0),
(150, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', '150', '', '', '2017-08-15 20:00:38', '2017-08-15 17:00:38', '', 0, 'http://premiumexchanger.ru/?p=150', 10, 'nav_menu_item', '', 0),
(151, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', '151', '', '', '2017-08-15 20:00:38', '2017-08-15 17:00:38', '', 0, 'http://premiumexchanger.ru/?p=151', 11, 'nav_menu_item', '', 0),
(152, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', '152', '', '', '2017-08-15 20:00:37', '2017-08-15 17:00:37', '', 0, 'http://premiumexchanger.ru/?p=152', 4, 'nav_menu_item', '', 0),
(153, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', 'ru-ru-kontaktyi-ru-ru-en-us-feedback-en-us', '', '', '2017-08-15 20:00:38', '2017-08-15 17:00:38', '', 0, 'http://premiumexchanger.ru/?p=153', 19, 'nav_menu_item', '', 0),
(154, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', 'ru-ru-lichnyiy-kabinet-ru-ru-en-us-personal-account-en-us', '', '', '2017-08-15 20:00:38', '2017-08-15 17:00:38', '', 0, 'http://premiumexchanger.ru/?p=154', 9, 'nav_menu_item', '', 0),
(155, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', '155', '', '', '2017-08-15 20:00:38', '2017-08-15 17:00:38', '', 0, 'http://premiumexchanger.ru/?p=155', 12, 'nav_menu_item', '', 0),
(156, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', '156', '', '', '2017-08-15 20:00:37', '2017-08-15 17:00:37', '', 0, 'http://premiumexchanger.ru/?p=156', 5, 'nav_menu_item', '', 0),
(157, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', '157', '', '', '2017-08-15 20:00:38', '2017-08-15 17:00:38', '', 0, 'http://premiumexchanger.ru/?p=157', 7, 'nav_menu_item', '', 0),
(158, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', '158', '', '', '2017-08-15 20:00:38', '2017-08-15 17:00:38', '', 0, 'http://premiumexchanger.ru/?p=158', 14, 'nav_menu_item', '', 0),
(159, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', '159', '', '', '2017-08-15 20:00:38', '2017-08-15 17:00:38', '', 0, 'http://premiumexchanger.ru/?p=159', 15, 'nav_menu_item', '', 0),
(160, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', '160', '', '', '2017-08-15 20:00:38', '2017-08-15 17:00:38', '', 0, 'http://premiumexchanger.ru/?p=160', 16, 'nav_menu_item', '', 0),
(161, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', '161', '', '', '2017-08-15 20:00:38', '2017-08-15 17:00:38', '', 0, 'http://premiumexchanger.ru/?p=161', 13, 'nav_menu_item', '', 0),
(162, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', '162', '', '', '2017-08-15 20:00:38', '2017-08-15 17:00:38', '', 0, 'http://premiumexchanger.ru/?p=162', 8, 'nav_menu_item', '', 0),
(163, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', 'ru-ru-registratsiya-ru-ru-en-us-registration-en-us', '', '', '2017-08-15 20:00:37', '2017-08-15 17:00:37', '', 0, 'http://premiumexchanger.ru/?p=163', 3, 'nav_menu_item', '', 0),
(164, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', '164', '', '', '2017-08-15 20:00:38', '2017-08-15 17:00:38', '', 0, 'http://premiumexchanger.ru/?p=164', 17, 'nav_menu_item', '', 0),
(165, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', '165', '', '', '2017-08-15 20:00:38', '2017-08-15 17:00:38', '', 0, 'http://premiumexchanger.ru/?p=165', 18, 'nav_menu_item', '', 0),
(166, 1, '2016-11-30 21:19:49', '2016-11-30 18:19:49', ' ', '', '', 'publish', 'closed', 'closed', '', '166', '', '', '2017-08-15 20:00:38', '2017-08-15 17:00:38', '', 0, 'http://premiumexchanger.ru/?p=166', 6, 'nav_menu_item', '', 0),
(170, 1, '2017-01-29 14:49:14', '2017-01-29 11:49:14', '', 'Avangardbank', '', 'inherit', 'open', 'closed', '', 'avangardbank', '', '', '2017-01-29 14:49:14', '2017-01-29 11:49:14', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Avangardbank.png', 0, 'attachment', 'image/png', 0),
(171, 1, '2017-01-29 14:49:16', '2017-01-29 11:49:16', '', 'Bank-perevod', '', 'inherit', 'open', 'closed', '', 'bank-perevod', '', '', '2017-01-29 14:49:16', '2017-01-29 11:49:16', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Bank-perevod.png', 0, 'attachment', 'image/png', 0),
(172, 1, '2017-01-29 14:49:18', '2017-01-29 11:49:18', '', 'Cash-EUR', '', 'inherit', 'open', 'closed', '', 'cash-eur', '', '', '2017-01-29 14:49:18', '2017-01-29 11:49:18', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Cash-EUR.png', 0, 'attachment', 'image/png', 0),
(173, 1, '2017-01-29 14:49:20', '2017-01-29 11:49:20', '', 'Cash-RUB', '', 'inherit', 'open', 'closed', '', 'cash-rub', '', '', '2017-01-29 14:49:20', '2017-01-29 11:49:20', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Cash-RUB.png', 0, 'attachment', 'image/png', 0),
(174, 1, '2017-01-29 14:49:22', '2017-01-29 11:49:22', '', 'Cash-USD', '', 'inherit', 'open', 'closed', '', 'cash-usd', '', '', '2017-01-29 14:49:22', '2017-01-29 11:49:22', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Cash-USD.png', 0, 'attachment', 'image/png', 0),
(175, 1, '2017-01-29 14:49:24', '2017-01-29 11:49:24', '', 'PMe-voucher', '', 'inherit', 'open', 'closed', '', 'pme-voucher', '', '', '2017-01-29 14:49:24', '2017-01-29 11:49:24', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/PMe-voucher.png', 0, 'attachment', 'image/png', 0),
(176, 1, '2017-01-29 14:49:27', '2017-01-29 11:49:27', '', 'Promsvyazbank', '', 'inherit', 'open', 'closed', '', 'promsvyazbank', '', '', '2017-01-29 14:49:27', '2017-01-29 11:49:27', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Promsvyazbank.png', 0, 'attachment', 'image/png', 0),
(177, 1, '2017-01-29 14:49:29', '2017-01-29 11:49:29', '', 'Russstandart', '', 'inherit', 'open', 'closed', '', 'russstandart', '', '', '2017-01-29 14:49:29', '2017-01-29 11:49:29', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/Russstandart.png', 0, 'attachment', 'image/png', 0),
(178, 1, '2017-01-29 14:49:31', '2017-01-29 11:49:31', '', 'VTB24', '', 'inherit', 'open', 'closed', '', 'vtb24', '', '', '2017-01-29 14:49:31', '2017-01-29 11:49:31', '', 0, 'http://premiumexchanger.ru/wp-content/uploads/VTB24.png', 0, 'attachment', 'image/png', 0),
(181, 1, '2017-07-22 10:14:24', '2017-07-22 07:14:24', '[exchangestep]', '[ru_RU:]Обмен - шаги[:ru_RU][en_US:]Exchange - steps[:en_US]', '', 'publish', 'closed', 'closed', '', 'hst', '', '', '2017-07-22 10:14:24', '2017-07-22 07:14:24', '', 0, 'http://premiumexchanger.ru/hst/', 0, 'page', '', 0),
(182, 1, '2017-07-22 11:03:54', '2017-07-22 08:03:54', '', '[en_US:]User agreement for personal data processing[:en_US][ru_RU:]Пользовательское соглашение по обработке персональных данных[:ru_RU]', '', 'publish', 'closed', 'closed', '', 'terms-personal-data', '', '', '2017-08-01 13:09:44', '2017-08-01 10:09:44', '', 0, 'http://premiumexchanger.ru/terms-personal-data/', 0, 'page', '', 0),
(183, 1, '2017-07-22 11:03:54', '2017-07-22 08:03:54', '[en_US:][checkstatus_form][:en_US]', '[en_US:]Check order status[:en_US][ru_RU:]Проверка статуса заявки[:ru_RU]', '', 'publish', 'closed', 'closed', '', 'checkstatus', '', '', '2017-08-01 13:09:25', '2017-08-01 10:09:25', '', 0, 'http://premiumexchanger.ru/checkstatus/', 0, 'page', '', 0),
(200, 1, '2017-08-01 13:04:04', '2017-08-01 10:04:04', '', '[ru_RU:]Условия участия в партнерской программе[:ru_RU][en_US:]Affiliate terms and conditions[:en_US]', '', 'publish', 'closed', 'closed', '', 'terms', '', '', '2017-08-01 13:04:04', '2017-08-01 10:04:04', '', 0, 'http://premiumexchanger.ru/terms/', 0, 'page', '', 0),
(206, 1, '2017-08-01 13:59:35', '2017-08-01 10:59:35', '[exchange]', '[ru_RU:]Обмен[:ru_RU][en_US:]Exchange[:en_US]', '', 'publish', 'closed', 'closed', '', 'exchange', '', '', '2017-08-01 13:59:35', '2017-08-01 10:59:35', '', 0, 'http://premiumexchanger.ru/exchange/', 0, 'page', '', 0),
(208, 1, '2017-09-01 12:52:29', '0000-00-00 00:00:00', '', 'Черновик', '', 'auto-draft', 'open', 'closed', '', '', '', '', '2017-09-01 12:52:29', '0000-00-00 00:00:00', '', 0, 'http://premiumexchanger.ru/?p=208', 0, 'post', '', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `pr_psys`
--

CREATE TABLE IF NOT EXISTS `pr_psys` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `psys_title` longtext NOT NULL,
  `psys_logo` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Дамп данных таблицы `pr_psys`
--

INSERT INTO `pr_psys` (`id`, `psys_title`, `psys_logo`) VALUES
(1, '[en_US:]Webmoney[:en_US][ru_RU:]Webmoney[:ru_RU]', '/wp-content/uploads/WebMoney.png'),
(2, '[en_US:]Perfect Money[:en_US][ru_RU:]Perfect Money[:ru_RU]', '/wp-content/uploads/Perfect-Money.png'),
(3, '[en_US:]Yandex.Money[:en_US][ru_RU:]Яндекс.Деньги[:ru_RU]', '/wp-content/uploads/Yandex.png'),
(4, '[en_US:]Okpay[:en_US][ru_RU:]Okpay[:ru_RU]', '/wp-content/uploads/Okpay.png'),
(5, '[en_US:]Privat24[:en_US][ru_RU:]Приват24[:ru_RU]', '/wp-content/uploads/Privatbank.png'),
(6, '[en_US:]Sberbank[:en_US][ru_RU:]Сбербанк[:ru_RU]', '/wp-content/uploads/Sberbank.png');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_recalc_bids`
--

CREATE TABLE IF NOT EXISTS `pr_recalc_bids` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `naps_id` bigint(20) NOT NULL DEFAULT '0',
  `enable_recalc` int(1) NOT NULL DEFAULT '0',
  `cou_hour` varchar(20) NOT NULL DEFAULT '0',
  `cou_minute` varchar(20) NOT NULL DEFAULT '0',
  `statused` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_reserve_requests`
--

CREATE TABLE IF NOT EXISTS `pr_reserve_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rdate` datetime NOT NULL,
  `user_email` varchar(250) NOT NULL,
  `naps_id` bigint(20) NOT NULL DEFAULT '0',
  `naps_title` longtext NOT NULL,
  `amount` varchar(250) NOT NULL,
  `comment` longtext NOT NULL,
  `locale` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_reviews`
--

CREATE TABLE IF NOT EXISTS `pr_reviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `user_name` tinytext NOT NULL,
  `user_email` tinytext NOT NULL,
  `user_site` tinytext NOT NULL,
  `review_date` datetime NOT NULL,
  `review_hash` tinytext NOT NULL,
  `review_text` longtext NOT NULL,
  `review_status` varchar(150) NOT NULL DEFAULT 'moderation',
  `review_locale` varchar(10) NOT NULL,
  `vo_rate` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `pr_reviews`
--

INSERT INTO `pr_reviews` (`id`, `user_id`, `user_name`, `user_email`, `user_site`, `review_date`, `review_hash`, `review_text`, `review_status`, `review_locale`, `vo_rate`) VALUES
(1, 0, 'Гость', '', '', '2015-10-22 22:32:00', '', 'Отличный обменник!', 'publish', 'ru_RU', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `pr_reviews_meta`
--

CREATE TABLE IF NOT EXISTS `pr_reviews_meta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) NOT NULL DEFAULT '0',
  `meta_key` longtext NOT NULL,
  `meta_value` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_sitecaptcha_images`
--

CREATE TABLE IF NOT EXISTS `pr_sitecaptcha_images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uslov` longtext NOT NULL,
  `img1` varchar(250) NOT NULL,
  `img2` varchar(250) NOT NULL,
  `img3` varchar(250) NOT NULL,
  `variant` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_sitecaptcha_user`
--

CREATE TABLE IF NOT EXISTS `pr_sitecaptcha_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `createdate` datetime NOT NULL,
  `sess_hash` varchar(150) NOT NULL,
  `img1` varchar(150) NOT NULL,
  `img2` varchar(150) NOT NULL,
  `img3` varchar(150) NOT NULL,
  `num1` varchar(150) NOT NULL,
  `num2` varchar(150) NOT NULL,
  `num3` varchar(150) NOT NULL,
  `uslov` longtext NOT NULL,
  `variant` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_spbbonus`
--

CREATE TABLE IF NOT EXISTS `pr_spbbonus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `bid_id` bigint(20) NOT NULL DEFAULT '0',
  `bonus_sum` varchar(250) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_standart_captcha`
--

CREATE TABLE IF NOT EXISTS `pr_standart_captcha` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `createdate` datetime NOT NULL,
  `sess_hash` varchar(150) NOT NULL,
  `num1` varchar(10) NOT NULL DEFAULT '0',
  `num2` varchar(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=49 ;

--
-- Дамп данных таблицы `pr_standart_captcha`
--

INSERT INTO `pr_standart_captcha` (`id`, `createdate`, `sess_hash`, `num1`, `num2`) VALUES
(47, '2016-08-15 16:16:19', 'c97a89a4efb7c51bafe7de9e41d52abf', '4', '2'),
(48, '2016-08-15 16:16:27', '8ffb4dc93192c7bf68d7c81bfbe1ce5e', '4', '5');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_standart_captcha_plus`
--

CREATE TABLE IF NOT EXISTS `pr_standart_captcha_plus` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `createdate` datetime NOT NULL,
  `sess_hash` varchar(150) NOT NULL,
  `num1` varchar(10) NOT NULL DEFAULT '0',
  `num2` varchar(10) NOT NULL DEFAULT '0',
  `symbol` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=48 ;

--
-- Дамп данных таблицы `pr_standart_captcha_plus`
--

INSERT INTO `pr_standart_captcha_plus` (`id`, `createdate`, `sess_hash`, `num1`, `num2`, `symbol`) VALUES
(46, '2017-09-01 12:52:00', '644e61d2cf1cd48b14532df27f8b180e', '8', '0', 2),
(47, '2017-09-01 12:52:01', '711729789dc771f44de09d55e3248a8e', '3', '6', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `pr_termmeta`
--

CREATE TABLE IF NOT EXISTS `pr_termmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `term_id` (`term_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_terms`
--

CREATE TABLE IF NOT EXISTS `pr_terms` (
  `term_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL DEFAULT '',
  `slug` varchar(200) NOT NULL DEFAULT '',
  `term_group` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_id`),
  KEY `slug` (`slug`(191)),
  KEY `name` (`name`(191))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `pr_terms`
--

INSERT INTO `pr_terms` (`term_id`, `name`, `slug`, `term_group`) VALUES
(1, 'Без рубрики', 'norubrik', 0),
(2, 'Верхнее меню / Top menu', 'verhnee-menyu-top-menu', 0),
(3, 'Нижнее меню / Footer menu', 'nizhnee-menyu-footer-menu', 0),
(4, 'Мобильное меню / Mobile menu', 'mobilnoe-menyu-mobile-menu', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `pr_term_meta`
--

CREATE TABLE IF NOT EXISTS `pr_term_meta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) NOT NULL DEFAULT '0',
  `meta_key` longtext NOT NULL,
  `meta_value` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_term_relationships`
--

CREATE TABLE IF NOT EXISTS `pr_term_relationships` (
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_taxonomy_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  KEY `term_taxonomy_id` (`term_taxonomy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `pr_term_relationships`
--

INSERT INTO `pr_term_relationships` (`object_id`, `term_taxonomy_id`, `term_order`) VALUES
(30, 2, 0),
(31, 2, 0),
(32, 2, 0),
(33, 2, 0),
(34, 2, 0),
(35, 3, 0),
(36, 3, 0),
(38, 1, 0),
(77, 3, 0),
(148, 4, 0),
(149, 4, 0),
(150, 4, 0),
(151, 4, 0),
(152, 4, 0),
(153, 4, 0),
(154, 4, 0),
(155, 4, 0),
(156, 4, 0),
(157, 4, 0),
(158, 4, 0),
(159, 4, 0),
(160, 4, 0),
(161, 4, 0),
(162, 4, 0),
(163, 4, 0),
(164, 4, 0),
(165, 4, 0),
(166, 4, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `pr_term_taxonomy`
--

CREATE TABLE IF NOT EXISTS `pr_term_taxonomy` (
  `term_taxonomy_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `taxonomy` varchar(32) NOT NULL DEFAULT '',
  `description` longtext NOT NULL,
  `parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `count` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_taxonomy_id`),
  UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`),
  KEY `taxonomy` (`taxonomy`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `pr_term_taxonomy`
--

INSERT INTO `pr_term_taxonomy` (`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES
(1, 1, 'category', '', 0, 1),
(2, 2, 'nav_menu', '', 0, 5),
(3, 3, 'nav_menu', '', 0, 3),
(4, 4, 'nav_menu', '', 0, 19);

-- --------------------------------------------------------

--
-- Структура таблицы `pr_trans_reserv`
--

CREATE TABLE IF NOT EXISTS `pr_trans_reserv` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `trans_title` longtext NOT NULL,
  `trans_create` datetime NOT NULL,
  `trans_edit` datetime NOT NULL,
  `user_creator` bigint(20) NOT NULL DEFAULT '0',
  `user_editor` bigint(20) NOT NULL DEFAULT '0',
  `trans_summ` varchar(50) NOT NULL DEFAULT '0',
  `valut_id` bigint(20) NOT NULL DEFAULT '0',
  `vtype_id` bigint(20) NOT NULL DEFAULT '0',
  `vtype_title` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Дамп данных таблицы `pr_trans_reserv`
--

INSERT INTO `pr_trans_reserv` (`id`, `trans_title`, `trans_create`, `trans_edit`, `user_creator`, `user_editor`, `trans_summ`, `valut_id`, `vtype_id`, `vtype_title`) VALUES
(1, '', '2015-10-22 22:29:34', '0000-00-00 00:00:00', 1, 0, '1000', 2, 3, 'USD'),
(2, '', '2015-10-22 22:29:43', '0000-00-00 00:00:00', 1, 0, '1000', 1, 3, 'USD'),
(3, '', '2015-10-22 22:29:55', '0000-00-00 00:00:00', 1, 0, '1000', 4, 1, 'RUB'),
(4, '', '2015-10-22 22:30:06', '0000-00-00 00:00:00', 1, 0, '1000', 3, 3, 'USD'),
(5, '', '2015-10-22 22:30:20', '0000-00-00 00:00:00', 1, 0, '1000', 5, 4, 'UAH'),
(6, '', '2015-10-22 22:30:36', '0000-00-00 00:00:00', 1, 0, '1000', 7, 1, 'RUB'),
(7, '', '2015-10-22 22:31:02', '0000-00-00 00:00:00', 1, 0, '1000', 6, 1, 'RUB'),
(8, '', '2015-10-22 22:32:13', '0000-00-00 00:00:00', 1, 0, '1000', 8, 2, 'EUR');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_trans_reserv_logs`
--

CREATE TABLE IF NOT EXISTS `pr_trans_reserv_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `valut_id` bigint(20) NOT NULL DEFAULT '0',
  `trans_id` bigint(20) NOT NULL DEFAULT '0',
  `trans_type` int(1) NOT NULL DEFAULT '0',
  `trans_date` datetime NOT NULL,
  `old_sum` varchar(250) NOT NULL DEFAULT '0',
  `new_sum` varchar(250) NOT NULL DEFAULT '0',
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `user_login` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_usermeta`
--

CREATE TABLE IF NOT EXISTS `pr_usermeta` (
  `umeta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`umeta_id`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=35 ;

--
-- Дамп данных таблицы `pr_usermeta`
--

INSERT INTO `pr_usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`) VALUES
(1, 1, 'nickname', 'superboss'),
(2, 1, 'first_name', 'Иван'),
(3, 1, 'last_name', 'Иванов'),
(4, 1, 'description', ''),
(5, 1, 'rich_editing', 'true'),
(6, 1, 'comment_shortcuts', 'false'),
(7, 1, 'admin_color', 'fresh'),
(8, 1, 'use_ssl', '0'),
(9, 1, 'show_admin_bar_front', 'true'),
(10, 1, 'pr_capabilities', 'a:1:{s:13:"administrator";b:1;}'),
(11, 1, 'pr_user_level', '10'),
(12, 1, 'dismissed_wp_pointers', ''),
(13, 1, 'show_welcome_panel', '0'),
(15, 1, 'pr_dashboard_quick_press_last_post_id', '208'),
(16, 1, 'admin_time_last', '1504270410'),
(17, 1, 'managenav-menuscolumnshidden', 'a:5:{i:0;s:11:"link-target";i:1;s:11:"css-classes";i:2;s:3:"xfn";i:3;s:11:"description";i:4;s:15:"title-attribute";}'),
(18, 1, 'metaboxhidden_nav-menus', 'a:1:{i:0;s:12:"add-post_tag";}'),
(19, 1, 'nav_menu_recently_edited', '2'),
(20, 1, 'pr_user-settings', 'editor=html&libraryContent=browse'),
(21, 1, 'pr_user-settings-time', '1500726398'),
(22, 1, 'closedpostboxes_dashboard', 'a:1:{i:0;s:21:"dashboard_quick_press";}'),
(23, 1, 'metaboxhidden_dashboard', 'a:0:{}'),
(24, 1, 'meta-box-order_dashboard', 'a:4:{s:6:"normal";s:149:"standart_security_dashboard_widget,standart_user_dashboard_widget,license_pn_dashboard_widget,statuswork_dashboard_widget,maintrance_dashboard_widget";s:4:"side";s:60:"dashboard_right_now,dashboard_activity,dashboard_quick_press";s:7:"column3";s:0:"";s:7:"column4";s:0:"";}'),
(25, 1, 'second_name', 'Иванович'),
(26, 1, 'user_phone', '1234567'),
(27, 1, 'user_skype', 'skype'),
(28, 1, 'user_passport', ''),
(29, 1, 'admin_comment', ''),
(32, 1, 'locale', ''),
(33, 1, 'user_bann', '0'),
(34, 1, 'session_tokens', 'a:1:{s:64:"0c105817c8608237f537747723aeb76915775e1258d533662b2f5269170b7673";a:4:{s:10:"expiration";i:1504518746;s:2:"ip";s:9:"127.0.0.1";s:2:"ua";s:115:"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36";s:5:"login";i:1504259546;}}');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_users`
--

CREATE TABLE IF NOT EXISTS `pr_users` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `user_pass` varchar(255) NOT NULL DEFAULT '',
  `user_nicename` varchar(50) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `user_url` varchar(100) NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(255) NOT NULL DEFAULT '',
  `user_status` int(11) NOT NULL DEFAULT '0',
  `display_name` varchar(250) NOT NULL DEFAULT '',
  `user_discount` varchar(50) NOT NULL DEFAULT '0',
  `sec_lostpass` int(1) NOT NULL DEFAULT '1',
  `sec_login` int(1) NOT NULL DEFAULT '0',
  `email_login` int(1) NOT NULL DEFAULT '0',
  `auto_login1` varchar(250) NOT NULL,
  `auto_login2` varchar(250) NOT NULL,
  `ref_id` bigint(20) NOT NULL DEFAULT '0',
  `partner_pers` varchar(50) NOT NULL DEFAULT '0',
  `user_verify` int(1) NOT NULL DEFAULT '0',
  `enable_ips` longtext NOT NULL,
  `rconfirm` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`),
  KEY `user_email` (`user_email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `pr_users`
--

INSERT INTO `pr_users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`, `user_discount`, `sec_lostpass`, `sec_login`, `email_login`, `auto_login1`, `auto_login2`, `ref_id`, `partner_pers`, `user_verify`, `enable_ips`, `rconfirm`) VALUES
(1, 'superboss', '$P$Bn.KgRJaFWfr4nPMPUIrO0ATDmE5m.0', 'superboss', 'info@premium.ru', '', '2015-10-22 14:25:04', '', 0, 'superboss', '0', 0, 0, 0, '$1$tz5.yg..$9GpXSYwtrzwGEJvUnFpoq.', '$1$Tq3.gv5.$u5wEob5qQt8GgwpQFsRJJ1', 0, '0', 0, '', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `pr_userverify`
--

CREATE TABLE IF NOT EXISTS `pr_userverify` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `createdate` datetime NOT NULL,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `user_login` varchar(250) NOT NULL,
  `user_email` varchar(250) NOT NULL,
  `theip` varchar(250) NOT NULL,
  `comment` longtext NOT NULL,
  `locale` varchar(20) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_user_accounts`
--

CREATE TABLE IF NOT EXISTS `pr_user_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `user_login` varchar(250) NOT NULL,
  `valut_id` bigint(20) NOT NULL DEFAULT '0',
  `accountnum` longtext NOT NULL,
  `verify` int(1) NOT NULL DEFAULT '0',
  `vidzn` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `pr_user_accounts`
--

INSERT INTO `pr_user_accounts` (`id`, `user_id`, `user_login`, `valut_id`, `accountnum`, `verify`, `vidzn`) VALUES
(2, 1, 'superboss', 7, '1234567812345678', 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `pr_user_discounts`
--

CREATE TABLE IF NOT EXISTS `pr_user_discounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sumec` varchar(50) NOT NULL DEFAULT '0',
  `discount` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Дамп данных таблицы `pr_user_discounts`
--

INSERT INTO `pr_user_discounts` (`id`, `sumec`, `discount`) VALUES
(1, '0', '0'),
(2, '500', '0.1'),
(3, '1000', '0.2'),
(4, '2000', '0.3'),
(5, '5000', '0.4'),
(6, '10000', '0.5');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_user_fav`
--

CREATE TABLE IF NOT EXISTS `pr_user_fav` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `link` varchar(250) NOT NULL DEFAULT '0',
  `title` varchar(250) NOT NULL DEFAULT '0',
  `menu_order` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_uv_accounts`
--

CREATE TABLE IF NOT EXISTS `pr_uv_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `createdate` datetime NOT NULL,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `user_login` varchar(250) NOT NULL,
  `user_email` varchar(250) NOT NULL,
  `valut_id` bigint(20) NOT NULL DEFAULT '0',
  `usac_id` bigint(20) NOT NULL DEFAULT '0',
  `theip` varchar(250) NOT NULL,
  `accountnum` longtext NOT NULL,
  `locale` varchar(20) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_uv_accounts_files`
--

CREATE TABLE IF NOT EXISTS `pr_uv_accounts_files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `uv_data` longtext NOT NULL,
  `uv_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_uv_field`
--

CREATE TABLE IF NOT EXISTS `pr_uv_field` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` longtext NOT NULL,
  `fieldvid` int(1) NOT NULL DEFAULT '0',
  `locale` varchar(20) NOT NULL,
  `uv_auto` varchar(250) NOT NULL,
  `uv_req` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '0',
  `uv_order` bigint(20) NOT NULL DEFAULT '0',
  `helps` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `pr_uv_field`
--

INSERT INTO `pr_uv_field` (`id`, `title`, `fieldvid`, `locale`, `uv_auto`, `uv_req`, `status`, `uv_order`, `helps`) VALUES
(1, '[ru_RU:]Фамилия[:ru_RU][en_US:]Surname[:en_US]', 0, '0', 'last_name', 1, 1, 0, ''),
(2, '[ru_RU:]Имя[:ru_RU][en_US:]Name[:en_US]', 0, '0', 'first_name', 1, 1, 0, ''),
(3, '[ru_RU:]Отчество[:ru_RU][en_US:]Middle name[:en_US]', 0, '0', 'second_name', 1, 1, 0, ''),
(4, '[ru_RU:]Скан вашего паспорта[:ru_RU][en_US:]Scan of your passport[:en_US]', 1, '0', '0', 1, 1, 0, ''),
(5, '[ru_RU:]Фото с развернутым паспортом в руках на фоне сайта[:ru_RU][en_US:]Foto with your passport in hand[:en_US]', 1, '0', '0', 1, 1, 0, '');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_uv_field_user`
--

CREATE TABLE IF NOT EXISTS `pr_uv_field_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `uv_data` longtext NOT NULL,
  `uv_id` bigint(20) NOT NULL DEFAULT '0',
  `uv_field` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_valuts`
--

CREATE TABLE IF NOT EXISTS `pr_valuts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `valut_logo` longtext NOT NULL,
  `valut_decimal` int(2) NOT NULL DEFAULT '2',
  `valut_status` int(1) NOT NULL DEFAULT '1',
  `valut_reserv` varchar(50) NOT NULL DEFAULT '0',
  `xml_value` varchar(250) NOT NULL,
  `inday1` varchar(50) NOT NULL DEFAULT '0',
  `inday2` varchar(50) NOT NULL DEFAULT '0',
  `minzn` int(2) NOT NULL DEFAULT '0',
  `maxzn` int(5) NOT NULL DEFAULT '100',
  `firstzn` varchar(20) NOT NULL,
  `cifrzn` int(2) NOT NULL DEFAULT '0',
  `vidzn` int(2) NOT NULL DEFAULT '0',
  `lead_num` varchar(20) NOT NULL DEFAULT '0',
  `helps` longtext NOT NULL,
  `txt1` longtext NOT NULL,
  `txt2` longtext NOT NULL,
  `show1` int(2) NOT NULL DEFAULT '1',
  `show2` int(2) NOT NULL DEFAULT '1',
  `pvivod` int(2) NOT NULL DEFAULT '1',
  `psys_id` bigint(20) NOT NULL DEFAULT '0',
  `psys_title` longtext NOT NULL,
  `vtype_id` bigint(20) NOT NULL DEFAULT '0',
  `vtype_title` longtext NOT NULL,
  `quickpay` bigint(20) NOT NULL DEFAULT '0',
  `cf_hidden` int(2) NOT NULL DEFAULT '0',
  `psys_logo` longtext NOT NULL,
  `reserv_place` varchar(150) NOT NULL DEFAULT '0',
  `inmon1` varchar(50) NOT NULL DEFAULT '0',
  `inmon2` varchar(50) NOT NULL DEFAULT '0',
  `reserv_order` bigint(20) NOT NULL DEFAULT '0',
  `check_text` longtext NOT NULL,
  `check_purse` varchar(150) NOT NULL DEFAULT '0',
  `helps2` longtext NOT NULL,
  `user_account` int(2) NOT NULL DEFAULT '1',
  `site_order` bigint(20) NOT NULL DEFAULT '0',
  `max_reserv` varchar(50) NOT NULL DEFAULT '0',
  `payout_com` varchar(50) NOT NULL DEFAULT '0',
  `paybonus` int(2) NOT NULL DEFAULT '0',
  `vo_cat` int(5) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Дамп данных таблицы `pr_valuts`
--

INSERT INTO `pr_valuts` (`id`, `valut_logo`, `valut_decimal`, `valut_status`, `valut_reserv`, `xml_value`, `inday1`, `inday2`, `minzn`, `maxzn`, `firstzn`, `cifrzn`, `vidzn`, `lead_num`, `helps`, `txt1`, `txt2`, `show1`, `show2`, `pvivod`, `psys_id`, `psys_title`, `vtype_id`, `vtype_title`, `quickpay`, `cf_hidden`, `psys_logo`, `reserv_place`, `inmon1`, `inmon2`, `reserv_order`, `check_text`, `check_purse`, `helps2`, `user_account`, `site_order`, `max_reserv`, `payout_com`, `paybonus`, `vo_cat`) VALUES
(1, '/wp-content/uploads/Perfect-Money.png', 4, 1, '1000', 'PMUSD', '0', '0', 0, 0, '', 0, 0, '1', '', '', '', 1, 1, 1, 2, '[en_US:]Perfect Money[:en_US][ru_RU:]Perfect Money[:ru_RU]', 3, 'USD', 0, 0, '/wp-content/uploads/Perfect-Money.png', '0', '0', '0', 0, '', '0', '', 1, 1, '0', '0', 0, 1),
(2, '/wp-content/uploads/Okpay.png', 4, 1, '1000', 'OKUSD', '0', '0', 0, 0, '', 0, 0, '1', '', '', '', 1, 1, 1, 4, '[en_US:]Okpay[:en_US][ru_RU:]Okpay[:ru_RU]', 3, 'USD', 3, 0, '/wp-content/uploads/Okpay.png', '0', '0', '0', 0, '', '0', '', 1, 5, '0', '0', 0, 1),
(3, '/wp-content/uploads/WebMoney.png', 4, 1, '1000', 'WMZ', '0', '0', 0, 0, '', 0, 0, '1', '', '', '', 1, 1, 1, 1, '[en_US:]Webmoney[:en_US][ru_RU:]Webmoney[:ru_RU]', 3, 'USD', 0, 0, '/wp-content/uploads/WebMoney.png', '0', '0', '0', 0, '', '0', '', 1, 3, '0', '0', 0, 1),
(4, '/wp-content/uploads/WebMoney.png', 4, 1, '1000', 'WMR', '0', '0', 0, 0, '', 0, 0, '100', '', '', '', 1, 1, 1, 1, '[en_US:]Webmoney[:en_US][ru_RU:]Webmoney[:ru_RU]', 1, 'RUB', 0, 0, '/wp-content/uploads/WebMoney.png', '0', '0', '0', 0, '', '0', '', 1, 4, '0', '0', 0, 1),
(5, '/wp-content/uploads/Privatbank.png', 4, 1, '1000', 'P24UAH', '0', '0', 0, 0, '', 0, 0, '1000', '', '', '', 1, 1, 1, 5, '[en_US:]Privat24[:en_US][ru_RU:]Приват24[:ru_RU]', 4, 'UAH', 0, 0, '/wp-content/uploads/Privatbank.png', '0', '0', '0', 0, '', '0', '', 1, 8, '0', '0', 0, 1),
(6, '/wp-content/uploads/Yandex.png', 4, 1, '1000', 'YAMRUB', '0', '0', 0, 0, '', 0, 0, '100', '', '', '', 1, 1, 1, 3, '[en_US:]Yandex.Money[:en_US][ru_RU:]Яндекс.Деньги[:ru_RU]', 1, 'RUB', 2, 0, '/wp-content/uploads/Yandex.png', '0', '0', '0', 0, '', '0', '', 1, 6, '0', '0', 0, 1),
(7, '/wp-content/uploads/Sberbank.png', 4, 1, '1000', 'SBERRUB', '0', '0', 0, 0, '', 1, 1, '100', '', '', '', 1, 1, 1, 6, '[en_US:]Sberbank[:en_US][ru_RU:]Сбербанк[:ru_RU]', 1, 'RUB', 0, 0, '/wp-content/uploads/Sberbank.png', '0', '0', '0', 0, '', '0', '', 1, 7, '0', '0', 0, 3),
(8, '/wp-content/uploads/Perfect-Money.png', 4, 1, '1000', 'PMEUR', '0', '0', 0, 0, '', 0, 0, '1', '', '', '', 1, 1, 1, 2, '[en_US:]Perfect Money[:en_US][ru_RU:]Perfect Money[:ru_RU]', 2, 'EUR', 0, 0, '/wp-content/uploads/Perfect-Money.png', '0', '0', '0', 0, '', '0', '', 1, 2, '0', '0', 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `pr_valuts_account`
--

CREATE TABLE IF NOT EXISTS `pr_valuts_account` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `valut_id` bigint(20) NOT NULL DEFAULT '0',
  `accountnum` longtext NOT NULL,
  `count_visit` int(5) NOT NULL DEFAULT '0',
  `max_visit` int(5) NOT NULL DEFAULT '0',
  `text_comment` longtext NOT NULL,
  `inday` varchar(50) NOT NULL DEFAULT '0',
  `inmonth` varchar(50) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_valuts_fstats`
--

CREATE TABLE IF NOT EXISTS `pr_valuts_fstats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `valut_id` bigint(20) NOT NULL DEFAULT '0',
  `com_summ` varchar(50) NOT NULL DEFAULT '0',
  `com_pers` varchar(20) NOT NULL DEFAULT '0',
  `nscom` int(1) NOT NULL DEFAULT '0',
  `minsummcom` varchar(50) NOT NULL DEFAULT '0',
  `maxsummcom` varchar(250) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_valuts_meta`
--

CREATE TABLE IF NOT EXISTS `pr_valuts_meta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) NOT NULL DEFAULT '0',
  `meta_key` longtext NOT NULL,
  `meta_value` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Дамп данных таблицы `pr_valuts_meta`
--

INSERT INTO `pr_valuts_meta` (`id`, `item_id`, `meta_key`, `meta_value`) VALUES
(1, 1, 'has_verify', '0'),
(2, 1, 'verify_files', '0'),
(3, 1, 'help_verify', ''),
(4, 7, 'has_verify', '1'),
(5, 7, 'verify_files', '2'),
(6, 7, 'help_verify', '[ru_RU:]Тест[:ru_RU]');

-- --------------------------------------------------------

--
-- Структура таблицы `pr_vo_support`
--

CREATE TABLE IF NOT EXISTS `pr_vo_support` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` longtext NOT NULL,
  `content` longtext NOT NULL,
  `menu_item` bigint(20) NOT NULL DEFAULT '0',
  `status` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_vtypes`
--

CREATE TABLE IF NOT EXISTS `pr_vtypes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vtype_title` longtext NOT NULL,
  `vncurs` varchar(50) NOT NULL DEFAULT '0',
  `parser` bigint(20) NOT NULL DEFAULT '0',
  `nums` varchar(50) NOT NULL DEFAULT '0',
  `elem` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Дамп данных таблицы `pr_vtypes`
--

INSERT INTO `pr_vtypes` (`id`, `vtype_title`, `vncurs`, `parser`, `nums`, `elem`) VALUES
(1, 'RUB', '58.0557', 1, '0', 0),
(2, 'EUR', '1.1825', 51, '0', 0),
(3, 'USD', '1', 0, '0', 0),
(4, 'UAH', '25.69396', 101, '0', 0),
(5, 'AMD', '1', 0, '0', 0),
(6, 'KZT', '337.04', 151, '0', 0),
(7, 'GLD', '1', 0, '0', 0),
(8, 'BYN', '1.9353', 201, '0', 0),
(9, 'UZS', '1', 0, '0', 0),
(10, 'BTC', '0.0003729', 352, '0', 0),
(11, 'TRY', '1', 0, '0', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `pr_vtypes_fstats`
--

CREATE TABLE IF NOT EXISTS `pr_vtypes_fstats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `vtype_id` bigint(20) NOT NULL DEFAULT '0',
  `vncurs` varchar(50) NOT NULL DEFAULT '0',
  `parser` bigint(20) NOT NULL DEFAULT '0',
  `nums` varchar(50) NOT NULL DEFAULT '0',
  `elem` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_warning_mess`
--

CREATE TABLE IF NOT EXISTS `pr_warning_mess` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `datestart` datetime NOT NULL,
  `dateend` datetime NOT NULL,
  `url` longtext NOT NULL,
  `text` longtext NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `theclass` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `pr_zapros_reserv`
--

CREATE TABLE IF NOT EXISTS `pr_zapros_reserv` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `zdate` datetime NOT NULL,
  `email` varchar(250) NOT NULL,
  `naps_id` bigint(20) NOT NULL DEFAULT '0',
  `naps_title` longtext NOT NULL,
  `zsum` varchar(250) NOT NULL,
  `comment` longtext NOT NULL,
  `locale` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
