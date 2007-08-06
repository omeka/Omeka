<?php head();?>
<?php common('users-nav'); ?>

<div id="primary">
<h2>User: <?php echo h($user->first_name); ?> <?php echo h($user->last_name); ?> <a class="edit" href="<?php echo uri('users/edit/'.$user->id); ?>">(Edit)</a></h2>
<dl>
	<dt>Username</dt>
	<dd><?php echo h($user->username); ?></dd>
	<dt>Real Name</dt>
	<dd><?php echo h($user->first_name . ' ' . $user->last_name); ?>
	<dt>Email</dt>
	<dd><?php echo h($user->email); ?></dd>
	<dt>Institution</dt>
	<dd><?php echo h($user->institution); ?></dd>
</dl>
</div>
<?php foot();?>