<?php
$pageTitle = __('Configure the &#8220;%s&#8221; Plugin', $plugin->getDisplayName());
head(array('title'=>$pageTitle, 'bodyclass'=>'plugins')); ?>
<h1><?php echo $pageTitle; ?></h1>
<div id="primary">
    <?php echo flash(); ?>
    <form method="post">
        <?php echo $pluginBroker->callHook('config_form', array(), $plugin); ?>
        <input type="submit" name="install_plugin" value="<?php echo __('Save Changes'); ?>" class="submit" />
    </form>
</div>

<?php foot(); ?>
