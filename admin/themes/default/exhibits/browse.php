<?php head(); ?>
<ul id="tertiary-nav" class="navigation">
	<?php 
		nav(array('Browse Exhibits' => uri('exhibits/browse'), 'Add Exhibit' => uri('exhibits/add')));
	?>
</ul>
<table id="exhibits">
	<thead>
	<tr>
		<th>Identification #</th>
		<th>Title</th>
		<th>Tags</th>
		<th>Edit?</th>
		<th>Delete?</th>
	</tr>
	</thead>
	<tbody>
<?php foreach( $exhibits as $key=>$exhibit ): ?>
	<tr class="exhibit <?php if($key%2==1) echo ' even'; else echo ' odd'; ?>">
		<td><?php echo $exhibit->id;?></td>
		<td><a href="<?php echo uri('exhibits/show/'.$exhibit->id); ?>"><?php echo $exhibit->title; ?></a></td>
		<td><?php tag_string($exhibit, uri('exhibits/browse/tag/')); ?></td>
		<td><a href="<?php echo uri('exhibits/edit/'.$exhibit->id); ?>">[Edit]</a></td>
		<td><a href="<?php echo uri('exhibits/delete/'.$exhibit->id); ?>">[Delete]</a></td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php foot(); ?>