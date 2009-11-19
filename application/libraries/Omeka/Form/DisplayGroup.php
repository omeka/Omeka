<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2009
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 **/

/**
 * Subclass of Zend_Form_DisplayGroup that exist to override the default 
 * decorators associated with display groups.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2009
 **/
class Omeka_Form_DisplayGroup extends Zend_Form_DisplayGroup
{
    public function loadDefaultDecorators()
    {
        $this->setDecorators(array('FormElements','Fieldset'));
    }
}
