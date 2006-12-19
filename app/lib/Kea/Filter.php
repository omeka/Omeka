<?php
interface Kea_Filter
{
	public function filter(&$params, Kea_Controller_Action $controller);
}
?>