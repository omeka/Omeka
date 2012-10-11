<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Exception thrown when an invalid job dispatcher has been configured.
 * 
 * @package Omeka\Application\Resource
 */
class Omeka_Application_Resource_Jobs_InvalidAdapterException extends InvalidArgumentException 
    implements Omeka_Application_Resource_Exception 
{}
