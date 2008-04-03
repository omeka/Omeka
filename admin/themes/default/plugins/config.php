<?php head(array('title'=>'Plugin Configuration', 'body_class'=>'plugins')); ?>

<div id="primary">
	
<h2>Please Configure The <?php echo $plugin; ?> Plugin</h2>

<form method="post">

<?php echo $config; ?>

<input type="submit" name="install_plugin" value="Save Configuration!" />

</form>

</div>

<?php foot(); ?>