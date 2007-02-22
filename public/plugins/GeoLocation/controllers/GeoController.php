<?php

require_once 'Kea/Controller/Action.php';

class GeoController extends Kea_Controller_Action
{
    public function indexAction()
    {
		echo 'Geo!';
    }

    public function noRouteAction()
    {
        $this->_redirect('/');
    }

	public function browseAction()
	{
		
	}
}

?>