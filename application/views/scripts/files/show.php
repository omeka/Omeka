<?php head(); ?>

<h1>File #<?php echo item_file('Id'); ?></h1>

<div id="primary">

<?php echo display_file($file, array('imageSize'=>'fullsize')); ?>

<?php echo show_file_metadata(); ?>

<div id="format-metadata">
    <div id="archive-filename" class="element">
        <h3>Archive Filename</h3> 
        <div class="element-text"><?php echo item_file('Archive Filename'); ?></div>
    </div>

    <div id="original-filename" class="element">
        <h3>Original Filename</h3> 
        <div class="element-text"><?php echo item_file('Original Filename'); ?></div>
    </div>
    
    <div id="file-size" class="element">
        <h3>File Size</h3> 
        <div class="element-text"><?php echo item_file('Size'); ?> bytes</div>
    </div>
</div>

<div id="type-metadata" class="section">
<h2>Type Metadata</h2>
    <div id="mime-type-browser" class="element">
        <h3>Mime Type / Browser:</h3> 
        <div class="element-text"><?php echo item_file('MIME Type'); ?></div>
    </div>
    <div id="mime-type-os" class="element">
        <h3>Mime Type / OS</h3> 
        <div class="element-text"><?php echo item_file('MIME Type OS'); ?></div>
    </div>
    <div id="file-type-os" class="element">
        <h3>File Type / OS</h3> 
        <div class="element-text"><?php echo item_file('File Type OS'); ?></div>
    </div>
</div>
	
<div id="file-history" class="section">
<h2>File History</h2>
<div id="date-added" class="element">
<h3>Date Added</h3> 
    <div class="element-text"><?php echo item_file('Date Added'); ?></div>
    </div>
<div id="date-modified" class="element">
<h3>Date Modified</h3> <div class="element-text"><?php echo item_file('Date Modified'); ?></div>
</div>
<div id="authentication" class="element">
<h3>Authentication</h3> <div class="element-text"><?php echo item_file('Authentication'); ?></div>
</div>
</div>

</div><!--end primary-->
<?php foot();?>