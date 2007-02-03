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
 * @version    $Id: Frequency.php 2883 2007-01-18 05:56:31Z gavin $
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
 * @subpackage Zend_Measure_Frequency
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Frequency extends Zend_Measure_Abstract
{
    // Frequency definitions
    const STANDARD = 'HERTZ';

    const ONE_PER_SECOND        = 'ONE_PER_SECOND';
    const CYCLE_PER_SECOND      = 'CYCLE_PER_SECOND';
    const DEGREE_PER_HOUR       = 'DEGREE_PER_HOUR';
    const DEGREE_PER_MINUTE     = 'DEGREE_PER_MINUTE';
    const DEGREE_PER_SECOND     = 'DEGREE_PER_SECOND';
    const GIGAHERTZ             = 'GIGAHERTZ';
    const HERTZ                 = 'HERTZ';
    const KILOHERTZ             = 'KILOHERTZ';
    const MEGAHERTZ             = 'MEGAHERTZ';
    const MILLIHERTZ            = 'MILLIHERTZ';
    const RADIAN_PER_HOUR       = 'RADIAN_PER_HOUR';
    const RADIAN_PER_MINUTE     = 'RADIAN_PER_MINUTE';
    const RADIAN_PER_SECOND     = 'RADIAN_PER_SECOND';
    const REVOLUTION_PER_HOUR   = 'REVOLUTION_PER_HOUR';
    const REVOLUTION_PER_MINUTE = 'REVOLUTION_PER_MINUTE';
    const REVOLUTION_PER_SECOND = 'REVOLUTION_PER_SECOND';
    const RPM                   = 'RPM';
    const TERRAHERTZ            = 'TERRAHERTZ';

    private static $_UNITS = array(
        'ONE_PER_SECOND'        => array(1,             '1/s'),
        'CYCLE_PER_SECOND'      => array(1,             'cps'),
        'DEGREE_PER_HOUR'       => array(array('' => 1, '/' => 1296000), '°/h'),
        'DEGREE_PER_MINUTE'     => array(array('' => 1, '/' => 21600),   '°/m'),
        'DEGREE_PER_SECOND'     => array(array('' => 1, '/' => 360),     '°/s'),
        'GIGAHERTZ'             => array(1000000000,    'GHz'),
        'HERTZ'                 => array(1,             'Hz'),
        'KILOHERTZ'             => array(1000,          'kHz'),
        'MEGAHERTZ'             => array(1000000,       'MHz'),
        'MILLIHERTZ'            => array(0.001,         'mHz'),
        'RADIAN_PER_HOUR'       => array(array('' => 1, '/' => 22619.467), 'rad/h'),
        'RADIAN_PER_MINUTE'     => array(array('' => 1, '/' => 376.99112), 'rad/m'),
        'RADIAN_PER_SECOND'     => array(array('' => 1, '/' => 6.2831853), 'rad/s'),
        'REVOLUTION_PER_HOUR'   => array(array('' => 1, '/' => 3600), 'rph'),
        'REVOLUTION_PER_MINUTE' => array(array('' => 1, '/' => 60),   'rpm'),
        'REVOLUTION_PER_SECOND' => array(1,             'rps'),
        'RPM'                   => array(array('' => 1, '/' => 60), 'rpm'),
        'TERRAHERTZ'            => array(1000000000000, 'THz')
    );

    private $_Locale = null;

    /**
     * Zend_Measure_Frequency provides an locale aware class for
     * conversion and formatting of Frequency values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  integer|string      $value   Value as string, integer, real or float
     * @param  string              $type    OPTIONAL A Zend_Measure_Frequency Type
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
     * @param  Zend_Measure_Frequency  $object  Frequency object to compare
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
     * @param  string              $type    OPTIONAL A Zend_Measure_Frequency Type
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
            throw new Zend_Measure_Exception("type ($type) is a unknown frequency");
        }

        parent::setValue($value, $type, $locale);
        parent::setType( $type );
    }


    /**
     * Set a new type, and convert the value
     *
     * @param  string  $type  New type to set
     * @throws Zend_Measure_Exception
     */
    public function setType( $type )
    {
        if (empty(self::$_UNITS[$type])) {
            throw new Zend_Measure_Exception("type ($type) is a unknown frequency");
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
