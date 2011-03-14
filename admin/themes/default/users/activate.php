<?php head(array(), 'login-header'); ?>
<h1><?php echo __('User Activation'); ?></h1>

<?php echo flash(); ?>
<h2><?php echo __('Hello, %s', $user->username); ?></h2>

<form method="post">
    <fieldset>
    <div class="field">
        <?php echo label('new_password1', __('Create a Password')); ?>
        <div class="inputs">
            <input type="password" name="new_password1" id="new_password1" class="textinput" />
        </div>
    </div>
    <div class="field">
        <?php echo label('new_password2', __('Re-type your Password')); ?>
        <div class="inputs">
            <input type="password" name="new_password2" id="new_password2" class="textinput" />
        </div>
    </div>
    </fieldset>
    <div>
        <input type="submit" class="submit submit-medium" name="submit" value="<?php echo __('Activate'); ?>"/>
    </div>
</form>

<?php foot(array(), 'login-footer'); ?>
