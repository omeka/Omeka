<h2>List of plugins:</h2>

<ol>
<?php foreach( $plugins as $key => $plugin ): ?>
	<li><a href="<?php echo $this->url(array('id'=>$plugin->id, 'action'=>'edit'), 'id'); ?>"> <?php echo $plugin->name; ?></a> </li>
<?php endforeach; ?>
</ol>