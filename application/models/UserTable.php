<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */
 
/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class UserTable extends Omeka_Db_Table
{
    public function getSelect()
    {
        $select = new Omeka_Db_Select($this->_db->getAdapter());
        
        $db = $this->getDb();
        
        $select->from(array('u'=>$db->User), 
                      array( 'u.id',
                             'u.username',
                             'u.password',
                             'u.salt',
                             'u.active',
                             'u.role',
                             'u.entity_id', 
                             'e.first_name', 
                             'e.middle_name', 
                             'e.last_name', 
                             'e.email',
                             'e.institution'))
                      ->joinInner(array('e'=>$db->Entity), 
                                  "e.id = u.entity_id", array());
        return $select;
    }

    /**
     * Find an active User given that user's ID.
     *
     * Returns null if the user being requested is not active.
     *
     * @return User|null
     */
    public function findActiveById($id)
    {
        $select = $this->getSelectForFind($id);
        $select->where('active = 1');
        return $this->fetchObject($select);
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
    
    public function applySearchFilters($select, $params)
    {
        // Show only users with a specific role.
        if (array_key_exists('role', $params) and !empty($params['role'])) {
            $select->where('u.role = ?', $params['role']);
        }
        
        // Show only users who are active
        if (array_key_exists('active', $params) and $params['active'] !== '') {
            $select->where('u.active = ?', (int)$params['active']);
        }
        
        // Sort by role, institution name, first/last name, username.
        // Order can be ASC or DESC
        if (array_key_exists('sort', $params)) {
            $sortOrder = (array_key_exists('sortOrder', $params) 
                and strtolower($params['sortOrder']) == 'desc') ? 
                'DESC' : 'ASC';
            
            switch ($params['sort']) {
                case 'role':
                    $orderClause = 'u.role ' . $sortOrder;
                    break;
                case 'institution':
                    $orderClause = 'e.institution ' . $sortOrder;
                    break;
                case 'first_name':
                    $orderClause = 'e.first_name ' . $sortOrder;
                    break;
                case 'last_name':
                    $orderClause = 'e.last_name ' . $sortOrder;
                    break;
                case 'username':
                    $orderClause = 'u.username ' . $sortOrder;
                    break;
                default:
                    # code...
                    break;
            }
            
            $select->order($orderClause);
        }
    }
}
