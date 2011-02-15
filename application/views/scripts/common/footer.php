</div><!-- end content -->

<div id="footer">
	
    <ul class="navigation">
		<?php echo public_nav_main(array('Home' => uri(''), 'Browse Items' => uri('items'), 'Browse Collections'=>uri('collections')));
    	?>
    </ul>

    <div id="footer-text">
        <?php echo get_theme_option('Footer Text'); ?>
        <?php if ((get_theme_option('Display Footer Copyright') == 1) && $copyright = settings('copyright')): ?>
            <p><?php echo $copyright; ?></p>
        <?php endif; ?>
        <p>Proudly powered by <a href="http://omeka.org">Omeka</a>.</p>
    </div>

	<?php plugin_footer(); ?>
	
</div><!-- end footer -->
</div><!-- end wrap -->
</body>
</html>