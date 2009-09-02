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
        <?php 
            $firstNameValue = ((!empty($user->first_name)) ? $user->first_name : $_POST['first_name']);
            echo text(array('name'=>'first_name', 'size'=>'30', 'class'=>'textinput', 'id'=>'first_name'), $firstNameValue); 
        ?>
    </div>
    
    <?php echo form_error('first_name'); ?>

</div>

<div class="field">
    <?php echo label('last_name','Last Name'); ?>
    <div class="inputs">
        <?php 
            $lastNameValue = ((!empty($user->last_name)) ? $user->last_name : $_POST['last_name']);
            echo text(array('name'=>'last_name', 'size'=>'30', 'class'=>'textinput', 'id'=>'last_name'), $lastNameValue); 
        ?>
    </div>
    <?php echo form_error('last_name'); ?>
</div>

<div class="field">
    <?php echo label('email','Email'); ?>
    <div class="inputs">
    <?php 
        $emailValue = ((!empty($user->email)) ? $user->email : $_POST['email']);
        echo text(array('name'=>'email', 'class'=>'textinput', 'size'=>'30', 'id'=>'email'), $emailValue); 
    ?>
    </div>
    <?php echo form_error('email'); ?>
</div>

<div class="field">
    <?php echo label('institution','Institution'); ?>
    <div class="inputs">
<?php echo text(array('name'=>'institution', 'size'=>'30','class'=>'textinput', 'id'=>'institution'),not_empty_or($user->institution, $_POST['institution'])); ?>
    </div>
</div>

<?php if ( has_permission('Users','changeRole') ): ?>
    <div class="field">
        <?php echo label('role','Role'); ?>
        <div class="inputs">
    <?php 
        $roleValue = ((!empty($user->role)) ? $user->role : $_POST['role']);
        echo select(array('name'=>'role','id'=>'role'),get_user_roles(), $roleValue); 
    ?>
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
<?php fire_plugin_hook('admin_append_to_users_form', $user); ?>