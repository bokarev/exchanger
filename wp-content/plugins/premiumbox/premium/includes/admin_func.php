<?php
/* защита от прямого обращения */
if( !defined( 'ABSPATH')){ exit(); }

/*
Универсальные административные функции
*/
if(!function_exists('pn_get_current_screen')){
	function pn_get_current_screen(){
		$screen = get_current_screen();
		$screen_id = $screen->id;
		if(strstr($screen_id, 'page_')){
			$screen_arr = explode('page_',$screen_id);
			$screen_id = is_isset($screen_arr,1);
		}
		return $screen_id;
	}
}

if(!function_exists('pn_set_screen_option')){
	add_filter('set-screen-option', 'pn_set_screen_option', 10, 3);
	function pn_set_screen_option($status, $option, $value) {
		return $value;
	}
}

/* хук для верхнего меню */
if(!function_exists('pn_trev_hook')){
	function pn_trev_hook(){
		$page = pn_strip_input(is_param_get('page'));
		if(has_filter('premium_screen_' . $page)){
			do_action('premium_screen_' . $page);
		}
	}
}

if(!function_exists('pn_only_caps')){
	function pn_only_caps($caps){
		$caps = (array)$caps;
		$dopusk = 0;
		foreach($caps as $cap){
			if(current_user_can($cap)){
				$dopusk = 1;
				break;
			}
		}
		if(!$dopusk){
			pn_display_mess(__('Error! insufficient privileges!','premium'));
		}
	}
}

/*
Стили и скрипты админки
*/
if(!function_exists('pn_admin_head')){
	add_action('admin_head', 'pn_admin_head');
	function pn_admin_head(){
		$screen_id = pn_get_current_screen();
		
		if(has_filter('pn_adminpage_style_' . $screen_id) or has_filter('pn_adminpage_style')){
			?>
			<style>
				<?php
					do_action('pn_adminpage_style_' . $screen_id);
					do_action('pn_adminpage_style');
				?>
			</style>
			<?php
		}
		
		if(has_filter('pn_adminpage_js_' . $screen_id) or has_filter('pn_adminpage_js')){
			?>
			<script type="text/javascript">
				jQuery(function($){
				<?php 
					do_action('pn_adminpage_js_' . $screen_id); 
					do_action('pn_adminpage_js'); 
				?>
				});	
			</script>	
			<?php
		}			
	}
}

if(!function_exists('pn_admin_init')){
	add_action('admin_enqueue_scripts', 'pn_admin_init',0);
	function pn_admin_init(){	
		global $or_site_url;
		
		$pn_version = get_premium_version();
		$plugin_url = get_premium_url();
		
		wp_enqueue_style('roboto-sans', is_ssl_url("https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i&subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese"), false, "1.0");
		
		wp_enqueue_style('premium ui-style', $plugin_url . "js/jquery-ui/style.css", false, "1.11.4");
		wp_enqueue_style('premium style', $plugin_url . "style.css", false, $pn_version);		
		
		$screen_id = pn_get_current_screen();
		if($screen_id != 'nav-menus'){
			if(has_filter('pn_adminpage_quicktags_' . $screen_id) or has_filter('pn_adminpage_quicktags')){
				wp_enqueue_script('premium other quicktags', $or_site_url . "/premium_quicktags.js?place=". $screen_id, array('quicktags'), $pn_version);
			}	
		}
	} 
}

if(!function_exists('pn_site_init')){
	add_action('wp_enqueue_scripts', 'pn_site_init',99);
	function pn_site_init(){
		global $or_site_url;
		
		$pn_version = get_premium_version();
		wp_enqueue_script('jquery premium-js', $or_site_url . '/premiumjs.js?lang='. get_lang_key(get_locale()), false, $pn_version);
	}
}

if(!function_exists('jserror_js_error_response')){
	add_action('pn_js_error_response', 'jserror_js_error_response');
	function jserror_js_error_response($type){ 
	?>
		console.log('<?php _e('Error text','premium'); ?>, text1: ' + res2 + ',text2:' + res3);
		for (key in res) {
			console.log(key + ' = ' + res[key]);
		}
	<?php
	}
} 

if(!function_exists('jserror_js_alert_response')){
	add_action('pn_js_alert_response', 'jserror_js_alert_response');
	function jserror_js_alert_response(){
	?>
		if(res['status_text']){
			alert(res['status_text']);
		}
	<?php
	}
}

/*
окошка select для админки, с возможностью выбора цвета
*/
if(!function_exists('pn_admin_select_box')){
	function pn_admin_select_box($place, $selects, $title=''){
		?>
		<div class="premium_default_window">
			<?php echo $title; ?> &rarr;
					
			<select name="" onchange="location = this.options[this.selectedIndex].value;" autocomplete="off">
				<?php 
				foreach($selects as $item){ 
					$style = '';
					$background = is_isset($item,'background');
					if($background){
						$style = 'background: '. $background .';';
					}
				?>
					<option value="<?php echo is_isset($item,'link');?>" <?php selected(is_isset($item,'default'), $place);?> style="<?php echo $style; ?>"><?php echo is_isset($item,'title');?></option>
				<?php } ?>
			</select>				
		</div>			
		<?php
	} 
} 

/* собираем данные в массив */
if(!function_exists('pn_admin_prepare_lost')){
	function pn_admin_prepare_lost($lost){
		$losted = array();
		if(is_array($lost)){
			$losted = $lost;
		} elseif(is_string($lost)) {
			$l = explode(',',$lost);
			foreach($l as $lk => $lv){
				$lv = trim($lv);
				if($lv){
					$losted[$lk] = $lv;
				}
			}		
		}
		return $losted;
	}
}
 
if(!function_exists('pn_admin_search_where')){
	function pn_admin_search_where($where){
		$page = pn_strip_input(is_param_get('page'));
		return apply_filters('pn_admin_searchwhere_'.$page, $where);
	}
}

if(!function_exists('pn_admin_back_menu')){
	function pn_admin_back_menu($back_menu, $data){
		$page = pn_strip_input(is_param_get('page'));
		$back_menu = apply_filters('pn_admin_back_menu_'.$page, $back_menu, $data);
		$back_menu = (array)$back_menu;
		?>
		<div style="margin: 0 0 10px 0;">
			<?php foreach($back_menu as $item){ 
				$target_html = '';
				$target = intval(is_isset($item, 'target'));
				if($target){
					$target_html = 'target="_blank"';
				}
			?>
				<a href="<?php echo is_isset($item,'link');?>" <?php echo $target_html; ?> class="button"><?php echo is_isset($item,'title');?></a>
			<?php } ?>
				<div class="premium_clear"></div>
		</div>	
		<?php
	}
} 

