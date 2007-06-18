<?php 
try{
	$front->dispatch();
}
catch(Exception $e) {
	render_404($e, $config->debug->exceptions);
}

function render_404($e, $debugExceptions = false) {
	if($debugExceptions) {
		include '404.php';
		exit;	
	}else {
		$front = Kea_Controller_Front::getInstance();
		$view = new Kea_View(null, array('request'=>$front->getRequest()));
		echo $view->render('404.php');
		exit;		
	}	
}
?>