<?php
$__c->admin()->protect();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
	<title><?php echo SITE_TITLE; ?> -Admin-</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="en-us" />
	<meta name="Copyright" content="Copyright (c) CHNM - Center for History and New Media chnm.gmu.edu" />
	<link rel="stylesheet" href="<?php $_link->style( 'main.css' ); ?>" type="text/css" />
	<?php $_common->javascripts( 'prototype.js', 'scriptaculous.js', 'common.js' ); ?>
</head>
<body>
<div id="page">
	<div id="header">
		<?php include( $_partial->file( 'header' ) ); ?>
	</div>
	<div id="content">
		<?php include( $content_for_layout ); ?>
	</div>
	<div id="footer">
		<p>Developed by <a href="http://chnm.gmu.edu">CHNM</a></p>
	</div>
</div>
</body>
</html>

