<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Validate a password to see if it matches that of an existing user.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2007-2010
 **/
class Omeka_Validate_UserPassword extends Zend_Validate_Abstract
{
    private $_user;
    
    public function __construct(User $user)
    {
        $this->_user = $user;
    }
    
    public function isValid($value, $context = null)
    {
        assert('$this->_user->password !== null');
        return $this->_user->hashPassword($value) === $this->_user->password;
    }
}
