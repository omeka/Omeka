<?php head(); ?>
<form method="post">
	<fieldset>
		<legend>Exhibit Metadata</legend>
	<?php 
		echo flash();
		text(array('name'=>'title', 'id'=>'title'), $exhibit->title, 'Exhibit Title');
		textarea(array('name'=>'description', 'id'=>'description'), $exhibit->description, 'Exhibit Description'); 
		textarea(array('name'=>'credits', 'id'=>'credits'), $exhibit->credits,'Exhibit Credits');
		
		

		
		text(array('name'=>'tags', 'id'=>'tags'), tag_string($exhibit->userTags(current_user()->id),null,', ',true), 'Your Exhibit Tags');
		
		//Super users should be able to edit all the tags
		if(has_permission('super')) {
			text(array('name'=>'all_tags', 'id'=>'all_tags'), tag_string($exhibit,null,', ',true), 'All Exhibit Tags');
		}
		
		checkbox(array('name'=>'featured', 'id'=>'featured'),$exhibit->featured, null ,'This Exhibit is Featured');
	?>
	</fieldset>
	<fieldset>
		<legend>Exhibit Display Data</legend>
		<?php 
			select(array('name'=>'theme','id'=>'theme'),get_ex_themes(),$exhibit->theme,'Select a Theme');
			text(array('name'=>'slug', 'id'=>'slug'), $exhibit->slug, 'Exhibit Slug (no spaces or special characters)');
		?>
		
	</fieldset>
	
		<table>
		<?php foreach( $exhibit->Sections as $key => $section ): ?>
			<tr>
				<td><?php text(array('name'=>"Sections[$key][order]",'size'=>2), $key); ?>
				<td><a href="<?php echo uri('exhibits/editSection/'.$section->id); ?>">[Edit]</a></td>
				<td><a href="<?php echo uri('exhibits/deleteSection/'.$section->id); ?>">[Delete]</a></td>
				<td><?php echo $section->title; ?></td>
			</tr>
		<?php endforeach; ?>
		</table>
		<?php 
			submit('Save &amp; Finish','save_exhibit');
			submit('Re-order the Exhibit Sections','reorder_sections'); 
			submit('Add a New Section to the Exhibit', 'add_section');
		?>
</form>		

<?php foot(); ?>