<?php self::$_session->saveLocation(); ?>
<?php $user = self::$_session->getUser(); ?>
<?php
	$current = isset( self::$_route['directory'] ) ?
		self::$_route['directory'] :
			( empty( self::$_route['template'] ) ?
			'welcome' :
			self::$_route['template'] ) ;
?>
<div id="<?php echo $current; ?>">
<h1><?php echo SITE_TITLE; ?></h1>

<ul id="navigation">
	<li id="nav-welcome"><a href="<?php echo $_link->to(); ?>">Home</a></li>
	<li id="nav-objects"><a href="<?php echo $_link->to( 'objects' ); ?>">Objects</a></li>
	<li id="nav-categories"><a href="<?php echo $_link->to( 'categories' ); ?>">Categories</a></li>
	<li id="nav-collections"><a href="<?php echo $_link->to( 'collections' ); ?>">Collections</a></li>
	<li id="nav-contributors"><a href="<?php echo $_link->to( 'contributors' ); ?>">Contributors</a></li>
	<li id="nav-tags"><a href="<?php echo $_link->to( 'tags' ); ?>">Tags</a></li>
	<li id="nav-myarchive"><a href="<?php echo $_link->to( 'account' ); ?>">My Archive</a></li>
	<?php if( self::$_session->getUser()->getPermissions() == 1 ): ?>
	<li id="nav-users"><a href="<?php echo $_link->to( 'users' ); ?>">Users</a></li>
	<?php endif; ?>

	<form method="post" action="<?php echo $_link->to( 'logout' ); ?>">
		<button type="submit">Logout</button>
	</form>
</ul>