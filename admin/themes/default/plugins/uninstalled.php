<?php 
$pageTitle = __('Uninstalled Plugins') . ' ' . __('(%s total)', count($uninstalledPlugins));
echo head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'plugins browse')); 
$noPlugins = __('All available plugins have been installed.');
?>

    <?php include('plugin-tabs.php'); ?>
    
    <?php $plugins = $uninstalledPlugins; ?>

    <?php include('plugin-table.php'); ?>

</div>

<?php echo foot(); ?>