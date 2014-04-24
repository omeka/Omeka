<?php
/**
 * Omeka
 *
 * @copyright Copyright 2007-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Validate an input as a hex color value as accepted by HTML5's color input.
 *
 * @package Omeka\Validate
 */
class Omeka_Validate_HexColor extends Zend_Validate_Abstract
{
    const MSG_BAD_COLOR = 'msgUri';

    protected $_messageTemplates = array(
        self::MSG_BAD_COLOR => '"%value%" is not a valid color. A color must be a hash (#) followed by 6 hex digits.',
    );

    public function isValid($value)
    {
        $this->_setValue($value);
        $valid = preg_match('/^#[0-9a-f]{6}$/i', $value);

        if (!$valid) {
            $this->_error(self::MSG_BAD_COLOR);
        }

        return $valid;
    }
}
