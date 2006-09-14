<?php
$msg = $__c->users()->login();
$__c->admin()->protect();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?php echo SITE_TITLE; ?> -Admin-</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="Content-Language" content="en-us" />
	<meta name="Copyright" content="Copyright (c) CHNM - Center for History and New Media chnm.gmu.edu" />
	<link rel="stylesheet" href="<?php $_link->style( 'login.css' ); ?>" type="text/css" />
</head>
<body>
<div id="topspacer"></div>
<div id="login">
	<?php if( !$user = self::$_session->getUser() ): ?>
	<form method="post" action="login">
		<fieldset id="login_form">
			<legend>Login</legend>
			<?php if ($msg): ?><h1 class="error"><?php echo $msg;?></h1><?php endif;?>
			<label for="username">Username</label>
			<input type="text" name="username" id="username"/>
			<label for="password">Password</label>
			<input type="password" name="password" id="password"/>
			<input type="submit" name="user_login" value="Login" id="login_submit"/>
		</fieldset>
		<p style="text-align:center;">If you have trouble logging in, please <a href="mailto:">contact us</a></p>
	</form>
	<?php else: ?>
	<form method="post" action="<?php echo $_link->to('logout'); ?>">
		<fieldset id="login_form">
			<legend>Login</legend>
			<h1>Currently logged in as: <?php echo $user->getUsername() ?></h1>
			<label for="logout">Please logout and sign in as an authorized user.</label>
			<input type="submit" name="logout" value="Logout"/>
		</fieldset>
	</form>
	<?php endif; ?>
</div>
</body>
</html>