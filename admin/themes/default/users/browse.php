<?php head();?>
<?php common('users-nav'); ?>
<div id="primary">

<h1>Browse Users</h1>
<div id="browse-users">
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
		<td><?php  echo $user->username; ?></td>
		<td><?php echo $user->first_name; ?> <?php echo $user->last_name; ?></td>
		<td><?php echo $user->email; ?></td>
		<td><span class="<?php echo $user->role; ?>"><?php echo $user->role; ?></span></td>
		
		<td><?php if($user->active):?>Active<?php else: ?>Not active<?php endif;?></td>
		<td><a class="edit-user" href="<?php echo uri('users/edit/'.$user->id);?>">Edit</a></td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php foot();?>