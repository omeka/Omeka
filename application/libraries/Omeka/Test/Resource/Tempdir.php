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
class Omeka_Test_Resource_Tempdir extends Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        if ($bootstrap = $this->getBootstrap()) {
            $bootstrap->bootstrap('Config');
            $config = Zend_Registry::get('test_config');
        }

        if (!empty($config->tempDir)) {
            $tempDir = $config->tempDir;
        } else {
            $tempDir = APP_DIR . '/tests/_files/temp';
        }

        $this->cleanDir($tempDir);

        return $tempDir;
    }

    public function cleanDir($dir)
    {
        $filenames = scandir($dir);
        foreach ($filenames as $filename) {
            if ($filename == '.' || $filename == '..' || $filename == '.keep') {
                continue;
            }
            $path = "$dir/$filename";
            if (is_dir($path)) {
                $this->cleanDir($path);
                rmdir($path);
            } else {
                unlink($path);
            }
        }
    }
}
