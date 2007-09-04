<?php head(); ?>
<?php common('archive-nav'); ?>

<div id="primary">
<?php echo flash(); ?>
<?php if ( total_results(true) ): ?>

<h1 class="floater">Browse Items (<?php echo total_results(true);?> items total)</h1>
<a class="add" id="add-item" href="<?php echo uri('items/add'); ?>">Add an Item</a>

<h2 id="search-header" class="close">Search Items</h2>
<?php items_filter_form(array('id'=>'search'), uri('items/browse')); ?>

<div id="browse-meta">
<div class="pagination"><?php echo pagination_links(); ?></div>

<ul class="navigation" id="view-style">
	<li><a id="simple" href="?view=simple"<?php if($_GET['view'] == 'simple' || $_GET['view'] == '') echo ' class="current"';?>>List View</a></li>
	<li><a id="detailed" href="?view=detailed"<?php if($_GET['view'] == 'detailed') echo ' class="current"';?>>Detailed View</a></li>
</ul>	
</div>
<form action="<?php echo uri('items/powerEdit'); ?>" method="post" accept-charset="utf-8">

<fieldset id="view-choice">
	<?php 
		switch ($_GET['view']) {
			case 'detailed':
				common('_detailed', compact('items'), 'items');
				break;
			case 'simple':
			default:
				common('_simple', compact('items'), 'items');
				break;
		}
	 ?>
</fieldset>

<input type="submit" name="submit" value="Save Changes" />

</form>

<?php elseif(!total_items(true)): ?>
	<div id="no-items">
	<h1>There are no items in the archive yet.
	
	<?php if(has_permission('Items','add')): ?>
		  Why don't you <a href="<?php echo uri('items/add'); ?>">add some</a>?</h1>
	<?php endif; ?>
</div>
	
<?php else: ?>
	<h1>The query searched <?php total_items(); ?> items and returned no results.</h1>
	
	<h2 id="search-header" class="close">Search Items</h2>
	<?php items_filter_form(array('id'=>'search'), uri('items/browse')); ?>
<?php endif; ?>

</div>
<?php foot(); ?>
