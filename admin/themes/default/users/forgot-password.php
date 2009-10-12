<?php head(array('bodyclass'=>'login'), 'login-header'); ?>
<h1>Forgot Password</h1>
<p id="login-links">
<span id="backtologin"><?php echo link_to('users', 'login', 'Back to Log In'); ?></span>
</p>

<p class="clear">Enter your email address to retrieve your password.</p>
<?php echo flash(); ?>
<form method="post" accept-charset="utf-8">
    <div class="field">        
        <label for="email">Email</label>
        <?php echo $this->formText('email', @$_POST['email'], array('class'=>'textinput')); ?>
    </div>

    <input type="submit" class="submit submit-small" value="Submit" />
</form>
<?php foot(array(), 'login-footer'); ?>