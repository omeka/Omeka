<?php 
$pageTitle = __('Plugins') . ' ' . __('(%s total)', $plugin_count);
echo head(array('title' => $pageTitle, 'bodyclass' => 'plugins browse'));
echo flash();
?>

<div class="table-actions">
    <?php if(isset($plugins['needs-attention'])) :?>
    <ul class="quick-filter-wrapper">
        <li><a href="#" tabindex="0"><?php echo __('Needs Action'); ?></a>
        <ul class="dropdown">
            <li><span class="quick-filter-heading"><?php echo __('Needs Action') ?></span></li>
            <?php foreach($plugins['needs-attention'] as $id=>$plugin): ?>
            <li><a href="#<?php echo $id; ?>"><?php echo $plugin->getDisplayName(); ?></a></li>
            <?php endforeach; ?>
        </ul>
        </li>
    </ul>    
    <?php endif; ?>
    <?php if(isset($plugins['active'])): ?>
    <a href="#active" class="small green button"><?php echo __('Active'); ?></a>
    <?php endif; ?>
    <?php if(isset($plugins['inactive'])): ?>
    <a href="#inactive" class="small blue button"><?php echo __('Inactive'); ?></a>
    <?php endif; ?>
    <?php if(isset($plugins['uninstalled'])): ?>
    <a href="#uninstalled" class="small red button"><?php echo __('Uninstalled'); ?></a>
    <?php endif; ?>
</div>

<?php if ($plugins): ?>
    <?php foreach ($plugins as $key => $value): ?>
    <?php if($key != 'active'): ?>
    <div class="table-actions">
        <?php if(isset($plugins['needs-attention'])) :?>
        <ul class="quick-filter-wrapper">
            <li><a href="#" tabindex="0"><?php echo __('Needs Action'); ?></a>
            <ul class="dropdown">
                <li><span class="quick-filter-heading"><?php echo __('Needs Action') ?></span></li>
                <?php foreach($plugins['needs-attention'] as $id=>$plugin): ?>
                <li><a href="#<?php echo $id; ?>"><?php echo $plugin->getDisplayName(); ?></a></li>
                <?php endforeach; ?>
            </ul>
            </li>
        </ul>    
        <?php endif; ?>
        <?php if(isset($plugins['active'])): ?>
        <a href="#active" class="small green button"><?php echo __('Active'); ?></a>
        <?php endif; ?>
        <?php if(isset($plugins['inactive'])): ?>
        <a href="#inactive" class="small blue button"><?php echo __('Inactive'); ?></a>
        <?php endif; ?>
        <?php if(isset($plugins['uninstalled'])): ?>
        <a href="#uninstalled" class="small red button"><?php echo __('Uninstalled'); ?></a>
        <?php endif; ?>
        <a href="#">Top</a>
    </div>
        <?php endif; ?>
    
        <h3 id="<?php echo $key;?>" style="clear:both">
        <?php
        if ('active' == $key):
            echo __('Active');
        elseif ('inactive' == $key):
            echo __('Inactive');
        elseif ('uninstalled' == $key):
            echo __('Uninstalled');
        elseif ('needs-attention' == $key):
            continue;
        endif;
        ?>
        </h3>
        <?php if ($value): ?>
        <table>
            <thead>
                <th><?php echo __('Name'); ?></th>
                <th><?php echo __('Version'); ?></th>
                <th><?php echo __('Author'); ?></th>
                <th><?php echo __('Description'); ?></th>
            </thead>
            <tbody>
            <?php $key = 0; ?>
            <?php foreach($value as $pluginDirName => $plugin): ?>
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
                    $loadErrors[] = __('There is no valid bootstrap file.');
                }
                $cannotLoad = !empty($loadErrors);
                $needsUpgrade = $plugin->hasNewVersion();
                ?>
                <tr id="<?php echo $pluginDirName; ?>" class="<?php echo (++$key % 2) ? 'odd' : 'even'; ?>">
                    <td <?php if(in_array($pluginDirName, array_keys($plugins['needs-attention']) ) ) { echo "class='needs-attention'"; } ?> >
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
        <p>
        <?php
        if ('active' == $key):
            echo __('There are no active plugins.');
        elseif ('inactive' == $key):
            echo __('There are no inactive plugins.');
        elseif ('uninstalled' == $key):
            echo __('There are no uninstalled plugins.');
        endif;
        ?>
        </p>
        <?php endif; ?>
    <?php endforeach; ?>
<?php else: ?>
    <p><?php echo __('You do not have any plugins installed. Add them to the plugins directory to see them listed here.'); ?></p>
<?php endif; ?>
<?php fire_plugin_hook('admin_plugins_browse', array('plugins' => $plugins, 'view' => $this)); ?>
<?php echo foot(); ?>
