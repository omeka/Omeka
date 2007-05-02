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
<input type="submit" value="Delete The Selected Role" onclick="return confirm('Are you sure you want to delete the selected role?');">
</form>

<br/>
<h3>Alter Role Permissions</h3>
<?php select(array('name' => 'role', 'id'=>'alter_role'), $roles); ?>
<div id="rulesForm"></div>
<?php foot();?>