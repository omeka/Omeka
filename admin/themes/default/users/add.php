<?php head();?>
<?php common('settings-nav'); ?>
<h1>Add a User</h1>
<div id="primary">
<?php echo flash(); ?>
<form method="post">
<?php include('form.php'); ?>
<input type="submit" name="submit" value="Add this User"/>
</form>
</div>
<?php foot();?>