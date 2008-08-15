<div class="field" id="add-more-files">
<label for="add_num_files">Add Files</label>
	<div class="files">
	<?php $numFiles = $_REQUEST['add_num_files'] or $numFiles = 1; ?>
	<?php 
	echo text(array('name'=>'add_num_files','size'=>2),$numFiles);
	echo submit('Add this many files', 'add_more_files'); 
	?>
	</div>
</div>

<div class="field" id="file-inputs">
<!-- MAX_FILE_SIZE must precede the file input field -->
	<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
	<label>Find a File</label>
		
	<?php for($i=0;$i<$numFiles;$i++): ?>
	<div class="files">
		<input name="file[<?php echo $i; ?>]" id="file-<?php echo $i; ?>" type="file" class="fileinput" />			
	</div>
	<?php endfor; ?>
</div>

<?php fire_plugin_hook('append_to_item_form_upload', $item); ?>

<?php if ( item_has_files() ): ?>
	<div class="label">Edit File Metadata</div>
	<div id="file-list">
	<table>
		<thead>
			<tr>
				<th>File Name</th>
				<th>Delete?</th>
			</tr>
		</thead>
		<tbody>
	<?php foreach( $item->Files as $key => $file ): ?>
		<tr>
			<td class="file-link">
				<a class="edit" href="<?php echo uri('files/edit/'.$file->id); ?>">
						<?php echo h($file->original_filename); ?>
				</a>
			</td>
			<td class="delete-link">
				<?php echo checkbox(array('name'=>'delete_files[]'),false,$file->id); ?>
			</td>	
		</tr>

	<?php endforeach; ?>
	</tbody>
	</table>
	</div>
<?php endif; ?>