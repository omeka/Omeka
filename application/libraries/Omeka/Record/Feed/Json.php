<?php 
/**
* 
*/
class Omeka_Record_Feed_Json extends Omeka_Record_Feed_Abstract
{
	/**
	 * Testing the flexibility of this.  Json returns arrays to the 
	 * Omeka_View_Format_Json class which handles placing them in the header
	 *
	 * @return array
	 **/
	
	public function renderOne(Omeka_Record $record) {
		return $record->toArray();
	}
	
	public function renderAll(array $records) {
		$json = array();
		foreach ($records as $record) {
			$json[] = $record->toArray();
		}
		
		return $json;
	}
}
 
?>
