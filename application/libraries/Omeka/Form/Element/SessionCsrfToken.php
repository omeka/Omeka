<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * CSRF form protection
 *
 * This class is an adaptation of ZF's Hash element that uses a per-session
 * token.
 *
 * @version 2.2.2
 * @see Zend_Form_Element_Hash
 * @package Omeka\Form
 */
class Omeka_Form_Element_SessionCsrfToken extends Zend_Form_Element_Xhtml
{
    const SESSION_NAME = 'OmekaSessionCsrfToken';

    /**
     * Use formHidden view helper by default
     * @var string
     */
    public $helper = 'formHidden';

    /**
     * Should we disable loading the default decorators?
     * @var bool
     */
    protected $_disableLoadDefaultDecorators = true;

    /**
     * Actual token used.
     *
     * @var mixed
     */
    protected $_token;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     * Constructor
     *
     * Creates session namespace for CSRF token, and adds validator for CSRF
     * token.
     *
     * @return void
     */
    public function init()
    {
        $this->_initToken()
             ->setAllowEmpty(false)
             ->setRequired(true)
             ->setDecorators(array('ViewHelper'))
             ->_initCsrfValidator();
    }

    /**
     * Set session object
     *
     * @param  Zend_Session_Namespace $session
     * @return self
     */
    public function setSession($session)
    {
        $this->_session = $session;
        return $this;
    }

    /**
     * Get session object
     *
     * Instantiate session object if none currently exists
     *
     * @return Zend_Session_Namespace
     */
    public function getSession()
    {
        if (null === $this->_session) {
            $this->_session = new Zend_Session_Namespace(self::SESSION_NAME);
        }
        return $this->_session;
    }

    /**
     * Retrieve CSRF token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Render CSRF token in form
     *
     * @param  Zend_View_Interface $view
     * @return string
     */
    public function render(Zend_View_Interface $view = null)
    {
        $this->setValue($this->_token);
        return parent::render($view);
    }

    /**
     * Override getLabel() to always be empty
     *
     * @return null
     */
    public function getLabel()
    {
        return null;
    }

    /**
     * Set the CSRF token
     *
     * If a session token exists, it is used. Otherwise, a new token is
     * generated and saved in the session.
     *
     * @return self
     */
    protected function _initToken()
    {
        $session = $this->getSession();
        if (isset($session->token)) {
            $this->_token = $session->token;
        } else {
            $this->_token = $session->token = $this->_generateToken();
        }
        return $this;
    }

    /**
     * Initialize CSRF validator
     *
     * @return self
     */
    protected function _initCsrfValidator()
    {
        $rightToken = $this->_token;
        $this->addValidator('Identical', true, array($rightToken));
        return $this;
    }

    /**
     * Generate CSRF token
     *
     * @return void
     */
    protected function _generateToken()
    {
        return md5(
            mt_rand(1,1000000)
            .  self::SESSION_NAME
            .  mt_rand(1,1000000)
        );
    }
}
