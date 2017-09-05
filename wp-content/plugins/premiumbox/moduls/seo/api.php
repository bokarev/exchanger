<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('myaction_request_sitemap','def_myaction_request_sitemap');
function def_myaction_request_sitemap(){
global $wpdb, $user_ID, $premiumbox;

header("Content-Type: text/xml");
$site_url = get_site_url_ml();
?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?><?xml-stylesheet type="text/xsl" href="'. $premiumbox->plugin_url .'moduls/seo/sm_style/sitemap.xsl"?>'; ?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">	
    <url>
		<loc><?php echo $site_url; ?></loc>
		<changefreq>daily</changefreq>
		<priority>1.0</priority>
	</url>	
<?php
if($premiumbox->get_option('up_mode') != 1){
if($premiumbox->get_option('xmlmap','pages') == 1){
	$ex = get_option('the_pages');
	$exclude_pages = $premiumbox->get_option('xmlmap','exclude_page');
	if(!is_array($exclude_pages)){ $exclude_pages = array(); }
	$exclude_pages[] = $ex['home'];
	$exclude = join(',',$exclude_pages);
	
	$args = array(
		'post_type' => 'page',
		'posts_per_page' => '-1',
		'exclude' => $exclude
	);			
	$mposts = get_posts($args);	
	foreach($mposts as $mpos){
?>
	<url>
		<loc><?php echo get_permalink($mpos->ID); ?></loc>
		<changefreq>daily</changefreq>
		<priority>0.6</priority>
	</url>
<?php		
	}	

}

if($premiumbox->get_option('xmlmap','exchanges') == 1){

	$show_data = pn_exchanges_output('smxml');
	if($show_data['mode'] == 1){

		$where = get_naps_where("smxml");
		$napobmens = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."naps WHERE $where ORDER BY site_order1 ASC");		
		foreach($napobmens as $item){
			$output = apply_filters('get_naps_output', 1, $item, 'smxml');
			if($output){
				$link = get_exchange_link($item->naps_name);
?>
	<url>
		<loc><?php echo $link; ?></loc>
		<changefreq>daily</changefreq>
		<priority>0.6</priority>
	</url>
<?php		
			}
		}	
	}
	
}

if($premiumbox->get_option('xmlmap','news') == 1){
	$args = array(
		'post_type' => 'post',
		'posts_per_page' => '-1',
	);			
	$mposts = get_posts($args);	
	foreach($mposts as $mpos){
?>
	<url>
		<loc><?php echo get_permalink($mpos->ID); ?></loc>
		<changefreq>daily</changefreq>
		<priority>0.6</priority>
	</url>
<?php		
	}	

}
}
?>
</urlset>
<?php
}