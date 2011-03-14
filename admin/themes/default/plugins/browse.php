<?php 
$pageTitle = __('Browse Plugins');
head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'plugins primary')); ?>
<h1><?php echo $pageTitle; ?> <?php echo __('(%s total)', count($plugins)); ?></h1>
<?php common('settings-nav'); ?>

<div id="primary">
    <?php echo flash(); ?>
    
    <?php if ($plugins): ?>

        <table id="plugin-info">
            <thead>
                <tr>
                    <th><?php echo __('Plugin'); ?></th>
                    <th><?php echo __('Action'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($plugins as $pluginDirName => $plugin): ?>
                <?php echo $this->partial('plugins/plugin-info.php', array('pluginDirName' => $pluginDirName, 'plugin' => $plugin, 'loader'=>$loader, 'versionCheck'=>$versionCheck)); ?>
            <?php endforeach; ?>
            </tbody>
        </table>

<?php else: ?>
<p><?php echo __('You do not have any plugins installed. Add them to the plugins directory to see them listed here.'); ?></p>
<?php endif; ?>

<p class="manageplugins"><?php echo __('Add new plugins by downloading them from the <a href="http://omeka.org/add-ons/plugins/">Omeka Plugins Directory</a>, or <a href="http://omeka.org/codex/Plugin_Writing_Best_Practices">write your own</a>!'); ?></p>
</div>

<?php foot(); ?>