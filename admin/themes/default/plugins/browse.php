<?php 
$pageTitle = __('All Plugins') . ' ' . __('(%s total)', count($plugins));
head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'plugins browse')); 
$noPlugins = __('You do not have any plugins installed. Add them to the plugins directory to see them listed here.');
?>

    <?php include('plugin-tabs.php'); ?>

    <?php include('plugin-table.php'); ?>
    
    </div>

<?php foot(); ?>
