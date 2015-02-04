        </article>

        <footer>

            <nav id="bottom-nav">
                <?php echo public_nav_main(); ?>
            </nav>

            <div id="footer-text">
                <?php echo get_theme_option('Footer Text'); ?>
                <?php if ((get_theme_option('Display Footer Copyright') == 1) && $copyright = option('copyright')): ?>
                    <p><?php echo $copyright; ?></p>
                <?php endif; ?>
                <p><?php echo __('Proudly powered by <a href="http://omeka.org">Omeka</a>.'); ?></p>
            </div>

            <?php fire_plugin_hook('public_footer', array('view'=>$this)); ?>

        </footer>

    </div><!-- end wrap -->
<script>
  $(document).ready(function() {
    // add a click handler to all links
    // that point to same-page targets (href="#...")
    $("a[href^='#']").click(function() {
      // get the href attribute of the internal link
      // then strip the first character off it (#)
      // leaving the corresponding id attribute
      $("#"+$(this).attr("href").slice(1)+"")
        // give that id focus (for browsers that didn't already do so)
        .focus()
        // add a highlight effect to that id (comment out if not using)
        //.effect("highlight", {}, 3000);
    });
  });
</script>

</body>
</html>
