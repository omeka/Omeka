<?php 
$pageTitle = __('Uninstalled Plugins') . ' ' . __('(%s total)', count($uninstalledPlugins));
head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'plugins browse')); 
?>

    <?php include('plugin-tabs.php'); ?>
    
    <?php $plugins = $uninstalledPlugins; ?>

    <?php include('plugin-table.php'); ?>

</div>

<?php foot(); ?>