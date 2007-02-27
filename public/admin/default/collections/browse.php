<?php head(); ?>
<ul id="secondary-nav">
	
</ul>
<div id="content">
	<h2>Collections</h2>
	<table id="collections-table">
		<thead>
		<th scope="col">Collection Name</th>
		<th scope="col">Collector</th>
		<th scope="col">Description</th>
		<th scope="col">View Items</td>

		</thead>
		<tbody id="sorted">
		<?php foreach( $collections as $collection ): ?>
			<tr>
				<td><?php echo $collection->name; ?></td>
				<td><?php echo $collection->collector; ?></td>
				<td><?php echo $collection->description; ?></td>
				<td><a href="<?php echo uri(''); ?>">View</a></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php foot(); ?>