<?php head(); ?>
<table>
	<tr>
		<th>Identification #</th>
		<th>Title</th>
		<th>Tags</th>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
	</tr>
<?php foreach( $exhibits as $exhibit ): ?>
	
	<tr>
		<td><?php 	echo $exhibit->id;?></td>
		<td><a href="<?php echo uri('exhibits/show/'.$exhibit->id); ?>"><?php echo $exhibit->title; ?></a></td>
		<td><?php tag_string($exhibit, uri('exhibits/browse/tag/')); ?></td>
		<td><a href="<?php echo uri('exhibits/edit/'.$exhibit->id); ?>">[Edit]</a></td>
		<td><a href="<?php echo uri('exhibits/delete/'.$exhibit->id); ?>">[Delete]</a></td>
	</tr>
<?php endforeach; ?>
</table>
<?php foot(); ?>