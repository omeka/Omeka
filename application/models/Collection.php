<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'CollectionPermissions.php';
require_once 'CollectionTable.php';

/**
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Collection extends Omeka_Record
{		
	public $name;
	public $description;
	public $public;
	public $featured;
	
	protected $_related = array('Collectors'=>'getCollectors');
	
	public function construct()
	{
		$this->_modules[] = new Relatable($this);
	}
	
	public function hasCollectors()
	{
		$db = $this->getDb();
		$id = (int) $this->id;
				
		$sql = "SELECT COUNT(er.entity_id) FROM $db->EntitiesRelations er 
				INNER JOIN $db->EntityRelationships err ON err.id = er.relationship_id
				WHERE er.relation_id = ? AND er.type = 'Collection' AND err.name = 'collector'";

		$count = $db->fetchOne($sql, array($id));
		
		return $count > 0;	
	}
	
	public function totalItems()
	{
		//This will query the ItemTable for a count of all items associated with the collection
		return $this->getDb()->getTable('Item')->count(array('collection'=>$this->name));
	}
	
	protected function getCollectors()
	{
		return ($this->exists()) ? $this->getRelatedEntities('collector') : array();
	}

	protected function _validate()
	{
		if(empty($this->name)) {
			$this->addError('name', 'Collection must be given a valid name.');
		}
		
		if(strlen($this->name) > 255) {
			$this->addError('name', 'Collection name must be less than 255 characters.');
		}
	}
    
    /**
     * Remove a collector's name from being associated with the collection.
     * 
     * @param Entity|integer
     * @return boolean Was successful or not.
     **/
    public function removeCollector($collector)
    {
        $result = $this->removeRelatedTo($collector, 'collector', 1);
        return ($result->rowCount() == 1);
    }
    
	public function beforeSaveForm(&$post)
	{
		//Handle the boolean vars in the form
		//This must be a radio button b/c checkboxes don't submit post correctly
		if(array_key_exists('public', $post)) {
			$this->public = (bool) $post['public'];
			unset($post['public']);
		}
			
		if(array_key_exists('featured', $post)) {
			$this->featured = (bool) $post['featured'];
			unset($post['featured']);
		}	
	}
	
	protected function afterSaveForm($post)
	{
		//Process the collectors that have been provided on the form
		$collectorsPost = $post['collectors'];
		
		foreach ($collectorsPost as $k => $c) {
			if(!empty($c)) {
				//Numbers mean that an entity_id has been passed, so add the relation
				if(is_numeric($c)) {
					$entity_id = $c;
					$this->addRelatedIfNotExists($entity_id, 'collector');
				}else {
					//@todo Add support for entering a string name (this is thorny b/c of string splitting and unicode)
					throw new Exception( 'Cannot enter a collector by name.' );
				}
			}
		}
	}
}