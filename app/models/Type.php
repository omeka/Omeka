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
require_once 'app/models/Metafield.php';
class Type extends Kea_Domain_Model
{
	public $type_id;
	public $type_name;
	public $type_description;
	public $type_active;
	
	/**
	 * Metafields Collection Object
	 */
	public $metafields;
	
	public static function findById( $id ) {
		return self::doFindById( $id, __CLASS__ );
	}
	
	public function getMetafields()
	{
		$this->metafields = self::getMapper('Metafield')->findByType( $this->type_id );
	}
	
	public function unqiue( $col, $val )
	{
		return $this->mapper()->unique( $col, $val );
	}
	
	public static function uniqueName( $name )
	{
		return self::getMapper(__CLASS__)->unique( 'type_name', $name );
	}
	
	public function removeMetafieldAssoc( Metafield $metafield )
	{
		if( !$metafield->getId() )
		{
			throw new Kea_Domain_Exception('Metafield needs a valid ID.');
		}
		$join_mapper = Kea_Domain_HelperFactory::getMapper('TypesMetafields');
		return $join_mapper->delete( $this->getId(), $metafield->getId() );
	}
	
	public function addMetafieldAssoc( Metafield $metafield )
	{
		if( !$metafield->getId() )
		{
			throw new Kea_Domain_Exception('Metafield needs a valid ID.');
		}
		$join_mapper = Kea_Domain_HelperFactory::getMapper('TypesMetafields');
		return $join_mapper->insert( $this->getId(), $metafield->getId() );
	}	
	
	public function hasMetafield( Metafield $metafield )
	{
		foreach( $this->metafields as $key => $mf )
		{
			if( ($mf instanceof Metafield && $mf->getId() == $metafield->getId() ) 
				|| (is_array($mf) && $mf['metafield_id'] == $metafield->getId() ) )
			{
				return TRUE;
			}
		}
		return FALSE;
	}
	
	public function delete( $id = null)
	{
		if(!$id) $id = $this->getId();
		// Delete type
		self::$_adapter->delete( $this->_table_name, 'type_id = "' . $id . '"' );
		
		// Delete metafield linkages
		self::$_adapter->delete( $this->_table_name.'_metafields', 'type_id = "' . $id . '"' );
		
		// Delete orphaned metafields
		self::$_adapter->delete( 'metafields', 'metafield_id NOT IN (SELECT metafield_id FROM types_metafields)' );

		return true;	
	}
	
	public function insertJoin( $cat_id, $mf_id )
	{	
		if( $this->joinExists( $cat_id, $mf_id ) ) {
			throw new Kea_DB_Mapper_Exception( 'The Type <=> Metafield join already exists.');
		}
		$array = array(
					'type_id'		=> $cat_id,
					'metafield_id'		=> $mf_id );

		$result = self::$_adapter->insert( 'types_metafields', $array );

		if( !$result ){
			throw new Kea_DB_Mapper_Exception( self::$_adapter->error() );
		}
		return true;
	}
	
	public function deleteJoin( $cat_id, $mf_id )
	{
		if( !$this->joinExists( $cat_id, $mf_id ) )
		{
			throw new Kea_DB_Mapper_Exception( 'The Type #'.$cat_id.' <=> Metafield #'.$mf_id.' does not already exist.');
		}
		
		$result = self::$_adapter->delete( 'types_metafields', 'type_id = '.$cat_id.' AND metafield_id = '.$mf_id );
		
		if( !$result ){
			throw new Kea_DB_Mapper_Exception( self::$_adapter->error() );
		}
		return true;
	}
	
	public function joinExists( $cat_id, $mf_id )
	{
		$stmt = self::$_adapter->select();
		$stmt->from( 'types_metafields' )
			 ->where( 'type_id = ?', $cat_id )
			 ->where( 'metafield_id = ?', $mf_id );
		$res = self::$_adapter->query( $stmt );
		if( !$res ) throw new Kea_DB_Mapper_Exception( self::$_adapter->error() );
		if( $res->num_rows > 0 ) {
			return true;
		}
		return false;
	}
	
	public function find_by_oc( $cat_id )
	{
		$select = self::$_adapter->select();
		$select->from( 'types_metafields' )
				->where( 'type_id = ?', $cat_id );
		return self::$_adapter->query( $select );
	}
	
}

?>