<?php
//Layout: default;
$users = $__c->users()->all( 'array' , 'alpha');
?>
<?php include( 'subnav.php' ); ?>

<style type="text/css" media="screen">
/* <![CDATA[ */
	.user-1 {background-color: #eee; padding:8px;}
	.user-2 {background-color: #fff; padding:8px;}
	#usertable {width: 660px; margin: 10px auto; font-size: 1.2em;}
	#usertable th {text-align:left;padding: 10px;}
	#usertable thead {padding: 10px 0; background: #FFF09E;}
	#usertable td {text-align:left; padding: 10px; border-bottom: 1px solid #fff;}
	#usertable tbody tr { background: #f3f3f3;padding: 10px 0;}
/* ]]> */
</style>

<br/>

<table id="usertable" summary="A list of users for Katrina's Jewish Voices.">
<thead>
<tr>
<th scope="col">Username</th>
<th scope="col">Full Name</th>
<th scope="col">Affiliation</th>
<th scope="col">Email</th>
<th scope="col">Level</th>
<th scope="col" class="hide">Edit User</th>
<th scope="col" class="hide">Change Password</th>
</tr>
</thead>
<tbody class="stripe">
	<?php $i = 1; foreach( $users as $user ): ?>
	<tr>
		<td><?php echo $user['user_username']; ?></td>
		<td><?php echo $user['user_first_name'].' '.$user['user_last_name']; ?></td>
		<td><?php echo $user['user_institution']; ?></td>
		<td><?php echo $user['user_email']; ?></td>
		<td><?php echo $user['user_permission_id']; ?></td>
		<td>[<a href="<?php echo $_link->to( 'users', 'edit' ) . $user['user_id']; ?>">edit</a>]</td>
		<td>[<a href="<?php echo $_link->to( 'users', 'password' ) . $user['user_id']; ?>">change password</a>]</td>
	</tr>
	<?php if( $i == 1 ){ $i++; }else{ $i = 1; } endforeach; ?></tbody>
</table>
