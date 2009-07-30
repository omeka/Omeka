<?php head(array('title'=>"Confirm \"$plugin\" Uninstall", 'bodyclass'=>'plugins')); ?>

<div id="primary">
    <?php echo flash(); ?>
    <h2>Confirm "<?php echo $plugin; ?>" Uninstall</h2>
    <p><strong>Are you sure you want to uninstall this plugin?</strong></p>
    <?php if ($message): ?>
    <?php echo $message; ?>
    <?php endif; ?>
    <form action="<?php echo uri('plugins/uninstall'); ?>" method="post">
        <p><input type="checkbox" name="confirm" /> Yes, I want to uninstall this plugin.</p>
        <input type="hidden" name="name" value="<?php echo $plugin ?>" />
        <p><input type="submit" name="uninstall-confirm" class="uninstall submit-medium" value="Uninstall" /> or <a href="<?php echo uri('plugins') ?>">Cancel</a></p>
    </form>
</div>

<?php foot(); ?>