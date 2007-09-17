<?php head(); ?>
<?php common('archive-nav'); ?>

<div id="primary">
<h1>Edit File #<?php echo h($file->id); ?></h1>
<p><a class="delete" href="<?php echo uri('files/delete/'.$file->id); ?>">Delete</a></p>
<?php if($file->hasThumbnail()): ?>
<div id="image"><?php thumbnail($file); ?><p>Thumbnail of File #<?php echo h($file->id); ?></div>
<?php endif; ?>
	
<form method="post" id="editfile" action="<?php echo uri('files/edit/'.$file->id); ?>" name="editFile">

<fieldset>

<legend>Core Metadata</legend>	

<div><label for="id">Identifier</label>
<?php
	text( array(	'name'	=> 'id',
							'readonly' => 'readonly',
							'class' => 'readonly textinput'),$file->id );
?>
</div>

<div><label for="title">Title</label>
<?php
	text( array(	'name'	=> 'title', 'class' => 'textinput'),$file->title );
?>
</div>

<div><label for="creator">Creator</label>
<?php
	text( array(	'name'	=> 'creator', 'class' => 'textinput'),$file->creator );
?>
</div>

<div><label for="subject">Subject</label>
<?php
	text( array(	'name'	=> 'subject', 'class' => 'textinput'),$file->subject );
?>
</div>

<div><label for="description">Description</label>
<?php
	textarea( array(	'name'	=> 'description','class' => 'textinput' ),
						$file->description );
?>
</div>

<div><label for="publisher">Publisher</label>
<?php
	text( array(	'name'	=> 'publisher', 'class' => 'textinput'),$file->publisher );
?>
</div>

<div><label for="creator">Other Creator</label>
<?php
	text( array(	'name'	=> 'additional_creator', 'class' => 'textinput'),$file->additional_creator );
?>
</div>

<div><label for="date">Date</label>
<?php
	text( array(	'name'	=> 'date',
							'class' => 'textinput',
							'id'	=> 'date'),$file->date);
							
	// Fields to break out dates
	/*	text( array(	'name'	=> 'date_month',
								'id'	=> 'date_month',
								'size'		=> 2,
								'maxlength' => 2),$file->date_month);
	?>
</div>
	<?php
		text( array(	'name'	=> 'date_day',
								'id'	=> 'date_day',
								'size'		=> 2,
								'maxlength' => 2),$file->date_day);
	?>
</div>
	<?php
		text( array(	'name'	=> 'date_year',
								'id'	=> 'date_year',
								'size'		=> 4,
								'maxlength' => 4),$file->date_year);	*/									
?>
</div>

<div><label for="source">Source</label>
<?php
	text( array(	'name'	=> 'source', 'class' => 'textinput'),$file->source );
?>
</div>

<div><label for="language">Language</label>
<?php
	text( array(	'name'	=> 'language',
							'class' => 'textinput', 'class' => 'textinput'),$file->language );
?>
</div>

<div><label for="relation">Relation</label>
<?php
	textarea( array(	'name'	=> 'relation', 'class' => 'textinput' ),
						$file->relation );
?>
</div>

<div><label for="coverage">Coverage</label>
<?php
	text( array(	'name' => 'coverage','class' => 'textinput'),$file->coverage);
?>
</div>
<div><label for="rights">Rights</label>
<?php
	textarea( array(	'name'	=> 'rights', 'class' => 'textinput' ),
						$file->rights );
?>
</div>
<div><label for="format">Format</label>
<?php
	textarea( array(	'name'	=> 'format' , 'class' => 'textinput'),
						$file->format );
?>
</div>
</fieldset>

<fieldset>
<legend>Format Metadata</legend>	

<div><label for="transcriber">Transcriber</label>
<?php
	text( array(	'name'	=> 'transcriber', 'class' => 'textinput'),$file->transcriber);
?>
</div>

<div><label for="producer">Producer</label>
<?php
	text( array(	'name'	=> 'producer',
	'class' => 'textinput'),$file->producer );
?>
</div>

<div><label for="render_device">Render Device</label>
<?php
	text( array(	'name'	=> 'render_device',
					'class' => 'textinput'),$file->render_device );
?>
</div>

<div><label for="render_details">Render Details (e.g. Duration, resolution, bit depth, width, height)</label>
<?php
	textarea( array(	'name'	=> 'render_details',
						'class' => 'textinput' ),
						$file->render_details );
