<?php

require_once 'Zend/Controller/Action.php';

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
      echo 'index/index';
      /*
		Zend::loadClass('Zend_View');
		$view = new Zend_View;
		$view->setScriptPath(BASE_DIR.'/application/views');
	
		require_once BASE_DIR.'/application/models/Collection.php';
		
		$conn = Doctrine_Manager::connection();
		$t = $conn->getTable('Collection');
		$m = $t->find(1);
		
        $data = array('collections' => $m);
		$view->data = $data;
		
		echo $view->render('Index.php');
		*/
    }

    public function noRouteAction()
    {
        $this->_redirect('/');
    }
}

?>