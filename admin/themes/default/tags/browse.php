<?php head(array('title'=>'Browse Tags', 'body_class'=>'tags')); ?>
<?php common('archive-nav'); ?>
<div id="primary">
<h1>Tags</h1>

<?php if(has_permission('Tags', 'edit')): ?>
	<h2><a href="<?php echo uri('tags/edit'); ?>">Edit/Rename</a></h2>
<?php endif; ?>

<?php if(has_permission('Tags', 'remove')): ?>
	<h2><a href="<?php echo uri('tags/delete'); ?>">Delete</a></h2>
<?php endif; ?>

<h3>View tags for: 
	<?php
		if ( $browse_for == 'Item' ): ?>
		<em>Items</em> <a href="<?php echo current_uri(array('tagType'=>'Exhibit')); ?>">Exhibits</a>
	<?php else: ?>
		<a href="<?php echo current_uri(array('tagType'=>'Item')); ?>">Items</a> <em>Exhibits</em>		
	<?php endif; ?>	
</h3>
	
	<h3>Sort by: 		
		<?php if($_GET['sort'] != 'most'): ?>
			<a href="<?php echo current_uri(array('sort'=>'most')); ?>">Most</a>
		<?php else: ?>
			<em>Most</em>
		<?php endif; ?>
		
		<?php if($_GET['sort'] != 'least'):?>
			<a href="<?php echo current_uri(array('sort'=>'least')); ?>">Least</a> 
		<?php else: ?> 
			<em>Least</em>
		<?php endif; ?>
		
		<?php if($_GET['sort'] != 'alpha'): ?>
			<a href="<?php echo current_uri(array('sort'=>'alpha')); ?>">Alphabetical</a>
		<?php else: ?>
			<em>Alphabetical</em>
		<?php endif; ?>
		
		<?php if($_GET['sort'] != 'recent'): ?>
			<a href="<?php echo current_uri(array('sort'=>'recent')); ?>">Recent</a>
		<?php else: ?>
			<em>Recent</em>
		<?php endif; ?>
	</h3>

<?php if ( total_results(true) ): ?>
	
	
	
	<?php
	tag_cloud($tags, ($browse_for == 'Item') ? uri('items/browse/'): uri('exhibits/browse/'));
	?>
<?php else: ?>
	<h2>There are no tags to display.  You must first tag some items.</h2>
<?php endif; ?>
</div>
<?php foot(); ?>