<?php head();?>
<?php common('archive-nav'); ?>

<?php js('editable');?>
<div id="primary">
<div id="type-info">
<h2>Type: <?php echo $type->name;?>
	 <?php if ( has_permission('Types','edit') ): ?>
	 	<a id="edit" href="<?php echo uri('types/edit/'.$type->id); ?>">(Edit)</a>
	<a id="delete" href="<?php echo uri('types/delete/'.$type->id); ?>">(Delete)</a>
	 <?php endif; ?>
</h2>
	<p><?php echo $type->description; ?></p>
	<h3>Type Metadata</h3>
	<dl class="type-metadata">

		<?php foreach($type->Metafields as $metafield): ?>
		<div class="name"><?php echo $metafield->name; ?></div>
		<div class="description"><?php echo $metafield->description; ?></div>
		<?php endforeach; ?>

		
	</dl>
</div>

<div id="type-items">
	<h3>Recent <?php echo $type->name; ?></h3>
	<?php if($type->Items): ?>
	<ul>
	<?php foreach ($type->Items as $key => $item): ?>
	<?php if ($key < 10): ?>
	<li><span class="title"><a href="<?php echo uri('items/show/'.$item->id); ?>"><?php echo $item->title; ?></a></span> <span class="date"><?php echo date('m.d.Y', strtotime($item->added)); ?></span></li>
	<?php endif; endforeach;?>
	</ul>
	
	<?php else: ?>
	<p>There are no items with the type <?php echo $type->name; ?></p>
	<?php endif;?>
		
</div>
</div>
<?php foot();?>