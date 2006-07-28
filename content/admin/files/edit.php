<?php
//Layout: popup;
$file = $__c->files()->edit();
?>
<html>
<head>
	<title>Edit File</title>
	<style type="text/css" media="all">
	body {padding: 10px; background: #ccc; font: 62.5% Verdana, sans-serif; color: #333;}
	h1 {font-weight:normal;}
	#wrap {padding: 10px; background: #fff;}
	fieldset {border:none;}
	input[type="text"],textarea {border: 1px solid #BBD9EE;width: 300px;padding: 4px; display:block;background: #e7f1f8;}
	input[type="text"]:focus,textarea:focus {background: #fff;}
	input.readonly {background:#eee !important; border: 1px solid #eee !important;}
	input.readonly:focus {background: #eee;}
	textarea {height: 200px;}
	label {display:block;margin-bottom: 4px;}
	a:link, a:visited {color: #38c;}
	a:hover, a:active {color: #369;}
	span.readonly {font-style:italic;}
	</style>
	<?php $_common->javascripts( 'prototype.js', 'scriptaculous.js', 'CalendarPopup.js' ); ?>
</head>
<body>
	<div id="wrap">
		<h1>Edit File</h1>
<form method="post" action="<?php echo $_link->to('files', 'edit'); ?>" name="editFile">
	
<h3>General Metadata</h3>	

<fieldset>
<label for="File[file_id]">Identifier</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_id]',
							'readonly' => 'readonly',
							'class' => 'readonly',
							'value'	=> $file->file_id ) );
?>
</fieldset>

<fieldset>
<label for="File[file_title]">Title</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_title]',
							'value'	=> $file->file_title ) );
?>
</fieldset>

<script>
var cal = new CalendarPopup('calDiv');
cal.showNavigationDropdowns();
cal.setReturnFunction("setDate");
function setDate(y,m,d)
{
	document.getElementById('File[file_date]').value = y+'-'+m+'-'+d+' 00:00:00';
}
</script>

<fieldset>
<label for="File[file_date]">Date</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_date]',
							'id'	=> 'File[file_date]',
							'value'	=> $file->file_date ));
?>
<a href="javascript:void(0)" name="calAnchor" id="calAnchor" onclick="cal.showCalendar('calAnchor'); return false;">Select a Date</a>
<div id="calDiv" style="font-size:smaller; position:absolute; margin-left:200px;visibility:hidden;background-color:#fff;"></div>
</fieldset>

<fieldset>
<label for="File[file_subject]">Subject</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_subject]',
							'value'	=> $file->file_subject ) );
?>
</fieldset>

<fieldset>
<label for="File[file_description]">Description</label>
<?php
	$_form->textarea( array(	'name'	=> 'File[file_description]' ),
						$file->file_description );
?>
</fieldset>

<fieldset>
<label for="File[file_creator]">Creator</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_creator]',
							'value'	=> $file->file_creator ) );
?>
</fieldset>

<fieldset>
<label for="File[file_publisher]">Publisher</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_publisher]', 'value' => $file->file_publisher ) );
?>
</fieldset>
<fieldset>
<label for="File[file_format]">Format</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_format]', 'value' => $file->file_format ) );
?>
</fieldset>

<fieldset>
<label for="File[file_source]">Source</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_source]', 'value' => $file->file_source ) );
?>
</fieldset>
<fieldset>
<label for="File[file_language]">Language</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_language]',
							'value'	=> $file->file_language ) );
?>
</fieldset>


<fieldset>
<label for="File[file_relation]">Relation</label>
<?php
	$_form->textarea( array(	'name'	=> 'File[file_relation]' ),
						$file->file_relation );
?>
</fieldset>


<fieldset>
	<script>
	var cal3 = new CalendarPopup('calDiv3');
	cal3.showNavigationDropdowns();
	cal3.setReturnFunction("setDate3");
	function setDate3(y,m,d)
	{
		document.getElementById('File[file_coverage_start]').value = y+'-'+m+'-'+d+' 00:00:00';
	}
	</script>
<label for="File[file_coverage_start]">Coverage Start</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_coverage_start]',
							'id'	=> 'File[file_coverage_start]',
							'value'	=> $file->file_coverage_start ) );
?>
<a href="javascript:void(0)" name="calAnchor3" id="calAnchor3" onclick="cal3.showCalendar('calAnchor3'); return false;">Select a Date</a>
<div id="calDiv3" style="font-size:smaller; position:absolute; margin-left:200px;visibility:hidden;background-color:#fff;"></div>
</fieldset>
<fieldset>
	<script>
	var cal4 = new CalendarPopup('calDiv4');
	cal4.showNavigationDropdowns();
	cal4.setReturnFunction("setDate4");
	function setDate4(y,m,d)
	{
		document.getElementById('File[file_coverage_end]').value = y+'-'+m+'-'+d+' 00:00:00';
	}
	</script>
<label for="File[file_coverage_end]">Coverage End</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_coverage_end]',
							'id'	=> 'File[file_coverage_end]',
							'value'	=> $file->file_coverage_end ) );
?>
<a href="javascript:void(0)" name="calAnchor4" id="calAnchor4" onclick="cal4.showCalendar('calAnchor4'); return false;">Select a Date</a>
<div id="calDiv4" style="font-size:smaller; position:absolute; margin-left:200px;visibility:hidden;background-color:#fff;"></div>
</fieldset>

<fieldset>
<label for="File[file_rights]">Rights</label>
<?php
	$_form->textarea( array(	'name'	=> 'File[file_rights]' ),
						$file->file_rights );
?>
</fieldset>

<h3>Preservation and Digitization Metadata</h3>	

