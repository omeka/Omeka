<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

require_once 'ItemsElementsTable.php';

/**
 * 
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ItemsElements extends Omeka_Record
{
    public $item_id;
    public $element_id;
    public $text;
    
    /**
     * Convenience method for grabbing the value of this record.
     * 
     * @return string
     **/
    public function __toString()
    {
        return (string) $this->text;
    }
}
