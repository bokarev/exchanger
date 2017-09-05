<?php 
if( !defined( 'ABSPATH')){ exit(); }

add_action('live_change_html','js_select_live');
function js_select_live(){
	?>
	$(document).Jselect();
	<?php
}