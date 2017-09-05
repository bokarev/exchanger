<?php
if( !defined( 'ABSPATH')){ exit(); }

/* название страны */
function is_country_attr($hash){
	$hash = pn_string($hash);
	if (preg_match("/^[a-zA-z]{2,3}$/", $hash, $matches )) {
		$r = $hash;
	} else {
		$r = 0;
	}
	return $r;
}

function get_country_title($attr){
global $wpdb;
	$attr = is_country_attr($attr);
	if($attr and $attr != 'NaN'){	
		$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."geoip_country WHERE attr='$attr'");
		if(isset($data->title)){
			return pn_strip_input(ctv_ml($data->title));
		} else {
			return __('is not determined','pn');
		}
	} else {
		return __('is not determined','pn');
	}
}

function get_geoip_country(){
	
$country = "
[ru_RU:]Австралия[:ru_RU][en_US:]Australia[:en_US];AU
[ru_RU:]Австрия[:ru_RU][en_US:]Austria[:en_US];AT
[ru_RU:]Азербайджан[:ru_RU][en_US:]Azerbaijan[:en_US];AZ
[ru_RU:]Аландские острова[:ru_RU][en_US:]Aland Islands[:en_US];AX
[ru_RU:]Албания[:ru_RU][en_US:]Albania[:en_US];AL
[ru_RU:]Алжир[:ru_RU][en_US:]Algeria[:en_US];DZ
[ru_RU:]Внешние малые острова (США)[:ru_RU][en_US:]Minor outlying Islands (USA)[:en_US];UM
[ru_RU:]Американские Виргинские острова[:ru_RU][en_US:]U.S. virgin Islands[:en_US];VI
[ru_RU:]Американское Самоа[:ru_RU][en_US:]American Samoa[:en_US];AS
[ru_RU:]Ангилья[:ru_RU][en_US:]Anguilla[:en_US];AI
[ru_RU:]Ангола[:ru_RU][en_US:]Angola[:en_US];AO
[ru_RU:]Андорра[:ru_RU][en_US:]Andorra[:en_US];AD
[ru_RU:]Антарктида[:ru_RU][en_US:]Antarctica[:en_US];AQ
[ru_RU:]Антигуа и Барбуда[:ru_RU][en_US:]Antigua and Barbuda[:en_US];AG
[ru_RU:]Аргентина[:ru_RU][en_US:]Argentina[:en_US];AR
[ru_RU:]Армения[:ru_RU][en_US:]Armenia[:en_US];AM
[ru_RU:]Аруба[:ru_RU][en_US:]Aruba[:en_US];AW
[ru_RU:]Афганистан[:ru_RU][en_US:]Afghanistan[:en_US];AF
[ru_RU:]Багамы[:ru_RU][en_US:]Bahamas[:en_US];BS
[ru_RU:]Бангладеш[:ru_RU][en_US:]Bangladesh[:en_US];BD
[ru_RU:]Барбадос[:ru_RU][en_US:]Barbados[:en_US];BB
[ru_RU:]Бахрейн[:ru_RU][en_US:]Bahrain[:en_US];BH
[ru_RU:]Белиз[:ru_RU][en_US:]Belize[:en_US];BZ
[ru_RU:]Белоруссия[:ru_RU][en_US:]Belarus[:en_US];BY
[ru_RU:]Бельгия[:ru_RU][en_US:]Belgium[:en_US];BE
[ru_RU:]Бенин[:ru_RU][en_US:]Benin[:en_US];BJ
[ru_RU:]Бермуды[:ru_RU][en_US:]Bermuda[:en_US];BM
[ru_RU:]Болгария[:ru_RU][en_US:]Bulgaria[:en_US];BG
[ru_RU:]Боливия[:ru_RU][en_US:]Bolivia[:en_US];BO
[ru_RU:]Босния и Герцеговина[:ru_RU][en_US:]Bosnia and Herzegovina[:en_US];BA
[ru_RU:]Ботсвана[:ru_RU][en_US:]Botswana[:en_US];BW
[ru_RU:]Бразилия[:ru_RU][en_US:]Brazil[:en_US];BR
[ru_RU:]Британская территория в Индийском океане[:ru_RU][en_US:]British Indian ocean territory[:en_US];IO
[ru_RU:]Британские Виргинские острова[:ru_RU][en_US:]British virgin Islands[:en_US];VG
[ru_RU:]Бруней[:ru_RU][en_US:]Brunei[:en_US];BN
[ru_RU:]Буркина Фасо[:ru_RU][en_US:]Burkina Faso[:en_US];BF
[ru_RU:]Бурунди[:ru_RU][en_US:]Burundi[:en_US];BI
[ru_RU:]Бутан[:ru_RU][en_US:]Bhutan[:en_US];BT
[ru_RU:]Вануату[:ru_RU][en_US:]Vanuatu[:en_US];VU
[ru_RU:]Ватикан[:ru_RU][en_US:]The Vatican[:en_US];VA
[ru_RU:]Великобритания[:ru_RU][en_US:]UK[:en_US];GB
[ru_RU:]Венгрия[:ru_RU][en_US:]Hungary[:en_US];HU
[ru_RU:]Венесуэла[:ru_RU][en_US:]Venezuela[:en_US];VE
[ru_RU:]Восточный Тимор[:ru_RU][en_US:]East Timor[:en_US];TL
[ru_RU:]Вьетнам[:ru_RU][en_US:]Vietnam[:en_US];VN
[ru_RU:]Габон[:ru_RU][en_US:]Gabon[:en_US];GA
[ru_RU:]Гаити[:ru_RU][en_US:]Haiti[:en_US];HT
[ru_RU:]Гайана[:ru_RU][en_US:]Guyana[:en_US];GY
[ru_RU:]Гамбия[:ru_RU][en_US:]Gambia[:en_US];GM
[ru_RU:]Гана[:ru_RU][en_US:]Ghana[:en_US];GH
[ru_RU:]Гваделупа[:ru_RU][en_US:]Guadeloupe[:en_US];GP
[ru_RU:]Гватемала[:ru_RU][en_US:]Guatemala[:en_US];GT
[ru_RU:]Гвинея[:ru_RU][en_US:]Guinea[:en_US];GN
[ru_RU:]Гвинея-Бисау[:ru_RU][en_US:]Guinea-Bissau[:en_US];GW
[ru_RU:]Германия[:ru_RU][en_US:]Germany[:en_US];DE
[ru_RU:]Гибралтар[:ru_RU][en_US:]Gibraltar[:en_US];GI
[ru_RU:]Гондурас[:ru_RU][en_US:]Honduras[:en_US];HN
[ru_RU:]Гонконг[:ru_RU][en_US:]Hong Kong[:en_US];HK
[ru_RU:]Гренада[:ru_RU][en_US:]Grenada[:en_US];GD
[ru_RU:]Гренландия[:ru_RU][en_US:]Greenland[:en_US];GL
[ru_RU:]Греция[:ru_RU][en_US:]Greece[:en_US];GR
[ru_RU:]Грузия[:ru_RU][en_US:]Georgia[:en_US];GE
[ru_RU:]Гуам[:ru_RU][en_US:]GUAM[:en_US];GU
[ru_RU:]Дания[:ru_RU][en_US:]Denmark[:en_US];DK
[ru_RU:]ДР Конго[:ru_RU][en_US:]DR Congo[:en_US];CD
[ru_RU:]Джибути[:ru_RU][en_US:]Djibouti[:en_US];DJ
[ru_RU:]Доминика[:ru_RU][en_US:]Dominica[:en_US];DM
[ru_RU:]Доминиканская Республика[:ru_RU][en_US:]Dominican Republic[:en_US];DO
[ru_RU:]Европейский союз[:ru_RU][en_US:]The European Union[:en_US];EU
[ru_RU:]Египет[:ru_RU][en_US:]Egypt[:en_US];EG
[ru_RU:]Замбия[:ru_RU][en_US:]Zambia[:en_US];ZM
[ru_RU:]Западная Сахара[:ru_RU][en_US:]Western Sahara[:en_US];EH
[ru_RU:]Зимбабве[:ru_RU][en_US:]Zimbabwe[:en_US];ZW
[ru_RU:]Израиль[:ru_RU][en_US:]Israel[:en_US];IL
[ru_RU:]Индия[:ru_RU][en_US:]India[:en_US];IN
[ru_RU:]Индонезия[:ru_RU][en_US:]Indonesia[:en_US];ID
[ru_RU:]Иордания[:ru_RU][en_US:]Jordan[:en_US];JO
[ru_RU:]Ирак[:ru_RU][en_US:]Iraq[:en_US];IQ
[ru_RU:]Иран[:ru_RU][en_US:]Iran[:en_US];IR
[ru_RU:]Ирландия[:ru_RU][en_US:]Ireland[:en_US];IE
[ru_RU:]Исландия[:ru_RU][en_US:]Iceland[:en_US];IS
[ru_RU:]Испания[:ru_RU][en_US:]Spain[:en_US];ES
[ru_RU:]Италия[:ru_RU][en_US:]Italy[:en_US];IT
[ru_RU:]Йемен[:ru_RU][en_US:]Yemen[:en_US];YE
[ru_RU:]КНДР[:ru_RU][en_US:]The DPRK[:en_US];KP
[ru_RU:]Кабо-Верде[:ru_RU][en_US:]Cape Verde[:en_US];CV
[ru_RU:]Казахстан[:ru_RU][en_US:]Kazakhstan[:en_US];KZ
[ru_RU:]Каймановы острова[:ru_RU][en_US:]Cayman Islands[:en_US];KY
[ru_RU:]Камбоджа[:ru_RU][en_US:]Cambodia[:en_US];KH
[ru_RU:]Камерун[:ru_RU][en_US:]Cameroon[:en_US];CM
[ru_RU:]Канада[:ru_RU][en_US:]Canada[:en_US];CA
[ru_RU:]Катар[:ru_RU][en_US:]Qatar[:en_US];QA
[ru_RU:]Кения[:ru_RU][en_US:]Kenya[:en_US];KE
[ru_RU:]Кипр[:ru_RU][en_US:]Cyprus[:en_US];CY
[ru_RU:]Киргизия[:ru_RU][en_US:]Kyrgyzstan[:en_US];KG
[ru_RU:]Кирибати[:ru_RU][en_US:]Kiribati[:en_US];KI
[ru_RU:]КНР[:ru_RU][en_US:]China[:en_US];CN
[ru_RU:]Кокосовые острова[:ru_RU][en_US:]Cocos Islands[:en_US];CC
[ru_RU:]Колумбия[:ru_RU][en_US:]Colombia[:en_US];CO
[ru_RU:]Коморы[:ru_RU][en_US:]Comoros[:en_US];KM
[ru_RU:]Коста-Рика[:ru_RU][en_US:]Costa Rica[:en_US];CR
[ru_RU:]Кот-д’Ивуар[:ru_RU][en_US:]Côte d'ivoire[:en_US];CI
[ru_RU:]Куба[:ru_RU][en_US:]Cuba[:en_US];CU
[ru_RU:]Кувейт[:ru_RU][en_US:]Kuwait[:en_US];KW
[ru_RU:]Лаос[:ru_RU][en_US:]Laos[:en_US];LA
[ru_RU:]Латвия[:ru_RU][en_US:]Latvia[:en_US];LV
[ru_RU:]Лесото[:ru_RU][en_US:]Lesotho[:en_US];LS
[ru_RU:]Либерия[:ru_RU][en_US:]Liberia[:en_US];LR
[ru_RU:]Ливан[:ru_RU][en_US:]Lebanon[:en_US];LB
[ru_RU:]Ливия[:ru_RU][en_US:]Libya[:en_US];LY
[ru_RU:]Литва[:ru_RU][en_US:]Lithuania[:en_US];LT
[ru_RU:]Лихтенштейн[:ru_RU][en_US:]Liechtenstein[:en_US];LI
[ru_RU:]Люксембург[:ru_RU][en_US:]Luxembourg[:en_US];LU
[ru_RU:]Маврикий[:ru_RU][en_US:]Mauritius[:en_US];MU
[ru_RU:]Мавритания[:ru_RU][en_US:]Mauritania[:en_US];MR
[ru_RU:]Мадагаскар[:ru_RU][en_US:]Madagascar[:en_US];MG
[ru_RU:]Майотта[:ru_RU][en_US:]Mayotte[:en_US];YT
[ru_RU:]Аомынь[:ru_RU][en_US:]Macau[:en_US];MO
[ru_RU:]Македония[:ru_RU][en_US:]Macedonia[:en_US];MK
[ru_RU:]Малави[:ru_RU][en_US:]Malawi[:en_US];MW
[ru_RU:]Малайзия[:ru_RU][en_US:]Malaysia[:en_US];MY
[ru_RU:]Мали[:ru_RU][en_US:]Mali[:en_US];ML
[ru_RU:]Мальдивы[:ru_RU][en_US:]The Maldives[:en_US];MV
[ru_RU:]Мальта[:ru_RU][en_US:]Malta[:en_US];MT
[ru_RU:]Марокко[:ru_RU][en_US:]Morocco[:en_US];MA
[ru_RU:]Мартиника[:ru_RU][en_US:]Martinique[:en_US];MQ
[ru_RU:]Маршалловы Острова[:ru_RU][en_US:]Marshall Islands[:en_US];MH
[ru_RU:]Мексика[:ru_RU][en_US:]Mexico[:en_US];MX
[ru_RU:]Мозамбик[:ru_RU][en_US:]Mozambique[:en_US];MZ
[ru_RU:]Молдавия[:ru_RU][en_US:]Moldova[:en_US];MD
[ru_RU:]Монако[:ru_RU][en_US:]Monaco[:en_US];MC
[ru_RU:]Монголия[:ru_RU][en_US:]Mongolia[:en_US];MN
[ru_RU:]Монтсеррат[:ru_RU][en_US:]Montserrat[:en_US];MS
[ru_RU:]Мьянма[:ru_RU][en_US:]Myanmar[:en_US];MM
[ru_RU:]Намибия[:ru_RU][en_US:]Namibia[:en_US];NA
[ru_RU:]Науру[:ru_RU][en_US:]Nauru[:en_US];NR
[ru_RU:]Непал[:ru_RU][en_US:]Nepal[:en_US];NP
[ru_RU:]Нигер[:ru_RU][en_US:]Niger[:en_US];NE
[ru_RU:]Нигерия[:ru_RU][en_US:]Nigeria[:en_US];NG
[ru_RU:]Нидерландские Антильские острова[:ru_RU][en_US:]Netherlands Antilles[:en_US];AN
[ru_RU:]Нидерланды[:ru_RU][en_US:]The Netherlands[:en_US];NL
[ru_RU:]Никарагуа[:ru_RU][en_US:]Nicaragua[:en_US];NI
[ru_RU:]Ниуэ[:ru_RU][en_US:]Niue[:en_US];NU
[ru_RU:]Новая Каледония[:ru_RU][en_US:]New Caledonia[:en_US];NC
[ru_RU:]Новая Зеландия[:ru_RU][en_US:]New Zealand[:en_US];NZ
[ru_RU:]Норвегия[:ru_RU][en_US:]Norway[:en_US];NO
[ru_RU:]ОАЭ[:ru_RU][en_US:]UAE[:en_US];AE
[ru_RU:]Оман[:ru_RU][en_US:]Oman[:en_US];OM
[ru_RU:]Остров Рождества[:ru_RU][en_US:]Christmas Island[:en_US];CX
[ru_RU:]Острова Кука[:ru_RU][en_US:]Cook Islands[:en_US];CK
[ru_RU:]Херд и Макдональд[:ru_RU][en_US:]Heard and McDonald[:en_US];HM
[ru_RU:]Пакистан[:ru_RU][en_US:]Pakistan[:en_US];PK
[ru_RU:]Палау[:ru_RU][en_US:]Palau[:en_US];PW
[ru_RU:]Палестина[:ru_RU][en_US:]Palestine[:en_US];PS
[ru_RU:]Панама[:ru_RU][en_US:]Panama[:en_US];PA
[ru_RU:]Папуа — Новая Гвинея[:ru_RU][en_US:]Papua New Guinea[:en_US];PG
[ru_RU:]Парагвай[:ru_RU][en_US:]Paraguay[:en_US];PY
[ru_RU:]Перу[:ru_RU][en_US:]Peru[:en_US];PE
[ru_RU:]Острова Питкэрн[:ru_RU][en_US:]Pitcairn Islands[:en_US];PN
[ru_RU:]Польша[:ru_RU][en_US:]Poland[:en_US];PL
[ru_RU:]Португалия[:ru_RU][en_US:]Portugal[:en_US];PT
[ru_RU:]Пуэрто-Рико[:ru_RU][en_US:]Puerto Rico[:en_US];PR
[ru_RU:]Республика Конго[:ru_RU][en_US:]Republic Of The Congo[:en_US];CG
[ru_RU:]Реюньон[:ru_RU][en_US:]Reunion[:en_US];RE
[ru_RU:]Россия[:ru_RU][en_US:]Russia[:en_US];RU
[ru_RU:]Руанда[:ru_RU][en_US:]Rwanda[:en_US];RW
[ru_RU:]Румыния[:ru_RU][en_US:]Romania[:en_US];RO
[ru_RU:]США[:ru_RU][en_US:]USA[:en_US];US
[ru_RU:]Сальвадор[:ru_RU][en_US:]Salvador[:en_US];SV
[ru_RU:]Самоа[:ru_RU][en_US:]Samoa[:en_US];WS
[ru_RU:]Сан-Марино[:ru_RU][en_US:]San Marino[:en_US];SM
[ru_RU:]Сан-Томе и Принсипи[:ru_RU][en_US:]Sao Tome and Principe[:en_US];ST
[ru_RU:]Саудовская Аравия[:ru_RU][en_US:]Saudi Arabia[:en_US];SA
[ru_RU:]Свазиленд[:ru_RU][en_US:]Swaziland[:en_US];SZ
[ru_RU:]Шпицберген и Ян-Майен[:ru_RU][en_US:]Svalbard and Jan Mayen[:en_US];SJ
[ru_RU:]Северные Марианские острова[:ru_RU][en_US:]Northern Mariana Islands[:en_US];MP
[ru_RU:]Сейшельские Острова[:ru_RU][en_US:]Seychelles[:en_US];SC
[ru_RU:]Сенегал[:ru_RU][en_US:]Senegal[:en_US];SN
[ru_RU:]Сент-Винсент и Гренадины[:ru_RU][en_US:]Saint Vincent and the Grenadines[:en_US];VC
[ru_RU:]Сент-Китс и Невис[:ru_RU][en_US:]Saint Kitts and Nevis[:en_US];KN
[ru_RU:]Сент-Люсия[:ru_RU][en_US:]Saint Lucia[:en_US];LC
[ru_RU:]Сен-Пьер и Микелон[:ru_RU][en_US:]Saint Pierre and Miquelon[:en_US];PM
[ru_RU:]Сербия[:ru_RU][en_US:]Serbia[:en_US];RS
[ru_RU:]Сербия и Черногория (действовал до сентября 2006 года)[:ru_RU][en_US:]Serbia and Montenegro (operated until September 2006)[:en_US];CS
[ru_RU:]Сингапур[:ru_RU][en_US:]Singapore[:en_US];SG
[ru_RU:]Сирия[:ru_RU][en_US:]Syria[:en_US];SY
[ru_RU:]Словакия[:ru_RU][en_US:]Slovakia[:en_US];SK
[ru_RU:]Словения[:ru_RU][en_US:]Slovenia[:en_US];SI
[ru_RU:]Соломоновы Острова[:ru_RU][en_US:]Solomon Islands[:en_US];SB
[ru_RU:]Сомали[:ru_RU][en_US:]Somalia[:en_US];SO
[ru_RU:]Судан[:ru_RU][en_US:]Sudan[:en_US];SD
[ru_RU:]Суринам[:ru_RU][en_US:]Suriname[:en_US];SR
[ru_RU:]Сьерра-Леоне[:ru_RU][en_US:]Sierra Leone[:en_US];SL
[ru_RU:]СССР (действовал до сентября 1992 года)[:ru_RU][en_US:]The USSR was valid until September 1992)[:en_US];SU
[ru_RU:]Таджикистан[:ru_RU][en_US:]Tajikistan[:en_US];TJ
[ru_RU:]Таиланд[:ru_RU][en_US:]Thailand[:en_US];TH
[ru_RU:]Китайская Республика[:ru_RU][en_US:]The Republic Of China[:en_US];TW
[ru_RU:]Танзания[:ru_RU][en_US:]Tanzania[:en_US];TZ
[ru_RU:]Того[:ru_RU][en_US:]In[:en_US];TG
[ru_RU:]Токелау[:ru_RU][en_US:]Tokelau[:en_US];TK
[ru_RU:]Тонга[:ru_RU][en_US:]Tonga[:en_US];TO
[ru_RU:]Тринидад и Тобаго[:ru_RU][en_US:]Trinidad and Tobago[:en_US];TT
[ru_RU:]Тувалу[:ru_RU][en_US:]Tuvalu[:en_US];TV
[ru_RU:]Тунис[:ru_RU][en_US:]Tunisia[:en_US];TN
[ru_RU:]Туркмения[:ru_RU][en_US:]Turkmenistan[:en_US];TM
[ru_RU:]Турция[:ru_RU][en_US:]Turkey[:en_US];TR
[ru_RU:]Уганда[:ru_RU][en_US:]Uganda[:en_US];UG
[ru_RU:]Узбекистан[:ru_RU][en_US:]Uzbekistan[:en_US];UZ
[ru_RU:]Украина[:ru_RU][en_US:]Ukraine[:en_US];UA
[ru_RU:]Уругвай[:ru_RU][en_US:]Uruguay[:en_US];UY
[ru_RU:]Фарерские острова[:ru_RU][en_US:]Faroe Islands[:en_US];FO
[ru_RU:]Микронезия[:ru_RU][en_US:]Micronesia[:en_US];FM
[ru_RU:]Фиджи[:ru_RU][en_US:]Fiji[:en_US];FJ
[ru_RU:]Филиппины[:ru_RU][en_US:]Philippines[:en_US];PH
[ru_RU:]Финляндия[:ru_RU][en_US:]Finland[:en_US];FI
[ru_RU:]Фолклендские острова[:ru_RU][en_US:]Falkland Islands[:en_US];FK
[ru_RU:]Франция[:ru_RU][en_US:]France[:en_US];FR
[ru_RU:]Французская Гвиана[:ru_RU][en_US:]French Guiana[:en_US];GF
[ru_RU:]Французская Полинезия[:ru_RU][en_US:]French Polynesia[:en_US];PF
[ru_RU:]Французские Южные и Антарктические Территории[:ru_RU][en_US:]French Southern and Antarctic lands[:en_US];TF
[ru_RU:]Хорватия[:ru_RU][en_US:]Croatia[:en_US];HR
[ru_RU:]ЦАР[:ru_RU][en_US:]CAR[:en_US];CF
[ru_RU:]Чад[:ru_RU][en_US:]Chad[:en_US];TD
[ru_RU:]Черногория[:ru_RU][en_US:]Montenegro[:en_US];ME
[ru_RU:]Чехия[:ru_RU][en_US:]Czech Republic[:en_US];CZ
[ru_RU:]Чили[:ru_RU][en_US:]Chile[:en_US];CL
[ru_RU:]Швейцария[:ru_RU][en_US:]Switzerland[:en_US];CH
[ru_RU:]Швеция[:ru_RU][en_US:]Sweden[:en_US];SE
[ru_RU:]Шри-Ланка[:ru_RU][en_US:]Sri Lanka[:en_US];LK
[ru_RU:]Эквадор[:ru_RU][en_US:]Ecuador[:en_US];EC
[ru_RU:]Экваториальная Гвинея[:ru_RU][en_US:]Equatorial Guinea[:en_US];GQ
[ru_RU:]Эритрея[:ru_RU][en_US:]Eritrea[:en_US];ER
[ru_RU:]Эстония[:ru_RU][en_US:]Estonia[:en_US];EE
[ru_RU:]Эфиопия[:ru_RU][en_US:]Ethiopia[:en_US];ET
[ru_RU:]ЮАР[:ru_RU][en_US:]South Africa[:en_US];ZA
[ru_RU:]Республика Корея[:ru_RU][en_US:]The Republic Of Korea[:en_US];KR
[ru_RU:]Южная Георгия и Южные Сандвичевы острова[:ru_RU][en_US:]South Georgia and the South sandwich Islands[:en_US];GS
[ru_RU:]Ямайка[:ru_RU][en_US:]Jamaica[:en_US];JM
[ru_RU:]Япония[:ru_RU][en_US:]Japan[:en_US];JP
[ru_RU:]Остров Буве[:ru_RU][en_US:]Bouvet Island[:en_US];BV
[ru_RU:]Остров Норфолк[:ru_RU][en_US:]Norfolk Island[:en_US];NF
[ru_RU:]Остров Святой Елены[:ru_RU][en_US:]St. Helena Island[:en_US];SH
[ru_RU:]Тёркс и Кайкос[:ru_RU][en_US:]Turks and Caicos Islands[:en_US];TC
[ru_RU:]Уоллис и Футуна[:ru_RU][en_US:]Wallis and Futuna[:en_US];WF
";	

	$array = array();	
	$country = explode("\n",$country);
	foreach($country as $cou){
		$data = explode(';',$cou);
		
		$title = trim(is_isset($data,0));
		$attr = trim(is_isset($data,1));
		if($title and $attr){
			
			$array[$attr] = $title;

		}
	}	
	
	asort($array);
	
	return $array;
}

