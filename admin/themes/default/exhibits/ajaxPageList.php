<?php 
	$section = exhibit_section($_REQUEST['id']);
	
	if(!$section or !has_permission('Exhibits', 'edit')) {
		header ("HTTP/1.0 404 Not Found"); 
		throw new Exception( 'no access!' );
	}	
	
	$section->loadPages(); 
?>


<?php foreach( $section->Pages as $key => $page ): ?>
<tr id="page_<?php echo $key; ?>">
	<td><?php text(array('name'=>"Pages[$key][order]",'size'=>2), $key); ?></td>
	<td><?php exhibit_layout($page->layout, false); ?></td>
	<td><?php echo $page->getItemCount(); ?></td>
	<td><?php echo $page->getTextCount(); ?></td>
	<td><a href="<?php echo uri('exhibits/editPage/'.$page->id); ?>">[Edit]</a></td>
	<td><a href="<?php echo uri('exhibits/deletePage/'.$page->id); ?>" class="delete-page">[Delete]</a></td>
</tr>
<?php endforeach; ?>