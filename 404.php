<?php
if($_REQUEST['moreinfo']) {
	Zend::dump( $e );
}
?>


<h1>404 - Development Only</h1>

<h3><?php echo $e->getMessage(); ?></h3>

<h4>Backtrace: <?php Zend::dump($e->getTraceAsString()); ?></h4>

<form id="error-details">

	<input type="checkbox" name="moreinfo" value="more" />
	<input type="submit" name="submit" value="Dump Error" />
</form>