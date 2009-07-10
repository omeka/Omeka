<?php
class Installer
{
    const OMEKA_PHP_VERSION = '5.2.4';
    const OMEKA_MYSQL_VERSION = '5.0';
    
    const DEFAULT_FULLSIZE_CONSTRAINT = 800;
    const DEFAULT_THUMBNAIL_CONSTRAINT = 200;
    const DEFAULT_SQUARE_THUMBNAIL_CONSTRAINT = 200;
    
    private $warnings = array();
    private $errors = array();
    
    private $core;
    private $showInstallForm = true;
    private $installFormValidationErrorExists = false;
    private $installFormValidationErrorMessage;
    
    public function __construct()
    {   
        // Load the necessary core phases.
        require_once 'Omeka/Core.php';
        $core = new Omeka_Core;
        $core->phasedLoading('initializeDb');
        $this->core = $core;
        
        // Verify Omeka requirements.
        $this->verifyRequirements();
        
        // Handle the install form.
        $this->handleInstallForm();
    }
    
    private function verifyRequirements()
    {
        $this->checkPhpVersion();
        $this->checkMysqliAvailable();
        $this->checkMysqlVersion();
        $this->checkHtaccessFileExists();
        $this->checkRegisterGlobalsIsOff();
        $this->checkExifModuleLoaded();
        $this->checkModRewriteEnabled();
        $this->checkArchiveDirectoriesWritable();
        if (count($this->errors)) {
            $errorMessage = "<h1>Installation Error</h1><p>Before installation can continue, 
            the following errors must be resolved:</p>";
            foreach ($this->errors as $error) {
                $errorMessage .= "<h2>{$error['header']}</h2><p>{$error['msg']}</p>";
            }
            throw new Exception($errorMessage);
        }
    }
    
    private function createTables()
    {
        $db = Omeka_Context::getInstance()->getDb();   
        
        // Build the Omeka database if necessary. Ensure that we don't confuse 
        // Omeka if there are already tables in the DB.
        $sql = "SHOW TABLES LIKE '{$db->prefix}options'";
        $tables = $db->query($sql)->fetchAll();
        if (empty($tables)) {
            include INSTALL_DIR . DIRECTORY_SEPARATOR . 'install.sql.php';
            $db->execBlock($install_sql);
        }
        
        // Check if the options table is filled (if so, Omeka already set up so 
        // exit this script)
        require_once 'Option.php';
        $options = $db->getTable('Option')->findAll();
        if (count($options)) {
            $errorMessage = "<h1>Omeka Already Installed</h1><p>It looks like Omeka has already 
            been installed. You may remove the 'install' directory for security 
            reasons.</p>";
            throw new Exception($errorMessage);
        }
    }
    
    private function handleInstallForm()
    {
        if (isset($_POST['install_submit'])) {
            $this->validateInstallForm();
            if (!$this->installFormValidationErrorExists) {
                $this->processInstallForm();
            }
        }
    }
    
    // Check whether correct version of PHP is installed
    private function checkPhpVersion()
    {
        if (version_compare(PHP_VERSION, self::OMEKA_PHP_VERSION, '<')) {
            $header = "Incorrect version of PHP";
            $msg = "Omeka requires PHP " . self::OMEKA_PHP_VERSION . " or greater 
            to be installed.  <a href=\"http://www.php.net/manual/en/migration5.php\">Instructions</a> 
            for upgrading are on the PHP website.</a>";
            $this->errors[] = compact('header', 'msg');
        }
    }
    
    // Verify that mysqli is installed and available
    private function checkMysqliAvailable()
    {
        if (!function_exists('mysqli_get_server_info')) {
            $header = "Mysqli extension is not available";
            $msg = "The mysqli PHP extension is required for Omeka to run.  Please 
            check with your server administrator to enable this extension and then 
            try again.";
            $this->errors[] = compact('header', 'msg');
        }
    }
    
