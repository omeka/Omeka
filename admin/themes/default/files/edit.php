<?php head(array('body_class'=>'items primary')); ?>
<h1>Edit File #<?php echo h($file->id); ?></h1>


<div id="primary">
<?php if (has_permission('Files', 'delete')): ?>
<p><?php echo link_to($file, 'delete', 'Delete', array('class'=>'delete')); ?></p>    
<?php endif; ?>

<?php if($file->hasThumbnail()): ?>
<div id="image"><?php echo thumbnail($file); ?><p>Thumbnail of File #<?php echo h($file->id); ?></div>
<?php endif; ?>
	
<form method="post" id="editfile" action="<?php echo uri('files/edit/'.$file->id); ?>" name="editFile">

<fieldset>

<legend>Core Metadata</legend>	

<div class="field">
	<label for="id">Identifier</label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'id',
							'readonly' => 'readonly',
							'class' => 'readonly textinput'),$file->id );
?></div>
</div>

<div class="field">
	<label for="title">Title</label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'title', 'class' => 'textinput'),$file->title );
?></div>
</div>

<div class="field">
	<label for="creator">Creator</label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'creator', 'class' => 'textinput'),$file->creator );
?></div>
</div>

<div class="field">
	<label for="subject">Subject</label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'subject', 'class' => 'textinput'),$file->subject );
?></div>
</div>

<div class="field">
	<label for="description">Description</label>
<div class="inputs"><?php
	echo textarea( array('name'	=> 'description','class' => 'textinput','cols' => '40', 'rows' => '20' ),
						$file->description );
?></div>
</div>

<div class="field">
	<label for="publisher">Publisher</label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'publisher', 'class' => 'textinput'),$file->publisher );
?></div>
</div>

<div class="field">
	<label for="creator">Other Creator</label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'additional_creator', 'class' => 'textinput'),$file->additional_creator );
?></div>
</div>

<div class="field">
	<label for="date">Date</label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'date',
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
?></div>
</div>

<div class="field">
	<label for="source">Source</label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'source', 'class' => 'textinput'),$file->source );
?></div>
</div>

<div class="field">
	<label for="language">Language</label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'language',
							'class' => 'textinput', 'class' => 'textinput'),$file->language );
?></div>
</div>

<div class="field">
	<label for="relation">Relation</label>
<div class="inputs"><?php
	echo textarea( array(	'rows'=>'20','cols'=>'40','name'	=> 'relation', 'class' => 'textinput' ),
						$file->relation );
?></div>
</div>

<div class="field">
	<label for="coverage">Coverage</label>
<div class="inputs"><?php
	echo text( array(	'name' => 'coverage','class' => 'textinput'),$file->coverage);
?></div>
</div>
<div class="field">
	<label for="rights">Rights</label>
<div class="inputs"><?php
	echo textarea( array(	'rows'=>'20','cols'=>'40','name'	=> 'rights', 'class' => 'textinput' ),
						$file->rights );
?></div>
</div>
<div class="field">
	<label for="format">Format</label>
<div class="inputs"><?php
	echo textarea( array(	'rows'=>'20','cols'=>'40','name'	=> 'format' , 'class' => 'textinput'),
						$file->format );
?></div>
</div>
</fieldset>

<fieldset>
<legend>Format Metadata</legend>	

<div class="field"><label for="transcriber">Transcriber</label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'transcriber', 'class' => 'textinput'),$file->transcriber);
?></div>
</div>

<div class="field"><label for="producer">Producer</label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'producer',
	'class' => 'textinput'),$file->producer );
?></div>
</div>

<div class="field"><label for="render_device">Render Device</label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'render_device',
					'class' => 'textinput'),$file->render_device );
?></div>
</div>

<div class="field"><label for="render_details">Render Details (e.g. Duration, resolution, bit depth, width, height)</label>
<div class="inputs"><?php
	echo textarea( array(	'rows'=>'20','cols'=>'40','name'	=> 'render_details',
						'class' => 'textinput' ),
						$file->render_details );
?></div>
</div>

