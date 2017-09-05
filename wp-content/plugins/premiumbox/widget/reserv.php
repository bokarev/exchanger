<?php
class pn_reserv_Widget extends WP_Widget {
	
 	public function __construct($id_base = false, $widget_options = array(), $control_options = array()){
		parent::__construct('get_pn_reserv', __('Currency reserve','pn'), $widget_options = array(), $control_options = array());
	}
	
	public function widget($args, $instance){
		extract($args);

		global $wpdb;	
		
		if(is_ml()){
			
			$lang = get_locale();
			$title = pn_strip_input(is_isset($instance,'title'.$lang));
			if(!$title){ $title = __('Currency reserve','pn'); }
			
		} else {
			
			$title = pn_strip_input(is_isset($instance,'title'));
			if(!$title){ $title = __('Currency reserve','pn'); }	
			
		}
		
		$vtype = intval(is_isset($instance,'vtype'));
		
		$output = is_isset($instance,'output');
		if(!is_array($output)){ $output = array(); }
		
		$datas = list_view_valuts($output);
		
		$reserv = '';
		$r=0;
		
		$vtypes = array();
		foreach($datas as $item){ $r++;
					
			$vt = $item['vtype'];
			$vtypes[$vt] = $vt;		
					
			if($r%2 == 0){
				$oddeven = 'even';
			} else {
				$oddeven = 'odd';
			}			
			
			$treserv = apply_filters('reserv_widget_one', '', $item, $r);
			if(!trim($treserv)){
				$treserv = '
					<div class="widget_reserv_line '. $oddeven .' widget_reserv_vt widget_reserv_vt_0 widget_reserv_vt_'. $vt .'">  
						<div class="widget_reserv_ico" style="background: url('. $item['logo'] .') no-repeat center center;"></div>
						<div class="widget_reserv_block">
							<div class="widget_reserv_title">
								'. $item['title'] .'
							</div>
							<div class="widget_reserv_sum">
								'. is_out_sum($item['reserv'], $item['decimal'], 'reserv') .'
							</div>
						</div>
							<div class="clear"></div>
					</div>		
				';
			}
			
			$reserv .= $treserv;
		} 		
		
		$vtype_filter = '';
		if($vtype){
			$vtype_filter .= '<div class="widget_reserv_filters">';
			$vtype_filter .= '<div class="widget_reserv_filter current" data-id="0"><span>'. __('All','pn') .'</span></div>';
			foreach($vtypes as $vt){
				$vtype_filter .= '<div class="widget_reserv_filter" data-id="'. $vt .'"><span>'. $vt .'</span></div>';
			}
			$vtype_filter .= '
				<div class="clear"></div>
			</div>';
		}
		
		$array = array(
			'[table]' => $reserv,
			'[title]' => $title,
			'[filter]' => $vtype_filter,
		);		
		
		$widget = '
			<div class="widget_reserv_div">
				<div class="widget_reserv_div_ins">
					<div class="widget_reserv_div_title">
						<div class="widget_reserv_div_title_ins">
							[title]
						</div>
					</div>
					
					[filter]
					
					[table]
					
				</div>
			</div>
		';		
		
		$widget = apply_filters('reserv_widget_block', $widget);
		$widget = get_replace_arrays($array, $widget);
		echo $widget;
	
	}


	public function form($instance){ 
	?>
		<?php if(is_ml()){ 
			$langs = get_langs_ml();
			foreach($langs as $key){
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title_'.$key); ?>"><?php _e('Title'); ?> (<?php echo get_title_forkey($key); ?>): </label><br />
			<input type="text" name="<?php echo $this->get_field_name('title'.$key); ?>" id="<?php $this->get_field_id('title'.$key); ?>" class="widefat" value="<?php echo is_isset($instance,'title'.$key); ?>">
		</p>		
			<?php } ?>
		
		<?php } else { ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>: </label><br />
			<input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php $this->get_field_id('title'); ?>" class="widefat" value="<?php echo is_isset($instance,'title'); ?>">
		</p>
		<?php } ?>
		
		<p>
			<label for="<?php echo $this->get_field_id('vtype'); ?>"><?php _e('Show currency code filter','pn'); ?>: </label><br />
			<select name="<?php echo $this->get_field_name('vtype'); ?>" autocomplete="off" id="<?php $this->get_field_id('vtype'); ?>">
				<option value="0" <?php selected(is_isset($instance,'vtype'), 0); ?>><?php _e('No','pn'); ?></option>
				<option value="1" <?php selected(is_isset($instance,'vtype'), 1); ?>><?php _e('Yes','pn'); ?></option>
			</select>
		</p>		
		
		<div style="padding: 0 0 2px 0;"><label><?php _e('Show currency reserve','pn'); ?>:</label></div>
		<div style="border: 1px solid #dedede; padding: 10px; margin: 0 0 10px 0;">
			<div style="max-height: 200px; overflow-y: scroll;" class="cf_div">
				<div><label style="font-weight: 500;"><input class="check_all" type="checkbox" name="0" value="0"> <?php _e('Check all/Uncheck all','pn'); ?></label></div>
				<?php
				global $wpdb;
				$output = is_isset($instance,'output');
				if(!is_array($output)){ $output = array(); }
				$valuts = list_view_valuts();
				foreach($valuts as $item){
				?>
					<div><label><input type="checkbox" name="<?php echo $this->get_field_name('output'); ?>[]" <?php if(in_array($item['id'], $output)){ ?>checked="checked"<?php } ?> value="<?php echo $item['id']; ?>"> <?php echo $item['title']; ?></label></div>
				<?php } ?>
			</div>
		</div>
		
		<script type="text/javascript">
		jQuery(function($){
			
			$('.check_all').on('change', function(){
				var par = $(this).parents('.cf_div');
				if($(this).prop('checked')){
					par.find('input').prop('checked',true);
				} else {
					par.find('input').prop('checked',false);
				}
			});
			
		});
		</script>		
	<?php
	} 
}

register_widget('pn_reserv_Widget');