<?php head(); ?>
<?php common('settings-nav'); ?>

<div id="primary">
<h2>List of plugins:</h2>

<table>
<?php foreach( $plugins as $key => $plugin ): ?>
	<tr>
		<td><a href="<?php echo uri('plugins/show/'.$plugin->id); ?>"><?php echo h($plugin->name); ?></a> </td>
		<td><?php echo h($plugin->description);?></td>
		<td><?php echo h($plugin->author);?></td>
	</tr>
<?php endforeach; ?>
</table>
</div>

<?php foot(); ?>