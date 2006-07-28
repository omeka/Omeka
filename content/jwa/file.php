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
	h1 {font-weight:normal;}
	#wrap {background: #fff;padding: 10px;}
	p {font-size: 1.1em;}
	a:link, a:visited {color: #38c;}
	a:hover, a:active {color: #369;}
	</style>
</head>
<body id="popup">
	<div id="wrap">
<h1>File</h1>
<p>file type:<?php echo $file->file_mime_browser; ?></p>
<?php if(preg_match("/image/",$file->file_mime_browser)):?>
<img src="<?php echo WEB_VAULT_DIR.'/'.$file->file_archive_filename; ?>" width="400" />
<?php endif; ?>
<?php if ($file->file_title): ?><p><strong>Title:</strong> <?php echo $file->file_title; ?></p><?php endif; ?>

<?php if ($file->file_description): ?><p><strong>Description:</strong> <?php echo $file->file_description; ?></p><?php endif; ?>

<?php if ($file->file_date): ?><p><strong>Date:</strong> <?php echo $file->file_date; ?></p><?php endif; ?>

<p>[<a href="<?php echo WEB_VAULT_DIR.'/'.$file->file_archive_filename; ?>">Download File</a>]</p>
</div>
</body>
</html>