<?php head();?>
<?php common('users-nav'); ?>
<div id="primary">

<h1>Browse Users</h1>

<table id="users">
	<thead>
		<tr>
			<th>Username</th>
			<th>Real Name</th>
			<th>Email</th>
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
		<td><?php echo h($user->email); ?></td>
		<td><span class="<?php echo h($user->role); ?>"><?php echo h($user->role); ?></span></td>
		
		<td><?php if($user->active):?>Active<?php else: ?>Not active<?php endif;?></td>
		<td><a href="<?php echo uri('users/edit/'.$user->id);?>">[Edit]</a></td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php foot();?>