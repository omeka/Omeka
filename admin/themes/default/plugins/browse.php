<?php head(array('title'=>'Browse Plugins', 'content_class' => 'vertical-nav', 'bodyclass'=>'plugins primary')); ?>
<h1>Browse Plugins (<?php echo count($pluginInfos) ?> total)</h1>
<?php common('settings-nav'); ?>

<div id="primary">
    <?php echo flash(); ?>
    
    <?php if ($pluginInfos && count($pluginInfos) > 0): ?>

<table id="plugin-info">
    <thead>
        <tr>
            <th>Plugin</th>
            <th>Action</th>
        </tr>
    </thead>
    
    <tbody>

<?php foreach($pluginInfos as $pluginDirName => $pluginInfo): ?>
    <?php
    $requiredPluginDirNames = $pluginInfo->requiredPluginDirNames;
    $requiredPluginNames = array();
    if (count($requiredPluginDirNames) > 0) {
        foreach($requiredPluginDirNames as $requiredPluginDirName) {
            $requiredPluginInfo = $pluginInfos[$requiredPluginDirName];
            if (!$requiredPluginInfo || 
                !($requiredPluginInfo->installed) || 
                !($requiredPluginInfo->active) || 
                !($requiredPluginInfo->hasPluginFile)) {
                
                if (!$requiredPluginInfo) {
                     $requiredPluginName = $requiredPluginDirName;
                } else {
                     $requiredPluginName = $requiredPluginInfo->name;
                }
                $requiredPluginNames[] = "'" . $requiredPluginName . "'";
            }
        }
    }
    ?>
    <tr <?php if (($pluginInfo->meetsOmekaMinimumVersion == false || count($requiredPluginNames) > 0) && 
                 !$pluginInfo->loaded) { 
                 echo 'class="plugin-load-error"'; 
              } elseif ($pluginInfo->hasNewVersion) { 
                 echo 'class="upgrade-plugin"'; 
              } ?>>
        <td>
            <?php
                if ($pluginInfo->meetsOmekaMinimumVersion == false || 
                    count($requiredPluginNames) > 0):
            ?>
                    <div class="warnings">
                        <strong>Warning!<?php if (($pluginInfo->installed || $pluginInfo->active) && $pluginInfo->loaded): ?> The <?php echo $pluginInfo->name; ?> plugin could not be loaded for the following reasons:<?php endif; ?></strong>
                        <ul>
                            <?php if ($pluginInfo->meetsOmekaMinimumVersion == false): ?>
                                <li class="omeka-minimum-version">The '<?php echo html_escape($pluginInfo->name); ?>' plugin requires at least Omeka <?php echo (string)get_plugin_ini($pluginInfo->directoryName, 'omeka_minimum_version'); ?>. You are using version Omeka <?php echo OMEKA_VERSION; ?>.</li>
                            <?php endif; ?>

                            <?php if (count($requiredPluginNames) > 0): ?>
                                <li class="required-plugins">
                                <?php if ($pluginInfo->active): ?>
                                    The '<?php echo html_escape($pluginInfo->name); ?>' plugin was not loaded because 
                                    the following plugins need to be 
                                    installed, activated, and loaded: 
                                    <?php echo html_escape(implode_array_to_english($requiredPluginNames)); ?> 
                                    plugin<?php if (count($requiredPluginNames) > 1) { echo 's';} ?>.
                                <?php else: ?>
                                    The '<?php echo html_escape($pluginInfo->name); ?>' plugin requires 
                                      the following plugins to be 
                                      installed, activated, and loaded: 
                                      <?php echo html_escape(implode_array_to_english($requiredPluginNames)); ?> 
                                      plugin<?php if (count($requiredPluginNames) > 1) { echo 's';} ?>.
                                <?php endif; ?>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
            <?php endif; ?>
            <div class="plugin-info">
            <p class="plugin-title"><?php
                if ($pluginInfo->link):
                    echo '<a href="' . html_escape($pluginInfo->link) . '">' . html_escape($pluginInfo->name) . '</a>';
                else:
                    echo html_escape($pluginInfo->name);
                endif; 
            ?>
            <?php if (has_permission('Plugins', 'config') && $pluginInfo->hasConfig ): ?><a href="<?php echo html_escape(uri('plugins/config', array('name'=>$pluginInfo->directoryName))); ?>" class="configure-button button">Configure</a><?php endif; ?></p>           
            <?php 
                $pluginMetadata = array();
                if (trim($pluginInfo->version) != ''):
                    $pluginMetadata[] = 'Version ' . html_escape(trim($pluginInfo->version));
                endif;
            
                if (trim($pluginInfo->author) != ''):
                    $pluginMetadata[] = 'By ' . html_escape(trim($pluginInfo->author));
                endif;
            ?>
            
            <?php if (!empty($pluginMetadata)): ?>
            <p class="plugin-meta"><?php echo implode(' | ', $pluginMetadata); ?></p>
            <?php endif; ?>
            
            <?php if(!empty($pluginInfo->description)): ?>
            <p class="plugin-description"><?php echo html_escape($pluginInfo->description); ?></p>
		    <?php endif; ?>
		    
		    <?php if ($pluginInfo->hasNewVersion): ?>    
		        <p class="notice plugin-upgrade"><strong>Notice:</strong> You have a new version of <?php echo html_escape($pluginInfo->name); ?>. Please upgrade!</p>
		    <?php endif; ?>
		    <?php if ($pluginInfo->meetsOmekaTestedUpTo == false): ?>
                <p class="notice omeka-tested-up-to"><strong>Notice:</strong> This version of the '<?php echo html_escape($pluginInfo->name); ?>' plugin has only been tested up to Omeka <?php echo html_escape((string)get_plugin_ini($pluginInfo->directoryName, 'omeka_tested_up_to')); ?>. You are using version Omeka <?php echo OMEKA_VERSION; ?>.</p>
            <?php endif; ?>
            </div>
        </td>
        
        <td>
        <?php if ($pluginInfo->installed): ?>
            <?php if ($pluginInfo->hasNewVersion): ?>
                <?php if (has_permission('Plugins', 'upgrade')): ?>
                    <form action="<?php echo html_escape(uri('plugins/upgrade')); ?>" method="post" accept-charset="utf-8">     
                            <button name="upgrade" type="submit" class="upgrade submit-medium" value="<?php echo html_escape($pluginInfo->directoryName); ?>">Upgrade</button>
                            <input type="hidden" name="name" value="<?php echo html_escape($pluginInfo->directoryName); ?>" />
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <?php $activateOrDeactivate = ($pluginInfo->active) ? 'deactivate' : 'activate'; ?>
                <?php if (has_permission('Plugins', 'activate')): ?>
                    <form action="<?php echo html_escape(uri('plugins/' . $activateOrDeactivate)); ?>" method="post" accept-charset="utf-8">
                        <button name="<?php echo $activateOrDeactivate; ?>" type="submit" class="<?php echo $activateOrDeactivate; ?> submit-medium" value="<?php echo html_escape($pluginInfo->directoryName); ?>"><?php echo ($pluginInfo->active) ? 'Deactivate' : 'Activate'; ?>
                        </button>
                        <input type="hidden" name="name" value="<?php echo html_escape($pluginInfo->directoryName); ?>" />
                    </form>                
                <?php endif; ?>
            <?php endif; ?>   
            <?php if (has_permission('Plugins', 'uninstall')): ?>
                <form action="<?php echo html_escape(uri(array(
                    'controller'=>'plugins', 
                    'action'=>'uninstall'), 'default')); ?>" method="post" accept-charset="utf-8">
                        <button name="uninstall" type="submit" class="uninstall submit-medium" value="<?php echo html_escape($pluginInfo->directoryName); ?>">Uninstall</button>
                        <input type="hidden" name="name" value="<?php echo html_escape($pluginInfo->directoryName); ?>" />
                </form>                
            <?php endif; ?>     
    <?php else: //The plugin has not been installed yet ?>
        <?php if (has_permission('Plugins', 'install')): ?>
            <form action="<?php echo html_escape(uri('plugins/install')); ?>" method="post" accept-charset="utf-8">
                    <button name="install" type="submit" class="submit-medium" value="<?php echo html_escape($pluginInfo->directoryName); ?>">Install</button>
                    <input type="hidden" name="name" value="<?php echo html_escape( $pluginInfo->directoryName); ?>" />
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