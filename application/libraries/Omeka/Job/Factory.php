<?php
/**
 * @version $Id$
 * @copyright Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Factory for instantiating Omeka_Job instances.
 *
 * @package Omeka
 * @copyright Center for History and New Media, 2010
 */
class Omeka_Job_Factory
{
    private $_options = array();

    public function __construct($options)
    {
        $this->_options = $options;
    }

    /**
     * Decode a message from JSON and use the results to instantiate a new job 
     * instance.
     *
     * @param string $json
     */
    public function from($json)
    {
        $data = Zend_Json::decode($json);
        if (!$data) {
            throw new Omeka_Job_Factory_MalformedJobException("The following malformed job was given: $json");
        }
        if (!array_key_exists('className', $data)) {
            throw new Omeka_Job_Factory_MalformedJobException("No 'className' attribute was given in the message.");
        }
        if (!array_key_exists('options', $data)) {
            throw new Omeka_Job_Factory_MalformedJobException("No 'options' attribute was given in the message.");
        }
        $className = $data['className'];
        if (!class_exists($className, true)) {
            throw new Omeka_Job_Factory_MissingClassException("Job class named $className does not exist.");
        }
        $jobOptions = array_merge($data['options'], $this->_options);
        return new $className($jobOptions);
    }
}
