<?php
if( !defined( 'ABSPATH')){ exit(); }

/* Новости и страницы */
add_action("admin_menu", "seo_metabox");
function seo_metabox($post_id) {
	if (function_exists("add_meta_box")) {
		$args = array('public' => true);
		$post_types = get_post_types($args,'names');
		if(is_array($post_types)){
			foreach($post_types as $pt){
				if($pt != 'attachment'){
					add_meta_box("pn_seo_id", __('Seo','pn'), "pn_seo_box", $pt, "normal");
				}
			}
		}
	}
}

function pn_seo_box($post){
	$post_id = $post->ID;
			
	$seo_title = get_post_meta($post_id, 'seo_title', true); 
	$seo_key = get_post_meta($post_id, 'seo_key', true); 
	$seo_descr = get_post_meta($post_id, 'seo_descr', true);

	$ogp_title = get_post_meta($post_id, 'ogp_title', true);
	$ogp_descr = get_post_meta($post_id, 'ogp_descr', true);
	?>
	<input type="hidden" name="pn_seo_box" value="1" />
		
		<p><strong><?php _e('Page title','pn'); ?></strong>
		<?php pn_inputbig_ml('','seo_title' , $seo_title); ?>
		</p>
		
		<p><strong><?php _e('Page keywords','pn'); ?></strong>
		<?php pn_textarea_ml('','seo_key', $seo_key, '', '50px'); ?>
		</p>		
		
		<p><strong><?php _e('Page description','pn'); ?></strong>
		<?php pn_textarea_ml('','seo_descr', $seo_descr, '', '100px'); ?>
		</p>

		<p><strong><?php _e('OGP title','pn'); ?></strong>
		<?php pn_inputbig_ml('','ogp_title' , $ogp_title); ?>
		</p>
		<p><strong><?php _e('OGP description','pn'); ?></strong>
		<?php pn_textarea_ml('','ogp_descr', $ogp_descr, '', '100px'); ?>
		</p>		
	<?php
}

add_action("edit_post", "edit_post_seo");
function edit_post_seo($post_id){
	if(!current_user_can('edit_post', $post_id )){
		return $post_id;
	}
		

	if(isset($_POST['pn_seo_box'])){					
		$seo_title = pn_strip_input(is_param_post_ml('seo_title'));
		update_post_meta($post_id, 'seo_title', $seo_title) or add_post_meta($post_id, 'seo_title', $seo_title, true);	
		
		$seo_key = pn_strip_input(is_param_post_ml('seo_key'));
		update_post_meta($post_id, 'seo_key', $seo_key) or add_post_meta($post_id, 'seo_key', $seo_key, true);

		$seo_descr = pn_strip_input(is_param_post_ml('seo_descr'));
		update_post_meta($post_id, 'seo_descr', $seo_descr) or add_post_meta($post_id, 'seo_descr', $seo_descr, true);	

		$ogp_title = pn_strip_input(is_param_post_ml('ogp_title'));
		update_post_meta($post_id, 'ogp_title', $ogp_title) or add_post_meta($post_id, 'ogp_title', $ogp_title, true);		
		
		$ogp_descr = pn_strip_input(is_param_post_ml('ogp_descr'));
		update_post_meta($post_id, 'ogp_descr', $ogp_descr) or add_post_meta($post_id, 'ogp_descr', $ogp_descr, true);		
	}
		
}
/* end новости и страницы */

/* категории и тэги */
add_action('init','all_posttypes_set_seo', 10000);
function all_posttypes_set_seo(){
	$taxonomies=get_taxonomies('','objects');
	if(is_array($taxonomies)){
		$not = array('nav_menu','link_category','post_format');
	    foreach($taxonomies as $tax){    
		    $name = $tax->name;
		    if(!in_array($name,$not)){	
				add_action($name . '_add_form_fields', 'add_form_fields_seo');
				add_action($name . '_edit_form', 'edit_form_fields_seo');
				add_action('edit_' . $name, 'edit_tags_seo');
				add_action('created_' . $name, 'edit_tags_seo');
			}
		}
	}
}
		 
