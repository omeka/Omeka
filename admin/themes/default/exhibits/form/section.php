<?php head(); ?>
<?php js('listsort'); ?>
<script type="text/javascript" charset="utf-8">
	var listSorter = {};
	
	Event.observe(window, 'load', function() {		
		listSorter.list = $('page-list');
		listSorter.form = $('section-form');
		listSorter.editUri = "<?php echo $_SERVER['REQUEST_URI']; ?>";
		listSorter.partialUri = "<?php echo uri('exhibits/pageList'); ?>";
		listSorter.recordId = '<?php echo h($section->id); ?>';
		listSorter.tag = 'tr';
		listSorter.handle = 'handle';
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
<h2>Provide title &amp; description for the section</h2>

<?php 
	echo flash();
?>

<form method="post" accept-charset="utf-8" action="" id="section-form">
	<fieldset>
	<div class="field"><?php text(array('name'=>'title', 'id'=>'title'), $section->title, 'Title for the Section'); ?></div>
	<div class="field"><?php textarea(array('name'=>'description', 'id'=>'description', 'rows'=>'10','cols'=>'40'), $section->description, 'Description for the Section'); ?></div>
	<div class="field"><?php text('slug', $section->slug, 'URL Slug (optional)'); ?></div>
	</fieldset>
	
	<?php if ( $section->Pages->count() ): ?>
		<fieldset>
		<table>
			<tr>
				<th>Reorder</th>
				<th>Page Order</th>
				<th>Layout</th>
				<th># of Items</th>
				<th># of Text Fields</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
			<tbody id="page-list">
			<?php common('_page_list', compact('section'), 'exhibits'); ?>
			</tbody>
		</table>
	</fieldset>
	<?php endif; ?>
	
	<fieldset>
		<?php 
			submit('Save & Return to Exhibit Edit Page', 'exhibit_form');
			submit('Save & Add a New Page', 'page_form'); 
		?>
		
	</fieldset>
	
</form>

<?php if ( $section->exists() ): ?>
	<form action="<?php echo uri('exhibits/deleteSection/'.$section->id); ?>">
		<input type="submit" name="submit" value="Delete this Section --&gt;" />
	</form>
<?php endif; ?>
</div>
<?php foot(); ?>