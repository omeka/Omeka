<?php

    $displayName = $plugin->getDisplayName();

    $requiredPluginDirNames = $plugin->getRequiredPlugins();
    $missingPluginNames = array();
    foreach($requiredPluginDirNames as $requiredPluginDirName):
        $requiredPlugin = $loader->getPlugin($requiredPluginDirName);
        if (!$requiredPlugin):
            $missingPluginNames[] = '"' . $requiredPluginDirName . '"';
        elseif (!$requiredPlugin->isLoaded()):
            $missingPluginNames[] = '"' . $requiredPlugin->getDirectoryName() . '"';
        endif;
    endforeach;

    $trClassName = null;
    if ($plugin->hasNewVersion()):
        $trClassName = "upgrade-plugin";
    endif;

    $loadErrors = array();
    if (!$plugin->meetsOmekaMinimumVersion()):
        $minVersion = $plugin->getMinimumOmekaVersion();
        $loadErrors[] = __('This version of %1$s requires at least Omeka %2$s.', $displayName, $minVersion);
    endif;
    if ($missingPluginNames):
        $loadErrors[] = __('%1$s requires the following plugins to be installed, activated, and loaded: %2$s', html_escape($displayName), implode(', ', $missingPluginNames));
    endif;
    if (!$loader->hasPluginBootstrap($pluginDirName)):
        $loadErrors[] = __('The plugin.php file is missing.');
    endif;
        
    $cannotLoad = !empty($loadErrors);
?>
<tr <?php if ($trClassName) { echo "class=\"$trClassName\""; } ?>>
    <td>
    <?php if ($cannotLoad): ?>
        <div class="warnings">
            <strong><?php echo __('The %s plugin cannot be loaded for the following reasons:', html_escape($displayName)); ?></strong>
            <ul>
            <?php foreach ($loadErrors as $error): ?>
                <li><?php echo html_escape($error); ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if ($plugin->hasNewVersion()): ?>    
        <p class="notice plugin-upgrade"><?php echo __('You have a new version of %s. Please upgrade!', html_escape($displayName)); ?></p>
    <?php endif; ?>
        <div class="plugin-info">
        <p class="plugin-title"><?php
            if ($plugin->getLinkUrl()):
                echo '<a href="' . html_escape($plugin->getLinkUrl()) . '">' . html_escape($displayName) . '</a>';
            else:
                echo html_escape($displayName);
            endif; 
            if (has_permission('Plugins', 'config') && $plugin->hasConfig() ): ?>
                <a href="<?php echo html_escape(uri('plugins/config', array('name'=>$plugin->getDirectoryName()))); ?>" class="configure-button button"><?php echo __('Configure'); ?></a>
            <?php endif; ?>
        </p>
        
        <?php 
            $pluginMetadata = array();
            if ($plugin->getIniVersion()):
                $pluginMetadata[] = __('Version %s', html_escape(trim($plugin->getIniVersion())));
            endif;
        
            if ($plugin->getAuthor()):
                $pluginMetadata[] = __('By %s', html_escape(trim($plugin->getAuthor())));
            endif;
            if (!empty($pluginMetadata)): ?>
                <p class="plugin-meta"><?php echo implode(' | ', $pluginMetadata); ?></p>
        <?php endif; ?>
        
        <?php if($pluginDescription = $plugin->getDescription()): ?>
        <p class="plugin-description"><?php echo html_escape($pluginDescription); ?></p>
        <?php endif; ?>
        </div>
    </td>
    
    <td>
    <?php if ($plugin->isInstalled()): ?>
        <?php if ($plugin->hasNewVersion()): ?>
            <?php if (has_permission('Plugins', 'upgrade')): ?>
                <form action="<?php echo html_escape(uri('plugins/upgrade')); ?>" method="post" accept-charset="utf-8">     
                    <div>
                        <button name="upgrade" type="submit" class="upgrade"<?php if ($cannotLoad): ?> disabled="disabled"<?php endif; ?>><?php echo __('Upgrade'); ?></button>
                        <input type="hidden" name="name" value="<?php echo html_escape($pluginDirName); ?>" />
                    </div>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <?php $activateOrDeactivate = ($plugin->isActive()) ? 'deactivate' : 'activate'; ?>
            <?php if (has_permission('Plugins', 'activate')): ?>
                <form action="<?php echo html_escape(uri('plugins/' . $activateOrDeactivate)); ?>" method="post" accept-charset="utf-8">
                    <div>
                        <button name="<?php echo $activateOrDeactivate; ?>" type="submit" class="<?php echo $activateOrDeactivate; ?>"<?php if ($cannotLoad): ?> disabled="disabled"<?php endif; ?>><?php echo ($plugin->isActive()) ? __('Deactivate') : __('Activate'); ?></button>
                        <input type="hidden" name="name" value="<?php echo html_escape($pluginDirName); ?>" />
                    </div>
                </form>                
            <?php endif; ?>
            <?php if (has_permission('Plugins', 'uninstall')): ?>
                <form action="<?php echo html_escape(uri(array(
                    'controller'=>'plugins',
                    'action'=>'uninstall'), 'default')); ?>" method="post" accept-charset="utf-8">
                    <div>
                        <button name="uninstall" type="submit" class="uninstall"<?php if ($cannotLoad): ?> disabled="disabled"<?php endif; ?>><?php echo __('Uninstall'); ?></button>
                        <input type="hidden" name="name" value="<?php echo html_escape($pluginDirName); ?>" />
                    </div>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    <?php else: //The plugin has not been installed yet ?>
    <?php if (has_permission('Plugins', 'install')): ?>
        <form action="<?php echo html_escape(uri('plugins/install')); ?>" method="post" accept-charset="utf-8">
                <div>
                    <button name="install" type="submit" class="install"<?php if ($cannotLoad): ?> disabled="disabled"<?php endif; ?>><?php echo __('Install'); ?></button>
                    <input type="hidden" name="name" value="<?php echo html_escape($pluginDirName); ?>" />
                </div>
        </form> 
    <?php endif; ?>
<?php endif; ?>

</td>
</tr>
