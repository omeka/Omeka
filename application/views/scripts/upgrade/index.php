<?php
$pageTitle = __('Upgrade Omeka');
head(array('title' => $pageTitle));
?>

<h1><?php echo __('Upgrade Your Omeka Database'); ?></h1> 
<p>
    <?php echo __('Your Omeka database is not compatible with your current version of Omeka.'); ?>
    <?php echo __('Please back up your existing database and then click the button to upgrade.'); ?>'
</p>
<?php echo link_to('upgrade', 'migrate', __('Upgrade Database'), array('id' => 'upgrade-database-link', 'class'=>'button')); ?>                        

<?php foot(); ?>