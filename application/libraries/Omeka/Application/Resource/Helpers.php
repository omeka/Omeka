<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Initializes controller action helpers.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Helpers extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $this->_initDbHelper();
        $this->_initViewRenderer();
        $this->_initResponseContexts();
        
        // The ACL helper should not be loaded during REST API requests. 
        // Otherwise, Omeka may attempt to redirect to users/login when it 
        // appears that a controller/action combination is protected.
        $front = Zend_Controller_Front::getInstance();
        if (!$front->getParam('api')) {
            $this->_initAclHelper();
        }
        
        Zend_Controller_Action_HelperBroker::addPath(APP_DIR . '/controllers/helpers', 'Omeka_Controller_Action_Helper');
    }
    
    private function _initDbHelper()
    {
        $this->getBootstrap()->bootstrap('Db');
        $dbHelper = new Omeka_Controller_Action_Helper_Db(
            $this->getBootstrap()->getResource('Db'));
        Zend_Controller_Action_HelperBroker::addHelper($dbHelper);
    }
    
    private function _initViewRenderer()
    {
        $this->getBootstrap()->bootstrap('View');
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = Zend_Registry::get('view');
        $viewRenderer->setView($view)
                     ->setViewSuffix('php');
    }
    
    /**
     * Define the custom response format contexts for Omeka.
     *
     * Plugin writers should use the 'response_contexts' filter to modify
     * or expand the list of formats that existing controllers may respond to.
     *
     * @link http://framework.zend.com/manual/en/zend.controller.actionhelpers.html#zend.controller.actionhelpers.contextswitch
     *
     * Example of a definition of a response context through the ZF API:
     *
     * $contexts->addContext('dc', array(
     *    'suffix'    => 'dc',
     *    'headers'   => array('Content-Type' => 'text/xml'),
     *    'callbacks' => array(
     *        'init' => 'atBeginningDoThis',
     *        'post' => 'afterwardsDoThis'
     *    )
     *  ));
     *
     * @return void
     */
    private function _initResponseContexts()
    {
        Zend_Controller_Action_HelperBroker::addHelper(new Omeka_Controller_Action_Helper_ContextSwitch);
        $contexts = Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch');
        
        $contexts->setContextParam('output');
        
        $contextArray = self::getDefaultResponseContexts();
        
        if ($pluginBroker = $this->getBootstrap()->getResource('PluginBroker')) {
            $contextArray = $pluginBroker->applyFilters('response_contexts', $contextArray);
        }
        
        $contexts->addContexts($contextArray);
    }
    
    /**
     * Returns the default response contexts for Omeka.
     *
     * @return array
     */
    static public function getDefaultResponseContexts()
    {
        return array(
             'omeka-xml' => array(
                'suffix'  => 'omeka-xml',
                'headers' => array('Content-Type' => 'text/xml')
             ),
             'omeka-json' => array(
                'suffix' => 'omeka-json',
                'headers' => array('Content-Type' => 'application/json')
             ),
             'dcmes-xml' => array(
                 'suffix'    => 'dcmes-xml',
                 'headers'   => array('Content-Type' => 'application/rdf+xml; charset=utf-8')
             ),
             'rss2' => array(
                 'suffix'    => 'rss2',
                 'headers'   => array('Content-Type' => 'application/rss+xml; charset=utf-8')
             ),
             'atom' => array(
                 'suffix'    => 'atom',
                 'headers'   => array('Content-Type' => 'application/atom+xml; charset=utf-8')
             )
         );
    }
    
    private function _initAclHelper()
    {
        $acl = $this->_bootstrap->bootstrap('Acl')->acl;
        // Set up the action helper for MVC.
        $currentUser = $this->_bootstrap->bootstrap('Currentuser')->getResource('Currentuser');
        $aclChecker = new Omeka_Controller_Action_Helper_Acl($acl, $currentUser);
        Zend_Controller_Action_HelperBroker::addHelper($aclChecker);
    }
}
