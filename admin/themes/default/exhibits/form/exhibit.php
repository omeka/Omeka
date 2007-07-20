<?php head(); ?>
<?php js('listsort'); ?>

<script type="text/javascript" charset="utf-8">	
	var listSorter = {};
	
	Event.observe(window, 'load', function() {		
		listSorter.list = $('section-list');
		listSorter.form = $('exhibit-form');
		listSorter.editUri = "<?php echo $_SERVER['REQUEST_URI']; ?>";
		listSorter.partialUri = "<?php echo uri('exhibits/ajaxSectionList'); ?>";
		listSorter.recordId = '<?php echo $exhibit->id; ?>';
		listSorter.tag = 'tr';
		listSorter.confirmation = 'Are you sure you want to delete this section?';
		listSorter.deleteLinks = listSorter.list.getElementsByClassName('delete-section');
								
		if(listSorter.list) {
			//Create the sortable list
			makeSortable(listSorter.list);
		
/*
				var submitInputs = $$('input[type="submit"]');
		
			//Activate the sortable form elements when the form gets submitted
			for (var i=0; i < submitInputs.length; i++) {
				submitInputs[i].onclick = function(event) {
					enableListForm(true);
				};
			};		
*/	
		}
		
/*
			var checkForm = Builder.node('input', {type:'button' ,name:'checkForm', value:'Check the form'});
		checkForm.onclick = function() {
			var serialized = listSorter.form.serialize();
			alert(serialized);
		}
		listSorter.form.appendChild(checkForm);
*/	
	});
</script>
<?php common('exhibits-nav'); ?>
<div id="primary">
<form id="exhibit-form" method="post">
	<fieldset>
		<legend>Exhibit Metadata</legend>
		<?php echo flash();?>
	<div class="field"><?php text(array('name'=>'title', 'class'=>'textinput', 'id'=>'title'), $exhibit->title, 'Exhibit Title'); ?></div>
		<div class="field"><?php textarea(array('name'=>'description', 'id'=>'description', 'class'=>'textinput','rows'=>'10','cols'=>'40'), $exhibit->description, 'Exhibit Description'); ?></div>
		<div class="field"><?php textarea(array('name'=>'credits', 'id'=>'credits', 'class'=>'textinput', 'rows'=>'10','cols'=>'40'), $exhibit->credits,'Exhibit Credits'); ?></div>
		
		

		
		<div class="field"><?php text(array('name'=>'tags', 'id'=>'tags', 'class'=>'textinput'), tag_string($exhibit,null,', ',true), 'Exhibit Tags'); ?></div>
		
	<div class="field">
		<div class="label">Exhibit is featured:</div> 
		<div class="radio"><?php radio(array('name'=>'featured', 'id'=>'featured'), array('0'=>'No','1'=>'Yes'), $exhibit->featured); ?></div>
	</div>
	</fieldset>
	<fieldset>
		<legend>Exhibit Display Data</legend>
		<div class="field"><?php select(array('name'=>'theme','id'=>'theme'),get_ex_themes(),$exhibit->theme,'Select a Theme'); ?></div>
			<div class="field"><?php text(array('name'=>'slug', 'id'=>'slug', 'class'=>'textinput'), $exhibit->slug, 'Exhibit Slug (no spaces or special characters)'); ?></div>
		
	</fieldset>
	
		<table>
			<tbody id="section-list">
		<?php foreach( $exhibit->Sections as $key => $section ): ?>
			<tr id="section_<?php echo $key; ?>">
				<td><?php text(array('name'=>"Sections[$key][order]",'size'=>2), $key); ?></td>
				<td><a href="<?php echo uri('exhibits/editSection/'.$section->id); ?>" class="edit-section">[Edit]</a></td>
				<td><a href="<?php echo uri('exhibits/deleteSection/'.$section->id); ?>"  class="delete-section">[Delete]</a></td>
				<td><?php echo $section->title; ?></td>
			</tr>
		<?php endforeach; ?>
			</tbody>
		</table>
		<?php 
			submit('Save &amp; Finish','save_exhibit');
			submit('Re-order the Exhibit Sections','reorder_sections'); 
			submit('Add a New Section to the Exhibit', 'add_section');
		?>
</form>		
</div>
<?php foot(); ?>