<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Exception thrown when required options have not been passed to the 
 * Omeka_Job_Dispatcher_Adapter's constructor.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 */
class Omeka_Job_Dispatcher_Adapter_RequiredOptionException extends LogicException implements Omeka_Job_Exception 
{}
