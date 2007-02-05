<?php

require_once 'Zend/Controller/Action.php';

class GeoController extends Zend_Controller_Action
{
    public function indexAction()
    {
		echo 'Geo!';
    }

    public function noRouteAction()
    {
        $this->_redirect('/');
    }
}

?>