<?php
/**
 * Omeka
 * 
 * @copyright Copyright 2007-2012 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * @package Omeka\Install
 */
class Installer_Requirements
{
    const OMEKA_PHP_VERSION = '5.2.11';
    const OMEKA_MYSQL_VERSION = '5.0';
    
    private $_dbAdapter;
    private $_storage;
    
    private $_errorMessages = array();
    private $_warningMessages = array();
    
    public function check()
    {
        $this->_checkPhpVersionIsValid();
        $this->_checkMysqliIsAvailable();
        $this->_checkMysqlVersionIsValid();
        $this->_checkHtaccessFilesExist();
        $this->_checkRegisterGlobalsIsOff();
        $this->_checkExifModuleIsLoaded();
        $this->_checkFileStorageSetup();
        $this->_checkFileinfoIsLoaded();
    }
    
    public function getErrorMessages()
    {
        return $this->_errorMessages;
    }
    
    public function getWarningMessages()
    {
        return $this->_warningMessages;
    }
    
    public function hasError()
    {
        return (boolean)count($this->getErrorMessages());
    }

    public function hasWarning()
    {
        return (boolean)count($this->getWarningMessages());
    }
    
    public function setDbAdapter(Zend_Db_Adapter_Abstract $db)
    {
        $this->_dbAdapter = $db;
    }

    public function setStorage(Omeka_Storage $storage)
    {
        $this->_storage = $storage;
    }
    
    private function _checkPhpVersionIsValid()
    {
        if (version_compare(PHP_VERSION, self::OMEKA_PHP_VERSION, '<')) {
            $header = 'Incorrect version of PHP';
            $message = "Omeka requires PHP " . self::OMEKA_PHP_VERSION . " or 
            greater to be installed. PHP " . PHP_VERSION . " is currently 
            installed. <a href=\"http://www.php.net/manual/en/migration5.php\">Instructions 
            for upgrading</a> are on the PHP website.";
            $this->_errorMessages[] = compact('header', 'message');
        }
    }
    
    private function _checkMysqliIsAvailable()
    {
        if (!function_exists('mysqli_get_server_info')) {
            $header = 'Mysqli extension is not available';
            $message = "The mysqli PHP extension is required for Omeka to run. 
            Please check with your server administrator to <a href=\"http://www.php.net/manual/en/mysqli.installation.php\">enable 
            this extension</a> and then try again.";
            $this->_errorMessages[] = compact('header', 'message');
        }
    }
    
    private function _checkMysqlVersionIsValid()
    {
        $mysqlVersion = $this->_dbAdapter->getServerVersion();
        if (version_compare($mysqlVersion, self::OMEKA_MYSQL_VERSION, '<')) {
            $header = 'Incorrect version of MySQL';
            $message = "Omeka requires MySQL " . self::OMEKA_MYSQL_VERSION . " 
            or greater to be installed. MySQL $mysqlVersion is currently 
            installed. <a href=\"http://dev.mysql.com/doc/refman/5.0/en/upgrade.html\">Instructions 
            for upgrading</a> are on the MySQL website.";
            $this->_errorMessages[] = compact('header', 'message');
        }
    }
    
    private function _checkHtaccessFilesExist()
    {
        if (!file_exists(BASE_DIR . '/.htaccess')) {
            $header = 'Missing .htaccess File';
            $message = "Omeka's .htaccess file is missing. Please make sure this 
            file has been uploaded correctly and try again.";
            $this->_errorMessages[] = compact('header', 'message');
        }
    }
    
    private function _checkRegisterGlobalsIsOff()
    {
        if (ini_get('register_globals')) {
            $header = '"register_globals" is enabled';
            $message = "Having PHP's <a href=\"http://www.php.net/manual/en/security.globals.php\">register_globals</a> 
            setting enabled represents a security risk to your Omeka 
            installation. Also, having this setting enabled might indicate that 
            Omeka's .htaccess file is not being properly parsed by Apache, which 
            can cause any number of strange errors. It is recommended (but not 
            required) that you disable register_globals for your Omeka 
            installation.";
            $this->_warningMessages[] = compact('header', 'message');
        }
    }
    
    private function _checkExifModuleIsLoaded()
    {
        if (!extension_loaded('exif')) {
            $header = '"exif" module not loaded';
            $message = "Without the <a href=\"http://www.php.net/manual/en/book.exif.php\">exif 
            module</a> loaded into PHP, Exif data cannot be automatically 
            extracted from uploaded images.";
            $this->_warningMessages[] = compact('header', 'message');
        }
    }
    
    private function _checkFileStorageSetup()
    {
        if (!$this->_storage->canStore()) {
            try {
                $this->_storage->setUp();
            } catch (Omeka_Storage_Exception $e) {
                $header = 'File storage not set up properly.';
                $exMessage = $e->getMessage();
                $message = "The following error occurred when attempting to "
                    . "set up storage for your Omeka site: $exMessage  "
                    . "Please ensure that all storage directories exist and "
                    . "are writable by your web server.";
                $this->_errorMessages[] = compact('header', 'message');
            }
        }
    }
    
    private function _checkFileinfoIsLoaded()
    {
        if (!extension_loaded('fileinfo')) {
            $header = '"fileinfo" module not loaded';
            $message = "Without the "
                     . "<a href=\"http://php.net/manual/en/book.fileinfo.php\"> "
                     . "fileinfo module</a> loaded into PHP, the content type "
                     . "and encoding of uploaded files about cannot be read. "
                     . "The installer will disable file upload validation.";
            $this->_warningMessages[] = compact('header', 'message');
        }
    }
}
