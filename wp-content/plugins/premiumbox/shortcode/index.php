<?php
if( !defined( 'ABSPATH')){ exit(); }

/* standart shortcodes */
add_action('pn_adminpage_quicktags','pn_adminpage_quicktags_standart');
function pn_adminpage_quicktags_standart(){
?>
edButtons[edButtons.length] = 
new edButton('premium_copyright_year', '<?php _e('Year','pn'); ?>','[copyright year=""]');

edButtons[edButtons.length] = 
new edButton('premium_h2', 'H2','<h2>','</h2>');

edButtons[edButtons.length] = 
new edButton('premium_h3', 'H3','<h3>','</h3>');

edButtons[edButtons.length] = 
new edButton('premium_from_user', '<?php _e('Users only','pn'); ?>','[from_user]','[/from_user]');

edButtons[edButtons.length] = 
new edButton('premium_from_guest', '<?php _e('Guests only','pn'); ?>','[from_guest]','[/from_guest]');

edButtons[edButtons.length] = 
new edButton('premium_classblock', '<?php _e('CSS class','pn'); ?>','[infobl class=""]','[/infobl]');
<?php	
}

function shortcode_copyright($atts,$content=""){ 
	$year = is_isset($atts, 'year');
	if(!$year){ $year = date('Y'); }
    return get_copy_date($year);
}
add_shortcode('copyright', 'shortcode_copyright');

function shortcode_from_user($atts,$content=""){ 
global $user_ID;
    if($user_ID){
        return $content;
    } 
}
add_shortcode('from_user', 'shortcode_from_user');

function shortcode_from_guest($atts,$content=""){ 
global $user_ID;
    if(!$user_ID){
        return $content;
    } 
}
add_shortcode('from_guest', 'shortcode_from_guest');

function shortcode_infobl($atts,$content=""){ 

    $class = is_isset($atts, 'class');
    return '<div class="infobl '. $class .'">'. $content . '</div>';
}
add_shortcode('infobl', 'shortcode_infobl');
/* end standart shortcodes */