<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Validates that a string is a DateTime value.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2009-2010
 */
class Omeka_Validate_DateTime extends Zend_Validate_Abstract
{
    /**
     * Verify a string against a date-time regex.
     *
     * @link http://us.php.net/manual/en/function.checkdate.php#78362
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $value, $matches)) {
            if (checkdate($matches[2], $matches[3], $matches[1])) {
                return true;
            }
        }
        
        return false;
    }
}
