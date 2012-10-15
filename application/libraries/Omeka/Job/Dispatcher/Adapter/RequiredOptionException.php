<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Exception thrown when required options have not been passed to the 
 * Omeka_Job_Dispatcher_Adapter_AdapterInterface's constructor.
 * 
 * @package Omeka\Job\Dispatcher\Adapter
 */
class Omeka_Job_Dispatcher_Adapter_RequiredOptionException extends LogicException
{}
