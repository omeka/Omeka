<?php head(); ?>
<?php common('archive-nav'); ?>

<div id="primary">
<h1>File #<?php echo h($file->id); ?></h1>

<div id="lgimage" class="section">
<?php if ($file->hasThumbnail()): ?>
	<?php fullsize($file, array(), 400); ?>
<?php else: ?>
	<?php echo h($file->original_filename); ?>
<?php endif; ?>
<a href="<?php echo file_download_uri($file); ?>">Download this file</a>
</div>
	
<div id="core-metadata" class="section">
<h2>Core Metadata</h2>
<dl>	
<dt>Identifier:</dt> <dd><?php if ($file->id): ?><?php echo h($file->id); ?><?php endif; ?></dd>
<dt>Title:</dt> <dd><?php if ($file->title): ?><?php echo h($file->title); ?><?php endif; ?></dd>
<dt>Creator:</dt> <dd><?php if ($file->creator): ?><?php echo h($file->creator); ?><?php endif; ?></dd>
<dt>Subject:</dt> <dd><?php if ($file->subject): ?><?php echo h($file->subject); ?><?php endif; ?></dd>
<dt>Description:</dt> <dd><?php if ($file->description): ?><?php echo h($file->description); ?><?php endif; ?></dd>
<dt>Publisher:</dt> <dd><?php if ($file->publisher): ?><?php echo h($file->publisher); ?><?php endif; ?></dd>
<dt>Other Creator:</dt> <dd><?php if ($file->additional_creator): ?><?php echo h($file->additional_creator); ?><?php endif; ?></dd>
<dt>Date:</dt> <dd><?php if ($file->date): ?><?php echo h($file->date); ?><?php endif; ?></dd>
<dt>Source:</dt> <dd><?php if ($file->source): ?><?php echo h($file->source); ?><?php endif; ?></dd>
<dt>Language:</dt> <dd><?php if ($file->language): ?><?php echo h($file->language); ?><?php endif; ?></dd>
<dt>Relation:</dt> <dd><?php if ($file->relation): ?><?php echo h($file->relation); ?><?php endif; ?></dd>
<dt>Coverage</dt> <dd><?php if ($file->coverage): ?><?php echo h($file->coverage); ?><?php endif; ?></dd>
<dt>Rights:</dt> <dd><?php if ($file->rights): ?><?php echo h($file->rights); ?><?php endif; ?></dd>
<dt>Format:</dt> <dd><?php if ($file->format): ?><?php echo h($file->format); ?><?php endif; ?></dd>
</dl>
</div>

<div id="format-metadata" class="section">
<h2>Format Metadata</h2>
<dl>
<dt>Transcriber:</dt> <dd><?php if ($file->transcriber): ?><?php echo h($file->transcriber); ?><?php endif; ?></dd>
<dt>Producer:</dt> <dd><?php if ($file->producer): ?><?php echo h($file->producer); ?><?php endif; ?></dd>
<dt>Render Device:</dt> <dd><?php if ($file->render_device): ?><?php echo h($file->render_device); ?><?php endif; ?></dd>
<dt>Render Details:</dt> <dd><?php if ($file->render_details): ?><?php echo h($file->render_details); ?><?php endif; ?></dd>
<dt>Capture Date:</dt> <dd><?php if ($file->capture_date): ?><?php echo h($file->capture_date); ?><?php endif; ?></dd>
<dt>Capture Device:</dt> <dd><?php if ($file->capture_device): ?><?php echo h($file->capture_device); ?><?php endif; ?></dd>
<dt>Capture Details:</dt> <dd><?php if ($file->capture_details): ?><?php echo h($file->capture_details); ?><?php endif; ?></dd>
<dt>Watermark:</dt> <dd><?php if ($file->watermark): ?><?php echo h($file->watermark); ?><?php endif; ?></dd>
<dt>Encryption:</dt> <dd><?php if ($file->encryption): ?><?php echo h($file->encryption); ?><?php endif; ?></dd>
<dt>Compression:</dt> <dd><?php if ($file->compression): ?><?php echo h($file->compression); ?><?php endif; ?></dd>
<dt>Post-processing:</dt> <dd><?php if ($file->post_processing): ?><?php echo h($file->post_processing); ?><?php endif; ?></dd>
<dt>Change History:</dt> <dd><?php if ($file->change_history): ?><?php echo h($file->change_history); ?><?php endif; ?></dd>
<dt>Archive Filename:</dt> <dd><?php if ($file->archive_filename): ?><?php echo h($file->archive_filename); ?><?php endif; ?></dd>
<dt>Original Filename:</dt> <dd><?php if ($file->original_filename): ?><?php echo h($file->original_filename); ?><?php endif; ?></dd>
<dt>File Size:</dt> <dd><?php if ($file->size): ?><?php echo h($file->size); ?> bytes<?php endif; ?></dd>
</dl>
</div>

<div id="type-metadata" class="section">
<h2>Type Metadata</h2>
<dl>
<dt>Mime Type / Browser:</dt> <dd><?php if ($file->mime_browser): ?><?php echo h($file->mime_browser); ?><?php endif; ?></dd>
<dt>Mime Type / OS:</dt> <dd><?php if ($file->mime_os): ?><?php echo h($file->mime_os); ?><?php endif; ?></dd>
<dt>File Type / OS:</dt> <dd><?php if ($file->type_os): ?><?php echo h($file->type_os); ?><?php endif; ?></dd>
</dl>
</div>
	
<div id="file-history" class="section">
<h2>File History</h2>
<dl>
<dt>Date Added:</dt> <dd><?php if ($file->added): ?><?php echo h($file->added); ?><?php endif; ?></dd>
<dt>Date Modified:</dt> <dd><?php if ($file->modified): ?><?php echo h($file->modified); ?><?php endif; ?></dd>
<dt>Authentication:</dt> <dd><?php if ($file->authentication): ?><?php echo h($file->authentication); ?><?php endif; ?></dd>
</dl>
</div>

</div><!--end primary-->
<?php foot();?>