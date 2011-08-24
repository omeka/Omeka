<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @deprecated
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 */
class EntitiesRelations extends Omeka_Record
{
    public $entity_id;
    public $relation_id;
    public $relationship_id;
    public $type;
    public $time;
    
    protected function _validate()
    {
        if (empty($this->type)) {
            $this->addError(__('Joins in the EntitiesRelations table must be given a polymorphic type'));
        }
        
        if (empty($this->relation_id) || empty($this->relationship_id)) {
            $this->addError(__('Joins in the EntitiesRelations table must be filled out entirely'));
        }
    }
    
    protected function beforeInsert()
    {
        $this->time = Zend_Date::now()->toString(self::DATE_FORMAT);
    }
}
