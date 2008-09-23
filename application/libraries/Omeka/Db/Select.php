<?php 
/**
 * Hacked version of Zend's Db_Select object for Omeka
 *
 * @version $Id$
 * @copyright CHNM, 18 May, 2007
 * @package Omeka
 **/

/**
 * Class for SQL SELECT generation and results.
 *
 * @package    Omeka
 * @subpackage Select
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
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