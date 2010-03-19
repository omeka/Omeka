<?php head(array('bodyclass'=>'login')); ?>

<script type="text/javascript" charset="utf-8">
    Event.observe(window,'load',function() {
        if ($('username')) {
            $('username').focus();
        }
    }); 
</script>

<h1>Log In</h1>
<p id="login-links">
<span id="forgotpassword"><?php echo link_to('users', 'forgot-password', 'Lost your password?'); ?></span>
</p>
<?php echo flash(); ?>
<?php echo $this->form->setAction($this->url('users/login')); ?>
<?php foot(); ?>
