<?php foreach( $section->Pages as $key => $page ): ?>
<tr id="page_<?php echo $key; ?>">
	<td class="handle"><img src="<?php echo img('icons/arrow_move.png'); ?>" alt="Drag" /></td>
	<td><?php text(array('name'=>"Pages[$key][order]",'size'=>2), $key); ?></td>
	<td><?php exhibit_layout($page->layout, false); ?></td>
	<td><?php echo h($page->getItemCount()); ?></td>
	<td><?php echo h($page->getTextCount()); ?></td>
	<td><a href="<?php echo uri('exhibits/editPage/'.$page->id); ?>">[Edit]</a></td>
	<td><a href="<?php echo uri('exhibits/deletePage/'.$page->id); ?>" class="delete-page">[Delete]</a></td>
</tr>
<?php endforeach; ?>