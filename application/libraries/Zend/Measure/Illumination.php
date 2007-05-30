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
 * @version    $Id: Illumination.php 2883 2007-01-18 05:56:31Z gavin $
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
 * @subpackage Zend_Measure_Illumination
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Illumination extends Zend_Measure_Abstract
{
    // Illumination definitions
    const STANDARD = 'LUX';

    const FOOTCANDLE              = 'FOOTCANDLE';
    const KILOLUX                 = 'KILOLUX';
    const LUMEN_PER_SQUARE_CENTIMETER = 'LUMEN_PER_SQUARE_CENTIMETER';
    const LUMEN_PER_SQUARE_FOOT   = 'LUMEN_PER_SQUARE_FOOT';
    const LUMEN_PER_SQUARE_INCH   = 'LUMEN_PER_SQUARE_INCH';
    const LUMEN_PER_SQUARE_METER  = 'LUMEN_PER_SQUARE_METER';
    const LUX                     = 'LUX';
    const METERCANDLE             = 'METERCANDLE';
    const MILLIPHOT               = 'MILLIPHOT';
    const NOX                     = 'NOX';
    const PHOT                    = 'PHOT';

    private static $_UNITS = array(
        'FOOTCANDLE'              => array(10.7639104,   'fc'),
        'KILOLUX'                 => array(1000,         'klx'),
        'LUMEN_PER_SQUARE_CENTIMETER' => array(10000,    'lm/cm²'),
        'LUMEN_PER_SQUARE_FOOT'   => array(10.7639104,   'lm/ft²'),
        'LUMEN_PER_SQUARE_INCH'   => array(1550.0030976, 'lm/in²'),
        'LUMEN_PER_SQUARE_METER'  => array(1,            'lm/m²'),
        'LUX'                     => array(1,            'lx'),
        'METERCANDLE'             => array(1,            'metercandle'),
        'MILLIPHOT'               => array(10,           'mph'),
        'NOX'                     => array(0.001,        'nox'),
        'PHOT'                    => array(10000,        'ph')
    );

    private $_Locale;

    /**
     * Zend_Measure_Illumination provides an locale aware class for
     * conversion and formatting of Illumination values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  integer|string      $value   Value as string, integer, real or float
     * @param  string              $type    OPTIONAL A Zend_Measure_Illumination Type
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
     * @param  Zend_Measure_Illumination  $object  Illumination object to compare
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
     * @param  string              $type    OPTIONAL A Zend_Measure_Illumination Type
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
            throw new Zend_Measure_Exception("type ($type) is a unknown illumination");
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
        if (empty( self::$_UNITS[$type] )) {
            throw new Zend_Measure_Exception("type ($type) is a unknown illumination");
        }

        // Convert to standard value
        $value = parent::getValue();
        $value = $value * (self::$_UNITS[parent::getType()][0]);

        // Convert to expected value
        $value = $value / (self::$_UNITS[$type][0]);
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
