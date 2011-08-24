<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2011
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Bootstrap resource for storage in test environment.
 */
class Omeka_Test_Resource_Storage extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $storage = new Omeka_Storage(array(
            Omeka_Storage::OPTION_ADAPTER => 'Omeka_Storage_Adapter_TempFilesystem',
        ));
        Zend_Registry::set('storage', $storage);
        return $storage;
    }
}
