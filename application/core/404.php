<?php
header("HTTP/1.0 404 Not Found");

if($_REQUEST['moreinfo']) {
	Zend_Debug::dump( $e );
}
?>


<h1>404 - Development Only</h1>

<h3><?php echo get_class($e) . ": ". $e->getMessage(); ?></h3>


<h4>Backtrace: <?php Zend_Debug::dump($e->getTraceAsString()); ?></h4>

<form id="error-details">

	<input type="checkbox" name="moreinfo" value="more" />
	<input type="submit" name="submit" value="Dump Error" />
</form>

