<h2><?php echo $plugin->name;?></h2>

<form method="post">

<?php foreach($plugin->config as $key => $value): ?>
	
	<label for="<?php echo $key; ?>"><?php echo $key; ?></label>
	<input type="text" name="config[<?php echo $key; ?>]" id="<?php echo $key;?>" value="<?php echo $value; ?>" />
	<br/>
<?php endforeach;?>

	<?php $value = ($plugin->active) ? "De-activate this plugin" : "Activate this plugin"; ?>
	<input type="submit" name="active" value="<?php echo $value; ?>" />
<input type="submit" name="submit" value="submit" />

</form>