?>
</div>

<div><label for="capture_date">Capture Date</label>
<?php
	text( array(	'name'	=> 'capture_date',
							'id'	=> 'capture_date', 'class' => 'textinput'),$file->capture_date );
?>
</div>

<div><label for="capture_device">Capture Device</label>
<?php
	text( array(	'name'	=> 'capture_device' ,'class' => 'textinput'),$file->capture_device);
?>
</div>

<div><label for="capture_details">Capture Details (e.g. Duration, resolution, bit depth, width, height)</label>
<?php
	textarea( array(	'name'	=> 'capture_details','class' => 'textinput' ),
					$file->capture_details );
?>
</div>

<div><label for="watermark">Watermark</label>
<?php
	text( array(	'name'	=> 'watermark', 'class' => 'textinput'),$file->watermark);
?>
</div>

<div><label for="encryption">Encryption</label>
<?php
	text( array(	'name'	=> 'encryption', 'class' => 'textinput'),$file->encryption);
?>
</div>

<div><label for="compression">Compression</label>
<?php
	text( array(	'name'	=> 'compression', 'class' => 'textinput'),$file->compression);
?>
</div>

<div><label for="post_processing">Post Processing</label>
<?php
	text( array(	'name'	=> 'post_processing', 'class' => 'textinput'),$file->post_processing);
?>
</div>

<div><label for="change_history">Change History</label>
<?php
	textarea( array(	'name'	=> 'change_history', 'class' => 'textinput' ),
						$file->change_history );
?>
</div>

<div><label class="readonly" for="archive_filename">Archive Filename <span class="readonly">(read only)</span></label>
<?php
	text( array(	'name'	=> 'archive_filename',
							'readonly' => 'readonly',
							'class' => 'readonly textinput'),$file->archive_filename );
?>
</div>

<div><label class="readonly" for="original_filename">Original File Name <span class="readonly">(read only)</span></label>
<?php
	text( array(	'name'	=> 'original_filename',
							'readonly' => 'readonly',
							'class' => 'readonly textinput'),$file->original_filename);
?>
</div>

<div><label class="readonly" for="size">File Size <span class="readonly">(read only)</span></label>
<?php
	text( array(	'name'	=> 'size',
							'readonly' => 'readonly',
							'class' => 'readonly textinput'),$file->size);
?>
</div>
</fieldset>

<fieldset>
	<legend>Type Metadata</legend>
<div><label class="readonly" for="mime_browser">File Mime Browser <span class="readonly">(read only)</span></label>
<?php
	text( array(	'name'	=> 'mime_browser',
							'readonly' => 'readonly',
							'class' => 'readonly textinput'),$file->mime_browser );
?>
</div>

<div><label class="readonly" for="mime_os">File Mime OS <span class="readonly">(read only)</span></label>
<?php
	text( array(	'name'	=> 'mime_os',
							'readonly' => 'readonly',
							'class' => 'readonly textinput'),$file->mime_os);
?>
</div>

<div><label class="readonly" for="type_os">File Type OS <span class="readonly">(read only)</span></label>
<?php
	text( array(	'name'	=> 'type_os',
							'readonly' => 'readonly',
							'class' => 'readonly textinput'),$file->type_os );
?>
</div>
</fieldset>

<fieldset>
	<legend>File History</legend>
	<div><div><label class="readonly" for="added">File Added <span class="readonly">(read only)</span></label>
	<?php
		text( array(	'name'	=> 'added',
								'readonly' => 'readonly',
								'class' => 'readonly textinput'),$file->added );
	?>
</div>
<div><label class="readonly" for="modified">File Modified <span class="readonly">(read only)</span></label>
<?php
	text( array(	'name'	=> 'modified',
							'readonly' => 'readonly',
							'class' => 'readonly textinput'),$file->modified );
?>
</div>

<div><label for="authentication">Authentication <span class="readonly">(read only)</span></label>
<?php
	text( array(	'name'	=> 'authentication',
							'readonly' => 'readonly',
							'class' => 'readonly textinput'),$file->authentication);
?>
</div>
</fieldset>

<fieldset>
<input type="submit" name="submit" value="Save Changes" id="file_edit" />
</fieldset>

</form>

</div>
<?php foot(); ?>