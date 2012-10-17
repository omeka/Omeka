<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Adapted from:
 * http://www.techchorus.net/validate-uri-form-fields-zend-framework-custom-validator
 * 
 * @package Omeka\Validate
 */
class Omeka_Validate_Uri extends Zend_Validate_Abstract
{
    const MSG_URI = 'msgUri';

    protected $_messageTemplates = array(
        self::MSG_URI => "Invalid URI",
    );

    public function isValid($value)
    {
        $this->_setValue($value);
        $valid = Zend_Uri::check($value);
        
        if (!$valid) {
            $this->_error(self::MSG_URI);
        }

        return $valid;
    }
}
