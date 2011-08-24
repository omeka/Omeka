<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * @package Omeka
 * @subpackage Models
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @deprecated
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 */
class EntityTable extends Omeka_Db_Table
{
    /**
     * Returns an object that has the same values as those supplied in the parameters.
     *
     * @param array $values The properties of the entity with which to search and match
     */
    public function findUnique($values)
    {
        $select = new Omeka_Db_Select;
        $db = $this->getDb();
        
        $select->from(array('e'=>"$db->Entity"), "e.*");
        
        foreach ($values as $key => $value) {
            $select->where("$key = ?", $value);
        }
        
        $select->limit(1);
        
        $unique = $this->fetchObject($select);
        return $unique;
    }
    
    protected function _getColumnPairs()
    {
        return array('e.id', 'e.name' => new Zend_db_Expr( 
            'CONCAT_WS(" ", e.first_name, e.middle_name, e.last_name, e.institution)'));
    }

    /**
     * Possible options include:
     * 
     * get_email
     *
     * @param Omeka_Db_Select
     * @param array
     */
    public function applySearchFilters($select, $params = array())
    {    
        // If we are not allowed to display email addresses, don't pull it from the DB
        if (!array_key_exists('get_email', $params) || !$params['get_email']) {
            $select->reset(Zend_Db_Select::COLUMNS);
            $select->from(array(), array('e.id', 
                                         'e.first_name', 
                                         'e.middle_name', 
                                         'e.last_name', 
                                         'e.institution') );
        }
    }
}
