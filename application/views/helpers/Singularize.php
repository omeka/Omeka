<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\View\Helper
 */
class Omeka_View_Helper_Singularize extends Zend_View_Helper_Abstract
{
    public function singularize($var)
    {
        return Inflector::singularize(Inflector::underscore($var));
    }
}