if(!function_exists('pn_admin_substrate')){
	function pn_admin_substrate($text=''){
		?>
		<div class="premium_default_window">
			<?php echo $text; ?>
		</div>			
		<?php
	} 
}  

if(!function_exists('pn_admin_searchbox')){
	function pn_admin_searchbox($search, $lost=''){
		$page = pn_strip_input(is_param_get('page'));
		$works = pn_admin_prepare_lost($lost); 
		$search = apply_filters('pn_admin_searchbox_'.$page, $search);
		if(is_array($search) and count($search) > 0){
			$has_filter = 0;
			$now_url = is_isset($_SERVER,'REQUEST_URI');
			$now_url = str_replace('/wp-admin/','', $now_url);
			$now_url = explode('?',$now_url);
			$now_url = $now_url[0];
			
			foreach($search as $item){
				$name = trim(is_isset($item, 'name'));
				if($name){
					$works[] = $name;
				}
			}
		?>
		<div class="premium_search">
			<form action="" method="get">
				
				<?php 
				if(isset($_GET) and is_array($_GET)){
					foreach($_GET as $key => $val){
						if(!in_array($key, $works)){
				?>
					<input type="hidden" name="<?php echo $key; ?>" value="<?php echo pn_strip_input($val); ?>" />
				<?php 
						}
					}
				} ?>
				
				<?php
				foreach($search as $option){
					$view = trim(is_isset($option,'view'));
					$title = trim(is_isset($option,'title'));
					$name = trim(is_isset($option,'name'));
					$default = pn_strip_input(is_isset($option,'default'));
					if(strlen($default) > 0){
						$has_filter = 1;
					}				
					if($view == 'input'){
						?>
						<div class="premium_search_div">
							<div class="premium_search_label"><?php echo $title; ?></div>
							<input type="search" name="<?php echo $name; ?>" value="<?php echo $default; ?>" />
						</div>
						<?php
					} elseif($view == 'date'){
						?>
						<div class="premium_search_div">
							<div class="premium_search_label"><?php echo $title; ?></div>
							<input type="search" name="<?php echo $name; ?>" class="pn_datepicker" value="<?php echo $default; ?>" />
						</div>
						<?php
					} elseif($view == 'datetime'){
						?>
						<div class="premium_search_div">
							<div class="premium_search_label"><?php echo $title; ?></div>
							<input type="search" name="<?php echo $name; ?>" class="pn_timepicker" value="<?php echo $default; ?>" />
						</div>
						<?php	
					} elseif($view == 'select'){
						$options = is_isset($option,'options');
						?>
						<div class="premium_search_div">
							<div class="premium_search_label"><?php echo $title; ?></div>
							<select name="<?php echo $name; ?>" style="position: relative; top: -1px;" autocomplete="off">
								<?php foreach($options as $key => $title){ ?>
									<option value="<?php echo $key; ?>" <?php selected($key, $default); ?>><?php echo $title; ?></option>
								<?php } ?>
							</select>
						</div>
						<?php					
					} elseif($view == 'line'){
						?>
						<div class="premium_clear"></div>	
						<div class="premium_search_line"></div>
						<?php
					}
				}
				?>
				
				<div class="premium_search_div">
					<div class="premium_search_label"></div>
					<input type="submit" style="float: left; margin-right: 5px;" name="" class="button" value="<?php _e('Filter','premium'); ?>"  />
					<?php if($has_filter){ ?>
						<a href="<?php echo admin_url($now_url.'?page='.$page);?>" class="button"><?php _e('Cancel','premium'); ?></a>
					<?php } ?>
				</div>
					<div class="premium_clear"></div>
			</form>
		</div><div class="premium_clear"></div>	
		<?php
		}
	} 
} 
 
if(!function_exists('pn_admin_one_screen')){
	function pn_admin_one_screen($filter, $options, $data='', $link=''){
		$link = trim($link);
		if(!$link){ $link = pn_link_ajax(); }
		$filter = trim($filter);
		if($filter){
			$options = apply_filters($filter, $options, $data);
		}
		?>
		<form method="post" action="<?php echo $link; ?>">
			<?php wp_referer_field(); ?>
					
			<div class="premium_body">
				<table class="premium_standart_table">
					<?php 
					$template = array(
						'before' => '<tr class="[class]">',
						'after' => '</tr>',
						'before_title' => '<th>',
						'after_title' => '</th>',
						'before_content' => '<td>',
						'after_content' => '</td>',
						'label' => 1,
					);
					pn_admin_work_options($options, $template); 
					?>
				</table>				
			</div>
		</form>
		<?php
	} 
}

