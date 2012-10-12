<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Class for SQL SELECT generation and results.
 * 
 * @package Omeka\Db
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
            if (!($db = Zend_Registry::get('bootstrap')->getResource('Db'))) {
                throw new RuntimeException("Unable to retrieve Omeka_Db instance.");
            }
            $adapter = $db->getAdapter();
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
