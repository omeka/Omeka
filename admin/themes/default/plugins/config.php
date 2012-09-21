<?php
$pageTitle = __('Configure the &#8220;%s&#8221; Plugin', $plugin->getDisplayName());
echo head(array('title'=>$pageTitle, 'bodyclass'=>'plugins')); ?>
        
            <div class="six columns">

            <?php echo flash(); ?>
        
            <form method="post">
                <?php echo $pluginBroker->callHook('config_form', array(), $plugin); ?>
                <input type="submit" name="install_plugin" value="<?php echo __('Save Changes'); ?>" class="submit" />
            </form>
            
            </div>

<?php echo foot(); ?>
