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
class Table_Process extends Omeka_Db_Table
{
    protected $_target = 'Process';
    
    public function findByClass($className)
    {
        $select = $this->getSelect()->where('processes.class = ?');
        return $this->fetchObjects($select, array($className));        
    }
    
    public function findByStatus($status)
    {
        $select = $this->getSelect()->where('processes.status = ?');
        return $this->fetchObjects($select, array($status));        
    }
}
