<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'Entity.php';
require_once 'Institution.php';
require_once 'PersonTable.php';

/**
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class Person extends Entity
{    
    protected function construct()
    {
        $this->type = "Person";
    }
    /**
     * Find the institution for the Entity and save it as parent_id
     *
     **/
    public function beforeSave()
    {                
        if (!empty($this->institution)) {
            $this->setParentToInstitution();
        }
    }
    
    protected function setParentToInstitution()
    {
        $name = $this->institution;
        
        $inst = $this->getTable('Entity')->findUniqueOrNew(array('institution'=>$name));
        $inst->type = "Institution";
        $inst->save();
        $this->parent_id = $inst->id;
        $this->institution = NULL;
    }
    
    public function getName()
    {
        return implode(' ', array($this->first_name, $this->middle_name, $this->last_name));
    }
}