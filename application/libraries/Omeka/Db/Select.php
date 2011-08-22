<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Class for SQL SELECT generation and results.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_Db_Select extends Zend_Db_Select
{
    /**
     * @param Zend_Db_Adapter $adapter (optional) Adapter to use instead of the
     * one set up by Omeka.
     */
    public function __construct($adapter=null)
    {
        if (!$adapter) {
            //Omeka's connection to the Zend_Db_Adapter
            if (!($db = Omeka_Context::getInstance()->getDb())) {
                throw new RuntimeException("Unabled to retrieve Omeka_Db instance from Omeka_Context.");
            }
            $adapter = Omeka_Context::getInstance()->getDb()->getAdapter();            
        }
        return parent::__construct($adapter);
    }
    
    /**
     * Detect if this SELECT joins with the given table.
     *
     * @param string $name Table name.
     * @return boolean
     */
    public function hasJoin($name)
    {
        return array_key_exists($name, $this->getPart('from'));
    }
}
