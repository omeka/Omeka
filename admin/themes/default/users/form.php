<?php if(!isset($user)) {
	$user = new User;
	$user->setArray($_POST);
} 
?>

<?php echo flash(); ?>
<fieldset>
<div class="field">
	<?php echo label('username','User Name'); ?>
	<div class="inputs">
	<?php echo text(array('name'=>'username', 'class'=>'textinput', 'size'=>'30','id'=>'username'),$user->username); ?>
	</div>
	<?php echo form_error('username'); ?>
</div>

<div class="field">
	<?php echo label('first_name','First Name'); ?>
	
	<div class="inputs">	
		<?php echo text(array('name'=>'first_name', 'size'=>'30', 'class'=>'textinput', 'id'=>'first_name'),not_empty_or($user->first_name, $_POST['first_name'])); ?>
	</div>
	
	<?php echo form_error('first_name'); ?>

</div>

<div class="field">
	<?php echo label('last_name','Last Name'); ?>
	<div class="inputs">
		<?php echo text(array('name'=>'last_name', 'size'=>'30', 'class'=>'textinput', 'id'=>'last_name'),not_empty_or($user->last_name, $_POST['last_name'])); ?>
	</div>
	<?php echo form_error('last_name'); ?>
</div>

<div class="field">
	<?php echo label('email','Email'); ?>
	<div class="inputs">
	<?php echo text(array('name'=>'email', 'class'=>'textinput', 'size'=>'30', 'id'=>'email'), not_empty_or($user->email, $_POST['email'])); ?>
	</div>
	<?php echo form_error('email'); ?>
</div>

<div class="field">
	<?php echo label('institution','Institution'); ?>
	<div class="inputs">
<?php echo text(array('name'=>'institution', 'size'=>'30','class'=>'textinput', 'id'=>'institution'),not_empty_or($user->institution, $_POST['institution'])); ?>
	</div>
</div>

<?php if ( has_permission('Users','showRoles') ): ?>
	<div class="field">
		<?php echo label('role','Role'); ?>
		<div class="inputs">
	<?php echo select(array('name'=>'role','id'=>'role'),get_user_roles(), not_empty_or($user->role, $_POST['role'])); ?>
	</div>
	<?php echo form_error('role'); ?>
	</div>
<?php endif; ?>

<?php if ( has_permission('super') ): ?>
	<div class="field">
		<div class="label">Activity</div>
<div class="inputs radio">
<?php echo radio(array('name'=>'active', 'id'=>'active'), array('0'=>'Inactive','1'=>'Active'), $user->active); ?>
</div>
</div>
<?php endif; ?>

</fieldset>