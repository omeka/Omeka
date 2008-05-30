<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class ItemTypeTable extends Omeka_Db_Table
{
    /**
     * Duplicated from findAllForSelectForm().  May be useful as a method
     * in Omeka_Db_Table, but it remains to be seen whether implementation
     * will require it.
     * 
     * @see CollectionTable::findAllForSelectForm()
     * @see select_item_type()
     * @return array
     **/
    public function findAllForSelectForm()
    {
        $select = new Omeka_Db_Select;
        $db = $this->getDb();
        $select->from(array('it'=>$db->ItemType), array('it.id', 'it.name'));

        $pairs = $db->fetchPairs($select);
        return $pairs;
    }
}
