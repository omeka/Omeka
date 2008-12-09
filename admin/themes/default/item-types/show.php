<?php head(array('title'=>'Type: '.htmlentities($itemtype->name),'body_class'=>'types'));?>
<h1>Type: <?php echo htmlentities($itemtype->name);?></h1>
<?php if ( has_permission('ItemTypes','edit') ): ?>
<p id="edit-itemtype" class="edit-button"><a class="edit" href="<?php echo record_uri($itemtype, 'edit', 'item-types'); ?>">Edit this Item Type</a></p>
<?php endif; ?>

<div id="primary">
	<div id="type-info">


		
		<p><?php echo htmlentities($itemtype->description); ?></p>
		<h2>Type Metadata</h2>
		<dl class="type-metadata">

			<?php foreach($itemtype->Elements as $element): ?>
			<dt><?php echo htmlentities($element->name); ?></dt>
			<dd><?php echo htmlentities($element->description); ?></dd>
			<?php endforeach; ?>
		
		</dl>
	</div>

	<div id="type-items">
		<h2>Recent Items with Type <?php echo htmlentities($itemtype->name); ?></h2>
		<?php if($itemtype->Items != null): ?>
		<ul>
	    <?php set_items_for_loop($itemtype->Items); ?>
		<?php while(loop_items()): ?>
		<li><a href="<?php echo item_uri(); ?>"><span class="title"><?php echo item('Dublin Core', 'Title'); ?></span> <span class="date"><?php echo date('m.d.Y', strtotime(item('Date Added'))); ?></span></a></li>
		<?php endwhile;?>
		</ul>
	
		<?php else: ?>
		<p>There are no items with the type <?php echo htmlentities($itemtype->name); ?></p>
		<?php endif;?>
		
	</div>
	
	<?php fire_plugin_hook('admin_append_to_item_types_show_primary', $itemtype); ?>
</div>
<?php foot();?>