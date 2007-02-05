<?php
/**
 * Abstract master class for extension.
 */
require_once 'Zend/View/Abstract.php';
/**
 * Customized view class
 *
 * @package Sitebuilder
 * @author Kris Kelly
 **/
class Kea_View extends Zend_View_Abstract
{
	public function _run() {
		extract($this->getVars());
		include func_get_arg(0);
	}
} // END class Kea_View

?>