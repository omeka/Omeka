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
class IndexController extends Omeka_Controller_AbstractActionController
{
    public function indexAction()
    {
        $this->_helper->viewRenderer->renderScript('index.php');
    }
}
