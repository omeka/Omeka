<?php 
try{
	$front->dispatch();
}
/**
 *	Hack for static pages: if the route is messed up, attempt to render the static page
 */
catch(Zend_Exception $ze) {
	$req = $front->getRequest();
	
	$page = $req->getActionName();
	$dir = $req->getControllerName();
	
	$req->setControllerName('index');
	$req->setParam('page', $page);
	$req->setParam('dir', $dir);
	
	$front->setRequest($req);
	
	try{
		$front->dispatch();
	}catch(Exception $e) {
		render_404($e, $config->debug->exceptions);
	}
	
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