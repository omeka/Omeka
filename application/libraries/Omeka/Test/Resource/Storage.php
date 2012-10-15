<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Bootstrap resource for storage in test environment.
 * 
 * @package Omeka\Test\Resource
 */
class Omeka_Test_Resource_Storage extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $bootstrap = $this->getBootstrap();
        if (!$bootstrap->hasPluginResource('Tempdir')) {
            $bootstrap->registerPluginResource('Tempdir');
        }
        $bootstrap->bootstrap('Tempdir');
        $tempDir = $bootstrap->getResource('Tempdir');

        $storageDirName = $tempDir . '/storage_files_' . mt_rand();
        mkdir($storageDirName);
        $storage = new Omeka_Storage(array(
            Omeka_Storage::OPTION_ADAPTER => 'Omeka_Storage_Adapter_TempFilesystem',
            Omeka_Storage::OPTION_TEMP_DIR => $tempDir,
            Omeka_Storage::OPTION_ADAPTER_OPTIONS => array(
                'localDir' => "$tempDir/$storageDirName",
                'webDir' => '/'
            )
        ));
        Zend_Registry::set('storage', $storage);
        return $storage;
    }
}
