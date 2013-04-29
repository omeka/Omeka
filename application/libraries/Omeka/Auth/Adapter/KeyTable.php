<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Authenticate against an API key.
 * 
 * @package Omeka\Auth
 */
class Omeka_Auth_Adapter_KeyTable implements Zend_Auth_Adapter_Interface
{
    protected $_key;
    
    /**
     * @param Omeka_Db $db Database object.
     */
    public function __construct($key = null)
    {
        $this->_key = $key;
    }
    
    /**
     * Authenticate against an API key.
     * 
     * @return Zend_Auth_Result|null
     */
    public function authenticate()
    {
        if (null === $this->_key) {
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, null);
        }
        
        $db = Zend_Registry::get('bootstrap')->getResource('db');
        $sql = "SELECT * FROM $db->Key WHERE `key` = ?";
        $key = $db->getTable('Key')->fetchObject($sql, array($this->_key));
        $code = $key ? Zend_Auth_Result::SUCCESS : Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
        return new Zend_Auth_Result($code, $key);
    }
}
