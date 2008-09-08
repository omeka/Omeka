<?php head(array('title'=>'Browse Users', 'content_class' => 'vertical-nav', 'body_class'=>'users primary'));?>
<h1>Users</h1>
<?php common('settings-nav'); ?>

<div id="primary">
<h2>Current Users</h2>
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
    <h2>Add a User</h2>
	<form id="new-user-form" action="<?php echo uri('users/add'); ?>" method="post" accept-charset="utf-8">
		<?php common('form', array(), 'users'); ?>
		
		<input type="submit" name="submit" class="submit submit-medium" value="Add User" />
	</form>
	
</div>

</div>
<?php foot();?>