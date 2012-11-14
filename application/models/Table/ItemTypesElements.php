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
class Table_ItemTypesElements extends Omeka_Db_Table
{
    public function findByElement($elementId)
    {
        $select = $this->getSelect()->where('item_types_elements.element_id = ?', (int) $elementId);
        return $this->fetchObjects($select);
    }
}
