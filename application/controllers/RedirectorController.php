<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Controller
 */
class RedirectorController extends Omeka_Controller_AbstractActionController
{
    public function indexAction()
    {
        $router = $this->getFrontController()->getRouter();
        // don't allow going to this action directly
        if ($router->getCurrentRouteName() !== Omeka_Application_Resource_Router::HOMEPAGE_ROUTE_NAME) {
            throw new Omeka_Controller_Exception_404;
        }
        $uri = trim($this->getRequest()->getUserParam('redirect_uri'));
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
        $redirector->gotoUrlAndExit($uri);
    }
}
