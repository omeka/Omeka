<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Subclass of Zend_Form_DisplayGroup that exist to override the default 
 * decorators associated with display groups.
 * 
 * @package Omeka\Form
 */
class Omeka_Form_DisplayGroup extends Zend_Form_DisplayGroup
{
    /**
     * Cause display groups to render as HTML fieldset elements.
     *
     * @return void
     */
    public function loadDefaultDecorators()
    {
        $this->setDecorators(array(
            array('Description', array('tag' => 'p', 'class' => 'explanation', 'escape' => false)),
            'FormElements',
            'Fieldset'
        ));
    }
}
