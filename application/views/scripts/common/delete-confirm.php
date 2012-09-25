<?php
$class = get_class($record);
$pageTitle = __('Delete %s', Inflector::titleize($class));

if (!$isPartial):
echo head(array('title' => $pageTitle));
?>
<h1><?php echo $pageTitle; ?></h1>
<div id="primary">   
<?php endif; ?>
<div title="<?php echo $pageTitle; ?>">
<h2><?php echo __('Are you sure?'); ?></h2>
<?php echo text_to_paragraphs(html_escape($confirmMessage)); ?>
<?php echo $form; ?>
</div>
<?php if (!$isPartial): ?>
</div>
<?php echo foot(); ?>
<?php endif; ?>
