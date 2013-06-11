<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The controller for API /resources.
 * 
 * @package Omeka\Controller
 */
class ResourcesController extends Omeka_Controller_AbstractActionController
{
    /**
     * Handle GET request without ID.
     */
    public function indexAction()
    {
        $apiResources = $this->getFrontController()->getParam('api_resources');
        // Add the site-specific URL to each API resource.
        foreach ($apiResources as $resource => &$resourceInfo) {
            $resourceInfo['url'] = Omeka_Record_Api_AbstractRecordAdapter::getResourceUrl("/$resource");
        }
        $this->_helper->jsonApi($apiResources);
    }
}
