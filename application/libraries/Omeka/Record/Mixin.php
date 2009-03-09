<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * This represents a kind of mixin for Omeka_Record implementations.
 *
 * Any methods declared for an implementation of this class can be called
 * transparently by an Omeka_Record object that uses one of these modules.
 *
 * For instance, the Item model does not have an addTags() method, but the
 * Taggable class does.  Since Item declares Taggable as one of its modules,
 * an Item instance call all of Taggable's methods, so that adding tags
 * would be as simple as calling $item->addTags('foo, bar');
 *
 * Note that this is not a true mixin because it cannot override any existing
 * methods on a Record object.  
 *
 * @todo Modify Omeka_Record to allow Omeka_Record_Module implementations to
 * be added at runtime.  
 * @see Taggable
 * @see Orderable
 * @see Omeka_Record
 * @abstract
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
abstract class Omeka_Record_Mixin
{
	protected $record;
	
	public function __call($m, $a)
	{
		return call_user_func_array( array($this->record, $m), $a);
	}
	
	public function beforeSave() {}
	public function beforeUpdate() {}
	public function beforeInsert() {}
	public function afterInsert() {}
	public function afterSave() {}
	public function afterUpdate() {}
	public function afterSaveForm(&$post) {}
	public function beforeSaveForm(&$post) {}
	public function beforeDelete() {}
	public function afterDelete() {}
	public function beforeValidate() {}
	public function afterValidate() {}
}