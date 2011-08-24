<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @subpackage Models
 * @author CHNM
 */
 
/**
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class ProcessTable extends Omeka_Db_Table
{
    protected $_target = 'Process';
    
    public function findByClass($className)
    {
        $select = $this->getSelect()->where("p.class = ?");
        return $this->fetchObjects($select, array($className));        
    }
    
    public function findByStatus($status)
    {
        $select = $this->getSelect()->where("p.status = ?");
        return $this->fetchObjects($select, array($status));        
    }
}
