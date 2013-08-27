<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Initialize the view object.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_View extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * Register the view object so that it can be called by the view helpers.
     */
    public function init()
    {
        Zend_Registry::set('view', new Omeka_View);
    }
}
