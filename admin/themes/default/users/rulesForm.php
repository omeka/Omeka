<form method="post" name="roleRules" action="<?php echo uri('users/setPermissions'); ?>">
<input type="hidden" name="role" value="<?php echo $role; ?>" />
<ul>
<?php foreach ($permissions as $resource => $resource_permissions): ?>
	<li><h2><?php echo $resource; ?></h2></li>
	<?php foreach ($resource_permissions as $permission): 
		if ($acl->isAllowed($role, $resource, $permission)): ?>
	<li><?php echo $permission; checkbox(array('name' => 'permissions['.$resource.']['.$permission.']', 'checked' => 'checked'));?></li>
	<?php else: ?>
		<li><?php echo $permission; checkbox(array('name' => 'permissions['.$resource.']['.$permission.']'));?></li>
	<?php endif; ?>
	<?php endforeach; ?>
<?php endforeach; ?>
</ul>
<input type="submit" value="Set Permissions"/>
</form>