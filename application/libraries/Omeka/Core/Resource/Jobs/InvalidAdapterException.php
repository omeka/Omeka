<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Exception thrown when an invalid job dispatcher has been configured.
 *
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 */
class Omeka_Core_Resource_Jobs_InvalidAdapterException extends InvalidArgumentException implements Omeka_Core_Resource_Exception 
{}
