<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 * @access private
 */

/**
 * Mask the behavior of Omeka_Core_Resource_Currentuser in tests. 
 * 
 * @internal This implements Omeka internals and is not part of the public API.
 * @access private
 * @package Omeka
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 */
class Omeka_Test_Resource_Currentuser extends Zend_Application_Resource_ResourceAbstract
{
    public function init() {}
}
