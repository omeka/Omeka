<?php
$pageTitle = __('403 Page Forbidden');
head(array('title' => $pageTitle));
?>
<div id="primary">
    <h1><?php echo $pageTitle; ?></h1>
    <?php echo flash(); ?>
    <p><?php echo __('You do not have permission to access this page.'); ?></p>
</div>
<?php foot(); ?>
