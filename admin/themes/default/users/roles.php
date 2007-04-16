<?php head(array(), 'role'); ?>
<?php print_r($_POST);?>
<h1 id="message"></h1>

<h3>Add a New Role</h3>
<div>
<form method="post" action="<?php echo uri('users/addRole');?>">
	<input type="text" name="name"/>
	<input type="submit" name="submit" value="Add a New Role"/>
</form>
</div>

<form action="<?php echo uri('users/setPermissions'); ?>" method="post">
<h3>Edit / Delete Roles</h3>
<?php select(array('name' => 'role'), $roles); ?>

<ul>
<?php foreach ($permissions as $resource => $resource_permissions): ?>
	<li><h2><?php echo $resource; ?></h2></li>
	<?php foreach ($resource_permissions as $permission): ?>
	<li><?php echo $permission; checkbox(array('name' => 'permissions['.$resource.']['.$permission.']'))?></li>
	<?php endforeach; ?>
<?php endforeach; ?>
</ul>

<input type="submit" value="Add Permissions to Users"/>
<input type="submit" value="Delete Selected User"/ onclick="return confirm('Are you sure you want to delete the selected user?');">
</form>


<?php
foot();
?>