<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Classes that implement this interface may override the default login behavior
 * for Omeka.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
interface Omeka_Login_Interface
{    
    /**
     * @return Zend_Form
     */
    public function getForm();
    
    /**
     * @return Zend_Auth_Result
     */        
    public function authenticate(Zend_Auth $auth, $input); 
}
