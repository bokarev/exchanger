<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_quicktags_pn_add_naps','pn_adminpage_quicktags_page_mobile');
add_action('pn_adminpage_quicktags_pn_naps_temp','pn_adminpage_quicktags_page_mobile');
add_action('pn_adminpage_quicktags_page','pn_adminpage_quicktags_page_mobile');
function pn_adminpage_quicktags_page_mobile(){
?>

edButtons[edButtons.length] = 
new edButton('premium_from_web', '<?php _e('Original verison only','pn'); ?>','[from_web]','[/from_web]');

edButtons[edButtons.length] = 
new edButton('premium_from_mobile', '<?php _e('Mobile version only','pn'); ?>','[from_mobile]','[/from_mobile]');
<?php	
}

function shortcode_from_web($atts,$content=""){ 
    if(!is_mobile()){
        return $content;
    } 
}
add_shortcode('from_web', 'shortcode_from_web');

function shortcode_from_mobile($atts,$content=""){ 
    if(is_mobile()){
        return $content;
    } 
}
add_shortcode('from_mobile', 'shortcode_from_mobile');