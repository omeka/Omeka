<div id="branding">
	<div class="padding">
	<h1 id="kjv-logo"><a href="<?php echo $_link->to(); ?>">Katrina&#8217;s Jewish Voices</a></h1>
	<img id="menorah" src="<?php echo $_link->in('menorah-sm.jpg','i'); ?>" alt="Image of a broken menorah." />
	<div id="meta">
		<form id="quicksearch" method="GET" action="<?php echo $_link->to('browse'); ?>">
			<label class="hide" for="searchinput">Search</label>
			<input type="text" id="search" class="textinput" name="search" />
			<input type="submit" class="submitinput" id="searchbutton" value="Search" />
		</form>
		<p id="login-blurb"><?php if (self::$_session->getUser()): ?>
		Logged in as <span class="user"><superuser><?php echo self::$_session->getUser()->getUserFirstName().' '.self::$_session->getUser()->getUserLastName(); ?></span>. <a href="<?php echo $_link->to('logout'); ?>">Logout</a>
		<?php else: ?><a href="<?php echo $_link->to('login'); ?>">Sign up or sign in</a>
		<?php endif; ?></p>
	</div>
<ul id="mainnav"><li><a id="nav-home" href="<?php echo $_link->to(); ?>">Home</a></li><li><a id="nav-contribute" href="<?php echo $_link->to('contribute'); ?>">Add Your Voice</a></li><li><a id="nav-browse" href="<?php echo $_link->to('browse'); ?>">Browse</a></li><li><a id="nav-myarchive" href="<?php echo $_link->to('myarchive'); ?>">MyArchive</a></li><li><a id="nav-about" href="<?php echo $_link->to('about'); ?>">About</a></li></ul>
</div>
</div>