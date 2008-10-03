<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/
 
/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class UserTable extends Omeka_Db_Table
{
    public function getSelect()
    {
        $select = new Omeka_Db_Select;
        
        $db = $this->getDb();
        
        $select->from(array('u'=>$db->User), 
                      array( 'u.id',
                             'u.username',
                             'u.password',
                             'u.active',
                             'u.role',
                             'u.entity_id', 
                             'e.first_name', 
                             'e.middle_name', 
                             'e.last_name', 
                             'e.email', 
                             'entity_type'=>'e.type'))
                      ->joinInner(array('e'=>$db->Entity), 
                                  "e.id = u.entity_id", array())
                    
                    // Need to join on the parent table to retrieve the 'institution'
                    // value for the user model.  this is b/c of the nested
                    // relationship between entities.              
                    ->joinLeft(array('e_parent'=>$db->Entity),
                                "e_parent.id = e.parent_id",
                                array('institution'=>"IFNULL(e.institution, e_parent.institution)"));

        return $select;
    }
    
    protected function _getColumnPairs()
    {
        return array(
            'u.id', 
            'u.name' => new Zend_db_Expr( 
                'CONCAT_WS(" ", e.first_name, e.middle_name, e.last_name)')
            );
    }
    
    public function findByEntity($entity_id)
    {
        $select = $this->getSelect();
        $select->where("e.id = ?")->limit(1);
                
        return $this->fetchObject($select, array((int) $entity_id));        
    }
    
    public function findByEmail($email)
    {
        $select = $this->getSelect();
        $select->where("e.email = ?")->limit(1);
        return $this->fetchObject($select, array($email));
    }
}