<fieldset>
<label for="File[file_transcriber]">Transcriber</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_transcriber]',
				'value' =>	$file->file_transcriber ));
?>
</fieldset>

<fieldset>
<label for="File[file_producer]">Producer</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_producer]',
					'value' =>	$file->file_producer ) );
?>
</fieldset>

<fieldset>
<label for="File[file_render_device]">Render Device</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_render_device]',
					'value' =>	$file->file_render_device ) );
?>
</fieldset>

<fieldset>
<label for="File[file_render_details]">Render Details</label>
<?php
	$_form->textarea( array(	'name'	=> 'File[file_render_details]' ),
						$file->file_render_details );
?>
</fieldset>

<script>
var cal2 = new CalendarPopup('calDiv2');
cal2.showNavigationDropdowns();
cal2.setReturnFunction("setDate2");
function setDate2(y,m,d)
{
	document.getElementById('File[file_capture_date]').value = y+'-'+m+'-'+d+' 00:00:00';
}
</script>


<fieldset>
<label for="File[file_capture_date]">Capture Date</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_capture_date]',
							'id'	=> 'File[file_capture_date]',
							'value'	=> $file->file_capture_date ) );
?>
<a href="javascript:void(0)" name="calAnchor2" id="calAnchor2" onclick="cal2.showCalendar('calAnchor2'); return false;">Select a Date</a>
<div id="calDiv2" style="font-size:smaller; position:absolute; margin-left:200px;visibility:hidden;background-color:#fff;"></div>
</fieldset>

<fieldset>
<label for="File[file_capture_device]">Capture Device</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_capture_device]' ,
					'value' => $file->file_capture_device ));
?>
</fieldset>
<fieldset>
<label for="File[file_capture_details]">Capture Details</label>
<?php
	$_form->textarea( array(	'name'	=> 'File[file_capture_details]' ),
					$file->file_capture_details );
?>
</fieldset>

<fieldset>
<label for="File[file_watermark]">Watermark</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_watermark]',
					'value' => $file->file_watermark ));
?>
</fieldset>

<fieldset>
<label for="File[file_authentication]">Authentication</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_authentication]',
					'value' => $file->file_authentication ));
?>
</fieldset>

<fieldset>
<label for="File[file_encryption]">Encryption</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_encryption]',
							'value' => $file->file_encryption ));
?>
</fieldset>

<fieldset>
<label for="File[file_compression]">Compression</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_compression]',
							'value' => $file->file_compression ));
?>
</fieldset>

<fieldset>
<label for="File[file_post_processing]">Post Processing</label>
<?php
	$_form->text( array(	'name'	=> 'File[file_post_processing]',
							'value' => $file->file_post_processing ));
?>
</fieldset>

<fieldset>
<label for="File[file_change_history]">Change History</label>
<?php
	$_form->textarea( array(	'name'	=> 'File[file_change_history]',
						$file->file_change_history ) );
?>
</fieldset>

<h3>Physical Metadata</h3>	

<fieldset>
<label class="readonly" for="File[file_archive_filename]">Archive Filename <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_archive_filename]',
							'readonly' => 'readonly',
							'class' => 'readonly',
							'value' => $file->file_archive_filename ) );
?>
</fieldset>

<fieldset>
<label class="readonly" for="File[file_original_filename]">Original Filename <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_original_filename]',
							'readonly' => 'readonly',
							'class' => 'readonly',
							'value' => $file->file_original_filename ));
?>
</fieldset>

<fieldset>
<label class="readonly" for="File[file_thumbnail_name]">Thumbnail Name <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_thumbnail_name]',
							'readonly' => 'readonly',
							'class' => 'readonly',
							'value' => $file->file_thumbnail_name ));
?>
</fieldset>

<fieldset>
<label class="readonly" for="File[file_size]">File Size <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_size]',
							'readonly' => 'readonly',
							'class' => 'readonly',
							'value' => $file->file_size ));
?>
</fieldset>

<fieldset>
<label class="readonly" for="File[file_mime_browser]">File Mime Browser <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_mime_browser]',
							'readonly' => 'readonly',
							'class' => 'readonly',
							'value' => $file->file_mime_browser ) );
?>
</fieldset>

<fieldset>
<label class="readonly" for="File[file_mime_php]">File Mime PHP <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_mime_php]',
							'readonly' => 'readonly',
							'class' => 'readonly',
							'value' => $file->file_mime_php ));
?>
</fieldset>

<fieldset>
<label class="readonly" for="File[file_mime_os]">File Mime OS <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_mime_os]',
							'readonly' => 'readonly',
							'class' => 'readonly',
							'value' =>	$file->file_mime_os ));
?>
</fieldset>

<fieldset>
<label class="readonly" for="File[file_type_os]">File Type OS <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_type_os]',
							'readonly' => 'readonly',
							'class' => 'readonly',
							'value' => $file->file_type_os ) );
?>
</fieldset>

<fieldset>
<label class="readonly" for="File[file_modified]">File Modified <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_modified]',
							'readonly' => 'readonly',
							'class' => 'readonly',
							'value' =>	$file->file_modified ) );
?>
</fieldset>

<fieldset>
<label class="readonly" for="File[file_added]">File Added <span class="readonly">(read only)</span></label>
<?php
	$_form->text( array(	'name'	=> 'File[file_added]',
							'readonly' => 'readonly',
							'class' => 'readonly',
							'value' => $file->file_added ) );
?>
</fieldset>
<input type="hidden" name="File[file_id]" id="File[file_id]" value="<?php echo $file->file_id; ?>">
<input type="submit" name="file_edit" value="Edit file &gt;&gt;" id="file_edit" />
</form>
</div>
</body>
</html>