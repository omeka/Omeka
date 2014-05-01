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
class Table_UsersActivations extends Omeka_Db_Table
{
    
    public function findByUrl($url)
    {
        return $this->fetchObject($this->getSelect()->where('url = ?', $url)->limit(1));
    }
    
    public function findByUser($user)
    {
        $select = $this->getSelect();
        $select->where('user_id = ?', $user->id);
        $select->limit(1);
        return $this->fetchObject($select);
    }
}
