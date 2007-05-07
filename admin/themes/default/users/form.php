<?php echo flash(); ?>
<?php error($user); ?>
<fieldset>
	<legend>User Info</legend>
<div class="field">
	<?php text(array('name'=>'username', 'class'=>'textinput', 'id'=>'username'),$user->username, 'Username'); ?>
</div>

<div class="field">
	<?php text(array('name'=>'first_name', 'class'=>'textinput', 'id'=>'first_name'),$user->first_name, 'First Name'); ?>
</div>

<div class="field">
	<?php text(array('name'=>'last_name', 'class'=>'textinput', 'id'=>'last_name'),$user->last_name, 'Last Name'); ?>
</div>

<div class="field">
	<?php text(array('name'=>'email', 'class'=>'textinput', 'id'=>'email'),$user->email, 'Email'); ?>
</div>

<div class="field">
<?php text(array('name'=>'institution', 'class'=>'textinput', 'id'=>'institution'),$user->institution, 'Institution'); ?>
</div>

<div class="field">
<label for="active">Active</label>
<?php checkbox(array('name'=>'active', 'id'=>'active'), $user->active); ?>
</div>

<?php if ( has_permission('Users','showRoles') ): ?>
	<div class="field">
	<?php select(array('name'=>'role','id'=>'role'),get_user_roles(), $user->role, 'Choose a Role for this user'); ?>
	</div>
<?php endif; ?>
</fieldset>