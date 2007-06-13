<?php head(); ?>

<h2>Install the <?php echo get_class($plugin); ?> Plugin</h2>

<?php 
	$meta = $plugin->getMetaInfo(); 
?>
<dl>
	<dt>Author:</dt>
	<dd><?php echo $meta['author']; ?>
	<dt>Description:</dt>
	<dd><?php echo $meta['description']; ?>
</dl>

<?php 
	$config = $plugin->getConfigDefinition(); 
?>
<form method="post" accept-charset="utf-8">
	<fieldset>
	<?php foreach( $config as $name => $def ): ?>
		<div class="field">	
			<label for="<?php echo $name; ?>"><?php echo $name; ?></label>	
			<p class="description"><?php echo $def['description']; ?></p>
			<input type="text" name="config[<?php echo $name; ?>]" value="<?php echo $def['default']; ?>" />
		</div>
	<?php endforeach; ?>
	</fieldset>
	
	<fieldset>
		<input type="submit" name="submit" value="Install this plugin --&gt;" />
	</fieldset>
</form>

<?php foot(); ?>