<h1>404 - Development Only</h1>

<h3><?php echo $e->getMessage(); ?></h3>

<h4>Backtrace: <?php Zend::dump($e->getTraceAsString()); ?></h4>
<?php
	
	Zend::dump( $e );

?>