    private function checkMysqlVersion()
    {
        $db = Omeka_Context::getInstance()->getDb();
        $mysqlVersion = $db->getAdapter()->getServerVersion();
        if (version_compare($mysqlVersion, self::OMEKA_MYSQL_VERSION, '<')) {
            $header = "Incorrect version of MySQL";
            $msg = "Omeka requires MySQL " . self::OMEKA_MYSQL_VERSION . " or greater 
            to be installed.  <a href=\"http://dev.mysql.com/doc/refman/5.0/en/upgrade.html\">Instructions</a> 
            for upgrading are on the MySQL website.</a>";
            $this->errors[] = compact('header', 'msg');
        }
    }
    
    // Check whether archive directories are writable
    private function checkArchiveDirectoriesWritable()
    {
        $archiveDirectories = array(ARCHIVE_DIR, FILES_DIR, FULLSIZE_DIR, 
                                    THUMBNAIL_DIR, SQUARE_THUMBNAIL_DIR);
        foreach ($archiveDirectories as $archiveDirectory) {
            if (!is_writable($archiveDirectory)) {
                $header = "Archive directory not writable";
                $msg = "The following directory must be writable by your web server 
                before installing Omeka: $archiveDirectory";
                $this->errors[] = compact('header', 'msg');
            }
        }
    }
    
    // Check whether the .htaccess file exists
    private function checkHtaccessFileExists()
    {
        if (!file_exists(BASE_DIR . DIRECTORY_SEPARATOR . '.htaccess')) {
            $header = "Missing .htaccess File";
            $msg = "Omeka's .htaccess file is missing.  Please make sure this and 
            any other hidden files, such as the .htaccess file in the admin/ directory, 
            have been uploaded correctly and try again.";
            $this->errors[] = compact('header', 'msg');
        }
    }
    
    // Check whether register globals is turned off
    private function checkRegisterGlobalsIsOff()
    {
        if (ini_get('register_globals')) {
            $header = "'register_globals' is enabled";
            $msg = "Having PHP's 'register_globals' setting enabled represents a 
            security risk to your Omeka installation.  Also, having this setting 
            enabled might indicate that Omeka's .htaccess file is not being properly 
            parsed by Apache, which can cause any number of strange errors.  It 
            is recommended (but not required) that you disable register_globals 
            for your Omeka installation.";
            $this->warnings[] = compact('header', 'msg');
        }
    }
    
    private function checkExifModuleLoaded()
    {
        if (!extension_loaded('exif')) {
            $header = "'exif' module not loaded";
            $msg = "Without the 'exif' module loaded into PHP, Exif data 
            cannot be automatically extracted from uploaded images.";
            $this->warnings[] = compact('header', 'msg');
        }
    }
    
    private function checkModRewriteEnabled()
    {
        // Verify that mod_rewrite is enabled (NOT WORK YET)
        $modRewriteUrl = WEB_ROOT . '/checkModRewrite.html';
        
        // If we can't use the http wrapper for file_get_contents(), warn that we 
        // were unable to check for mod_rewrite
        if (!ini_get('allow_url_fopen')) {
            $header = "Unable to check for mod_rewrite";
            $msg = "Unable to verify that mod_rewrite is enabled on your server.  
            mod_rewrite is an Apache extension that is required for Omeka to work 
            properly.  Omeka is unable to check because your php.ini 'allow_url_fopen' 
            setting has been disabled.  You can manually verify that Omeka mod_rewrite 
            by checking to see that the following URL works in your browser: $modRewriteUrl";
            $this->warnings[] = compact('header', 'msg');
        // We are trying to retrieve this URL
        } else if (!$modRewrite = @file_get_contents($modRewriteUrl)) {
            $header = "mod_rewrite is not enabled";
            $msg = "Apache's mod_rewrite extension must be enabled for Omeka to 
            work properly.  Please enable mod_rewrite and try again.";
            $this->errors[] = compact('header', 'msg');
        }
    }
    
    /**
     * @since 1.0 Retrieve only the directory containing the 'convert' command,
     * not the full path to the executable.
     * @return string
     **/
    public function getPathToConvert()
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
    
