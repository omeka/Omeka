<?php 
$pageTitle = __('Inctive Plugins') . ' ' . __('(%s total)', count($inactivePlugins));
head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'plugins browse')); 
$noPlugins = __('You have no inactive plugins.');
?>

    <?php include('plugin-tabs.php'); ?>

    <?php $plugins = $inactivePlugins; ?>
    
    <?php include('plugin-table.php'); ?>

        </div>

<?php foot(); ?>