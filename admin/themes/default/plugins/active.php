<?php 
$pageTitle = __('Active Plugins') . ' ' . __('(%s total)', count($activePlugins));
head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'plugins browse')); 
$noPlugins = __('You do not have any active plugins.');
?>

    <?php include('plugin-tabs.php'); ?>
    
    <?php $plugins = $activePlugins; ?>

    <?php include('plugin-table.php'); ?>

</div>

<?php foot(); ?>