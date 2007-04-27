<?php head();?>
<h2>Users</h2>
<table id="users">
	<thead>
		<tr>
			<th>Username</th>
			<th>Real Name</th>
			<th>Email</th>
			<th>Institution</th>
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
		<td><?php echo $user->institution; ?></td>
		
		<td><?php if($user->active):?>Active<?php else: ?>Not active<?php endif;?></td>
		<td><a href="<?php echo uri('users/edit/'.$user->id);?>">[Edit]</a></td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php foot();?>