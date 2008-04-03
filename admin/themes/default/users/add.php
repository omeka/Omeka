<?php head();?>
<?php common('users-nav'); ?>

<div id="primary">
<h1>Add a User</h1>
<form method="post">
<?php include('form.php'); ?>
<input type="submit" name="submit" value="Add this User"/>
</form>
</div>
<?php foot();?>