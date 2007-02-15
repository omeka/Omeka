<h2>List of plugins:</h2>

<ol>
<?php foreach( $plugins as $key => $plugin ): ?>
	<li><a href="<?php echo url(array('id'=>$plugin->id, 'controller' => 'plugins', 'action'=>'show'), 'id'); ?>"><?php echo $plugin->name; ?></a> </li>
<?php endforeach; ?>
</ol>