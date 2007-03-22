<?php head();?>
<h2>Users</h2>
<table id="users">
<?php foreach( $users as $key => $user ): ?>
	<tr>
		<td><?php  echo $user->username; ?></td>
		<td><?php if($user->active):?>Active<?php else: ?>Not active<?php endif;?></td>
		<td><a href="<?php echo uri('users/edit/'.$user->id);?>">[Edit]</a></td>
	</tr>
<?php endforeach; ?>
</table>
<?php foot();?>