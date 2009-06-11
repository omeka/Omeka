<?php

/**
* 
*/
class Omeka_Test_Resource_Options extends Zend_Application_Resource_ResourceAbstract
{
    protected $_dbOptions = array();
        
    /**
     * Set some default options for the test cases to use.
     * 
     * @return array
     **/
    public function init()
    {   
        return $this->_dbOptions;
    }
    
    /**
     * Insanely confusing, but Zend bootstrap resources require a 'setOptions'
     * method which has a purpose that is a superset of what we might imagine is
     * the purpose of this method.
     * 
     * @return void
     **/
    public function setOptions(array $options)
    {
        if (array_key_exists('options', $options)) {
            $this->_dbOptions = $options['options'];
        }
    }
}
