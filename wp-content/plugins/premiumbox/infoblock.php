<?php
if( !defined( 'ABSPATH')){ exit(); }

add_action('pn_adminpage_footer','pn_adminpage_footer_infoblock');
function pn_adminpage_footer_infoblock($page){	
?>
    <div class="premium_footer">
		<div class="premium_footer_left">
			&copy; <?php echo get_copy_date('2015'); ?> <strong>Premium Exchanger</strong>. 
		</div>
		
		<!--
		<div class="premium_footer_right">
		    <ul>
				<li><a href="#">link 1</a></li>
				<li><a href="#">link 2</a></li>
				<li><a href="#">link 3</a></li>
				<li><a href="#">link 4</a></li>
				<li><a href="#">link 5</a></li>
				<li><a href="#">link 6</a></li>
				<li><a href="#">link 7</a></li>
			</ul>
				<div class="rclear"></div>
		</div>
		-->
 			<div class="rclear"></div>
	</div>
<?php	
}
