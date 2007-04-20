<?php head(array(), 'role');?>
<h1 id="message"></h1>

<h3>Add a New Role</h3>
<form method="post" action="<?php echo uri('users/addRole');?>">
	<input type="text" name="name"/>
	<input type="submit" name="submit" value="Add a New Role"/>
</form>

<form action="<?php echo uri('users/deleteRole'); ?>" method="post">
<h3>Delete Roles</h3>
<?php select(array('name' => 'role'), $roles); ?>
<input type="submit" value="Delete Selected User" onclick="return confirm('Are you sure you want to delete the selected user?');">
</form>

<br/>
<h3>Alter Permissions</h3>
<form action="" method="post">
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
</form>


<?php foot();?>