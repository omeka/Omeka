<?php head(array('title'=>'Plugin Configuration', 'bodyclass'=>'plugins')); ?>

<div id="primary">
    <?php echo flash(); ?>
    <h2>Please Configure The '<?php echo html_escape($plugin->getDisplayName()); ?>' Plugin</h2>
    <form method="post">
        <?php echo $pluginBroker->callHook('config_form', array(), $plugin); ?>
        <input type="submit" name="install_plugin" value="Save Changes" class="submit" />
    </form>
</div>

<?php foot(); ?>
