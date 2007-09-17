<?php head(); ?>
<?php common('settings-nav'); ?>

<div id="primary">
<h2>List of plugins:</h2>

<form action="<?php echo uri('plugins/activate'); ?>" method="post" accept-charset="utf-8">
<table>
<?php if ($plugins): 
	foreach( $plugins as $key => $plugin ): ?>
	<tr>
		<td><?php echo h($plugin->directory); ?></td>
		<td><?php echo h($plugin->name); ?></td>
		<td><?php echo h($plugin->description);?></td>
		<td><?php echo h($plugin->author);?></td>
		<td><button name="activate" type="submit" value="<?php echo $plugin->directory; ?>"><?php echo ($plugin->active) ? 'De-activate' : 'Activate'; ?></button>
	</tr>
<?php endforeach; 
	else:
		echo "You don't have any plugins installed.  Add them to the plugins directory to see them listed here.";
	endif; ?>
</table>
</form>

</div>

<?php foot(); ?>