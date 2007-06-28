<?php 	
	$exhibit = exhibit($_REQUEST['id']);
	
	if(!$exhibit or !has_permission('Exhibits', 'edit')) {
		header ("HTTP/1.0 404 Not Found"); 
		throw new Exception( 'no access!' );
	}	
	$exhibit->loadSections();
?>


<?php foreach( $exhibit->Sections as $key => $section ): ?>
	<tr id="section_<?php echo $key; ?>">
		<td><?php text(array('name'=>"Sections[$key][order]",'size'=>2), $key); ?>
		<td><a href="<?php echo uri('exhibits/editSection/'.$section->id); ?>" class="edit-section">[Edit]</a></td>
		<td><a href="<?php echo uri('exhibits/deleteSection/'.$section->id); ?>"  class="delete-section">[Delete]</a></td>
		<td><?php echo $section->title; ?></td>
	</tr>
<?php endforeach; ?>