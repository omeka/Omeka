<?php
/**
 *
 * Copyright 2006:
 * George Mason University
 * Center for History and New Media,
 * State of Virginia 
 *
 * LICENSE
 *
 * This source file is subject to the GNU Public License that
 * is bundled with this package in the file GPL.txt, and the
 * specific license found in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL: 
 * http://www.gnu.org/licenses/gpl.txt
 * If you did not receive a copy of the GPL or local license and are unable to
 * obtain it through the world-wide-web, please send an email 
 * to chnm@gmu.edu so we can send you a copy immediately.
 *
 * This software is licensed under the GPL license by the Center
 * For History and New Media, at George Mason University, except 
 * where other free software licenses apply.
 * The source code may only be reused or redistributed if the
 * copyright notice and licensing information above are retained,
 * and other included Zend and Cake licenses, are preserved. 
 * 
 * @author Nate Agrin
 * @contributors Josh Greenburg, Kris Kelly, Dan Stillman
 * @license http://www.gnu.org/licenses/gpl.txt GNU Public License
 */
require_once 'Kea/Domain/Model.php';
class Collection extends Kea_Domain_Model
{
	public $collection_id;
	public $collection_name;
	public $collection_description;
	public $collection_active;
	public $collection_collector;
	
	//Put this in your model to use the syntax Collection::findById()
	public static function findById( $id ) {
		return self::doFindById( $id, __CLASS__ );
	}
	
	protected static function getTable() { return 'collections'; }
	
	public function addToCollection( $item_id, $collection_id )
	{
		$select = self::$_adapter->select();
		$select->from( 'items_collections' )
				->where( 'item_id = ?', $item_id )
				->where( 'collection_id = ?', $collection_id );
		
		$result = self::$_adapter->query($select);
		
		if( $result->num_rows == 0 ) {
			return self::$_adapter->insert( 'items_collections',
				array(	'item_id' => $item_id,
				 		'collection_id' => $collection_id ) );	
		} else {
			self::$_session->flash( 'This item is already in this collection.' );
			return null;
		}
	}
}

?>