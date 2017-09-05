<?php
/* защита от прямого обращения */
if( !defined( 'ABSPATH')){ exit(); }

/*
Фиксим все наши меню
*/
if(!function_exists('pn_wp_nav_menu_args')){
	add_filter( 'wp_nav_menu_args', 'pn_wp_nav_menu_args' );
	function pn_wp_nav_menu_args( $args = '' ){
		$args['container'] = false;
		return $args;
	} 

	add_filter( 'wp_nav_menu_objects', 'pn_wp_nav_menu_objects' );
	function pn_wp_nav_menu_objects( $items ) {
		
		$parents = array();
		$firstul = array();
		foreach ( $items as $item ) {
			$parents[] = $item->menu_item_parent;
			if($item->menu_item_parent == 0){
				$firstul[] = $item->ID;
			}
		}

		$first_class = $last_class = '';
		$r=0;
		$count_first = count($firstul);
		foreach($firstul as $fi){ $r++;
			if($r == 1){
				$first_class = $fi;
			}
			if($r == $count_first){
				$last_class = $fi;
			}
		}
		
		foreach ( $items as $item ) {
			$classed = '';
			if ( in_array( $item->ID, $parents ) ) {
				$classed .= 'has_sub_menu'; 
			}
			if($item->ID == $first_class){
				$classed .= ' first_menu_li';
			}
			if($item->ID == $last_class){
				$classed .= ' last_menu_li';
			}		
				
			$item->classes[] = $classed;
		}
		
		return $items;    
	}

	add_filter('nav_menu_item_title', 'pn_nav_menu_item_title');
	function pn_nav_menu_item_title($title){
		return '<span>'. $title .'</span>';
	}
	
	if(function_exists('is_ml') and is_ml()){
		add_action('pn_adminpage_js_nav-menus','premium_pn_adminpage_js_nav_menus');
		function premium_pn_adminpage_js_nav_menus(){
			?>
			function premium_construct_menu(){
				var title = '';
				$('.js_construct_menu').each(function(){
					var k = $(this).attr('data-key');
					var d = $.trim($(this).val());
					if(d.length > 0){
						title = title + '[' + k + ':]' + d + '[:' + k + ']';
					}
				});
				$('.js_construct_menu_title').val(title);
			}
			$('.js_construct_menu').on('change', function(){
				premium_construct_menu();
			});
			$('.js_construct_menu').on('keyup', function(){
				premium_construct_menu();
			});			
			<?php
		}
		
		add_action("admin_menu", "premium_menu_def_init"); 
		function premium_menu_def_init() { 
			add_meta_box('premium_constcruct_accordion_title', __('Title designer','premium'), 'premium_construct_accordion', 'nav-menus', 'side', 'low'); 
		} 

		function premium_construct_accordion() { 
			?>
			<div style="margin: 0 0 5px 0; padding: 0 0 5px 0; border-bottom: 1px solid #ccc;">
				<input type="text" name="" class="js_construct_menu_title premium_input big_input" value="" autocomplete="off" />
			</div>
			<?php
			$langs = get_langs_ml();
			foreach($langs as $key){
				?>
				<div style="margin: 0 0 5px 0;">
					<div style="margin: 0 0 3px 0; font-weight: 600;"><?php echo get_title_forkey($key); ?></div>
					<input type="text" name="" class="js_construct_menu premium_input big_input" data-key="<?php echo $key; ?>" value="" autocomplete="off" />
				</div>
				<?php
			}
		} 	
	}
}