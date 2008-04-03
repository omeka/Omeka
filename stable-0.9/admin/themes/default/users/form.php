<?php if(!isset($user)) {
	$user = new User;
	$user->setArray($_POST);
} 
?>

<?php echo flash(); ?>
<fieldset>
<div class="field">
	<?php text(array('name'=>'username', 'class'=>'textinput', 'id'=>'username'),$user->username, 'Username'); ?>
	<?php echo form_error('username'); ?>
</div>

<div class="field">
	<?php text(array('name'=>'first_name', 'class'=>'textinput', 'id'=>'first_name'),not_empty_or($user->first_name, $_POST['first_name']), 'First Name'); ?>
	<?php echo form_error('first_name'); ?>
</div>

<div class="field">
	<?php text(array('name'=>'last_name', 'class'=>'textinput', 'id'=>'last_name'),not_empty_or($user->last_name, $_POST['last_name']), 'Last Name'); ?>
	<?php echo form_error('last_name'); ?>
</div>

<div class="field">
	<?php text(array('name'=>'email', 'class'=>'textinput', 'id'=>'email'), not_empty_or($user->email, $_POST['email']), 'Email'); ?>
	<?php echo form_error('email'); ?>
</div>

<div class="field">
<?php text(array('name'=>'institution', 'class'=>'textinput', 'id'=>'institution'),not_empty_or($user->institution, $_POST['institution']), 'Institution'); ?>
</div>

<?php if ( has_permission('Users','showRoles') ): ?>
	<div class="field">
	<?php select(array('name'=>'role','id'=>'role'),get_user_roles(), not_empty_or($user->role, $_POST['role']), 'Role'); ?>
	<?php echo form_error('role'); ?>
	</div>
<?php endif; ?>

<?php if ( has_permission('super') ): ?>
	<div class="field">
<div class="radio">
<?php radio(array('name'=>'active', 'id'=>'active'), array('0'=>'Inactive','1'=>'Active'), $user->active); ?>
</div>
</div>
<?php endif; ?>

</fieldset>