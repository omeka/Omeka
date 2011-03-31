<?php queue_js('login'); ?>
<?php head(array('bodyclass' => 'login', 'title' => 'Log In'), $header); ?>
<h1>Log In</h1>

<p id="login-links">
<span id="backtosite"><?php echo link_to_home_page('Go to Home Page'); ?></span>  |  <span id="forgotpassword"><?php echo link_to('users', 'forgot-password', 'Lost your password?'); ?></span>
</p>

<?php echo flash(); ?>
    
<?php echo $this->form->setAction($this->url('users/login')); ?>

<?php foot(array(), $footer); ?>
