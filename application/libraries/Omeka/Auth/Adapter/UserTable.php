<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * 
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2010
 **/
class Omeka_Auth_Adapter_UserTable extends Zend_Auth_Adapter_DbTable
{
    
    public function __construct(Omeka_Db $db)
    {
        parent::__construct($db->getAdapter(), 
                            $db->User, 
                            'username', 
                            'password', 
                            'SHA1(CONCAT(salt, ?)) AND active = 1');
    }
    
    protected function _authenticateValidateResult($resultIdentity)
    {
        $authResult = parent::_authenticateValidateResult($resultIdentity);
        if (!$authResult->isValid()) {
            return $authResult;
        }
        // This auth result uses the username as the identity, what we need
        // instead is the user ID.
        $correctResult = new Zend_Auth_Result($authResult->getCode(), $this->_resultRow['id'], $authResult->getMessages());
        return $correctResult;
    }
}
