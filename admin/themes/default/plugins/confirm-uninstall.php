<?php 
$pageTitle = __('Uninstall Plugin: %s', $plugin->getDisplayName());
echo head(array('title'=>$pageTitle, 'bodyclass'=>'plugins'));
echo flash();
?>
<section class="six columns alpha">
    <p><strong><?php echo __('Are you sure you want to uninstall %s?', $plugin->getDisplayName()); ?></strong></p>
    <?php if ($message): ?>
    <?php echo __($message); ?>
    <?php endif; ?>
    <form action="<?php echo html_escape(url('plugins/uninstall')); ?>" method="post">
        <p><input type="checkbox" name="confirm" /> <?php echo __('Yes, I want to uninstall this plugin.'); ?></p>
        <input type="hidden" name="name" value="<?php echo html_escape($plugin->getDirectoryName()); ?>" />

        <p id="uninstall-confirm">
            <input type="submit" class="uninstall small red button" name="uninstall-confirm" class="foo" value="<?php echo __('Uninstall'); ?>" />
            <span><?php echo __('or'); ?> <?php echo link_to('plugins', 'browse', __('Cancel')); ?></span>
        </p>
        <?php echo $csrf; ?>
    </form>
</section>

<?php echo foot(); ?>
