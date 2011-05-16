</div>
<div id="footer" class="group">
    <?php admin_plugin_footer(); ?>
    <p><a href="http://omeka.org" id="omeka-logo">Powered by Omeka</a> | <a href="http://omeka.org/codex/">Documentation</a> | <a href="http://omeka.org/forums/">Support Forums</a></p>
    <p id="system-info">
     Version <?php echo OMEKA_VERSION; ?> 
    <?php if (get_option('display_system_info') 
           && has_permission('SystemInfo', 'index')): ?>
        | <a href="<?php echo html_escape(uri('system-info')); ?>">More information about your system</a>
    <?php endif; ?>
    </p>
</div>
</div>
</body>
</html>
