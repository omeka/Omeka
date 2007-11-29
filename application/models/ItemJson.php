<?php 
/**
* 
*/
class ItemJson extends Omeka_Record_Feed_Abstract
{
	/**
	 * Testing the flexibility of this.  Json returns arrays to the 
	 * Omeka_View_Format_Json class which handles placing them in the header
	 *
	 * @return array
	 **/
	public function renderOne(Omeka_Record $item) {
		return $item->toArray();
	}
	
	public function renderAll(array $items) {
		$json = array();
		foreach ($items as $item) {
			$json[] = $item->toArray();
		}
		
		return $json;
	}
}
 
?>
