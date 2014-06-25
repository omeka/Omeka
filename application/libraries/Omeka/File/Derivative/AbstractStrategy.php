<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Abstract class for pluggable file derivative creation strategies.
 *
 * @package Omeka\File\Derivative\Strategy
 */
abstract class Omeka_File_Derivative_AbstractStrategy
    implements Omeka_File_Derivative_StrategyInterface
{
    protected $_options = array();

    /**
     * Set options for the derivative strategy.
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->_options = $options;
    }

    /**
     * Get the options for the strategy.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Get the value for the specified option.
     *
     * @param string $name Name of the option to get
     * @param mixed $default Default value to return if the option is missing.
     *  Defaults to null.
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        if (array_key_exists($name, $this->_options)) {
            return $this->_options[$name];
        } else {
            return $default;
        }
    }
}
