<?php 
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Db\Table
 */
class Table_User extends Omeka_Db_Table
{
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
            'users.id', 
            'users.name');
    }
    
    public function findByEmail($email)
    {
        $select = $this->getSelect();
        $select->where('users.email = ?')->limit(1);
        return $this->fetchObject($select, array($email));
    }
    
    public function applySearchFilters($select, $params)
    {
        // Show only users with a specific role.
        if (array_key_exists('role', $params) and !empty($params['role'])) {
            $select->where('users.role = ?', $params['role']);
        }
        
        // Show only users who are active
        if (array_key_exists('active', $params) and $params['active'] !== '') {
            $boolean = new Omeka_Filter_Boolean;
            $select->where('users.active = ?', $boolean->filter($params['active']));
        }
        
        if(isset($params['name'])) {
            $select->where('users.name LIKE ?', "%" . $params['name'] ."%");
        }
        
        if(isset($params['username'])) {
            $select->where('users.username LIKE ?', "%" . $params['username'] ."%");
        }
        
        if(isset($params['email'])) {
            $select->where('users.email = ?', $params['email']);
        }                
    }
}
