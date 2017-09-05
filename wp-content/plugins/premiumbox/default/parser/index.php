<?php
if( !defined( 'ABSPATH')){ exit(); }
	
/* 
Подключаем к меню
*/
add_action('admin_menu', 'pn_adminpage_parser');
function pn_adminpage_parser(){
global $premiumbox;
	
	add_menu_page(__('Parsers','pn'), __('Parsers','pn'), 'administrator', 'pn_parser', array($premiumbox, 'admin_temp'), $premiumbox->get_icon_link('parser'));  
	add_submenu_page("pn_parser", __('Parsers','pn'), __('Parsers','pn'), 'administrator', "pn_parser", array($premiumbox, 'admin_temp'));
	
}

add_filter('get_pn_parser','get_pn_parser_def');
function get_pn_parser_def($parsers){
	
	$parsers = array(
		'1' => array(
			'title' => 'USD - RUB',
			'birg' => 'CBR.RU',
			'curs' => 1,
		),
		'2' => array(
			'title' => 'RUB - USD',
			'birg' => 'CBR.RU',
			'curs' => 1000,
		),
		'3' => array(
			'title' => 'EUR - RUB',
			'birg' => 'CBR.RU',
			'curs' => 1,
		),
		'4' => array(
			'title' => 'RUB - EUR',
			'birg' => 'CBR.RU',
			'curs' => 1000,
		),
		'5' => array(
			'title' => 'UAH - RUB',
			'birg' => 'CBR.RU',
			'curs' => 100,
		),		
		'6' => array(
			'title' => 'RUB - UAH',
			'birg' => 'CBR.RU',
			'curs' => 100,
		),
		'7' => array(
			'title' => 'KZT - RUB',
			'birg' => 'CBR.RU',
			'curs' => 100,
		),
		'8' => array(
			'title' => 'AMD - RUB',
			'birg' => 'CBR.RU',
			'curs' => 100,
		),
		'9' => array(
			'title' => 'RUB - AMD',
			'birg' => 'CBR.RU',
			'curs' => 1000,
		),
		'10' => array(
			'title' => 'BYN - RUB',
			'birg' => 'CBR.RU',
			'curs' => 1,
		),	
		/* *** */
		'51' => array(
			'title' => 'EUR - USD',
			'birg' => 'ECB.EU',
			'curs' => 1,
		),
		'52' => array(
			'title' => 'USD - EUR',
			'birg' => 'ECB.EU',
			'curs' => 1,
		),
		/* *** */
		
		'101' => array(
			'title' => 'USD - UAH',
			'birg' => 'НБУ',
			'data' => array('buy','sale'),
			'curs' => 100,
		),
		'102' => array(
			'title' => 'UAH - USD',
			'birg' => 'НБУ',
			'curs' => 1000,
		),
		'103' => array(
			'title' => 'EUR - UAH',
			'birg' => 'НБУ',
			'data' => array('buy','sale'),
			'curs' => 100,
		),
		'104' => array(
			'title' => 'UAH - EUR',
			'birg' => 'НБУ',
			'curs' => 1000,
		),	
		/* *** */
		
		'105' => array(
			'title' => 'USD - UAH',
			'birg' => 'PRIVATBANK.UA',
			'data' => array('buy','sale'),
			'curs' => 100,
		),
		'106' => array(
			'title' => 'UAH - USD',
			'birg' => 'PRIVATBANK.UA',
			'curs' => 1000,
		),
		'107' => array(
			'title' => 'EUR - UAH',
			'birg' => 'PRIVATBANK.UA',
			'data' => array('buy','sale'),
			'curs' => 100,
		),
		'108' => array(
			'title' => 'UAH - EUR',
			'birg' => 'PRIVATBANK.UA',
			'curs' => 1000,
		),	
		
		/* *** */
		'151' => array(
			'title' => 'USD - KZT',
			'birg' => 'NATIONALBANK.KZ',
			'curs' => 1,
		),
		'152' => array(
			'title' => 'KZT - USD',
			'birg' => 'NATIONALBANK.KZ',
			'curs' => 1000,
		),		
		'153' => array(
			'title' => 'EUR - KZT',
			'birg' => 'NATIONALBANK.KZ',
			'curs' => 1,
		),
		'154' => array(
			'title' => 'KZT - EUR',
			'birg' => 'NATIONALBANK.KZ',
			'curs' => 1000,
		),		
		'155' => array(
			'title' => 'RUB - KZT',
			'birg' => 'NATIONALBANK.KZ',
			'curs' => 1,
		),
		'156' => array(
			'title' => 'KZT - RUB',
			'birg' => 'NATIONALBANK.KZ',
			'curs' => 100,
		),		
		/* *** */
		'201' => array(
			'title' => 'USD - BYN',
			'birg' => 'NBRB.BY',
			'curs' => 1,
		),
		'202' => array(
			'title' => 'BYN - USD',
			'birg' => 'NBRB.BY',
			'curs' => 10,
		),
		'203' => array(
			'title' => 'EUR - BYN',
			'birg' => 'NBRB.BY',
			'curs' => 1,
		),
		'204' => array(
			'title' => 'BYN - EUR',
			'birg' => 'NBRB.BY',
			'curs' => 10,
		),
		'205' => array(
			'title' => 'RUB - BYN',
			'birg' => 'NBRB.BY',
			'curs' => 100,
		),	
		
		/* *** */
		
		'251' => array(
			'title' => 'WMZ - WMR',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),	
		'252' => array(
			'title' => 'WMR - WMZ',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),		
		'253' => array(
			'title' => 'WME - WMR',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'254' => array(
			'title' => 'WMR - WME',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),		
		'255' => array(
			'title' => 'WMZ - WME',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),		
		'256' => array(
			'title' => 'WME - WMZ',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'257' => array(
			'title' => 'WMZ - WMU',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'258' => array(
			'title' => 'WMU - WMZ',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'259' => array(
			'title' => 'WMR - WMU',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'260' => array(
			'title' => 'WMU - WMR',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'261' => array(
			'title' => 'WMU - WME',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'262' => array(
			'title' => 'WME - WMU',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'263' => array(
			'title' => 'WMZ - WMG',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),		
		'264' => array(
			'title' => 'WMG - WMZ',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'265' => array(
			'title' => 'WME - WMG',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'266' => array(
			'title' => 'WMG - WME',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'267' => array(
			'title' => 'WMR - WMG',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'268' => array(
			'title' => 'WMG - WMR',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'269' => array(
			'title' => 'WMU - WMG',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'270' => array(
			'title' => 'WMG - WMU',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'271' => array(
			'title' => 'WMZ - WMX',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'272' => array(
			'title' => 'WMX - WMZ',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'273' => array(
			'title' => 'WME - WMX',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'274' => array(
			'title' => 'WMX - WME',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'275' => array(
			'title' => 'WMR - WMX',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'276' => array(
			'title' => 'WMX - WMR',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'277' => array(
			'title' => 'WMU - WMX',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'278' => array(
			'title' => 'WMX - WMU',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'279' => array(
			'title' => 'WMK - WMZ',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'280' => array(
			'title' => 'WMZ - WMK',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'281' => array(
			'title' => 'WMK - WME',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'282' => array(
			'title' => 'WME - WMK',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'283' => array(
			'title' => 'WMR - WMK',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),		
		'284' => array(
			'title' => 'WMK - WMR',
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'285' => array(
			'title' => 'WMB - WMZ', //17
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),		
		'286' => array(
			'title' => 'WMZ - WMB', //18
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),		
		'287' => array(
			'title' => 'WMB - WME', //19
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),	
		'288' => array(
			'title' => 'WME - WMB', //20
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),	
		'289' => array(
			'title' => 'WMR - WMB', //23
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'290' => array(
			'title' => 'WMB - WMR', //24
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'291' => array(
			'title' => 'WMB - WMU', //47
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),	
		'292' => array(
			'title' => 'WMU - WMB', //48
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'293' => array(
			'title' => 'WMB - WMX', //49
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'294' => array(
			'title' => 'WMX - WMB', //50
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),	
		'295' => array(
			'title' => 'WMB - WMG', //53
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'296' => array(
			'title' => 'WMG - WMB', //54
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'297' => array(
			'title' => 'WMB - WMK', //55
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),
		'298' => array(
			'title' => 'WMK - WMB', //56
			'birg' => 'Wm.exchanger',
			'curs' => 1,
		),		
		/* *** */
		
		'301' => array(
			'title' => 'BTC - USD',
			'birg' => __('BSTAMP','pn'),
			'data' => array('last','high','low','bid','vwap','ask','open'),
			'curs' => 1,
		),	
		
		/* *** */
/* 		
		'351' => array(
			'title' => 'BTC - USD',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'352' => array(
			'title' => 'USD - BTC',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1000,
		),
		'353' => array(
			'title' => 'BTC - RUB',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'354' => array(
			'title' => 'RUB - BTC',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 10000,
		),
		'355' => array(
			'title' => 'BTC - EUR',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'356' => array(
			'title' => 'EUR - BTC',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1000,
		),
		'357' => array(
			'title' => 'BTC - UAH',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 10,
		),
		'358' => array(
			'title' => 'UAH - BTC',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1000,
		),
		'359' => array(
			'title' => 'LTC - USD',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'360' => array(
			'title' => 'USD - LTC',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1000,
		),
		'361' => array(
			'title' => 'LTC - EUR',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'362' => array(
			'title' => 'EUR - LTC',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1000,
		),
		'363' => array(
			'title' => 'LTC - RUB',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'364' => array(
			'title' => 'RUB - LTC',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 10000,
		),
		'365' => array(
			'title' => 'LTC - BTC',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'366' => array(
			'title' => 'BTC - LTC',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'367' => array(
			'title' => 'NVC - BTC',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'368' => array(
			'title' => 'NMC - BTC',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'369' => array(
			'title' => 'NVC - USD',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'370' => array(
			'title' => 'NMC - USD',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),			
		'373' => array(
			'title' => 'ETH - BTC',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'374' => array(
			'title' => 'BTC - ETH',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),		
		'375' => array(
			'title' => 'ETH - USD',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'376' => array(
			'title' => 'USD - ETH',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),		
		'377' => array(
			'title' => 'ETH - LTC',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'378' => array(
			'title' => 'LTC - ETH',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),		
		'379' => array(
			'title' => 'ETH - RUB',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'380' => array(
			'title' => 'RUB - ETH',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1000,
		),
		
		'381' => array(
			'title' => 'USD - RUB',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'382' => array(
			'title' => 'RUB - USD',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1000,
		),
		
		'383' => array(
			'title' => 'EUR - RUB',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'384' => array(
			'title' => 'RUB - EUR',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1000,
		),
		'385' => array(
			'title' => 'EUR - USD',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'386' => array(
			'title' => 'USD - EUR',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'371' => array(
			'title' => 'DSH - BTC',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'372' => array(
			'title' => 'BTC - DSH',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),		
		'388' => array(
			'title' => 'DSH - USD',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'389' => array(
			'title' => 'USD - DSH',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1000,
		),		
		'390' => array(
			'title' => 'DSH - RUB',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),
		'391' => array(
			'title' => 'RUB - DSH',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1000,
		),		
		'392' => array(
			'title' => 'DSH - EUR',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1,
		),		
		'393' => array(
			'title' => 'EUR - DSH',
			'birg' => __('BTC-E','pn'),
			'data' => array('last','high','low','buy','avg','sell'),
			'curs' => 1000,
		),		 
*/

		/* *** */
		'400' => array(
			'title' => 'USD - RUB',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'401' => array(
			'title' => 'USD - EUR',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'402' => array(
			'title' => 'USD - UAH',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'403' => array(
			'title' => 'EUR - USD',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'404' => array(
			'title' => 'EUR - RUB',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'405' => array(
			'title' => 'EUR - UAH',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'406' => array(
			'title' => 'RUB - USD',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'407' => array(
			'title' => 'RUB - EUR',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'408' => array(
			'title' => 'RUB - UAH',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'409' => array(
			'title' => 'UAH - USD',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'410' => array(
			'title' => 'UAH - EUR',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'411' => array(
			'title' => 'UAH - RUB',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),	
		'412' => array(
			'title' => 'USD - CNY',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'413' => array(
			'title' => 'CNY - USD',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'414' => array(
			'title' => 'UAH - CNY',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'415' => array(
			'title' => 'CNY - UAH',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'416' => array(
			'title' => 'CNY - RUB',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'417' => array(
			'title' => 'RUB - CNY',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'418' => array(
			'title' => 'CNY - EUR',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'419' => array(
			'title' => 'EUR - CNY',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),

		'420' => array(
			'title' => 'CNY - UAH',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'421' => array(
			'title' => 'UAH - CNY',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'422' => array(
			'title' => 'CNY - BYN',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'423' => array(
			'title' => 'BYN - CNY',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'424' => array(
			'title' => 'CNY - KZT',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		'425' => array(
			'title' => 'KZT - CNY',
			'birg' => 'Yahoo',
			'curs' => 1,
			'data' => array('Ask','Bid','Rate'),
		),
		
		/*******/
 		
		'551' => array(
			'title' => 'BTC - USD',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'552' => array(
			'title' => 'USD - BTC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1000,
		),
		'553' => array(
			'title' => 'BTC - EUR',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'554' => array(
			'title' => 'EUR - BTC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1000,
		),
		'555' => array(
			'title' => 'BTC - RUB',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'556' => array(
			'title' => 'RUB - BTC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 10000,
		),			 
		'557' => array(
			'title' => 'BTC - UAH',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'558' => array(
			'title' => 'UAH - BTC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 10000,
		),
		'559' => array(
			'title' => 'DASH - BTC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'560' => array(
			'title' => 'BTC - DASH',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'561' => array(
			'title' => 'DASH - USD',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'562' => array(
			'title' => 'USD - DASH',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1000,
		),
		'563' => array(
			'title' => 'ETH - BTC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 10,
		),
		'564' => array(
			'title' => 'BTC - ETH',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'565' => array(
			'title' => 'ETH - USD',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'566' => array(
			'title' => 'USD - ETH',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1000,
		), 
		'585' => array(
			'title' => 'ETH - UAH',
			'birg' => __('ExMo','pn'),
			'curs' => 1,
		),
		'586' => array(
			'title' => 'UAH - ETH',
			'birg' => __('ExMo','pn'),
			'curs' => 1000,
		),		
		'567' => array(
			'title' => 'DOGE - BTC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1000000,
		),
		'568' => array(
			'title' => 'BTC - DOGE',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'569' => array(
			'title' => 'LTC - BTC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'570' => array(
			'title' => 'BTC - LTC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'587' => array(
			'title' => 'LTC - UAH',
			'birg' => __('ExMo','pn'),
			'curs' => 1,
		),
		'588' => array(
			'title' => 'UAH - LTC',
			'birg' => __('ExMo','pn'),
			'curs' => 1000,
		),		
		'571' => array(
			'title' => 'ETH - RUB',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'572' => array(
			'title' => 'RUB - ETH',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1000,
		),
		'573' => array(
			'title' => 'ETH - EUR',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'574' => array(
			'title' => 'EUR - ETH',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		), 
		'575' => array(
			'title' => 'LTC - RUB',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'576' => array(
			'title' => 'RUB - LTC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1000,
		),
		'577' => array(
			'title' => 'DASH - RUB',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'578' => array(
			'title' => 'RUB - DASH',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1000,
		),
		'589' => array(
			'title' => 'DASH - UAH',
			'birg' => __('ExMo','pn'),
			'curs' => 1,
		),
		'590' => array(
			'title' => 'UAH - DASH',
			'birg' => __('ExMo','pn'),
			'curs' => 1000,
		),		
		'579' => array(
			'title' => 'ETH - LTC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'580' => array(
			'title' => 'LTC - ETH',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'581' => array(
			'title' => 'USD - RUB',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'582' => array(
			'title' => 'RUB - USD',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'583' => array(
			'title' => 'WAVES - BTC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1000,
		),
		'584' => array(
			'title' => 'BTC - WAVES',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'591' => array(
			'title' => 'LTC - USD',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),	
		'592' => array(
			'title' => 'USD - LTC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 100,
		),	
		'593' => array(
			'title' => 'LTC - EUR',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),	
		'594' => array(
			'title' => 'EUR - LTC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 100,
		),
		'595' => array(
			'title' => 'ZEC - BTC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 100,
		),	
		'596' => array(
			'title' => 'BTC - ZEC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'597' => array(
			'title' => 'ZEC - USD',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),	
		'598' => array(
			'title' => 'USD - ZEC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'599' => array(
			'title' => 'ZEC - EUR',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),	
		'600' => array(
			'title' => 'EUR - ZEC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),
		'601' => array(
			'title' => 'ZEC - RUB',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1,
		),	
		'602' => array(
			'title' => 'RUB - ZEC',
			'birg' => __('ExMo','pn'),
			'data' => array('low','buy_price','sell_price','last_trade','high','avg'),
			'curs' => 1000,
		),		
	);

	return $parsers;
}

function get_list_parsers($type='all', $format=''){
	if(!$format){ $format = '[para] [[birg]] [[work]]'; }
	$en_types = array('all','work','notwork');
	if(!in_array($type,$en_types)){ $type = 'all'; } 
	$parsers = apply_filters('get_pn_parser', array());
	$work_parser = get_option('work_parser');
	if(!is_array($work_parser)){ $work_parser = array(); }
	$list_parsers = array();
	if(is_array($parsers)){
		foreach($parsers as $key => $data){
			$work = intval(is_isset($work_parser,$key));
			$title = is_isset($data, 'title');
			$birg = is_isset($data, 'birg');
			$enable_title = __('Disable','pn'); 
			if($work){ $enable_title = __('Enable','pn'); }
			$parser_title = '';
			$parser_title = str_replace('[para]',$title,$format);
			$parser_title = str_replace('[birg]',$birg,$parser_title);
			$parser_title = str_replace('[work]',$enable_title,$parser_title);
			if($type == 'all' or $work and $type=='work' or !$work and $type=='notwork'){
				$list_parsers[$birg][] = array(
					'title' => $parser_title,
					'para' => $title,
					'id' => $key,
					'birg' => $birg,
					'work' => $enable_title,
					'enable' => $work,
					'options' => is_isset($data, 'data'),
				);
			}
		}
	}
	$new_list_parsers = array();
	foreach($list_parsers as $k => $array){
		foreach($array as $v){
			$new_list_parsers[$v['id']] = $v;
		}
	}
	
	return $new_list_parsers;
} 

add_action('myaction_request_curscron','my_request_curscron'); 
function my_request_curscron(){
global $premiumbox;	
	
	$premiumbox->up_mode('get');
	
	if(function_exists('parser_upload_data') and check_hash_cron()){
		parser_upload_data();
		_e('Done','pn');
	} else {
		_e('Cron function does not exist','pn');
	}	
}

add_action('pn_adminpage_content_pn_cron','curscron_pn_adminpage_content_pn_cron',9);
add_action('pn_adminpage_content_pn_parser','curscron_pn_adminpage_content_pn_cron',9);
function curscron_pn_adminpage_content_pn_cron(){
?>
	<div class="premium_default_window">
		<?php _e('Cron URL for updating rates of CB and cryptocurrencies','pn'); ?><br /> 
		<a href="<?php echo get_site_url_or(); ?>/request-curscron.html<?php echo get_hash_cron('?'); ?>" target="_blank"><?php echo get_site_url_or(); ?>/request-curscron.html<?php echo get_hash_cron('?'); ?></a>
	</div>	
<?php
}

global $premiumbox;
$premiumbox->include_patch(__FILE__, 'cron');
$premiumbox->include_patch(__FILE__, 'parser');