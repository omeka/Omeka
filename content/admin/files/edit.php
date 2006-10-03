<?php
//Layout: default;
$file = $__c->files()->edit();

// Get Item this file belongs to
$item = $__c->items()->findById($file->item_id);
?>
<?php include('subnav.php'); ?>

<h2>Edit File</h2>
		
<form method="post" id="editfile" action="<?php echo $_link->to('files', 'edit'); ?>" name="editFile">
	<fieldset>
<legend>Core Metadata</legend>	


<label for="File[file_id]">Identifier</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_id]',
							'readonly' => 'readonly',
							'class' => 'readonly textinput',
							'value'	=> $file->file_id ) );
?>



<label for="File[file_title]">Title</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_title]', 'class' => 'textinput',
							'value'	=> $file->file_title ) );
?>


<label for="File[file_creator]">Creator</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_creator]', 'class' => 'textinput',
							'value'	=> $file->file_creator ) );
?>
<label for="File[file_subject]">Subject</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_subject]', 'class' => 'textinput',
							'value'	=> $file->file_subject ) );
?>
<label for="File[file_description]">Description</label>
<?php
	$_form->textarea( array(	'name'	=> 'File[file_description]','class' => 'textinput' ),
						$file->file_description );
?>


<label for="File[file_publisher]">Publisher</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_publisher]', 'class' => 'textinput', 'value' => $file->file_publisher ) );
?>

<label for="File[file_creator]">Other Creator</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_creator_other]', 'class' => 'textinput',
							'value'	=> $file->file_creato_other ) );
?>


<label for="File[file_date]">Date</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_date]',
							'class' => 'textinput',
							'id'	=> 'File[file_date]',
							'value'	=> $file->file_date ));
							
	// Fields to break out dates
	/*	$_form->text( array(	'name'	=> 'File[file_date_month]',
								'id'	=> 'File[file_date_month]',
								'size'		=> 2,
								'maxlength' => 2,
								'value'	=> $file->file_date_month ));
	?>
	<?php
		$_form->text( array(	'name'	=> 'File[file_date_day]',
								'id'	=> 'File[file_date_day]',
								'size'		=> 2,
								'maxlength' => 2,
								'value'	=> $file->file_date_day ));
	?>
	<?php
		$_form->text( array(	'name'	=> 'File[file_date_year]',
								'id'	=> 'File[file_date_year]',
								'size'		=> 4,
								'maxlength' => 4,
								'value'	=> $file->file_date_year ));	*/									
?>


<label for="File[file_source]">Source</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_source]', 'class' => 'textinput', 'value' => $file->file_source ) );
?>


<label for="File[file_language]">Language</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_language]',
							'class' => 'textinput',
							'value'	=> $file->file_language ) );
?>

<label for="File[file_relation]">Relation</label>
<?php
	$_form->textarea( array(	'name'	=> 'File[file_relation]' ),
						$file->file_relation );
?>

<label for="File[file_coverage]">Coverage</label>
<?php
	$_form->text( array(	'name' => 'File[file_coverage]','class' => 'textinput',
							'value' => $file->file_coverage));
?>
<label for="File[file_rights]">Rights</label>
<?php
	$_form->textarea( array(	'name'	=> 'File[file_rights]' ),
						$file->file_rights );
?>
</fieldset>
<fieldset>
<legend>Format Metadata</legend>	


<label for="File[file_transcriber]">Transcriber</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_transcriber]', 'class' => 'textinput',
				'value' =>	$file->file_transcriber ));
?>

<label for="File[file_producer]">Producer</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_producer]',
	'class' => 'textinput',
					'value' =>	$file->file_producer ) );
?>

<label for="File[file_render_device]">Render Device</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_render_device]',
					'class' => 'textinput',
					'value' =>	$file->file_render_device ) );
?>

<label for="File[file_render_details]">Render Details (e.g. Duration, resolution, bit depth, width, height)</label>
<?php
	$_form->textarea( array(	'name'	=> 'File[file_render_details]',
						'class' => 'textinput' ),
						$file->file_render_details );
?>

<label for="File[file_capture_date]">Capture Date</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_capture_date]',
							'id'	=> 'File[file_capture_date]', 'class' => 'textinput',
							'value'	=> $file->file_capture_date ) );
