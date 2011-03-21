<?php
$class = get_class($record);
$pageTitle = 'Delete ' . Inflector::titleize($class);

if (!$isPartial):
head(array('title' => $pageTitle));
?>
<h1><?php echo $pageTitle; ?></h1>
<div id="primary">   
<?php endif; ?>
<div title="<?php echo $pageTitle; ?>">
<h2>Are you sure?</h2>
<?php echo nls2p(html_escape($confirmMessage)); ?>
<?php echo $form; ?>
</div>
<?php if (!$isPartial): ?>
</div>
<?php foot(); ?>
<?php endif; ?>