<?php
if( !defined( 'ABSPATH')){ exit(); }

if(!function_exists('get_pagenavi_calc')){
	function get_pagenavi_calc($limit,$get,$count){

		$current = intval($get); 
		if ($current) {
			$offset = ($current - 1) * $limit;
		} else {
			$offset = 0; 
			$current = 1;
		}
		
		$disable = $current * $limit; 
		if($count > 0){
			$countpage = ceil($count/$limit);
		} else {
			$countpage = 1;
		}

		if($countpage == $current){
			$next = $current;
		} else {
			$next = $current + 1; 
		}
		
		$prev = $current - 1; 
		if(!$prev){ $prev = 1; }
		if(!$countpage){ $countpage = 1; }

		$pagenavi = array();
		$pagenavi['limit'] = intval($limit);
		$pagenavi['cur'] = intval($current);
		$pagenavi['disable'] = intval($disable);
		$pagenavi['countpage'] = intval($countpage);
		$pagenavi['next'] = intval($next);
		$pagenavi['prev'] = intval($prev);
		$pagenavi['offset'] = intval($offset);

		return $pagenavi;
	}
}

if(!function_exists('get_pagenavi_mini_calc')){
	function get_pagenavi_mini_calc($limit,$get){

		$current = intval($get); 
		if ($current) {
			$offset = ($current - 1) * $limit;
		} else {
			$offset = 0; 
			$current = 1;
		}
		
		$next = $current + 1; 
		$prev = $current - 1; 
		if(!$prev){ $prev = 1; }

		$pagenavi = array();
		$pagenavi['limit'] = intval($limit);
		$pagenavi['cur'] = intval($current);
		$pagenavi['next'] = intval($next);
		$pagenavi['prev'] = intval($prev);
		$pagenavi['offset'] = intval($offset);

		return $pagenavi;
	}
}

if(!function_exists('get_pagenavi_mini')){
	function get_pagenavi_mini($pagenavi='', $url=''){
	global $or_site_url;

		$uri = $url;
		$uri = preg_replace('/[&?]{1}idspage[=]{1}[0-9]{0,8}/','',$uri);
		
		$uri = pn_strip_input($uri);
		
		if(strstr($uri,'?')){
			$zn = '&';
			$explode_uri = explode('?',$uri);
			$uri = $explode_uri[0];
			$query = '?' . $explode_uri[1];
		} else {
			$zn = '?';
			$query = '';
		}
		$idspage = is_isset($pagenavi,'cur');
		$prev = is_isset($pagenavi,'prev');
		$next = is_isset($pagenavi,'next');
		
		$pn = '<div class="pagenavi">';	

		if($idspage > 1){
			$pn .= '<a href="'. $uri . $query .'" data-page="1" class="first">'. __('First page','premium') .'</a>';
			$pn .= '<a href="'. $uri . $query . $zn .'idspage='. $prev .'" data-page="'. $prev .'" class="prev">&larr;</a>';
		}  
		
		$pn .= '<span>'. $idspage .'</span>';
		$pn .= '<a href="'. $uri . $query . $zn .'idspage='. $next .'" data-page="'. $next .'" class="next">&rarr;</a>';
	  
		$pn .= '<div class="clear"></div></div>';
			return $pn;
	}
}