?>

<label for="File[file_capture_device]">Capture Device</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_capture_device]' ,'class' => 'textinput',
					'value' => $file->file_capture_device ));
?>

<label for="File[file_capture_details]">Capture Details (e.g. Duration, resolution, bit depth, width, height)</label>
<?php
	$_form->textarea( array(	'name'	=> 'File[file_capture_details]','class' => 'textinput' ),
					$file->file_capture_details );
?>

<label for="File[file_watermark]">Watermark</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_watermark]', 'class' => 'textinput',
					'value' => $file->file_watermark ));
?>

<label for="File[file_encryption]">Encryption</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_encryption]', 'class' => 'textinput',
							'value' => $file->file_encryption ));
?>

<label for="File[file_compression]">Compression</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_compression]', 'class' => 'textinput',
							'value' => $file->file_compression ));
?>



<label for="File[file_post_processing]">Post Processing</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_post_processing]', 'class' => 'textinput',
							'value' => $file->file_post_processing ));
?>

<label for="File[file_change_history]">Change History</label>
<?php
	$_form->textarea( array(	'name'	=> 'File[file_change_history]', 'class' => 'textinput',
						$file->file_change_history ) );
?>

<label class="readonly" for="File[file_archive_filename]">Archive Filename <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_archive_filename]',
							'readonly' => 'readonly',
							'class' => 'readonly textinput',
							'value' => $file->file_archive_filename ) );
?>



<label class="readonly" for="File[file_original_filename]">Original File Name <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_original_filename]',
							'readonly' => 'readonly',
							'class' => 'readonly textinput',
							'value' => $file->file_original_filename ));
?>

<label class="readonly" for="File[file_thumbnail_name]">Display File Name <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_fullsize_filename]',
							'readonly' => 'readonly',
							'class' => 'readonly textinput',
							'value' => $file->file_fullsize_filename ));
?>

<label class="readonly" for="File[file_thumbnail_name]">Thumbnail File Name <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_thumbnail_name]',
							'readonly' => 'readonly',
							'class' => 'readonly textinput',
							'value' => $file->file_thumbnail_name ));
?>

<label class="readonly" for="File[file_size]">File Size <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_size]',
							'readonly' => 'readonly',
							'class' => 'readonly textinput',
							'value' => $file->file_size ));
?>
</fieldset>
<fieldset>
	<legend>Type Metadata</legend>
<label class="readonly" for="File[file_mime_browser]">File Mime Browser <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_mime_browser]',
							'readonly' => 'readonly',
							'class' => 'readonly textinput',
							'value' => $file->file_mime_browser ) );
?>

<label class="readonly" for="File[file_mime_php]">File Mime PHP <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_mime_php]',
							'readonly' => 'readonly',
							'class' => 'readonly textinput',
							'value' => $file->file_mime_php ));
?>



<label class="readonly" for="File[file_mime_os]">File Mime OS <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_mime_os]',
							'readonly' => 'readonly',
							'class' => 'readonly textinput',
							'value' =>	$file->file_mime_os ));
?>

<label class="readonly" for="File[file_type_os]">File Type OS <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_type_os]',
							'readonly' => 'readonly',
							'class' => 'readonly textinput',
							'value' => $file->file_type_os ) );
?>
</fieldset>
<fieldset>
	<legend>File History</legend>
	<label class="readonly" for="File[file_added]">File Added <span class="readonly">(read only)</span></label>
	<?php
		$_form->text( array(	'name'	=> 'File[file_added]',
								'readonly' => 'readonly',
								'class' => 'readonly textinput',
								'value' => $file->file_added ) );
	?>
<label class="readonly" for="File[file_modified]">File Modified <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_modified]',
							'readonly' => 'readonly',
							'class' => 'readonly textinput',
							'value' =>	$file->file_modified ) );
?>


<label for="File[file_authentication]">Authentication <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_authentication]',
							'readonly' => 'readonly',
							'class' => 'readonly textinput',
							'value' => $file->file_authentication ));
?>
</fieldset>
<fieldset>
<input type="hidden" name="File[file_id]" id="File[file_id]" value="<?php echo $file->file_id; ?>">
<input type="submit" name="file_edit" value="Edit file &gt;&gt;" id="file_edit" />
</fieldset>
</form>
