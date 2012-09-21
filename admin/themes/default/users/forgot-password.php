<?php
$pageTitle = __('Forgot Password');
echo head(array('title' => $pageTitle, 'bodyclass' => 'login'), $header);
?>
<h1><?php echo $pageTitle; ?></h1>

<h2><?php echo link_to_admin_home_page(); ?></h2>

<?php echo flash(); ?>

<p><?php echo __('Enter your email address to retrieve your password.'); ?></p>

<div class="eight columns alpha offset-by-one">
<form method="post" accept-charset="utf-8">
    <div class="field">    
        <div class="inputs six columns offset-by-one omega">
            <?php echo $this->formText('email', @$_POST['email']); ?>
        </div>
    </div>

    <input type="submit" class="submit" value="<?php echo __('Submit'); ?>" />
</form>

<p id="login-links">
<span id="backtologin"><?php echo link_to('users', 'login', __('Back to Log In')); ?></span>
</p>
</div>
<?php echo foot(array(), $footer); ?>
