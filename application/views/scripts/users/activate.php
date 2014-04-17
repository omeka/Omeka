<?php
$pageTitle = __('User Activation');
echo head(array('title' => $pageTitle), $header);
?>
<h1><?php echo $pageTitle; ?></h1>

<?php echo flash(); ?>
<h2><?php echo html_escape(__('Hello %s. Your username is %s', $user->name , $user->username)); ?></h2>


<form method="post">
    <fieldset>
    <div class="field">
    <?php echo $this->formLabel('new_password1', __('Create a Password')); ?>
        <div class="inputs">
            <input type="password" name="new_password1" id="new_password1" class="textinput" />
        </div>
    </div>
    <div class="field">
        <?php echo $this->formLabel('new_password2', __('Re-type the Password')); ?>
        <div class="inputs">
            <input type="password" name="new_password2" id="new_password2" class="textinput" />
        </div>
    </div>
    </fieldset>
    <div>
    <input type="submit" class="submit" name="submit" value="<?php echo __('Activate'); ?>"/>
    </div>
</form>

<?php echo foot(array(), $footer); ?>
