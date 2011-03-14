<?php

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
    if ($plugin->isInstalled() && $plugin->isActive() && !$plugin->isLoaded()):
        if (($plugin->meetsOmekaMinimumVersion() || $missingPluginNames)):
            $trClassName = "plugin-load-error"; 
        elseif ($plugin->hasNewVersion()): 
            $trClassName = "upgrade-plugin";
        endif;
    endif;
?>
<tr <?php if ($trClassName): ?>class="<?php echo $trClassName; ?>"<?php endif; ?>>
    <td>
        <?php if ($plugin->isInstalled() && $plugin->isActive() && !$plugin->isLoaded()): ?>
                <div class="warnings">
                    <strong><?php echo __('Warning! The %s plugin could not be loaded for the following reasons:', $plugin->getDisplayName()); ?></strong>
                    <?php if (!$plugin->meetsOmekaMinimumVersion() || $missingPluginNames): ?>
                        <ul>
                            <?php if (!$plugin->meetsOmekaMinimumVersion()): ?>
                                <li class="omeka-minimum-version"><?php echo __('This version of %1$s requires at least Omeka %2$s. You are using Omeka version %3$s.', html_escape($plugin->getDisplayName()), html_escape($plugin->getMinimumOmekaVersion()), OMEKA_VERSION); ?></li>
                            <?php endif; ?>

                            <?php if ($missingPluginNames): ?>
                                <li class="required-plugins">
                                    <?php echo __('The %s plugin requires the following plugins to be installed, activated, and loaded:', html_escape($plugin->getDisplayName())); ?> 
                                      <?php echo html_escape(implode (', ', $missingPluginNames)); ?>.
                                </li>
                            <?php endif; ?>
                        </ul>
                    <?php endif; ?>
                </div>
        <?php endif; ?>
        <div class="plugin-info">
        <p class="plugin-title"><?php
            if ($plugin->getLinkUrl()):
                echo '<a href="' . html_escape($plugin->getLinkUrl()) . '">' . html_escape($plugin->getDisplayName()) . '</a>';
            else:
                echo html_escape($plugin->getDisplayName());
            endif; 
            if (has_permission('Plugins', 'config') && $plugin->hasConfig() ): ?>
                <a href="<?php echo html_escape(uri('plugins/config', array('name'=>$plugin->getDirectoryName()))); ?>" class="configure-button button"><?php echo __('Configure'); ?></a>
            <?php endif; ?>
        </p>           
        
        <?php 
            $pluginMetadata = array();
            if ($plugin->getIniVersion()):
                $pluginMetadata[] = __('Version') . ' ' . html_escape(trim($plugin->getIniVersion()));
            endif;
        
            if ($plugin->getAuthor()):
                $pluginMetadata[] = __('By') . ' ' . html_escape(trim($plugin->getAuthor()));
            endif;
            if (!empty($pluginMetadata)): ?>
                <p class="plugin-meta"><?php echo implode(' | ', $pluginMetadata); ?></p>
        <?php endif; ?>
        
        <?php if($pluginDescription = $plugin->getDescription()): ?>
        <p class="plugin-description"><?php echo html_escape($pluginDescription); ?></p>
	    <?php endif; ?>
	    
	    <?php if ($plugin->hasNewVersion()): ?>    
	        <p class="notice plugin-upgrade"><strong><?php echo __('Notice:'); ?></strong> <?php echo __('You have a new version of %s. Please upgrade!', html_escape($plugin->getDisplayName())); ?></p>
	    <?php endif; ?>
	    <?php if ($versionCheck && !$plugin->meetsOmekaTestedUpToVersion()): ?>
            <p class="notice omeka-tested-up-to"><strong><?php echo __('Notice:'); ?></strong> <?php echo __('This version of %1$s has only been tested up to Omeka %2$s. You are using Omeka version %3$s', html_escape($plugin->getDisplayName()), html_escape($plugin->getTestedUpToOmekaVersion()), OMEKA_VERSION); ?>.</p>
        <?php endif; ?>
        </div>
    </td>
    
    <td>
    <?php if ($plugin->isInstalled()): ?>
        <?php if ($plugin->hasNewVersion()): ?>
            <?php if (has_permission('Plugins', 'upgrade')): ?>
                <form action="<?php echo html_escape(uri('plugins/upgrade')); ?>" method="post" accept-charset="utf-8">     
                    <div>
                        <button name="upgrade" type="submit" class="upgrade submit-medium" value="<?php echo html_escape($plugin->getDirectoryName()); ?>"><?php echo __('Upgrade'); ?></button>
                        <input type="hidden" name="name" value="<?php echo html_escape($plugin->getDirectoryName()); ?>" />
                    </div>
                </form>
            <?php endif; ?>
        <?php else: ?>
            <?php $activateOrDeactivate = ($plugin->isActive()) ? 'deactivate' : 'activate'; ?>
            <?php if (has_permission('Plugins', 'activate')): ?>
                <form action="<?php echo html_escape(uri('plugins/' . $activateOrDeactivate)); ?>" method="post" accept-charset="utf-8">
                    <div>
                        <button name="<?php echo $activateOrDeactivate; ?>" type="submit" class="<?php echo $activateOrDeactivate; ?> submit-medium" value="<?php echo html_escape($plugin->getDirectoryName()); ?>"><?php echo ($plugin->isActive()) ? __('Deactivate') : __('Activate'); ?></button>
                        <input type="hidden" name="name" value="<?php echo html_escape($plugin->getDirectoryName()); ?>" />
                    </div>
                </form>                
            <?php endif; ?>
        <?php endif; ?>   
        <?php if (has_permission('Plugins', 'uninstall')): ?>
            <form action="<?php echo html_escape(uri(array(
                'controller'=>'plugins', 
                'action'=>'uninstall'), 'default')); ?>" method="post" accept-charset="utf-8">
                <div>
                    <button name="uninstall" type="submit" class="uninstall submit-medium" value="<?php echo html_escape($plugin->getDirectoryName()); ?>"><?php echo __('Uninstall'); ?></button>
                    <input type="hidden" name="name" value="<?php echo html_escape($plugin->getDirectoryName()); ?>" />
                </div>
            </form>                
        <?php endif; ?>     
<?php else: //The plugin has not been installed yet ?>
    <?php if (has_permission('Plugins', 'install')): ?>
        <form action="<?php echo html_escape(uri('plugins/install')); ?>" method="post" accept-charset="utf-8">
                <div>
                    <button name="install" type="submit" class="submit-medium" value="<?php echo html_escape($plugin->getDirectoryName()); ?>"><?php echo __('Install'); ?></button>
                    <input type="hidden" name="name" value="<?php echo html_escape( $plugin->getDirectoryName()); ?>" />
                </div>
        </form> 
    <?php endif; ?>
<?php endif; ?>

</td>
</tr>