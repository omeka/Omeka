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
 * @version    $Id: Acceleration.php 2883 2007-01-18 05:56:31Z gavin $
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
 * @subpackage Zend_Measure_Acceleration
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Acceleration extends Zend_Measure_Abstract
{
    // Acceleration definitions
    const STANDARD = 'METER_PER_SQUARE_SECOND';

    const CENTIGAL                     = 'CENTIGAL';                 // Metric
    const CENTIMETER_PER_SQUARE_SECOND = 'CENTIMETER_PER_SQUARE_SECOND'; // Metric
    const DECIGAL                      = 'DECIGAL';                  // Metric
    const DECIMETER_PER_SQUARE_SECOND  = 'DECIMETER_PER_SQUARE_SECOND';  // Metric
    const DEKAMETER_PER_SQUARE_SECOND  = 'DEKAMETER_PER_SQUARE_SECOND';  // Metric
    const FOOT_PER_SQUARE_SECOND       = 'FOOT_PER_SQUARE_SECOND';       // US
    const G                            = 'G';                        // Gravity
    const GAL                          = 'GAL';                      // Metric = 1cm/s²
    const GALILEO                      = 'GALILEO';                  // Metric = 1cm/s²
    const GRAV                         = 'GRAV';                     // Gravity
    const HECTOMETER_PER_SQUARE_SECOND = 'HECTOMETER_PER_SQUARE_SECOND'; // Metric
    const INCH_PER_SQUARE_SECOND       = 'INCH_PER_SQUARE_SECOND';       // US
    const KILOMETER_PER_HOUR_SECOND    = 'KILOMETER_PER_HOUR_SECOND';    // Metric
    const KILOMETER_PER_SQUARE_SECOND  = 'KILOMETER_PER_SQUARE_SECOND';  // Metric
    const METER_PER_SQUARE_SECOND      = 'METER_PER_SQUARE_SECOND';      // Metric
    const MILE_PER_HOUR_MINUTE         = 'MILE_PER_HOUR_MINUTE';         // US
    const MILE_PER_HOUR_SECOND         = 'MILE_PER_HOUR_SECOND';         // US
    const MILE_PER_SQUARE_SECOND       = 'MILE_PER_SQUARE_SECOND';       // US
    const MILLIGAL                     = 'MILLIGAL';                 // Metric
    const MILLIMETER_PER_SQUARE_SECOND = 'MILLIMETER_PER_SQUARE_SECOND'; // Metric

    private static $_UNITS = array(
        'CENTIGAL'                     => array(0.0001,   'cgal'),
        'CENTIMETER_PER_SQUARE_SECOND' => array(0.01,     'cm/s²'),
        'DECIGAL'                      => array(0.001,    'dgal'),
        'DECIMETER_PER_SQUARE_SECOND'  => array(0.1,      'dm/s²'),
        'DEKAMETER_PER_SQUARE_SECOND'  => array(10,       'dam/s²'),
        'FOOT_PER_SQUARE_SECOND'       => array(0.3048,   'ft/s²'),
        'G'                            => array(9.80665,  'g'),
        'GAL'                          => array(0.01,     'gal'),
        'GALILEO'                      => array(0.01,     'gal'),
        'GRAV'                         => array(9.80665,  'g'),
        'HECTOMETER_PER_SQUARE_SECOND' => array(100,      'h/s²'),
        'INCH_PER_SQUARE_SECOND'       => array(0.0254,   'in/s²'),
        'KILOMETER_PER_HOUR_SECOND'    => array(array('' => 5,'/' => 18), 'km/h²'),
        'KILOMETER_PER_SQUARE_SECOND'  => array(1000,     'km/s²'),
        'METER_PER_SQUARE_SECOND'      => array(1,        'm/s²'),
        'MILE_PER_HOUR_MINUTE'         => array(array('' => 22, '/' => 15, '*' => 0.3048, '/' => 60), 'mph/m'),
        'MILE_PER_HOUR_SECOND'         => array(array('' => 22, '/' => 15, '*' => 0.3048), 'mph/s'),
        'MILE_PER_SQUARE_SECOND'       => array(1609.344, 'mi/s²'),
        'MILLIGAL'                     => array(0.00001,  'mgal'),
        'MILLIMETER_PER_SQUARE_SECOND' => array(0.001,    'mm/s²')
    );

    private $_Locale = null;

    /**
     * Zend_Measure_Acceleration provides an locale aware class for
     * conversion and formatting of acceleration values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  integer|string      $value   Value as string, integer, real or float
     * @param  string              $type    OPTIONAL A Zend_Measure_Acceleration Type
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
     * @param  Zend_Measure_Acceleration  $object  Acceleration object to compare
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
     * @param  string              $type    OPTIONAL A Zend_Measure_Acceleration Type
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
            throw new Zend_Measure_Exception("type ($type) is a unknown acceleration");
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
            throw new Zend_Measure_Exception("type ($type) is a unknown acceleration");
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
