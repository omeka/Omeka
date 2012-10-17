<?php
queue_js_file('login');
$pageTitle = __('Log In');
echo head(array('bodyclass' => 'login', 'title' => $pageTitle), $header);
?>
<h1>Omeka</h1>

<h2><?php echo link_to_admin_home_page(); ?></h2>

<?php echo flash(); ?>

<div class="eight columns alpha offset-by-one">
<?php echo $this->form->setAction($this->url('users/login')); ?>
</div>    

<p id="forgotpassword">
<?php echo link_to('users', 'forgot-password', __('(Lost your password?)')); ?>
</p>

<?php echo foot(array(), $footer); ?>