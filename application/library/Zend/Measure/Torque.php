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
 * @version    $Id: Torque.php 2883 2007-01-18 05:56:31Z gavin $
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
 * @subpackage Zend_Measure_Torque
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Torque extends Zend_Measure_Abstract
{
    // Torque definitions
    const STANDARD = 'NEWTON_METER';

    const DYNE_CENTIMETER     = 'DYNE_CENTIMETER';
    const GRAM_CENTIMETER     = 'GRAM_CENTIMETER';
    const KILOGRAM_CENTIMETER = 'KILOGRAM_CENTIMETER';
    const KILOGRAM_METER      = 'KILOGRAM_METER';
    const KILONEWTON_METER    = 'KILONEWTON_METER';
    const KILOPOND_METER      = 'KILOPOND_METER';
    const MEGANEWTON_METER    = 'MEGANEWTON_METER';
    const MICRONEWTON_METER   = 'MICRONEWTON_METER';
    const MILLINEWTON_METER   = 'MILLINEWTON_METER';
    const NEWTON_CENTIMETER   = 'NEWTON_CENTIMETER';
    const NEWTON_METER        = 'NEWTON_METER';
    const OUNCE_FOOT          = 'OUNCE_FOOT';
    const OUNCE_INCH          = 'OUNCE_INCH';
    const POUND_FOOT          = 'POUND_FOOT';
    const POUNDAL_FOOT        = 'POUNDAL_FOOT';
    const POUND_INCH          = 'POUND_INCH';

    private static $_UNITS = array(
        'DYNE_CENTIMETER'     => array(0.0000001,          'dyncm'),
        'GRAM_CENTIMETER'     => array(0.0000980665,       'gcm'),
        'KILOGRAM_CENTIMETER' => array(0.0980665,          'kgcm'),
        'KILOGRAM_METER'      => array(9.80665,            'kgm'),
        'KILONEWTON_METER'    => array(1000,               'kNm'),
        'KILOPOND_METER'      => array(9.80665,            'kpm'),
        'MEGANEWTON_METER'    => array(1000000,            'MNm'),
        'MICRONEWTON_METER'   => array(0.000001,           'µNm'),
        'MILLINEWTON_METER'   => array(0.001,              'mNm'),
        'NEWTON_CENTIMETER'   => array(0.01,               'Ncm'),
        'NEWTON_METER'        => array(1,                  'Nm'),
        'OUNCE_FOOT'          => array(0.084738622,        'ozft'),
        'OUNCE_INCH'          => array(array('' => 0.084738622, '/' => 12), 'ozin'),
        'POUND_FOOT'          => array(array('' => 0.084738622, '*' => 16), 'lbft'),
        'POUNDAL_FOOT'        => array(0.0421401099752144, 'plft'),
        'POUND_INCH'          => array(array('' => 0.084738622, '/' => 12, '*' => 16), 'lbin')
    );

    private $_Locale = null;

    /**
     * Zend_Measure_Torque provides an locale aware class for
     * conversion and formatting of Torque values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  integer|string      $value   Value as string, integer, real or float
     * @param  string              $type    OPTIONAL A Zend_Measure_Torque Type
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
     * @param  Zend_Measure_Torque  $object  Torque object to compare
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
     * @param  string              $type    OPTIONAL A Zend_Measure_Torque Type
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
            throw new Zend_Measure_Exception("type ($type) is a unknown torque");
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
            throw new Zend_Measure_Exception("type ($type) is a unknown torque");
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
