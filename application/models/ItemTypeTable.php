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
class ItemTypeTable extends Omeka_Db_Table
{
    protected $_alias = 'it';
    
    protected function _getColumnPairs()
    {
        return array('it.id', 'it.name');
    }
}
