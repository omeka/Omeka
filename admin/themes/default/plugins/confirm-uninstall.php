<?php head(array('title'=>"Confirm '" . $pluginInfo->name . "' Uninstall", 'bodyclass'=>'plugins')); ?>

<div id="primary">
    <?php echo flash(); ?>
    <h2>Confirm '<?php echo html_escape($pluginInfo->name); ?>' Uninstall</h2>
    <p><strong>Are you sure you want to uninstall this plugin?</strong></p>
    <?php 
        if ($message) {
            echo html_escape($message);            
        }
    ?>
    <form action="<?php echo uri('plugins/uninstall'); ?>" method="post">
        <p><input type="checkbox" name="confirm" /> Yes, I want to uninstall this plugin.</p>
        <input type="hidden" name="name" value="<?php echo $pluginInfo->directoryName; ?>" />
        <p><input type="submit" name="uninstall-confirm" class="uninstall submit-medium" value="Uninstall" /> or <a href="<?php echo uri('plugins') ?>">Cancel</a></p>
    </form>
</div>

<?php foot(); ?>