function translate_yandex($word=''){
	if($word){
		$key = 'trnsl.1.1.20150518T052838Z.8fb02647eea4e432.41063bbb2fd720c2d90175e84e298a1247fc9239';
		if( $curl = curl_init() ) {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_URL, 'https://translate.yandex.net/api/v1.5/tr/translate?key='. $key .'&text='.$word.'&lang=ru-en');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_USERAGENT, 'Opera 11.00');
			curl_setopt($curl, CURLOPT_TIMEOUT, 25);
			$err  = curl_errno($curl);
			$out = curl_exec($curl);
			curl_close($curl);
			if(!$err){
				if(is_string($out)){
					if(strstr($out,'<?xml')){
					   $res = @simplexml_load_string($out);
					   return (string)$res->text;
					} else {
						return $word;
					}
				} else {
					return $word;
				}
			} else {
				return $word;
			}
		} else {
			return $word;
		}
	}
}

add_action('init', 'geoip_init', 0);
function geoip_init(){ 
global $wpdb, $user_now_country, $premiumbox;
	$user_now_country = 'NaN';
	if(!is_admin()){
		$notban = 0;
		$agent = is_isset($_SERVER,'HTTP_USER_AGENT');
		if(preg_match("/Google/i", $agent) or preg_match("/Yandex/i", $agent)){
			$notban = 1;
		} 
		
		$ip = pn_real_ip();
		$ccwhite = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."geoip_whiteip WHERE theip='$ip'");
		if($ccwhite > 0){	
			$notban = 1;
		}	
		
		$ccblock = $wpdb->query("SELECT id FROM ". $wpdb->prefix ."geoip_blackip WHERE theip='$ip'");
		if($ccblock > 0 and $notban == 0){
			header('Content-Type: text/html; charset=utf-8');
			
			$temp = '
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" lang="ru-RU">
			<head profile="http://gmpg.org/xfn/11">

			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

			<title>'. __('Your ip is blocked','pn') .'</title>

			<link rel="stylesheet" href="'. $premiumbox->plugin_url .'moduls/geoip/sitestyle.css" type="text/css" media="screen" />

			</head>
			<body>
			<div id="container">


				<div class="title">'. __('Your ip is blocked','pn') .'</div>
				
				<div class="content">
					<div class="text">
						'. __('Access to the website is prohibited','pn') .'
					</div>	
				</div>

			</div>
			</body>
			</html>
			';
			
			echo apply_filters('geoip_blockip_temp', $temp, $ip);
			exit;
		}	
		
		$ip = sprintf('%u', ip2long($ip));
		$data = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."geoip_iplist WHERE before_cip < $ip AND after_cip > $ip");
		if(isset($data->id)){
			$country_attr = $user_now_country = is_country_attr($data->country_attr);
			$cdata = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."geoip_country WHERE attr='$country_attr'");
			if(isset($cdata->id)){	
				if($cdata->status == 0 and $notban == 0){
					
					$temp_id = intval($cdata->temp_id);
					$title = __('Access denied','pn');
					$content = __('Access to website for your country is prohibited','pn');
					$placeinfo = 0;
					$place = '';
					
					if($temp_id > 0){
						$wdata = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."geoip_template WHERE id='$temp_id'");
						if(isset($wdata->id)){
							$title = pn_strip_input($wdata->title);
							$content = pn_strip_text($wdata->content);
						}
					} else {
						$wdata = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."geoip_template WHERE default_temp='1'");
						if(isset($wdata->id)){
							$title = pn_strip_input($wdata->title);
							$content = pn_strip_text($wdata->content);
						}				
					}
					
					header('Content-Type: text/html; charset=utf-8');
					
					$temp ='
					<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
					<html xmlns="http://www.w3.org/1999/xhtml" lang="ru-RU">
					<head profile="http://gmpg.org/xfn/11">

					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

					<title>'. $title .'</title>

					<link rel="stylesheet" href="'. $premiumbox->plugin_url .'moduls/geoip/sitestyle.css" type="text/css" media="screen" />

					</head>
					<body>
					<div id="container">
						<div class="title">'. $title .'</div>
						<div class="content">
							<div class="text">
								'. apply_filters('comment_text', $content) .'
							</div>	
						</div>
					</div>
					</body>
					</html>
					';
					echo apply_filters('geoip_bloccountry_temp',$temp, $title, $content, $cdata);
					exit;
				}
			}
		}
	}	
}