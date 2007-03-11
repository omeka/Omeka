<?php head(); ?>
<a href="<?php echo uri('plugins/edit/'.$plugin->id); ?>">Edit</a>

<h2><?php echo $plugin->name;?></h2>


<?php foreach($plugin->config as $key => $value): ?>
	
	<?php echo $key; ?>) <?php echo $value; ?>
	<br/>
<?php endforeach;?>

<?php if ( $plugin->active ): ?>
	<h3>Plugin is active</h3>
<?php else: ?>
	<h3>Plugin is inactive</h3>
<?php endif; ?>

<?php foot(); ?>