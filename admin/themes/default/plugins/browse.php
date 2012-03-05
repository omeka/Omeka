<?php 
$pageTitle = __('Plugins');
head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'plugins browse')); ?>    

    <?php if ($plugins): ?>
    
               <?php echo flash(); ?>            

            <?php foreach($plugins as $pluginDirName => $plugin): ?>
                <?php echo $this->partial('plugins/plugin-info.php', array('pluginDirName' => $pluginDirName, 'plugin' => $plugin, 'loader'=>$loader)); ?>
            <?php endforeach; ?>

<?php else: ?>
<p><?php echo __('You do not have any plugins installed. Add them to the plugins directory to see them listed here.'); ?></p>
<?php endif; ?>
        
        </div>
        
<?php foot(); ?>