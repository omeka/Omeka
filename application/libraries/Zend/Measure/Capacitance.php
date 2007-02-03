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
 * @version    $Id: Capacitance.php 2883 2007-01-18 05:56:31Z gavin $
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
 * @subpackage Zend_Measure_Capacitance
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure_Capacitance extends Zend_Measure_Abstract
{
    // Capacitance definitions
    const STANDARD = 'FARAD';

    const ABFARAD              = 'ABFARAD';
    const AMPERE_PER_SECOND_VOLT   = 'AMPERE_PER_SECOND_VOLT';
    const CENTIFARAD           = 'CENTIFARAD';
    const COULOMB_PER_VOLT         = 'COULOMB_PER_VOLT';
    const DECIFARAD            = 'DECIFARAD';
    const DEKAFARAD            = 'DEKAFARAD';
    const ELECTROMAGNETIC_UNIT = 'ELECTROMAGNETIC_UNIT';
    const ELECTROSTATIC_UNIT   = 'ELECTROSTATIC_UNIT';
    const FARAD                = 'FARAD';
    const FARAD_INTERNATIONAL  = 'FARAD_INTERNATIONAL';
    const GAUSSIAN             = 'GAUSSIAN';
    const GIGAFARAD            = 'GIGAFARAD';
    const HECTOFARAD           = 'HECTOFARAD';
    const JAR                  = 'JAR';
    const KILOFARAD            = 'KILOFARAD';
    const MEGAFARAD            = 'MEGAFARAD';
    const MICROFARAD           = 'MICROFARAD';
    const MILLIFARAD           = 'MILLIFARAD';
    const NANOFARAD            = 'NANOFARAD';
    const PICOFARAD            = 'PICOFARAD';
    const PUFF                 = 'PUFF';
    const SECOND_PER_OHM       = 'SECOND_PER_OHM';
    const STATFARAD            = 'STATFARAD';
    const TERAFARAD            = 'TERAFARAD';

    private static $_UNITS = array(
        'ABFARAD'              => array(1.0e+9,      'abfarad'),
        'AMPERE_PER_SECOND_VOLT' => array(1,         'A/sV'),
        'CENTIFARAD'           => array(0.01,        'cF'),
        'COULOMB_PER_VOLT'     => array(1,           'C/V'),
        'DECIFARAD'            => array(0.1,         'dF'),
        'DEKAFARAD'            => array(10,          'daF'),
        'ELECTROMAGNETIC_UNIT' => array(1.0e+9,      'capacity emu'),
        'ELECTROSTATIC_UNIT'   => array(1.11265e-12, 'capacity esu'),
        'FARAD'                => array(1,           'F'),
        'FARAD_INTERNATIONAL'  => array(0.99951,     'F'),
        'GAUSSIAN'             => array(1.11265e-12, 'G'),
        'GIGAFARAD'            => array(1.0e+9,      'GF'),
        'HECTOFARAD'           => array(100,         'hF'),
        'JAR'                  => array(1.11265e-9,  'jar'),
        'KILOFARAD'            => array(1000,        'kF'),
        'MEGAFARAD'            => array(1000000,     'MF'),
        'MICROFARAD'           => array(0.000001,    'µF'),
        'MILLIFARAD'           => array(0.001,       'mF'),
        'NANOFARAD'            => array(1.0e-9,      'nF'),
        'PICOFARAD'            => array(1.0e-12,     'pF'),
        'PUFF'                 => array(1.0e-12,     'pF'),
        'SECOND_PER_OHM'       => array(1,           's/Ohm'),
        'STATFARAD'            => array(1.11265e-12, 'statfarad'),
        'TERAFARAD'            => array(1.0e+12,     'TF')
    );

    private $_Locale = null;

    /**
     * Zend_Measure_Capacitance provides an locale aware class for
     * conversion and formatting of Capacitance values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  integer|string      $value   Value as string, integer, real or float
     * @param  string              $type    OPTIONAL A Zend_Measure_Capacitance Type
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
     * @param  Zend_Measure_Capacitance  $object  Capacitance object to compare
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
     * @param  string              $type    OPTIONAL A Zend_Measure_Capacitance Type
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
            throw new Zend_Measure_Exception("type ($type) is a unknown capacity");
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
            throw new Zend_Measure_Exception("type ($type) is a unknown capacity");
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
