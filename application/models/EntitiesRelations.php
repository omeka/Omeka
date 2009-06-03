<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'EntityRelationships.php';

/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
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
            $this->addError('Joins in the EntitiesRelations table must be given a polymorphic type');
        }
        
        if (empty($this->relation_id) || empty($this->relationship_id)) {
            $this->addError('Joins in the EntitiesRelations table must be filled out entirely');
        }
    }
    
    //@todo Move this to CURRENT_TIMESTAMP() SQL
    protected function beforeInsert()
    {
        $this->time = date('YmdHis');
    }
}