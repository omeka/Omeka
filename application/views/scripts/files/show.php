<?php head(); ?>

<h1>File #<?php echo item_file('Id'); ?></h1>

<div id="primary">

<?php echo display_file($file, array('imageSize'=>'fullsize')); ?>

<?php echo show_file_metadata(); ?>

<div id="format-metadata" class="section">
<dl>
<dt>Archive Filename:</dt> <dd><?php echo item_file('Archive Filename'); ?></dd>
<dt>Original Filename:</dt> <dd><?php echo item_file('Original Filename'); ?></dd>
<dt>File Size:</dt> <dd><?php echo item_file('Size'); ?> bytes</dd>
</dl>
</div>

<div id="type-metadata" class="section">
<h2>Type Metadata</h2>
<dl>
<dt>Mime Type / Browser:</dt> <dd><?php echo item_file('MIME Type'); ?></dd>
<dt>Mime Type / OS:</dt> <dd><?php echo item_file('MIME Type OS'); ?></dd>
<dt>File Type / OS:</dt> <dd><?php echo item_file('File Type OS'); ?></dd>
</dl>
</div>
	
<div id="file-history" class="section">
<h2>File History</h2>
<dl>
<dt>Date Added:</dt> <dd><?php echo item_file('Date Added'); ?></dd>
<dt>Date Modified:</dt> <dd><?php echo item_file('Date Modified'); ?></dd>
<dt>Authentication:</dt> <dd><?php echo item_file('Authentication'); ?></dd>
</dl>
</div>

</div><!--end primary-->
<?php foot();?>