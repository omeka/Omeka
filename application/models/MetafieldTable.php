<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2008
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @deprecated
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Center for History and New Media, 2007-2008
 **/
class MetafieldTable extends Omeka_Db_Table
{
    /**
     * Used to retrieve the row ID given the name (which is unique)
     * Called in one spot as of 12/13/07 : Item::saveMetatext()
     *
     * A bit more efficient than pulling down the entire row but may or may not be necessary
     *
     * @return int
     **/
    public function findIdFromName($name)
    {
        $db = $this->getDb();
        $sql = "
        SELECT mf.id 
        FROM $db->Metafield mf 
        WHERE mf.name = ? 
        LIMIT 1";
        return (int) $db->fetchOne($sql, array($name));
    }
    
    public function findByName($name) {
        $metafields = $this->findBySql("name = ?", array($name));
        return current($metafields);
    }
}