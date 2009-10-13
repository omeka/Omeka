<?php head(array('title'=>'Add New User', 'content_class' => 'vertical-nav', 'bodyclass'=>'users primary'));?>
<h1>Add a User</h1>

<?php common('settings-nav'); ?>
<div id="primary">
<?php echo flash(); ?>
<form method="post">
<?php include('form.php'); ?>
<input type="submit" name="submit" value="Add this User" />
</form>
</div>
<?php foot();?>