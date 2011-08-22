<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Authentication resource.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @todo Should be combined with the CurrentUser resource.
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_Core_Resource_Auth extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @return Zend_Auth
     */
    public function init()
    {
        // Make sure the session has been configured properly beforehand in order
        // to avoid bypassing Zend_Session::start()'s configuration mechanism.
        $this->getBootstrap()->bootstrap('Session');
        
        return Zend_Auth::getInstance();
    }
}
