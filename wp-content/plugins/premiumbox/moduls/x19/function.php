<?php
if( !defined( 'ABSPATH')){ exit(); }

function x19_info_for_wm2($wm){
	$arr = array();
	$arr['err']=0;
	$arr['wmid'] = '';
	
	$options = array(
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => "<request><purse>$wm</purse></request>",
	);
	
	$result = get_curl_parser('https://passport.webmoney.ru/xml/XMLGetWMIDInfo.aspx', $options, 'moduls', 'x19');
	if(!$result['err']){
		$out = $result['output'];
		$res = @simplexml_load_string($out);
		$error = $res->error['id'];
		if(!$error){
			$arr['wmid'] = (string)$res->certinfo['wmid'];
		} else {
			$arr['err']=1;
		}
	} else {
		$arr['err']=1;
	}

	return $arr;
}

function x19_info_for_wm($wm){
	$arr = array();
	$arr['err']=0;
	$arr['wmid'] = '';
	$result = get_curl_parser('https://passport.webmoney.ru/asp/CertView.asp?purse='.$wm, '', 'moduls', 'x19');
	if(!$result['err']){
		$out = $result['output'];
		if(strstr($out, 'Object moved')){
			$arr['err']=1;
		} else {
			$urlwmid = '';
			if(preg_match('/WebMoney.Events" href="(.*?)">/s',$out, $item)){
				$urlwmid = trim($item[1]);
			}
			$wmid = explode('?',$urlwmid);
			$wmid = trim(is_isset($wmid,1));
			if($wmid){
				$arr['wmid'] = $wmid;
			} else {
				$arr['err'] = 1;	
			}
		}
	} else {
		$arr['err']=1;
	}		
	
	return $arr;
}

function x19_phone($tel){
	$tel = str_replace(array('(',')','-',' '),'',$tel);
	if(strstr($tel,'+')){
		return mb_substr($tel,2,mb_strlen($tel));
	} else {
		return $tel;
	}
}