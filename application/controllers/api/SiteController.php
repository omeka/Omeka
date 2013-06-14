<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * The controller for API /site.
 * 
 * @package Omeka\Controller
 */
class SiteController extends Omeka_Controller_AbstractActionController
{
    /**
     * Handle GET request without ID.
     */
    public function indexAction()
    {
        $site = array(
            'omeka_url' => WEB_ROOT, 
            'omeka_version' => get_option('omeka_version'), 
            'title' => get_option('site_title'), 
            'description' => get_option('description'), 
            'author' => get_option('author'), 
            'copyright' => get_option('copyright'), 
        );
        $this->_helper->jsonApi($site);
    }
}
