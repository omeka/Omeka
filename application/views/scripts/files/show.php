<?php
    $fileTitle = metadata('file', array('Dublin Core', 'Title')) ? strip_formatting(metadata('file', array('Dublin Core', 'Title'))) : metadata('file', 'original filename');

    if ($fileTitle != '') {
        $fileTitle = ': &quot;' . $fileTitle . '&quot; ';
    } else {
        $fileTitle = '';
    }
    $fileTitle = __('File #%s', metadata('file', 'id')) . $fileTitle;
?>
<?php head(array('title' => $fileTitle, 'bodyclass'=>'files show primary-secondary')); ?>

<h1><?php echo $fileTitle; ?></h1>

<div id="primary">

    <?php echo display_file($file, array('imageSize'=>'fullsize')); ?>

    <?php echo all_element_texts('file'); ?>

    <div id="format-metadata">
        <div id="filename" class="element">
            <h3><?php echo __('Filename'); ?></h3>
            <div class="element-text"><?php echo metadata('file', 'Filename'); ?></div>
        </div>

        <div id="original-filename" class="element">
            <h3><?php echo __('Original Filename'); ?></h3>
            <div class="element-text"><?php echo metadata('file', 'Original Filename'); ?></div>
        </div>

        <div id="file-size" class="element">
            <h3><?php echo __('File Size'); ?></h3>
            <div class="element-text"><?php echo metadata('file', 'Size'); ?> bytes</div>
        </div>
    </div><!-- end format-metadata -->

    <div id="type-metadata" class="section">
        <h2><?php echo __('Type Metadata'); ?></h2>
        <div id="mime-type-browser" class="element">
            <h3><?php echo __('Mime Type / Browser'); ?></h3>
            <div class="element-text"><?php echo metadata('file', 'MIME Type'); ?></div>
        </div>
        <div id="mime-type-os" class="element">
            <h3><?php echo __('Mime Type / OS'); ?></h3>
            <div class="element-text"><?php echo metadata('file', 'MIME Type OS'); ?></div>
        </div>
        <div id="file-type-os" class="element">
            <h3><?php echo __('File Type / OS'); ?></h3>
            <div class="element-text"><?php echo metadata('file', 'File Type OS'); ?></div>
        </div>
    </div><!-- end type-metadata -->

    <div id="file-history" class="section">
        <h2><?php echo __('File History'); ?></h2>
        <div id="date-added" class="element">
            <h3><?php echo __('Date Added'); ?></h3>
            <div class="element-text"><?php echo metadata('file', 'Date Added'); ?></div>
        </div>
        <div id="date-modified" class="element">
            <h3><?php echo __('Date Modified'); ?></h3>
            <div class="element-text"><?php echo metadata('file', 'Date Modified'); ?></div>
        </div>
        <div id="authentication" class="element">
            <h3><?php echo __('Authentication'); ?></h3>
            <div class="element-text"><?php echo metadata('file', 'Authentication'); ?></div>
        </div>
    </div><!-- end file-history -->

</div><!--end primary-->
<?php foot();?>
