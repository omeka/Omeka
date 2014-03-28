<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Abstract class for pluggable file derivative creation strategies.
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
    public function setOptions(array $options) {
        $this->_options = $options;
    }

    /**
     * Get the options for the strategy.
     *
     * @return array
     */
    public function getOptions() {
        return $this->_options;
    }

    /**
     * Get the value for the specified option.
     *
     * @return mixed
     */
    public function getOption($name) {
        if (array_key_exists($name, $this->_options)) {
            return $this->_options[$name];
        } else {
            return null;
        }
    }
}
