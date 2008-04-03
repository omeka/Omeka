<?php head(array('title'=>'Browse Users', 'body_class'=>'users'));?>
<?php common('users-nav'); ?>
<div id="primary">
<h1 class="floater">Browse Users</h1>
<table id="users">
	<thead>
		<tr>
			<th>Username</th>
			<th>Real Name</th>
			<th>Role</th>
			<th>Active?</th>
			<th>Edit</th>
		</tr>
	</thead>
	<tbody>
<?php foreach( $users as $key => $user ): ?>
	<tr class="<?php if($key%2==1) echo 'even'; else echo 'odd'; ?>">
		<td><?php  echo h($user->username); ?></td>
		<td><?php echo h($user->first_name); ?> <?php echo h($user->last_name); ?></td>
		<td><span class="<?php echo h($user->role); ?>"><?php echo h($user->role); ?></span></td>
		
		<td><?php if($user->active):?>Active<?php else: ?>Not active<?php endif;?></td>
		<td><a class="edit" href="<?php echo uri('users/edit/'.$user->id);?>">Edit</a></td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>

<div>
	<form id="new-user-form" action="<?php echo uri('users/add'); ?>" method="post" accept-charset="utf-8">
		<?php common('form', array(), 'users'); ?>
		
		<input type="submit" name="submit" value="Add User" />
	</form>
	
</div>

</div>
<?php foot();?>