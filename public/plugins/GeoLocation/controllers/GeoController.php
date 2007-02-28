<?php

require_once 'Kea/Controller/Action.php';
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Metafield.php';

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
		//Add a bunch of filtering so that you can filter by collection, etc.
		$query = new Doctrine_Query();
		$query->from('Item i')->innerJoin('i.Metatext mt')->innerJoin('mt.Metafield mf')->where("mf.name = 'Map Latitude' AND mt.text != ''");
		$items = $query->execute();
		$this->render('map/browse.php', compact('items'));
	}
}

?>