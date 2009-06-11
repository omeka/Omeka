<?php 
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * @todo Should be combined with the CurrentUser resource.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Core_Resource_Auth extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        return Zend_Auth::getInstance();
    }
}