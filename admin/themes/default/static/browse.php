<?php head(); ?>
<h2>Routes</h2>
<form method="post">
	<?php foreach( $routes as $key => $route ): ?>
		<?php  echo $route->name; ?>
		<a href="<?php echo uri('static/'."?activate=true&id={$route->id}"); ?>"><?php if(!$route->active): ?>Activate<?php else: ?>De-activate<?php endif; ?></a>
	<?php endforeach; ?>
	
	<p>Add a new static page:</p>
	<?php text(array('name'=>'name','id'=>'name'), 'Name of Page'); ?>
	<?php text(array('name'=>'route','id'=>'route'), 'Relative Url'); ?>
	<?php text(array('name'=>'path','id'=>'path'), 'Path within theme folder'); ?>
	
	<input type="submit" name="submit" value="Add this page" />
</form>
<?php foot(); ?>