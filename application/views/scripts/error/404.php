<?php
$pageTitle = __('404 Page Not Found');
echo head(array('title'=>$pageTitle));
?>
<h1><?php echo $pageTitle; ?></h1>
<p><?php echo __('%s is not a valid URL.', html_escape($badUri)); ?></p>
<?php echo foot(); ?>
