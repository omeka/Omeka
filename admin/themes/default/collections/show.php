<?php head(array('title'=>'Collection # '.collection('Id'), 'bodyclass'=>'collections show')); ?>
<h1>Collection: <?php echo strip_formatting(collection('Name'));?><span class="view-public-page">[ <a href="<?php echo public_uri('collections/show/'.collection('id')); ?>">View Public Page</a> ]</span> </h1>
<?php if (has_permission('Collections', 'edit')): ?>    
<p id="edit-collection" class="edit-button"><?php echo link_to_collection('Edit this Collection', array('class'=>'edit'), 'edit'); ?></p>
<?php endif; ?>

<div id="primary">
<div id="collection-info">
<h2>Description</h2> 
<p><?php echo collection('Description'); ?></p>

	<h2>Collectors</h2>
	<ul id="collector-list">
    	<?php if (collection_has_collectors()): ?> 
    	<li><?php echo collection('Collectors', array('delimiter'=>'</li><li>')); ?></li>
		<?php else: ?>
		<li>No collectors</li>
		<?php endif; ?>	
	</ul>

</div>
<div id="collection-items">
	<h2>Recently Added to <?php echo collection('Name'); ?></h2>
	
	<ul>
	<?php while (loop_items_in_collection(10)): ?>
		<li><span class="title"><?php echo link_to_item(); ?></span> <span class="date"><?php echo date('m.d.Y', strtotime(item('Date Added'))); ?></span></li>
	<?php endwhile;?>
	</ul>
	<h4>Total Number of Items in Collection: <?php echo total_items_in_collection();?></h4>
	
</div>

<?php fire_plugin_hook('admin_append_to_collections_show_primary', $collection); ?>
</div>
<?php foot(); ?>