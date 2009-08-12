<?php
/*
-- Checkbox next to "Administrator Email": "Use Superuser Email"
-- Runtime JavaScript validation
*/
class Installer
{
    const CORE_STOP_PHASE = 'initializeDb';
    const OMEKA_PHP_VERSION = '5.2.4';
    const OMEKA_MYSQL_VERSION = '5.0';
    const DEFAULT_FULLSIZE_CONSTRAINT = 800;
    const DEFAULT_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_PER_PAGE_ADMIN = 10;
    const DEFAULT_PER_PAGE_PUBLIC = 10;
    
    private $_db;
    private $_form;
    private $_errorMessages = array();
    private $_warningMessages = array();
    
    public function __construct()
    {   
        // Load Omeka. Catch any exceptions that occur.
        try {
            $this->_loadOmeka();
        } catch (Exception $e) {
            include 'fatal-error.php';
            exit;
        }
        
        // Exit installation if Omeka is already installed.
        if ($this->_omekaIsInstalled()) {
            include 'already-installed.php';
            exit;
        }
        
        // Set the database object;
        $this->_db = Omeka_Context::getInstance()->getDb();
        //die($this->_db->prefix);
    }
    
    public function checkRequirements()
    {
        $this->_checkPhpVersionIsValid();
        $this->_checkMysqliIsAvailable();
        $this->_checkMysqlVersionIsValid();
        $this->_checkHtaccessFilesExist();
        $this->_checkRegisterGlobalsIsOff();
        $this->_checkExifModuleIsLoaded();
        $this->_checkModRewriteIsEnabled();
        $this->_checkArchiveDirectoriesAreWritable();
    }
    
    public function hasError()
    {
        return count($this->_errorMessages) ? true : false;
    }
    
    public function hasWarning()
    {
        return count($this->_warningMessages) ? true : false;
    }
    
    public function getErrorMessages()
    {
        return $this->_errorMessages;
    }
    
    public function getWarningMessages()
    {
        return $this->_warningMessages;
    }
    
