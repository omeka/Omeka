<?php head(array('title'=>'Browse Items','content_class' => 'horizontal-nav', 'body_class'=>'items primary browse-items')); ?>
<h1>Browse Items (<?php echo total_results();?> items total)</h1>
<p id="add-item" class="add-button"><a class="add" href="<?php echo uri('items/add'); ?>">Add an Item</a></p>

<?php if ( total_results() ): ?>

<ul id="section-nav" class="navigation">
<?php
	$section_nav = array(
	    'List View' => current_uri(array('view'=>'simple')), 
	    'Detailed View' => current_uri(array('view'=>'detailed'))
	    );
				
	echo nav($section_nav);
?>
</ul>
<?php endif; ?>

<div id="primary">
	<div id="search-form">
	<?php echo simple_search(); ?>

	<?php echo link_to_advanced_search(); ?>
	</div>
<?php echo flash(); ?>

<?php if ( total_results() ): ?>

<div id="browse-meta">
<div class="pagination"><?php echo pagination_links(); ?></div>

	
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
    <input type="submit" class="submit submit-medium" id="save-changes" name="submit" value="Save Changes" />
</fieldset>

</form>

<?php elseif(!total_items(true)): ?>
	<div id="no-items">
	<p>There are no items in the archive yet.
	
	<?php if(has_permission('Items','add')): ?>
		  Why don&#8217;t you <a href="<?php echo uri('items/add'); ?>">add one</a>?</p>
	<?php endif; ?>
</div>
	
<?php else: ?>
	<h1>The query searched <?php total_items(); ?> items and returned no results.</h1>
	
	<?php items_search_form(array('id'=>'search'), uri('items/browse')); ?>
<?php endif; ?>

<?php fire_plugin_hook('append_to_items_browse', $items); ?>

</div>
<?php foot(); ?>
