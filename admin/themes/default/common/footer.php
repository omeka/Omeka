</div>
<div id="footer" class="group">
    <?php admin_plugin_footer(); ?>
    <p><a href="http://omeka.org" id="omeka-logo"><?php echo __('Powered by Omeka'); ?></a> | <a href="http://omeka.org/codex/"><?php echo __('Documentation'); ?></a> | <a href="http://omeka.org/forums/"><?php echo __('Support Forums'); ?></a></p>
    <p id="system-info">
     <?php echo __('Version %s', OMEKA_VERSION); ?> 
    <?php if (get_option('display_system_info') 
           && has_permission('SystemInfo', 'index')): ?>
        | <a href="<?php echo html_escape(uri('system-info')); ?>"><?php echo __('More information about your system'); ?></a>
    <?php endif; ?>
    </p>
</div>
</div>
</body>
</html>
