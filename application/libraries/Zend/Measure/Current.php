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
 * @version    $Id: Current.php 2883 2007-01-18 05:56:31Z gavin $
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
 * @subpackage Zend_Measure_Current
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Current extends Zend_Measure_Abstract
{
    // Current definitions
    const STANDARD = 'AMPERE';

    const ABAMPERE             = 'ABAMPERE';
    const AMPERE               = 'AMPERE';
    const BIOT                 = 'BIOT';
    const CENTIAMPERE          = 'CENTIAMPERE';
    const COULOMB_PER_SECOND   = 'COULOMB_PER_SECOND';
    const DECIAMPERE           = 'DECIAMPERE';
    const DEKAAMPERE           = 'DEKAAMPERE';
    const ELECTROMAGNETIC_UNIT = 'ELECTROMAGNATIC_UNIT';
    const ELECTROSTATIC_UNIT   = 'ELECTROSTATIC_UNIT';
    const FRANCLIN_PER_SECOND  = 'FRANCLIN_PER_SECOND';
    const GAUSSIAN             = 'GAUSSIAN';
    const GIGAAMPERE           = 'GIGAAMPERE';
    const GILBERT              = 'GILBERT';
    const HECTOAMPERE          = 'HECTOAMPERE';
    const KILOAMPERE           = 'KILOAMPERE';
    const MEGAAMPERE           = 'MEGAAMPERE';
    const MICROAMPERE          = 'MICROAMPERE';
    const MILLIAMPERE          = 'MILLIAMPERE';
    const NANOAMPERE           = 'NANOAMPERE';
    const PICOAMPERE           = 'PICOAMPERE';
    const SIEMENS_VOLT         = 'SIEMENS_VOLT';
    const STATAMPERE           = 'STATAMPERE';
    const TERAAMPERE           = 'TERAAMPERE';
    const VOLT_PER_OHM         = 'VOLT_PER_OHM';
    const WATT_PER_VOLT        = 'WATT_PER_VOLT';
    const WEBER_PER_HENRY      = 'WEBER_PER_HENRY';

    private static $_UNITS = array(
        'ABAMPERE'             => array(10,           'abampere'),
        'AMPERE'               => array(1,            'A'),
        'BIOT'                 => array(10,           'Bi'),
        'CENTIAMPERE'          => array(0.01,         'cA'),
        'COULOMB_PER_SECOND'   => array(1,            'C/s'),
        'DECIAMPERE'           => array(0.1,          'dA'),
        'DEKAAMPERE'           => array(10,           'daA'),
        'ELECTROMAGNATIC_UNIT' => array(10,           'current emu'),
        'ELECTROSTATIC_UNIT'   => array(3.335641e-10, 'current esu'),
        'FRANCLIN_PER_SECOND'  => array(3.335641e-10, 'Fr/s'),
        'GAUSSIAN'             => array(3.335641e-10, 'G current'),
        'GIGAAMPERE'           => array(1.0e+9,       'GA'),
        'GILBERT'              => array(0.79577472,   'Gi'),
        'HECTOAMPERE'          => array(100,          'hA'),
        'KILOAMPERE'           => array(1000,         'kA'),
        'MEGAAMPERE'           => array(1000000,      'MA') ,
        'MICROAMPERE'          => array(0.000001,     'µA'),
        'MILLIAMPERE'          => array(0.001,        'mA'),
        'NANOAMPERE'           => array(1.0e-9,       'nA'),
        'PICOAMPERE'           => array(1.0e-12,      'pA'),
        'SIEMENS_VOLT'         => array(1,            'SV'),
        'STATAMPERE'           => array(3.335641e-10, 'statampere'),
        'TERAAMPERE'           => array(1.0e+12,      'TA'),
        'VOLT_PER_OHM'         => array(1,            'V/Ohm'),
        'WATT_PER_VOLT'        => array(1,            'W/V'),
        'WEBER_PER_HENRY'      => array(1,            'Wb/H')
    );

    private $_Locale = null;

    /**
     * Zend_Measure_Current provides an locale aware class for
     * conversion and formatting of current values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  integer|string      $value   Value as string, integer, real or float
     * @param  string              $type    OPTIONAL A Zend_Measure_Current Type
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
     * @param  Zend_Measure_Current  $object  Current object to compare
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
     * @param  string              $type    OPTIONAL A Zend_Measure_Current Type
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
            throw new Zend_Measure_Exception("type ($type) is a unknown current");
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
            throw new Zend_Measure_Exception("type ($type) is a unknown current");
        }

        // Convert to standard value
        $value = parent::getValue();
        $value = $value * (self::$_UNITS[parent::getType()][0]);

        // Convert to expected value
        $value = $value / (self::$_UNITS[$type][0]);
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
