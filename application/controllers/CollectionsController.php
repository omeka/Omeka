<?php
/**
 * @package Omeka
 **/
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Collection.php';
require_once 'Kea/Controller/Action.php';
class CollectionsController extends Kea_Controller_Action
{
	public function init()
	{
		$this->_table = Doctrine_Manager::getInstance()->getTable('Collection');
		$this->_modelClass = 'Collection';
	}
	
	/**
	 * All the code below this marker is essentially a direct duplication of the working code in the ItemsController,
	 * where Item has been changed to Collection.  It may be worthwhile to abstract this basic CRUD interface to the Kea_Controller_Action class
	 * as a builtin form of scaffolding that may be overriden as needed
	 */
	
    public function indexAction()
    {
		$this->_forward('collections', 'browse');
    }
	
	protected function commitForm($collection)
	{
		if(!empty($_POST))
		{
			$collection->setArray($_POST);
			try {
				$collection->save();
				return true;
			}
			catch(Doctrine_Validator_Exception $e) {
				return false;
			}	
		}
		return false;
	}
	
    public function noRouteAction()
    {
        $this->_redirect('/');
    }
}
?>