if(!function_exists('pn_admin_work_options')){
	function pn_admin_work_options($options, $template){
		$options = (array)$options;
		foreach($options as $option){
			$view = trim(is_isset($option,'view'));
			$title = trim(is_isset($option,'title'));
			$name = trim(is_isset($option,'name'));
			$default = is_isset($option,'default');
			$class = trim(is_isset($option,'class'));
			$ml = intval(is_isset($option,'ml'));
			if($view == 'h3'){
				$submit = trim(is_isset($option,'submit'));
				$colspan = trim(is_isset($option,'colspan'));
				pn_h3($title, $submit, $colspan);
			} elseif($view == 'clear_table'){	
				?>
					</table>				
				</div>
				<div class="premium_body">
					<table class="premium_standart_table">			
				<?php
			} elseif($view == 'select'){
				$sel_options = is_isset($option,'options');
				pn_select($title, $name, $sel_options, $default, $class, $template);
			} elseif($view == 'select_disabled'){
				$sel_options = is_isset($option,'options');
				pn_select_disabled($title, $name, $sel_options, $default, $class, $template);			
			} elseif($view == 'checkbox'){
				$second_title = is_isset($option,'second_title');
				$value = is_isset($option,'value');
				pn_checkbox($title, $name, $second_title, $value, $default, $class, $template);		
			} elseif($view == 'help'){	
				pn_help($title, $default, $class, $template);
			} elseif($view == 'warning'){	
				pn_warning($default, $class, $template);
			} elseif($view == 'textfield'){
				pn_textfield($title, $default, $class, $template);			
			} elseif($view == 'line'){
				$colspan = trim(is_isset($option,'colspan'));
				pn_line($colspan);
			} elseif($view == 'hidden_input'){
				pn_hidden_input($name, $default);					
			} elseif($view == 'input'){
				$not_auto = trim(is_isset($option,'not_auto'));
				pn_input($title, $name, $default, $class, $not_auto, $template);
			} elseif($view == 'inputbig' and $ml){
				$not_auto = trim(is_isset($option,'not_auto'));
				pn_inputbig_ml($title, $name, $default, $class, $not_auto, $template);				
			} elseif($view == 'inputbig'){
				$not_auto = trim(is_isset($option,'not_auto'));
				pn_inputbig($title, $name, $default, $class, $not_auto, $template);			
			} elseif($view == 'textarea' and $ml){
				$width = trim(is_isset($option,'width'));
				$height = trim(is_isset($option,'height'));
				pn_textarea_ml($title, $name, $default, $width, $height, $class, $template);				
			} elseif($view == 'textarea'){
				$width = trim(is_isset($option,'width'));
				$height = trim(is_isset($option,'height'));			
				pn_textarea($title, $name, $default, $width, $height, $class, $template);		
			} elseif($view == 'datetime'){
				pn_datetime($title, $name, $default, $class, $template);
			} elseif($view == 'date'){
				pn_date($title, $name, $default, $class, $template);			
			} elseif($view == 'editor' and $ml){
				$rows = trim(is_isset($option,'rows'));
				$media = is_isset($option,'media');
				pn_editor_ml($title, $name, $default, $rows, $media, $class, $template);				
			} elseif($view == 'editor'){
				$rows = trim(is_isset($option,'rows'));
				$media = is_isset($option,'media');
				pn_editor($title, $name, $default, $rows, $media, $class, $template);
			} elseif($view == 'uploader' and $ml){
				pn_uploader_ml($title, $name, $default, $class, $template);				
			} elseif($view == 'uploader'){
				pn_uploader($title, $name, $default, $class, $template);			
			} elseif($view == 'textareatags' and $ml){
				$tags = is_isset($option,'tags');
				$width = trim(is_isset($option,'width'));
				$height = trim(is_isset($option,'height'));
				$prefix1 = trim(is_isset($option,'prefix1'));
				$prefix2 = trim(is_isset($option,'prefix2'));
				pn_textareaico_ml($title, $name, $default, $tags, $prefix1, $prefix2, $width, $height, $class, $template);					
			} elseif($view == 'textareatags'){
				$tags = is_isset($option,'tags');
				$width = trim(is_isset($option,'width'));
				$height = trim(is_isset($option,'height'));
				$prefix1 = trim(is_isset($option,'prefix1'));
				$prefix2 = trim(is_isset($option,'prefix2'));
				pn_textareaico($title, $name, $default, $tags, $prefix1, $prefix2, $width, $height, $class, $template);
			} elseif($view == 'user_func'){
				$func = trim(is_isset($option,'func'));
				$func_data = is_isset($option,'func_data');
				if($func){
					call_user_func($func, $func_data);
				}
			}
		}	
	}
}

if(!function_exists('pn_set_option_template')){
	function pn_set_option_template($data){
		$array = array();
		$template = array(
			'before' => '',
			'after' => '',
			'before_title' => '',
			'after_title' => '',
			'before_content' => '',
			'after_content' => '',
			'label' => '',
		);		
		foreach($template as $key => $val){
			$array[$key] = trim(is_isset($data, $key));
		}
			return $array;
	}
} 

if(!function_exists('pn_h3')){
	function pn_h3($title=false, $submit=false, $colspan=2){			
		$temp = '<tr><td colspan="'. $colspan .'">';			
		$temp .= '<div class="premium_h3">'. $title .'</div>';
		if($submit){
			$temp .= '<div class="premium_h3submit"><input type="submit" formtarget="_top" name="" class="button" value="'. pn_strip_input($submit) .'" /></div>';
		}			
		$temp .= '<div class="premium_clear"></div></td></tr>';			
		echo $temp;
	}
} 

if(!function_exists('pn_date')){
	function pn_date($title, $name='', $default='', $class='', $template=''){			
		if($default){
			$dforv = get_mydate($default,'d.m.Y');
		} else {
			$dforv = date('d.m.Y',current_time('timestamp'));
		}
		$temp = '';
		$template = pn_set_option_template($template);			
		$temp .= $template['before'];
		$temp .= $template['before_title'];	
		if($template['label']){	
			$temp .= '<label for="pn_'. $name .'">'. $title .'</label>';
		}	
		$temp .= $template['after_title'];
		$temp .= $template['before_content'];			
		$temp .= '<div class="premium_wrap_standart">';
		$temp .= '<input type="text" name="'. $name .'" id="pn_'. $name .'" class="pn_datepicker premium_input big_input" value="'. pn_strip_input($dforv) .'" />';
		$temp .= '</div>';			
		$temp .= $template['after_content'];
		$temp .= $template['after'];
		$temp = str_replace("[class]", $class, $temp);
		echo $temp;	
	}
}

if(!function_exists('pn_datetime')){
	function pn_datetime($title, $name='', $default='', $class='', $template=''){			
		if($default){
			$dforv = get_mytime($default,'d.m.Y H:i');
		} else {
			$dforv = date('d.m.Y H:i',current_time('timestamp'));
		}
		$temp = '';
		$template = pn_set_option_template($template);			
		$temp .= $template['before'];
		$temp .= $template['before_title'];	
		if($template['label']){	
			$temp .= '<label for="pn_'. $name .'">'. $title .'</label>';
		}	
		$temp .= $template['after_title'];
		$temp .= $template['before_content'];			
		$temp .= '<div class="premium_wrap_standart">';
		$temp .= '<input type="text" name="'. $name .'" id="pn_'. $name .'" class="pn_timepicker premium_input big_input" value="'. pn_strip_input($dforv) .'" />';
		$temp .= '</div>';			
		$temp .= $template['after_content'];
		$temp .= $template['after'];
		$temp = str_replace("[class]", $class, $temp);
		echo $temp;	
	}
}

