<?php

// Layout: default;

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


		<h2>MyArchive | Sigin/Signup</h2>

			<div id="explanation">
<!--			<p>Sign up for an account to do more than browse (upload, bookmark, <a href="<?php echo $_link->to('whataretags'); ?>" class="popup">tag</a>, etc.). Questions? <a  href="<?php echo $_link->to('contact'); ?>">Contact us!</a></p>
			</div> -->
			
			<div id="primary" <?php if (self::$_session->getValue( 'contribute_form_need_login' )) echo 'class="wide"'; ?> >




		<?php if( @$_REQUEST['forgot'] ): ?>
			<?php if (!empty($msg)): ?><p class="alert"><?php echo $msg; ?></p><?php endif; ?>
			<h3>Password recovery</h3>
			<p>Please enter your e-mail address, and we will send you your account information:</p>
			<form method="post" id="loginform" action="<?php echo $_link->to('login'); ?>">			
					<label for="user_email">E-mail address:</label>
					<input type="text" class="textinput" name="user_email" id="user_email"/>
					<input type="submit" name="user_forgot" class="submitinput" value="Send Information" id="user_forgot"/>
			</form>
		<?php else: ?>
		
			
			
			<?php if( self::$_session->getValue( 'contribute_form_need_login' ) ): ?>
				<?php if (!empty($msg)): ?><p class="alert"><?php echo $msg; ?></p><?php endif; ?><h3>You Already Have an Account!</h3>
				<p>It seems that you may already have an account. If so, please sign in and your contribution will be added to the archive. Otherwise, please use a different email address as your current choice has been taken by another user. If you believe there has been a mistake, please <a href="<?php echo $_link->to('contact'); ?>">contact us</a>.</p>
				<form method="post" action="login">
						<?php //if( !empty( $e ) ) echo '<h1 id="error">' . $e->getMessage() . '</h1>'; ?>
						<label for="username">Username</label>
						<input type="text" class="textinput" name="username" id="username"/>
						<label for="password">Password</label>
						<input type="password" class="textinput" name="password" id="password"/>
						<input type="submit" name="user_login" class="submitinput" value="Sign In" id="login_submit"/>
				</form>
				<p>Forgot your username or password? <a href="?forgot=true">Click here</a>!</p>

			<?php else: ?>
				<?php if (@$_REQUEST['user_login']): ?><p class="alert"><?php echo $msg; ?></p><?php endif;?>
				<h3>Already Have an Account? Log in!</h3>
				<form method="post" action="login">
						<?php //if( !empty( $e ) ) echo '<h1 id="error">' . $e->getMessage() . '</h1>'; ?>
						<label for="username">Username</label>
						<input type="text" class="textinput" name="username" id="username"/>
						<label for="password">Password</label>
						<input type="password" class="textinput" name="password" id="password"/>
						<input type="submit" name="user_login" class="submitinput" value="Sign In" id="login_submit"/>
				</form>
				<p>Forgot your username or password? <a href="?forgot=true">Click here</a>!</p>
			<?php endif; ?>

		<?php endif; ?>

			
	</div> <!-- closes primary div -->


	<?php if( !self::$_session->getValue( 'contribute_form_need_login' )): ?>
	<div id="secondary">		
		<h3>Need an Account? Sign up!</h3>
		<?php if (@$_REQUEST['user_add']): ?><p class="alert"><?php echo $msg; ?></p><?php endif;?>
		<form method="post" id="signupform" action="<?php echo $_link->to('login'); ?>">
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
		</div> <!-- closes secondary div -->
		
	<?php endif; ?>
	