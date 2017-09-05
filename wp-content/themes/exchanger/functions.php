<?php
if( !defined( 'ABSPATH')){ exit(); }

load_theme_textdomain( 'pntheme', get_template_directory() . '/lang' );

function my_template($page){
$pager = TEMPLATEPATH . "/".$page.".php";
    if(file_exists($pager)){
        include($pager);
    }
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_inactive( 'premiumbox/premiumbox.php' ) and !is_admin() ) {
	header('Content-Type: text/html; charset=utf-8');
	$text = trim(get_option('pn_update_plugin_text'));
	if(!$text){ $text = __('Dear users, right now our website is updating. Please come back later.','pntheme'); }
	?>
	<div style="border: 1px solid #ff0000; padding: 10px 15px; font: 13px Arial; width: 500px; border-radius: 3px; margin: 0 auto; text-align: center;">
		<?php echo apply_filters('comment_text', $text); ?>
	</div>	
	<?php
	exit;
}

if ( is_plugin_inactive( 'premiumbox/premiumbox.php' )) {
	return;
}

my_template('includes/sites_func');
my_template('includes/breadcrumb');
my_template('includes/api');

my_template('change/header');
my_template('change/home');
my_template('change/footer');

my_template('mail/index');