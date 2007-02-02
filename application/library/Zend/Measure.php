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
 * @version    $Id: Measure.php 2883 2007-01-18 05:56:31Z gavin $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Include needed Measure classes
 */
require_once 'Zend/Measure/Exception.php';
require_once 'Zend/Locale.php';


/**
 * @category   Zend
 * @package    Zend_Measure
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Measure
{

    const ACCELERATION   = 'ACCELERATION';
    const ANGLE          = 'ANGLE';
    const AREA           = 'AREA';
    const BINARY         = 'BINARY';
    const CAPACITANCE    = 'CAPACITANCE';
    const COOKING_VOLUME = 'COOKING_VOLUME';
    const COOKING_WEIGHT = 'COOKING_WEIGHT';
    const CURRENT        = 'CURRENT';
    const DENSITY        = 'DENSITY';
    const ENERGY         = 'ENERGY';
    const FORCE          = 'FORCE';
    const FLOW_MASS      = 'FLOW_MASS';
    const FLOW_MOLE      = 'FLOW_MOLE';
    const FLOW_VOLUME    = 'FLOW_VOLUME';
    const FREQUENCY      = 'FREQUENCY';
    const ILLUMINATION   = 'ILLUMINATION';
    const LENGTH         = 'LENGTH';
    const LIGHTNESS      = 'LIGHTNESS';
    const NUMBER         = 'NUMBER';
    const POWER          = 'POWER';
    const PRESSURE       = 'PRESSURE';
    const SPEED          = 'SPEED';
    const TEMPERATURE    = 'TEMPERATURE';
    const TORQUE         = 'TORQUE';
    const VISCOSITY_DYNAMIC   = 'VISCOSITY_DYNAMIC';
    const VISCOSITY_KINEMATIC = 'VISCOSITY_KINEMATIC';
    const VOLUME         = 'VOLUME';
    const WEIGHT         = 'WEIGHT';

    private static $_UNIT = array(
        'ACCELERATION'   => array('Acceleration' =>   'METER_PER_SQUARE_SECOND'),
        'ANGLE'          => array('Angle' =>          'RADIAN'),
        'AREA'           => array('Area' =>           'SQUARE_METER'),
        'BINARY'         => array('Binary' =>         'BYTE'),
        'CAPACITANCE'    => array('Capacitance' =>    'FARAD'),
        'COOKING_VOLUME' => array('Cooking_Volume' => 'CUBIC_METER'),
        'COOKING_WEIGHT' => array('Cooking_Weight' => 'GRAM'),
        'CURRENT'        => array('Current' =>        'AMPERE'),
        'DENSITY'        => array('Density' =>        'KILOGRAM_PER_CUBIC_METER'),
        'ENERGY'         => array('Energy' =>         'JOULE'),
        'FORCE'          => array('Force' =>          'NEWTON'),
        'FLOW_MASS'      => array('Flow_Mass' =>      'KILOGRAM_PER_SECOND'),
        'FLOW_MOLE'      => array('Flow_Mole' =>      'MOLE_PER_SECOND'),
        'FLOW_VOLUME'    => array('Flow_Volume' =>    'CUBIC_METER_PER_SECOND'),
        'FREQUENCY'      => array('Frequency' =>      'HERTZ'),
        'ILLUMINATION'   => array('Illumination' =>   'LUX'),
        'LENGTH'         => array('Length' =>         'METER'),
        'LIGHTNESS'      => array('Lightness' =>      'CANDELA_PER_SQUARE_METER'),
        'NUMBER'         => array('Number' =>         'DECIMAL'),
        'POWER'          => array('Power' =>          'WATT'),
        'PRESSURE'       => array('Pressure' =>       'NEWTON_PER_SQUARE_METER'),
        'SPEED'          => array('Speed' =>          'METER_PER_SECOND'),
        'TEMPERATURE'    => array('Temperature' =>    'KELVIN'),
        'TORQUE'         => array('Torque' =>         'NEWTON_METER'),
        'VISCOSITY_DYNAMIC'   => array('Viscosity_Dynamic' =>   'KILOGRAM_PER_METER_SECOND'),
        'VISCOSITY_KINEMATIC' => array('Viscosity_Kinematic' => 'SQUARE_METER_PER_SECOND'),
        'VOLUME'         => array('Volume' =>         'CUBIC_METER'),
        'WEIGHT'         => array('Weight' =>         'KILOGRAM')
    );

    private $_Measurement;
    private $_Locale;


    /**
     * Zend_Measure_Area provides an locale aware class for
     * conversion and formatting of area values
     *
     * Zend_Measure $input can be a locale based input string
     * or a value. $locale can be used to define that the
     * input is made in a different language than the actual one.
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Area Type
     * @param  $locale locale - OPTIONAL a Zend_Locale Type
     * @throws Zend_Measure_Exception
     */
    public function __construct($value, $type, $locale = false)
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

        if (strpos($type, '::') !== false) {
            $type = substr($type, 0, strpos($type, '::'));
            $sublib  = substr($type, strpos($type, '::') + 2);
        }

        if (!array_key_exists($type, self::$_UNIT)) {
            throw new Zend_Measure_Exception("type ($type) is unknown");
        }
        
        if (empty($sublib)) {
            $sublib = current(self::$_UNIT[$type]);
        }

        $library = 'Zend_Measure_' . key(self::$_UNIT[$type]);

        Zend::loadClass($library);
        $this->_Measurement = new $library($value, $sublib, $locale);
    }


    /**
     * Serialize
     */
    public function serialize()
    {
        return serialize($this);
    }


    /**
     * Compare if the value and type is equal
     *
     * @param $object  object to compare equality
     * @return boolean
     */
    public function equals($object)
    {
        return $this->_Measurement->equals($object);
    }


    /**
     * Returns the internal value
     *
     * @return value  mixed
     */
    public function getValue()
    {
        return $this->_Measurement->getValue();
    }


    /**
     * Set a new value
     *
     * @param  $value  mixed  - Value as string, integer, real or float
     * @param  $type   type   - OPTIONAL a Zend_Measure_Temperature Type
     * @param  $locale locale - OPTIONAL a Zend_Locale Type
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

        if (array_key_exists($type, self::$_UNIT)) {
            $type = current(self::$_UNIT[$type]);
        }

        $this->_Measurement->setValue($value, $type, $locale);
    }


    /**
     * Returns the original type
     *
     * @return type mixed
     */
    public function getType()
    {
        return $this->_Measurement->getType();
    }


    /**
     * Set a new type, and convert the value
     *
     * @param $type  new type to set
     * @throws Zend_Measure_Exception
     */
    public function setType($type)
    {
        $library = substr($type, 0, strpos($type, '::'));

        if ($library == 'Zend_Measure')
        {
            $library = $library . '_' . key(self::$_UNIT[$type]);
            $type = key(self::$_UNIT[$type]) . '::' . current(self::$_UNIT[$type]);
        }

        $this->_Measurement->setType($type);
    }


    /**
     * Returns a string representation
     *
     * @return string
     */
    public function toString()
    {
        return $this->_Measurement->__toString();
    }


    /**
     * Returns a string representation
     * Alias for toString()
     */
    public function __toString()
    {
        return $this->toString();
    }


    /**
     * Alias function for setType returning the converted unit
     *
     * @param $type  type
     * @return
     */
    public function convertTo($type)
    {
        $this->setType($type);
        return $this->toString();
    }


    /**
     * Adds an unit to another one
     *
     * @param $object  object of same unit type
     * @return  Zend_Measure object
     */
    public function add($object)
    {
        $object->setType($this->getType());
        $value  = $this->getValue() + $object->getValue();

        $this->setValue($value, $this->getType(), $this->_Locale);
        return $this;
    }


    /**
     * Substracts an unit from another one
     *
     * @param $object  object of same unit type
     * @return  Zend_Measure object
     */
    public function sub($object)
    {
        $object->setType($this->getType());
        $value  = $this->getValue() - $object->getValue();
        
        $this->setValue($value, $this->getType(), $this->_Locale);
        return $this;
    }


    /**
     * Compares two units
     *
     * @param $object  object of same unit type
     * @return object
     */
    public function compare($object)
    {
        $object->setType($this->getType());
        $value  = $this->getValue() - $object->getValue();

        return $value;
    }


    /**
     * Returns a list of all types
     *
     * @return array
     */
    public function getAllTypes()
    {
        foreach(self::$_UNIT as $temp) {
          $types[] = key($temp);
        }

        return $types;
    }


    /**
     * Returns a list of all types from a unit
     *
     * @return array
     */
    public function getTypeList()
    {
        $values = $this->_Measurement->getConversionList();
        return $values;
    }
}
