<?php error($user); ?>

<?php text(array('name'=>'username', 'value'=>$user->username), 'Username'); ?>
<?php text(array('name'=>'first_name', 'value'=>$user->first_name), 'First Name'); ?>
<?php text(array('name'=>'last_name', 'value'=>$user->last_name), 'Last Name'); ?>
<?php text(array('name'=>'email', 'value'=>$user->email), 'Email'); ?>
<?php text(array('name'=>'institution', 'value'=>$user->institution), 'Institution'); ?>

<label for="active">Active</label><?php checkbox(array('name'=>'active', 'id'=>'active'), $user->active); ?>
