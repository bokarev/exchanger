<?php
if( !defined( 'ABSPATH')){ exit(); }

/* фильтры мультиязычности */
if(!is_ml()){ return; }	
	
	function convert_link_ml($link){
		if(get_site_lang() != get_locale()){
			$key = get_lang_key(get_locale());
			if ( false === strpos( $link, get_site_url_or().'/'. $key ) ) {
				return str_replace(get_site_url_or(), get_site_url_or().'/'. $key ,$link);
			}
		}
		return $link;
	}

	function get_the_terms_ml($terms){
		$in_key = array('name','description','post_title','post_content','post_excerpt');
		if (is_array($terms)) { 
			$lang = get_locale();
			$new_terms = array();
			foreach($terms as $key => $class){	
				if (is_object($class)) {
					$new_class = array();
					foreach($class as $cl_key => $cl_zn){
						if(in_array($cl_key, $in_key)){
							$new_class[$cl_key] = ctv_ml($cl_zn);
						} else {
							$new_class[$cl_key] = $cl_zn;
						}
					}
					$new_terms[$key] = (object)$new_class;
				}
			}
			return $new_terms;
        }
		return $terms;
	}	
	
 	function get_term_ml($term){
		if (is_object($term)) {
			$lang = get_locale();
			$new_class = new stdClass;
			foreach($term as $cl_key => $cl_zn){
				if($cl_key == 'name' or $cl_key == 'description'){
					$new_class->$cl_key = ctv_ml($cl_zn);
				} else {
					$new_class->$cl_key = $cl_zn;
				}				
			}	
			
			if(class_exists('WP_Term')){
				return new WP_Term($new_class);
			} else {
				return $new_class;
			}
		}
		
		return $term;
	}	
	
	function get_bloginfo_rss_ml($output, $show){
		if($show == 'url' or $show == 'wpurl'){
			return convert_link_ml($output);
		}
		return $output;
	}
	
	function hide_inline_action($actions){
		unset($actions['inline hide-if-no-js']);
		return $actions;
	}		
	
 	function add_form_fields_ml($tag){
		?>
		<script type="text/javascript">
		jQuery(function($){
			$('#tag-name').parents('.form-field').hide();
			$('#tag-description').parents('.form-field').hide();
			
			$('.terms_name_val').change(function(){
				var vale = $(this).val();
				$('#tag-name').val(vale);
			});
		});		
		</script>
		
		<input type="hidden" name="tag_ml_filter" value="1" />
		
		<?php 
		$admin_lang = get_admin_lang();
		$langs = get_langs_ml();
		foreach($langs as $key){ ?>
			<div class="form-field <?php if($admin_lang == $key){ ?>form-required<?php } ?> term-name-wrap">
				<label><?php _ex( 'Name', 'term name' ); ?> (<?php echo get_title_forkey($key); ?>)</label>
				<input name="tag_name_ml_<?php echo $key; ?>" <?php if($admin_lang == $key){ ?>class="terms_name_val" aria-required="true"<?php } ?> type="text" value="" size="40" />
				<p><?php _e('The name is how it appears on your site.'); ?></p>
			</div>		
		<?php 
		} 
		foreach($langs as $key){ ?>
			<div class="form-field term-name-wrap">
				<label><?php _e( 'Description' ); ?> (<?php echo get_title_forkey($key); ?>)</label>
				<textarea name="description_ml_<?php echo $key; ?>" rows="5" cols="40"></textarea>
				<p><?php _e('The description is not prominent by default; however, some themes may show it.'); ?></p>
			</div>		
		<?php 
		} 		
	}  
	 
 	function created_tags_ml($id){
		global $wpdb;
		
		if(isset($_POST['tag_ml_filter'])){
			
			$name = pn_strip_input(is_param_post_ml('tag_name_ml'));
			$wpdb->update($wpdb->prefix ."terms", array('name' => $name), array('term_id' => $id));
			
			$description = pn_strip_text(is_param_post_ml('description_ml'));
			$wpdb->update($wpdb->prefix ."term_taxonomy", array('description' => $description), array('term_id' => $id));
		
		}
	}	
	
 	function edit_form_fields_ml($tag){
		?>
		<script type="text/javascript">
		jQuery(function($){
			$('#name').parents('.form-field').hide();
			$('#description').parents('.form-field').hide();
			
			$('.terms_name_val').change(function(){
				var vale = $(this).val();
				$('#name').val(vale);
			});
		});		
		</script>
		
		<input type="hidden" name="tag_ml_edit" value="1" />
		
		<table class="form-table">
		<?php 
		$admin_lang = get_admin_lang();
		$langs = get_langs_ml();
		
		$name_to_pattern = get_value_ml($tag->name);
		$description_to_pattern = get_value_ml($tag->description);
		
		foreach($langs as $key){ 
			$val = is_isset($name_to_pattern, $key);
		?>
			<tr class="form-field <?php if($admin_lang == $key){ ?>form-required<?php } ?> term-name-wrap">
				<th scope="row"><label><?php _ex( 'Name', 'term name' ); ?> (<?php echo get_title_forkey($key); ?>)</label></th>
				<td><input name="tag_name_ml_<?php echo $key; ?>" type="text" value="<?php echo pn_strip_input($val); ?>" size="40" <?php if($admin_lang == $key){ ?>class="terms_name_val" aria-required="true"<?php } ?> />
				<p class="description"><?php _e('The name is how it appears on your site.'); ?></p></td>
			</tr>			
		<?php 
		}	

		foreach($langs as $key){ 
			$val = is_isset($description_to_pattern, $key);
		?>
			<tr class="form-field form-required term-name-wrap">
				<th scope="row"><label><?php _e( 'Description' ); ?> (<?php echo get_title_forkey($key); ?>)</label></th>
				<td>
				<textarea name="description_ml_<?php echo $key; ?>" rows="5" cols="50" class="large-text"><?php echo pn_strip_text($val); ?></textarea>
				<p class="description"><?php _e('The description is not prominent by default; however, some themes may show it.'); ?></p></td>
			</tr>			
		<?php 
		} 		
		
		?>
		</table>
		<?php	
	} 
	
 	function edit_tags_ml($id){
		global $wpdb;
		
		if(isset($_POST['tag_ml_edit'])){
			
			$name = pn_strip_input(is_param_post_ml('tag_name_ml'));
			$wpdb->update($wpdb->prefix ."terms", array('name' => $name), array('term_id' => $id));
			
			$description = pn_strip_text(is_param_post_ml('description_ml'));
			$wpdb->update($wpdb->prefix ."term_taxonomy", array('description' => $description), array('term_id' => $id));
		
		} 
		
	}		
	
 	function pn_adminpage_style_post_ml(){
	?>
	input.post_title_multi {
		padding: 3px 8px;
		font-size: 1.7em;
		line-height: 100%;
		height: 1.7em;
		width: 100%;
		outline: none;
		margin: 0;
		background-color: #fff;
	}	
	#titlewrap, #postdivrich{
		display: none!important;	
	}		
	<?php		
	}	
	
 	function edit_form_before_permalink_mli($post){
		if(isset($post->post_type)){
			if($post->post_type != 'attachment'){
		?>
			<input type="hidden" name="title_ml_admin" value="1" />
				
			<?php 
			$admin_lang = get_admin_lang();
			$langs = get_langs_ml();

			$title_to_pattern = get_value_ml(esc_attr( htmlspecialchars( $post->post_title ) ));
			$title_placeholder = apply_filters( 'enter_title_here', __( 'Enter title here' ), $post );
			foreach($langs as $key){
				$val = is_isset($title_to_pattern, $key);
				?>
					
				<div style="margin: 0 0 10px 0;">
					<div style="margin: 0 0 3px 0; font-weight: 600;"><?php echo $title_placeholder; ?> (<?php echo get_title_forkey($key); ?>)</div>
					<input type="text" name="title_ml_<?php echo $key; ?>" size="30" class="post_title_multi <?php if($admin_lang == $key){ ?>post_title_currentlang<?php } ?>" value="<?php echo $val; ?>" spellcheck="true" autocomplete="off" />
				</div>
					
				<?php
			} 
			?>
		<?php
			}
		}
	} 
	
  	function edit_form_after_title_ml($post){
		if(isset($post->post_type)){
			if($post->post_type != 'attachment'){
					
		?>
			<div style="padding: 10px 0 0 0;">
				<input type="hidden" name="content_ml_admin" value="1" />
				
				<?php pn_editor_ml('', 'content_ml', $post->post_content, 24, true); ?>	
			</div>	
		<?php
			} 	
		}
	} 	
	
	function new_excerpt_metabox_ml($post_id) {
		if (function_exists("add_meta_box")) {
			$args = array('public' => true, 'capability_type' => 'post' );
			$post_types = get_post_types($args,'names');
			if(is_array($post_types)){
				foreach($post_types as $pt){
					if($pt != 'attachment'){
						add_meta_box("pn_excerpt_id", __('Excerpt'), "excerpt_box_ml", $pt, "normal");
					}
				}
			}
		}
	} 

  	function excerpt_box_ml($post){
		$post_id = $post->ID; 				
		?>
		<input type="hidden" name="excerpt_ml_admin" value="1" />
		<?php pn_textarea_ml('', 'excerpt_ml', $post->post_excerpt, '', '100px'); ?>		
		<?php 
	}  	
	
	function edit_post_ml($post_id){
		if(!current_user_can('edit_post', $post_id )){
			return $post_id;
		}
		global $wpdb;
		$post = get_post($post_id);
		if(isset($post->ID)){
			$parr = array();
			if(isset($_POST['title_ml_admin'])){					
				$parr['post_title'] = pn_strip_input(is_param_post_ml('title_ml'));					
			}
			if(isset($_POST['content_ml_admin'])){
				$parr['post_content'] = pn_strip_text(is_param_post_ml('content_ml'));			
			}	
			if(isset($_POST['excerpt_ml_admin'])){
				$parr['post_excerpt'] = pn_strip_text(is_param_post_ml('excerpt_ml'));			
			}
			if(count($parr) > 0){
				$wpdb->update($wpdb->prefix ."posts", $parr, array('ID' => $post_id)); 
			}
		}
		
	} 
	
 	function pn_adminpage_js_multi(){
	?>
		$('.post_title_currentlang').change(function(){
			var vale = $(this).val();
			$('#title').val(vale);
		});		
		function ml_resize(){
			if($('.multi_wrapper:visible').length > 0){
				$('.multi_wrapper:first:visible').each(function(){
					var nw = $(this).width();
					var abs_w = 0;
					$(this).find('.tab_multi_title:visible').each(function(){
						abs_w = abs_w + parseInt($(this).css('width')) + parseInt($(this).css('padding-left')) + parseInt($(this).css('padding-right'));
					});
					abs_w = abs_w + parseInt($(this).find('.clear_multi_title').css('width')) + parseInt($(this).find('.clear_multi_title').css('margin-left'));
						
					if(abs_w > nw){
						$('.tab_multi_title:visible').addClass('minimize');
					} 
				});
			}
		}
		ml_resize();
		$(window).on('resize', function(){
			ml_resize();
		});
		$(window).on('load', function(){
			ml_resize();
		});			
		$('.tab_multi_title').on('click',function(){
			var id = $(this).attr('name');
			var par = $(this).parents('.multi_wrapper');
			par.find('.tab_multi_title').removeClass('active');
			$(this).addClass('active');
			par.find('.premium_wrap_multi').removeClass('active');
			$('#'+id).addClass('active');
			return false;
		});
		$('.clear_multi_title').on('click',function(){
			var par = $(this).parents('.multi_wrapper');
			par.find('input[type=text], textarea').val('');
			return false;
		});	
	<?php		
	} 
		
