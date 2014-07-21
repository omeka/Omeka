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
    /**
     * Name of the element that stores the CSRF token.
     *
     * This should be unique across the application, but we do provide a
     * default of "csrf".
     *
     * @var string
     */
    protected $_hashName = 'csrf';

    /**
     * Time the token is valid, in seconds. The default setting here is an hour.
     *
     * @var int
     */
    protected $_timeout = 3600;
        
    public function init()
    {
        parent::init();
        $this->addElement('hash', $this->_hashName, array('timeout' => $this->_timeout));
    }

    public function loadDefaultDecorators()
    {
        $this->setDecorators(array('FormElements'));
    }

    public function setHashName($hashName)
    {
        $this->_hashName = $hashName;
    }

    public function setTimeout($timeout)
    {
        $this->_timeout = $timeout;
    }
}
