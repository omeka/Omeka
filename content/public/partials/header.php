<div id="header">

	<div id="meta">	
		<p id="login-blurb"><?php if (self::$_session->getUser()): ?>
		Logged in as <span class="user"><superuser><?php 
	
			if (self::$_session->getUser()->getUserFirstName() && self::$_session->getUser()->getUserLastName())
				echo self::$_session->getUser()->getUserFirstName().' '.self::$_session->getUser()->getUserLastName(); 
			else 
				echo self::$_session->getUser()->getEmail();
		?></span>. <a href="<?php echo $_link->to('logout'); ?>">Logout</a>
		<?php else: ?><a href="<?php echo $_link->to('login'); ?>">Sign up or sign in</a>
		<?php endif; ?></p>
	</div>
	
	<h1 id="title"><a href="<?php echo $_link->to(); ?>">STS Working Archive</a></h1>
	<h1 id="subtitle">A Place to Put Your Stuff While You Work</h1>
<ul id="mainnav"><li id="nav-home"><a href="<?php echo $_link->to(); ?>">Home</a></li><li id="nav-browse"><a href="<?php echo $_link->to('browse'); ?>">Browse</a></li><!--<li id="nav-collections"><a href="<?php echo $_link->to('collection'); ?>">Collections</a></li>--><li id="nav-upload"><a href="<?php echo $_link->to('myarchive'); ?>">My Archive</a></li><li id="nav-about"><a href="<?php echo $_link->to('about'); ?>">About</a></li></ul>
</div>