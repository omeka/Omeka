<?php head(); ?>
<h2>Provide title &amp; description for the section</h2>

<?php 
	echo flash();
?>

<form method="post" accept-charset="utf-8" action="">
	<fieldset>
	<div class="field"><?php text(array('name'=>'title', 'id'=>'title'), $section->title, 'Title for the Section'); ?></div>
	<div class="field"><?php textarea(array('name'=>'description', 'id'=>'description', 'rows'=>'10','cols'=>'40'), $section->description, 'Description for the Section'); ?></div>
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
		<?php foreach( $section->Pages as $key => $page ): ?>
		<tr>
			<td><?php echo $page->order; ?></td>
			<td><?php exhibit_layout($page->layout); ?></td>
			<td><?php echo $page->getItemCount(); ?></td>
			<td><?php echo $page->getTextCount(); ?></td>
			<td><a href="<?php echo uri('exhibits/editPage/'.$page->id); ?>">[Edit]</a></td>
			<td><a href="<?php echo uri('exhibits/deletePage/'.$page->id); ?>">[Delete]</a></td>
		</tr>
		<?php endforeach; ?>
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

<?php foot(); ?>