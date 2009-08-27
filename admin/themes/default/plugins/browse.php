<?php head(array('title'=>'Plugins', 'content_class' => 'vertical-nav', 'bodyclass'=>'plugins primary')); ?>
<h1>Plugins</h1>

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
	<tr>
		<td>
	        <p class="plugin-title">
    		<?php
    		    if ($pluginInfo->link):
    				echo '<a href="' . html_escape($pluginInfo->link) . '">' . html_escape($pluginInfo->name) . '</a>';
    			else:
    		        echo html_escape($pluginInfo->name);
    			endif; 
    		?>
    		<?php if (has_permission('Plugins', 'config') && $pluginInfo->hasConfig ): ?>
		        <a href="<?php echo uri('plugins/config', array('name'=>$pluginInfo->directoryName)); ?>" class="configure-button button">Configure</a>
            <?php endif; ?>
    		</p>           
            <?php 
                $pluginMetadata = array();
                if (!empty($pluginInfo->version)):
                    $pluginMetadata[] = 'Version ' . html_escape($pluginInfo->version);
                endif;
            
                if (!empty($pluginInfo->author)):
                    $pluginMetadata[] = 'By ' . html_escape($pluginInfo->author);
                endif;
            ?>
            <?php if (!empty($pluginMetadata)): ?>
                <p class="plugin-meta">    
                    <?php echo implode(' | ', $pluginMetadata); ?>
                </p>
            <?php endif; ?>
            
            <?php if(!empty($pluginInfo->description)): ?>
                <p class="plugin-description">
                    <?php echo $pluginInfo->description; ?>
                </p>
		    <?php endif; ?>
		</td>
		
		<td>
        <?php if ($pluginInfo->installed): ?>
            <?php if ($pluginInfo->canUpgrade): ?>
                <?php if (has_permission('Plugins', 'upgrade')): ?>
                    <form action="<?php echo uri('plugins/upgrade'); ?>" method="post" accept-charset="utf-8">     
                            <button name="upgrade" type="submit" class="upgrade submit-medium" value="<?php echo $pluginInfo->directoryName; ?>">Upgrade</button>
                            <input type="hidden" name="name" value="<?php echo $pluginInfo->directoryName; ?>" />
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <?php $activateOrDeactivate = ($pluginInfo->active) ? 'deactivate' : 'activate'; ?>
                <?php if (has_permission('Plugins', 'activate')): ?>
                    <form action="<?php echo uri('plugins/' . $activateOrDeactivate); ?>" method="post" accept-charset="utf-8">
                        <button name="<?php echo $activateOrDeactivate; ?>" type="submit" class="<?php echo $activateOrDeactivate; ?> submit-medium" value="<?php echo $pluginInfo->directoryName; ?>"><?php echo ($pluginInfo->active) ? 'Deactivate' : 'Activate'; ?>
                        </button>
                        <input type="hidden" name="name" value="<?php echo $pluginInfo->directoryName; ?>" />
                    </form>                
                <?php endif; ?>
                <br />
                <?php if (has_permission('Plugins', 'uninstall')): ?>
                    <form action="<?php echo uri(array(
                        'controller'=>'plugins', 
                        'action'=>'uninstall'), 'default'); ?>" method="post" accept-charset="utf-8">
                            <button name="uninstall" type="submit" class="uninstall submit-medium" value="<?php echo $pluginInfo->directoryName; ?>">Uninstall</button>
                            <input type="hidden" name="name" value="<?php echo $pluginInfo->directoryName; ?>" />
                    </form>                
                <?php endif; ?>
            <?php endif; ?>        
    <?php else: //The plugin has not been installed yet ?>
        <?php if (has_permission('Plugins', 'install')): ?>
            <form action="<?php echo uri('plugins/install'); ?>" method="post" accept-charset="utf-8">     
                    <button name="install" type="submit" class="submit-medium" value="<?php echo $pluginInfo->directoryName; ?>">Install</button>
                    <input type="hidden" name="name" value="<?php echo $pluginInfo->directoryName; ?>" />
            </form> 
        <?php endif; ?>
    <?php endif; ?>
    
    </td>
    </tr>
    <?php 
        $requiredPluginDirNames = $pluginInfo->requiredPluginDirNames;
        $optionalPluginDirNames =  $pluginInfo->optionalPluginDirNames;
        if(count($requiredPluginDirNames) > 0): 
    ?>
        <tr class="related-plugin-info">
          <td colspan="2">
            <?php if($pluginInfo->meetsOmekaMinimumVersion == false): ?>
                <div class="error">Your version of Omeka is less than the required version for this plugin.</div>
            <?php endif; ?>
            <?php if($pluginInfo->meetsOmekaTestedUpTo == false): ?>
                <div class="error">This plugin has not been tested for your current version of Omeka.</div>
            <?php endif; ?>
                 <?php 
                 $requiredPluginNames = array();
                 foreach($requiredPluginDirNames as $requiredPluginDirName):
                     $requiredPluginInfo = $pluginInfos[$requiredPluginDirName];
                     if (!$requiredPluginInfo || 
                         !($requiredPluginInfo->installed) || 
                         !($requiredPluginInfo->active) || 
                         !($requiredPluginInfo->hasPluginFiles)):
                         if (!$requiredPluginInfo) {
                              $requiredPluginName = $requiredPluginDirName;
                          } else {
                              $requiredPluginName = $requiredPluginInfo->name;
                          }
                         $requiredPluginNames[] = "'" . $requiredPluginName . "'";
                     endif;
                 endforeach; 
                 ?>
                 <?php if(count($requiredPluginNames) > 0): ?>
                     <div class="required-plugins">
                        <?php if ($pluginInfo->active): ?>
                            <p>The '<?php echo html_escape($pluginInfo->name); ?>' plugin was not loaded because 
                            the following plugins need to be 
                            installed, activated, and loaded: 
                            <?php echo html_escape(implode_array_to_english($requiredPluginNames)); ?> 
                            plugin<?php if (count($requiredPluginNames) > 1) { echo 's';} ?>.</p>
                        <?php else: ?>
                            <p>The '<?php echo html_escape($pluginInfo->name); ?>' plugin requires 
                              the following plugins to be 
                              installed, activated, and loaded: 
                              <?php echo html_escape(implode_array_to_english($requiredPluginNames)); ?> 
                              plugin<?php if (count($requiredPluginNames) > 1) { echo 's';} ?>.</p>
                        <?php endif; ?>
                     </div>
                 <?php endif; ?>
             </td>
        </tr>
    <?php endif; ?>
    
<?php endforeach; ?>
</tbody>
</table>

<?php else: ?>
<p>You don't have any plugins installed.  Add them to the plugins directory to see them listed here.</p>
<?php endif; ?>

<p class="manageplugins">Add new plugins by downloading them from the <a href="http://omeka.org/add-ons/plugins/">Omeka Plugins  Directory</a>, or <a href="http://omeka.org/codex/Plugin_Writing_Best_Practices">write your own</a>!</p>
</div>

<?php foot(); ?>