if(!function_exists('pn_textfield')){
	function pn_textfield($title, $content='', $class='', $template=''){
		$temp = '';
		$template = pn_set_option_template($template);			
		$temp .= $template['before'];
		$temp .= $template['before_title'];	
		if($template['label']){
			$temp .= '<label>'. $title .'</label>';
		}	
		$temp .= $template['after_title'];
		$temp .= $template['before_content'];			
		$temp .= '
		<div class="premium_wrap_standart">'. $content .'</div>
		';			
		$temp .= $template['after_content'];
		$temp .= $template['after'];
		$temp = str_replace("[class]", $class, $temp);
		echo $temp;	
	}
}

if(!function_exists('pn_checkbox')){
	function pn_checkbox($title, $name='', $second_title='', $value='', $default='', $class='', $template=''){
		$temp = '';
		$template = pn_set_option_template($template);			
		$temp .= $template['before'];
		$temp .= $template['before_title'];	
		if($template['label']){
			$temp .= '<label for="pn_'. $name .'">'. $title .'</label>';
		}	
		$temp .= $template['after_title'];
		$temp .= $template['before_content'];			
		$temp .= '<div class="premium_wrap_standart">';
		
		$checked = '';
		if(is_array($default)){
			if(in_array($value, $default)){
				$checked = 'checked="checked"';
			}		
		} else {
			if($default == $value){
				$checked = 'checked="checked"';
			}		
		}
		
		$temp .= '<label><input type="checkbox" name="'. $name .'" id="pn_'. $name .'" '. $checked .' value="'. $value .'" />'. $second_title .'</label>';
			
		$temp .= '</div>';			
		$temp .= $template['after_content'];
		$temp .= $template['after'];
		$temp = str_replace("[class]", $class, $temp);
		echo $temp;	
	} 
}

if(!function_exists('pn_line')){
	function pn_line($colspan=2){
		$temp = '';
		$temp .= '<tr><td colspan="'. $colspan .'"><div class="premium_line"></div></td></tr>';
		echo $temp;
	}
}

if(!function_exists('pn_hidden_input')){
	function pn_hidden_input($name='', $default=''){
		$temp = '<input type="hidden" name="'. $name .'" value="'. pn_strip_input($default) .'" />';
		echo $temp;
	}	
}	

if(!function_exists('pn_select')){
	function pn_select($title, $name='', $options=array(), $default='', $class='', $template=''){
		$options = (array)$options;
		$temp = '';
		$template = pn_set_option_template($template);			
		$temp .= $template['before'];
		$temp .= $template['before_title'];	
		if($template['label']){
			$temp .= '<label for="pn_'. $name .'">'. $title .'</label>';
		}	
		$temp .= $template['after_title'];
		$temp .= $template['before_content'];			
		$temp .= '<div class="premium_wrap_standart"><select name="'. $name .'" id="pn_'. $name .'" autocomplete="off">';
			foreach($options as $k => $v){
				$temp .= '<option value="'. $k .'" '. selected($default,$k, false) .'>'. pn_strip_input($v) .'</option>';
			}
		$temp .= '</select></div>';			
		$temp .= $template['after_content'];
		$temp .= $template['after'];
		$temp = str_replace("[class]", $class, $temp);
		echo $temp;
	}
}

if(!function_exists('pn_select_disabled')){
	function pn_select_disabled($title, $name='', $options=array(), $default='', $class='', $template=''){
		$options = (array)$options;
		$temp = '';
		$template = pn_set_option_template($template);			
		$temp .= $template['before'];
		$temp .= $template['before_title'];	
		if($template['label']){
			$temp .= '<label for="pn_'. $name .'">'. $title .'</label>';
		}	
		$temp .= $template['after_title'];
		$temp .= $template['before_content'];			
		$temp .= '<div class="premium_wrap_standart"><select name="'. $name .'" id="pn_'. $name .'" disabled="disabled" autocomplete="off">';
			foreach($options as $k => $v){
				$temp .= '<option value="'. $k .'" '. selected($default,$k, false) .'>'. pn_strip_input($v) .'</option>';
			}
		$temp .= '</select></div>';			
		$temp .= $template['after_content'];
		$temp .= $template['after'];
		$temp = str_replace("[class]", $class, $temp);
		echo $temp;
	}
}

if(!function_exists('pn_inputbig_ml')){
	function pn_inputbig_ml($title='', $name='', $default='', $class='', $not_auto=0, $template=''){
		$not_auto = intval($not_auto);
		$au_c = '';
		if($not_auto){ 
			$au_c = 'autocomplete="off"'; 
		}
		$temp = '';
		$template = pn_set_option_template($template);			
		$temp .= $template['before'];
		$temp .= $template['before_title'];	
		if($template['label']){		
			$temp .= '<label>'. $title .'</label>';	
		}	
		$temp .= $template['after_title'];
		$temp .= $template['before_content'];
		if(is_ml()){	
			$langs = get_langs_ml();
			$admin_lang = get_admin_lang();
			$temp .= '
			<div class="multi_wrapper">
				<div class="premium_title_multi">
			';
				foreach($langs as $key){ 
					$cl = '';
					if($key == $admin_lang){ $cl = 'active'; }
					$temp .= '
					<div name="tab_'. $name .'_'. $key .'" class="tab_multi_title '. $cl .'">
						<div class="tab_multi_flag"><img src="'. get_lang_icon($key) .'" alt="" /></div>
						<span class="tab_multi_flag_name">'. get_title_forkey($key) .'</span>
					</div>
					';
				}				
			$temp .= '
				<div class="clear_multi_title" title="'. __('Clear field','premium') .'"></div>
					<div class="premium_clear"></div>
			</div>
			';
			$value_ml = get_value_ml($default);
			foreach($langs as $key){ 
				$cl = '';
				if($key == $admin_lang){ $cl = 'active'; }	
				$val = '';
				if(isset($value_ml[$key])){
					$val = $value_ml[$key];
				}
				$temp .= '			
				<div class="premium_wrap_multi '. $cl .'" id="tab_'. $name .'_'. $key .'">
					<div class="premium_wrap_standart">
						<input type="text" name="'. $name .'_'. $key .'" '. $au_c .' class="premium_input big_input" value="'. pn_strip_input($val) .'" />
					</div>	
				</div>
				';
			} 				
					
			$temp .= '</div>';
		} else {
			$default = ctv_ml($default);
			$temp .= '<div class="premium_wrap_standart">';
			$temp .= '<input type="text" name="'. $name .'" '. $au_c .' class="premium_input big_input" value="'. pn_strip_input($default) .'" />';
			$temp .= '</div>';
		}			
		$temp .= $template['after_content'];
		$temp .= $template['after'];
		$temp = str_replace("[class]", $class, $temp);
		echo $temp;
	}	
}

