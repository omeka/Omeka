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
class Metafield extends Kea_Domain_Model
{
	public $metafield_id;
	public $metafield_name;
	public $metafield_description;
	
	public static function findById( $id ) {
		return self::doFindById( $id, __CLASS__ );
	}
	
	public function uniqueName( $name = null )
	{
		if(!$name) $name = $this->metafield_name;
		return $this->unique( 'metafield_name', $name );
	}
	
	public static function findBy( $col, $val )
	{
		$inst = new self;
		$sql = $inst->find()->where( "$col = ?", $val );
		$res = $inst->query( $sql );
		if ( $res->num_rows )
		{
			return new Metafield( $res->fetch_assoc() );
		}
		else
		{
			return null;
		}
	}
	
	public function findByType( $id )
	{
		return $this->find()
					->joinLeft( 'types_metafields', 'types_metafields.metafield_id = metafields.metafield_id')
					->where( 'types_metafields.type_id = ?', $id )
					->execute();
	}
}

?>