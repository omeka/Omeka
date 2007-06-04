<?php
require_once MODEL_DIR.DIRECTORY_SEPARATOR.'Metafield.php';
/**
 * @package Omeka
 **/
require_once 'Kea/Controller/Action.php';
class TypesController extends Kea_Controller_Action
{
	public function init()
	{
		$this->_modelClass = 'Type';
		$this->_table = Doctrine_Manager::getInstance()->getTable('Type');
	}
	
	protected function commitForm($type) 
	{
		if(!empty($_POST))
		{
			$conn = $this->getConn();
		
			//Start the transaction
			$conn->beginTransaction();
			
			$type->setFromForm($_POST);
			
			//Remove empty metafield submissions
			foreach( $type->TypesMetafields as $key => $tm )
			{
				if(empty($tm->metafield_id)) {
					$type->TypesMetafields->remove($key);
				}
			}
			
			//duplication (delete/remove existing metafields)
			foreach( $type->Metafields as $key => $metafield )
			{
				if($_POST['delete_metafield'][$key] == 'on') {
					$metafield->delete();
				}
				
				if(empty($metafield->name) || $_POST['remove_metafield'][$key] == 'on') {
					$type->Metafields->remove($key);
				}
			}
			
			try {
				$type->save();
				$conn->commit();
				return true;
			}
			catch(Doctrine_Validator_Exception $e) {
				$type->gatherErrors($e);
				$conn->rollback();
				return false;
			}	
		}
		return false;
	}
	
	protected function loadFormData() 
	{
		$id = $this->getRequest()->getParam('id');
		$type = Doctrine_Manager::getInstance()->getTable('Type')->find($id);
		$metafields = Doctrine_Manager::getInstance()->getTable('Metafield')->findMetafieldsWithoutType($type);
		$this->_view->assign(compact('metafields'));
	}
	
	public function metafieldsAction()
	{
		return $this->getTable('Metafield')->findAll();
	}
}
?>