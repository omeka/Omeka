<?php 
try{
	$front->dispatch();
}catch(Exception $e) {
	if($config->debug->exceptions) {
		include '404.php';	
	}else {
		$view = new Kea_View(null, array('request'=>$front->getRequest()));
		echo $view->render('404.php');
		exit;		
	}	
}

 ?>
