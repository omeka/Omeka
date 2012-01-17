<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 *
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009
 */
class Omeka_Core_Resource_Helpers extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $this->getBootstrap()->bootstrap('Db');
        $this->_initDbHelper();
        $this->initializeActionHelpers();
        $this->_initAclHelper();
    }

    private function _initDbHelper()
    {
        $dbHelper = new Omeka_Controller_Action_Helper_Db(
            $this->getBootstrap()->getResource('Db'));
        Zend_Controller_Action_HelperBroker::addHelper($dbHelper);
    }

    private function initializeActionHelpers()
    {
        $this->initViewRenderer();
        $this->initResponseContexts();
        $this->initSearchHelper();
    }

    private function initSearchHelper()
    {
        $searchHelper = new Omeka_Controller_Action_Helper_SearchItems;
        Zend_Controller_Action_HelperBroker::addHelper($searchHelper);
    }

    private function initViewRenderer()
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = new Omeka_View();
        $viewRenderer->setView($view)
                     ->setViewSuffix('php');

        // Register the view object so that it can be called by the view helpers.
        Zend_Registry::set('view', $view);
    }

    /**
     * Define the custom response format contexts for Omeka.
     *
     * Plugin writers should use the 'define_response_contexts' filter to modify
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
    private function initResponseContexts()
    {
        Zend_Controller_Action_HelperBroker::addHelper(new Omeka_Controller_Action_Helper_ContextSwitch);
        $contexts = Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch');

        $contexts->setContextParam('output');

        $contextArray = self::getDefaultResponseContexts();

        if ($pluginBroker = $this->getBootstrap()->getResource('PluginBroker')) {
            $contextArray = $pluginBroker->applyFilters('define_response_contexts', $contextArray);
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
