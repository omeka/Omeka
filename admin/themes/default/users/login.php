<?php head(array('bodyclass'=>'login'), 'login-header'); ?>
<?php echo js('login'); ?>
<h1><?php echo __('Log In'); ?></h1>
<p id="login-links">
<span id="backtosite"><?php echo link_to_home_page(__('View Public Site')); ?></span>  |  <span id="forgotpassword"><?php echo link_to('users', 'forgot-password', __('Lost your password?')); ?></span>
</p>
    <?php
    if (isset($errorMessage)):
        ?><div class="error"><?php echo __('Error'); ?>: <span>
            
        <?php echo html_escape($errorMessage); ?>
        </span></div>
    <?php endif; ?>
    
<?php echo $this->form->setAction($this->url('users/login')); ?>

<?php foot(array(),'login-footer'); ?>