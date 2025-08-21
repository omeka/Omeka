<?php
$pageTitle = __('403 Page Forbidden');
echo head(['title' => $pageTitle]);
?>

<h1><?php echo $pageTitle; ?></h1>
<?php echo flash(); ?>
<p><?php echo __('You do not have permission to access this page.'); ?></p>

<?php echo foot(); ?>