<div class="field"><label for="capture_date">Capture Date</label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'capture_date',
							'id'	=> 'capture_date', 'class' => 'textinput'),$file->capture_date );
?></div>
</div>

<div class="field"><label for="capture_device">Capture Device</label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'capture_device' ,'class' => 'textinput'),$file->capture_device);
?></div>
</div>

<div class="field"><label for="capture_details">Capture Details (e.g. Duration, resolution, bit depth, width, height)</label>
<div class="inputs"><?php
	echo textarea( array(	'rows'=>'20','cols'=>'40','name'	=> 'capture_details','class' => 'textinput' ),
					$file->capture_details );
?></div>
</div>

<div class="field"><label for="watermark">Watermark</label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'watermark', 'class' => 'textinput'),$file->watermark);
?></div>
</div>

<div class="field"><label for="encryption">Encryption</label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'encryption', 'class' => 'textinput'),$file->encryption);
?></div>
</div>

<div class="field"><label for="compression">Compression</label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'compression', 'class' => 'textinput'),$file->compression);
?></div>
</div>

<div class="field"><label for="post_processing">Post Processing</label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'post_processing', 'class' => 'textinput'),$file->post_processing);
?></div>
</div>

<div class="field"><label for="change_history">Change History</label>
<div class="inputs"><?php
	echo textarea( array('rows'=>'20','cols'=>'40',	'name'	=> 'change_history', 'class' => 'textinput' ),
						$file->change_history );
?></div>
</div>

<div class="field"><label class="readonly" for="archive_filename">Archive Filename <span class="readonly">(read only)</span></label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'archive_filename',
							'readonly' => 'readonly',
							'class' => 'readonly textinput'),$file->archive_filename );
?></div>
</div>

<div class="field"><label class="readonly" for="original_filename">Original File Name <span class="readonly">(read only)</span></label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'original_filename',
							'readonly' => 'readonly',
							'class' => 'readonly textinput'),$file->original_filename);
?></div>
</div>

<div class="field"><label class="readonly" for="size">File Size <span class="readonly">(read only)</span></label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'size',
							'readonly' => 'readonly',
							'class' => 'readonly textinput'),$file->size);
?></div>
</div>
</fieldset>

<fieldset>
	<legend>Type Metadata</legend>
<div class="field"><label class="readonly" for="mime_browser">File Mime Browser <span class="readonly">(read only)</span></label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'mime_browser',
							'readonly' => 'readonly',
							'class' => 'readonly textinput'),$file->mime_browser );
?></div>
</div>

<div class="field"><label class="readonly" for="mime_os">File Mime OS <span class="readonly">(read only)</span></label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'mime_os',
							'readonly' => 'readonly',
							'class' => 'readonly textinput'),$file->mime_os);
?></div>
</div>

<div class="field"><label class="readonly" for="type_os">File Type OS <span class="readonly">(read only)</span></label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'type_os',
							'readonly' => 'readonly',
							'class' => 'readonly textinput'),$file->type_os );
?></div>
</div>
</fieldset>

<fieldset>
	<legend>File History</legend>
	<div class="field"><label class="readonly" for="added">File Added <span class="readonly">(read only)</span></label>
<div class="inputs">	<?php
		echo text( array(	'name'	=> 'added',
								'readonly' => 'readonly',
								'class' => 'readonly textinput'),$file->added );
	?></div>
</div>
<div class="field"><label class="readonly" for="modified">File Modified <span class="readonly">(read only)</span></label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'modified',
							'readonly' => 'readonly',
							'class' => 'readonly textinput'),$file->modified );
?></div>
</div>

<div class="field"><label for="authentication">Authentication <span class="readonly">(read only)</span></label>
<div class="inputs"><?php
	echo text( array(	'name'	=> 'authentication',
							'readonly' => 'readonly',
							'class' => 'readonly textinput'),$file->authentication);
?></div>
</div>
</fieldset>

<fieldset>
<input type="submit" name="submit" class="submit submit-medium" value="Save Changes" id="file_edit" />
</fieldset>

</form>

</div>
<?php foot(); ?>