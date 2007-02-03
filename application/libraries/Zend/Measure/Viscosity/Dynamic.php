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
 * @version    $Id: Dynamic.php 2851 2007-01-17 15:03:14Z thomas $
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
 * @subpackage Zend_Measure_Viscosity_Dynamic
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Viscosity_Dynamic extends Zend_Measure_Abstract
{
    // Viscosity_Dynamic definitions
    const STANDARD = 'KILOGRAM_PER_METER_SECOND';

    const CENTIPOISE           = 'CENTIPOISE';
    const DECIPOISE            = 'DECIPOISE';
    const DYNE_SECOND_PER_SQUARE_CENTIMETER       = 'DYNE_SECOND_PER_SQUARE_CENTIMETER';
    const GRAM_FORCE_SECOND_PER_SQUARE_CENTIMETER = 'GRAM_FORCE_SECOND_PER_SQUARE_CENTIMETER';
    const GRAM_PER_CENTIMETER_SECOND              = 'GRAM_PER_CENTIMETER_SECOND';
    const KILOGRAM_FORCE_SECOND_PER_SQUARE_METER  = 'KILOGRAM_FORCE_SECOND_PER_SQUARE_METER';
    const KILOGRAM_PER_METER_HOUR    = 'KILOGRAM_PER_METER_HOUR';
    const KILOGRAM_PER_METER_SECOND  = 'KILOGRAM_PER_METER_SECOND';
    const MILLIPASCAL_SECOND   = 'MILLIPASCAL_SECOND';
    const MILLIPOISE           = 'MILLIPOISE';
    const NEWTON_SECOND_PER_SQUARE_METER = 'NEWTON_SECOND_PER_SQUARE_METER';
    const PASCAL_SECOND        = 'PASCAL_SECOND';
    const POISE                = 'POISE';
    const POISEUILLE           = 'POISEUILLE';
    const POUND_FORCE_SECOND_PER_SQUARE_FEET = 'POUND_FORCE_SECOND_PER_SQUARE_FEET';
    const POUND_FORCE_SECOND_PER_SQUARE_INCH = 'POUND_FORCE_SECOND_PER_SQUARE_INCH';
    const POUND_PER_FOOT_HOUR                = 'POUND_PER_FOOT_HOUR';
    const POUND_PER_FOOT_SECOND              = 'POUND_PER_FOOT_SECOND';
    const POUNDAL_HOUR_PER_SQUARE_FOOT       = 'POUNDAL_HOUR_PER_SQUARE_FOOT';
    const POUNDAL_SECOND_PER_SQUARE_FOOT     = 'POUNDAL_SECOND_PER_SQUARE_FOOT';
    const REYN                 = 'REYN';
    const SLUG_PER_FOOT_SECOND = 'SLUG_PER_FOOT_SECOND';
    const LBFS_PER_SQUARE_FOOT = 'LBFS_PER_SQUARE_FOOT';
    const NS_PER_SQUARE_METER  = 'NS_PER_SQUARE_METER';
    const WATER_20C            = 'WATER_20C';
    const WATER_40C            = 'WATER_40C';
    const HEAVY_OIL_20C        = 'HEAVY_OIL_20C';
    const HEAVY_OIL_40C        = 'HEAVY_OIL_40C';
    const GLYCERIN_20C         = 'GLYCERIN_20C';
    const GLYCERIN_40C         = 'GLYCERIN_40C';
    const SAE_5W_MINUS18C      = 'SAE_5W_MINUS18C';
    const SAE_10W_MINUS18C     = 'SAE_10W_MINUS18C';
    const SAE_20W_MINUS18C     = 'SAE_20W_MINUS18C';
    const SAE_5W_99C           = 'SAE_5W_99C';
    const SAE_10W_99C          = 'SAE_10W_99C';
    const SAE_20W_99C          = 'SAE_20W_99C';

    private static $_UNITS = array(
        'CENTIPOISE'          => array(0.001,      'cP'),
        'DECIPOISE'           => array(0.01,       'dP'),
        'DYNE_SECOND_PER_SQUARE_CENTIMETER'       => array(0.1,     'dyn s/cm²'),
        'GRAM_FORCE_SECOND_PER_SQUARE_CENTIMETER' => array(98.0665, 'gf s/cm²'),
        'GRAM_PER_CENTIMETER_SECOND'              => array(0.1,     'g/cm s'),
        'KILOGRAM_FORCE_SECOND_PER_SQUARE_METER'  => array(9.80665, 'kgf s/m²'),
        'KILOGRAM_PER_METER_HOUR'    => array(array('' => 1, '/' => 3600), 'kg/m h'),
        'KILOGRAM_PER_METER_SECOND'  => array(1,   'kg/ms'),
        'MILLIPASCAL_SECOND'  => array(0.001,      'mPa s'),
        'MILLIPOISE'          => array(0.0001,     'mP'),
        'NEWTON_SECOND_PER_SQUARE_METER' => array(1, 'N s/m²'),
        'PASCAL_SECOND'       => array(1,          'Pa s'),
        'POISE'               => array(0.1,        'P'),
        'POISEUILLE'          => array(1,          'Pl'),
        'POUND_FORCE_SECOND_PER_SQUARE_FEET' => array(47.880259,  'lbf s/ft²'),
        'POUND_FORCE_SECOND_PER_SQUARE_INCH' => array(6894.75729, 'lbf s/in²'),
        'POUND_PER_FOOT_HOUR' => array(0.00041337887,             'lb/ft h'),
        'POUND_PER_FOOT_SECOND'          => array(1.4881639,      'lb/ft s'),
        'POUNDAL_HOUR_PER_SQUARE_FOOT'   => array(0.00041337887,  'pdl h/ft²'),
        'POUNDAL_SECOND_PER_SQUARE_FOOT' => array(1.4881639,      'pdl s/ft²'),
        'REYN'                => array(6894.75729, 'reyn'),
        'SLUG_PER_FOOT_SECOND'=> array(47.880259,  'slug/ft s'),
        'WATER_20C'           => array(0.001,      'water (20°)'),
        'WATER_40C'           => array(0.00065,    'water (40°)'),
        'HEAVY_OIL_20C'       => array(0.45,       'oil (20°)'),
        'HEAVY_OIL_40C'       => array(0.11,       'oil (40°)'),
        'GLYCERIN_20C'        => array(1.41,       'glycerin (20°)'),
        'GLYCERIN_40C'        => array(0.284,      'glycerin (40°)'),
        'SAE_5W_MINUS18C'     => array(1.2,        'SAE 5W (-18°)'),
        'SAE_10W_MINUS18C'    => array(2.4,        'SAE 10W (-18°)'),
        'SAE_20W_MINUS18C'    => array(9.6,        'SAE 20W (-18°)'),
        'SAE_5W_99C'          => array(0.0039,     'SAE 5W (99°)'),
        'SAE_10W_99C'         => array(0.0042,     'SAE 10W (99°)'),
        'SAE_20W_99C'         => array(0.0057,     'SAE 20W (99°)')
    );

    private $_Locale = null;

    /**
     * Zend_Measure_Viscosity_Dynamic provides an locale aware class for
     * conversion and formatting of viscosity-dynamic values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  integer|string      $value   Value as string, integer, real or float
     * @param  string              $type    OPTIONAL A Zend_Measure_Cooking_Weight Type
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
     * @param  Zend_Measure_Viscosity_Dynamic  $object  Viscosity Dynamic object to compare
     *      * @param $object  object to compare equality
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
     * @param  string              $type    OPTIONAL A Zend_Measure_Viscosity_Dynamic Type
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
            throw new Zend_Measure_Exception("type ($type) is a unknown dynamic viscosity");
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
        if (empty( self::$_UNITS[$type] )) {
            throw new Zend_Measure_Exception("type ($type) is a unknown dynamic viscosity");
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
                switch ( $key ) {
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
