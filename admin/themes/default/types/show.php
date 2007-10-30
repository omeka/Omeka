<?php head(array('title'=>'Type: '.h($type->name),'body_class'=>'types'));?>
<?php common('archive-nav'); ?>

<?php js('editable');?>
<div id="primary">
	<div id="type-info">
	<h1>Type: <?php echo h($type->name);?></h1>

		<p><?php if ( has_permission('Types','edit') ): ?>
		 	<a class="edit" href="<?php echo uri('types/edit/'.$type->id); ?>">Edit</a>
		 <?php endif; ?></p>
		
		<p><?php echo h($type->description); ?></p>
		<h2>Type Metadata</h2>
		<dl class="type-metadata">

			<?php foreach($type->Metafields as $metafield): ?>
			<dt><?php echo h($metafield->name); ?></dt>
			<dd><?php echo h($metafield->description); ?></dd>
			<?php endforeach; ?>
		
		</dl>
	</div>

	<div id="type-items">
		<h2>Recent Items with Type <?php echo h($type->name); ?></h2>
		<?php if($type->Items != null): ?>
		<ul>
		<?php foreach ($type->Items as $key => $item): ?>
		<?php if ($key < 10): ?>
		<li><a href="<?php echo uri('items/show/'.$item->id); ?>"><span class="title"><?php echo h($item->title); ?></span> <span class="date"><?php echo date('m.d.Y', strtotime($item->added)); ?></span></a></li>
		<?php endif; endforeach;?>
		</ul>
	
		<?php else: ?>
		<p>There are no items with the type <?php echo h($type->name); ?></p>
		<?php endif;?>
		
	</div>
</div>
<?php foot();?>