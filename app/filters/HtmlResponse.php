<?php

class HtmlResponse implements Kea_Filter
{
	private $respondable = array();
	
	private $redirect_to;
	
	public function __construct(array $methods, $redirect_to = null)
	{
		foreach ($methods as $page) {
			$this->respondable[$page] = 1;
		}
	}
	
	public function filter(&$action, $controller)
	{
		if (array_key_exists($action, $this->respondable)
			&& $controller->getResponse() instanceof Kea_Controller_Response_Theme) {
			$controller->getResponse()->setPage($action);
		}
	}
}
?>