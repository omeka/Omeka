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
 * @version    $Id: Abstract.php 2883 2007-01-18 05:56:31Z gavin $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage Zend_Measure_Abstract
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Measure_Abstract //implements Serializable
{

    /**
     * internal plain value in standard unit
     */
    private $_value;


    /**
     * internal original type for this unit
     */
    private $_type;


    /**
     * Returns the internal value
     */
    public function getValue()
    {
        return $this->_value;
    }


    /**
     * Sets the internal value
     * 
     * @param $value  new value to set
     * @param $type   new type to set   - abstract
     * @param $locale new locale to set - abstract
     */
    protected function setValue($value, $type = null, $locale = null)
    {
        $this->_value = $value;
    }


    /**
     * Returns the original type
     * 
     * @return type
     */
    public function getType()
    {
        return $this->_type;
    }


    /**
     * Sets the original type
     * 
     * @param $type  new type to set
     */
    protected function setType($type)
    {
        $this->_type = $type;
    }


    /**
     * Serialize
     * 
     * @return serialize
     */
    public function serialize() {
        return serialize($this);
    }


    /**
     * Compare if the value and type is equal
     *
     * @param  $object  object to equal with
     * @return boolean
     */
    abstract public function equals($object);


    /**
     * Returns a string representation
     *
     * @return string
     */
    abstract public function toString();


    /**
     * Returns a string representation
     *
     * @return string
     */
    abstract public function __toString();



    /**
     * Returns the conversion list
     * 
     * @return array
     */
    abstract public function getConversionList();
}
