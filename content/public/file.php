<?php
//Layout: popup;
$file = $__c->files()->findById();

// Get Object this file belongs to
$object = $__c->objects()->findById($file->object_id);
?>
<h1>File</h1>
<p>file type:<?php echo $file->file_mime_browser; ?></p>
<img src="<?php echo WEB_VAULT_DIR.'/'.$file->file_archive_filename; ?>" width="400" />
<?php if ($file->file_title): ?><p><strong>Title:</strong> <?php echo $file->file_title; ?></p><?php endif; ?>

<?php if ($file->file_description): ?><p><strong>Description:</strong> <?php echo $file->file_description; ?></p><?php endif; ?>

<?php if ($file->file_date): ?><p><strong>Date:</strong> <?php echo $file->file_date; ?></p><?php endif; ?>

<p>[<a href="<?php echo WEB_VAULT_DIR.'/'.$file->file_archive_filename; ?>">Download File</a>]</p>