function add_form_fields_seo($tag){
?>
	<input type="hidden" name="tag_seo_filter" value="1" />
		
	<div class="form-field term-name-wrap">
		<label><?php _e('Page title','pn'); ?></label>
		<?php pn_inputbig_ml('','seo_title', ''); ?>
	</div>
	<div class="form-field term-name-wrap">
		<label><?php _e('Page keywords','pn'); ?></label>
		<?php pn_textarea_ml('','seo_key', '', '', '50px'); ?>
	</div>
	<div class="form-field term-name-wrap">
		<label><?php _e('Page description','pn'); ?></label>
		<?php pn_textarea_ml('','seo_descr', '', '', '100px'); ?>
	</div>	
	
	<div class="form-field term-name-wrap">
		<label><?php _e('OGP title','pn'); ?></label>
		<?php pn_inputbig_ml('','ogp_title', ''); ?>
	</div>
	<div class="form-field term-name-wrap">
		<label><?php _e('OGP description','pn'); ?></label>
		<?php pn_textarea_ml('','ogp_descr', '', '', '100px'); ?>
	</div>	
<?php 	
}

function edit_form_fields_seo($tag){
	
	$term_id = $tag->term_id;
	$seo_title = get_term_meta($term_id, 'seo_title', true); 
	$seo_key = get_term_meta($term_id, 'seo_key', true); 
	$seo_descr = get_term_meta($term_id, 'seo_descr', true);

	$ogp_title = get_term_meta($term_id, 'ogp_title', true);
	$ogp_descr = get_term_meta($term_id, 'ogp_descr', true);
?>
	<input type="hidden" name="tag_seo_filter" value="1" />
		
	<table class="form-table">
		<tr class="form-field form-required term-name-wrap">
			<th scope="row"><label><?php _e('Page title','pn'); ?></label></th>
			<td>
				<?php pn_inputbig_ml('','seo_title', $seo_title); ?>
			</td>
		</tr>
		<tr class="form-field form-required term-name-wrap">
			<th scope="row"><label><?php _e('Page keywords','pn'); ?></label></th>
			<td>
				<?php pn_textarea_ml('','seo_key', $seo_key, '', '50px'); ?>
			</td>
		</tr>
		<tr class="form-field form-required term-name-wrap">
			<th scope="row"><label><?php _e('Page description','pn'); ?></label></th>
			<td>
				<?php pn_textarea_ml('','seo_descr', $seo_descr, '', '100px'); ?>
			</td>
		</tr>

		<tr class="form-field form-required term-name-wrap">
			<th scope="row"><label><?php _e('OGP title','pn'); ?></label></th>
			<td>
				<?php pn_inputbig_ml('','ogp_title', $ogp_title); ?>
			</td>
		</tr>
		<tr class="form-field form-required term-name-wrap">
			<th scope="row"><label><?php _e('OGP description','pn'); ?></label></th>
			<td>
				<?php pn_textarea_ml('','ogp_descr', $ogp_descr, '', '100px'); ?>
			</td>
		</tr>		
	</table>
<?php	
} 

function edit_tags_seo($id){

	if(isset($_POST['tag_seo_filter'])){
			
		$seo_title = pn_strip_input(is_param_post_ml('seo_title'));
		update_term_meta($id, 'seo_title', $seo_title);	
		
		$seo_key = pn_strip_input(is_param_post_ml('seo_key'));
		update_term_meta($id, 'seo_key', $seo_key);

		$seo_descr = pn_strip_input(is_param_post_ml('seo_descr'));
		update_term_meta($id, 'seo_descr', $seo_descr);
		
		$ogp_title = pn_strip_input(is_param_post_ml('ogp_title'));
		update_term_meta($id, 'ogp_title', $ogp_title);			

		$ogp_descr = pn_strip_input(is_param_post_ml('ogp_descr'));
		update_term_meta($id, 'ogp_descr', $ogp_descr);		
	}
	
}
/* end категории и тэги */

/* направления обменов */
add_filter('list_tabs_naps','list_tabs_naps_seo');
function list_tabs_naps_seo($list_tabs_naps){
	
	$list_tabs_naps['tab10'] = __('SEO','pn');
	
	return $list_tabs_naps;
}

