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
 * @version    $Id: Lightness.php 2883 2007-01-18 05:56:31Z gavin $
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
 * @subpackage Zend_Measure_Lightness
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Lightness extends Zend_Measure_Abstract
{
    // Lightness definitions
    const STANDARD               = 'CANDELA_PER_SQUARE_METER';

    const APOSTILB                      = 'APOSTILB';
    const BLONDEL                       = 'BLONDEL';
    const CANDELA_PER_SQUARE_CENTIMETER = 'CANDELA_PER_SQUARE_CENTIMETER';
    const CANDELA_PER_SQUARE_FOOT       = 'CANDELA_PER_SQUARE_FOOT';
    const CANDELA_PER_SQUARE_INCH       = 'CANDELA_PER_SQUARE_INCH';
    const CANDELA_PER_SQUARE_METER      = 'CANDELA_PER_SQUARE_METER';
    const FOOTLAMBERT                   = 'FOOTLAMBERT';
    const KILOCANDELA_PER_SQUARE_CENTIMETER = 'KILOCANDELA_PER_SQUARE_CENTIMETER';
    const KILOCANDELA_PER_SQUARE_FOOT   = 'KILOCANDELA_PER_SQUARE_FOOT';
    const KILOCANDELA_PER_SQUARE_INCH   = 'KILOCANDELA_PER_SQUARE_INCH';
    const KILOCANDELA_PER_SQUARE_METER  = 'KILOCANDELA_PER_SQUARE_METER';
    const LAMBERT                       = 'LAMBERT';
    const MILLIMALBERT                  = 'MILLILAMBERT';
    const NIT                           = 'NIT';
    const STILB                         = 'STILB';

    private static $_UNITS = array(
        'APOSTILB'                      => array(0.31830989,   'asb'),
        'BLONDEL'                       => array(0.31830989,   'blondel'),
        'CANDELA_PER_SQUARE_CENTIMETER' => array(10000,        'cd/cm²'),
        'CANDELA_PER_SQUARE_FOOT'       => array(10.76391,     'cd/ft²'),
        'CANDELA_PER_SQUARE_INCH'       => array(1550.00304,   'cd/in²'),
        'CANDELA_PER_SQUARE_METER'      => array(1,            'cd/m²'),
        'FOOTLAMBERT'                   => array(3.4262591,    'ftL'),
        'KILOCANDELA_PER_SQUARE_CENTIMETER' => array(10000000, 'kcd/cm²'),
        'KILOCANDELA_PER_SQUARE_FOOT'   => array(10763.91,     'kcd/ft²'),
        'KILOCANDELA_PER_SQUARE_INCH'   => array(1550003.04,   'kcd/in²'),
        'KILOCANDELA_PER_SQUARE_METER'  => array(1000,         'kcd/m²'),
        'LAMBERT'                       => array(3183.0989,    'L'),
        'MILLILAMBERT'                  => array(3.1830989,    'mL'),
        'NIT'                           => array(1,            'nt'),
        'STILB'                         => array(10000,        'sb')
    );

    private $_Locale = null;

    /**
     * Zend_Measure_Lightness provides an locale aware class for
     * conversion and formatting of Lightness values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  integer|string      $value   Value as string, integer, real or float
     * @param  string              $type    OPTIONAL A Zend_Measure_Lightness Type
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
     * @param  Zend_Measure_Lightness  $object  Lightness object to compare
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
     * @param  string              $type    OPTIONAL A Zend_Measure_Lightness Type
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
            throw new Zend_Measure_Exception("type ($type) is a unknown lightness");
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
            throw new Zend_Measure_Exception("type ($type) is a unknown lightness");
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
