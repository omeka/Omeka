<?php
$pageTitle = __('404 Page Not Found');
echo head(array('title'=>$pageTitle));
?>
<div id="primary">
    <h1><?php echo $pageTitle; ?></h1>
    <p><?php echo __('%s is not a valid URL.', html_escape($badUri)); ?></p>
</div>
<?php echo foot(); ?>
