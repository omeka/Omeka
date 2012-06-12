<?php
/**
 * @copyright Roy Rosenzweig Center for History and New Media, 2010
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @package Omeka
 */

/**
 * Bootstrap resource for configuring the file storage layer.
 *
 * @package Omeka
 */
class Omeka_Core_Resource_Storage extends Zend_Application_Resource_ResourceAbstract
{
    const DEFAULT_ADAPTER = "Omeka_Storage_Adapter_Filesystem";

    public function init()
    {
        $this->getBootstrap()->bootstrap('Config');
        
        $config = $this->getBootstrap()->config->storage;

        if ($config) {
            $storageOptions = $config->toArray();
        } else {
            $storageOptions = array();
        }

        if (empty($storageOptions[Omeka_Storage::OPTION_ADAPTER])) {
            $storageOptions[Omeka_Storage::OPTION_ADAPTER] = self::DEFAULT_ADAPTER;
        }

        $storage = new Omeka_Storage($storageOptions);
        Zend_Registry::set('storage', $storage);
        return $storage;
    }
}
