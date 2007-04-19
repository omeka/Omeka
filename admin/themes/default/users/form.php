<?php error($user); ?>

<?php text(array('name'=>'username', 'id'=>'username'),$user->username, 'Username'); ?>
<?php text(array('name'=>'first_name', 'id'=>'first_name'),$user->first_name, 'First Name'); ?>
<?php text(array('name'=>'last_name', 'id'=>'last_name'),$user->last_name, 'Last Name'); ?>
<?php text(array('name'=>'email', 'id'=>'email'),$user->email, 'Email'); ?>
<?php text(array('name'=>'institution', 'id'=>'institution'),$user->institution, 'Institution'); ?>

<label for="active">Active</label><?php checkbox(array('name'=>'active', 'id'=>'active'), $user->active); ?>
