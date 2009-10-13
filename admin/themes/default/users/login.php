<?php head(array('bodyclass'=>'login'), 'login-header'); ?>

<script type="text/javascript" charset="utf-8">
    Event.observe(window,'load',function() {
        $('username').focus();
    }); 
</script>

<h1>Log In</h1>
<p id="login-links">
<span id="backtosite"><?php echo link_to_home_page('View Public Site'); ?></span>  |  <span id="forgotpassword"><?php echo link_to('users', 'forgot-password', 'Lost your password?'); ?></span>
</p>
    <?php
    if (isset($errorMessage)):
        ?><div class="error">Error: <span>
            
        <?php echo html_escape($errorMessage); ?>
        </span></div>
    <?php endif; ?>
    
<form id="login-form" action="<?php echo html_escape(uri('users/login'));?>" method="post" accept-charset="utf-8">
    <fieldset>
        <div class="field">
    <label for="username">Username</label> 
    <input type="text" name="username" class="textinput" id="username" />
    </div>
    <div class="field">
    <label for="password">Password</label> 
    <input type="password" name="password" class="textinput" id="password" />
    </div>
    
    <div class="field">
        <label for="remember">Remember Me?</label> 
        <?php echo $this->formCheckbox('remember', null, array('class' => 'checkbox')); ?>
    </div>
    </fieldset>
    <div><input type="submit" class="submit-small submit" value="Log In" /></div>
</form>

<?php foot(array(),'login-footer'); ?>