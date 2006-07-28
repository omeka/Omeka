<?php

//$user = $__c->users()->add();

//if ($user) {
//	echo 'ping';
//} else {

	$msg = $__c->users()->login();

	if (self::$_session->getUser())
	{
		if( self::$_session->getValue( 'contribute_form_need_login' ) )
		{
			header( "Location:" . $_link->to('contribute') );
			exit;
		}
		header("Location: ".$_link->to('myarchive'));
		exit;
	}
	
	if (@$_REQUEST['user_add']):
		$msg = $__c->users()->addPublicUser();
	elseif (@$_REQUEST['user_forgot']):
		$msg = $__c->users()->mailNewPassword(@$_REQUEST['user_email']);
	else:
		$__c->users()->login();
		if (self::$_session->getUser()):
			header("Location: ".$_link->to('myarchive'));
			exit;
		endif;
	endif;


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Signin/Signup | Katrina's Jewish Voices</title>
<?php include ('inc/metalinks.php'); ?>

</head>

<body id="login" class="login">
	
	
<a class="hide" href="#content">Skip to Content</a>
<div id="wrap">
	<?php include("inc/header.php"); ?>
	<div id="content">
		<h2>MyArchive | Sigin/Signup</h2>
		<div id="primary">

		<?php if( self::$_session->getValue( 'contribute_form_need_login' ) ): ?>
		<h3>It seems that you may already have an account, if so, please sign in and your contribution will be added to the archive.  Otherwise, please use a different email address as your current choice has been taken by another user.  If you believe there has been a mistake, please contact an administrator.</h3>
		<?php elseif( @$_REQUEST['forgot'] ): ?>
		<h3>Account Information</h3>
		<?php if (!empty($msg)): ?><p class="alert"><?php echo $msg; ?></p><?php else:?><p>Please enter your e-mail address, and we will send you your account information:</p><?php endif; ?>
		<form method="post" id="loginform">
						
				<label for="user_email">E-mail address:</label>
				<input type="text" class="textinput" name="user_email" id="user_email"/>
				<input type="submit" name="user_forgot" class="submitinput" value="Send Information" id="user_forgot"/>
		</form>
		<?php else: ?>
		<h3>Already Have an Account? Log in!</h3>
		<?php if (!empty($msg)): ?><p class="alert"><?php echo $msg; ?></p><?php endif;?>

		<form method="post" action="login">
				<?php if( !empty( $e ) ) echo '<h1 id="error">' . $e->getMessage() . '</h1>'; ?>
				<label for="username">Username</label>
				<input type="text" class="textinput" name="username" id="username"/>
				<label for="password">Password</label>
				<input type="password" class="textinput" name="password" id="password"/>
				<input type="submit" name="user_login" class="submitinput" value="Sign In" id="login_submit"/>
		</form>
		<p>Forgot your username or password? <a href="?forgot=true">Click here</a>!</p>
		<?php endif; ?>
			
	</div> <!-- closes primary div -->
	<?php if( !self::$_session->getValue( 'contribute_form_need_login' ) ): ?>
	<div id="secondary">
		<h3>Need an Account? Sign up!</h3>
		<?php if (@$_REQUEST['user_add']): ?>
			<p class="alert"><?php echo $msg; ?></p>
		<?php else: ?>
		<form method="post" id="signupform">
				<label for="user_name">Choose a Username</label>
				<?php
					$_form->text( array('name' 	=> 'user[user_username]',
					 					'class' => 'textinput',
										'id'	=> 'user_name') );
					$_form->displayError( 'User', 'user_username', $__c->users()->validationErrors() );
				?>
				<label for="user_email">Email address</label>
				<?php
					$_form->text( array('id'	=> 'user_email',
 										'class' => 'textinput',
										'name'	=> 'user[user_email]') );
					$_form->displayError( 'User', 'user_email', $__c->users()->validationErrors() );
				?>
			<input type="submit" name="user_add" id="user_add" value="Sign Up"/>
		</form>
		<?php endif; ?>
		<?php endif; ?>
		</div> <!-- closes secondary div -->
	<div id="tertiary">
		<div id="explanation">
		<h3>Explanation</h3>
		<p>Explanation text about signing up for an account, and logging in to your account. Lorem ipsum dolor sit amet.</p>
		</div> <!-- closes tertiary div -->
	</div> <!-- closes content div -->
	</div>

<?php include("inc/footer.php"); ?>
</div>
</body>
</html>