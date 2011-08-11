</div></div>

<div id="footer" class="sitemap">

	<div id="footer-content" class="center-div">
	    <div id="custom-footer-text">
	<?php if (get_theme_option('Footer Text')): ?>
	        <p><?php echo get_theme_option('Footer Text'); ?></p>
	<?php endif; ?>
            <?php if ((get_theme_option('Display Footer Copyright') == 1) && $copyright = settings('copyright')): ?>
                <p><?php echo $copyright; ?></p>
            <?php endif; ?>
		<ul class="navigation">
			<?php echo public_nav_main(array('Home' => uri(''), 'Browse Items' => uri('items'), 'Browse Collections'=>uri('collections')));
        	?>
		</ul>

		<p class=omeka>Proudly powered by <a href="http://omeka.org">Omeka</a></p>
	<?php echo plugin_footer(); ?>

	    </div>

		
	</div><!-- end footer-content -->
	

</div><!-- end footer -->

</div><!--end wrap-->

</body>

</html>