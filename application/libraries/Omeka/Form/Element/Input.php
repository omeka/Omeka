<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * HTML5 input form element
 * 
 * @package Omeka\Form
 */
class Omeka_Form_Element_Input extends Zend_Form_Element_Xhtml
{
    /**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'formInput';
}
