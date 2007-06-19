<?php
if($_REQUEST['moreinfo']) {
	Zend::dump( $e );
}
?>


<h1>404 - Development Only</h1>

<h3><?php echo get_class($e) . ": ". $e->getMessage(); ?></h3>

<?php
if($e instanceof Doctrine_Validator_Exception):
	foreach ($e->getInvalidRecords() as $key => $record):
		echo get_class($record).": ".$record->getErrorMsg();
	endforeach;
endif;
?>


<h4>Backtrace: <?php Zend::dump($e->getTraceAsString()); ?></h4>

<form id="error-details">

	<input type="checkbox" name="moreinfo" value="more" />
	<input type="submit" name="submit" value="Dump Error" />
</form>

