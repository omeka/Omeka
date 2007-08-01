<?php echo flash(); ?>
<fieldset>
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

<?php if ( has_permission('super') ): ?>
	<div class="field">
<div class="label">Active</div>
<div class="radio">
<?php radio(array('name'=>'active', 'id'=>'active'), array('0'=>'Inactive','1'=>'Active'), $user->active); ?>
</div>
</div>
<?php endif; ?>

<?php if ( has_permission('Users','showRoles') ): ?>
	<div class="field">
	<?php select(array('name'=>'role','id'=>'role'),get_user_roles(), $user->role, 'Choose a Role for this user'); ?>
	</div>
<?php endif; ?>

</fieldset>