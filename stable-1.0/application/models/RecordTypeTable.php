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
class RecordTypeTable extends Omeka_Db_Table
{
    protected $_alias = 'rty';
    
    public function findIdFromName($recordTypeName)
    {
        $select = $this->getSelect()->reset('columns');
        $select->from(array(), 'id')->where('name = ?', (string) $recordTypeName);
        return $this->getDb()->fetchOne($select);
    }
}