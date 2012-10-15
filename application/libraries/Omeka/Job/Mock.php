<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Mock job class for unit tests.
 * 
 * @package Omeka\Job
 */
class Omeka_Job_Mock extends Omeka_Job_AbstractJob
{
    public $options;
    public $performed;

    public function __construct(array $options)
    {
        $this->options = $options;
        parent::__construct($options);
    }

    public function perform()
    {
        $this->performed = true;
    }

    /**
     * Getter method to expose protected properties.
     */
    public function getDb()
    {
        return $this->_db;
    }

    /**
     * Getter method to expose protected properties.
     */
    public function getDispatcher()
    {
        return $this->_dispatcher;
    }

    public function getMiscOptions()
    {
        return $this->_options;
    }
}
