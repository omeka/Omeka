<?php head(array('title'=>'Type: '.h($itemtype->name),'body_class'=>'types'));?>


<div id="primary">
	<div id="type-info">
	    <h1>Type: <?php echo h($itemtype->name);?></h1>

		<p><?php if ( has_permission('ItemTypes','edit') ): ?>
		 	<a class="edit" href="<?php echo url_for_record($itemtype, 'edit', 'item-types'); ?>">Edit</a>
		 <?php endif; ?></p>
		
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
		<li><a href="<?php echo url_for_item(); ?>"><span class="title"><?php echo item('Title', ', '); ?></span> <span class="date"><?php echo date('m.d.Y', strtotime(item('Date Added'))); ?></span></a></li>
		<?php endwhile;?>
		</ul>
	
		<?php else: ?>
		<p>There are no items with the type <?php echo htmlentities($itemtype->name); ?></p>
		<?php endif;?>
		
	</div>
</div>
<?php foot();?>