<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The controller for API /version.
 * 
 * @package Omeka\Controller
 */
class VersionController extends Omeka_Controller_AbstractActionController
{
    /**
     * The API version number.
     */
    const API_VERSION = '1.0';
    
    /**
     * Handle GET request without ID.
     */
    public function indexAction()
    {
        $this->_helper->jsonApi(array('version' => self::API_VERSION));
    }
}
