<?php head(); ?>
<h2>File #<?php echo h($file->id); ?></h2>

<div id="primary">
<div id="core-metadata" class="section">
	<h3>Core Metadata</h3>
	
<dl><dt>Identifier:</dt> <?php if ($file->id): ?><dd><?php echo h($file->id); ?></dd><?php endif; ?></dl>
<dl><dt>Title:</dt> <?php if ($file->title): ?><dd><?php echo h($file->title); ?></dd><?php endif; ?></dl>
<dl><dt>Creator:</dt> <?php if ($file->creator): ?><dd><?php echo h($file->creator); ?></dd><?php endif; ?></dl>
<dl><dt>Subject:</dt> <?php if ($file->subject): ?><dd><?php echo h($file->subject); ?></dd><?php endif; ?></dl>

<dl><dt>Description:</dt> <?php if ($file->description): ?><dd><?php echo h($file->description); ?></dd><?php endif; ?></dl>

<dl><dt>Publisher:</dt> <?php if ($file->publisher): ?><dd><?php echo h($file->publisher); ?></dd><?php endif; ?></dl>
<dl><dt>Other Creator:</dt> <?php if ($file->additional_creator): ?><dd><?php echo h($file->additional_creator); ?></dd><?php endif; ?></dl>
<dl><dt>Date:</dt> <?php if ($file->date): ?><dd><?php echo h($file->date); ?></dd><?php endif; ?></dl>
<dl><dt>Source:</dt> <?php if ($file->source): ?><dd><?php echo h($file->source); ?></dd><?php endif; ?></dl>
<dl><dt>Language:</dt> <?php if ($file->language): ?><dd><?php echo h($file->language); ?></dd><?php endif; ?></dl>
<dl><dt>Relation:</dt> <?php if ($file->relation): ?><dd><?php echo h($file->relation); ?></dd><?php endif; ?></dl>
<dl><dt>Coverage</dt> <?php if ($file->coverage): ?><dd><?php echo h($file->coverage); ?></dd><?php endif; ?></dl>
<dl><dt>Rights:</dt> <?php if ($file->rights): ?><dd><?php echo h($file->rights); ?></dd><?php endif; ?></dl>
<dl><dt>Format:</dt> <?php if ($file->format): ?><dd><?php echo h($file->format); ?></dd><?php endif; ?></dl>
</div>
<div class="section" id="format-metadata">
<h3>Format Metadata</h3>
<dl><dt>Transcriber:</dt> <?php if ($file->transcriber): ?><dd><?php echo h($file->transcriber); ?></dd><?php endif; ?></dl>
<dl><dt>Producer:</dt> <?php if ($file->producer): ?><dd><?php echo h($file->producer); ?></dd><?php endif; ?></dl>
<dl><dt>Render Device:</dt> <?php if ($file->render_device): ?><dd><?php echo h($file->render_device); ?></dd><?php endif; ?></dl>
<dl><dt>Render Details:</dt> <?php if ($file->render_details): ?><dd><?php echo h($file->render_details); ?></dd><?php endif; ?></dl>
<dl><dt>Capture Date:</dt> <?php if ($file->capture_date): ?><dd><?php echo h($file->capture_date); ?></dd><?php endif; ?></dl>
<dl><dt>Capture Device:</dt> <?php if ($file->capture_device): ?><dd><?php echo h($file->capture_device); ?></dd><?php endif; ?></dl>
<dl><dt>Capture Details:</dt> <?php if ($file->capture_details): ?><dd><?php echo h($file->capture_details); ?></dd><?php endif; ?></dl>
<dl><dt>Watermark:</dt> <?php if ($file->watermark): ?><dd><?php echo h($file->watermark); ?></dd><?php endif; ?></dl>

<dl><dt>Encryption:</dt> <?php if ($file->encryption): ?><dd><?php echo h($file->encryption); ?></dd><?php endif; ?></dl>
<dl><dt>Compression:</dt> <?php if ($file->compression): ?><dd><?php echo h($file->compression); ?></dd><?php endif; ?></dl>
<dl><dt>Post-processing:</dt> <?php if ($file->post_processing): ?><dd><?php echo h($file->post_processing); ?></dd><?php endif; ?></dl>
<dl><dt>Change History:</dt> <?php if ($file->change_history): ?><dd><?php echo h($file->change_history); ?></dd><?php endif; ?></dl>

<dl><dt>Archive Filename:</dt> <?php if ($file->archive_filename): ?><dd><?php echo h($file->archive_filename); ?></dd><?php endif; ?></dl>
<dl><dt>Original Filename:</dt> <?php if ($file->original_filename): ?><dd><?php echo h($file->original_filename); ?></dd><?php endif; ?></dl>
<dl><dt>File Size:</dt> <?php if ($file->size): ?><dd><?php echo h($file->size); ?> bytes</dd><?php endif; ?></dl>
</div>
<div id="type-metadata" class="section">
	<h3>Type Metadata</h3>
<dl><dt>Mime Type / Browser:</dt> <?php if ($file->mime_browser): ?><dd><?php echo h($file->mime_browser); ?></dd><?php endif; ?></dl>
<dl><dt>Mime Type / OS:</dt> <?php if ($file->mime_os): ?><dd><?php echo h($file->mime_os); ?></dd><?php endif; ?></dl>
<dl><dt>File Type / OS:</dt> <?php if ($file->type_os): ?><dd><?php echo h($file->type_os); ?></dd><?php endif; ?></dl>
</div>
</div>
<div id="secondary">
<div id="image">
<?php if ($file->hasThumbnail()): ?>
	<img src="<?php echo WEB_FILES.'/'.$file->archive_filename; ?>" alt="<?php echo h($file->title); ?>" width="400" />
<?php else: ?>
	<?php echo h($file->archive_filename); ?>
<?php endif; ?>
<a href="<?php echo WEB_FILES.'/'.addslashes($file->archive_filename); ?>">Download this file</a>
</div>

<div id="file-history" class="section">
	<h3>File History</h3>
	<dl><dt>Date Added:</dt> <?php if ($file->added): ?><dd><?php echo h($file->added); ?></dd><?php endif; ?></dl>
	<dl><dt>Date Modified:</dt> <?php if ($file->modified): ?><dd><?php echo h($file->modified); ?></dd><?php endif; ?></dl>
	<dl><dt>Authentication:</dt> <?php if ($file->authentication): ?><dd><?php echo h($file->authentication); ?></dd><?php endif; ?></dl>
</div>
</div>
<?php foot();?>