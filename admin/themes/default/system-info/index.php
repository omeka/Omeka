<?php
$title = __('System Information');
head(array('title' => $title, 'bodyclass' => 'system-info')); ?>

<h1><?php echo $title; ?></h1>

<div id="primary">
	<?php echo flash(); ?>
<pre id="info-field">
<?php foreach ($info as $category => $entries): ?>
<?php echo html_escape(__($category)) . ':'; ?>

<?php foreach ($entries as $name => $value): ?>
<?php printf("    %-20s%s", html_escape($name) . ':', html_escape($value)); ?> 
<?php endforeach; ?>

<?php endforeach; ?>
</pre>
</div>

<?php foot();
