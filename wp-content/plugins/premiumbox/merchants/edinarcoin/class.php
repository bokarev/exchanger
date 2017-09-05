<?php
if(!class_exists('Edinar')){
class Edinar {
	
    private $token = "";
	private $test = 0;

    function __construct($token)
    {
        $this->token = trim($token);
    }	
	
	/*
	Array
	(
		[message] => Invalid token
	)
	*/	
	
	public function add_adress($account, $hook){
		
		$request = $this->request('new-account/'.$this->token , array('account' => $account, 'hook' => $hook), 'post');
		$res = @json_decode($request);
		$res = (array)$res;
		
		$address = '';
		if(isset($res['address'])){
			$address = pn_strip_input($res['address']);
		}
		return $address;
	}

/*
Array
(
    [589c190f6a61d3200e2289d2] => Array
        (
            [date] => 2017-02-09T07:23:59.000Z
            [amount] => 0.004
        )

)
*/	
	public function get_history_address($address){
		
		$request = $this->request('history/'. $this->token .'/'. $address , array(), 'get');
		$res = @json_decode($request);
		$res = (array)$res;
		
		$data = array();
		if(is_array($res)){
			foreach($res as $item){
				$id = pn_strip_input(is_isset($item, 'id'));
				if($id){
					$data[$id] = array(
						'date' => pn_strip_input(is_isset($item, 'date')),
						'amount' => pn_strip_input(is_isset($item, 'amount')),
					);
				}
			}
		}
		
		return $data;
	}	
	
	
	public function get_history(){
		
		$request = $this->request('history/'. $this->token , array(), 'get');
		$res = @json_decode($request);
		$res = (array)$res;
		
		$data = array();
		if(is_array($res)){
			foreach($res as $item){
				$id = pn_strip_input(is_isset($item, 'id'));
				if($id){
					$data[$id] = array(
						'date' => pn_strip_input(is_isset($item, 'date')),
						'amount' => pn_strip_input(is_isset($item, 'amount')),
					);
				}
			}
		}
		
		return $data;
	}	
	
	public function request($key, $params = array(), $method='post'){
		
		$params = (array)$params;
		$key = trim((string)$key);
		
		$url = "https://receive.edinarcoin.com/" . $key;
		$post_fields = http_build_query($params, '', '&');
		
		$c_options = array();
		if($method == 'post'){
			$c_options[CURLOPT_POST] = true;
			$c_options[CURLOPT_POSTFIELDS] = $post_fields;
		}	
		
						
		$c_result = get_curl_parser($url, $c_options, 'merchant', 'edinar');
		$err  = $c_result['err'];
		$out = $c_result['output'];	
		if(!$err){
					
			if($this->test == 1){
				echo $out;
				exit;
			}					
					
			return $out;
					
		} elseif($this->test == 1){
			echo $err;
			exit;
		}		
		
	}
}    
}