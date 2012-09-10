<?php
$pageTitle = __('Upgrade Omeka');
head(array('title' => $pageTitle));
?><?php if(!$success): ?>
    <h1><?php echo __('Omeka encountered an error when upgrading your installation.'); ?></h1>
    <p class="error_text"><?php echo html_escape($error); ?></p>
    <?php if ($debugMode): ?>
        <pre id="debug"><?php echo html_escape($trace); ?></pre>
    <?php endif; ?>
    <p class="instruction"><?php echo __('Please restore from your database backup and try again.'); ?>
        <?php echo
        __('If you have any questions please refer to <a href="http://omeka.org/codex">Omeka documentation</a> or post a message on the <a href="http://omeka.org/forums">Omeka forums</a>.'); ?>
        </p>
<?php else: ?>
    <h1><?php echo __('Omeka has upgraded successfully.'); ?></h1>
    <p><?php echo link_to_admin_home_page(__('Return to Dashboard')); ?></p>    
<?php endif; ?>
</div>
</body>
</html>
