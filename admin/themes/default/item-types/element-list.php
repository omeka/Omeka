<h3>Edit Current Elements:</h3>
<table width="100%">
	<thead>
		<tr>
			<th>Element Name</th>
			<th>Element Description</th>
			<th>Element Order</th>
			<th>Remove Element</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($elements as $key => $element): ?>

		    <tr class="element <?php echo is_odd($key) ? 'odd' : 'even'; ?>">
		        <td class="element-name"><strong><?php echo htmlentities($element->name); ?></strong></td>
				<td><?php echo htmlentities($element->description); ?></td>
		        <td class="element-order"><?php echo __v()->formText("Elements[$key][order]", $key+1, array('size'=>2)); // Key starts at 0 ?></td>

		        <td><a href="<?php echo uri(array('controller'=>'item-types', 'action'=>'delete-element', 'element-id'=>$element->id, 'item-type-id'=>$itemtype->id), 'default'); ?>" class="delete delete-element">[X]</a></td>

		        <?php echo __v()->formHidden("Elements[$key][element_id]", $element->id); ?>
		    </tr>
		<?php endforeach; ?>
	</tbody>
</table>