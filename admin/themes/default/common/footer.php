        </div>
        
    </section>

    <footer class="container">
    
        <div class="ten columns offset-by-two">
        <?php fire_plugin_hook('admin_theme_footer'); ?>
        <p class="left"><a href="http://www.omeka.org" target="_blank"><?php echo __('Powered by Omeka') ?></a> | <a href="http://omeka.org/codex/" target="_blank"><?php echo __('Documentation'); ?></a> | <a href="http://omeka.org/forums/" target="_blank"><?php echo __('Support Forums'); ?></a></p>
        
        <p class="right"><?php echo __('Version %s', OMEKA_VERSION); ?>
        <?php if (get_option('display_system_info') && is_allowed('SystemInfo', 'index')): ?>
        | <a href="<?php echo html_escape(url('system-info')); ?>"><?php echo __('More information about your system'); ?></a></p>
        <?php endif; ?>
        </div>
    </footer>

</section>

</body>

</html>