    public function setForm()
    {
        // http://framework.zend.com/manual/en/zend.form.quickstart.html
        // http://devzone.zend.com/article/3450
        $form = new Zend_Form;
        $form->setAction('index.php')->setMethod('post');
        $form->removeDecorator('HtmlTag');
        
        // Add form elements.
        $elementDecorators = array('ViewHelper', 
                                   'Errors', 
                                   'Description',
                                   'Label', 
                                   array('HtmlTag', array('tag' => 'div')));
        
        $form->addElement('text', 'username', array(
            'label' => 'Username', 
            'value' => $form->getValue('username'), 
            'validators' => array(array('StringLength', false, array(User::USERNAME_MIN_LENGTH, User::USERNAME_MAX_LENGTH))), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('password', 'password', array(
            'label' => 'Password', 
            'value' => $form->getValue('password'), 
            'validators' => array('Alnum', 
                                  array('StringLength', false, array(User::PASSWORD_MIN_LENGTH, User::PASSWORD_MAX_LENGTH))), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'super_email', array(
            'label' => 'Email', 
            'value' => $form->getValue('super_email'), 
            'validators' => array('EmailAddress'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'site_title', array(
            'label' => 'Site Title', 
            'value' => $form->getValue('site_title'), 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('textarea', 'description', array(
            'label' => 'Site Description', 
            'value' => $form->getValue('description'), 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'administrator_email', array(
            'label' => 'Administrator Email', 
            'value' => $form->getValue('administrator_email'), 
            'validators' => array('EmailAddress'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'copyright', array(
            'label' => 'Site Copyright Information', 
            'value' => $form->getValue('copyright'), 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'author', array(
            'label' => 'Site Author Information', 
            'value' => $form->getValue('author'), 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'fullsize_constraint', array(
            'label' => 'Fullsize Image Size', 
            'description' => 'Maximum fullsize image size constraint (in pixels).', 
            'value' => $form->getValue('fullsize_constraint') ? $form->getValue('fullsize_constraint') : self::DEFAULT_FULLSIZE_CONSTRAINT, 
            'validators' => array('Digits'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'thumbnail_constraint', array(
            'label' => 'Thumbnail Size', 
            'description' => 'Maximum thumbnail size constraint (in pixels).', 
            'value' => $form->getValue('thumbnail_constraint') ? $form->getValue('thumbnail_constraint') : self::DEFAULT_THUMBNAIL_CONSTRAINT, 
            'validators' => array('Digits'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'square_thumbnail_constraint', array(
            'label' => 'Square Thumbnail Size', 
            'description' => 'Maximum square thumbnail size constraint (in pixels).', 
            'value' => $form->getValue('square_thumbnail_constraint') ? $form->getValue('square_thumbnail_constraint') : self::DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT, 
            'validators' => array('Digits'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'per_page_admin', array(
            'label' => 'Items Per Page (admin)', 
            'description' => 'Limit the number of items displayed per page in the administrative interface.', 
            'value' => $form->getValue('per_page_admin') ? $form->getValue('per_page_admin') : self::DEFAULT_PER_PAGE_ADMIN, 
            'validators' => array('Digits'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'per_page_public', array(
            'label' => 'Items Per Page (public)', 
            'description' => 'Limit the number of items displayed per page in the public interface.', 
            'value' => $form->getValue('per_page_public') ? $form->getValue('per_page_public') : self::DEFAULT_PER_PAGE_PUBLIC, 
            'validators' => array('Digits'), 
            'required' => true, 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'path_to_convert', array(
            'label' => 'Imagemagick Directory Path', 
            'value' => $form->getValue('path_to_convert') ? $form->getValue('path_to_convert') : $this->_getPathToConvert(), 
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('text', 'path_to_php_cli', array(
            'label' => 'PHP-CLI Binary Path',
            'value' => $form->getValue('path_to_convert') ? $form->getValue('path_to_php_cli') : $this->_getPathToPhpCli(),
            'decorators' => $elementDecorators
        ));
        
        $form->addElement('submit', 'install_submit', array(
            'label' => 'Install', 
            'decorators' => array('Tooltip', 'ViewHelper')
        ));

        // Add fieldsets.
        $displayGroupDecorators = array('FormElements', 'Fieldset');
        
        $form->addDisplayGroup(
            array('username', 'password', 'super_email'), 
            'superuser_account', 
            array('legend' => 'Default Superuser Account', 
                  'decorators' => $displayGroupDecorators)
        );
        
        $form->addDisplayGroup(
            array('administrator_email', 'site_title', 'description', 
                  'copyright', 'author', 'fullsize_constraint', 
                  'thumbnail_constraint', 'square_thumbnail_constraint', 
                  'per_page_admin', 'per_page_public', 'path_to_convert', 
                  'path_to_php_cli'), 
            'site_settings', 
            array('legend' =>'Site Settings', 
                  'decorators' => $displayGroupDecorators)
        );
        
        $form->addDisplayGroup(
            array('install_submit'), 
            'submit', 
            array('decorators' => $displayGroupDecorators)
        );
        
        $form->setView(new Zend_View);
        $this->_form = $form;
    }
    
    public function getForm()
    {
        return $this->_form;
    }
    
    public function installDatabase()
    {
        $db = $this->_db;
        
        // Create the database tables and insert default data.
        $sql = "SHOW TABLES LIKE '{$db->prefix}options'";
        $tables = $this->_db->query($sql)->fetchAll();
        if (empty($tables)) {
            include INSTALL_DIR . DIRECTORY_SEPARATOR . 'install.sql.php';
            $this->_db->execBlock($installSql);
        }
        
        $entitySql = "
        INSERT INTO {$this->_db->Entity} (
            email, 
            first_name, 
            last_name
        ) VALUES (?, ?, ?)";
        $this->_db->exec($entitySql, array($this->_form->getValue('super_email'), 'Super', 'User'));
        
        $userSql = "
        INSERT INTO {$this->_db->User} (
            username, 
            password, 
            active, 
            role, 
            entity_id
        ) VALUES (?, SHA1(?), 1, 'super', LAST_INSERT_ID())";
        $this->_db->exec($userSql, array($this->_form->getValue('username'), $this->_form->getValue('password')));
        
        // Insert options.
        $optionSql = "
        INSERT INTO {$this->_db->Option} (
            name, 
            value
        ) VALUES (?, ?)";
        
        // Insert the form options to the options table.
        $options = array('administrator_email', 
                         'copyright', 
                         'site_title', 
                         'author', 
                         'description', 
                         'thumbnail_constraint', 
                         'square_thumbnail_constraint', 
                         'fullsize_constraint', 
                         'per_page_admin', 
                         'per_page_public', 
                         'path_to_convert',
                         'path_to_php_cli');
        foreach ($options as $option) {
            $this->_db->exec($optionSql, array($option, $this->_form->getValue($option)));
        }
        
        // Insert default options to the options table. 
        $this->_db->exec($optionSql, array('migration', OMEKA_MIGRATION));
        $this->_db->exec($optionSql, array('admin_theme', 'default'));
        $this->_db->exec($optionSql, array('public_theme', 'default'));
        $this->_db->exec($optionSql, array('file_extension_whitelist', Omeka_Validate_File_Extension::DEFAULT_WHITELIST));
        $this->_db->exec($optionSql, array('file_mime_type_whitelist', Omeka_Validate_File_MimeType::DEFAULT_WHITELIST));
        $this->_db->exec($optionSql, array('disable_default_file_validation', 0));
        
        return true;
    }
    
    private function _loadOmeka()
    {
        require_once '../paths.php';
        require_once 'Omeka/Core.php';
        $core = new Omeka_Core;
        $core->phasedLoading(self::CORE_STOP_PHASE);
    }
    
    private function _omekaIsInstalled()
    {
        $db = Omeka_Context::getInstance()->getDb();
        
        // Assume Omeka is not installed if the `options` table does not exist.
        $sql = "SHOW TABLES LIKE '{$db->prefix}options'";
        $tables = $db->query($sql)->fetchAll();
        if (empty($tables)) {
            return false;
        }
        
        // Assume Omeka is not installed if the `options` table contains no rows.
        require_once 'Option.php';
        $options = $db->getTable('Option')->findAll();
        if (empty($options)) {
            return false;
        }
        
        // Otherwise, assume Omeka is already installed.
        return true;
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
        $mysqlVersion = $this->_db->getAdapter()->getServerVersion();
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
        if (!file_exists(BASE_DIR . DIRECTORY_SEPARATOR . '.htaccess')) {
            $header = 'Missing .htaccess File';
            $message = "Omeka's .htaccess file is missing. Please make sure this 
            file has been uploaded correctly and try again.";
            $this->_errorMessages[] = compact('header', 'message');
        }
        
        if (!file_exists(ADMIN_DIR . DIRECTORY_SEPARATOR . '.htaccess')) {
            $header = 'Missing admin/.htaccess File';
            $message = "Omeka's admin/.htaccess file is missing. Please make 
            sure this file has been uploaded correctly and try again.";
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
    
    private function _checkModRewriteIsEnabled()
    {
        $modRewriteUrl = WEB_ROOT . '/check-mod-rewrite.html';
        
        // Set the http timeout to 5 to prevent recursion, which leads to a 
        // MySQL "too many connections" error. This assumes Apache needs only 5 
        // second to rewrite the URL.
        $context = stream_context_create(array('http' => array('timeout' => 5))); 
        
        // If we can't use the http wrapper for file_get_contents(), warn that 
        // we were unable to check for mod_rewrite.
        if (!ini_get('allow_url_fopen')) {
            $header = 'Unable to check for mod_rewrite';
            $message = "Unable to verify that <a href=\"http://httpd.apache.org/docs/1.3/mod/mod_rewrite.html\">mod_rewrite</a> 
            is enabled on your server. mod_rewrite is an Apache extension that 
            is required for Omeka to work properly. Omeka is unable to check 
            because your php.ini <a href=\"http://us2.php.net/manual/en/filesystem.configuration.php#ini.allow-url-fopen\">allow_url_fopen</a> 
            setting has been disabled. You can manually verify that Omeka 
            mod_rewrite by checking to see that the following URL works in your 
            browser: <a href=\"$modRewriteUrl\">$modRewriteUrl</a>";
            $this->_warningMessages[] = compact('header', 'message');
        
        // We are trying to retrieve this URL.
        } else if (!$modRewrite = @file_get_contents($modRewriteUrl, false, $context)) {
            $header = 'mod_rewrite is not enabled';
            $message = "Apache's <a href=\"http://httpd.apache.org/docs/1.3/mod/mod_rewrite.html\">mod_rewrite</a> 
            extension must be enabled for Omeka to work properly. Please enable 
            mod_rewrite and try again.";
            $this->_errorMessages[] = compact('header', 'message');
        }
    }
    
    private function _checkArchiveDirectoriesAreWritable()
    {
        $archiveDirectories = array(ARCHIVE_DIR, FILES_DIR, FULLSIZE_DIR, 
                                    THUMBNAIL_DIR, SQUARE_THUMBNAIL_DIR);
        foreach ($archiveDirectories as $archiveDirectory) {
            if (!is_writable($archiveDirectory)) {
                $header = 'Archive directory not writable';
                $message = "The following directory must be writable by your web 
                server before installing Omeka: $archiveDirectory";
                $this->_errorMessages[] = compact('header', 'message');
            }
        }
    }
    
    private function _getPathToConvert()
    {
        // Use the "which" command to auto-detect the path to ImageMagick;
        // redirect std error to where std input goes, which is nowhere. See: 
        // http://www.unix.org.ua/orelly/unix/upt/ch45_21.htm. If $returnVar is "0" 
        // there was no error, so assign the output of the "which" command. See: 
        // http://us.php.net/manual/en/function.system.php#66795.
        $command = 'which convert 2>&0';
        $lastLineOutput = exec($command, $output, $returnVar);
        // Return only the directory component of the path returned.
        return $returnVar == 0 ? dirname($lastLineOutput) : '';
    }
    
    private function _getPathToPhpCli()
    {
        $command = 'which php 2>&0';
        $lastLineOutput = exec($command, $output, $returnVar);
        return $returnVar == 0 ? $lastLineOutput : '';
    }
}