if(!function_exists('pn_inputbig')){
	function pn_inputbig($title='', $name='', $default='', $class='', $not_auto=0, $template=''){
		$not_auto = intval($not_auto);
		$au_c = '';
		if($not_auto){ 
			$au_c = 'autocomplete="off"'; 
		}
		$temp = '';
		$template = pn_set_option_template($template);			
		$temp .= $template['before'];
		$temp .= $template['before_title'];	
		if($template['label']){	
			$temp .= '<label for="pn_'. $name .'">'. $title .'</label>';
		}	
		$temp .= $template['after_title'];
		$temp .= $template['before_content'];			
		$temp .= '<div class="premium_wrap_standart">';
		$temp .= '<input type="text" name="'. $name .'" '. $au_c .' id="pn_'. $name .'" class="premium_input big_input" value="'. pn_strip_input($default) .'" />';
		$temp .= '</div>';			
		$temp .= $template['after_content'];
		$temp .= $template['after'];
		$temp = str_replace("[class]", $class, $temp);
		echo $temp;
	}	
}

if(!function_exists('pn_input')){
	function pn_input($title='', $name='', $default='', $class='', $not_auto=0, $template=''){
		$not_auto = intval($not_auto);
		$au_c = '';
		if($not_auto){ 
			$au_c = 'autocomplete="off"'; 
		}
		$temp = '';
		$template = pn_set_option_template($template);			
		$temp .= $template['before'];
		$temp .= $template['before_title'];	
		if($template['label']){
			$temp .= '<label for="pn_'. $name .'">'. $title .'</label>';
		}	
		$temp .= $template['after_title'];
		$temp .= $template['before_content'];			
		$temp .= '<div class="premium_wrap_standart">';
		$temp .= '<input type="text" name="'. $name .'" '. $au_c .' id="pn_'. $name .'" class="premium_input" value="'. pn_strip_input($default) .'" />';
		$temp .= '</div>';			
		$temp .= $template['after_content'];
		$temp .= $template['after'];
		$temp = str_replace("[class]", $class, $temp);
		echo $temp;
	}
}

if(!function_exists('pn_uploader')){
	function pn_uploader($title='', $name='', $default='', $class='', $template=''){
		$temp = '';
		$template = pn_set_option_template($template);			
		$temp .= $template['before'];
		$temp .= $template['before_title'];			
		if($template['label']){
			$temp .= '<label for="pn_'. $name .'">'. $title .'</label>';
		}			
		$temp .= $template['after_title'];
		$temp .= $template['before_content'];			
		$temp .= '<div class="premium_wrap_standart">';
		$temp .= '
		<div class="premium_uploader">
			<div class="premium_uploader_img">'; 
				if($default){ $temp .= '<img src="'. $default .'" alt="" />'; } 
			$temp .= '
			</div>
			<div class="premium_uploader_input">
					<input type="text" name="'. $name .'" id="pn_'. $name .'_value" value="'. pn_strip_input($default) .'" />
			</div>
			<div class="premium_uploader_show tgm-open-media" id="pn_'. $name .'"></div>
			<div class="premium_uploader_hide ';
				if($default){ 
					$temp .= 'act'; 
				} 
			$temp .= '"></div>
				<div class="premium_clear"></div>
		</div>	
		';
		$temp .= '</div>';			
		$temp .= $template['after_content'];
		$temp .= $template['after'];
		$temp = str_replace("[class]", $class, $temp);
		echo $temp;
	}
}

if(!function_exists('pn_uploader_ml')){
	function pn_uploader_ml($title='', $name='', $default='', $class='', $template=''){
		$temp = '';
		$template = pn_set_option_template($template);			
		$temp .= $template['before'];
		$temp .= $template['before_title'];			
		if($template['label']){
			$temp .= '<label for="pn_'. $name .'">'. $title .'</label>';
		}			
		$temp .= $template['after_title'];
		$temp .= $template['before_content'];			

			if(is_ml()){
				
				$langs = get_langs_ml();
				$admin_lang = get_admin_lang();
				$temp .= '
				<div class="multi_wrapper">
					<div class="premium_title_multi">';
						 
						foreach($langs as $key){ 
							$cl = '';
								if($key == $admin_lang){ $cl = 'active'; }
							$temp .= '
							<div name="tab_'. $name .'_'. $key .'" class="tab_multi_title '. $cl .'">
								<div class="tab_multi_flag"><img src="'. get_lang_icon($key) .'" alt="" /></div>
								<span class="tab_multi_flag_name">'. get_title_forkey($key) .'</span>
							</div>';
						} 
						
						$temp .= '
							<div class="premium_clear"></div>
					</div>';		
					
					$value_ml = get_value_ml($default);
					foreach($langs as $key){ 
						$cl = '';
						if($key == $admin_lang){ $cl = 'active'; }
									
						$val = '';
						if(isset($value_ml[$key])){
							$val = $value_ml[$key];
						}
						$temp .= '				
						<div class="premium_wrap_multi '. $cl .'" id="tab_'. $name .'_'. $key .'">
							<div class="premium_wrap_standart">			
								<div class="premium_uploader">
									<div class="premium_uploader_img">'; 
										if($val){ $temp .= '<img src="'. $val .'" alt="" />'; } 
									$temp .= '
									</div>
									<div class="premium_uploader_input">
											<input type="text" name="'. $name.'_'.$key .'" id="pn_'. $name.'_'.$key .'_value" value="'. pn_strip_input($val) .'" />
									</div>
									<div class="premium_uploader_show tgm-open-media" id="pn_'. $name.'_'.$key .'"></div>
									<div class="premium_uploader_hide ';
										if($default){ 
											$temp .= 'act'; 
										} 
									$temp .= '"></div>
									<div class="premium_clear"></div>
								</div>								 		
							</div>	
						</div>';
					} 
				$temp .= '</div>';								
				
			} else {
				$temp .= '
				<div class="premium_wrap_standart">
					<div class="premium_uploader">
						<div class="premium_uploader_img">'; 
							if($default){ $temp .= '<img src="'. $default .'" alt="" />'; } 
						$temp .= '
						</div>
						<div class="premium_uploader_input">
								<input type="text" name="'. $name .'" id="pn_'. $name .'_value" value="'. pn_strip_input($default) .'" />
						</div>
						<div class="premium_uploader_show tgm-open-media" id="pn_'. $name .'"></div>
						<div class="premium_uploader_hide ';
							if($default){ 
								$temp .= 'act'; 
							} 
						$temp .= '"></div>
						<div class="premium_clear"></div>
					</div>
				</div>
				';
			}		
		$temp .= $template['after_content'];
		$temp .= $template['after'];
		$temp = str_replace("[class]", $class, $temp);
		echo $temp;
	}
}

