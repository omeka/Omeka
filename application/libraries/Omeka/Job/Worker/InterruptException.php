<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Exception thrown when the type of job could not be inferred from the message 
 * passed to the factory.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 */
class Omeka_Job_Worker_InterruptException extends Exception implements Omeka_Job_Exception 
{}
