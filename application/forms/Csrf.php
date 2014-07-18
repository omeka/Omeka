<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Quasi-form for adding CSRF token checking to manually-created forms.
 * 
 * @package Omeka\Form
 */
class Omeka_Form_Csrf extends Omeka_Form
{
    private $_hashName = 'csrf';
        
    public function init()
    {
        parent::init();
        $this->removeDecorator('Form');
        $this->addElement('hash', $this->_hashName);
    }

    public function setHashName($hashName)
    {
        $this->_hashName = $hashName;
    }
}
