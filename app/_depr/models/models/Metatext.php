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
class Metatext extends Kea_Domain_Model
{
	public $metatext_id;
	public $metafield_id;
	public $item_id;
	public $metatext_text;
	
	public static function findById( $id ) {
		return self::doFindById( $id, __CLASS__ );
	}
	
	protected static function getTable() { return 'metatext'; }
	
	public function deleteByItem( $item_id )
	{
		return self::$_adapter->delete( 'metatext', 'metatext.item_id = \'' . $item_id . '\'');
	}
	
	public function deleteUnused( $item_id, $saved_metatext_ids )
	{
		$count = count( $saved_metatext_ids );
		$sql = "DELETE FROM {$this->_table_name} WHERE metatext.item_id = '$item_id'";
		for ( $i=0; $i < $count; $i++ )
		{ 
			$metatext_id = $saved_metatext_ids[$i];
			$sql .= " AND metatext.metatext_id != '$metatext_id' ";
		}
		return self::$_adapter->query($sql);
	}
}

?>