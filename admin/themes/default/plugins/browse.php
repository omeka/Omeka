<?php head(); ?>
<?php common('settings-nav'); ?>

	<div id="installed">
<h2>List of plugins:</h2>

<table>
<?php foreach( $plugins as $key => $plugin ): ?>
	<tr>
		<td><a href="<?php echo uri('plugins/show/'.$plugin->id); ?>"><?php echo $plugin->name; ?></a> </td>
		<td><?php echo $plugin->description;?></td>
		<td><?php echo $plugin->author;?></td>
	</tr>
<?php endforeach; ?>
</table>
</div>

<?php foot(); ?>