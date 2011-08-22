<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Validate a password to see if it matches that of an existing user.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class Omeka_Validate_UserPassword extends Zend_Validate_Abstract
{
    /**
     * Invalid password error.
     *
     * @var string
     */
    const INVALID = 'invalid';
    
    /**
     * Error message templates.
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID => "Password is invalid."
    );
    
    /**
     * User to check the password against.
     *
     * @var User
     */
    private $_user;
    
    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->_user = $user;
    }
    
    /**
     * Validate against a user's stored password.
     *
     * @param string $value Password to check.
     * @param null $context Not used.
     */
    public function isValid($value, $context = null)
    {
        assert('$this->_user->password !== null');
        $valid = $this->_user->hashPassword($value) === $this->_user->password;
        if (!$valid) {
            $this->_error(self::INVALID);
        }
        return $valid;
    }
}
