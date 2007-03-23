<?php head();?>
<h2>Users | Edit a User</h2>
<form method="post">
<?php include('form.php'); ?>
<h3>Change this user's password</h3>
<label for="old_password">Current password:</label><input type="password" name="old_password" id="old_password"/>
<input type="password" name="new_password1" id="new_password1"/>
<input type="password" name="new_password2" id="new_password2"/>

<input type="submit" name="submit" value="Edit this user">
</form>
<?php foot();?>