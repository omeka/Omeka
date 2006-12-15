<?php
/**
	Why do we need a resolver you might ask?
	Well mostly because we need to model the behavior of
	the errant user who forgets things like actions,
	or enters them with mistakes.
 */
/**
 * @edited 10/13/06 n8agrin
 */
require_once 'Kea/Kea.php';
class Kea_Controller_Router
{
	private $_default_controller = DEFAULT_CONTROLLER;
	private $_default_action = DEFAULT_ACTION;
	
	public function resolve(Kea_Request $r)
	{
		$action = array();
		
		switch (true) {
			case ($c = $r->get('c')):
				$action['controller'] = strtolower(Kea::cleanName($c));
				$r->get('a') ?
					$action['action'] = Kea::cleanName($r->get('a')) :
					$action['action'] = $this->_default_action;
			break;
			case ($a = $r->get('a')):
				$action['controller'] = $this->_default_controller;
				$action['action'] = Kea::cleanName($a);
			break;
			default:
				$action['controller'] = $this->_default_controller;
				$action['action'] = $this->_default_action;
			break;
		}
		
		$r->addAction($action);
		
		return $r;
	}
}

?>