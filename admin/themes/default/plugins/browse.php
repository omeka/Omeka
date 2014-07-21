<?php 
$pageTitle = __('Plugins') . ' ' . __('(%s total)', $plugin_count);
echo head(array('title' => $pageTitle, 'bodyclass' => 'plugins browse'));
echo flash();
?>

<?php if ($plugins): ?>
    <?php foreach($plugins as $pluginDirName => $plugin): ?>
        <?php
        $displayName = $plugin->getDisplayName();
        $requiredPluginDirNames = $plugin->getRequiredPlugins();
        $missingPluginNames = array();
        
        foreach($requiredPluginDirNames as $requiredPluginDirName) {
            $requiredPlugin = $loader->getPlugin($requiredPluginDirName);
            if (!$requiredPlugin) {
                $missingPluginNames[] = '"' . $requiredPluginDirName . '"';
            } elseif (!$requiredPlugin->isLoaded()) {
                $missingPluginNames[] = '"' . $requiredPlugin->getDirectoryName() . '"';
            }
        }
        
        $loadErrors = array();
        if (!$plugin->meetsOmekaMinimumVersion()) {
            $minVersion = $plugin->getMinimumOmekaVersion();
            $loadErrors[] = __('This version of %1$s requires at least Omeka %2$s.', $displayName, $minVersion);
        }
        if ($missingPluginNames) {
            $loadErrors[] = __('%1$s requires the following plugins to be installed, activated, and loaded: %2$s', html_escape($displayName), implode(', ', $missingPluginNames));
        }
        if (!$loader->hasPluginBootstrap($plugin->name)) {
            $loadErrors[] = __('This is not a valid plugin.');
        }
        $cannotLoad = !empty($loadErrors);
        $needsUpgrade = $plugin->hasNewVersion();
        
        $attentionClass = $cannotLoad ? 'cannotload' : ($needsUpgrade ? 'upgrade' : '');
        ?>
        
    <div class="plugin <?php echo $attentionClass; ?>">
        <div class="six columns alpha">
            <h2>
            <?php if ($plugin->getLinkUrl()): ?>
                <a href="<?php echo html_escape($plugin->getLinkUrl()); ?>" target="_blank"><?php echo html_escape($displayName); ?></a>
            <?php else: ?>
                <?php echo html_escape($displayName); ?>
            <?php endif; ?>
            </h2>
            <p class="plugin-metadata">
            <?php 
            if ($plugin->getIniVersion()):
                echo __('Version %s', html_escape($plugin->getIniVersion()));
            endif;
            ?>
            <?php 
            if ($plugin->getAuthor()):
                echo __('by %s', html_escape($plugin->getAuthor()));
            endif;
            ?>
            </p>
            <?php if($pluginDescription = $plugin->getDescription()): ?>
                <p class="plugin-description"><?php echo html_escape($pluginDescription); ?></p>
            <?php endif; ?>
            <?php if($pluginSupportLink = $plugin->getSupportLinkUrl()):?>
                <p class="plugin-support-link"><a href="<?php echo $pluginSupportLink; ?>"><?php echo __("Get support"); ?></a></p>
            <?php endif;?>
            <?php if ($needsUpgrade): ?>
                <ul class="details">
                    <li class="success"><?php echo __('You have a new version of %s. Please upgrade!', $displayName); ?></li>
                </ul>
            <?php endif; ?>
            <?php if ($cannotLoad): ?>
                <ul class="details">
                <?php foreach ($loadErrors as $error): ?>
                    <li class="error"><?php echo html_escape($error); ?></li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            </div>
            <div class="four columns omega">
                <ul class="action-links">
                <?php if ($plugin->isInstalled()): ?>
                    <?php if ($needsUpgrade): ?>
                        <?php if (is_allowed('Plugins', 'upgrade')): ?>
                            <form action="<?php echo html_escape(url('plugins/upgrade')); ?>" method="post" accept-charset="utf-8">     
                                <li></li>
                                <li></li>
                                <li>
                                    <button name="upgrade" type="submit" class="upgrade big green button"<?php if ($cannotLoad): ?> disabled="disabled"<?php endif; ?>><?php echo __('Upgrade'); ?></button>
                                    <input type="hidden" name="name" value="<?php echo html_escape($pluginDirName); ?>" />
                                </li>
                                <?php echo $csrf; ?>
                            </form>
                        <?php endif; ?>
                    <?php else: ?>
                    <?php $activateOrDeactivate = ($plugin->isActive()) ? 'deactivate' : 'activate'; ?>
                    <?php if (is_allowed($plugin, 'activate')  && !$cannotLoad): ?>
                        <li>
                        <?php if (is_allowed($plugin, 'config') && $plugin->hasConfig()): ?>
                        <a href="<?php echo html_escape(url('plugins/config', array('name' => $plugin->getDirectoryName()))); ?>" class="big blue button"><?php echo __('Configure'); ?></a>
                        <?php endif; ?>
                        </li>
                        <li>
                        <?php if (!$cannotLoad): ?>
                            <form action="<?php echo html_escape(url('plugins/' . $activateOrDeactivate)); ?>" method="post" accept-charset="utf-8">
                            <button name="<?php echo $activateOrDeactivate; ?>" type="submit" class="big <?php echo ($plugin->isActive()) ? 'red' : 'green'; ?> button"><?php echo ($plugin->isActive()) ? __('Deactivate') : __('Activate'); ?></button>
                            <input type="hidden" name="name" value="<?php echo html_escape($plugin->name); ?>" />
                            <?php echo $csrf; ?>
                            </form>
                        <?php endif; ?>
                        </li>
                    <?php endif; ?>
                        <li>
                    <?php if (is_allowed($plugin, 'uninstall') && !$cannotLoad): ?>
                            <form action="<?php echo html_escape(url(array('controller' => 'plugins', 'action' => 'uninstall'), 'default')); ?>" method="post" accept-charset="utf-8">

                            <button name="uninstall" type="submit" class="uninstall big red button"><?php echo __('Uninstall'); ?></button>
                            <input type="hidden" name="name" value="<?php echo html_escape($plugin->name); ?>" />
                            </form>
                    <?php endif; ?> 
                        </li>

                    <?php endif; ?>
                <?php else: //The plugin has not been installed yet ?>
                    <?php if (is_allowed($plugin, 'install') && !$cannotLoad): ?>
                        <li></li>
                        <li></li>
                        <li>
                            <form action="<?php echo html_escape(url('plugins/install')); ?>" method="post" accept-charset="utf-8">
                            <button name="install" type="submit" class="install big green button"<?php if ($cannotLoad): ?> disabled="disabled"<?php endif; ?>><?php echo __('Install'); ?></button>
                            <input type="hidden" name="name" value="<?php echo html_escape($plugin->name); ?>" />
                            <?php echo $csrf; ?>
                            </form> 
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                </ul>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p><?php echo __('You do not have any plugins installed. Add them to the plugins directory to see them listed here.'); ?></p>
<?php endif; ?>
<?php fire_plugin_hook('admin_plugins_browse', array('plugins' => $plugins, 'view' => $this)); ?>
<?php echo foot(); ?>