add_action('tab_naps_tab10','tab_naps_tab_seo',99,2);
function tab_naps_tab_seo($data, $data_id){
						
	$seo_exch_title = get_naps_meta($data_id, 'seo_exch_title'); 
	$seo_title = get_naps_meta($data_id, 'seo_title'); 
	$seo_key = get_naps_meta($data_id, 'seo_key'); 
	$seo_descr = get_naps_meta($data_id, 'seo_descr');

	$ogp_title = get_naps_meta($data_id, 'ogp_title'); 
	$ogp_descr = get_naps_meta($data_id, 'ogp_descr');							
	?>
	<tr>
		<th><?php _e('Exchange title (H1)','pn'); ?></th>
		<td colspan="2">
			<?php pn_inputbig_ml('','seo_exch_title', $seo_exch_title); ?>
		</td>
	</tr>	
	<tr>
		<th><?php _e('Page title','pn'); ?></th>
		<td colspan="2">
			<?php pn_inputbig_ml('','seo_title', $seo_title); ?>
		</td>
	</tr>	
	<tr>
		<th><?php _e('Page keywords','pn'); ?></th>
		<td colspan="2">
			<?php pn_textarea_ml('','seo_key', $seo_key, '', '50px'); ?>
		</td>
	</tr>							
	<tr>
		<th><?php _e('Page description','pn'); ?></th>
		<td colspan="2">
			<?php pn_textarea_ml('','seo_descr', $seo_descr, '', '100px'); ?>
		</td>
	</tr>
	<tr>
		<th><?php _e('OGP title','pn'); ?></th>
		<td colspan="2">
			<?php pn_inputbig_ml('','ogp_title', $ogp_title); ?>
		</td>
	</tr>							
	<tr>
		<th><?php _e('OGP description','pn'); ?></th>
		<td colspan="2">
			<?php pn_textarea_ml('','ogp_descr', $ogp_descr, '', '100px'); ?>
		</td>
	</tr>
	<?php						
}

add_action('pn_naps_edit_before','pn_naps_edit_seo');
add_action('pn_naps_add','pn_naps_edit_seo');
function pn_naps_edit_seo($data_id){
	
	/* seo */
	$seo_exch_title = pn_strip_input(is_param_post_ml('seo_exch_title'));
	update_naps_meta($data_id, 'seo_exch_title', $seo_exch_title);	
					
	$seo_title = pn_strip_input(is_param_post_ml('seo_title'));
	update_naps_meta($data_id, 'seo_title', $seo_title);	
						
	$seo_key = pn_strip_input(is_param_post_ml('seo_key'));
	update_naps_meta($data_id, 'seo_key', $seo_key);

	$seo_descr = pn_strip_input(is_param_post_ml('seo_descr'));
	update_naps_meta($data_id, 'seo_descr', $seo_descr);					
									
	$ogp_title = pn_strip_input(is_param_post_ml('ogp_title'));
	update_naps_meta($data_id, 'ogp_title', $ogp_title);

	$ogp_descr = pn_strip_input(is_param_post_ml('ogp_descr'));
	update_naps_meta($data_id, 'ogp_descr', $ogp_descr);					
	/* end seo */	
	
} 
/* end направления обменов */

/* canonical */
remove_action('wp_head', 'rel_canonical');
add_action( 'wp_head', 'seo_rel_canonical');
function seo_rel_canonical() {
	if ( !is_singular() ){
		return;
	}	

	global $wp_the_query;
	if ( !$id = $wp_the_query->get_queried_object_id() ){
		return;
	}	

	if(is_exchange_page()){

		$pnhash = get_query_var('pnhash');	
		$link = get_exchange_link($pnhash);
		echo "<link rel='canonical' href='$link' />\n";
		
	} else {
		if(!is_exchangestep_page()){
			
			$link = get_permalink( $id );
			echo "<link rel='canonical' href='$link' />\n";
			
		}	
	}
}

/* exchange title */
add_filter('get_exchange_title' , 'get_exchange_title_seo', 99, 4);
function get_exchange_title_seo($title, $naps_id, $item_title1, $item_title2){
global $premiumbox;
	
	$item_id = intval($naps_id);
		
	$seo_exch_title = pn_strip_input(ctv_ml(get_naps_meta($item_id, 'seo_exch_title')));
	if($seo_exch_title){
		return $seo_exch_title;
	}

	$new_title = pn_strip_input(ctv_ml($premiumbox->get_option('seo','exch_temp2')));
	$new_title = str_replace('[title1]',$item_title1,$new_title);
	$new_title = str_replace('[title2]',$item_title2,$new_title);
	if($new_title){
		return $new_title;
	}		
	
	return $title;
}
/* end exchange title */

