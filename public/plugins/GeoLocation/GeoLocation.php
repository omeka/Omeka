<?php
/**
 * 
 *
 * @package Sitebuilder
 * @author Kris Kelly
 **/
class GeoLocation extends Kea_Plugin
{
	protected $defaults = array(
		'Latitude' => 50, 
		'Longitude' => 70, 
		'ZoomLevel' => 5, 
		'Maps Key' => 'ABQIAAAAD-SKaHlA87rO8jrVjT7SHBQ22YnqeXddIs-jHkCCm8C4K5z8GBTo29raXitwn3YbLGstzhF1Yn4Ctg');
	
	public function defineRoutes()
	{
		$this->router->addRoute(
			'geoindex',
			new Zend_Controller_Router_Route('geolocation', array('controller' => 'geo', 'action' => 'index'))
		);
		$this->router->addRoute(
			'itemsgeo',
			new Zend_Controller_Router_Route('items/geolocation', array('controller' => 'geo', 'action' => 'index'))
		);
	}
	
	public function routeStartup()
    {
//        $this->getResponse()->appendBody('<p>routeStartup() called</p>');
    }

    public function routeShutdown($request)
    {
//        $this->getResponse()->appendBody('<p>routeShutdown() called</p>');
    }

    public function dispatchLoopStartup($request)
    {
//        $this->getResponse()->appendBody('<p>dispatchLoopStartup() called</p>');
    }

    public function preDispatch($request)
    {
//        $this->getResponse()->appendBody('<p>preDispatch() called</p>');
    }

    public function postDispatch($request)
    {
//        $this->getResponse()->appendBody('<p>postDispatch() called</p>');
    }

    public function dispatchLoopShutdown()
    {
//        $this->getResponse()->appendBody('<p>dispatchLoopShutdown() called</p>');
    }
	
	public function onCreate(Doctrine_Record $record) {
//		$this->insertAfter('Created an instance of '.get_class($record), 'called</p>');
	}
} // END class FooPlugin

?>
