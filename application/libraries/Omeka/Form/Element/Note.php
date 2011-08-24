<?php 
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Form element for inserting plain HTML into forms.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class Omeka_Form_Element_Note extends Zend_Form_Element_Xhtml  
{  
    public $helper = 'formNote';  

    public function loadDefaultDecorators()
    {
        $this->clearDecorators();
        $this->addDecorator('ViewHelper');
    }
}

