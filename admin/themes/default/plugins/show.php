<?php head(); ?>
<?php common('settings-nav'); ?>
<div id="primary">
<a href="<?php echo uri('plugins/edit/'.$plugin->id); ?>">Edit</a>

<h2><?php echo h($plugin->name);?></h2>

<?php if(!empty($plugin->config)): ?>
	<?php foreach($plugin->config as $key => $value): ?>
	
		<?php echo $key; ?>) <?php echo $value; ?>
		<br/>
	<?php endforeach;?>
<?php endif; ?>

<?php if ( $plugin->active ): ?>
	<h3>Plugin is active</h3>
<?php else: ?>
	<h3>Plugin is inactive</h3>
<?php endif; ?>
</div>
<?php foot(); ?>