if(!function_exists('pn_editor_ml')){
	function pn_editor_ml($title, $name='', $default='', $rows=10, $media=false, $class='', $template=''){
		$template = pn_set_option_template($template);			
		echo str_replace("[class]", $class, $template['before']);
		echo str_replace("[class]", $class, $template['before_title']);	
		if($template['label']){	
			echo '<label>'. $title .'</label>';
		}	
		echo str_replace("[class]", $class, $template['after_title']);
		echo str_replace("[class]", $class, $template['before_content']);
		
		if(is_ml()){
			$langs = get_langs_ml();
			$admin_lang = get_admin_lang();
			?>	
			<div class="multi_wrapper">
				<div class="premium_title_multi">
					<?php 
					foreach($langs as $key){ 
						$cl = '';
							if($key == $admin_lang){ $cl = 'active'; }
					?>
						<div name="tab_<?php echo $name;?>_<?php echo $key; ?>" class="tab_multi_title <?php echo $cl; ?>">
							<div class="tab_multi_flag"><img src="<?php echo get_lang_icon($key); ?>" alt="" /></div>
							<span class="tab_multi_flag_name"><?php echo get_title_forkey($key); ?></span>
						</div>
					<?php } ?>
						<div class="premium_clear"></div>
				</div>		
				<?php 
				$value_ml = get_value_ml($default);
				foreach($langs as $key){ 
					$cl = '';
					if($key == $admin_lang){ $cl = 'active'; }
								
					$val = '';
					if(isset($value_ml[$key])){
						$val = $value_ml[$key];
					}
					?>				
					<div class="premium_wrap_multi <?php echo $cl; ?>" id="tab_<?php echo $name;?>_<?php echo $key; ?>">
						<div class="premium_wrap_standart">
											
							<?php 		
							$settings['wpautop'] = true;
							$settings['media_buttons'] = $media;
							$settings['teeny'] = true;
							$settings['tinymce'] = true;
							$settings['textarea_rows'] = $rows;
							wp_editor(pn_strip_text($val), $name.'_'.$key ,$settings); 
							?>								

						</div>	
					</div>
				<?php } ?>
			</div>				
			<?php
		} else {
			$default = pn_strip_text(ctv_ml($default));

			echo '<div class="premium_wrap_standart">';
			
			$settings = array();
			$settings['wpautop'] = true;
			$settings['media_buttons'] = $media;
			$settings['teeny'] = true;
			$settings['tinymce'] = true;
			$settings['textarea_rows'] = $rows;
			wp_editor($default,$name,$settings); 	
			
			echo '</div>';		
		}
		
		echo str_replace("[class]", $class, $template['after_content']);
		echo str_replace("[class]", $class, $template['after']);	
	}  
} 

if(!function_exists('pn_editor')){
	function pn_editor($title, $name='', $default='', $rows=10, $media=false, $class='', $template=''){
		$template = pn_set_option_template($template);			
		echo str_replace("[class]", $class, $template['before']);
		echo str_replace("[class]", $class, $template['before_title']);	
		if($template['label']){	
			echo '<label>'. $title .'</label>';
		}	
		echo str_replace("[class]", $class, $template['after_title']);
		echo str_replace("[class]", $class, $template['before_content']);
		$default = pn_strip_text($default);
		echo '<div class="premium_wrap_standart">';
		
		$settings = array();
		$settings['wpautop'] = true;
		$settings['media_buttons'] = $media;
		$settings['teeny'] = true;
		$settings['tinymce'] = true;
		$settings['textarea_rows'] = $rows;
		wp_editor($default,$name,$settings); 	
		
		echo '</div>';			
		echo str_replace("[class]", $class, $template['after_content']);
		echo str_replace("[class]", $class, $template['after']);	
	}
}

if(!function_exists('pn_textarea')){
	function pn_textarea($title, $name='', $default='', $width='', $height='100px', $class='', $template=''){
		if(!$width){ $width = '100%'; }

		$temp = '';
		$template = pn_set_option_template($template);			
		$temp .= $template['before'];
		$temp .= $template['before_title'];	
		if($template['label']){	
			$temp .= '<label for="pn_'. $name .'">'. $title .'</label>';
		}	
		$temp .= $template['after_title'];
		$temp .= $template['before_content'];			
		$temp .= '<div class="premium_wrap_standart">';
		$temp .= '<textarea name="'. $name .'" id="pn_'. $name .'" style="width: '. $width .'; height: '. $height .';">'. pn_strip_text($default) .'</textarea>';
		$temp .= '</div>';			
		$temp .= $template['after_content'];
		$temp .= $template['after'];
		$temp = str_replace("[class]", $class, $temp);
		echo $temp;
	} 
}

