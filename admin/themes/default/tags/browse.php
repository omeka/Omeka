<?php head(array('title'=>'Browse Tags', 'content_class' => 'horizontal-nav','body_class'=>'tags primary')); ?>
<h1>Tags</h1>

<?php common('tags-nav'); ?>

<div id="primary">
<?php if ( total_results() ): ?>
	
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

	
	
	
	<?php
	echo tag_cloud($tags, ($browse_for == 'Item') ? uri('items/browse/'): uri('exhibits/browse/'));
	?>
<?php else: ?>
	<p>There are no tags to display.  You must first tag some items.</p>
<?php endif; ?>
</div>
<?php foot(); ?>