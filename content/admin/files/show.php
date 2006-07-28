<?php
//Layout: popup;
$file = $__c->files()->findById();

// Get Object this file belongs to
$object = $__c->objects()->findById($file->object_id);

?>
<html>
<head>
	<title><?php echo $file->file_title; ?></title>
	<style type="text/css" media="screen">
	body {padding: 10px; background: #ccc; font: 62.5% Verdana, sans-serif;}
	#wrap {background: #fff;padding: 10px;}
	h1 {font-weight:normal;}
	p {font-size: 1.1em;}
	a:link, a:visited {color: #38c;}
	a:hover, a:active {color: #369;}
	</style>
</head>
<body id="popup">
	<div id="wrap">
<?php include('subnav.php'); ?>
<h1>File</h1>
<?php if ($file->file_thumbnail_name): ?><img src="<?php echo WEB_VAULT_DIR.'/'.$file->file_archive_filename; ?>" alt="<?php echo $file->file_title; ?>" width="300" /><?php endif; ?>
<a href="<?php echo WEB_VAULT_DIR.'/'.addslashes($file->file_archive_filename); ?>">download file</a>

<h2>General Metadata</h2>
<?php if ($file->file_id): ?><p><strong>Identifier:</strong> <?php echo $file->file_id; ?></p><?php endif; ?>
<?php if ($file->file_title): ?><p><strong>Title:</strong> <?php echo $file->file_title; ?></p><?php endif; ?>
<?php if ($file->file_date): ?><p><strong>Date:</strong> <?php echo $file->file_date; ?></p><?php endif; ?>
<?php if ($file->file_subject): ?><p><strong>Subject:</strong> <?php echo $file->file_subject; ?></p><?php endif; ?>
<?php if ($file->file_description): ?><p><strong>Description:</strong> <?php echo $file->file_description; ?></p><?php endif; ?>
<?php if ($file->file_creator): ?><p><strong>Creator:</strong> <?php echo $file->file_creator; ?></p><?php endif; ?>
<?php if ($file->file_publisher): ?><p><strong>Publisher:</strong> <?php echo $file->file_publisher; ?></p><?php endif; ?>
<?php if ($file->file_format): ?><p><strong>Format:</strong> <?php echo $file->file_format; ?></p><?php endif; ?>
<?php if ($file->file_source): ?><p><strong>Source:</strong> <?php echo $file->file_source; ?></p><?php endif; ?>
<?php if ($file->file_language): ?><p><strong>Language:</strong> <?php echo $file->file_language; ?></p><?php endif; ?>
<?php if ($file->file_relation): ?><p><strong>Relation:</strong> <?php echo $file->file_relation; ?></p><?php endif; ?>
<?php if ($file->file_coverage_start): ?><p><strong>Coverage Start:</strong> <?php echo $file->file_coverage_start; ?></p><?php endif; ?>
<?php if ($file->file_coverage_end): ?><p><strong>Coverage End:</strong> <?php echo $file->file_coverage_end; ?></p><?php endif; ?>
<?php if ($file->file_rights): ?><p><strong>Rights:</strong> <?php echo $file->file_rights; ?></p><?php endif; ?>

<h2>Preservation and Digitization Metadata</h2>
<?php if ($file->file_transcriber): ?><p><strong>Transcriber:</strong> <?php echo $file->file_transcriber; ?></p><?php endif; ?>
<?php if ($file->file_producer): ?><p><strong>Producer:</strong> <?php echo $file->file_producer; ?></p><?php endif; ?>
<?php if ($file->file_render_device): ?><p><strong>Render Device:</strong> <?php echo $file->file_render_device; ?></p><?php endif; ?>
<?php if ($file->file_render_details): ?><p><strong>Render Details:</strong> <?php echo $file->file_render_details; ?></p><?php endif; ?>
<?php if ($file->file_capture_date): ?><p><strong>Capture Date:</strong> <?php echo $file->file_capture_date; ?></p><?php endif; ?>
<?php if ($file->file_capture_device): ?><p><strong>Capture Device:</strong> <?php echo $file->file_capture_device; ?></p><?php endif; ?>
<?php if ($file->file_capture_details): ?><p><strong>Capture Details:</strong> <?php echo $file->file_capture_details; ?></p><?php endif; ?>
<?php if ($file->file_watermark): ?><p><strong>Watermark:</strong> <?php echo $file->file_watermark; ?></p><?php endif; ?>
<?php if ($file->file_authentication): ?><p><strong>Authentication:</strong> <?php echo $file->file_authentication; ?></p><?php endif; ?>
<?php if ($file->file_encryption): ?><p><strong>Encryption:</strong> <?php echo  $file->file_encryption; ?></p><?php endif; ?>
<?php if ($file->file_compression): ?><p><strong>Compression:</strong> <?php echo $file->file_compression; ?></p><?php endif; ?>
<?php if ($file->file_post_processing): ?><p><strong>Post-processing:</strong> <?php echo $file->file_post_processing; ?></p><?php endif; ?>
<?php if ($file->file_change_history): ?><p><strong>Change History:</strong> <?php echo $file->file_change_history; ?></p><?php endif; ?>

<h2>Physical Metadata</h2>
<?php if ($file->file_archive_filename): ?><p><strong>Archive Filename:</strong> <?php echo $file->file_archive_filename; ?></p><?php endif; ?>
<?php if ($file->file_original_filename): ?><p><strong>Original Filename:</strong> <?php echo $file->file_original_filename; ?></p><?php endif; ?>
<?php if ($file->file_thumbnail_name): ?><p><strong>Thumbnail Name:</strong> <?php echo $file->file_thumbnail_name; ?></p><?php endif; ?>
<?php if ($file->file_size): ?><p><strong>File Size:</strong> <?php echo $file->file_size; ?> bytes</p><?php endif; ?>
<?php if ($file->file_mime_browser): ?><p><strong>Mime Type / Browser:</strong> <?php echo $file->file_mime_browser; ?></p><?php endif; ?>
<?php if ($file->file_mime_php): ?><p><strong>Mime Type / PHP:</strong> <?php echo $file->file_mime_php; ?></p><?php endif; ?>
<?php if ($file->file_mime_os): ?><p><strong>Mime Type / OS:</strong> <?php echo $file->file_mime_os; ?></p><?php endif; ?>
<?php if ($file->file_type_os): ?><p><strong>File Type / OS:</strong> <?php echo $file->file_type_os; ?></p><?php endif; ?>
<?php if ($file->file_modified): ?><p><strong>Date Modified:</strong> <?php echo $file->file_modified; ?></p><?php endif; ?>
<?php if ($file->file_added): ?><p><strong>Date Added:</strong> <?php echo $file->file_added; ?></p><?php endif; ?>

</div>
</body>
</html>