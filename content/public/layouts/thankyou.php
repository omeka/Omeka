<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	
<title><?php echo SITE_TITLE; ?></title>

<!-- Meta elements -->
<?php include( $_partial->file( 'metalinks' ) ); ?>

<!-- Stylesheets -->
<?php include( $_partial->file( 'stylesheets' ) ); ?>

<!-- JavaScripts -->
<?php include( $_partial->file( 'javascripts' ) ); ?>

</head>
<body id="thankyou">
<?php if (@$_REQUEST['success']!='true'): ?><div style="display:none;"><a class="lbauto" href="<?php echo $_link->to('contributemore'); ?>">Contribute More</a></div><?php endif; ?>
<div id="wrap">
	<?php include( $_partial->file( 'header' ) ); ?>
	<div id="content">
		<?php include( $content_for_layout ); ?>
	</div>
	<?php include( $_partial->file( 'footer' ) ); ?>
</div>
</body>
</html>