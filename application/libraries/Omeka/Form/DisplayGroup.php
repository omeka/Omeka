<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Subclass of Zend_Form_DisplayGroup that exist to override the default 
 * decorators associated with display groups.
 *
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
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
        $this->setDecorators(array('FormElements','Fieldset'));
    }
}
