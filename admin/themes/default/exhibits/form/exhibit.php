<?php head(); ?>
<?php js('listsort'); ?>

<script type="text/javascript" charset="utf-8">	
//<![CDATA[

	Event.observe(window,'load',function() {
		$('add_new_section').onclick = function() {
			var exhibit_id = getExhibitId();
			
						
			//If we don't have a valid exhibit ID, we need to save the exhibit first
			if(isNaN(exhibit_id)) {
				
				saveNewExhibit();
				
			}
			else {
				loadSectionForm(exhibit_id);
			}			
			return false;
		}		
	});
	
	function loadSectionForm(exhibit_id)
	{
		//Now submit the request for the 
		new Ajax.Updater('new-section', "<?php echo uri('exhibits/sectionForm'); ?>", {
			parameters: "id=" + exhibit_id,
			onFailure: function(t) {
				Omeka.flash(t.responseText);
			},
			onSuccess: function(t) {
				//Highlight the updated DIV
				new Effect.Highlight('new-section');					
			},
			onComplete: function(t) {
				//Now make the add/cancel links work
				var addLink = $('add_section');
				var cancelLink = $('cancel-add');
				addLink.onclick = addSection;
				cancelLink.onclick = removeAddSectionForm;
			}
		});		
	}
	
	function addSection()
	{
		//Serialize all the form inputs (also specify JSON output)
		var inputs = $$('#new-section input, #new-section textarea');
		var params = Form.serializeElements(inputs) + "&output=json";
		
		//Generate the URI for the 
		var addSectionUri = "<?php echo generate_url(array('controller'=>'exhibits','action'=>'addSection'), 'default'); ?>";
		
		var addSectionUri = addSectionUri + "/" + getExhibitId();
		
		var sectionListUri = "<?php echo generate_url(array('controller'=>'exhibits','action'=>'sectionList'), 'default'); ?>";
		
		//Send an AJAX request that saves the Section, then send another that updates the section list
		new Ajax.Request(addSectionUri, {
			parameters: params,
			onSuccess: function(t) {
				//When successful, update the section list
				new Ajax.Updater('section-list', sectionListUri, {
					parameters: {id: getExhibitId()},
					onSuccess: function(t) {
						//flash a happy message and get rid of the form
						Omeka.flash('Section has been saved successfully!');
						removeAddSectionForm();
					},
					onFailure: function(t) {
						alert(t.responseText);
					},
					onComplete: function(t) {
						//Make the section list draggable
						makeSectionListDraggable();
						//Highlight the section list
						new Effect.Highlight($('section-list').parent);
					}
				});
			},
			//When adding a section does not work
			onFailure: function(t, section) {
				Omeka.flash(section['Flash']);
			}
		})
	}

	
	function removeAddSectionForm()
	{
		$('new-section').update();
	}
	
	//This is a bit of a hack.  The exhibit ID is a hidden value on the form
	function getExhibitId()
	{
		var id = $('exhibit_id').value;
		return parseInt(id);
	}
	
	function setExhibitId(val)
	{
		$('exhibit_id').value = val;
	}
	
	//Save the exhibit and return the unique identifier of the new exhibit
	function saveNewExhibit()
	{
		//To save the exhibit we just post back to the current URI
		var uri = "<?php echo uri('exhibits/save'); ?>";
		
		var exhibit_id = null;
				
		new Ajax.Request(uri, {
			parameters: $('exhibit-form').serialize() + "&output=json",
			method:'post',

			onSuccess: function(t, exhibit) {

				Omeka.flash('Exhibit was saved successfully');
				setExhibitId(exhibit['id']);
				
				//After a successful save, update the exhibit slug b/c that is most likely to be auto-generated
				$('slug').value = exhibit['slug'];
				
				exhibit_id = exhibit['id'];
				
				loadSectionForm(exhibit_id);
			},

			onFailure: function(t, exhibit) {
				Omeka.flash(exhibit['Flash']);
			}
		});			
	}
	
	var listSorter = {};
	
	function makeSectionListDraggable()
	{
		var list = $('section-list');
		var exhibit_id = getExhibitId();
		listSorter.list = list;
		listSorter.form = $('exhibit-form');
		listSorter.editUri = "<?php echo generate_url(array('controller'=>'exhibits','action'=>'edit'),'default'); ?>/" + exhibit_id;
		listSorter.partialUri = "<?php echo uri('exhibits/sectionList'); ?>?id="+exhibit_id;
		listSorter.recordId = exhibit_id;
		listSorter.tag = 'li';
		listSorter.handle = 'handle';
		listSorter.confirmation = 'Are you sure you want to delete this section?';
		listSorter.deleteLinks = $$('.section-delete a');
		listSorter.callback = styleExhibitBuilder;		
									
		if(listSorter.list) {
			//Create the sortable list
			makeSortable(listSorter.list);
		}
		
		styleExhibitBuilder();		
	}
	
	Event.observe(window, 'load', function() {	
		makeSectionListDraggable();
	});
//]]>	
</script>
<?php common('exhibits-nav'); ?>
<div id="primary">
	<h1>Edit Exhibit: <?php echo h($exhibit->title); ?></h1>

<form id="exhibit-form" method="post">
	<fieldset id="save-exhibit">
		
			<button type="submit" name="save_exhibit" id="save_exhibit" class="exhibit-button" disabled="disabled">Exhibit Metadata</button>
			<button type="submit" name="add_new_section" id="add_new_section" class="exhibit-button">Add a Section</button>
			<div id="pages_button" class="exhibit-button">Pages</div>
			
		
	<?php 
		//submit('Save &amp; Finish','save_exhibit');
		//submit('Add a New Section', 'add_section');
		
		//submit('Re-order the Exhibit Sections','reorder_sections'); 
	?>
	
	</fieldset>
	<div id="exhibit-metadata">
	<fieldset>
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
	
	<div class="field">
		<label for="featured">Exhibit is public:</label>
		<div class="radio"><?php radio(array('name'=>'public', 'id'=>'public'), array('0'=>'No','1'=>'Yes'), $exhibit->public); ?></div>
	</div>
	
	</fieldset>
	</div>
	<div id="exhibit-display">
	<fieldset>
		<legend>Exhibit Display Data</legend>
		<div class="field">
			<label for="theme">Exhibit Theme</label>
			<div class="select"><?php select(array('name'=>'theme','id'=>'theme'),get_ex_themes(),$exhibit->theme); ?>
			</div>
		</div>
		<div id="section-list-container">
			<h2>Exhibit Sections</h2>
			<ol id="section-list">
				<?php common('_section_list', compact('exhibit'), 'exhibits'); ?>
			</ol>
			<div id="new-section"></div>
			<input type="hidden" name="exhibit_id" id="exhibit_id" value="<?php echo h($exhibit->id); ?>" />
		
		</div>
		</fieldset>
	</div>	
	<fieldset id="exhibit-save">
		<?php submit('Save &amp; Finish','save_exhibit'); ?>
		</fieldset>
</form>		
</div>
<?php foot(); ?>