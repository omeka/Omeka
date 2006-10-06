<?php
//Layout: default;
$users = $__c->users()->all( 'array' , 'alpha');
?>
<?php include( 'subnav.php' ); ?>
<h2>All Users</h2>
<table id="usertable" summary="A list of users">
<thead>
<tr>
<th scope="col">Username</th>
<th scope="col">Full Name</th>
<th scope="col">Email</th>
<th scope="col">Level</th>
<th scope="col" class="hide">Change Password</th>
</tr>
</thead>
<tbody class="stripe">
	<?php $i = 1; foreach( $users as $user ): ?>
	<tr>
		<td><a href="<?php echo $_link->to( 'users', 'edit' ) . $user['user_id']; ?>"><?php echo $user['user_username']; ?></a></td>
		<td><?php echo $user['user_first_name'].' '.$user['user_last_name']; ?></td>
		<td><?php echo $user['user_email']; ?></td>
		<td><?php echo $user['user_permission_id']; ?></td>
		<td>[<a href="<?php echo $_link->to( 'users', 'password' ) . $user['user_id']; ?>">change password</a>]</td>
	</tr>
	<?php if( $i == 1 ){ $i++; }else{ $i = 1; } endforeach; ?></tbody>
</table>
