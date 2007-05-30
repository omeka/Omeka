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
 * @version    $Id: Binary.php 2883 2007-01-18 05:56:31Z gavin $
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
 * @subpackage Zend_Measure_Binary
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 */
class Zend_Measure_Binary extends Zend_Measure_Abstract
{
    // Binary definitions
    const STANDARD = 'BYTE';

    const BIT              = 'BIT';
    const CRUMB            = 'CRUMB';
    const NIBBLE           = 'NIBBLE';
    const BYTE             = 'BYTE';
    const KILOBYTE         = 'KILOBYTE';
    const KIBIBYTE         = 'KIBIBYTE';
    const KILO_BINARY_BYTE = 'KILO_BINARY_BYTE';
    const KILOBYTE_SI      = 'KILOBYTE_SI';
    const MEGABYTE         = 'MEGABYTE';
    const MEBIBYTE         = 'MEBIBYTE';
    const MEGA_BINARY_BYTE = 'MEGA_BINARY_BYTE';
    const MEGABYTE_SI      = 'MEGABYTE_SI';
    const GIGABYTE         = 'GIGABYTE';
    const GIBIBYTE         = 'GIBIBYTE';
    const GIGA_BINARY_BYTE = 'GIGA_BINARY_BYTE';
    const GIGABYTE_SI      = 'GIGABYTE_SI';
    const TERABYTE         = 'TERABYTE';
    const TEBIBYTE         = 'TEBIBYTE';
    const TERA_BINARY_BYTE = 'TERA_BINARY_BYTE';
    const TERABYTE_SI      = 'TERABYTE_SI';
    const PETABYTE         = 'PETABYTE';
    const PEBIBYTE         = 'PEBIBYTE';
    const PETA_BINARY_BYTE = 'PETA_BINARY_BYTE';
    const PETABYTE_SI      = 'PETABYTE_SI';
    const EXABYTE          = 'EXABYTE';
    const EXBIBYTE         = 'EXBIBYTE';
    const EXA_BINARY_BYTE  = 'EXA_BINARY_BYTE';
    const EXABYTE_SI       = 'EXABYTE_SI';
    const ZETTABYTE        = 'ZETTABYTE';
    const ZEBIBYTE         = 'ZEBIBYTE';
    const ZETTA_BINARY_BYTE= 'ZETTA_BINARY_BYTE';
    const ZETTABYTE_SI     = 'ZETTABYTE_SI';
    const YOTTABYTE        = 'YOTTABYTE';
    const YOBIBYTE         = 'YOBIBYTE';
    const YOTTA_BINARY_BYTE= 'YOTTA_BINARY_BYTE';
    const YOTTABYTE_SI     = 'YOTTABYTE_SI';

    private static $_UNITS = array(
        'BIT'              => array('0.125',                     'b'),
        'CRUMB'            => array('0.25',                      'crumb'),
        'NIBBLE'           => array('0.5',                       'nibble'),
        'BYTE'             => array('1',                         'B'),
        'KILOBYTE'         => array('1024',                      'kB'),
        'KIBIBYTE'         => array('1024',                      'KiB'),
        'KILO_BINARY_BYTE' => array('1024',                      'KiB'),
        'KILOBYTE_SI'      => array('1000',                      'kB.'),
        'MEGABYTE'         => array('1048576',                   'MB'),
        'MEBIBYTE'         => array('1048576',                   'MiB'),
        'MEGA_BINARY_BYTE' => array('1048576',                   'MiB'),
        'MEGABYTE_SI'      => array('1000000',                   'MB.'),
        'GIGABYTE'         => array('1073741824',                'GB'),
        'GIBIBYTE'         => array('1073741824',                'GiB'),
        'GIGA_BINARY_BYTE' => array('1073741824',                'GiB'),
        'GIGABYTE_SI'      => array('1000000000',                'GB.'),
        'TERABYTE'         => array('1099511627776',             'TB'),
        'TEBIBYTE'         => array('1099511627776',             'TiB'),
        'TERA_BINARY_BYTE' => array('1099511627776',             'TiB'),
        'TERABYTE_SI'      => array('1000000000000',             'TB.'),
        'PETABYTE'         => array('1125899906842624',          'PB'),
        'PEBIBYTE'         => array('1125899906842624',          'PiB'),
        'PETA_BINARY_BYTE' => array('1125899906842624',          'PiB'),
        'PETABYTE_SI'      => array('1000000000000000',          'PB.'),
        'EXABYTE'          => array('1152921504606846976',       'EB'),
        'EXBIBYTE'         => array('1152921504606846976',       'EiB'),
        'EXA_BINARY_BYTE'  => array('1152921504606846976',       'EiB'),
        'EXABYTE_SI'       => array('1000000000000000000',       'EB.'),
        'ZETTABYTE'        => array('1180591620717411303424',    'ZB'),
        'ZEBIBYTE'         => array('1180591620717411303424',    'ZiB'),
        'ZETTA_BINARY_BYTE'=> array('1180591620717411303424',    'ZiB'),
        'ZETTABYTE_SI'     => array('1000000000000000000000',    'ZB.'),
        'YOTTABYTE'        => array('1208925819614629174706176', 'YB'),
        'YOBIBYTE'         => array('1208925819614629174706176', 'YiB'),
        'YOTTA_BINARY_BYTE'=> array('1208925819614629174706176', 'YiB'),
        'YOTTABYTE_SI'     => array('1000000000000000000000000', 'YB.')
    );

    private $_Locale = null;

    /**
     * Zend_Measure_Binary provides an locale aware class for
     * conversion and formatting of Binary values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  integer|string      $value   Value as string, integer, real or float
     * @param  string              $type    OPTIONAL A Zend_Measure_Binary Type
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
     * @param  Zend_Measure_Binary  $object  Binary object to compare
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
     * @param  string              $type    OPTIONAL A Zend_Measure_Binary Type
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
            throw new Zend_Measure_Exception("type ($type) is a unknown binary");
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
    public function setType($type)
    {
        if (empty( self::$_UNITS[$type] )) {
            throw new Zend_Measure_Exception("type ($type) is a unknown binary");
        }

        // Convert to standard value
        $value = parent::getValue();
        $value = call_user_func(Zend_Locale_Math::$mul, $value, self::$_UNITS[parent::getType()][0], 25);

        // Convert to expected value
        $value = call_user_func(Zend_Locale_Math::$div, $value, self::$_UNITS[$type][0]);
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