/* keywords */
add_filter('wp_head' , 'wp_head_seo');
function wp_head_seo() {
global $premiumbox;

	$key = '';
	$descr = '';
	$ogp_title = '';
	$ogp_descr = '';
	$ogp_image = '';
	
	$sitename = get_option('blogname');
	
	if(is_front_page()){
		$key = pn_strip_input(ctv_ml($premiumbox->get_option('seo','home_key')));
		$descr = pn_strip_input(ctv_ml($premiumbox->get_option('seo','home_descr')));
		
		$ogp_image = pn_strip_input($premiumbox->get_option('seo','ogp_home_img'));
		$ogp_title = pn_strip_input(ctv_ml($premiumbox->get_option('seo','ogp_home_title')));
		if(!$ogp_title){ $ogp_title = pn_strip_input($sitename); }
		$ogp_descr = pn_strip_input(ctv_ml($premiumbox->get_option('seo','ogp_home_descr')));
		if(!$ogp_descr){ $ogp_descr = pn_strip_input(get_option('blogdescription')); }		
	} elseif(is_home()){
		$key = pn_strip_input(ctv_ml($premiumbox->get_option('seo','news_key')));
		$descr = pn_strip_input(ctv_ml($premiumbox->get_option('seo','news_descr')));

		$ogp_image = pn_strip_input($premiumbox->get_option('seo','ogp_news_img'));
		$ogp_title = pn_strip_input(ctv_ml($premiumbox->get_option('seo','ogp_news_title')));
		$ogp_descr = pn_strip_input(ctv_ml($premiumbox->get_option('seo','ogp_news_descr')));		
	} elseif(is_category() or is_tag()){
		
		$term_data = get_queried_object();
		$term_id = $term_data->term_id;
		
		$key = pn_strip_input(ctv_ml(get_term_meta($term_id, 'seo_key', true))); 
		$descr = pn_strip_input(ctv_ml(get_term_meta($term_id, 'seo_descr', true)));

		$ogp_title = pn_strip_input(ctv_ml(get_term_meta($term_id, 'ogp_title', true)));
		if(!$ogp_title){ $ogp_title = pn_strip_input($term_data->name); }
		$ogp_descr = pn_strip_input(ctv_ml(get_term_meta($term_id, 'ogp_descr', true)));	
		if(!$ogp_descr){ $ogp_descr = pn_strip_input($term_data->description); }		
		
	} elseif(is_singular('post')){
		global $post;
		$post_id = intval($post->ID);
		$key = pn_strip_input(ctv_ml(get_post_meta($post_id, 'seo_key', true))); 
		$descr = pn_strip_input(ctv_ml(get_post_meta($post_id, 'seo_descr', true)));
		
		$ogp_title = pn_strip_input(ctv_ml(get_post_meta($post_id, 'ogp_title', true))); 
		if(!$ogp_title){ $ogp_title = esc_html(ctv_ml($post->post_title)); }
		$ogp_descr = pn_strip_input(ctv_ml(get_post_meta($post_id, 'ogp_descr', true)));
		if(!$ogp_descr){ $ogp_descr = esc_html(wp_trim_words(strip_tags(ctv_ml($post->post_content)),10)); }
 		
		$image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'thumbnail');
		$ogp_image = pn_strip_input(is_isset($image_url,0));
		
	} elseif(is_page()){		
		if(is_exchange_page()){
			global $naps_id;
			$naps_id = intval($naps_id);
			$key = pn_strip_input(ctv_ml(get_naps_meta($naps_id, 'seo_key'))); 
			$descr = pn_strip_input(ctv_ml(get_naps_meta($naps_id, 'seo_descr')));					
		
			$ogp_title = pn_strip_input(ctv_ml(get_naps_meta($naps_id, 'ogp_title')));
			$ogp_descr = pn_strip_input(ctv_ml(get_naps_meta($naps_id, 'ogp_descr')));	
		
		} else {
			global $post;
			$post_id = intval($post->ID);
			$key = pn_strip_input(ctv_ml(get_post_meta($post_id, 'seo_key', true))); 
			$descr = pn_strip_input(ctv_ml(get_post_meta($post_id, 'seo_descr', true)));

			$ogp_title = pn_strip_input(ctv_ml(get_post_meta($post_id, 'ogp_title', true))); 
			if(!$ogp_title){ $ogp_title = esc_html(ctv_ml($post->post_title)); }
			$ogp_descr = pn_strip_input(ctv_ml(get_post_meta($post_id, 'ogp_descr', true)));
			if(!$ogp_descr){ $ogp_descr = esc_html(wp_trim_words(strip_tags(ctv_ml($post->post_content)),10)); }
 		
			$image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'thumbnail');
			$ogp_image = pn_strip_input(is_isset($image_url,0));			
		}
	}
	
	$ogp_image = is_ssl_url($ogp_image);

