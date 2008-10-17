<?php head(array('title'=>'Collection # '.collection('Id'), 'body_class'=>'collections')); ?>
<h1>Collection: <?php echo collection('Name');?></h1>

<div id="primary">
<div id="collection-info">

<?php if (has_permission('Collections', 'edit')): ?>    
<p> <?php echo link_to_collection('Edit', array('class'=>'edit'), 'edit'); ?></p>
<?php endif; ?>

<h2>Description:</h2> <p><?php echo collection('Description'); ?></p>

	<h2>Collectors:</h2>
	<ul id="collector-list">
		<li><?php echo collection('Collectors', array('delimiter'=>'</li><li>')); ?></li>
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