<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Class for SQL SELECT generation and results.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Db_Select extends Zend_Db_Select
{
    
    public function __construct($adapter=null)
    {
        if (!$adapter) {
            //Omeka's connection to the Zend_Db_Adapter
            $adapter = Omeka_Context::getInstance()->getDb()->getAdapter();            
        }
        return parent::__construct($adapter);
    }
        
    public function hasJoin($name)
    {
        return array_key_exists($name, $this->getPart('from'));
    }
}