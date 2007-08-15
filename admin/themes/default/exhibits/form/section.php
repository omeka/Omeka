<?php head(); ?>
<?php js('listsort'); ?>
<script type="text/javascript" charset="utf-8">
	var listSorter = {};
	
	Event.observe(window, 'load', function() {	
		if(!$('page-list')) return;	
		listSorter.list = $('page-list');
		listSorter.form = $('section-form');
		listSorter.editUri = "<?php echo $_SERVER['REQUEST_URI']; ?>";
		listSorter.partialUri = "<?php echo uri('exhibits/pageList'); ?>";
		listSorter.recordId = '<?php echo h($section->id); ?>';
		listSorter.tag = 'li';
		listSorter.handle = 'handle';
		listSorter.overlap = 'horizontal';
		listSorter.constraint = 'horizontal';
		listSorter.confirmation = 'Are you sure you want to delete this page?';
		listSorter.deleteLinks = listSorter.list.getElementsByClassName('delete-page');
								
		if(listSorter.list) {
			//Create the sortable list
			makeSortable(listSorter.list);
		};
	});
</script>
<?php common('exhibits-nav'); ?>
<div id="primary">

<?php 
	echo flash();
?>

<form method="post" accept-charset="utf-8" action="" id="section-form">
	<fieldset>
		<button type="submit" name="exhibit_form" id="exhibit_form" class="exhibit-button">Exhibit Metadata</button>
		<div id="section_button" class="exhibit-button">Section Metadata</div>
		<button type="submit" name="page_form" id="page_form" class="exhibit-button">Add a Page</button>
				
		<?php 
		//	submit('Exhibit', 'exhibit_form');
		//	submit('New Page', 'page_form'); 
		?>
		
	</fieldset>
	<fieldset id="section-meta">
		<legend>Section Meta</legend>
		
		<input type="submit" name="add_new_section" value="Save &amp; Add Another Section --&gt;" />
		
	<div class="field"><?php text(array('name'=>'title', 'id'=>'title', 'class'=>'textinput'), $section->title, 'Title for the Section'); ?></div>
		<div class="field"><?php text(array('name'=>'slug','id'=>'slug','class'=>'textinput'), $section->slug, 'URL Slug (optional)'); ?></div>
	</fieldset>
	<fieldset id="section-description">
		<legend>Section Description</legend>
	<div class="field"><?php textarea(array('name'=>'description', 'id'=>'description', 'class'=>'textinput', 'rows'=>'10','cols'=>'40'), $section->description, 'Add a description for this section'); ?></div>

	</fieldset>
		<fieldset id="section-pages">
			<legend>Pages in This Section</legend>	
	<?php if ( $section->Pages->count() ): ?>
	
			<ul id="page-list">
			<?php common('_page_list', compact('section'), 'exhibits'); ?>

			</ul>
		
	<?php else: ?>
		<p>There are no pages in this section. <button type="submit" name="page_form" id="page_form" class="inline-button">Add a Page</button>
		</p>
	<?php endif; ?>
	</fieldset>

	
</form>

<?php if ( $section->exists() ): ?>
	<form action="<?php echo uri('exhibits/deleteSection/'.$section->id); ?>">
		<input type="submit" name="submit" value="Delete this Section --&gt;" />
	</form>
<?php endif; ?>
</div>
<?php foot(); ?>