<?php
if( !defined( 'ABSPATH')){ exit(); }
 
function bread_page($id,$array,$pof, $fid){
	
	if(!is_array($array)){ $array = array(); }
	
    if($id){
		$id = intval($id);
		
        global $wpdb;
        $post_data = $wpdb->get_row("SELECT ID, post_title, post_parent FROM ".$wpdb->prefix."posts WHERE post_type='page' AND post_status='publish' AND ID='$id'");
        
		if(isset($post_data->ID)){
			if($post_data->ID != $pof and $post_data->ID != $fid){
				$array[]= '<li><a href="'. get_permalink($post_data->ID) .'" itemprop="url"><span itemprop="title">'. pn_strip_input(ctv_ml($post_data->post_title)) .'</span></a></li>';
			}
			
			bread_page($post_data->post_parent,$array,$pof,$fid);			
		}
		
    } else {
        $array = array_reverse($array);
        foreach($array as $sarray){
            echo $sarray;
        }
    } 
}
 
function the_breadcrumb(){
global $post; 
  
 	$pof = get_option('page_on_front');
    $sof = get_option('show_on_front');	
	$home_url = get_site_url_ml();
    if($sof == 'page'){
        $blog_url = get_permalink(get_option('page_for_posts'));
    } else {
        $blog_url = $home_url;
    }	
	?>
	<ul>
		<li class="first"><a href="<?php echo $home_url;?>" itemprop="url"><span itemprop="title"><?php _e('Home','pntheme'); ?></span></a></li>
		
	    <?php if(is_singular('post') or is_tag() or is_category()){ ?>
			<li><a href="<?php echo $blog_url; ?>" itemprop="url"><span itemprop="title"><?php _e('News','pntheme'); ?></span></a></li>		
		<?php } elseif(is_page()){ ?>
			<?php bread_page($post->ID,'',$pof, $post->ID); ?>
		<?php } ?>
		
			<div class="clear"></div>
	</ul>
	<?php	
}