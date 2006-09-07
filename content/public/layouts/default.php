<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	
<title><?php echo INFO_TITLE; ?></title>

<!-- Meta elements -->
<?php include( $_partial->file( 'metalinks' ) ); ?>

<!-- Stylesheets -->
<?php include( $_partial->file( 'stylesheets' ) ); ?>

<!-- JavaScripts -->
<?php include( $_partial->file( 'javascripts' ) ); ?>

</head>
<body id="<?php if(self::$_route['template'] == '') {echo 'home';} elseif (preg_match( "/my/" , self::$_route['template'] ) || self::$_route['template'] == "add") { echo "myarchive"; } else { echo (self::$_route['template']); } ?>">

<div id="wrap">
	<div id="content">
		<?php include( $_partial->file( 'header' ) ); ?>
		<div id="shell">
		<?php include( $content_for_layout ); ?>
		</div>
	</div>
	<?php include( $_partial->file( 'footer' ) ); ?>
</div>
</body>
</html>