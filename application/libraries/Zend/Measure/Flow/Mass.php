<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Measure
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Mass.php 2854 2007-01-17 15:39:24Z thomas $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Implement needed classes
 */
require_once 'Zend/Measure/Exception.php';
require_once 'Zend/Measure/Abstract.php';
require_once 'Zend/Locale.php';


/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Flow_Mass
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Flow_Mass extends Zend_Measure_Abstract
{
    // Flow_Mass definitions
    const STANDARD = 'KILOGRAM_PER_SECOND';

    const CENTIGRAM_PER_DAY    = 'CENTIGRAM_PER_DAY';
    const CENTIGRAM_PER_HOUR   = 'CENTIGRAM_PER_HOUR';
    const CENTIGRAM_PER_MINUTE = 'CENTIGRAM_PER_MINUTE';
    const CENTIGRAM_PER_SECOND = 'CENTIGRAM_PER_SECOND';
    const GRAM_PER_DAY         = 'GRAM_PER_DAY';
    const GRAM_PER_HOUR        = 'GRAM_PER_HOUR';
    const GRAM_PER_MINUTE      = 'GRAM_PER_MINUTE';
    const GRAM_PER_SECOND      = 'GRAM_PER_SECOND';
    const KILOGRAM_PER_DAY     = 'KILOGRAM_PER_DAY';
    const KILOGRAM_PER_HOUR    = 'KILOGRAM_PER_HOUR';
    const KILOGRAM_PER_MINUTE  = 'KILOGRAM_PER_MINUTE';
    const KILOGRAM_PER_SECOND  = 'KILOGRAM_PER_SECOND';
    const MILLIGRAM_PER_DAY    = 'MILLIGRAM_PER_DAY';
    const MILLIGRAM_PER_HOUR   = 'MILLIGRAM_PER_HOUR';
    const MILLIGRAM_PER_MINUTE = 'MILLIGRAM_PER_MINUTE';
    const MILLIGRAM_PER_SECOND = 'MILLIGRAM_PER_SECOND';
    const OUNCE_PER_DAY        = 'OUNCE_PER_DAY';
    const OUNCE_PER_HOUR       = 'OUNCE_PER_HOUR';
    const OUNCE_PER_MINUTE     = 'OUNCE_PER_MINUTE';
    const OUNCE_PER_SECOND     = 'OUNCE_PER_SECOND';
    const POUND_PER_DAY        = 'POUND_PER_DAY';
    const POUND_PER_HOUR       = 'POUND_PER_HOUR';
    const POUND_PER_MINUTE     = 'POUND_PER_MINUTE';
    const POUND_PER_SECOND     = 'POUND_PER_SECOND';
    const TON_LONG_PER_DAY     = 'TON_LONG_PER_DAY';
    const TON_LONG_PER_HOUR    = 'TON_LONG_PER_HOUR';
    const TON_LONG_PER_MINUTE  = 'TON_LONG_PER_MINUTE';
    const TON_LONG_PER_SECOND  = 'TON_LONG_PER_SECOND';
    const TON_PER_DAY          = 'TON_PER_DAY';
    const TON_PER_HOUR         = 'TON_PER_HOUR';
    const TON_PER_MINUTE       = 'TON_PER_MINUTE';
    const TON_PER_SECOND       = 'TON_PER_SECOND';
    const TON_SHORT_PER_DAY    = 'TON_SHORT_PER_DAY';
    const TON_SHORT_PER_HOUR   = 'TON_SHORT_PER_HOUR';
    const TON_SHORT_PER_MINUTE = 'TON_SHORT_PER_MINUTE';
    const TON_SHORT_PER_SECOND = 'TON_SHORT_PER_SECOND';

    private static $_UNITS = array(
        'CENTIGRAM_PER_DAY'    => array(array('' => 0.00001, '/' => 86400),    'cg/day'),
        'CENTIGRAM_PER_HOUR'   => array(array('' => 0.00001, '/' => 3600),     'cg/h'),
        'CENTIGRAM_PER_MINUTE' => array(array('' => 0.00001, '/' => 60),       'cg/m'),
        'CENTIGRAM_PER_SECOND' => array(0.00001,                               'cg/s'),
        'GRAM_PER_DAY'         => array(array('' => 0.001, '/' => 86400),      'g/day'),
        'GRAM_PER_HOUR'        => array(array('' => 0.001, '/' => 3600),       'g/h'),
        'GRAM_PER_MINUTE'      => array(array('' => 0.001, '/' => 60),         'g/m'),
        'GRAM_PER_SECOND'      => array(0.001,                                 'g/s'),
        'KILOGRAM_PER_DAY'     => array(array('' => 1, '/' => 86400),          'kg/day'),
        'KILOGRAM_PER_HOUR'    => array(array('' => 1, '/' => 3600),           'kg/h'),
        'KILOGRAM_PER_MINUTE'  => array(array('' => 1, '/' => 60),             'kg/m'),
        'KILOGRAM_PER_SECOND'  => array(1,                                     'kg/s'),
        'MILLIGRAM_PER_DAY'    => array(array('' => 0.000001, '/' => 86400),   'mg/day'),
        'MILLIGRAM_PER_HOUR'   => array(array('' => 0.000001, '/' => 3600),    'mg/h'),
        'MILLIGRAM_PER_MINUTE' => array(array('' => 0.000001, '/' => 60),      'mg/m'),
        'MILLIGRAM_PER_SECOND' => array(0.000001,                              'mg/s'),
        'OUNCE_PER_DAY'        => array(array('' => 0.0283495, '/' => 86400),  'oz/day'),
        'OUNCE_PER_HOUR'       => array(array('' => 0.0283495, '/' => 3600),   'oz/h'),
        'OUNCE_PER_MINUTE'     => array(array('' => 0.0283495, '/' => 60),     'oz/m'),
        'OUNCE_PER_SECOND'     => array(0.0283495,                             'oz/s'),
        'POUND_PER_DAY'        => array(array('' => 0.453592, '/' => 86400),   'lb/day'),
        'POUND_PER_HOUR'       => array(array('' => 0.453592, '/' => 3600),    'lb/h'),
        'POUND_PER_MINUTE'     => array(array('' => 0.453592, '/' => 60),      'lb/m'),
        'POUND_PER_SECOND'     => array(0.453592,                              'lb/s'),
        'TON_LONG_PER_DAY'     => array(array('' => 1016.04608, '/' => 86400), 't/day'),
        'TON_LONG_PER_HOUR'    => array(array('' => 1016.04608, '/' => 3600),  't/h'),
        'TON_LONG_PER_MINUTE'  => array(array('' => 1016.04608, '/' => 60),    't/m'),
        'TON_LONG_PER_SECOND'  => array(1016.04608,                            't/s'),
        'TON_PER_DAY'          => array(array('' => 1000, '/' => 86400),       't/day'),
        'TON_PER_HOUR'         => array(array('' => 1000, '/' => 3600),        't/h'),
        'TON_PER_MINUTE'       => array(array('' => 1000, '/' => 60),          't/m'),
        'TON_PER_SECOND'       => array(1000,                                  't/s'),
        'TON_SHORT_PER_DAY'    => array(array('' => 907.184, '/' => 86400),    't/day'),
        'TON_SHORT_PER_HOUR'   => array(array('' => 907.184, '/' => 3600),     't/h'),
        'TON_SHORT_PER_MINUTE' => array(array('' => 907.184, '/' => 60),       't/m'),
        'TON_SHORT_PER_SECOND' => array(907.184,                               't/s')
    );

    private $_Locale = null;

    /**
     * Zend_Measure_Flow_Mass provides an locale aware class for
     * conversion and formatting of Flow_Mass values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  integer|string      $value   Value as string, integer, real or float
     * @param  string              $type    OPTIONAL A Zend_Measure_Flow_Mass Type
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing numbers
     * @throws Zend_Measure_Exception
     */
    public function __construct($value, $type = null, $locale = null)
    {
        $this->setValue($value, $type, $locale);
    }


    /**
     * Compare if the value and type is equal
     *
     * @param  Zend_Measure_Flow_Mass  $object  Flow Mass object to compare
     * @return boolean
     */
    public function equals($object)
    {
        if ($object->toString() == $this->toString()) {
            return true;
        }

        return false;
    }


    /**
     * Set a new value
     *
     * @param  integer|string      $value   Value as string, integer, real or float
     * @param  string              $type    OPTIONAL A Zend_Measure_Flow_Mass Type
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for parsing numbers
     * @throws Zend_Measure_Exception
     */
    public function setValue($value, $type = null, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->_Locale;
        }

        if (!$locale = Zend_Locale::isLocale($locale, true)) {
            throw new Zend_Measure_Exception("language ($locale) is a unknown language");
        }

        if ($type === null) {
            $type = self::STANDARD;
        }

        try {
            $value = Zend_Locale_Format::getNumber($value, $locale);
        } catch(Exception $e) {
            throw new Zend_Measure_Exception($e->getMessage());
        }

        if (empty(self::$_UNITS[$type])) {
            throw new Zend_Measure_Exception("type ($type) is a unknown flow mass");
        }

        parent::setValue($value, $type, $locale);
        parent::setType($type);
    }


    /**
     * Set a new type, and convert the value
     *
     * @param  string  $type  New type to set
     * @throws Zend_Measure_Exception
     */
    public function setType($type)
    {
        if (empty(self::$_UNITS[$type])) {
            throw new Zend_Measure_Exception("type ($type) is a unknown flow mass");
        }

        // Convert to standard value
        $value = parent::getValue();
        if (is_array(self::$_UNITS[parent::getType()][0])) {
            foreach (self::$_UNITS[parent::getType()][0] as $key => $found) {
                switch ($key) {
                    case "/":
                        $value /= $found;
                        break;
                    default:
                        $value *= $found;
                        break;
                }
            }
        } else {
            $value = $value * (self::$_UNITS[parent::getType()][0]);
        }

        // Convert to expected value
        if (is_array(self::$_UNITS[$type][0])) {
            foreach (self::$_UNITS[$type][0] as $key => $found) {
                switch ($key) {
                    case "/":
                        $value *= $found;
                        break;
                    default:
                        $value /= $found;
                        break;
                }
            }
        } else {
            $value = $value / (self::$_UNITS[$type][0]);
        }

        parent::setValue($value, $type, $this->_Locale);
        parent::setType($type);
    }


    /**
     * Returns a string representation
     *
     * @return string
     */
    public function toString()
    {
        return parent::getValue() . ' ' . self::$_UNITS[parent::getType()][1];
    }


    /**
     * Returns a string representation
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }


    /**
     * Returns the conversion list
     * 
     * @return array
     */
    public function getConversionList()
    {
        return self::$_UNITS;
    }
}
