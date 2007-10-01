<?php
require_once 'Zend/Auth/Result.php';
class Omeka_Auth_Token extends Zend_Auth_Result
{

    /**
     * Sets the token values, as appropriate
     *
     * @param  boolean $valid
     * @param  array   $identity
     * @param  string  $message
     * @return void
     */
    public function __construct($code, $identity, $message = null)
    {
        $this->_code    = $valid;
        $this->_identity = $identity;
        $this->_message  = $message;
    }
}
?>