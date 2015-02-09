    </div>

    <footer class="container" role="contentinfo">
        <div class="ten columns offset-by-two">
        <?php fire_plugin_hook('admin_footer', array('view'=>$this)); ?>
        <p class="left"><a href="http://www.omeka.org" target="_blank"><?php echo __('Powered by Omeka') ?></a> | <a href="http://omeka.org/codex" target="_blank"><?php echo __('Documentation'); ?></a> | <a href="http://omeka.org/forums/" target="_blank"><?php echo __('Support Forums'); ?></a></p>

        <p class="right"><?php echo __('Version %s', OMEKA_VERSION); ?>
        <?php if (get_option('display_system_info') && is_allowed('SystemInfo', 'index')): ?>
        | <a href="<?php echo html_escape(url('system-info')); ?>"><?php echo __('System Information'); ?></a></p>
        <?php endif; ?>
        </div>
    </footer>
</div>
<script type="text/javascript">
jQuery(document).ready(function () {
    Omeka.runReadyCallbacks();
});
</script>
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
