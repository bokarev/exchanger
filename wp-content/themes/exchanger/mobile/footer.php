<?php if( !defined( 'ABSPATH')){ exit(); } 

$mobile_change = get_option('mobile_change');
$array = array('ctext');
$change = array();
foreach($array as $opt){
	$change[$opt] = ctv_ml(is_isset($mobile_change,$opt));	
}	
?>
		</div>
	</div>

	<div class="footer">
		<?php if($change['ctext']){ ?>
			<div class="copyright"><?php echo apply_filters('comment_text',$change['ctext']); ?></div>
		<?php } ?>
		
		<div id="topped"><?php _e('to the top', 'pntheme'); ?></div>

		<a href="<?php echo web_vers_link(); ?>" class="webversion_link"><?php _e('Go to a Original version', 'pntheme'); ?></a>
	</div>
</div>

<?php wp_footer(); ?>

</body>
</html>