if(is_admin()){
	
	add_action('pn_adminpage_js', 'pn_adminpage_js_multi');
		
	add_action('edit_form_before_permalink', 'edit_form_before_permalink_mli' );
	add_action('edit_form_after_title', 'edit_form_after_title_ml' );
	
	add_action("admin_menu", "new_excerpt_metabox_ml", 0);
	add_action("edit_post", "edit_post_ml");
	
	add_action('tag_row_actions','hide_inline_action');	
	
	add_action('init','all_posttypes_set_langs', 10000);
	function all_posttypes_set_langs(){
		$args = array('public' => true);
		$post_types = get_post_types($args,'names');
		if(is_array($post_types)){
			foreach($post_types as $pt){
				if($pt != 'attachment'){
					add_action($pt.'_row_actions','hide_inline_action');
					add_action('pn_adminpage_style_'.$pt,'pn_adminpage_style_post_ml');
				}
			}
		}
		
		$taxonomies=get_taxonomies('','objects');
	    if(is_array($taxonomies)){
			$not = array('nav_menu','link_category','post_format');
	        foreach($taxonomies as $tax){    
		        $name = $tax->name;
		        if(!in_array($name,$not)){
					add_action($name.'_add_form_fields', 'add_form_fields_ml');
					add_action($name.'_edit_form', 'edit_form_fields_ml');
					add_action('edit_'.$name, 'edit_tags_ml');
					add_action('created_'.$name, 'created_tags_ml');
				}
			}
		}
	}
	
	add_filter('term_name',	'ctv_ml',0);
	add_filter('the_category', 'ctv_ml',0);
	add_filter( 'list_cats', 'ctv_ml', 0 );
	add_filter('term_description',	'ctv_ml',0);
	/*
	add_filter('terms_to_edit',	'ctv_ml',0);
	add_filter('get_terms', 'get_the_terms_ml', 0 );
	*/
	
} else {  

	add_filter('self_link', 'convert_link_ml');
	add_filter('term_link',	'convert_link_ml');	
	add_filter('author_feed_link','convert_link_ml');
	add_filter('author_link','convert_link_ml');
	add_filter('day_link',	'convert_link_ml');
	add_filter('get_comment_author_url_link','convert_link_ml');
	add_filter('month_link','convert_link_ml');
	add_filter('page_link',	'convert_link_ml');
	add_filter('post_link',	'convert_link_ml');
	add_filter('post_type_link', 'convert_link_ml');
	add_filter('year_link',	'convert_link_ml');
	add_filter('category_link',	'convert_link_ml');
	add_filter('tag_link','convert_link_ml');
	add_filter('the_permalink',	'convert_link_ml');
	add_filter('get_comment_link',	'convert_link_ml',0);
	add_filter('category_feed_link', 'convert_link_ml');
	add_filter('feed_link',	'convert_link_ml');
	add_filter('get_comments_pagenum_link',	'convert_link_ml');
	add_filter('cancel_comment_reply_link',	'convert_link_ml');
	add_filter('tag_feed_link',	'convert_link_ml');
	add_filter('get_pagenum_link',	'convert_link_ml');
	add_filter('post_comments_feed_link', 'convert_link_ml');
	add_filter('get_bloginfo_rss', 'get_bloginfo_rss_ml', 0, 2 );
	add_filter('get_the_terms', 'get_the_terms_ml', 0);
	add_filter('get_term', 'get_term_ml', 0);
	
}	
	
/*
временная заплатка
*/
function the_title_temporary_patch($string){ 
	if(is_admin() and function_exists('get_current_screen')){
		$screen = get_current_screen();
		if(isset($screen) and isset($screen->id) and $screen->id == 'nav-menus'){
			return $string;
		} 
	} 
		return ctv_ml($string);
}	

add_filter('the_title',	'the_title_temporary_patch', 0);	
add_filter('option_blogname', 'ctv_ml', 0 );
add_filter('option_blogdescription', 'ctv_ml', 0 ); 
add_filter('get_the_excerpt',	'ctv_ml', 0);
add_filter('the_content',	'ctv_ml', 0);
add_filter('the_excerpt_rss','ctv_ml', 0);
add_filter('single_post_title',	'ctv_ml',0);
add_filter('get_wp_title_rss',	'ctv_ml',0);
add_filter('wp_title_rss',	'ctv_ml',0);
add_filter('the_title_rss',	'ctv_ml',0);
add_filter('the_content_rss',	'ctv_ml',0);
add_filter('get_pages',	'get_the_terms_ml',0);