?><meta name="keywords" content="<?php echo $key; ?>" />
<meta name="description" content="<?php echo $descr; ?>" />

<meta property="og:type" content="website" />
<meta property="og:url" content="<?php echo lang_self_link(); ?>" />
<meta property="og:site_name" content="<?php echo $sitename; ?>" />
<?php if($ogp_descr){ ?>
<meta property="og:description" content="<?php echo $ogp_descr; ?>" />
<?php } ?>
<?php if($ogp_title){ ?>
<meta property="og:title" content="<?php echo $ogp_title; ?>" />
<?php } ?>
<?php if($ogp_image){ ?>
<meta property="og:image" content="<?php echo $ogp_image; ?>" />
<?php } 	
	
}

add_filter('exchange_step1_log', 'seo_exchange_step1_log');
function seo_exchange_step1_log($log){
global $naps_id;

	$naps_id = intval($naps_id);
	$key = pn_strip_input(ctv_ml(get_naps_meta($naps_id, 'seo_key'))); 
	$descr = pn_strip_input(ctv_ml(get_naps_meta($naps_id, 'seo_descr')));	
	
	$log['keywords'] = $key;
	$log['description'] = $descr;
	
	return $log;
}
/* end keywords */

/* title */
add_filter('wp_title' , 'wp_title_seo',99);
function wp_title_seo($title) {
global $wpdb, $premiumbox;	
	
	if(is_front_page()){
		$new_title = pn_strip_input(ctv_ml($premiumbox->get_option('seo','home_title')));
		if($new_title){
			return $new_title;
		}
	} elseif(is_home()){
		$new_title = pn_strip_input(ctv_ml($premiumbox->get_option('seo','news_title')));
		if($new_title){
			return $new_title;
		}		
	} elseif(is_singular('post')){
		global $post;
		
		$item_id = intval($post->ID);
		
		$seo_title = pn_strip_input(ctv_ml(get_post_meta($item_id, 'seo_title', true)));
		if($seo_title){
			return $seo_title;
		}
		
		$item_title = pn_strip_input(ctv_ml($post->post_title));
		$news_temp = pn_strip_input(ctv_ml($premiumbox->get_option('seo','news_temp')));
		$new_title = str_replace('[title]',$item_title,$news_temp);
		if($new_title){
			return $new_title;
		}
		
	} elseif(is_category()){
		
		global $cat;
		$item_id = intval($cat);
		
		$seo_title = pn_strip_input(ctv_ml(get_term_meta($item_id, 'seo_title', true)));
		if($seo_title){
			return $seo_title;
		}		
		
	} elseif(is_tag()){	
		
		global $tag_id;
		$item_id = intval($tag_id);
		
		$seo_title = pn_strip_input(ctv_ml(get_term_meta($item_id, 'seo_title', true)));
		if($seo_title){
			return $seo_title;
		}		
		
	} elseif(is_page()){	
	
		if(is_exchange_page()){
			
			global $naps_id, $naps_data;
			$item_id = intval($naps_id);
		
			$seo_title = pn_strip_input(ctv_ml(get_naps_meta($item_id, 'seo_title')));
			if($seo_title){
				return $seo_title;
			}

			$item_title1 = pn_strip_input($naps_data->item1);
			$item_title2 = pn_strip_input($naps_data->item2);
			$new_title = pn_strip_input(ctv_ml($premiumbox->get_option('seo','exch_temp')));
			$new_title = str_replace('[title1]',$item_title1,$new_title);
			$new_title = str_replace('[title2]',$item_title2,$new_title);
			if($new_title){
				return $new_title;
			}			
		
		} elseif(is_exchangestep_page()){
		
			return get_exchangestep_title();
		
		} else {
			
			global $post;
			
			$item_id = intval($post->ID);
			
			$seo_title = pn_strip_input(ctv_ml(get_post_meta($item_id, 'seo_title', true)));
			if($seo_title){
				return $seo_title;
			}
			
			$item_title = pn_strip_input(ctv_ml($post->post_title));
			$new_title = pn_strip_input(ctv_ml($premiumbox->get_option('seo','page_temp')));
			$new_title = str_replace('[title]',$item_title,$new_title);
			if($new_title){
				return $new_title;
			}			
			
		}
	}		
				
	return $title;
} 
/* end title */	