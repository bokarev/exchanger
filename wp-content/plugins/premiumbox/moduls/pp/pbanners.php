<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_title_pn_pbanners', 'def_adminpage_title_pn_pbanners');
function def_adminpage_title_pn_pbanners(){
	_e('Banners','pn');
}

add_action('pn_adminpage_content_pn_pbanners','def_adminpage_content_pn_pbanners');
function def_adminpage_content_pn_pbanners(){
	$banners = get_option('banners');
	$nbanners = apply_filters('pp_banners',array());
	
	$template = array(
		'before' => '<tr class="[class]">',
		'after' => '</tr>',
		'before_title' => '<th>',
		'after_title' => '</th>',
		'before_content' => '<td>',
		'after_content' => '</td>',
		'label' => 1,
	);	
?>	
<div class="premium_body">	
	<form method="post" action="<?php pn_the_link_post(); ?>">
		<table class="premium_standart_table">
			<?php
				pn_h3(__('Banners','pn'), __('Save','pn'), 2);
					
				pn_help(__('Shortcodes','pn'),'
				<p><input type="text" name="" value="[partner_link]" onclick="this.select()" /> - '. __('Partner link','pn') .'</p>
				<p><input type="text" name="" value="[url]" onclick="this.select()" /> - '. __('Website link','pn') .'</p>
				','',$template);
				
 				if(is_array($nbanners)){ 
				$r=0; 
					foreach($nbanners as $key => $val){ $r++;
			
					pn_line(2);
					?>
					<tr>
						<th><?php echo $val;?></th>
						<td>
							<?php 
							if(isset($banners[$key]) and is_array($banners[$key])){ 
								$text = $banners[$key];
								foreach($text as $tex){ 
									if($tex){ ?>				
										<div class="premium_wrap_standart">
											<textarea name="banner[<?php echo $key;?>][]" style="width: 100%; height: 60px;"><?php echo pn_strip_text($tex);?></textarea>
										
											<div class="plusminusblock">
												<a href="#" class="plminlink minussed"></a>
												<a href="#" class="plminlink plussed"></a>
													<div class="premium_clear"></div>
											</div>
												<div class="premium_clear"></div>
										</div>					
								<?php }
								}
							} ?>				
							
										<div class="premium_wrap_standart">
											<textarea name="banner[<?php echo $key;?>][]" style="width: 100%; height: 60px;"></textarea>
											
											<div class="plusminusblock">
												<a href="#" class="plminlink minussed"></a>
												<a href="#" class="plminlink plussed"></a>
													<div class="premium_clear"></div>
											</div>	
												<div class="premium_clear"></div>
										</div>								
						</td>
					</tr>			
				<?php	
					}
				} 				
				
				pn_h3('', __('Save','pn'), 2);	
			?>
		</table>
	</form>		
</div>	
<script type="text/javascript">
$(function(){
    $(document).on('click', '.plminlink', function(){ 
		var part = $(this).parents('td');
	    if($(this).hasClass('minussed')){
		    var cc = part.find('.premium_wrap_standart').length;
			if(cc > 1){
			    $(this).parents('.premium_wrap_standart').remove();
			}
		} else {
		    var pattern = part.find('.premium_wrap_standart:last').html();
			part.find('.premium_wrap_standart:last').after('<div class="premium_wrap_standart">'+ pattern +'</div>');
		}
	    return false;
	});
});
</script>	
<?php
}


add_action('premium_action_pn_pbanners','def_premium_action_pn_pbanners');
function def_premium_action_pn_pbanners(){
global $wpdb;	

	only_post();
	pn_only_caps(array('administrator','pn_pp'));
	
	$banners = array();
	$nbanners = apply_filters('pp_banners',array());

	if(is_array($nbanners)){ 
		foreach($nbanners as $key => $val){
			if(is_array($_POST['banner'][$key])){ 
				foreach($_POST['banner'][$key] as $tex){
					$tex = pn_strip_text($tex);
					if($tex){
						$banners[$key][] = $tex;
					}
				}
			}
		}		
	}

	update_option('banners', $banners);				
			
	$url = admin_url('admin.php?page=pn_pbanners&reply=true');
	wp_redirect($url);
	exit;
}