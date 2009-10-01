<?php head(array('title'=>'Browse Plugins', 'content_class' => 'vertical-nav', 'bodyclass'=>'plugins primary')); ?>
<h1>Browse Plugins (<?php echo count($plugins) ?> total)</h1>
<?php common('settings-nav'); ?>

<div id="primary">
    <?php echo flash(); ?>
    
    <?php if ($plugins): ?>

<table id="plugin-info">
    <thead>
        <tr>
            <th>Plugin</th>
            <th>Action</th>
        </tr>
    </thead>
    
    <tbody>

<?php foreach($plugins as $pluginDirName => $plugin): ?>
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
                        <strong>Warning!The <?php echo $plugin->getDisplayName(); ?> plugin could not be loaded for the following reasons:</strong>
                        <ul>
                            <?php if (!$plugin->meetsOmekaMinimumVersion()): ?>
                                <li class="omeka-minimum-version">The '<?php echo html_escape($plugin->getDisplayName()); ?>' plugin requires at least Omeka <?php echo $plugin->getMinimumOmekaVersion(); ?>. You are using version Omeka <?php echo OMEKA_VERSION; ?>.</li>
                            <?php endif; ?>

                            <?php if ($missingPluginNames): ?>
                                <li class="required-plugins">
                                    The '<?php echo html_escape($plugin->getDisplayName()); ?>' plugin requires 
                                      the following plugins to be 
                                      installed, activated, and loaded: 
                                      <?php echo html_escape(implode_array_to_english($missingPluginNames)); ?> 
                                      plugin<?php if (count($missingPluginNames) > 1) { echo 's';} ?>.
                                </li>
                            <?php endif; ?>
                        </ul>
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
                    <a href="<?php echo uri('plugins/config', array('name'=>$plugin->getDirectoryName())); ?>" class="configure-button button">Configure</a>
                <?php endif; ?>
            </p>           
            
            <?php 
                $pluginMetadata = array();
                if ($plugin->getIniVersion()):
                    $pluginMetadata[] = 'Version ' . html_escape(trim($plugin->getIniVersion()));
                endif;
            
                if ($plugin->getAuthor()):
                    $pluginMetadata[] = 'By ' . html_escape(trim($plugin->getAuthor()));
                endif;
                if (!empty($pluginMetadata)): ?>
                    <p class="plugin-meta"><?php echo implode(' | ', $pluginMetadata); ?></p>
            <?php endif; ?>
            
            <?php if($description = $plugin->getDescription()): ?>
            <p class="plugin-description"><?php echo $description; ?></p>
		    <?php endif; ?>
		    
		    <?php if ($plugin->hasNewVersion()): ?>    
		        <p class="notice plugin-upgrade"><strong>Notice:</strong> You have a new version of <?php echo $plugin->getDisplayName(); ?>. Please upgrade!</p>
		    <?php endif; ?>
		    <?php if (!$plugin->meetsOmekaTestedUpToVersion()): ?>
                <p class="notice omeka-tested-up-to"><strong>Notice:</strong> This version of the '<?php echo html_escape($plugin->getDisplayName()); ?>' plugin has only been tested up to Omeka <?php echo $plugin->getTestedUpToOmekaVersion(); ?>. You are using version Omeka <?php echo OMEKA_VERSION; ?>.</p>
            <?php endif; ?>
            </div>
        </td>
        
        <td>
        <?php if ($plugin->isInstalled()): ?>
            <?php if ($plugin->hasNewVersion()): ?>
                <?php if (has_permission('Plugins', 'upgrade')): ?>
                    <form action="<?php echo uri('plugins/upgrade'); ?>" method="post" accept-charset="utf-8">     
                            <button name="upgrade" type="submit" class="upgrade submit-medium" value="<?php echo $plugin->getDirectoryName(); ?>">Upgrade</button>
                            <input type="hidden" name="name" value="<?php echo $plugin->getDirectoryName(); ?>" />
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <?php $activateOrDeactivate = ($plugin->isActive()) ? 'deactivate' : 'activate'; ?>
                <?php if (has_permission('Plugins', 'activate')): ?>
                    <form action="<?php echo uri('plugins/' . $activateOrDeactivate); ?>" method="post" accept-charset="utf-8">
                        <button name="<?php echo $activateOrDeactivate; ?>" type="submit" class="<?php echo $activateOrDeactivate; ?> submit-medium" value="<?php echo $plugin->getDirectoryName(); ?>"><?php echo ($plugin->isActive()) ? 'Deactivate' : 'Activate'; ?>
                        </button>
                        <input type="hidden" name="name" value="<?php echo $plugin->getDirectoryName(); ?>" />
                    </form>                
                <?php endif; ?>
            <?php endif; ?>   
            <?php if (has_permission('Plugins', 'uninstall')): ?>
                <form action="<?php echo uri(array(
                    'controller'=>'plugins', 
                    'action'=>'uninstall'), 'default'); ?>" method="post" accept-charset="utf-8">
                        <button name="uninstall" type="submit" class="uninstall submit-medium" value="<?php echo $plugin->getDirectoryName(); ?>">Uninstall</button>
                        <input type="hidden" name="name" value="<?php echo $plugin->getDirectoryName(); ?>" />
                </form>                
            <?php endif; ?>     
    <?php else: //The plugin has not been installed yet ?>
        <?php if (has_permission('Plugins', 'install')): ?>
            <form action="<?php echo uri('plugins/install'); ?>" method="post" accept-charset="utf-8">     
                    <button name="install" type="submit" class="submit-medium" value="<?php echo $plugin->getDirectoryName(); ?>">Install</button>
                    <input type="hidden" name="name" value="<?php echo $plugin->getDirectoryName(); ?>" />
            </form> 
        <?php endif; ?>
    <?php endif; ?>
    
    </td>
    </tr>
    
<?php endforeach; ?>
</tbody>
</table>

<?php else: ?>
<p>You don't have any plugins installed.  Add them to the plugins directory to see them listed here.</p>
<?php endif; ?>

<p class="manageplugins">Add new plugins by downloading them from the <a href="http://omeka.org/add-ons/plugins/">Omeka Plugins  Directory</a>, or <a href="http://omeka.org/codex/Plugin_Writing_Best_Practices">write your own</a>!</p>
</div>

<?php foot(); ?>