<?php
//Layout: default;
$file = $__c->files()->findById();

// Get Item this file belongs to
$item = $__c->items()->findById($file->item_id);

?>
<?php include('subnav.php'); ?>
<h2>File #<?php echo $file->file_id; ?></h2>

<div id="primary">
<div id="core-metadata" class="section">
	<h3>Core Metadata</h3>
	
<dl><dt>Identifier:</dt> <?php if ($file->file_id): ?><dd><?php echo $file->file_id; ?></dd><?php endif; ?></dl>
<?php if ($file->file_title): ?><dl><dt>Title:</dt> <dd><?php echo $file->file_title; ?></dd><?php endif; ?></dl>
<dl><dt>Creator:</dt> <?php if ($file->file_creator): ?><dd><?php echo $file->file_creator; ?></dd><?php endif; ?></dl>
<dl><dt>Subject:</dt> <?php if ($file->file_subject): ?><dd><?php echo $file->file_subject; ?></dd><?php endif; ?></dl>

<dl><dt>Description:</dt> <?php if ($file->file_description): ?><dd><?php echo $file->file_description; ?></dd><?php endif; ?></dl>

<dl><dt>Publisher:</dt> <?php if ($file->file_publisher): ?><dd><?php echo $file->file_publisher; ?></dd><?php endif; ?></dl>
<dl><dt>Other Creator:</dt> <?php if ($file->file_creator_other): ?><dd><?php echo $file->file_creator_other; ?></dd><?php endif; ?></dl>
<dl><dt>Date:</dt> <?php if ($file->file_date): ?><dd><?php echo $file->file_date; ?></dd><?php endif; ?></dl>
<dl><dt>Source:</dt> <?php if ($file->file_source): ?><dd><?php echo $file->file_source; ?></dd><?php endif; ?></dl>
<dl><dt>Language:</dt> <?php if ($file->file_language): ?><dd><?php echo $file->file_language; ?></dd><?php endif; ?></dl>
<dl><dt>Relation:</dt> <?php if ($file->file_relation): ?><dd><?php echo $file->file_relation; ?></dd><?php endif; ?></dl>
<dl><dt>Coverage</dt> <?php if ($file->file_coverage): ?><dd><?php echo $file->file_coverage; ?></dd><?php endif; ?></dl>
<dl><dt>Rights:</dt> <?php if ($file->file_rights): ?><dd><?php echo $file->file_rights; ?></dd><?php endif; ?></dl>
<dl><dt>Format:</dt> <dd>View</dd></dl>
<dl><dt>Type:</dt> <dd>View</dd></dl>
</div>
<div class="section" id="format-metadata">
<h3>Format Metadata</h3>
<dl><dt>Transcriber:</dt> <?php if ($file->file_transcriber): ?><dd><?php echo $file->file_transcriber; ?></dd><?php endif; ?></dl>
<dl><dt>Producer:</dt> <?php if ($file->file_producer): ?><dd><?php echo $file->file_producer; ?></dd><?php endif; ?></dl>
<dl><dt>Render Device:</dt> <?php if ($file->file_render_device): ?><dd><?php echo $file->file_render_device; ?></dd><?php endif; ?></dl>
<dl><dt>Render Details:</dt> <?php if ($file->file_render_details): ?><dd><?php echo $file->file_render_details; ?></dd><?php endif; ?></dl>
<dl><dt>Capture Date:</dt> <?php if ($file->file_capture_date): ?><dd><?php echo $file->file_capture_date; ?></dd><?php endif; ?></dl>
<dl><dt>Capture Device:</dt> <?php if ($file->file_capture_device): ?><dd><?php echo $file->file_capture_device; ?></dd><?php endif; ?></dl>
<dl><dt>Capture Details:</dt> <?php if ($file->file_capture_details): ?><dd><?php echo $file->file_capture_details; ?></dd><?php endif; ?></dl>
<dl><dt>Watermark:</dt> <?php if ($file->file_watermark): ?><dd><?php echo $file->file_watermark; ?></dd><?php endif; ?></dl>

<dl><dt>Encryption:</dt> <?php if ($file->file_encryption): ?><dd><?php echo  $file->file_encryption; ?></dd><?php endif; ?></dl>
<dl><dt>Compression:</dt> <?php if ($file->file_compression): ?><dd><?php echo $file->file_compression; ?></dd><?php endif; ?></dl>
<dl><dt>Post-processing:</dt> <?php if ($file->file_post_processing): ?><dd><?php echo $file->file_post_processing; ?></dd><?php endif; ?></dl>
<dl><dt>Change History:</dt> <?php if ($file->file_change_history): ?><dd><?php echo $file->file_change_history; ?></dd><?php endif; ?></dl>

<dl><dt>Archive Filename:</dt> <?php if ($file->file_archive_filename): ?><dd><?php echo $file->file_archive_filename; ?></dd><?php endif; ?></dl>
<dl><dt>Original Filename:</dt> <?php if ($file->file_original_filename): ?><dd><?php echo $file->file_original_filename; ?></dd><?php endif; ?></dl>
<dl><dt>Thumbnail Name:</dt> <?php if ($file->file_thumbnail_name): ?><dd><?php echo $file->file_thumbnail_name; ?></dd><?php endif; ?></dl>
<dl><dt>File Size:</dt> <?php if ($file->file_size): ?><dd><?php echo $file->file_size; ?> bytes</dd><?php endif; ?></dl>
</div>
<div id="type-metadata" class="section">
	<h3>Type Metadata</h3>
<dl><dt>Mime Type / Browser:</dt> <?php if ($file->file_mime_browser): ?><dd><?php echo $file->file_mime_browser; ?></dd><?php endif; ?></dl>
<dl><dt>Mime Type / PHP:</dt> <?php if ($file->file_mime_php): ?><dd><?php echo $file->file_mime_php; ?></dd><?php endif; ?></dl>
<dl><dt>Mime Type / OS:</dt> <?php if ($file->file_mime_os): ?><dd><?php echo $file->file_mime_os; ?></dd><?php endif; ?></dl>
<dl><dt>File Type / OS:</dt> <?php if ($file->file_type_os): ?><dd><?php echo $file->file_type_os; ?></dd><?php endif; ?></dl>
</div>
</div>
<div id="secondary">
<div id="image">
<?php if ($file->file_thumbnail_name): ?><a href="<?php echo WEB_VAULT_DIR.'/'.addslashes($file->file_archive_filename); ?>"><img src="<?php echo WEB_VAULT_DIR.'/'.$file->file_archive_filename; ?>" alt="<?php echo $file->file_title; ?>" width="400" /></a><?php endif; ?>
</div>

<div id="file-history" class="section">
	<h3>File History</h3>
	<dl><dt>Date Added:</dt> <?php if ($file->file_added): ?><dd><?php echo $file->file_added; ?></dd><?php endif; ?></dl>
	<dl><dt>Date Modified:</dt> <?php if ($file->file_modified): ?><dd><?php echo $file->file_modified; ?></dd><?php endif; ?></dl>
	<dl><dt>Authentication:</dt> <?php if ($file->file_authentication): ?><dd><?php echo $file->file_authentication; ?></dd><?php endif; ?></dl>
</div>
</div>