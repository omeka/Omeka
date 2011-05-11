<?php head(array('title'=>'Browse Plugins', 'content_class' => 'vertical-nav', 'bodyclass'=>'plugins primary')); ?>
<h1>Browse Plugins (<?php echo count($plugins) ?> total)</h1>
<?php common('settings-nav'); ?>

<div id="primary">
    <?php echo flash(); ?>
    
    <?php if ($plugins): ?>

        <table id="plugin-info">
            <tbody>
            <?php foreach($plugins as $pluginDirName => $plugin): ?>
                <?php echo $this->partial('plugins/plugin-info.php', array('pluginDirName' => $pluginDirName, 'plugin' => $plugin, 'loader'=>$loader, 'versionCheck'=>$versionCheck)); ?>
            <?php endforeach; ?>
            </tbody>
        </table>

<?php else: ?>
<p>You don't have any plugins installed.  Add them to the plugins directory to see them listed here.</p>
<?php endif; ?>

<p class="manageplugins">Add new plugins by downloading them from the <a href="http://omeka.org/add-ons/plugins/">Omeka Plugins  Directory</a>, or <a href="http://omeka.org/codex/Plugin_Writing_Best_Practices">write your own</a>!</p>
</div>

<?php foot(); ?>
