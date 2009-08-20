<?php head(array('title'=>'Plugins', 'content_class' => 'vertical-nav', 'bodyclass'=>'plugins primary')); ?>
<h1>Plugins</h1>

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
<?php foreach( $plugins as $key => $plugin ): ?>
    <tr>
    <?php $relatedPluginInfo = get_related_plugin_info($plugin->directoryName); ?>
    <td>
        <p class="plugin-title"><strong><?php if(!empty($plugin->name)) { echo $plugin->name; } else { echo $plugin->directoryName; } ?></strong> <?php if ( has_permission('Plugins', 'config') and $plugin->has_config ): ?><a href="<?php echo uri('plugins/config', array('name'=>$plugin->directory)); ?>" class="configure-button button">Configure</a>
                	<?php endif; ?></p>
                	<p class="plugin-meta"><?php if(!empty($plugin->version)): ?>Version <?php echo $plugin->version; ?></em><?php endif; ?><?php if(!empty($plugin->author)): ?> | By <?php echo $plugin->author; ?><?php endif; ?><?php if(!empty($plugin->link)): ?> | <a href="<?php echo $plugin->link; ?>">Visit plugin website</a><?php endif; ?></p>
                	<br />
        <p class="plugin-description"><?php if(!empty($plugin->description)): ?><?php echo $plugin->description; endif; ?></p>
        
        


        </td>
        <td>
            <?php 
            //If the plugin has been installed, then there should be separate forms for activation/configuration
            //I hate IE so much.  
            if($plugin->installed): ?>
                <?php if (has_permission('Plugins', 'activate')): ?>
                    <form action="<?php echo uri('plugins/activate'); ?>" method="post" accept-charset="utf-8">
                        <button name="" type="submit" class="<?php echo ($plugin->active) ? 'deactivate' : 'activate'; ?> submit submit-medium" value="<?php echo $plugin->directory; ?>"><?php echo ($plugin->active) ? 'De-activate' : 'Activate'; ?>
                        </button>
                        <input type="hidden" name="activate" value="<?php echo $plugin->directory; ?>" />
                    </form>                
                <?php endif; ?>
                <br />
                <?php if (has_permission('Plugins', 'uninstall')): ?>
                    <form action="<?php echo uri(array(
                        'controller'=>'plugins', 
                        'action'=>'uninstall'), 'default'); ?>" method="post" accept-charset="utf-8">
                            <input type="submit" name="uninstall" class="uninstall submit submit-medium" value="Uninstall" />
                            <input type="hidden" name="name" value="<?php echo $plugin->directory; ?>" />
                    </form>                
                <?php endif; ?>
            <?php else: ?>
        	    <?php if (has_permission('Plugins', 'install')): ?>
                            <form action="<?php echo uri('plugins/install'); ?>" method="post" accept-charset="utf-8">     
                                    <button name="" type="submit" class="submit submit-medium" value="<?php echo $plugin->directory; ?>">Install</button>
                                    <input type="hidden" name="name" value="<?php echo $plugin->directory; ?>" />
                            </form> 
                        <?php endif; ?>
            <?php endif; ?></td>
    </tr>
    
      <?php if(count($relatedPluginInfo) > 0): ?>
            <tr class="related-plugin-info">
          <td colspan="3">
                 <?php 
                 $requiredPlugins = array();
                 $optionalPlugins = array();

                 foreach($relatedPluginInfo as $pluginName => $pluginInfo) {
                     if($pluginInfo['type'] == 'required' && ($pluginInfo['is_installed'] == false || $pluginInfo['is_active'] == false || $pluginInfo['has_ini_file'] == false)) {
                         $requiredPlugins[] = $pluginName;
                     } else {
                         $optionalPlugins[] = $pluginName;
                     }
                 } 
                 ?>

                 <?php if(count($requiredPlugins) > 0): ?>
                     <div class="required-plugins">
                     <p>The following plugins are required: <?php echo implode_array_to_english($requiredPlugins); ?>
                     </p>
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