if(!function_exists('get_pagenavi')){
	function get_pagenavi($pagenavi='', $vid='standart', $url=''){
	global $or_site_url;

		if($url){
			$uri = $url;
		} elseif(function_exists('get_site_url_ml')) {
			$uri = get_site_url_ml() . $_SERVER['REQUEST_URI'];
		} else {
			$uri = $or_site_url . $_SERVER['REQUEST_URI'];
		}

		if($vid == 'standart'){
			$uri = preg_replace('/\/page\/[0-9]{0,8}/','',$uri);
		} else {
			$uri = preg_replace('/[&?]{1}idspage[=]{1}[0-9]{0,8}/','',$uri);
		}
		
		$uri = pn_strip_input($uri);
		
		if(strstr($uri,'?')){
			$zn = '&';
			$explode_uri = explode('?',$uri);
			$uri = $explode_uri[0];
			$query = '?' . $explode_uri[1];
		} else {
			$zn = '?';
			$query = '';
		}
		
		if($vid == 'standart'){ 
			$uri = rtrim($uri,'/').'/';
		}
		
		if(is_array($pagenavi)){
			$countpage = $pagenavi['countpage'];
			$idspage = $pagenavi['cur'];
		} else {
			global $wp_query;
			$countpage = $wp_query->max_num_pages;
			
			if($vid == 'standart'){
				$idspage = intval(get_query_var('paged'));
			} else {
				$idspage = intval(is_param_get('idspage'));
			}		
		}	
		
		if(!$idspage){$idspage=1;}
		
		$pn = '';
		
		if($countpage > 1){
			
			$pn .= apply_filters('get_pagenavi_start', '<div class="pagenavi"><div class="pagenavi_ins">');

			if(is_admin()){
				$array = array();
				$array['first'] = __('First page','premium');
				$array['prev'] = '&larr;';
				$array['next'] = '&rarr;';
				$array['last'] = __('Last page','premium');
				$array['num'] = 2;
				$array['numleft'] = 1;
				$array['numright'] = 1;
				$array = apply_filters('get_pagenavi_admin', $array);
			} else {
				$array = array();
				$array['first'] = __('First page','premium');
				$array['prev'] = '&larr;';
				$array['next'] = '&rarr;';
				$array['last'] = __('Last page','premium');
				$array['num'] = 2;
				$array['numleft'] = 2;
				$array['numright'] = 2;
				$array = apply_filters('get_pagenavi', $array);			
			}
		
			$numleft = intval(is_isset($array,'numleft'));
			$numright = intval(is_isset($array,'numright'));
			$num = intval(is_isset($array,'num'));
		
			if($idspage > 1){
				if(isset($array['first']) and $array['first']){
					if($vid == 'standart'){
						$pn .= '<a href="'. $uri . $query . '" data-page="1" class="first">'. $array['first'] .'</a>';
					} else {
						$pn .= '<a href="'. $uri . $query .'" data-page="1" class="first">'. $array['first'] .'</a>';
					}
				}
				if(isset($array['prev']) and $array['prev']){
					if($vid == 'standart'){
						$dpage = $idspage-1;
						$pn .= '<a href="'. $uri .'page/'. $dpage .'/'. $query .'" data-page="'. $dpage .'" class="prev">'. $array['prev'] .'</a>';	
					} else {
						$dpage = $idspage-1;
						$pn .= '<a href="'. $uri . $query . $zn .'idspage='. $dpage .'" data-page="'. $dpage .'" class="prev">'. $array['prev'] .'</a>';
					}   
				}
			}  
	  
			$pagearr = array();
			$r=0;
			while($r++ < $numleft){
				$pagearr[] = $r;
			}
			
			$dc = ($num * 2) + 1;
			$r = $idspage - 1 - $num;
			$mr = $r + $dc;
			while($r++ < $mr){
				$pagearr[] = $r;
			}	
			
			$r = $countpage - $numright;
			while($r++ < $countpage){
				$pagearr[] = $r;
			}

			$pagearr = array_unique($pagearr);
			
			$pa = array();
			$lv = 0;
			foreach($pagearr as $v){
				if($v > 0 and $v <= $countpage){
					
					if($lv and $lv != $v){
						$pn .= '<span>...</span>';
					}
					$lv = $v+1;				
					
					if($v == $idspage){
						$pn .= '<span class="current" data-page="'. $v .'">'. $v .'</span>';
					} elseif($v == 1){
						if($vid == 'standart'){
							$pn .= '<a href="'. $uri . $query . '" data-page="'. $v .'">'. $v .'</a>';
						} else {
							$pn .= '<a href="'. $uri . $query .'" data-page="'. $v .'">'. $v .'</a>';
						}					
					} else {
						if($vid == 'standart'){
							$pn .= '<a href="'. $uri .'page/'. $v .'/'. $query .'" data-page="'. $v .'">'. $v .'</a>';	
						} else {
							$pn .= '<a href="'. $uri . $query . $zn .'idspage='. $v .'" data-page="'. $v .'">'. $v .'</a>';
						}					
					}
				}
			}

			if($idspage != $countpage){
				if(isset($array['next']) and $array['next']){
					$dpage = $idspage+1;
					if($vid == 'standart'){
						$pn .= '<a href="'. $uri .'page/'. $dpage .'/'. $query .'" data-page="'. $dpage .'" class="next">'. $array['next'] .'</a>';	
					} else {
						$pn .= '<a href="'. $uri . $query . $zn .'idspage='. $dpage .'" data-page="'. $dpage .'" class="next">'. $array['next'] .'</a>';
					} 				
				}
				if(isset($array['last']) and $array['last']){
					if($vid == 'standart'){
						$pn .= '<a href="'. $uri .'page/'. $countpage .'/'. $query .'" data-page="'. $countpage .'" class="last">'. $array['last'] .'</a>';	
					} else {
						$pn .= '<a href="'. $uri . $query . $zn .'idspage='. $countpage .'" data-page="'. $countpage .'" class="last">'. $array['last'] .'</a>';
					} 				
				}						
			} 
	  
			$pn .= apply_filters('get_pagenavi_end', '<div class="clear"></div></div></div>');
		}
			return $pn;
	}
}

if(!function_exists('the_pagenavi')){
	function the_pagenavi($pagenavi='', $vid='standart', $url=''){
		echo get_pagenavi($pagenavi, $vid, $url);
	}
}