<?php head(array('title'=>'Add New User', 'content_class' => 'vertical-nav', 'bodyclass'=>'users primary'));?>
<h1>Add a User</h1>

<?php common('settings-nav'); ?>
<div id="primary">
<?php echo flash(); ?>
<?php echo $userForm; ?>
</div>
<?php foot();?>
