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
 * @version    $Id: Kinematic.php 2849 2007-01-17 14:34:50Z thomas $
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
 * @subpackage Zend_Measure_Viscosity_Kinematic
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Viscosity_Kinematic extends Zend_Measure_Abstract
{
    // Viscosity_Kinematic definitions
    const STANDARD = 'SQUARE_METER_PER_SECOND';

    const CENTISTOKES                  = 'CENTISTOKES';
    const LENTOR                       = 'LENTOR';
    const LITER_PER_CENTIMETER_DAY     = 'LITER_PER_CENTIMETER_DAY';
    const LITER_PER_CENTIMETER_HOUR    = 'LITER_PER_CENTIMETER_HOUR';
    const LITER_PER_CENTIMETER_MINUTE  = 'LITER_PER_CENTIMETER_MINUTE';
    const LITER_PER_CENTIMETER_SECOND  = 'LITER_PER_CENTIMETER_SECOND';
    const POISE_CUBIC_CENTIMETER_PER_GRAM = 'POISE_CUBIC_CENTIMETER_PER_GRAM';
    const SQUARE_CENTIMETER_PER_DAY    = 'SQUARE_CENTIMETER_PER_DAY';
    const SQUARE_CENTIMETER_PER_HOUR   = 'SQUARE_CENTIMETER_PER_HOUR';
    const SQUARE_CENTIMETER_PER_MINUTE = 'SQUARE_CENTIMETER_PER_MINUTE';
    const SQUARE_CENTIMETER_PER_SECOND = 'SQUARE_CENTIMETER_PER_SECOND';
    const SQUARE_FOOT_PER_DAY          = 'SQUARE_FOOT_PER_DAY';
    const SQUARE_FOOT_PER_HOUR         = 'SQUARE_FOOT_PER_HOUR';
    const SQUARE_FOOT_PER_MINUTE       = 'SQUARE_FOOT_PER_MINUTE';
    const SQUARE_FOOT_PER_SECOND       = 'SQUARE_FOOT_PER_SECOND';
    const SQUARE_INCH_PER_DAY          = 'SQUARE_INCH_PER_DAY';
    const SQUARE_INCH_PER_HOUR         = 'SQUARE_INCH_PER_HOUR';
    const SQUARE_INCH_PER_MINUTE       = 'SQUARE_INCH_PER_MINUTE';
    const SQUARE_INCH_PER_SECOND       = 'SQUARE_INCH_PER_SECOND';
    const SQUARE_METER_PER_DAY         = 'SQUARE_METER_PER_DAY';
    const SQUARE_METER_PER_HOUR        = 'SQUARE_METER_PER_HOUR';
    const SQUARE_METER_PER_MINUTE      = 'SQUARE_METER_PER_MINUTE';
    const SQUARE_METER_PER_SECOND      = 'SQUARE_METER_PER_SECOND';
    const SQUARE_MILLIMETER_PER_DAY    = 'SQUARE_MILLIMETER_PER_DAY';
    const SQUARE_MILLIMETER_PER_HOUR   = 'SQUARE_MILLIMETER_PER_HOUR';
    const SQUARE_MILLIMETER_PER_MINUTE = 'SQUARE_MILLIMETER_PER_MINUTE';
    const SQUARE_MILLIMETER_PER_SECOND = 'SQUARE_MILLIMETER_PER_SECOND';
    const STOKES                       = 'STOKES';

    private static $_UNITS = array(
        'CENTISTOKES'                  => array(0.000001,        'cSt'),
        'LENTOR'                       => array(0.0001,          'lentor'),
        'LITER_PER_CENTIMETER_DAY'     => array(array('' => 1, '/' => 864000), 'l/cm day'),
        'LITER_PER_CENTIMETER_HOUR'    => array(array('' => 1, '/' => 36000),  'l/cm h'),
        'LITER_PER_CENTIMETER_MINUTE'  => array(array('' => 1, '/' => 600),    'l/cm m'),
        'LITER_PER_CENTIMETER_SECOND'  => array(0.1,             'l/cm s'),
        'POISE_CUBIC_CENTIMETER_PER_GRAM' => array(0.0001,       'P cm³/g'),
        'SQUARE_CENTIMETER_PER_DAY'    => array(array('' => 1, '/' => 864000000),'cm²/day'),
        'SQUARE_CENTIMETER_PER_HOUR'   => array(array('' => 1, '/' => 36000000),'cm²/h'),
        'SQUARE_CENTIMETER_PER_MINUTE' => array(array('' => 1, '/' => 600000),'cm²/m'),
        'SQUARE_CENTIMETER_PER_SECOND' => array(0.0001,          'cm²/s'),
        'SQUARE_FOOT_PER_DAY'          => array(0.0000010752667, 'ft²/day'),
        'SQUARE_FOOT_PER_HOUR'         => array(0.0000258064,    'ft²/h'),
        'SQUARE_FOOT_PER_MINUTE'       => array(0.001548384048,  'ft²/m'),
        'SQUARE_FOOT_PER_SECOND'       => array(0.09290304,      'ft²/s'),
        'SQUARE_INCH_PER_DAY'          => array(7.4671296e-9,    'in²/day'),
        'SQUARE_INCH_PER_HOUR'         => array(0.00000017921111, 'in²/h'),
        'SQUARE_INCH_PER_MINUTE'       => array(0.000010752667,  'in²/m'),
        'SQUARE_INCH_PER_SECOND'       => array(0.00064516,      'in²/s'),
        'SQUARE_METER_PER_DAY'         => array(array('' => 1, '/' => 86400), 'm²/day'),
        'SQUARE_METER_PER_HOUR'        => array(array('' => 1, '/' => 3600),  'm²/h'),
        'SQUARE_METER_PER_MINUTE'      => array(array('' => 1, '/' => 60),    'm²/m'),
        'SQUARE_METER_PER_SECOND'      => array(1,               'm²/s'),
        'SQUARE_MILLIMETER_PER_DAY'    => array(array('' => 1, '/' => 86400000000), 'mm²/day'),
        'SQUARE_MILLIMETER_PER_HOUR'   => array(array('' => 1, '/' => 3600000000),  'mm²/h'),
        'SQUARE_MILLIMETER_PER_MINUTE' => array(array('' => 1, '/' => 60000000),    'mm²/m'),
        'SQUARE_MILLIMETER_PER_SECOND' => array(0.000001,        'mm²/s'),
        'STOKES'                       => array(0.0001,          'St')
    );

    private $_Locale = null;

    /**
     * Zend_Measure_Viscosity_Kinematic provides an locale aware class for
     * conversion and formatting of kinematic viscosity values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  integer|string      $value   Value as string, integer, real or float
     * @param  string              $type    OPTIONAL A Zend_Measure_Viscosity_Kinematic Type
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
     * @param  Zend_Measure_Viscosity_Kinematic  $object  Viscosity Kinematic object to compare
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
     * @param  string              $type    OPTIONAL A Zend_Measure_Viscosity_Kinematic Type
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

        if (empty( self::$_UNITS[$type] )) {
            throw new Zend_Measure_Exception("type ($type) is a unknown kinematic viscosity");
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
            throw new Zend_Measure_Exception("type ($type) is a unknown kinematic viscosity");
        }

        // Convert to standard value
        $value = parent::getValue();
        if (is_array(self::$_UNITS[parent::getType()][0])) {
            foreach (self::$_UNITS[parent::getType()][0] as $key => $found) {
                switch ( $key ) {
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
        parent::setType( $type );
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
