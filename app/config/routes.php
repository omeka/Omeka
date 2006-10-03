<?php

Kea_Template_Router::connect(
	'/:admin/items/:template/*',
	array( 'template' => 'all', 'directory' => 'items' ) );

Kea_Template_Router::connect(
	'/:admin/types/:template/*',
	array( 'template' => 'all', 'directory' => 'types' ) );

Kea_Template_Router::connect(
	'/:admin/collections/:template/*',
	array( 'template' => 'all', 'directory' => 'collections' ) );

Kea_Template_Router::connect(
	'/:admin/contributors/:template/*',
	array( 'template' => 'all', 'directory' => 'contributors' ) );
	
Kea_Template_Router::connect(
	'/:admin/users/:template/*',
	array( 'template' => 'all', 'directory' => 'users' ) );
	
Kea_Template_Router::connect(
	'/:admin/account/:template/*',
	array( 'template' => 'index', 'directory' => 'account' ) );

Kea_Template_Router::connect(
	'/tags/*',
	array( 'template' => 'tags' ) );
	
Kea_Template_Router::connect(
	'/:admin/tags/:template/*',
	array( 'template' => 'cloud', 'directory' => 'tags' ) );
	


Kea_Template_Router::connect(
	'/:admin/files/:template/*',
	array( 'template' => 'show', 'directory' => 'files' ) );

?>