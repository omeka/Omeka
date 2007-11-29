<table id="items" class="simple" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
		<th scope="col">ID</th>
		<th scope="col">Title</th>
		<th scope="col">Type</th>
		<th scope="col">Creator</th>
		<th scope="col">Date Added</th>
		<th scope="col">Public</th>
		<th scope="col">Featured</th>
		<th scope="col">Edit?</th>
		</tr>
	</thead>
	<tbody>
<?php foreach($items as $key => $item):?>
<tr class="item<?php if($key%2==1) echo ' even'; else echo ' odd'; ?>">
	<td scope="row"><?php echo h($item->id);?></td> 
	<td><?php link_to_item($item); ?></td>
	<td><?php echo h($item->Type->name); ?></td>
	<td><?php echo h($item->creator); ?></td>	
	<td><?php echo date('m.d.Y', strtotime($item->added)); ?></td>
	<td><?php checkbox(array('name'=>"items[$item->id][public]",'class'=>"make-public"), $item->public); ?></td>
	<td><?php checkbox(array('name'=>"items[$item->id][featured]",'class'=>"make-featured"), $item->featured); ?>
		<?php hidden(array('name'=>"items[$item->id][id]"), $item->id); ?>
	</td>
	<td><?php link_to_item($item, 'edit', 'Edit', array('class'=>'edit')); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>



