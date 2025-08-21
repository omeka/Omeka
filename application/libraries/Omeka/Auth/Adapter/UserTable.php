<?php
/**
 * Omeka
 * 
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Auth adapter that uses Omeka's users table for authentication.
 * 
 * @package Omeka\Auth
 */
class Omeka_Auth_Adapter_UserTable implements Zend_Auth_Adapter_Interface
{
    protected $_db;
    protected $_identity;
    protected $_credential;

    /**
     * @param Omeka_Db $db Database object.
     */
    public function __construct(Omeka_Db $db)
    {
        $this->_db = $db;
    }

    /**
     * @param string $identity
     * @return Omeka_Auth_Adapter_UserTable
     */
    public function setIdentity($identity)
    {
        $this->_identity = $identity;
        return $this;
    }

    /**
     * @param string $credential
     * @return Omeka_Auth_Adapter_UserTable
     */
    public function setCredential($credential)
    {
        $this->_credential = $credential;
        return $this;
    }

    /**
     * Authenticate with the provided identity and credential
     *
     * Uses PHP's password_hash to check the credential.
     *
     * @throws Zend_Auth_Adapter_Exception
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $db = $this->_db;
        $identity = $this->_identity;
        $credential = $this->_credential;
        if ($identity === null) {
            throw new Zend_Auth_Adapter_Exception('A value for the identity was not provided prior to authentication.');
        }
        if ($credential === null) {
            throw new Zend_Auth_Adapter_Exception('A credential value was not provided prior to authentication.');
        }

        $sql = "SELECT id, password FROM `{$db->User}` WHERE username = ? AND active = 1";
        try {
            $resultIdentities = $this->_db->fetchAll($sql, [$identity], Zend_Db::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Zend_Auth_Adapter_Exception('The supplied parameters failed to produce a valid sql statement.', 0, $e);
        }

        if (count($resultIdentities) < 1) {
            $code = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $message = 'A record with the supplied identity could not be found.';
        } elseif (count($resultIdentities) > 1) {
            $code = Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS;
            $message = 'More than one record matches the supplied identity.';
        } else {
            $identityRow = $resultIdentities[0];
            // Returned auth result must be the user ID, not name
            $identity = $identityRow['id'];
            if (password_verify($credential, $identityRow['password'])) {
                $code = Zend_Auth_Result::SUCCESS;
                $message = 'Authentication successful.';
            } else {
                $code = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
                $message = 'Supplied credential is invalid.';
            }
        }
        return new Zend_Auth_Result($code, $identity, [$message]);
    }
}
