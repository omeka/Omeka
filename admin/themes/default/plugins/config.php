<?php head(array('title'=>'Plugin Configuration', 'bodyclass'=>'plugins')); ?>

<div id="primary">
    <?php echo flash(); ?>
    <h2>Please Configure The '<?php echo html_escape($pluginInfo->name); ?>' Plugin</h2>
    <form method="post">
        <?php echo $config; ?>
        <input type="submit" name="install_plugin" value="Save Changes" class="submit submit-medium" />
    </form>
</div>

<?php foot(); ?>