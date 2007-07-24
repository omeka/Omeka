<?php head(); ?>
<?php js('listsort'); ?>
<script type="text/javascript" charset="utf-8">
	var listSorter = {};
	
	Event.observe(window, 'load', function() {		
		listSorter.list = $('page-list');
		listSorter.form = $('section-form');
		listSorter.editUri = "<?php echo $_SERVER['REQUEST_URI']; ?>";
		listSorter.partialUri = "<?php echo uri('exhibits/ajaxPageList'); ?>";
		listSorter.recordId = '<?php echo $section->id; ?>';
		listSorter.tag = 'tr';
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
				<th>Page Order</th>
				<th>Layout</th>
				<th># of Items</th>
				<th># of Text Fields</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
			<tbody id="page-list">
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