if(!function_exists('pn_textarea_ml')){
	function pn_textarea_ml($title, $name='', $default='', $width='', $height='100px', $class='', $template=''){
		if(!$width){ $width = '100%'; }

		$temp = '';
		$template = pn_set_option_template($template);			
		$temp .= $template['before'];
		$temp .= $template['before_title'];	
		if($template['label']){	
			$temp .= '<label for="pn_'. $name .'">'. $title .'</label>';
		}	
		$temp .= $template['after_title'];
		$temp .= $template['before_content'];
		
		if(is_ml()){
			$langs = get_langs_ml();
			$admin_lang = get_admin_lang();	
			
			$temp .= '
			<div class="multi_wrapper">
				<div class="premium_title_multi">';	
					foreach($langs as $key){ 
						$cl = '';
						if($key == $admin_lang){ $cl = 'active'; }
				
						$temp .= '
						<div name="tab_'. $name .'_'. $key .'" class="tab_multi_title '. $cl .'">
							<div class="tab_multi_flag"><img src="'. get_lang_icon($key) .'" alt="" /></div>
							<span class="tab_multi_flag_name">'. get_title_forkey($key) .'</span>
						</div>
						';
					}
			$temp .= '
					<div class="clear_multi_title" title="'. __('Clear field','premium') .'"></div>
					<div class="premium_clear"></div>
				</div>
			';
			
			$value_ml = get_value_ml($default);
			foreach($langs as $key){
				$cl = '';
				if($key == $admin_lang){ $cl = 'active'; }
				
				$val = '';
				if(isset($value_ml[$key])){
					$val = $value_ml[$key];
				}	
				
				$temp .= '
				<div class="premium_wrap_multi '. $cl .'" id="tab_'. $name .'_'. $key .'">
					<div class="premium_wrap_standart">
						<textarea name="'. $name .'_'. $key .'" style="width: '. $width .'; height: '. $height .';">'. pn_strip_text($val) .'</textarea>
					</div>
				</div>
				';
				
			}
			$temp .= '	
			</div>';	
		} else { 
			$default = ctv_ml($default);
			$temp .= '<div class="premium_wrap_standart">';
			$temp .= '<textarea name="'. $name .'" id="pn_'. $name .'" style="width: '. $width .'; height: '. $height .';">'. pn_strip_text($default) .'</textarea>';
			$temp .= '</div>';		
		} 				
		
		$temp .= $template['after_content'];
		$temp .= $template['after'];
		$temp = str_replace("[class]", $class, $temp);
		echo $temp;
	}
}

if(!function_exists('pn_help')){
	function pn_help($title, $content='',$class='', $template=''){
		$temp = '';
		$template = pn_set_option_template($template);			
		$temp .= $template['before'];
		$temp .= $template['before_title'];			
		$temp .= $template['after_title'];
		$temp .= $template['before_content'];			
		$temp .= '
		<div class="premium_wrap_help">
			<div class="premium_helptitle"><span>'. $title .'</span></div>
			<div class="premium_helpcontent">'. $content .'</div>
		</div>
		';			
		$temp .= $template['after_content'];
		$temp .= $template['after'];
		$temp = str_replace("[class]", $class, $temp);
		echo $temp;
	}
}

if(!function_exists('pn_warning')){
	function pn_warning($content, $class='', $template=''){
		$temp = '';
		$template = pn_set_option_template($template);			
		$temp .= $template['before'];
		$temp .= $template['before_title'];			
		$temp .= $template['after_title'];
		$temp .= $template['before_content'];			
		$temp .= '<div class="premium_wrap_warning">'. $content .'</div>';			
		$temp .= $template['after_content'];
		$temp .= $template['after'];
		$temp = str_replace("[class]", $class, $temp);
		echo $temp;	
	}
}

if(!function_exists('pn_textareaico_ml')){
	function pn_textareaico_ml($title, $name='', $default='', $tags='', $prefix1='[', $prefix2=']',$width='', $height='100px' , $class='', $template=''){
		if(!$width){ $width = '100%'; }
		$tags = (array)$tags;
		$temp = '';
		$template = pn_set_option_template($template);			
		$temp .= $template['before'];
		$temp .= $template['before_title'];	
		if($template['label']){
			$temp .= '<label>'. $title .'</label>';
		}	
		$temp .= $template['after_title'];
		$temp .= $template['before_content'];	
		if(function_exists('is_ml') and is_ml()){
			$langs = get_langs_ml();
			$admin_lang = get_admin_lang();		
			$temp .= '
			<div class="multi_wrapper">
				<div class="premium_title_multi">';
					foreach($langs as $key){ 
						$cl = '';
						if($key == $admin_lang){ $cl = 'active'; }
						$temp .= '	
						<div name="tab_'. $name .'_'. $key .'" class="tab_multi_title '. $cl .'">
							<div class="tab_multi_flag"><img src="'. get_lang_icon($key) .'" alt="" /></div>
							<span class="tab_multi_flag_name">'. get_title_forkey($key) .'</span>
						</div>';
					} 	
					$temp .= '		
						<div class="clear_multi_title" title="'. __('Clear field','premium') .'"></div>
							<div class="premium_clear"></div>
					</div>';	 
					$value_ml = get_value_ml($default);
					foreach($langs as $key){ 
						$cl = '';
						if($key == $admin_lang){ $cl = 'active'; }	
						$val = '';
						if(isset($value_ml[$key])){
							$val = $value_ml[$key];
						}	
						$temp .= '	
						<div class="premium_wrap_multi '. $cl .'" id="tab_'. $name .'_'. $key .'">
							<div class="premium_wrap_standart">
								<div class="prem_tagtext_wrap">
									<div class="prem_tagtext">';
										if(is_array($tags)){						
											foreach($tags as $tag => $value){ 
												$temp .= '
												<span title="'. $prefix1 . trim($tag) . $prefix2 .'">
													<div class="prem_span_hide">'. $prefix1 . trim($tag) . $prefix2 .'</div>
													'. trim($value) .'
												</span>  
												';
											}
										}					
										$temp .= '
											<div class="premium_clear"></div>
									</div>
									<textarea name="'. $name .'_'. $key .'" style="width: '. $width .'; height: '. $height .';">'. pn_strip_text($val) .'</textarea>
								</div>
							</div>
						</div>';
					} 	
			$temp .= '	
			</div>';					
		} else { 
			if(function_exists('ctv_ml')){
				$default = ctv_ml($default);
			}
			$temp .= '<div class="premium_wrap_standart">
			<div class="prem_tagtext_wrap">
				<div class="prem_tagtext">
			';	
				foreach($tags as $tag => $value){ 
					$temp .= '
					<span title="'. $prefix1 . trim($tag) . $prefix2 .'">
						<div class="prem_span_hide">'. $prefix1 . trim($tag) . $prefix2 .'</div>
						'. trim($value) .'
					</span>
					';		
				}						
				$temp .= '
					<div class="premium_clear"></div>
				</div>
				<textarea name="'. $name .'" style="width: '. $width .'; height: '. $height .';">'. pn_strip_text($default) .'</textarea>';
			$temp .= '
				</div>
			</div>';	
		}			
				
		$temp .= $template['after_content'];
		$temp .= $template['after'];
		$temp = str_replace("[class]", $class, $temp);
		echo $temp;
	} 
}
 
