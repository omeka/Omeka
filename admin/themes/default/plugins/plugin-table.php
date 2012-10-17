<?php if ($plugins): ?>

    <?php echo flash(); ?>            

    
    <table>
        <thead>
            <th><?php echo __('Name'); ?></th>
            <th><?php echo __('Version'); ?></th>
            <th><?php echo __('Author'); ?></th>
            <th><?php echo __('Description'); ?></th>
        </thead>
        <tbody>
        <?php $key = 0; ?>
        <?php foreach($plugins as $pluginDirName => $plugin): ?>            

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
    
                $loadErrors = array();
                if (!$plugin->meetsOmekaMinimumVersion()):
                    $minVersion = $plugin->getMinimumOmekaVersion();
                    $loadErrors[] = __('This version of %1$s requires at least Omeka %2$s.', $displayName, $minVersion);
                endif;
                if ($missingPluginNames):
                    $loadErrors[] = __('%1$s requires the following plugins to be installed, activated, and loaded: %2$s', html_escape($displayName), implode(', ', $missingPluginNames));
                endif;
                if (!$loader->hasPluginBootstrap($plugin->name)):
                    $loadErrors[] = __('There is no valid bootstrap file.');
                endif;
    
                $cannotLoad = !empty($loadErrors);
                $needsUpgrade = $plugin->hasNewVersion();

            ?>
            <tr class="<?php if(++$key%2==1) echo 'odd'; else echo 'even'; ?>">
                <td>
                <?php $displayName = $plugin->getDisplayName(); ?>
                <?php if ($plugin->getLinkUrl()): ?>
                    <a href="<?php echo html_escape($plugin->getLinkUrl()); ?>" target="_blank"><?php echo html_escape($displayName); ?></a>
                <?php else: ?>
                    <?php echo html_escape($displayName); ?>
                <?php endif; ?>
                    <ul class="action-links">
                    <?php if ($plugin->isInstalled()): ?>
                        <?php if ($needsUpgrade): ?>
                            <?php if (is_allowed('Plugins', 'upgrade')): ?>
                                <form action="<?php echo html_escape(url('plugins/upgrade')); ?>" method="post" accept-charset="utf-8">     
                                    <li>
                                        <button name="upgrade" type="submit" class="upgrade"<?php if ($cannotLoad): ?> disabled="disabled"<?php endif; ?>><?php echo __('Upgrade'); ?></button>
                                        <input type="hidden" name="name" value="<?php echo html_escape($pluginDirName); ?>" />
                                    </li>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php $activateOrDeactivate = ($plugin->isActive()) ? 'deactivate' : 'activate'; ?>
                        <?php if (is_allowed($plugin, 'activate')  && !$cannotLoad): ?>
                        <form action="<?php echo html_escape(url('plugins/' . $activateOrDeactivate)); ?>" method="post" accept-charset="utf-8">
                            <?php if (is_allowed($plugin, 'config') && $plugin->hasConfig()): ?>
                            <li><a href="<?php echo html_escape(url('plugins/config', array('name' => $plugin->getDirectoryName()))); ?>" class="small blue button"><?php echo __('Configure'); ?></a></li>
                            <?php endif; ?>
                            <?php if (!$cannotLoad): ?>
                            <li>
                                <button name="<?php echo $activateOrDeactivate; ?>" type="submit"><?php echo ($plugin->isActive()) ? __('Deactivate') : __('Activate'); ?></button>
                                <input type="hidden" name="name" value="<?php echo html_escape($plugin->name); ?>" />
                            </li>
                            <?php endif; ?>
                        </form>
                        <?php endif; ?>
                        <?php if (is_allowed($plugin, 'uninstall') && !$cannotLoad): ?>
                        <form action="<?php echo html_escape(url(array('controller' => 'plugins', 'action' => 'uninstall'), 'default')); ?>" method="post" accept-charset="utf-8">
                            <li>
                                <button name="uninstall" type="submit" class="uninstall small red button"><?php echo __('Uninstall'); ?></button>
                                <input type="hidden" name="name" value="<?php echo html_escape($plugin->name); ?>" />
                            </li>
                        </form>
                        <?php endif; ?> 
                    <?php else: //The plugin has not been installed yet ?>
                        <?php if (is_allowed($plugin, 'install') && !$cannotLoad): ?>
                            <form action="<?php echo html_escape(url('plugins/install')); ?>" method="post" accept-charset="utf-8">
                                    <li>
                                        <button name="install" type="submit" class="install"<?php if ($cannotLoad): ?> disabled="disabled"<?php endif; ?>><?php echo __('Install'); ?></button>
                                        <input type="hidden" name="name" value="<?php echo html_escape($plugin->name); ?>" />
                                    </li>
                            </form> 
                        <?php endif; ?>
                    <?php endif; ?>
                    </ul>
                    <?php if ($needsUpgrade): ?>
                        <ul class="details">
                            <li class="error"><?php echo __('You have a new version of %s. Please upgrade!', $displayName); ?></li>
                        </ul>
                    <?php endif; ?>
                    <?php if ($cannotLoad): ?>
                        <ul class="details">
                        <?php foreach ($loadErrors as $error): ?>
                            <li class="error"><?php echo html_escape($error); ?></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </td>
                <td>
                <?php 
                    if ($plugin->getIniVersion()):
                        echo html_escape(trim($plugin->getIniVersion()));
                    endif;
                ?>
                </td>
                <td>
                <?php 
                    if ($plugin->getAuthor()):
                        echo __('%s', html_escape(trim($plugin->getAuthor())));
                    endif;
                ?>
                </td>
                <td>
                <?php if($pluginDescription = $plugin->getDescription()): ?>
                    <p class="plugin-description"><?php echo html_escape($pluginDescription); ?></p>
                <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>


<?php else: ?>
<p><?php echo $noPlugins; ?></p>
<?php endif; ?>