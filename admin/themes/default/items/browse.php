<?php head(array('title'=>'Browse Items', 'body_class'=>'items')); ?>
<div id="primary">
<?php echo flash(); ?>

<a class="add" id="add-item" href="<?php echo uri('items/add'); ?>">Add an Item</a>

<div id="search">
<?php echo simple_search(array(), url_for('items/browse')); ?>

<?php echo link_to_advanced_search(); ?>
</div>
<?php if ( total_results() ): ?>

<h1>Browse Items (<?php echo total_results();?> items total)</h1>

<div id="browse-meta">
<div class="pagination"><?php echo pagination(); ?></div>

<ul class="navigation" id="view-style">
	<li><a id="simple-view" href="<?php echo current_uri(array('view'=>'simple')); ?>"<?php if($_GET['view'] == 'simple' || $_GET['view'] == '') echo ' class="current"';?>>List View</a></li>
	<li><a id="detailed-view" href="<?php echo current_uri(array('view'=>'detailed')); ?>"<?php if($_GET['view'] == 'detailed') echo ' class="current"';?>>Detailed View</a></li>
</ul>	
</div>
<form action="<?php echo uri('items/power-edit'); ?>" method="post" accept-charset="utf-8">

<fieldset id="view-choice">
	<?php 
		switch ($_GET['view']) {
			case 'detailed':
			    common('detailed-view', compact('items'), 'items');
			    break;
			case 'simple':
			default:
				common('simple-view', compact('items'), 'items');
				break;
		}
	 ?>
</fieldset>

<fieldset>
    <input type="submit" class="submit" id="save-changes" name="submit" value="Save Changes" />
</fieldset>

</form>

<?php elseif(!total_items(true)): ?>
	<div id="no-items">
	    <h1>Browse Items</h1>
	<p>There are no items in the archive yet.
	
	<?php if(has_permission('Items','add')): ?>
		  Why don't you <a href="<?php echo uri('items/add'); ?>">add some</a>?</p>
	<?php endif; ?>
</div>
	
<?php else: ?>
	<h1>The query searched <?php total_items(); ?> items and returned no results.</h1>
	
	<?php items_search_form(array('id'=>'search'), uri('items/browse')); ?>
<?php endif; ?>

<?php fire_plugin_hook('append_to_items_browse', $items); ?>

</div>
<?php foot(); ?>
