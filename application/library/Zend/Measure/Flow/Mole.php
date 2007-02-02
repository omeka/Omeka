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
 * @version    $Id: Mole.php 2851 2007-01-17 15:03:14Z thomas $
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
 * @subpackage Zend_Measure_Flow_Mole
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Flow_Mole extends Zend_Measure_Abstract
{
    // Mole definitions
    const STANDARD = 'MOLE_PER_SECOND';

    const CENTIMOLE_PER_DAY    = 'CENTIMOLE_PER_DAY';
    const CENTIMOLE_PER_HOUR   = 'CENTIMOLE_PER_HOUR';
    const CENTIMOLE_PER_MINUTE = 'CENTIMOLE_PER_MINUTE';
    const CENTIMOLE_PER_SECOND = 'CENTIMOLE_PER_SECOND';
    const MEGAMOLE_PER_DAY     = 'MEGAMOLE_PER_DAY';
    const MEGAMOLE_PER_HOUR    = 'MEGAMOLE_PER_HOUR';
    const MEGAMOLE_PER_MINUTE  = 'MEGAMOLE_PER_MINUTE';
    const MEGAMOLE_PER_SECOND  = 'MEGAMOLE_PER_SECOND';
    const MICROMOLE_PER_DAY    = 'MICROMOLE_PER_DAY';
    const MICROMOLE_PER_HOUR   = 'MICROMOLE_PER_HOUR';
    const MICROMOLE_PER_MINUTE = 'MICROMOLE_PER_MINUTE';
    const MICROMOLE_PER_SECOND = 'MICROMOLE_PER_SECOND';
    const MILLIMOLE_PER_DAY    = 'MILLIMOLE_PER_DAY';
    const MILLIMOLE_PER_HOUR   = 'MILLIMOLE_PER_HOUR';
    const MILLIMOLE_PER_MINUTE = 'MILLIMOLE_PER_MINUTE';
    const MILLIMOLE_PER_SECOND = 'MILLIMOLE_PER_SECOND';
    const MOLE_PER_DAY         = 'MOLE_PER_DAY';
    const MOLE_PER_HOUR        = 'MOLE_PER_HOUR';
    const MOLE_PER_MINUTE      = 'MOLE_PER_MINUTE';
    const MOLE_PER_SECOND      = 'MOLE_PER_SECOND';

    private static $_UNITS = array(
        'CENTIMOLE_PER_DAY'    => array(array('' => 0.01, '/' => 86400),     'cmol/day'),
        'CENTIMOLE_PER_HOUR'   => array(array('' => 0.01, '/' => 3600),      'cmol/h'),
        'CENTIMOLE_PER_MINUTE' => array(array('' => 0.01, '/' => 60),        'cmol/m'),
        'CENTIMOLE_PER_SECOND' => array(0.01,     'cmol/s'),
        'MEGAMOLE_PER_DAY'     => array(array('' => 1000000, '/' => 86400),  'Mmol/day'),
        'MEGAMOLE_PER_HOUR'    => array(array('' => 1000000, '/' => 3600),   'Mmol/h'),
        'MEGAMOLE_PER_MINUTE'  => array(array('' => 1000000, '/' => 60),     'Mmol/m'),
        'MEGAMOLE_PER_SECOND'  => array(1000000,  'Mmol/s'),
        'MICROMOLE_PER_DAY'    => array(array('' => 0.000001, '/' => 86400), 'µmol/day'),
        'MICROMOLE_PER_HOUR'   => array(array('' => 0.000001, '/' => 3600),  'µmol/h'),
        'MICROMOLE_PER_MINUTE' => array(array('' => 0.000001, '/' => 60),    'µmol/m'),
        'MICROMOLE_PER_SECOND' => array(0.000001, 'µmol/s'),
        'MILLIMOLE_PER_DAY'    => array(array('' => 0.001, '/' => 86400),    'mmol/day'),
        'MILLIMOLE_PER_HOUR'   => array(array('' => 0.001, '/' => 3600),     'mmol/h'),
        'MILLIMOLE_PER_MINUTE' => array(array('' => 0.001, '/' => 60),       'mmol/m'),
        'MILLIMOLE_PER_SECOND' => array(0.001,    'mmol/s'),
        'MOLE_PER_DAY'         => array(array('' => 1, '/' => 86400),        'mol/day'),
        'MOLE_PER_HOUR'        => array(array('' => 1, '/' => 3600),         'mol/h'),
        'MOLE_PER_MINUTE'      => array(array('' => 1, '/' => 60),           'mol/m'),
        'MOLE_PER_SECOND'      => array(1,        'mol/s')
    );

    private $_Locale = null;

    /**
     * Zend_Measure_Flow_Mole provides an locale aware class for
     * conversion and formatting of Mole values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  integer|string      $value   Value as string, integer, real or float
     * @param  string              $type    OPTIONAL A Zend_Measure_Flow_Mole Type
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
     * @param  Zend_Measure_Flow_Mole  $object  Flow Mole object to compare
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
     * @param  string              $type    OPTIONAL A Zend_Measure_Flow_Mole Type
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
            throw new Zend_Measure_Exception("type ($type) is a unknown flow mole");
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
            throw new Zend_Measure_Exception("type ($type) is a unknown flow mole");
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
