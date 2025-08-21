<?php
$pageTitle = __('Configure Plugin: %s', $plugin->getDisplayName());
echo head(['title' => $pageTitle, 'bodyclass' => 'plugins']);
echo flash();
?>
<form method="post">
    <section class="seven columns alpha">
        <?php echo $pluginBroker->callHook('config_form', ['view' => $this], $plugin); ?>
    </section>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php
            echo $this->formSubmit(
                'install_plugin', __('Save Changes'),
                ['class' => 'submit big green button']);
            ?>
        </div>
    </section>
    <?php echo $csrf; ?>
</form>
<?php echo foot(); ?>
