<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Subclass of Zend_Form_Element_Xhtml designed to allow extra html into a form as an element.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_Form_Element_Html extends Zend_Form_Element_Xhtml
{
    public $helper = 'formHtml';
    
    public function isValid($value)
    {
        return true;
    }
}