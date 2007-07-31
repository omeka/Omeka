<?php head(); ?>
<?php js('listsort'); ?>

<script type="text/javascript" charset="utf-8">	

	Event.observe(window,'load',function() {
		$('add_section').onclick = function() {
			new Ajax.Request("<?php echo uri('exhibits/_new_section'); ?>", {
				onComplete: function(t) {
					new Insertion.Bottom($('new-section'), t.responseText);
				}
			});
		}
		
		
		
	});
	var listSorter = {};
	
	Event.observe(window, 'load', function() {	

		listSorter.list = $('section-list');
		listSorter.form = $('exhibit-form');
		listSorter.editUri = "<?php echo $_SERVER['REQUEST_URI']; ?>";
		listSorter.partialUri = "<?php echo uri('exhibits/ajaxSectionList'); ?>";
		listSorter.recordId = '<?php echo $exhibit->id; ?>';
		listSorter.tag = 'li';
		listSorter.handle = 'handle';
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
	<fieldset id="exhibit-metadata">
		<legend>Exhibit Metadata</legend>
		<?php echo flash();?>
	<div class="field"><?php text(array('name'=>'title', 'class'=>'textinput', 'id'=>'title'), $exhibit->title, 'Exhibit Title'); ?></div>
	<div class="field"><?php text(array('name'=>'slug', 'id'=>'slug', 'class'=>'textinput'), $exhibit->slug, 'Exhibit Slug (no spaces or special characters)'); ?>
	</div>
	<div class="field"><?php text(array('name'=>'credits', 'id'=>'credits', 'class'=>'textinput'), $exhibit->credits,'Exhibit Credits'); ?></div>
	<div class="field"><?php textarea(array('name'=>'description', 'id'=>'description', 'class'=>'textinput','rows'=>'10','cols'=>'40'), $exhibit->description, 'Exhibit Description'); ?></div>	
	<div class="field"><?php text(array('name'=>'tags', 'id'=>'tags', 'class'=>'textinput'), tag_string($exhibit,null,', ',true), 'Exhibit Tags'); ?></div>
	<div class="field">
		<label for="featured">Exhibit is featured:</label>
		<div class="radio"><?php radio(array('name'=>'featured', 'id'=>'featured'), array('0'=>'No','1'=>'Yes'), $exhibit->featured); ?></div>
	</div>
	</fieldset>
	<fieldset id="exhibit-display">
		<legend>Exhibit Display Data</legend>
		<div class="field">
			<label for="theme">Exhibit Theme</label>
			<div class="select"><?php select(array('name'=>'theme','id'=>'theme'),get_ex_themes(),$exhibit->theme); ?>
			</div>
		</div>
		<div id="section-list-container">
			<h2>Exhibit Sections</h2>
			<ol id="section-list">
		<?php foreach( $exhibit->Sections as $key => $section ): ?>
			<li id="section_<?php echo $key; ?>">
				<span class="left">
				<span class="handle"><img src="<?php echo img('icons/arrow_move.png'); ?>" alt="Drag" /></span>
				<span class="input"><?php text(array('name'=>"Sections[$key][order]",'size'=>2,'class'=>'order-input'), $key); ?></span>
		
				<span class="section-title"><?php echo $section->title; ?></span>
				</span>
				<span class="right">
				<span class="section-edit"><a href="<?php echo uri('exhibits/editSection/'.$section->id); ?>"><img src="<?php echo img('icons/page_white_edit.png'); ?>" alt="Edit" /> Edit</a></span>
				<span class="section-delete"><a href="<?php echo uri('exhibits/deleteSection/'.$section->id); ?>"><img src="<?php echo img('icons/delete.png'); ?>" alt="Drag" /> Delete</a></span>
				</span>
			</li>
		<?php endforeach; ?>
			</ol>
			<div id="add_section">Add section</div>
			<div id="new-section"></div>
			<?php //submit('Add a New Section', 'add_section'); ?>
		</fieldset>
		<?php 
			submit('Save &amp; Finish','save_exhibit');
			submit('Re-order the Exhibit Sections','reorder_sections'); 
		?>
</form>		
</div>
<?php foot(); ?>