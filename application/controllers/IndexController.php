<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * The only thing this controller does is load the home page of the theme
 * at index.php within any given theme.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @subpackage Controllers
 * @author CHNM
 * @copyright Roy Rosenzweig Center for History and New Media, 2007-2010
 */
class IndexController extends Omeka_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->viewRenderer->renderScript('index.php');
    }
}
