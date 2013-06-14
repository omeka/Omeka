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
class ApiController extends Omeka_Controller_AbstractActionController
{
    public function indexAction()
    {
        if (is_admin_theme()) {
            // There is no API endpoint on the admin theme.
            $this->_helper->redirector('index', 'index');
        }
        $this->view->title = get_option('site_title');
        $this->view->site_url = Omeka_Record_Api_AbstractRecordAdapter::getResourceUrl('/site');
        $this->view->resource_url = Omeka_Record_Api_AbstractRecordAdapter::getResourceUrl('/resources');
    }
}