    private function validateInstallForm()
    {
        $usernameValidator = new Zend_Validate_StringLength(User::USERNAME_MIN_LENGTH, User::USERNAME_MAX_LENGTH);
        $passwordValidator = new Zend_Validate_StringLength(User::PASSWORD_MIN_LENGTH, User::PASSWORD_MAX_LENGTH);
        $validation = array('administrator_email'         => "EmailAddress",
                            'super_email'                 => "EmailAddress",
                            'thumbnail_constraint'        => "Digits",
                            'fullsize_constraint'         => "Digits",
                            'square_thumbnail_constraint' => "Digits",
                            'per_page_admin'              => "Digits",
                            'per_page_public'             => "Digits",
                            'username'                    => array('Alnum', $usernameValidator), 
                            'password'                    => $passwordValidator); 
        $filter = new Zend_Filter_Input(null, $validation, $_POST);
        
        // We got some errors
        if ($filter->hasInvalid()) {
            $validationErrorMessage = "
            <h2>Form Validation Errors</h2>
            <p>There were errors found in your form. Please edit and resubmit.</p>";
            foreach ($filter->getInvalid() as $field => $message) {
                $validationErrorMessage .= "<h3>$field</h3><p>" . array_pop($message) . ".</p>";
            }
            $this->installFormValidationErrorExists = true;
            $this->installFormValidationErrorMessage = $validationErrorMessage;
        }
    }
    
    private function processInstallForm()
    {
        $db = Omeka_Context::getInstance()->getDb();
        
        // Create the database tables.
        $this->createTables();

        // Create the default user
        require_once 'User.php';
        
        $userTable = $db->User;
        $entityTable = $db->Entity;
        
        $entitySql = "
        INSERT INTO $entityTable (
            email, 
            first_name, 
            last_name
        ) VALUES (?, ?, ?)";
        $db->exec($entitySql, array($_POST['super_email'], 'Super', 'User'));
        
        $userSql = "
        INSERT INTO $userTable (
            username, 
            password, 
            active, 
            role, 
            entity_id
        ) VALUES (?, SHA1(?), 1, 'super', LAST_INSERT_ID())";
        $db->exec($userSql, array($_POST['username'], $_POST['password']));
        
        // Namespace for the authentication session (to prevent clashes on 
        // shared servers)
        $optionTable = $db->Option;
        
        $optionSql = "
        INSERT INTO $optionTable (
            name, 
            value
        ) VALUES (?, ?)";
        $db->exec($optionSql, array('migration', OMEKA_MIGRATION));
        
        // Add the settings to the db
        $settings = array('administrator_email', 
                          'copyright', 
                          'site_title', 
                          'author', 
                          'description', 
                          'thumbnail_constraint', 
                          'square_thumbnail_constraint', 
                          'fullsize_constraint', 
                          'per_page_admin', 
                          'per_page_public', 
                          'path_to_convert');
        foreach ($settings as $v) {
            $db->exec($optionSql, array($v, $_POST[$v]));
        }
        
        $db->exec($optionSql, array('admin_theme', 'default'));
        $db->exec($optionSql, array('public_theme', 'default'));
        
        $db->exec($optionSql, array('file_extension_whitelist', Omeka_Validate_File_Extension::DEFAULT_WHITELIST));
        $db->exec($optionSql, array('file_mime_type_whitelist', Omeka_Validate_File_MimeType::DEFAULT_WHITELIST));
        $db->exec($optionSql, array('disable_default_file_validation', 0));
        
        $this->showInstallForm = false;
    }
    
    public function getWarningMessage()
    {
        if (empty($this->warnings)) {
            return false;
        }
        $warningMessage = "<h1>Installation Warning</h1>
        <p>The following issues will not affect installation, but they may negatively 
        affect the behavior of Omeka:</p>";
        foreach ($this->warnings as $warning) {
            $warningMessage .= "<h2>{$warning['header']}</h2><p>{$warning['msg']}</p>";
        }
        return $warningMessage;
    }
    
    public function getShowInstallForm()
    {
        return $this->showInstallForm;
    }
    
    public function getInstallFormValidationErrorExists()
    {
        return $this->installFormValidationErrorExists;
    }
    
    public function getInstallFormValidationErrorMessage()
    {
        return $this->installFormValidationErrorMessage;
    }
}
