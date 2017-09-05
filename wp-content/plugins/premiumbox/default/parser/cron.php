<?php
if( !defined( 'ABSPATH')){ exit(); }

function parser_upload_data(){	
global $wpdb;

	$work_parser = get_option('work_parser');
	if(!is_array($work_parser)){ $work_parser = array(); }
	
	$config_parser = get_option('config_parser');
	if(!is_array($config_parser)){ $config_parser = array(); }
	
	$curs_parser = get_option('curs_parser');
	if(!is_array($curs_parser)){ $curs_parser = array(); }
	
	update_option('lcurs_parser', $curs_parser);
	
	$parsers = apply_filters('get_pn_parser', array());
	
	$time_parser = current_time('timestamp');

	/* CBR */
	$time = $time_parser + (24*60*60);
 	$date = date('d.m.Y', $time);
	if(is_isset($work_parser,1) == 1 or is_isset($work_parser,3) == 1 or is_isset($work_parser,5) == 1 or is_isset($work_parser,7) == 1 or is_isset($work_parser,8) == 1 or is_isset($work_parser,10) == 1){

		$curl = get_curl_parser('http://www.cbr.ru/scripts/XML_daily.asp?date_req='.$date, '', 'parser', 'cbr');
		if(is_array($curl) and !$curl['err'] and strstr($curl['output'],'<?xml')){
			$string = $curl['output'];
			$res = @simplexml_load_string($string);
			if(is_object($res)){
				if(isset($res->Valute)){
					$valuts = $res->Valute;
					foreach($valuts as $v_obj){
					
						$CharCode = (string)$v_obj->CharCode;
						$CharCode = trim($CharCode);
					
						if($CharCode == 'USD'){
					
							$on1 = (string)$v_obj->Value;
							$on1 = is_my_money($on1);
							$zn = (string)$v_obj->Nominal;
							
							if($on1 > 0){
								$curs1 = def_parser_curs($parsers, '1', 1);
								$curs_parser[1]['curs1'] = $curs1; // USD
								$curs_parser[1]['curs2'] = is_my_money($on1 / $zn * $curs1); // RUB
								
								$curs1 = def_parser_curs($parsers, '2', 1000);
								$curs_parser[2]['curs1'] = $curs1; // RUB
								$curs_parser[2]['curs2'] = is_my_money($curs1 / $on1 * $zn); // USD							
							}				
						
						}
						
						if($CharCode == 'EUR'){
					
							$on1 = (string)$v_obj->Value;
							$on1 = is_my_money($on1);
							$zn = (string)$v_obj->Nominal;
							
							if($on1 > 0){
								$curs1 = def_parser_curs($parsers, '3', 1);
								$curs_parser[3]['curs1'] = $curs1; // EUR
								$curs_parser[3]['curs2'] = is_my_money($on1 / $zn * $curs1); // RUB
								
								$curs1 = def_parser_curs($parsers, '4', 1000);
								$curs_parser[4]['curs1'] = $curs1; // RUB
								$curs_parser[4]['curs2'] = is_my_money($curs1 / $on1 * $zn); // EUR							
							}				
						
						}

						if($CharCode == 'UAH'){
					
							$on1 = (string)$v_obj->Value;
							$on1 = is_my_money($on1);
							$zn = (string)$v_obj->Nominal;
							
							if($on1 > 0){
								$curs1 = def_parser_curs($parsers, '5', 100);
								$curs_parser[5]['curs1'] = $curs1; // UAH
								$curs_parser[5]['curs2'] = is_my_money($on1 / $zn * $curs1); // RUB
								
								$curs1 = def_parser_curs($parsers, '6', 100);							
								$curs_parser[6]['curs1'] = $curs1; // RUB
								$curs_parser[6]['curs2'] = is_my_money($curs1 / $on1 * $zn); // UAH	
							}				
						
						}

						if($CharCode == 'KZT'){
					
							$on1 = (string)$v_obj->Value;
							$on1 = is_my_money($on1);
							$zn = (string)$v_obj->Nominal;
							
							if($on1 > 0){ 
								$curs1 = def_parser_curs($parsers, '7', 100);
								$curs_parser[7]['curs1'] = $curs1; // KZT
								$curs_parser[7]['curs2'] = is_my_money($on1 / $zn * $curs1); // RUB
							}				
						
						}

						if($CharCode == 'AMD'){
					
							$on1 = (string)$v_obj->Value;
							$on1 = is_my_money($on1);
							$zn = (string)$v_obj->Nominal;
							
							if($on1 > 0){ //1000 => 7480.77441
								$curs1 = def_parser_curs($parsers, '8', 100);							
								$curs_parser[8]['curs1'] = $curs1; // AMD
								$curs_parser[8]['curs2'] = is_my_money($on1 / $zn * $curs1); // RUB
								
								$curs1 = def_parser_curs($parsers, '9', 1000);
								$curs_parser[9]['curs1'] = $curs1; // RUB
								$curs_parser[9]['curs2'] = is_my_money($curs1 / $on1 * $zn); // AMD	
							}				
						
						}

						if($CharCode == 'BYN'){
					
							$on1 = (string)$v_obj->Value;
							$on1 = is_my_money($on1);
							$zn = (string)$v_obj->Nominal;
							
							if($on1 > 0){
								$curs1 = def_parser_curs($parsers, '10', 1);								
								$curs_parser[10]['curs1'] = $curs1; // BYN
								$curs_parser[10]['curs2'] = is_my_money($on1 / $zn * $curs1); // RUB
							}				
						
						}					
					}
				}
			}
		}
	}  
	/* end CBR */
	
	/* ECB */
 	if(is_isset($work_parser,51) == 1 or is_isset($work_parser,52) == 1){
		$curl = get_curl_parser('http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml', '', 'parser', 'ecb');
		if(is_array($curl) and !$curl['err'] and strstr($curl['output'],'<?xml')){		
			$string = $curl['output'];
			$res = @simplexml_load_string($string);
			if(is_object($res) and isset($res->Cube)){
				$on1 = $res->Cube->Cube->Cube[0]['rate'];
				$on1 = (string)$on1;
				$on1 = is_my_money($on1);
				if($on1 > 0){
					
					$curs1 = def_parser_curs($parsers, '51', 1);	
					$curs_parser[51]['curs1'] = $curs1; // EUR
					$curs_parser[51]['curs2'] = is_my_money($on1 * $curs1); // USD

					$curs1 = def_parser_curs($parsers, '52', 1);	
					$curs_parser[52]['curs1'] = $curs1; // USD
					$curs_parser[52]['curs2'] = is_my_money($curs1 / $on1); // EUR				

				}			
			}
		}
	} 
	/* end ECB */
	
	/* privatbank */
  	if(is_isset($work_parser,101) == 1 or is_isset($work_parser,103) == 1){
		$curl = get_curl_parser('https://api.privatbank.ua/p24api/pubinfo?exchange&coursid=3', '', 'parser', 'privatbank');
		if(is_array($curl) and !$curl['err'] and strstr($curl['output'],'<exchangerates')){		
			$string = $curl['output'];
			$res = @simplexml_load_string($string);
			if(is_object($res) and isset($res->row)){
				
				foreach($res->row as $val){
					$v_data = (array)$val->exchangerate;
					$ccy = $v_data['@attributes']['ccy'];
					if($ccy == 'USD'){
						
						$key = trim(is_isset($config_parser,101));
						if($key != 'sale'){ $key='buy'; }
						$usduah = (string)$v_data['@attributes'][$key];
						
						$curs1 = def_parser_curs($parsers, '101', 100);	
						$curs_parser[101]['curs1'] = $curs1; // USD
						$curs_parser[101]['curs2'] = is_my_money($usduah * $curs1); // UAH
				
						$curs1 = def_parser_curs($parsers, '102', 1000);					
						$curs_parser[102]['curs1'] = $curs1; // UAH
						$curs_parser[102]['curs2'] = is_my_money($curs1 / $usduah); // USD							

					} elseif($ccy == 'EUR'){
					
						$key = trim(is_isset($config_parser,103));
						if($key != 'sale'){ $key='buy'; }			
						$euruah = (string)$v_data['@attributes'][$key];
						
						$curs1 = def_parser_curs($parsers, '103', 100);	
						$curs_parser[103]['curs1'] = $curs1; // EUR
						$curs_parser[103]['curs2'] = is_my_money($euruah * $curs1); // UAH
				
						$curs1 = def_parser_curs($parsers, '104', 1000);	
						$curs_parser[104]['curs1'] = $curs1; // UAH
						$curs_parser[104]['curs2'] = is_my_money($curs1 / $euruah); // EUR
					
					}
				}				
										
			}
		}
	} 
  	if(is_isset($work_parser,105) == 1 or is_isset($work_parser,107) == 1){
		$curl = get_curl_parser('https://api.privatbank.ua/p24api/pubinfo?exchange&coursid=5', '', 'parser', 'privatbank');
		if(is_array($curl) and !$curl['err'] and strstr($curl['output'],'<exchangerates')){		
			$string = $curl['output'];
			$res = @simplexml_load_string($string);
			if(is_object($res) and isset($res->row)){
				
				foreach($res->row as $val){
					$v_data = (array)$val->exchangerate;
					$ccy = $v_data['@attributes']['ccy'];
					if($ccy == 'USD'){
						
						$key = trim(is_isset($config_parser,105));
						if($key != 'sale'){ $key='buy'; }
						$usduah = (string)$v_data['@attributes'][$key];
						
						$curs1 = def_parser_curs($parsers, '105', 100);	
						$curs_parser[105]['curs1'] = $curs1; // USD
						$curs_parser[105]['curs2'] = is_my_money($usduah * $curs1); // UAH
				
						$curs1 = def_parser_curs($parsers, '106', 1000);
						$curs_parser[106]['curs1'] = $curs1; // UAH
						$curs_parser[106]['curs2'] = is_my_money($curs1 / $usduah); // USD							

					} elseif($ccy == 'EUR'){
					
						$key = trim(is_isset($config_parser,107));
						if($key != 'sale'){ $key='buy'; }			
						$euruah = (string)$v_data['@attributes'][$key];
						
						$curs1 = def_parser_curs($parsers, '107', 100);
						$curs_parser[107]['curs1'] = $curs1; // EUR
						$curs_parser[107]['curs2'] = is_my_money($euruah * $curs1); // UAH
				
						$curs1 = def_parser_curs($parsers, '108', 1000);
						$curs_parser[108]['curs1'] = $curs1; // UAH
						$curs_parser[108]['curs2'] = is_my_money($curs1 / $euruah); // EUR
					
					}
				}
									
			}
		}
	} 	
	/* end privatbank */

	/* national */
 	if(is_isset($work_parser,151) == 1 or is_isset($work_parser,153) == 1 or is_isset($work_parser,155) == 1){
		$curl = get_curl_parser('http://www.nationalbank.kz/rss/rates_all.xml', '', 'parser', 'nationalbank');
		if(is_array($curl) and !$curl['err'] and strstr($curl['output'],'<?xml')){		
			$string = $curl['output'];
			$res = @simplexml_load_string($string);
			if(is_object($res) and isset($res->channel)){
				foreach($res->channel->item as $data){
				
					$CharCode = $data->title;
				
					if($CharCode == 'USD'){
					
						$on1 = (string)$data->description;
						$on1 = is_my_money($on1);
						if($on1 > 0){
							$curs1 = def_parser_curs($parsers, '151', 1);
							$curs_parser[151]['curs1'] = $curs1; // USD
							$curs_parser[151]['curs2'] = is_my_money($on1 * $curs1); // KZT
								
							$curs1 = def_parser_curs($parsers, '152', 1000);	
							$curs_parser[152]['curs1'] = $curs1; // KZT
							$curs_parser[152]['curs2'] = is_my_money($curs1 / $on1); // USD	
						}				
						
					}

					if($CharCode == 'EUR'){
					
						$on1 = (string)$data->description;
						$on1 = is_my_money($on1);
						if($on1 > 0){
							$curs1 = def_parser_curs($parsers, '153', 1);
							$curs_parser[153]['curs1'] = $curs1; // EUR
							$curs_parser[153]['curs2'] = is_my_money($on1 * $curs1); // KZT
								
							$curs1 = def_parser_curs($parsers, '154', 1000);	
							$curs_parser[154]['curs1'] = $curs1; // KZT
							$curs_parser[154]['curs2'] = is_my_money($curs1 / $on1); // EUR	
						}				
						
					}

					if($CharCode == 'RUB'){
					
						$on1 = (string)$data->description;
						$on1 = is_my_money($on1);
						if($on1 > 0){
							$curs1 = def_parser_curs($parsers, '155', 1);
							$curs_parser[155]['curs1'] = $curs1; // RUB
							$curs_parser[155]['curs2'] = is_my_money($on1 * $curs1); // KZT
								
							$curs1 = def_parser_curs($parsers, '156', 100);	
							$curs_parser[156]['curs1'] = $curs1; // KZT
							$curs_parser[156]['curs2'] = is_my_money($curs1 / $on1); // RUB	
						}				
						
					}				
							
				}
			}
		}
	}	 
	/* end national */

	/* nbrb */
	if(is_isset($work_parser,201) == 1 or is_isset($work_parser,203) == 1 or is_isset($work_parser,205) == 1){
		$date = date('m/d/Y', $time_parser);
		$curl = get_curl_parser('http://www.nbrb.by/Services/XmlExRates.aspx?ondate='.$date, '', 'parser', 'nbrb');
		if(is_array($curl) and !$curl['err'] and strstr($curl['output'],'<?xml')){		
			$string = $curl['output'];
			$res = @simplexml_load_string($string);
			if(is_object($res) and isset($res->Currency)){
				foreach($res->Currency as $data){
				
					$CharCode = $data->CharCode;
				
					if($CharCode == 'USD'){
					
						$on1 = (string)$data->Rate;
						$on1 = is_my_money($on1);
						if($on1 > 0){
							$curs1 = def_parser_curs($parsers, '201', 1);
							$curs_parser[201]['curs1'] = $curs1; // USD
							$curs_parser[201]['curs2'] = is_my_money($on1 * $curs1); // BYN
								
							$curs1 = def_parser_curs($parsers, '202', 10);	
							$curs_parser[202]['curs1'] = $curs1; // BYN
							$curs_parser[202]['curs2'] = is_my_money($curs1 / $on1); // USD	
						}				
						
					}

					if($CharCode == 'EUR'){
					
						$on1 = (string)$data->Rate;
						$on1 = is_my_money($on1);
						if($on1 > 0){
							$curs1 = def_parser_curs($parsers, '203', 1);
							$curs_parser[203]['curs1'] = $curs1; // EUR
							$curs_parser[203]['curs2'] = is_my_money($on1 * $curs1); // BYN
								
							$curs1 = def_parser_curs($parsers, '204', 10);	
							$curs_parser[204]['curs1'] = $curs1; // BYN
							$curs_parser[204]['curs2'] = is_my_money($curs1 / $on1); // EUR	
						}				
						
					}

					if($CharCode == 'RUB'){
					
						$on1 = (string)$data->Rate;
						$on1 = is_my_money($on1);
						if($on1 > 0){ //100 => 3.0398
							
							$curs1 = def_parser_curs($parsers, '205', 100);
							$curs_parser[205]['curs1'] = $curs1; // RUB
							$curs_parser[205]['curs2'] = is_my_money($on1 / 100 * $curs1); // BYN
		
						}				
						
					}				
							
				}
			}
		}
	} 	
	/* end nbrb */
	
	/* yahoo */
	if(
		is_isset($work_parser,400) == 1 or is_isset($work_parser,401) == 1 or is_isset($work_parser,402) == 1 or
		is_isset($work_parser,403) == 1 or is_isset($work_parser,404) == 1 or is_isset($work_parser,405) == 1 or
		is_isset($work_parser,406) == 1 or is_isset($work_parser,407) == 1 or is_isset($work_parser,408) == 1 or
		is_isset($work_parser,409) == 1 or is_isset($work_parser,410) == 1 or is_isset($work_parser,411) == 1 or
		is_isset($work_parser,412) == 1 or is_isset($work_parser,413) == 1 or is_isset($work_parser,414) == 1 or
		is_isset($work_parser,415) == 1 or is_isset($work_parser,416) == 1 or is_isset($work_parser,417) == 1 or
		is_isset($work_parser,418) == 1 or is_isset($work_parser,419) == 1 or is_isset($work_parser,420) == 1 or
		is_isset($work_parser,421) == 1 or is_isset($work_parser,422) == 1 or is_isset($work_parser,423) == 1 or
		is_isset($work_parser,424) == 1 or is_isset($work_parser,425) == 1 
	){
		$curl = get_curl_parser('https://query.yahooapis.com/v1/public/yql?q=select+*+from+yahoo.finance.xchange+where+pair+=+"USDRUB,USDEUR,USDUAH,EURUSD,EURRUB,EURUAH,RUBUSD,RUBEUR,RUBUAH,UAHUSD,UAHEUR,UAHRUB,USDCNY,CNYUSD,UAHCNY,CNYUAH,CNYRUB,RUBCNY,CNYEUR,EURCNY,CNYUAH,UAHCNY,CNYBYN,BYNCNY,CNYKZT,KZTCNY"&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=', '', 'parser', 'yahoo');
		if(is_array($curl) and !$curl['err']){		
			$string = $curl['output'];
			$res = @json_decode($string);
			if(is_object($res) and isset($res->query) and isset($res->query->results->rate)){
				
				$arrs = array(
					'0' => array(
						'v1' => 'USD',
						'v2' => 'RUB',
						'id' => 400,
					),
					'1' => array(
						'v1' => 'USD',
						'v2' => 'EUR',
						'id' => 401,
					),
					'2' => array(
						'v1' => 'USD',
						'v2' => 'UAH',
						'id' => 402,
					),
					'3' => array(
						'v1' => 'EUR',
						'v2' => 'USD',
						'id' => 403,
					),
					'4' => array(
						'v1' => 'EUR',
						'v2' => 'RUB',
						'id' => 404,
					),
					'5' => array(
						'v1' => 'EUR',
						'v2' => 'UAH',
						'id' => 405,
					),
					'6' => array(
						'v1' => 'RUB',
						'v2' => 'USD',
						'id' => 406,
					),
					'7' => array(
						'v1' => 'RUB',
						'v2' => 'EUR',
						'id' => 407,
					),
					'8' => array(
						'v1' => 'RUB',
						'v2' => 'UAH',
						'id' => 408,
					),
					'9' => array(
						'v1' => 'UAH',
						'v2' => 'USD',
						'id' => 409,
					),
					'10' => array(
						'v1' => 'UAH',
						'v2' => 'EUR',
						'id' => 410,
					),
					'11' => array(
						'v1' => 'UAH',
						'v2' => 'RUB',
						'id' => 411,
					),	
					'12' => array(
						'v1' => 'USD',
						'v2' => 'CNY',
						'id' => 412,
					),	
					'13' => array(
						'v1' => 'CNY',
						'v2' => 'USD',
						'id' => 413,
					),	
					'14' => array(
						'v1' => 'UAH',
						'v2' => 'CNY',
						'id' => 414,
					),
					'15' => array(
						'v1' => 'CNY',
						'v2' => 'UAH',
						'id' => 415,
					),
					'16' => array(
						'v1' => 'CNY',
						'v2' => 'RUB',
						'id' => 416,
					),
					'17' => array(
						'v1' => 'RUB',
						'v2' => 'CNY',
						'id' => 417,
					),
					'18' => array(
						'v1' => 'CNY',
						'v2' => 'EUR',
						'id' => 418,
					),
					'19' => array(
						'v1' => 'EUR',
						'v2' => 'CNY',
						'id' => 419,
					),
					'20' => array(
						'v1' => 'CNY',
						'v2' => 'UAH',
						'id' => 420,
					),
					'21' => array(
						'v1' => 'UAH',
						'v2' => 'CNY',
						'id' => 421,
					),
					'22' => array(
						'v1' => 'CNY',
						'v2' => 'BYN',
						'id' => 422,
					),
					'23' => array(
						'v1' => 'BYN',
						'v2' => 'CNY',
						'id' => 423,
					),
					'24' => array(
						'v1' => 'CNY',
						'v2' => 'KZT',
						'id' => 424,
					),
					'25' => array(
						'v1' => 'KZT',
						'v2' => 'CNY',
						'id' => 425,
					),					
				);
				
				foreach($res->query->results->rate as $data){
				
					$Name = explode('/',$data->Name);
					$v1 = trim(is_isset($Name,0));
					$v2 = trim(is_isset($Name,1));
					
					foreach($arrs as $ar_data){
						if($v1 == $ar_data['v1'] and $v2 == $ar_data['v2']){
							$key = trim(is_isset($config_parser, $ar_data['id']));
							if(!$key){ $key='Ask'; }
							
							$on1 = (string)$data->$key;
							$on1 = is_my_money($on1);
							if($on1 > 0){
								$curs1 = def_parser_curs($parsers, $ar_data['id'], 1);
								$curs_parser[$ar_data['id']]['curs1'] = $curs1; 
								$curs_parser[$ar_data['id']]['curs2'] = is_my_money($on1 * $curs1);
							}				
						}							
					}					
				}
			}
		}
	} 	
	/* end yahoo */	
	
	/* wm exchanger */
	$arrs = array(
		array(
			'id' => 251,
			'exchtype' => 2,
		),
		array(
			'id' => 252,
			'exchtype' => 1,
		),
		array(
			'id' => 253,
			'exchtype' => 6,
		),	
		array(
			'id' => 254,
			'exchtype' => 5,
		),
		array(
			'id' => 255,
			'exchtype' => 4,
		),
		array(
			'id' => 256,
			'exchtype' => 3,
		),	
		array(
			'id' => 257,
			'exchtype' => 8,
		),
		array(
			'id' => 258,
			'exchtype' => 7,
		),	
		array(
			'id' => 259,
			'exchtype' => 10,
		),
		array(
			'id' => 260,
			'exchtype' => 9,
		),
		array(
			'id' => 261,
			'exchtype' => 12,
		),
		array(
			'id' => 262,
			'exchtype' => 11,
		),
		array(
			'id' => 263,
			'exchtype' => 26,
		),
		array(
			'id' => 264,
			'exchtype' => 25,
		),
		array(
			'id' => 265,
			'exchtype' => 28,
		),
		array(
			'id' => 266,
			'exchtype' => 27,
		),
		array(
			'id' => 267,
			'exchtype' => 30,
		),
		array(
			'id' => 268,
			'exchtype' => 29,
		),
		array(
			'id' => 269,
			'exchtype' => 32,
		),
		array(
			'id' => 270,
			'exchtype' => 31,
		),
		array(
			'id' => 271,
			'exchtype' => 34,
		),
		array(
			'id' => 272,
			'exchtype' => 33,
		),
		array(
			'id' => 273,
			'exchtype' => 36,
		),
		array(
			'id' => 274,
			'exchtype' => 35,
		),
		array(
			'id' => 275,
			'exchtype' => 38,
		),
		array(
			'id' => 276,
			'exchtype' => 37,
		),
		array(
			'id' => 277,
			'exchtype' => 40,
		),
		array(
			'id' => 278,
			'exchtype' => 39,
		),
		array(
			'id' => 279,
			'exchtype' => 42,
		),
		array(
			'id' => 280,
			'exchtype' => 41,
		),
		array(
			'id' => 281,
			'exchtype' => 44,
		),
		array(
			'id' => 282,
			'exchtype' => 43,
		),
		array(
			'id' => 283,
			'exchtype' => 46,
		),
		array(
			'id' => 284,
			'exchtype' => 45,
		),
		array(
			'id' => 285,
			'exchtype' => 17,
		),
		array(
			'id' => 286,
			'exchtype' => 18,
		),
		array(
			'id' => 287,
			'exchtype' => 19,
		),
		array(
			'id' => 288,
			'exchtype' => 20,
		),
		array(
			'id' => 289,
			'exchtype' => 23,
		),
		array(
			'id' => 290,
			'exchtype' => 24,
		),
		array(
			'id' => 291,
			'exchtype' => 47,
		),
		array(
			'id' => 292,
			'exchtype' => 48,
		),
		array(
			'id' => 293,
			'exchtype' => 49,
		),
		array(
			'id' => 294,
			'exchtype' => 50,
		),
		array(
			'id' => 295,
			'exchtype' => 53,
		),
		array(
			'id' => 296,
			'exchtype' => 54,
		),
		array(
			'id' => 297,
			'exchtype' => 55,
		),
		array(
			'id' => 298,
			'exchtype' => 56,
		),		
	);
	foreach($arrs as $arr){
		if(is_isset($work_parser,$arr['id']) == 1){
			$curl = get_curl_parser('https://wm.exchanger.ru/asp/XMLWMList.asp?exchtype='.$arr['exchtype'], '', 'parser', 'wmexchanger');
			if(is_array($curl) and !$curl['err'] and strstr($curl['output'],'<?xml')){		
				$string = $curl['output'];
				$res = @simplexml_load_string($string);
				if(is_object($res) and isset($res->WMExchnagerQuerys)){	
					$on1 = (string)$res->WMExchnagerQuerys->query['inoutrate'][0];
					$on1 = is_my_money($on1);
					if($on1 > 0){
						$curs1 = def_parser_curs($parsers, $arr['id'], 1);
						$curs_parser[$arr['id']]['curs1'] = $curs1; 
						$curs_parser[$arr['id']]['curs2'] = $on1 * $curs1; 			
					}
				}
			}
		}
	}
	/* end wm exchanger */
	
	/* BitCoin -> USD (BSTAMP) */
 	if(is_isset($work_parser,301) == 1){
		
		$curl = get_curl_parser('https://www.bitstamp.net/api/ticker/', '', 'parser', 'bitstamp');
		if(!$curl['err']){
			$out = @json_decode($curl['output']);
			
			$key = trim(is_isset($config_parser,301));
			if(!$key){ $key='low'; }
			if(is_object($out) and isset($out->$key)){
				$ck = is_my_money($out->$key);
				if($ck){
					$curs1 = def_parser_curs($parsers, '301', 1);
					$curs_parser[301]['curs1'] = $curs1; 
					$curs_parser[301]['curs2'] = $ck * $curs1;	
				}
			}
		}		
	}  
	
/* 	
	$arrs = array(
		array(
			'id1' => 351,
			'id2' => 352,
			'url' => 'https://btc-e.nz/api/2/btc_usd/ticker/'
		),	
		array(
			'id1' => 353,
			'id2' => 354,
			'url' => 'https://btc-e.nz/api/2/btc_rur/ticker'
		),		
		array(
			'id1' => 355,
			'id2' => 356,
			'url' => 'https://btc-e.nz/api/2/btc_eur/ticker'
		),	
		array(
			'id1' => 359,
			'id2' => 360,
			'url' => 'https://btc-e.nz/api/2/ltc_usd/ticker'
		),
		array(
			'id1' => 361,
			'id2' => 362,
			'url' => 'https://btc-e.nz/api/2/ltc_eur/ticker'
		),
		array(
			'id1' => 363,
			'id2' => 364,
			'url' => 'https://btc-e.nz/api/2/ltc_rur/ticker'
		),
		array(
			'id1' => 365,
			'id2' => 366,
			'url' => 'https://btc-e.nz/api/2/ltc_btc/ticker'
		),
		array(
			'id1' => 371,
			'id2' => 372,
			'url' => 'https://btc-e.nz/api/2/dsh_btc/ticker'
		),		
		array(
			'id1' => 373,
			'id2' => 374,
			'url' => 'https://btc-e.nz/api/2/eth_btc/ticker'
		),
		array(
			'id1' => 375,
			'id2' => 376,
			'url' => 'https://btc-e.nz/api/2/eth_usd/ticker'
		),
		array(
			'id1' => 377,
			'id2' => 378,
			'url' => 'https://btc-e.nz/api/2/eth_ltc/ticker'
		),
		array(
			'id1' => 379,
			'id2' => 380,
			'url' => 'https://btc-e.nz/api/2/eth_rur/ticker'
		),
		array(
			'id1' => 381,
			'id2' => 382,
			'url' => 'https://btc-e.nz/api/2/usd_rur/ticker'
		),	
		array(
			'id1' => 383,
			'id2' => 384,
			'url' => 'https://btc-e.nz/api/2/eur_rur/ticker'
		),
		array(
			'id1' => 385,
			'id2' => 386,
			'url' => 'https://btc-e.nz/api/2/eur_usd/ticker'
		),
		array(
			'id1' => 367,
			'url' => 'https://btc-e.nz/api/2/nvc_btc/ticker'
		),
		array(
			'id1' => 368,
			'url' => 'https://btc-e.nz/api/2/nmc_btc/ticker'
		),
		array(
			'id1' => 369,
			'url' => 'https://btc-e.nz/api/2/nvc_usd/ticker'
		),	
		array(
			'id1' => 370,
			'url' => 'https://btc-e.nz/api/2/nmc_usd/ticker'
		),
		array(
			'id1' => 388,
			'id2' => 389,
			'url' => 'https://btc-e.nz/api/2/dsh_usd/ticker'
		),
		array(
			'id1' => 390,
			'id2' => 391,
			'url' => 'https://btc-e.nz/api/2/dsh_rur/ticker'
		),
		array(
			'id1' => 392,
			'id2' => 393,
			'url' => 'https://btc-e.nz/api/2/dsh_eur/ticker'
		),		
	);
	
	
	foreach($arrs as $arr){
		if(isset($arr['id1']) and is_isset($work_parser,$arr['id1']) == 1 or isset($arr['id2']) and is_isset($work_parser,$arr['id2']) == 1){
			$curl = get_curl_parser($arr['url'], '', 'parser', 'btce');
			if(!$curl['err']){
				$out = @json_decode($curl['output']);
				if(is_object($out)){
					
					$key1 = trim(is_isset($config_parser,$arr['id1']));
					if(!$key1){ $key1 = 'last'; }
					
					if(isset($out->ticker)){
						$ck1 = is_my_money($out->ticker->$key1);
						$curs1 = def_parser_curs($parsers, $arr['id1'], 1);
						if($ck1){
							$curs_parser[$arr['id1']]['curs1'] = $curs1;  
							$curs_parser[$arr['id1']]['curs2'] = $ck1 * $curs1;	
						}
						
						if(isset($arr['id2'])){
							$key2 = trim(is_isset($config_parser,$arr['id2']));
							if(!$key2){ $key2 = 'last'; }
							$ck2 = is_my_money($out->ticker->$key2);
							
							$curs2 = def_parser_curs($parsers, $arr['id2'], 1000);
							$ck2 = is_my_money($curs2 / $ck2 * 1);
							if($ck2){
								$curs_parser[$arr['id2']]['curs1'] = $curs2; 
								$curs_parser[$arr['id2']]['curs2'] = $ck2;	
							}
						}
					}
				}
			}		
		} 
	}		 
*/	

	$arrs = array(
		'BTC_USD' => array(
			'id1' => 551,
			'sum1' => 1,
			'id2' => 552,
			'sum2' => 1000,			
		),
		'BTC_EUR' => array(
			'id1' => 553,
			'sum1' => 1,
			'id2' => 554,
			'sum2' => 1000,			
		),
		'BTC_RUB' => array(
			'id1' => 555,
			'sum1' => 1,
			'id2' => 556,
			'sum2' => 10000,			
		),
		'BTC_UAH' => array(
			'id1' => 557,
			'sum1' => 1,
			'id2' => 558,
			'sum2' => 10000,			
		),
		'DASH_BTC' => array(
			'id1' => 559,
			'sum1' => 1,
			'id2' => 560,
			'sum2' => 1,			
		),
		'DASH_USD' => array(
			'id1' => 561,
			'sum1' => 1,
			'id2' => 562,
			'sum2' => 1000,			
		),
		'ETH_BTC' => array(
			'id1' => 563,
			'sum1' => 10,
			'id2' => 564,
			'sum2' => 1,			
		),
		'ETH_USD' => array(
			'id1' => 565,
			'sum1' => 1,
			'id2' => 566,
			'sum2' => 1000,			
		),
		'ETH_UAH' => array(
			'id1' => 585,
			'sum1' => 1,
			'id2' => 586,
			'sum2' => 1000,			
		),		
		'DOGE_BTC' => array(
			'id1' => 567,
			'sum1' => 1000000,
			'id2' => 568,
			'sum2' => 1,			
		),
		'LTC_BTC' => array(
			'id1' => 569,
			'sum1' => 1,
			'id2' => 570,
			'sum2' => 1,			
		),
		'ETH_RUB' => array(
			'id1' => 571,
			'sum1' => 1,
			'id2' => 572,
			'sum2' => 1000,			
		),
		'ETH_EUR' => array(
			'id1' => 573,
			'sum1' => 1,
			'id2' => 574,
			'sum2' => 1,			
		),
		'LTC_RUB' => array(
			'id1' => 575,
			'sum1' => 1,
			'id2' => 576,
			'sum2' => 1000,			
		),
		'DASH_RUB' => array(
			'id1' => 577,
			'sum1' => 1,
			'id2' => 578,
			'sum2' => 1000,			
		),
		'ETH_LTC' => array(
			'id1' => 579,
			'sum1' => 1,
			'id2' => 580,
			'sum2' => 1,			
		),
		'USD_RUB' => array(
			'id1' => 581,
			'sum1' => 1,
			'id2' => 582,
			'sum2' => 1,			
		),
		'WAVES_BTC' => array(
			'id1' => 583,
			'sum1' => 1000,
			'id2' => 584,
			'sum2' => 1,			
		),
		'LTC_USD' => array(
			'id1' => 591,
			'sum1' => 1,
			'id2' => 592,
			'sum2' => 100,			
		),
		'LTC_EUR' => array(
			'id1' => 593,
			'sum1' => 1,
			'id2' => 594,
			'sum2' => 100,			
		),
		'ZEC_BTC' => array(
			'id1' => 595,
			'sum1' => 100,
			'id2' => 596,
			'sum2' => 1,			
		),
		'ZEC_USD' => array(
			'id1' => 597,
			'sum1' => 1,
			'id2' => 598,
			'sum2' => 1,			
		),
		'ZEC_EUR' => array(
			'id1' => 599,
			'sum1' => 1,
			'id2' => 600,
			'sum2' => 1,			
		),
		'ZEC_RUB' => array(
			'id1' => 601,
			'sum1' => 1,
			'id2' => 602,
			'sum2' => 1000,			
		),		
	);

	$curl = get_curl_parser('https://api.exmo.me/v1/ticker/', '', 'parser', 'exmo');
	if(!$curl['err']){
		$outs = @json_decode($curl['output']);
		if(is_object($outs)){
			foreach($arrs as $arr_id => $arr_data){
				if(isset($outs->$arr_id)){
					$item = $outs->$arr_id;
					$id1 = intval(is_isset($arr_data, 'id1'));
					$sum1 = intval(is_isset($arr_data, 'sum1'));
					$id2 = intval(is_isset($arr_data, 'id2'));
					$sum2 = intval(is_isset($arr_data, 'sum2'));					
					
					if(is_isset($work_parser, $id1) == 1){
						$key1 = trim(is_isset($config_parser, $id1));
						if(!$key1){ $key1 = 'low'; }
						$ck1 = is_my_money($item->$key1);
						$curs1 = def_parser_curs($parsers, $id1, $sum1);
						if($ck1){
							$curs_parser[$id1]['curs1'] = $curs1;
							$curs_parser[$id1]['curs2'] = $ck1 * $curs1;
						}
					}
					if(is_isset($work_parser, $id2) == 1){
						$key2 = trim(is_isset($config_parser, $id2));
						if(!$key2){ $key2 = 'low'; }
						$ck2def = is_my_money($item->$key2); 
						$curs2 = def_parser_curs($parsers, $id2, $sum2);
						if($curs2 and $ck2def){
							$ck2 = is_my_money($curs2 / $ck2def);
							$curs_parser[$id2]['curs1'] = $curs2; 
							$curs_parser[$id2]['curs2'] = $ck2;
						}						
					}					
				}	
			}	
		} 
	}		 	
	
	$rubuah = 0;
	if(isset($curs_parser[6]['curs2']) and $curs_parser[6]['curs2'] > 0){
		$def = def_parser_curs($parsers, 6, 100);
		$rubuah = $curs_parser[6]['curs2'] / $def;
	}
	$usduah = 0;
	if(isset($curs_parser[101]['curs2']) and $curs_parser[101]['curs2'] > 0){
		$def = def_parser_curs($parsers, 101, 100);
		$usduah = $curs_parser[101]['curs2'] / $def;
	}	
	if(isset($curs_parser[105]['curs2']) and $curs_parser[105]['curs2'] > 0){
		$def = def_parser_curs($parsers, 105, 100);
		$usduah = $curs_parser[105]['curs2'] / $def;
	}
	if(is_isset($work_parser, 587) == 1){
		if($rubuah > 0 and isset($curs_parser[575]['curs2']) and $curs_parser[575]['curs2'] > 0){
			$def = def_parser_curs($parsers, 575, 1);
			$curs_one = $curs_parser[575]['curs2'] / $def;
			$curs_parser[587]['curs1'] = 1; 
			$curs_parser[587]['curs2'] = is_my_money($curs_one * $rubuah);

			$def = def_parser_curs($parsers, 588, 1000);
			$curs_parser[588]['curs1'] = $def; 
			$curs_parser[588]['curs2'] = is_my_money(1 / $curs_parser[587]['curs2'] * $def);			
		}	
	}
	if(is_isset($work_parser, 589) == 1){
		if($rubuah > 0 and isset($curs_parser[561]['curs2']) and $curs_parser[561]['curs2'] > 0){
			$def = def_parser_curs($parsers, 561, 1);
			$curs_one = $curs_parser[561]['curs2'] / $def;
			$curs_parser[589]['curs1'] = 1; 
			$curs_parser[589]['curs2'] = is_my_money($curs_one * $usduah);	

			$def = def_parser_curs($parsers, 590, 1000);
			$curs_parser[590]['curs1'] = $def; 
			$curs_parser[590]['curs2'] = is_my_money(1 / $curs_parser[589]['curs2'] * $def);			
		}	
	}	
	
	$curs_parser = apply_filters('before_load_curs_parser', $curs_parser, $work_parser, $config_parser, $parsers);
	
	update_option('curs_parser', $curs_parser);

	do_action('load_parser_courses');
	
	update_option('time_parser', $time_parser);
}

add_filter('mycron_1hour', 'mycron_1hour_parser');
function mycron_1hour_parser($filters){
	$filters['parser_upload_data'] = __('Central Bank Rates parser','pn');
	
	return $filters;
}