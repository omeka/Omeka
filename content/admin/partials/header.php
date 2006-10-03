<?php self::$_session->saveLocation(); ?>
<?php $user = self::$_session->getUser(); ?>
<?php
	$current = isset( self::$_route['directory'] ) ?
		self::$_route['directory'] :
			( empty( self::$_route['template'] ) ?
			'welcome' :
			self::$_route['template'] ) ;
?>
<h1><?php echo SITE_TITLE; ?></h1>

<ul id="navigation">
	<li id="nav-welcome"<?php if(self::$_route['template'] == '') {echo ' class="current"';} ?>><a href="<?php echo $_link->to(); ?>">Home</a></li>
	<li id="nav-items"<?php if($current == 'items' || $current == 'files') { echo ' class="current"';} ?>><a href="<?php echo $_link->to( 'items' ); ?>">Items</a></li>
	<li id="nav-types"<?php if($current == 'types') {echo ' class="current"';} ?>><a href="<?php echo $_link->to( 'types' ); ?>">Types</a></li>
	<li id="nav-collections"<?php if($current == 'collections') {echo ' class="current"';} ?>><a href="<?php echo $_link->to( 'collections' ); ?>">Collections</a></li>
	<li id="nav-tags"<?php if($current == 'tags') {echo ' class="current"';} ?>><a href="<?php echo $_link->to( 'tags' ); ?>">Tags</a></li>
	<li id="nav-myarchive"<?php if($current == 'account') {echo ' class="current"';} ?>><a href="<?php echo $_link->to( 'account' ); ?>">My Archive</a></li>
	<?php if( self::$_session->getUser()->getPermissions() == 1 ): ?>
	<li id="nav-people"<?php if($current == 'users') {echo ' class="current"';} ?>><a href="<?php echo $_link->to( 'users' ); ?>">People</a></li>
		<li id="nav-settings"<?php if($current == 'settings') {echo ' class="current"';} ?>><a href="<?php echo $_link->to( 'settings' ); ?>">Settings</a></li>
	<?php endif; ?>
</ul>