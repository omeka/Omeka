        </div><!-- end content -->

        <div id="footer">

            <ul class="navigation">
                <?php echo public_nav_main(array(
                        array('label' => __('Home'), 
                              'uri'=> url('')), 
                        array('label' => __('Browse Items'), 
                              'uri' => url('items')), 
                        array('label' => __('Browse Collections'), 
                              'uri' => url('collections')))); ?>
            </ul>

            <div id="footer-text">
                <?php echo get_theme_option('Footer Text'); ?>
                <?php if ((get_theme_option('Display Footer Copyright') == 1) && $copyright = option('copyright')): ?>
                    <p><?php echo $copyright; ?></p>
                <?php endif; ?>
                <p><?php echo __('Proudly powered by <a href="http://omeka.org">Omeka</a>.'); ?></p>
            </div>

            <?php fire_plugin_hook('public_theme_footer'); ?>

        </div><!-- end footer -->
    </div><!-- end wrap -->
</body>
</html>
