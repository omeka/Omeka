<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Exception thrown when the type of job could not be inferred from the message 
 * passed to the factory.
 * 
 * @package Omeka\Job\Worker
 */
class Omeka_Job_Worker_InterruptException extends Exception
{}