if(!function_exists('pn_textareaico')){ 
	function pn_textareaico($title, $name='', $default='', $tags='', $prefix1='[', $prefix2=']',$width='', $height='100px' , $class='', $template=''){
		if(!$width){ $width = '100%'; }
		$tags = (array)$tags;
		$temp = '';
		$template = pn_set_option_template($template);			
		$temp .= $template['before'];
		$temp .= $template['before_title'];	
		if($template['label']){
			$temp .= '<label>'. $title .'</label>';
		}	
		$temp .= $template['after_title'];
		$temp .= $template['before_content'];			
		$temp .= '<div class="premium_wrap_standart">';
		$temp .= '
		<div class="prem_tagtext_wrap">
			<div class="prem_tagtext">';

				foreach($tags as $tag => $value){ 
					$temp .= '
					<span title="'. $prefix1 . trim($tag) . $prefix2 .'">
						<div class="prem_span_hide">'. $prefix1 . trim($tag) . $prefix2 .'</div>
						'. trim($value) .'
					</span>
					';  
				}
							
				$temp .= '
					<div class="premium_clear"></div>
			</div>
			<textarea name="'. $name .'" style="width: '. $width .'; height: '. $height .';">'. pn_strip_text($default) .'</textarea>			
		</div>
		';
		$temp .= '</div>';			
		$temp .= $template['after_content'];
		$temp .= $template['after'];
		$temp = str_replace("[class]", $class, $temp);
		echo $temp;
	}
}

if(!function_exists('pn_strip_options')){
	function pn_strip_options($filter, $options, $method='post'){
		$new = array();
		$filter = trim($filter);
		if($filter){
			$options = apply_filters($filter, $options, '');
		}	
		foreach($options as $option){
			$name = trim(is_isset($option,'name'));
			$work = trim(is_isset($option,'work'));
			$ml = intval(is_isset($option,'ml'));
			if($name and $work){
				if($ml and is_ml()){
					if($method == 'post'){
						$val = is_param_post_ml($name);
					} else {
						$val = is_param_get_ml($name);
					}
				} else {
					if($method == 'post'){
						$val = is_param_post($name);
					} else {
						$val = is_param_get($name);
					}						
				}		
				if($work == 'int'){
					$new[$name] = intval($val);
				} elseif($work == 'none'){
					$new[$name] = $val;						
				} elseif($work == 'input'){
					$new[$name] = pn_strip_input($val);
				} elseif($work == 'sum'){
					$new[$name] = is_my_money($val);				
				} elseif($work == 'text'){
					$new[$name] = pn_strip_text($val);					
				} elseif($work == 'email'){
					$new[$name] = is_email($val);					
				} elseif($work == 'input_array'){
					$new[$name] = pn_strip_input_array($val);
				} elseif($work == 'symbols'){
					$new[$name] = pn_strip_symbols($val);					
				}
			}
		}	
		return $new;
	}
}

if(!function_exists('pn_admin_submenu')){
	function pn_admin_submenu($name, $options, $lost='', $title=''){
		$name = trim($name);
		$losted = pn_admin_prepare_lost($lost);
		$title = pn_strip_input($title);
		$mod = pn_strip_input(is_param_get($name));
		$now_url = is_isset($_SERVER,'REQUEST_URI');
		$now_url = str_replace('/wp-admin/','', $now_url);
		$now_url = explode('?',$now_url);
		$link = $now_url[0];
		$sign = '?'; 
		if(isset($_GET) and is_array($_GET)){
			foreach($_GET as $k => $v){
				if($k != $name and !in_array($k,$losted)){
					$link .= $sign . $k .'='. esc_html($v);
					$sign = '&';
				}
			}
		}	
		?>
		<div class="premium_submenu">
			<?php if($title){ ?>
			<div class="premium_submenu_title">
				<?php echo $title; ?>:
			</div>
			<?php } ?>
			<ul class="subsubsub">
				<li><a href="<?php echo $link;?>" <?php if(!$mod){?>class="current"<?php }?>><?php _e('All'); ?></a></li>
				<?php 
				if(is_array($options)){
					foreach($options as $key => $val){
				?>
					<li>| <a href="<?php echo $link . $sign . $name . '=' . $key; ?>" <?php if($mod == $key){ ?>class="current"<?php }?> ><?php echo $val; ?></a></li>
				<?php 
					}
				} 
				?>
			</ul>	
				<div class="premium_clear"></div>
		</div>
			<div class="premium_clear"></div>
		<?php
	}
}  

if(!function_exists('pn_admin_filter_data')){
	function pn_admin_filter_data($url, $lost=''){
		$losted = pn_admin_prepare_lost($lost);
		$n = parse_url($url);
		$data_url = array();
		if(isset($n['query'])){
			parse_str($n['query'], $data_url);
		}
		foreach($losted as $v){
			if(isset($data_url[$v])){
				unset($data_url[$v]);
			}		
		}
		$sign = '?';
		$link = is_isset($n, 'path');
		if(is_array($data_url)){
			foreach($data_url as $k => $v){  
				$link .= $sign . $k .'='. esc_html($v);
				$sign = '&';
			}
		}	
		return $link . $sign;
	}
}

if(!function_exists('get_sort_ul')){
	function get_sort_ul($items, $num){
		if(isset($items[$num]) and is_array($items[$num])){
			if(count($items[$num]) > 0){
		?>
		<ul>
			<?php 
			foreach($items[$num] as $item){ 
				$item_id = is_isset($item,'id');
			?>
				<li id="number_<?php echo is_isset($item,'number'); ?>">
					<div class="premium_sort_block"><?php echo is_isset($item,'title');?></div>
						<div class="premium_clear"></div>
						<?php 
						get_sort_ul($items, $item_id); 
						?>					
				</li>		
			<?php 
			} 
			?>
		</ul>
		<?php
			}
		} 
	}
} 

if(!function_exists('pn_sort_one_screen')){
	function pn_sort_one_screen($items, $title=''){
		$title = trim($title);
		if(!$title){ $title = __('Put in the correct order','premium'); }
		?>
		<div class="premium_sort_wrap thesort">
			<div class="premium_sort_title"><?php echo $title; ?></div>
			<?php 
			get_sort_ul($items,0); 
			?>	    
		</div>
		<?php
	}
} 