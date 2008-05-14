<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class EntityTable extends Omeka_Table
{
	public function findUniqueOrNew($values, $other = array())
	{	
		$select = new Omeka_Db_Select;
		$db = $this->getDb();
		
		$select->from( array('e'=>"$db->Entity"), "e.*");
		
		foreach ($values as $key => $value) {
			$select->where("$key = ?", $value);
		}
		
		$select->limit(1);
		
		$unique = $this->fetchObject($select);
		
		if(!$unique) {
			$unique = $this->recordFromData($values);
		}
		
		return $unique;
	}
	
	/**
	 * Possible options include:
	 * 
	 * get_email
	 * type
	 *
	 * @param Omeka_Db_Select
	 * @param array
	 **/
	public function applySearchFilters($select, $params=array())
	{	
		//If we are not allowed to display email addresses, don't pull it from the DB
		if(!$params['get_email']) {
			$select->reset('columns');
			$select->from(array(), array('e.id', 'e.first_name', 'e.middle_name', 'e.last_name', 'e.institution', 'e.parent_id', 'e.type') );
		}
		
		if($params['type']) {
		    $select->where('`e`.`type` = ?', (string) $params['type']);
		}		
	}
}