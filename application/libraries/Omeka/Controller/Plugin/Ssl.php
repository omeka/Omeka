<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Handle SSL configuration for Omeka sites.
 * 
 * @package Omeka\Controller\Plugin
 */
class Omeka_Controller_Plugin_Ssl extends Zend_Controller_Plugin_Abstract
{
    const LOGINS = 'logins';
    const SESSIONS = 'sessions';
    const ALWAYS = 'always';

    private $_sslConfig;

    private $_redirector;

    private $_auth;

    public function __construct($sslConfig, 
                                $redirector,
                                Zend_Auth $auth)
    {
        $this->_sslConfig = $sslConfig;
        $this->_redirector = $redirector;
        $this->_auth = $auth;
    }

    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        if ($this->_isSslRequest($request)) {
            return;
        }

        if ($this->_secureAllRequests() || $this->_secureAuthenticatedSession()) {
            return $this->_redirect($request);
        }
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if ($this->_isSslRequest($request)) {
            return;
        }

        if ($this->_isLoginRequest($request)) {
            return $this->_redirect($request);
        }
    }

    private function _isLoginRequest($request)
    {
        // Logins should be protected in all configurations.
        if (!in_array($this->_sslConfig, array(
            self::LOGINS, self::SESSIONS, self::ALWAYS
        ))) {
            return false;
        }

        // It remains an open question as to whether this should be interpreted 
        // as the 'universal' login action for all plugin modules (not just 
        // default).
        return ($request->getActionName() == 'login') 
            && ($request->getControllerName() == 'users');
    }

    /**
     * Unauthenticated sessions are not as valuable to attackers, so we only 
     * really need to check if an authenticated session is being used.
     */
    private function _secureAuthenticatedSession()
    {
        if ($this->_sslConfig != self::SESSIONS) {
            return false;
        }

        return (boolean)$this->_auth->getStorage()->read();
    }

    private function _isSslRequest($request)
    {
        return $request->isSecure();
    }

    private function _redirect($request)
    {
        $_SERVER['HTTPS'] = 'on';
        $secureUrl = $request->getScheme() . '://' 
                   . $request->getHttpHost() . $request->getRequestUri();
        return $this->_redirector->gotoUrl($secureUrl);
    }

    private function _secureAllRequests()
    {
        return $this->_sslConfig == self::ALWAYS;
    }
}
