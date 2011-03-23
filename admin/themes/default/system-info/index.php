<?php head(array('title' => 'System Information', 'bodyclass' => 'system-info')); ?>

<h1>System Information</h1>

<div id="primary">
	<?php echo flash(); ?>
<pre id="info-field">
<?php foreach ($info as $category => $entries): ?>
<?php echo html_escape($category) . ':'; ?>

<?php foreach ($entries as $name => $value): ?>
<?php printf("    %-20s%s", html_escape($name) . ':', html_escape($value)); ?> 
<?php endforeach; ?>

<?php endforeach; ?>
</pre>
</div>

<?php foot();
