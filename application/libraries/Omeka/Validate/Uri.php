<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Adapted from:
 * http://www.techchorus.net/validate-uri-form-fields-zend-framework-custom-validator
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
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
