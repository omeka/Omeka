<?php head(array('title'=>'Browse Types','body_class'=>'item-types')); ?>
<h1>Item Types</h1>
<p id="add-item-type" class="add-button"><a class="add" href="<?php echo uri('item-types/add'); ?>">Add an Item Type</a></p>

<div id="primary">
	<table>
		<thead>
			<tr>
				<th>Type Name</th>
				<th>Description</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
<?php foreach( $itemtypes as $itemtype ): ?>
<tr class="itemtype">
	 <td width="20%"><a href="<?php echo record_uri($itemtype, 'show', 'item-types'); ?>"><?php echo htmlentities($itemtype->name); ?></a></td>
	<td width="70%"><?php echo htmlentities($itemtype->description); ?></td>
	<td><a class="edit" href="<?php echo uri('item-types/edit/'.$itemtype->id); ?>">Edit</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php foot(); ?>