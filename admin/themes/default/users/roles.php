<?php head(array('title'=>'User Roles'), 'roles'); ?>

<h1 id="message"></h1>

<h3>Groups</h3>
<table>
<?php foreach ($roles as $role): ?>
<tr>
<td><?php echo $role; ?></td>
</tr>
<?php endforeach; ?>
</table>

<h3>Add a New Group</h3>
<div>
<form method="post" action="<?php echo uri('users/addRole');?>">
	<input type="text" name="name"/>
	<input type="submit" name="submit"/>
</form>
<button onclick="getMsg()">GOOBER!</button>
</div>

<?php
foot();
?>