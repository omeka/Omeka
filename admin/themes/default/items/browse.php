<?php head(); ?>
<?php common('archive-nav'); ?>

<div id="primary">
<ul id="tertiary-nav" class="navigation">
	<?php 
		if(has_permission('Items','add')) {
			nav(array('Browse Items' => uri('items/browse'), 'Add Item' => uri('items/add')));
		}
	?>
</ul>
<?php if ( total_results(true) ): ?>

<h1>Browse Items (<?php echo total_results(true);?> items total)</h1>

<h2 id="search-header" class="close">Search Items</h2>
<?php include('searchform.php'); ?>

	<div class="pagination"><?php echo pagination(); ?></div>

<ul class="navigation" id="view-style">
	<li><a id="detailed" href="?view=detailed"<?php if($_GET['view'] == 'detailed' || $_GET['view'] == '') echo ' class="current"';?>>Detailed</a></li>
	<li><a id="simple" href="?view=simple"<?php if($_GET['view'] == 'simple') echo ' class="current"';?>>Simple</a></li>
</ul>	

<form action="<?php echo uri('items/powerEdit'); ?>" method="post" accept-charset="utf-8">
	
	<div id="view-choice">
	<?php if($_GET['view'] == 'detailed' || $_GET['view'] == ''):?>
		<?php include('detailed-view.php'); ?>
	<?php elseif($_GET['view'] == 'simple'):?>
		<?php include('simple-view.php'); ?>
	<?php endif; ?>
	</div>

<input type="submit" name="submit" value="Modify these Items --&gt;">

</form>

<?php elseif(!total_items(true)): ?>
	<div id="no-items">
	<h2>There are no items in the archive yet.
	
	<?php if(has_permission('Items','add')): ?>
		  Why don't you <a href="<?php echo uri('items/add'); ?>">add some</a>?</h2>
	<?php endif; ?>
</div>
	
<?php else: ?>
	<h2>The query searched <?php total_items(); ?> items and returned no results.</h2>
	
	<h2 id="search-header" class="close">Search Items</h2>
	<?php include('searchform.php'); ?>
<?php endif; ?>

</div>
<?php foot(); ?>