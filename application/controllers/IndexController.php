<?php
/**
 * @package Sitebuilder
 * @author Nate Agrin
 **/
require_once 'Zend/Controller/Action.php';
class IndexController extends Zend_Controller_Action
{
	/**
	 * This allows for GET style routing.
	 * DO NOT EDIT THIS UNLESS THERE IS A BUG, OR YOU KNOW WHAT YOU ARE DOING
	 * @author Nate Agrin
	 */
    public function indexAction()
    {
		$req = $this->getRequest();
		
		$c = $req->getParam('c');
		$a = $req->getParam('a');
		$admin = (boolean) $req->getParam('admin');

		if (!$c) {
			// Assume that they want to go to the default location
			$this->_forward('items', 'browse');
		}
		
		if ($admin && $c) {
			$this->_forward('admin', 'index');
			return;
		}
		
		if ($c) {
			if ($a) {
				$this->_forward($c, $a);
				return;
			}
			else {
				$this->_forward($c, 'index');
				return;
			}
		}
    }

    public function noRouteAction()
    {
        $this->_redirect('/');
    }
}
?>